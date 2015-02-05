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

$liste_dossiers = pg_query("SELECT dos_id, dos_type, dos_numcomplet, dos_responsable, dos_titre, dos_refclient, dos_rf_int, dos_statut FROM dossier WHERE EXTRACT(YEAR FROM dos_creadate) = " . (date('Y')-1) . " OR EXTRACT(YEAR FROM dos_creadate) = " . (date('Y')));
?>
<!-- Contenu principal de la page -->
<div class="container">    
    <div class="row">
        <h2>Dossiers</h2>
        <table class="table table-striped table-bordered table-condensed table-hover" id="ldossiers">
            <thead>
                <tr>
                    <th scope="col"></th>
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
                while ($dossier = pg_fetch_row($liste_dossiers)) { ?>
                    <tr>
                        <td></td>
                        <td><?php echo $dossier[1]; ?></td>
                        <td><?php echo $dossier[2]; ?></td>
                        <td><?php echo $dossier[3]; ?></td>
                        <td><?php echo $dossier[4]; ?></td>
                        <td><?php echo $dossier[5]; ?></td>
                        <td><?php echo $dossier[6]; ?></td>
                        <td><?php echo $dossier[7]; ?></td>
                    </tr>                    
                <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <th scope="col"></th>
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
</div>
<!-- Bootstrap --> 
<script type="text/javascript" src="librairies/bootstrap-3.3.2-dist/js/bootstrap.min.js"></script> 

<!--DataTables-->
<script type="text/javascript" src="librairies/DataTables-1.10.4/media/js/jquery.js"></script> 
<script type="text/javascript" src="librairies/DataTables-1.10.4/media/js/jquery.dataTables.min.js"></script> 
<script type="text/javascript" charset="utf-8">
    $(document).ready(function() {
        $('#ldossiers').DataTable({
        "language": {
            "sUrl": "librairies/DataTables-1.10.4/media/fr_FR.txt"
          }
        });
    });
</script>