<?php
/********************************************
* listePrestations.php                      *
* Affiche toutes les prestations            *
*                                           *
* Auteurs : Anne-Sophie Balestra            *
*           Abdoul Wahab Haidara            *
*           Yvan-Christian Maso             *
*           Baptiste Quere                  *
*           Yoann Le Taillanter             *
*                                           *
* Date de creation : 23/03/2015             *
********************************************/

//Connexion a la base de donnees
$pdo = new SPDO();

//On cree la requete pour recupéré les prestations de facon general
$stmt_presta = "SELECT DISTINCT(pres_id_general), pres_prestation, pres_repartition_cons, pres_rf_nom, nom_code, pres_rf_pay, pay_nom, " 
        . "pres_rf_typ_operation, t_ope_libelle, pres_rf_typ_dossier, t_dos_entite, t_dos_type " 
        . "FROM prestation, nomenclature, pays, type_operation, type_dossier " 
        . "WHERE pres_rf_nom = nom_id "
        . "AND pres_rf_pay = pay_id "
        . "AND pres_rf_typ_operation = t_ope_id "
        . "AND pres_rf_typ_dossier = t_dos_id ";
$result_presta = $pdo->prepare($stmt_presta);
$result_presta->execute();

//On recupere les differents type d'operation possibles pour filtrer nos prestations selon les opérations
$stmt_ope = "SELECT t_ope_id, t_ope_libelle FROM type_operation ORDER BY t_ope_libelle";
$result_ope = $pdo->prepare($stmt_ope);
$result_ope->execute();

//On parcours les opérations pour les inserer dans un tableau du type [ope1, ope2...] (syntaxe pour le filtrage DataTables)
$array_ope = "[";
foreach ($result_ope->fetchAll(PDO::FETCH_OBJ) as $ope) {
    $array_ope .= "'" . $ope->t_ope_libelle . "', ";
}
$array_ope .= "]";

//On recupere les differents type de dossier possibles pour filtrer nos prestations selon les types
$stmt_t_dos = "SELECT t_dos_id, t_dos_type FROM type_dossier ORDER BY t_dos_type";
$result_t_dos = $pdo->prepare($stmt_t_dos);
$result_t_dos->execute();

//On parcours les types de dossier pour les inserer dans un tableau du type [type1, type2...] (syntaxe pour le filtrage DataTables)
$array_t_dos = "[";
foreach ($result_t_dos->fetchAll(PDO::FETCH_OBJ) as $t_dos) {
    $array_t_dos .= "'" . $t_dos->t_dos_type . "', ";
}
$array_t_dos .= "]";

//On recupere les codes de nomenclatures auxquels on voudra associer des prestations
$stmt_nom = "SELECT nom_id, nom_code FROM nomenclature ORDER BY nom_code";
$result_nom = $pdo->prepare($stmt_nom);
$result_nom->execute();

//On recupere tous les pays qui peuvent etre associés à une prestation
$stmt_pays_reg = "SELECT DISTINCT(pay_region) FROM pays ORDER BY pay_region";
$result_pays_reg = $pdo->prepare($stmt_pays_reg);
$result_pays_reg->execute();

