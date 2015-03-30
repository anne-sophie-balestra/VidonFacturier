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
<div class="container" style="width:100%;">  
    <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal">
  Launch demo modal
</button>
    <form id="formUpdatePrestation" action="index.php?action=changePrestation" method="post" role="form" data-toggle="validator">  
        <div class="modal fade" role="dialog" aria-labelledby="modalInfoPrestationGenerale" aria-hidden="true" id="myModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">   
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="modalInfoPrestationGeneraleLabel">Modification d'une prestation</h4>
                        </div>
                        <div class="modal-body">
                            <div class="container-fluid"> 
                                <div class="form-group">
                                    <label class="control-label" for="prestation">Prestation :</label>
                                    <!--on prend le nom general de la prestation, i.e. nom du modele-->
                                    <input name="prestation" type="text" value="" required class="form-control" id="prestation" maxlength="255" data-error="Veuillez entrer le nom de la prestation générale">
                                    <div class="help-block with-errors"></div>
                                </div>
                                <!--On gere ici la repartition des consultants soit par un select, soit avec un slider (les deux sont liés)-->
                                <div class="form-group">
                                    <label class="control-label" for="repartition">Répartition des consultants :</label>
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <select id="pourcentage_select" class="form-inline" onchange="document.getElementById('pourcentage').innerHTML=this.value+'%';document.getElementById('repartition').value=this.value;">
                                                <?php for($i=0; $i<=100; $i+=5) { ?>
                                                    <option><?php echo $i; ?></option>
                                                <?php } ?>
                                            </select>
                                        </span>
                                        <input name="repartition" value="" id="repartition" onchange="document.getElementById('pourcentage').innerHTML=this.value+'%';document.getElementById('pourcentage_select').value=this.value;" type="range" min="0" max="100" step="5" required class="form-control">
                                        <span id="pourcentage" class="input-group-addon">0%</span>
                                    </div>
                                </div>
                                    <div class="form-group">
                                        <label class="control-label" for="libelle">Libellé :</label>
                                        <input name="libelle" type="text" required onkeyup="checkLignePrestation('subAction');" class="form-control" id="libelle" maxlength="255">
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label" for="t_tarif">Type de tarification :</label>
                                        <!--On choisit le type de tarification et on genere les champs qu'il faut en fonction-->
                                        <select name="t_tarif" id="t_tarif" required class="form-control" onchange="afficherTarifs(this.value);checkLignePrestation('subAction');">
                                            <option value="" disabled selected>Choisissez un type de tarification...</option>
                                            <option value="F">Forfaitaire</option>
                                            <option value="TH">Tarif Horaire</option>
                                        </select>
                                    </div>
                                    <div class="form-group" id="tarif_std_div" style="display: none;">
                                        <label class="control-label" for="tarif_std">Tarif :</label>
                                        <div class="input-group">
                                            <input name="tarif_std" id="tarif_std" type="text" onkeyup="checkLignePrestation('subAction');" pattern="\d+(\.\d{1,2})?" data-error='Veuillez renseigner un montant (ex: 400.50)' required class="form-control">
                                            <span class="input-group-addon">€</span>
                                        </div>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                    <div class="form-group" id="tarif_jr_div" style="display: none;">
                                        <label class="control-label" for="tarif_jr">Tarif junior :</label>
                                        <div class="input-group">
                                            <input name="tarif_jr" id="tarif_jr" type="text" onkeyup="checkLignePrestation('subAction');" pattern="\d+(\.\d{1,2})?" data-error='Veuillez renseigner un montant (ex: 400.50)' required class="form-control">
                                            <span class="input-group-addon">€</span>
                                        </div>
                                        <div class="help-block with-errors"></div>
                                    </div>        
                                    <div class="form-group" id="tarif_sr_div" style="display: none;">
                                        <label class="control-label" for="tarif_sr">Tarif senior :</label>
                                        <div class="input-group">
                                            <input name="tarif_sr" id="tarif_sr" type="text" onkeyup="checkLignePrestation('subAction');" pattern="\d+(\.\d{1,2})?" data-error='Veuillez renseigner un montant (ex: 400.50)' required class="form-control">
                                            <span class="input-group-addon">€</span>
                                        </div>
                                        <div class="help-block with-errors"></div>
                                    </div>        
                                    <div class="form-group" id="tarif_mgr_div" style="display: none;">
                                        <label class="control-label" for="tarif_mgr">Tarif manager :</label>
                                        <div class="input-group">
                                            <input name="tarif_mgr" id="tarif_mgr" type="text" onkeyup="checkLignePrestation('subAction');" pattern="\d+(\.\d{1,2})?" data-error='Veuillez renseigner un montant (ex: 400.50)' required class="form-control">
                                            <span class="input-group-addon">€</span>
                                        </div>
                                        <div class="help-block with-errors"></div>
                                    </div> 
                                    <!--Bouton pou ajouter ou modifier une ligne de prestation-->
                                    <div class="form-group" id="button_action">
                                        <button type="button" class="btn btn-default" disabled name="subAction" id="subAction" onclick="ajouterPrestationForm('listePrestations', false);"><i class='icon-plus fa fa-plus'></i> Ajouter une prestation</button>
                                    </div>
                                <!--input pour compter le nombre de prestations ajoutees (au moins une necessaire)-->
                                <div class="form-group">
                                    <input name="nbInfos" id="nbInfos" style="display: none;" type="number" value="0" min='1' required class="form-control" data-error="Veuillez ajouter au moins une ligne de prestation">   
                                    <div class="help-block with-errors"></div>
                                </div>
                                <!--input pour compter le nombre de prestations ajoutees en tout (meme si elles ont ete supprimees ensuite)-->
                                <div class="form-group" hidden>
                                    <input name="nbInfosTot" id="nbInfosTot" type="number" value="0" required class="form-control">
                                </div>
                                    </div>
                                </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                            <input type="submit" class="btn btn-primary" id="button" value="Modifier">
                        </div>         
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    </form>
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
            <th scope="col">Afficher</th>
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
            <td>
                <div class="progress">
                    <div class="progress-bar progress-bar-primary " role="progressbar" aria-valuenow="<?php echo $presta->pres_repartition_cons; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $presta->pres_repartition_cons; ?>%; min-width: 2em;">
                        <span><?php echo $presta->pres_repartition_cons; ?>%</span>
                    </div>
                </div>
            </td>
            <td><?php echo $presta->pay_nom; ?></td>
            <td align="center">
                <button class="btn btn-primary btn-sm" data-target="#modalLignesPrestation_<?php echo $presta->pres_id_general; ?>" data-toggle="modal">
                    <i class="icon-plus fa fa-eye"></i> Afficher
                </button>
                <?php //requete pour recuperer les lignes de prestations en fonction de l'id de la prestation generale 
                $stmt_lignes = "SELECT pres_libelle_ligne_fac, pres_t_tarif, pres_tarif_std, pres_tarif_jr, pres_tarif_sr, pres_tarif_mgr "
                        . "FROM prestation " 
                        . "WHERE pres_id_general = :idGen ";
                $result_lignes = $pdo->prepare($stmt_lignes);
                $result_lignes->bindParam(":idGen", $presta->pres_id_general);
                $result_lignes->execute();
                ?>
                <!--Affichage des lignes de prestation-->
                <div class="modal fade" role="dialog" aria-labelledby="modalLignesPrestation_<?php echo $presta->pres_id_general; ?>" aria-hidden="true" id="modalLignesPrestation_<?php echo $presta->pres_id_general; ?>">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-body">                                    
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><br />
                                <div class="container-fluid">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">Lignes de prestation</div>
                                        <table class="table">
                                            <tr>
                                                <th scope="col">Libellé</th>
                                                <th scope="col">Type tarification</th>
                                                <th scope="col">Tarif standard</th>
                                                <th scope="col">Tarif junior</th>
                                                <th scope="col">Tarif senior</th>
                                                <th scope="col">Tarif manager</th>
                                            </tr>

                                            <?php //On parcours les lignes pour les afficher
                                            foreach($result_lignes->fetchAll(PDO::FETCH_OBJ) as $ligne) { ?>
                                                <tr>
                                                    <td><?php echo $ligne->pres_libelle_ligne_fac; ?></td>
                                                    <td><?php if($ligne->pres_t_tarif == "F") { echo "Forfaitaire"; } else { echo "Tarif horaire"; } ?></td>
                                                    <td><?php echo $ligne->pres_tarif_std; ?></td>
                                                    <td><?php echo $ligne->pres_tarif_jr; ?></td>
                                                    <td><?php echo $ligne->pres_tarif_sr; ?></td>
                                                    <td><?php echo $ligne->pres_tarif_mgr; ?></td>
                                                </tr>   
                                            <?php } ?>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
            </td>
            <td align="center">
                <button class="btn btn-primary btn-sm" onclick="genererModalPrestation('modalPrestation','<?php echo $presta->pres_id_general; ?>');">
                    <i class="icon-plus fa fa-edit"></i> Modifier
                </button>     
            </td>
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
            <th scope="col">Afficher</th>
            <th scope="col">Modifier</th>
            </tr>
        </tfoot>
    </table>    
    <!--modal pour modifier une prestation-->
    <div id="modalPrestation"></div>
</div>

<script type="text/javascript" charset="utf-8">
    
    jQuery.noConflict();
    jQuery('#lprestations').dataTable({
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
    