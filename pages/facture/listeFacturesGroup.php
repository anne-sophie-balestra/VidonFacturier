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



//On va chercher les factures depuis l'année derniere a aujourd'hui qui sont individuelles cad les valeurs de client.cli_libellefact=1
$stmt = "SELECT fac_id, fac_num, fac_type, fac_rf_ent,fac_rf_dos, fac_objet,fac_status, fac_date, fac_echeance, fac_impression, 
fac_export, fac_honoraires, fac_retro, fac_taxes, fac_montantht FROM facture 
		Join entite on facture.fac_rf_ent=entite.ent_id 
		Join client on entite.ent_id=client.cli_rf_ent
		WHERE client.cli_libellefact=2
		AND EXTRACT(YEAR FROM fac_creadate) between ". (date('Y')-1) . "and  " . (date('Y'));
$result_fac = $pdo->prepare($stmt);
$result_fac->execute();


?>
<!-- Contenu principal de la page -->
<div class="container" style="width:100%;">
    <h2>Factures Group&eacute;es</h2>
    <table class="table table-striped table-bordered table-condensed table-hover" id="lfacturesInd">
    <thead>
        <tr>
            <th scope="col">Dossier/Client</th>
             <th scope="col">Numero</th>
            <th scope="col" >Objet</th>
             <th scope="col">Type</th>
             <th scope="col">Statut </th>
            <th scope="col">Date facture/Date écheance</th>
            <th scope="col">Impression/Export compta</th>
            <th scope="col">Afficher</th>
            
        </tr>
        <tr>
           <th scope="col">Dossier/Client</th>
             <th scope="col">Numero</th>
            <th scope="col">Objet</th>
             <th scope="col">Type</th>
             <th scope="col">Statut </th>
            <th scope="col">Date facture/Date écheance</th>
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
              	case 2:echo "Prof Envoy&eacute;e au Client";break;
              	case 3:echo "Prof accept&eacute;e";break;
              	case 4:echo "Facture imprim&eacute;e";break;
              	case 5:echo "Facture export&eacute;e";break;
              	case 6:echo "Facture Ech&eacute;ance depass&eacute;e";break;
              	case 7:echo "Facture R�gl&eacute;e";break;	 
              }
              ?>
              </td>      
           
            <td><?php echo substr($fac->fac_date, 0, 11); ?><br /><?php echo substr($fac->fac_echeance, 0, 11); ?></td>
            <td><?php echo substr($fac->fac_impression, 0, 11); ?><br /><?php echo substr($fac->fac_export, 0, 11); ?></td>
         
            <td align="center">
                <a class="btn btn-primary btn-sm" 
                 href="index.php?action=details_facture&facid=<?php echo $fac->fac_id;?>&honos=<?php echo $fac->fac_honoraires;
                 ?>&retro=<?php echo $fac->fac_retro;?>&montant=<?php echo $fac->fac_montantht;?>
                 &taxes=<?php echo $fac->fac_taxes;?>&dispa=2">
                    <i class="icon-plus fa fa-eye"></i> Afficher
                </a>
             </td>               
        </tr>
        <?php } ?>
    </tbody>
        <tfoot>
            <tr>
            <th scope="col">Dossier/Client</th>
             <th scope="col">Numero</th>
            <th scope="col">Objet</th>
             <th scope="col">Type</th>
             <th scope="col">Statut </th>
            <th scope="col">Date facture/Date écheance</th>
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