?>
<!-- Contenu principal de la page -->
<div class="container" style="width:90%;">    
    <h2>Prestations</h2>
    <table class="table table-striped table-bordered table-condensed table-hover" id="lprestations">
    <thead>
        <tr>
            <th scope="col">Opération</th>
            <th scope="col">Type dossier</th>
            <th scope="col">Code</th>
            <th scope="col">Prestation</th>
            <th scope="col">Répartition consultants</th>
            <th scope="col">Pays</th>
        </tr>
        <tr>
            <th scope="col">Opération</th>
            <th scope="col">Type dossier</th>
            <th scope="col">Code</th>
            <th scope="col">Prestation</th>
            <th scope="col">Répartition consultants</th>
            <th scope="col">Pays</th>
            <th scope="col">Modifier</th>
        </tr>
    </thead>
    <tbody>
        <?php /* On parcours les prestations pour les inserer dans le tableau */
        foreach($result_presta->fetchAll(PDO::FETCH_OBJ) as $presta) { ?>
        <tr>
            <td><?php echo $presta->t_ope_libelle; ?></td>
            <td><?php echo $presta->t_dos_type; ?></td>
            <td><?php echo $presta->nom_code; ?></td>
            <td><?php echo $presta->pres_prestation; ?></td>
            <td><?php echo $presta->pres_repartition_cons; ?>%</td>
            <td><?php echo $presta->pay_nom; ?></td>
            <td align="center">
                <a class="btn btn-primary btn-sm" data-toggle="modal" href="#mod_updatePrestationGen">
                    <i class="icon-plus fa fa-edit"></i> Modifier
                </a>
            </td>
            <!--Modal de modification de la prestation générale-->
            <div class="modal fade" id="mod_updatePrestationGen" role="dialog" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Modifier la prestation générale</h4>
                        </div>
                        <div class="modal-body">          
                            <!--Creation d'un formulaire avec la validation Bootstrap-->
                            <form id="formUpdatePrestationGen" action="#" method="post" role="form">                   
                                <div class="form-group">
                                    <label class="control-label" for="operation">Opération :</label>
                                        <select name="operation" id="operation" required class="form-control select2">
                                        <?php //On affiche toutes les operations comme des options du select
                                        $result_ope->execute();
                                        foreach($result_ope->fetchAll(PDO::FETCH_OBJ) as $ope) { ?>
                                            <option value="<?php echo $ope->t_ope_id; ?>" <?php if($ope->t_ope_id == $presta->pres_rf_typ_operation) { echo "selected"; } ?>><?php echo $ope->t_ope_libelle; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="type_dossier">Type de dossier :</label><br />
                                    <!--On affiche les differents types de dossier possibles-->
                                    <select name="type_dossier" id="type_dossier" required class="form-control select2">
                                    <?php // On affiche les types de dossier disponibles 
                                    $result_t_dos->execute();
                                    foreach($result_t_dos->fetchAll(PDO::FETCH_OBJ) as $t_dos) { ?>
                                        <option value="<?php echo $t_dos->t_dos_id; ?>" <?php if($t_dos->t_dos_id == $presta->pres_rf_typ_dossier) { echo "selected"; } ?>><?php echo $t_dos->t_dos_type; ?></option>
                                    <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="nom_code">Code :</label>
                                    <!--On affiche les codes de nomenclature dans le select--> 
                                    <select name="nom_code" id="nom_code" required class="form-control select2">
                                    <?php foreach($result_nom->fetchAll(PDO::FETCH_OBJ) as $nom) { ?>
                                        <option value="<?php echo $nom->nom_id; ?>" <?php if($nom->nom_id == $presta->pres_rf_nom) { echo "selected"; } ?>><?php echo $nom->nom_code; ?></option>
                                    <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="pays">Pays :</label>
                                    <!--On affiche les pays en les groupant par regions-->
                                    <select name="pays" id="pays" required class="form-control select2">
                                    <?php foreach($result_pays_reg->fetchAll(PDO::FETCH_OBJ) as $pays_reg) { ?>
                                        <optgroup label="<?php echo $pays_reg->pay_region; ?>">
                                            <?php $stmt_pays = "SELECT pay_id, pay_nom FROM pays WHERE pay_region = '" . $pays_reg->pay_region . "' ORDER BY pay_nom";
                                            $result_pays = $pdo->prepare($stmt_pays);
                                            $result_pays->execute();
                                            foreach($result_pays->fetchAll(PDO::FETCH_OBJ) as $pays) { ?>
                                                <option value="<?php echo $pays->pay_id; ?>" <?php if($pays->pay_id == $presta->pres_rf_pay) { echo "selected"; } ?>><?php echo $pays->pay_nom; ?></option>
                                            <?php } ?>
                                        </optgroup>
                                    <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="prestation">Prestation :</label>
                                    <!--on prend le nom general de la prestation, i.e. nom du modele-->
                                    <input name="prestation" type="text" value="<?php echo $presta->pres_prestation; ?>" required class="form-control" id="prestation" maxlength="255">
                                    <div class="help-block with-errors"></div>
                                </div>
                                <!--On gere ici la repartition des consultants soit par un select, soit avec un slider (les deux sont liés)-->
                                <div class="form-group">
                                    <label class="control-label" for="repartition">Répartition des consultants :</label>
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <select id="pourcentage_select" class="form-inline" onchange="document.getElementById('pourcentage').innerHTML=this.value+'%';document.getElementById('repartition').value=this.value;">
                                                <?php for($i=0; $i<=100; $i+=5) { ?>
                                                    <option <?php if($presta->pres_repartition_cons == $i) { echo "selected"; } ?>><?php echo $i; ?></option>
                                                <?php } ?>
                                            </select>
                                        </span>
                                        <input name="repartition" id="repartition" value="<?php echo $presta->pres_repartition_cons; ?>" onchange="document.getElementById('pourcentage').innerHTML=this.value+'%';document.getElementById('pourcentage_select').value=this.value;" type="range" min="0" max="100" step="5" required class="form-control">
                                        <span id="pourcentage" class="input-group-addon"><?php echo $presta->pres_repartition_cons; ?>%</span>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                            <button type="button" class="btn btn-primary">Modifier</button>
                        </div>        
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
        </tr>
        <?php } ?>
    </tbody>
        <tfoot>
            <tr>
            <th scope="col">Opération</th>
            <th scope="col">Type dossier</th>
            <th scope="col">Code</th>
            <th scope="col">Prestation</th>
            <th scope="col">Répartition consultants</th>
            <th scope="col">Pays</th>
            <th scope="col">Modifier</th>
            </tr>
        </tfoot>
    </table>
</div>

<script type="text/javascript" charset="utf-8">
    $('#lprestations').dataTable({
        "language":{
            sProcessing:   "Traitement en cours...",
            sLengthMenu:   "Afficher _MENU_ &eacute;l&eacute;ments",
            sZeroRecords:  "Aucun &eacute;l&eacute;ment &agrave; afficher",
            sInfo:         "Affichage des &eacute;lements _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
            sInfoEmpty:    "Affichage de l'&eacute;lement 0 &agrave; 0 sur 0 &eacute;l&eacute;ments",
            sInfoFiltered: "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
            sInfoPostFix:  "",
            sSearch:       "Rechercher&nbsp;:",
            sUrl:          "",
            "oPaginate": {
                    sFirst:    "Premier",
                    sPrevious: "Pr&eacute;c&eacute;dent",
                    sNext:     "Suivant",
                    sLast:     "Dernier"
            }
        }    
    }).columnFilter({        
        sPlaceHolder: "head:after",
        aoColumns: [
                        {
                            type: "select",
                            values: <?php echo $array_ope; ?>
                        },
                        {
                            type: "select",
                            values: <?php echo $array_t_dos; ?>
                        },
                        {
                            type: "text"
                        },
                        {
                            type: "text"
                        },
                        {
                            type: "text"
                        },
                        {
                            type: "text"
                        }
                    ]

    });
</script>
    