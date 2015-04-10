<?php

$facid=$_GET['facid'];
$fac_hono=$_GET['honos'];
$fac_retro=$_GET['retro'];
$fac_taxes=$_GET['taxes'];
$fac_montant=$_GET['montant'];
//variable de distinguition des factures groupÈs et celles individuelles
$disp=$_GET['dispa'];

$pdo=new SPDO();
/*
 * fonction de selection des beneficiaires des rÈpartitions
 */
function get_beneficiare_repartition($id_util)
{
	$pdo_benef = new SPDO();
	$stmt_benef="select uti_nom from utilisateur where uti_id='".$id_util."'";
	$result=$pdo_benef->prepare($stmt_benef);

	$list_benefi=null;
	$result->execute();

	foreach($result->fetchAll(PDO::FETCH_OBJ) as $row)

	{
		$list_benefi=$row;

	}
	return $list_benefi;

}
?>
<div class="container-fluid">
<h2>Details de la Facture</h2>
<!-- Panel des Details des Montants -->
<head>
</head>

<div class="panel panel-default">
<div class="panel-heading"> Details des Montants</div>
		<table class="table">
		<tr>
		<th scope="col">Honoraires</th>
				<th scope="col">Retro</th>
				<th scope="col">Taxes</th>
				<th scope="col">Montant</th>
		</tr>
				<tr>
				<td><?php echo $fac_hono; ?></td>
           		<td><?php echo $fac_retro;?></td>
           		<td><?php echo $fac_taxes ?></td>
           		<td><?php echo $fac_montant ?></td>       										 
                   </tr>                      
                                        </table>
                                    </div>
                                
                                   <!-- Panel des Lignes de Factures -->
                                   <!-- Requete aussi des lignes de factures liÈs ‡ la facture -->
                                   
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
                                            $result_ligne= $pdo->prepare("select 
					                            lig_rubrique,lig_code,
												lig_libelle,lig_tauxtva,lig_tva,lig_total_dev,lig_montant,
												lig_typeligne from lignefacture
												where lig_rf_fac= :id_fac");
                                            $result_ligne->bindParam(":id_fac", $facid);
                                            $result_ligne->execute();
                                            $list_ligne=array();
                                            $list_ligne= $result_ligne->fetchAll(PDO::FETCH_OBJ);
                                             
                							foreach($list_ligne as $ligne) 
												  { ?>
                                                <tr>
                                                    <td><?php echo $ligne->lig_rubrique; ?></td>
                                                    <td><?php echo $ligne->lig_code; ?></td>
                                                    <td><?php echo $ligne->lig_libelle; ?></td>
                                                     <td><?php if ($ligne->lig_typeligne=="honos") 
                                                     	echo"honoraires";
                                                    	else echo $ligne->lig_typeligne;?>
                                                     </td>
                                                    <td><?php echo $ligne->lig_tva; ?></td>
                                                    <td><?php echo $ligne->lig_montant; ?></td>
                                                </tr>   
                                            <?php } ?>
                                        </table>
                                    </div>
                                    
                                    <!-- Panel des Repartitions -->
                                    <!-- Requete aussi des lignes de factures liÈs ‡ la facture..->plus  -->
                                    
                                    
                                              <div class="panel panel-default">
                                        <div class="panel-heading"> Details des Repartitions</div>
                                        <table class="table">
                                            <tr>
                                               
                                                <th scope="col">Pourcentage</th>
                                                <th scope="col">Libell√©</th>
                                                 <th scope="col">B√©n√©ficaires</th>
                                            </tr>

                                            <?php //On parcours des Repartitions pour les afficher                                                
                                                  $result_rep= $pdo->prepare("select rep_pourcentage,rep_rf_uti,rep_rf_fac,rep_type
                                                          from repartition where rep_rf_fac=:id_facture");
                                                     $result_rep->bindParam(":id_facture", $facid);
                                                     $result_rep->execute();
                                                     $list_rep=array();
                                                     $list_rep= $result_rep->fetchAll(PDO::FETCH_OBJ);
                                      	
                								foreach($list_rep as $ligne_rep) 
												  { ?>
                                                <tr>
                                                 <td>  
                                              <div class="progress">
                   					 <div class="progress-bar progress-bar-primary " role="progressbar" 
                   					aria-valuenow="<?php echo $ligne_rep->rep_pourcentage;?>" 
                   					 aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $ligne_rep->rep_pourcentage; ?>%;min-width: 2em;">
                      				  <span><?php echo $ligne_rep->rep_pourcentage; ?>%</span>
                   				 </div>
               							 </div>                
               							 	 	 </td>
                                                  <td><?php echo $ligne_rep->rep_type; ?></td>
                                                  <td><?php echo get_beneficiare_repartition($ligne_rep->rep_rf_uti);?></td>                            
                                                </tr>   
                                            <?php } ?>
                                        </table>
                                    </div>      
                 
                              <div>
                              <?php 
                              if($disp==1)
                              {
                              ?>
            <a href="index.php?action=listeFacturesInd" class="btn btn-default" title="Retour">Retour</a>
            				<?php 
                              }
                              else 
                              {
                              ?>
                              
               <a href="index.php?action=listeFacturesGroup" class="btn btn-default" title="Retour">Retour</a>               	
                              <?php }?>
            					
	</div>
                                </div>