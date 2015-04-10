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

//On va chercher les factures depuis l'ann√©e derniere a aujourd'hui qui sont individuelles cad les valeurs de client.cli_libellefact=1
$stmt = "SELECT fac_id, fac_num, fac_type, fac_rf_ent,fac_rf_dos, fac_objet,fac_status, fac_date, fac_echeance, fac_impression, 
fac_export, fac_honoraires, fac_retro, fac_taxes, fac_montantht FROM facture 
		Join entite on facture.fac_rf_ent=entite.ent_id 
		Join client on entite.ent_id=client.cli_rf_ent
		WHERE client.cli_libellefact=2 
		AND EXTRACT(YEAR FROM fac_creadate) between " . (date('Y')-1) . " and  ". (date('Y'));
		
$result_fac = $pdo->prepare($stmt);
$result_fac->execute();

/*
//On va chercher toutes les repartitions liÈs ‡ la facture
function get_all_repartitions($id_fac)
{
	$pdo_rep = new SPDO();
	$stmt_rep="select rep_id,rep_creadate,rep_moddate,rep_moduser,rep_pourcentage,
			rep_rf_uti,rep_rf_fac,rep_type
     from repartition where rep_rf_fac='".$id_fac."'";
	$result=$pdo_rep->prepare($stmt_rep);

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
	$pdo_ligne = new SPDO();
	$stmt_dos="select lig_id,lig_creadate,lig_moddate,lig_moduser,lig_rubrique,lig_code,
			lig_libelle,lig_rf_fac,lig_tauxtva,lig_tva,lig_total_dev,lig_montant,
			lig_nb,lig_rf_act,lig_rang,lig_typeligne from lignefacture
		where lig_rf_fac='".$id_fac."'";
	$result=$pdo_ligne->prepare($stmt_dos);

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
*/
?>
<!-- Contenu principal de la page -->
<head>
 <link rel="stylesheet" type="text/css" href="librairies//DataTables-1.10.5/media/css/jquery.dataTables.css">
</head>
<div class="container" style="width:100%;">    
    <h2>Factures Group&eacute;es</h2>
    <!-- Ajout d'une partie du css  de datatable...pour ne pas impacter les autres fichiers ??en peu mais efficace -->
    <table class="table table-striped table-bordered table-condensed table-hover" id="lfacturesInd"
    style="
    
    /*
 * Table styles
 */
