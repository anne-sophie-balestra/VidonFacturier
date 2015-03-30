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
            <td>
                <div class="progress">
                    <div class="progress-bar progress-bar-primary " role="progressbar" aria-valuenow="<?php echo $presta->pres_repartition_cons; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $presta->pres_repartition_cons; ?>%">
                        <span><?php echo $presta->pres_repartition_cons; ?>%</span>
                    </div>
                </div>
            </td>
            <td><?php echo $presta->pay_nom; ?></td>
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
    