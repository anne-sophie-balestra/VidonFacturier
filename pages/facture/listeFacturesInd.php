<?php
/********************************************
* listeFacturesInd.php                      *
* Affiche toutes les factures individuelles *
*                                           *
* Auteurs : Anne-Sophie Balestra            *
*           Abdoul Wahab Haidara            *
*           Yvan-Christian Maso             *
*           Baptiste Quere                  *
*           Yoann Le Taillanter             *
*                                           *
* Date de creation : 31/03/2015             *
********************************************/

//Connexion a la base de donnees
$pdo = new SPDO();

//On va chercher les factures depuis l'année derniere a aujourd'hui qui sont individuelles
$stmt = "SELECT fac_id, fac_num, fac_type, fac_rf_dos, fac_rf_ent, fac_objet, fac_date, fac_echeance, fac_impression, fac_export, fac_honoraires, fac_retro, fac_taxes, fac_montantht FROM facture WHERE fac_group IS NULL AND EXTRACT(YEAR FROM fac_creadate) = " . (date('Y')-1) . " OR EXTRACT(YEAR FROM fac_creadate) = " . (date('Y'));
$result_fac = $pdo->prepare($stmt);
$result_fac->execute();
?>
<!-- Contenu principal de la page -->
<div class="container" style="width:90%;">    
    <h2>Factures individuelles</h2>
    <table class="table table-striped table-bordered table-condensed table-hover" id="lfacturesInd">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Type</th>
            <th scope="col">Dossier</th>
            <th scope="col">Client</th>
            <th scope="col">Objet</th>
            <th scope="col">Date</th>
            <th scope="col">Echeance</th>
            <th scope="col">Impression</th>
            <th scope="col">Export compta</th>
            <th scope="col">Honoraires</th>
            <th scope="col">Retro</th>
            <th scope="col">Taxes</th>
            <th scope="col">Montant HT</th>
        </tr>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Type</th>
            <th scope="col">Dossier</th>
            <th scope="col">Client</th>
            <th scope="col">Objet</th>
            <th scope="col">Date</th>
            <th scope="col">Echeance</th>
            <th scope="col">Impression</th>
            <th scope="col">Export compta</th>
            <th scope="col">Honoraires</th>
            <th scope="col">Retro</th>
            <th scope="col">Taxes</th>
            <th scope="col">Montant HT</th>
        </tr>
    </thead>
    <tbody>
        <?php /* On parcours les factures pour les inserer dans le tableau */
        foreach($result_fac->fetchAll(PDO::FETCH_OBJ) as $fac) { ?>
        <tr>
            <td><span class="badge"><?php echo $fac->fac_num; ?></span></td>
            <td><?php echo $fac->fac_type; ?></a></td>
            <td><?php echo $fac->fac_rf_dos; ?></td>
            <td><?php echo $fac->fac_rf_ent; ?></td>
            <td><?php echo $fac->fac_objet; ?></td>
            <td><?php echo $fac->fac_date; ?></td>
            <td><?php echo $fac->fac_echeance; ?></td>
            <td><?php echo $fac->fac_impression; ?></td>
            <td><?php echo $fac->fac_export; ?></td>
            <td><?php echo $fac->fac_honoraires; ?></td>
            <td><?php echo $fac->fac_retro; ?></td>
            <td><?php echo $fac->fac_taxes; ?></td>
            <td><?php echo $fac->fac_montantht; ?></td>
        </tr>
        <?php } ?>
    </tbody>
        <tfoot>
            <tr>
            <th scope="col">#</th>
            <th scope="col">Type</th>
            <th scope="col">Dossier</th>
            <th scope="col">Client</th>
            <th scope="col">Objet</th>
            <th scope="col">Date</th>
            <th scope="col">Echeance</th>
            <th scope="col">Impression</th>
            <th scope="col">Export compta</th>
            <th scope="col">Honoraires</th>
            <th scope="col">Retro</th>
            <th scope="col">Taxes</th>
            <th scope="col">Montant HT</th>
            </tr>
        </tfoot>
    </table>
</div>
<script type="text/javascript" charset="utf-8">
    $('#lfacturesInd').dataTable({
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
                            values: [ 'Brevet', 'Dessin', 'Marque']
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
                        },
                        {
                            type: "text"
                        },
                        {
                            type: "select",
                            values: [ 'due', 'env', 'prj']
                        }
                    ]

    });
</script>