table.dataTable {
  width: 80%;
  margin: 0 auto;
  clear: both;
  border-collapse: separate;
  border-spacing: 0;
  /*
   * Header and footer styles
   */
  /*
   * Body styles
   */
}
table.dataTable thead th,
table.dataTable tfoot th {
  font-weight: bold;
}
table.dataTable thead th,
table.dataTable thead td {
  padding: 10px 18px;
  border-bottom: 1px solid #111111;
}
table.dataTable thead th:active,
table.dataTable thead td:active {
  outline: none;
}
table.dataTable tfoot th,
table.dataTable tfoot td {
  padding: 10px 18px 6px 18px;
  border-top: 1px solid #111111;
}
table.dataTable thead .sorting_asc,
table.dataTable thead .sorting_desc,
table.dataTable thead .sorting {
  cursor: pointer;
  *cursor: hand;
}
table.dataTable thead .sorting {
  background: url("../images/sort_both.png") no-repeat center right;
}
table.dataTable thead .sorting_asc {
  background: url("../images/sort_asc.png") no-repeat center right;
}
table.dataTable thead .sorting_desc {
  background: url("../images/sort_desc.png") no-repeat center right;
}
table.dataTable thead .sorting_asc_disabled {
  background: url("../images/sort_asc_disabled.png") no-repeat center right;
}
table.dataTable thead .sorting_desc_disabled {
  background: url("../images/sort_desc_disabled.png") no-repeat center right;
}
table.dataTable tbody tr {
  background-color: white;
}
table.dataTable tbody tr.selected {
  background-color: #b0bed9;
}
table.dataTable tbody th,
table.dataTable tbody td {
  padding: 8px 10px;
}
table.dataTable.row-border tbody th, table.dataTable.row-border tbody td, table.dataTable.display tbody th, table.dataTable.display tbody td {
  border-top: 1px solid #dddddd;
}
table.dataTable.row-border tbody tr:first-child th,
table.dataTable.row-border tbody tr:first-child td, table.dataTable.display tbody tr:first-child th,
table.dataTable.display tbody tr:first-child td {
  border-top: none;
}
table.dataTable.cell-border tbody th, table.dataTable.cell-border tbody td {
  border-top: 1px solid #dddddd;
  border-right: 1px solid #dddddd;
}
table.dataTable.cell-border tbody tr th:first-child,
table.dataTable.cell-border tbody tr td:first-child {
  border-left: 1px solid #dddddd;
}
table.dataTable.cell-border tbody tr:first-child th,
table.dataTable.cell-border tbody tr:first-child td {
  border-top: none;
}
table.dataTable.stripe tbody tr.odd, table.dataTable.display tbody tr.odd {
  background-color: #f9f9f9;
}
table.dataTable.stripe tbody tr.odd.selected, table.dataTable.display tbody tr.odd.selected {
  background-color: #abb9d3;
}
table.dataTable.hover tbody tr:hover,
table.dataTable.hover tbody tr.odd:hover,
table.dataTable.hover tbody tr.even:hover, table.dataTable.display tbody tr:hover,
table.dataTable.display tbody tr.odd:hover,
table.dataTable.display tbody tr.even:hover {
  background-color: whitesmoke;
}
table.dataTable.hover tbody tr:hover.selected,
table.dataTable.hover tbody tr.odd:hover.selected,
table.dataTable.hover tbody tr.even:hover.selected, table.dataTable.display tbody tr:hover.selected,
table.dataTable.display tbody tr.odd:hover.selected,
table.dataTable.display tbody tr.even:hover.selected {
  background-color: #a9b7d1;
}
table.dataTable th.dt-right,
table.dataTable td.dt-right {
  text-align: right;
}
table.dataTable th.dt-justify,
table.dataTable td.dt-justify {
  text-align: justify;
}
table.dataTable th.dt-nowrap,
table.dataTable td.dt-nowrap {
  white-space: nowrap;
}
table.dataTable thead th.dt-head-left,
table.dataTable thead td.dt-head-left,
table.dataTable tfoot th.dt-head-left,
table.dataTable tfoot td.dt-head-left {
  text-align: left;
}
table.dataTable thead th.dt-head-center,
table.dataTable thead td.dt-head-center,
table.dataTable tfoot th.dt-head-center,
table.dataTable tfoot td.dt-head-center {
  text-align: center;
}
table.dataTable thead th.dt-head-right,
table.dataTable thead td.dt-head-right,
table.dataTable tfoot th.dt-head-right,
table.dataTable tfoot td.dt-head-right {
  text-align: right;
}
table.dataTable thead th.dt-head-justify,
table.dataTable thead td.dt-head-justify,
table.dataTable tfoot th.dt-head-justify,
table.dataTable tfoot td.dt-head-justify {
  text-align: justify;
}
table.dataTable thead th.dt-head-nowrap,
table.dataTable thead td.dt-head-nowrap,
table.dataTable tfoot th.dt-head-nowrap,
table.dataTable tfoot td.dt-head-nowrap {
  white-space: nowrap;
}
table.dataTable tbody th.dt-body-left,
table.dataTable tbody td.dt-body-left {
  text-align: left;
}
table.dataTable tbody th.dt-body-center,
table.dataTable tbody td.dt-body-center {
  text-align: center;
}
table.dataTable tbody th.dt-body-right,
table.dataTable tbody td.dt-body-right {
  text-align: right;
}
table.dataTable tbody th.dt-body-justify,
table.dataTable tbody td.dt-body-justify {
  text-align: justify;
}
table.dataTable tbody th.dt-body-nowrap,
table.dataTable tbody td.dt-body-nowrap {
  white-space: nowrap;
}

table.dataTable,
table.dataTable th,
table.dataTable td {
  -webkit-box-sizing: content-box;
  -moz-box-sizing: content-box;
  box-sizing: content-box;
}

/*
 * Control feature layout
 */
.dataTables_wrapper {
  position: relative;
  clear: both;
  *zoom: 1;
  zoom: 1;
}
.dataTables_wrapper .dataTables_length {
  float: left;
}
.dataTables_wrapper .dataTables_filter {
  float: right;
  text-align: right;
}
.dataTables_wrapper .dataTables_filter input {
  margin-left: 0.5em;
}
.dataTables_wrapper .dataTables_info {
  clear: both;
  float: left;
  padding-top: 0.755em;
}


