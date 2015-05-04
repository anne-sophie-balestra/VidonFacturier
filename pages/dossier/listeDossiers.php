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
$stmt = "SELECT dos_id, dos_type, dos_numcomplet, dos_responsable, dos_titre, ent_raisoc, dos_rf_int, dos_statut FROM dossier, entite WHERE dossier.dos_rf_ent = entite.ent_id AND EXTRACT(YEAR FROM dos_creadate) = " . (date('Y')-1) . " OR EXTRACT(YEAR FROM dos_creadate) = " . (date('Y'));
$result_dossiers = $pdo->prepare($stmt);
$result_dossiers->execute();
?>
<!-- Contenu principal de la page -->
<div class="container" style="width:90%;">    
    <h2>Dossiers</h2>
    <table class="table table-striped table-bordered table-condensed table-hover" id="ldossiers">
    <thead>
        <tr>
            <th scope="col">Type</th>
            <th scope="col">Numéro</th>
            <th scope="col">Responsables</th>
            <th scope="col">Dossier</th>
            <th scope="col">Client</th>
            <th scope="col">Interlocuteur</th>
            <th scope="col">Statut</th>
        </tr>
        <tr>
            <th scope="col">Type</th>
            <th scope="col">Numéro</th>
            <th scope="col">Responsables</th>
            <th scope="col">Dossier</th>
            <th scope="col">Client</th>
            <th scope="col">Interlocuteur</th>
            <th scope="col">Statut</th>
        </tr>
    </thead>
    <tbody>
        <?php /* On parcours les dossiers pour les inserer dans le tableau */
        foreach($result_dossiers->fetchAll(PDO::FETCH_OBJ) as $dossier) { ?>
        <tr>
            <td><?php echo $dossier->dos_type; ?></td>
            <td>
<!--             <a  href="createFacture.php?id=<?php echo $dossier->dos_id; ?>"> 
 -->
            <ul class="dropdown">
                    <a href="#menuDossier<?php echo $dossier->dos_id; ?>" class="dropdown-toggle" data-toggle="dropdown" title="Dossier_<?php echo $dossier->dos_id; ?>"><?php echo $dossier->dos_numcomplet; ?> <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="#dossier"> Voir le dossier</a></li>
                        <li class="dropdown-header">Factures</li>
                        <li><a href="index.php?action=createFacture&id=<?php echo $dossier->dos_id; ?>"><i class="icon-plus fa fa-plus"></i> Nouvelle facture manuelle...</a></li>
                        <li><a href="index.php?action=createFactureAutoDos&id=<?php echo $dossier->dos_id; ?>"><i class="icon-plus fa fa-plus"></i> Nouvelle facture automatique...</a></li>
                    </ul>
                </ul>
            
<!--             <?php echo $dossier->dos_numcomplet; ?>
 -->            </a></td>
            <td><?php echo $dossier->dos_responsable; ?></td>
            <td><?php echo $dossier->dos_titre; ?></td>
            <td><?php echo $dossier->ent_raisoc; ?></td>
            <td><?php echo $dossier->dos_rf_int; ?></td>
            <td><?php echo $dossier->dos_statut; ?></td>
        </tr>
        <?php } ?>
    </tbody>
        <tfoot>
            <tr>
                <th scope="col">Type</th>
                <th scope="col">Numéro</th>
                <th scope="col">Responsables</th>
                <th scope="col">Dossier</th>
                <th scope="col">Client</th>
                <th scope="col">Interlocuteur</th>
                <th scope="col">Statut</th>
            </tr>
        </tfoot>
    </table>
</div>
<script type="text/javascript" charset="utf-8">
    $('#ldossiers').dataTable({
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
