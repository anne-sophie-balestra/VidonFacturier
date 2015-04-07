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

//On va chercher les factures depuis l'ann√©e derniere a aujourd'hui qui sont individuelles
$stmt = "SELECT fac_id, fac_num, fac_type, fac_rf_dos, fac_rf_ent, fac_objet, fac_date, fac_echeance, fac_impression, fac_export, fac_honoraires, fac_retro, fac_taxes, fac_montantht FROM facture WHERE fac_group IS NULL AND EXTRACT(YEAR FROM fac_creadate) = " . (date('Y')-1) . " OR EXTRACT(YEAR FROM fac_creadate) = " . (date('Y'));
$result_fac = $pdo->prepare($stmt);
$result_fac->execute();


//On va chercher toutes les repartitions liÈs ‡ la facture
function get_all_repartitions($id_fac)
{
	$stmt_rep="select rep_id,rep_creadate,rep_moddate,rep_moduser,rep_pourcentage,
			rep_rf_uti,rep_rf_fac,rep_type 
     from repartition where rep_rf_fac='".$id_fac."'";
	$result=$pdo->prepare($stmt_dos);
	
	$list_rep=array();
	$result->execute();
	
	foreach($result->fetchAll(PDO::FETCH_OBJ) as $row)
	
	{
		$list_rep[]=$row;
	
	}
	return $list_rep;
}

//on va chercher tous les lignes de factures liÈs ‡ la facture

function get_all_lignes($id_fac)
{
	$stmt_dos="select lig_id,lig_creadate,lig_moddate,lig_moduser,lig_rubrique,lig_code,
			lig_libelle,lig_rf_fac,lig_tauxtva,lig_tva,lig_total_dev,lig_montant,
			lig_nb,lig_rf_act,lig_rang from lignefacture	
		where lig_rf_fac='".$id_fac."'";
	$result=$pdo->prepare($stmt_dos);
	
	$list_ligne=array();
	$result->execute();
	
	foreach($result->fetchAll(PDO::FETCH_OBJ) as $row)
	
	{
		$list_ligne[]=$row;
	
	}
	return $list_ligne;
	
}


?>
<!-- Contenu principal de la page -->
<div class="container" style="width:100%;">    
    <h2>Factures individuelles</h2>
    <table class="table table-striped table-bordered table-condensed table-hover" id="lfacturesInd">
    <thead>
        <tr>
            <th scope="col">#/Type</th>
            <th scope="col">Dossier/Client</th>
            <th scope="col">Objet</th>
            <th scope="col">Date facture/Date √©cheance</th>
            <th scope="col">Impression/Export compta</th>
            <th scope="col"></th>
            <th scope="col"></th>
            <th scope="col"></th>
            <th scope="col"></th>
        </tr>
        <tr>
            <th scope="col">#<br />Type</th>
            <th scope="col">Dossier<br />Client</th>
            <th scope="col">Objet</th>
            <th scope="col">Date facture<br />Date √©cheance</th>
            <th scope="col">Impression<br />Export compta</th>
            <th scope="col">Honoraires</th>
            <th scope="col">Retro</th>
            <th scope="col">Taxes</th>
            <th scope="col">Montant HT</th>
            <th scope="col">Afficher</th>
        </tr>
    </thead>
    <tbody>
        <?php /* On parcours les factures pour les inserer dans le tableau */
        foreach($result_fac->fetchAll(PDO::FETCH_OBJ) as $fac) { ?>
        <tr>
            <td><span class="badge"><?php echo $fac->fac_num; ?></span><br /><?php echo $fac->fac_type; ?></td>
            <td><?php echo $fac->fac_rf_dos; ?><br />
                <?php $entite = $pdo->prepare("SELECT ent_raisoc FROM entite WHERE ent_id = :ent");
                $entite->bindParam(":ent", $fac->fac_rf_ent);
                $entite->execute();
                $ent = $entite->fetch(PDO::FETCH_OBJ);
                echo $ent->ent_raisoc; ?>
            </td>
            <td><?php echo $fac->fac_objet; ?></td>
            <td><?php echo substr($fac->fac_date, 0, 11); ?><br /><?php echo substr($fac->fac_echeance, 0, 11); ?></td>
            <td><?php echo substr($fac->fac_impression, 0, 11); ?><br /><?php echo substr($fac->fac_export, 0, 11); ?></td>
            <td><?php echo $fac->fac_honoraires; ?></td>
            <td><?php echo $fac->fac_retro; ?></td>
            <td><?php echo $fac->fac_taxes; ?></td>
            <td><?php echo $fac->fac_montantht; ?></td>
            
            <td align="center">
                <button class="btn btn-primary btn-sm" data-target="#modalDetailsfact_<?php echo $fac->fac_id; ?>" data-toggle="modal">
                    <i class="icon-plus fa fa-eye"></i> Afficher
                </button>
                <?php 
                
                ?>
                <!--Affichage des lignes de facture-->
                <div class="modal fade" role="dialog" aria-labelledby="modalDetailsfact_<?php echo  $fac->fac_id; ?>" aria-hidden="true" id="modalDetailsfact_<?php echo $fac->fac_id;  ?>">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-body">                                    
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><br />
                                <div class="container-fluid">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">Lignes de factures</div>
                                        <table class="table">
                                            <tr>
                                                <th scope="col">Libell√©</th>
                                                <th scope="col">Type tarification</th>
                                                <th scope="col">Tarif standard</th>
                                                <th scope="col">Tarif junior</th>
                                                <th scope="col">Tarif senior</th>
                                                <th scope="col">Tarif manager</th>
                                            </tr>

                                            <?php //On parcours les lignes pour les afficher
											$lignes_fact=get_all_lignes($fac->fac_id);
                							foreach($lignes_fact as $ligne) { ?>
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
            
            
            
            
            
            
            
            
            
            
            
            
            
        </tr>
        <?php } ?>
    </tbody>
        <tfoot>
            <tr>
            <th scope="col">#<br />Type</th>
            <th scope="col">Dossier<br />Client</th>
            <th scope="col">Objet</th>
            <th scope="col">Date facture<br />Date √©cheance</th>
            <th scope="col">Impression<br />Export compta</th>
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
                        }
                    ]

    });
</script>
