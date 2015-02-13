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
$stmt = "SELECT dos_id, dos_type, dos_numcomplet, dos_responsable, dos_titre, dos_refclient, dos_rf_int, dos_statut FROM dossier WHERE EXTRACT(YEAR FROM dos_creadate) = " . (date('Y')-1) . " OR EXTRACT(YEAR FROM dos_creadate) = " . (date('Y'));
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
        <?php /* On parcours les chantiers pour les inserer dans le tableau */
        foreach($result_dossiers->fetchAll(PDO::FETCH_OBJ) as $dossier) { ?>
        <tr>
            <td><?php echo $dossier->dos_type; ?></td>
            <td><?php echo $dossier->dos_numcomplet; ?></td>
            <td><?php echo $dossier->dos_responsable; ?></td>
            <td><?php echo $dossier->dos_titre; ?></td>
            <td><?php echo $dossier->dos_refclient; ?></td>
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
<!-- JQuery --> 
<script type="text/javascript" src="http://code.jquery.com/jquery-1.11.1.min.js"></script> 
<!-- Bootstrap --> 
<script type="text/javascript" src="../../librairies/bootstrap-3.3.2-dist/js/bootstrap.min.js"></script> 
<!-- Datatables --> 
<script type="text/javascript" src="http://cdn.datatables.net/1.10.5/js/jquery.dataTables.min.js"></script> 
<script type="text/javascript" src="http://jquery-datatables-column-filter.googlecode.com/svn/trunk/media/js/jquery.dataTables.columnFilter.js"></script> 
<script type="text/javascript" src="http://cdn.datatables.net/plug-ins/f2c75b7247b/integration/bootstrap/3/dataTables.bootstrap.js"></script> 
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