.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_processing,
.dataTables_wrapper .dataTables_paginate {
  color: #333333;
}
.dataTables_wrapper .dataTables_scroll {
  clear: both;
}
.dataTables_wrapper .dataTables_scroll div.dataTables_scrollBody {
  *margin-top: -1px;
  -webkit-overflow-scrolling: touch;
}">
    <thead>
        <tr>
            <th scope="col">Dossier/Client</th>
             <th scope="col">Numero</th>
            <th scope="col" >Objet</th>
             <th scope="col">#<br/>Type</th>
             <th scope="col">Statut </th>
            <th scope="col">Date facture/Date √©cheance</th>
            <th scope="col">Impression/Export compta</th>
            <th scope="col">Afficher</th>
            
        </tr>
        <tr class="warning">
           <th scope="col">Dossier/Client</th>
             <th scope="col">Numero</th>
            <th scope="col">Objet</th>
             <th scope="col">#<br/>Type</th>
             <th scope="col">Statut </th>
            <th scope="col">Date facture/Date √©cheance</th>
            <th scope="col">Impression/Export compta</th>
            <th scope="col"></th>       
        </tr>
    </thead>
    <tbody>
        <?php /* On parcours les factures pour les inserer dans le tableau */
        foreach($result_fac->fetchAll(PDO::FETCH_OBJ) as $fac) { ?>
        <tr>
           
            <td><?php echo $fac->fac_rf_dos; ?><br />
                <?php $entite = $pdo->prepare("SELECT ent_raisoc FROM entite WHERE ent_id = :ent");
                $entite->bindParam(":ent", $fac->fac_rf_ent);
                $entite->execute();
                $ent = $entite->fetch(PDO::FETCH_OBJ);
                echo $ent->ent_raisoc; ?>
            </td>
             <td><?php echo $fac->fac_id; ?></td>
            <td><?php echo $fac->fac_objet; ?></td>
            <td><span class="badge"><?php echo $fac->fac_num; ?></span><br /><?php echo $fac->fac_type; ?></td>
              <td>
              <?php 
              switch ($fac->fac_status) 
				{
              	case 0:echo "Prof &agrave; Valider";break;
              	case 1:echo "Prof Valid&eacute;e CPV";break;
              	case 2:echo "Prof Envoy&eacute;e ‡ Client";break;
              	case 3:echo "Prof accept&eacute;e";break;
              	case 4:echo "Facture imprim&eacute;e";break;
              	case 5:echo "Facture export&eacute;e";break;
              	case 6:echo "Facture Ech&eacute;ance depass&eacute;e";break;
              	case 7:echo "Facture RÈgl&eacute;e";break;	 
              }
              ?>
              </td>      
           
            <td><?php echo substr($fac->fac_date, 0, 11); ?><br /><?php echo substr($fac->fac_echeance, 0, 11); ?></td>
            <td><?php echo substr($fac->fac_impression, 0, 11); ?><br /><?php echo substr($fac->fac_export, 0, 11); ?></td>
           <!--  <td><?php echo $fac->fac_honoraires; ?></td>
            <td><?php echo $fac->fac_retro; ?></td>
            <td><?php echo $fac->fac_taxes; ?></td>
            <td><?php echo $fac->fac_montantht; ?></td>--> 
            <td align="center">
                <button class="btn btn-primary btn-sm" data-target="#modalDetailsfact_<?php echo $fac->fac_id; ?>" data-toggle="modal">
                    <i class="icon-plus fa fa-eye"></i> Afficher
                </button>
            
            <div class="modal fade" role="dialog" aria-labelledby="modalDetailsfact_<?php echo  $fac->fac_id; ?>" aria-hidden="true" id="modalDetailsfact_<?php echo $fac->fac_id;  ?>">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-body">                                    
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><br />
                                <div class="container-fluid">
                                  
                                  <!-- Panel des Details des Montants -->
                                  
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
                                                 <td><?php echo $fac->fac_honoraires; ?></td>
           										  <td><?php echo $fac->fac_retro; ?></td>
            									  <td><?php echo $fac->fac_taxes; ?></td>
           										 <td><?php echo $fac->fac_montantht; ?></td>       										 
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
                                            $result_ligne->bindParam(":id_fac", $fac->fac_id);
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
                                            </tr>

                                            <?php //On parcours des Repartitions pour les afficher                                                
                                                  $result_rep= $pdo->prepare("select rep_pourcentage,rep_rf_uti,rep_rf_fac,rep_type
                                                          from repartition where rep_rf_fac=:id_facture");
                                                     $result_rep->bindParam(":id_facture", $fac->fac_id);
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
                   					 aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $ligne_rep->rep_pourcentage; ?>%; min-width: 2em;">
                      				  <span><?php echo $ligne_rep->rep_pourcentage; ?>%</span>
                   				 </div>
               							 </div>                
               							 	 	 </td>
                                                  <td><?php echo $ligne_rep->rep_type; ?></td>                            
                                                </tr>   
                                            <?php } ?>
                                        </table>
                                    </div>      
                             
                                </div>
                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div>                
        </tr>
        <?php } ?>
    </tbody>
        <tfoot>
            <tr>
            <th scope="col">Dossier/Client</th>
             <th scope="col">Numero</th>
            <th scope="col">Objet</th>
             <th scope="col">#<br/>Type</th>
             <th scope="col">Statut </th>
            <th scope="col">Date facture/Date √©cheance</th>
            <th scope="col">Impression/Export compta</th>
            <th scope="col">Afficher</th>
         
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
                            type: "select"
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
