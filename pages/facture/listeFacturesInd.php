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
	$pdo = new SPDO();
	$stmt_rep="select rep_id,rep_creadate,rep_moddate,rep_moduser,rep_pourcentage,
			rep_rf_uti,rep_rf_fac,rep_type 
     from repartition where rep_rf_fac='".$id_fac."'";
	$result=$pdo->prepare($stmt_rep);
	
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
	$pdo = new SPDO();
	$stmt_dos="select lig_id,lig_creadate,lig_moddate,lig_moduser,lig_rubrique,lig_code,
			lig_libelle,lig_rf_fac,lig_tauxtva,lig_tva,lig_total_dev,lig_montant,
			lig_nb,lig_rf_act,lig_rang,lig_typeligne from lignefacture	
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

//on va chercher le nom du bÈnÈficaire 

function get_beneficiare_repartition($id_util)
{
	$pdo = new SPDO();
	$stmt_benef="select uti_nom from utilisateur where uti_id='".$id_util."'";
	$result=$pdo->prepare($stmt_benef);
	
	$list_benefi=null;
	$result->execute();
	
	foreach($result->fetchAll(PDO::FETCH_OBJ) as $row)
	
	{
		$list_benefi=$row;
	
	}
	return $list_benefi;
	
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
            <th scope="col">Afficher</th>
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
               
                <div class="modal fade" role="dialog" aria-labelledby="modalDetailsfact_<?php echo  $fac->fac_id; ?>" aria-hidden="true" id="modalDetailsfact_<?php echo $fac->fac_id;  ?>">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-body">                                    
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><br />
                                <div class="container-fluid">
                                   <!-- Panel des Lignes de Factures -->
                                   
                                    <div class="panel panel-default">
                                        <div class="panel-heading"> Details de Lignes de factures</div>
                                        <table class="table">
                                            <tr>
                                                <th scope="col">Rubrique</th>
                                                <th scope="col">Code</th>
                                                <th scope="col">Libell√©</th>
                                                 <th scope="col">Type</th>
                                                <th scope="col">TVA</th>
                                                <th scope="col">Montant</th>
                                             
                                            </tr>

                                            <?php 
											$lignes_fact=get_all_lignes($fac->fac_id);
                							foreach($lignes_fact as $ligne) 
												  { ?>
                                                <tr>
                                                    <td><?php echo $ligne->lig_rubrique; ?></td>
                                                    <td><?php echo $ligne->lig_code; ?></td>
                                                    <td><?php echo $ligne->lig_libelle; ?></td>
                                                     <td><?php if ($ligne->lig_typeligne=="honos") echo"honoraires";else echo $ligne->lig_typeligne; ?></td>
                                                    <td><?php echo $ligne->lig_tva; ?></td>
                                                    <td><?php echo $ligne->lig_montant; ?></td>
                                                </tr>   
                                            <?php } ?>
                                        </table>
                                    </div>
                                    
                                    <!-- Panel des Repartitions -->
                                    
                                    
                                              <div class="panel panel-default">
                                        <div class="panel-heading"> Details des Repartitions</div>
                                        <table class="table">
                                            <tr>
                                               
                                                <th scope="col">Pourcentage</th>
                                                <th scope="col">Libell√©</th>
                                                 <th scope="col">B√©n√©ficiaires</th>
             
                                            </tr>

                                            <?php //On parcours des Repatitions pour les afficher
											$lignes_rep=get_all_repartitions($fac->fac_id);
                							foreach($lignes_rep as $ligne) 
												  { ?>
                                                <tr>
                                                 <td>  
                                                   <div class="progress">
                   					 <div class="progress-bar progress-bar-primary " role="progressbar" aria-valuenow="<?php echo $ligne->rep_pourcentage; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $ligne->rep_pourcentage; ?>%; min-width: 2em;">
                      				  <span><?php echo $ligne->rep_pourcentage; ?>%</span>
                   				 </div>
               							 </div>
               							 	 	 </td>
                                                  <td><?php echo $ligne->rep_type; ?></td>
                                                    <td><?php 
                                                    $list_benef=get_beneficiare_repartition($ligne->rep_rf_uti);
                                                   echo $list_benef; ?></td>                            
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
