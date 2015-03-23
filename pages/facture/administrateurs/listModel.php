<?php
/********************************************
* listeDossiers.php                         *
* Affiche tous les dossiers en liste        *
*                                           *
* Auteurs : Anne-Sophie Balestra            *
*           Abdoul Wahab Haidara            *
*           Yvan-Christian Maso             *
*           Baptiste Quere                  *
*           Yoann Le Taillanter             *
*                                           *
* Date de creation : 30/01/2015             *
********************************************/
$pdo = new SPDO();

$stmt = "SELECT t_fac_id, t_fac_rf_ent, t_fac_rf_typdos, t_fac_rf_ope FROM type_facture";
$result_model = $pdo->prepare($stmt);
$result_model->execute();
?>

<!-- Contenu principal de la page -->
<div class="container" style="width:90%;">    
    <h2>Dossiers</h2>
    <table class="table table-striped table-bordered table-condensed table-hover" id="ldossiers">
    <thead>
        <tr>
            <th scope="col">Id</th>
            <th scope="col">Entreprise</th>
            <th scope="col">Dossier</th>
            <th scope="col">Opération</th>
        </tr>
        <tr>
            <th scope="col">Id</th>
            <th scope="col">Entreprise</th>
            <th scope="col">Dossier</th>
            <th scope="col">Opération</th>
        </tr>
    </thead>
    <tbody>
        <?php /* On parcours les chantiers pour les inserer dans le tableau */
        foreach($result_model->fetchAll(PDO::FETCH_OBJ) as $model) { ?>
        <tr>
            <td><?php echo $model->t_fac_id; ?></td>
            <td><?php echo $model->t_fac_rf_ent; ?></td>
            <td><?php echo $model->t_fac_rf_typdos; ?></td>
            <td><?php echo $model->t_fac_rf_ope; ?></td>
        </tr>
        <?php } ?>
    </tbody>
        <tfoot>
            <tr>
            <th scope="col">Id</th>
            <th scope="col">Entreprise</th>
            <th scope="col">Dossier</th>
            <th scope="col">Opération</th>
            </tr>
        </tfoot>
    </table>
</div>
<script type="text/javascript" charset="utf-8">
    $('#list_models').dataTable({
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