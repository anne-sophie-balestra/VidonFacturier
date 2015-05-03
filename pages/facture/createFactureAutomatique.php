<?php
/********************************************
* createFacture.php                         *
* Formulaire de creation d'une facture      *
* manuelle                                  *
*                                           *
* Auteurs : Anne-Sophie Balestra            *
*           Abdoul Wahab Haidara            *
*           Yvan-Christian Maso             *
*           Baptiste Quere                  *
*           Yoann Le Taillanter             *
*                                           *
* Date de creation : 02/04/2015             *
********************************************/

// Connexion a la base de donnees
$pdo = new SPDO();

$id_facture = $_POST['nom_modele'];
$t_ope = $_POST['type_operation'];

//On recupere les dossiers pour associer la facture a l'un d'eux
$stmt_tfact_obj = "SELECT t_fac_type, t_fac_objet FROM type_facture WHERE t_fac_id= :id_facture";
$result_tfact_obj = $pdo->prepare($stmt_tfact_obj);
$result_tfact_obj->bindParam(":id_facture", $id_facture);
$result_tfact_obj->execute();
foreach($result_tfact_obj->fetchAll(PDO::FETCH_OBJ) as $tfact) {
	$tfact_objet= $tfact->t_fac_objet;
	$tfact_type= $tfact->t_fac_type;
}


//On recupere les differentes operations disponibles si dans le formaulaire le type n'a pas �t� choisi
if ($t_ope == null){
	$stmt_ope = "SELECT t_ope_id, t_ope_libelle FROM type_operation";
	$result_ope = $pdo->prepare($stmt_ope);
	$result_ope->execute();
}

//On recupere les utilisateurs
$stmt_cons = "SELECT uti_id, uti_nom, uti_prenom FROM utilisateur ORDER BY uti_initial";
$result_cons = $pdo->prepare($stmt_cons);
$result_cons->execute();

$stmt_t_facture ="SELECT t_fac_id AS idFacture,t_fac_modelname AS modele, pres_id AS idPrestation,
	pres_rf_nom AS codeNomenclature,nom_code, pres_libelle_ligne_fac AS libelle, pres_t_tarif AS typeTarif, 
	pres_tarif_std AS tarif_standard, pres_tarif_jr AS junior,
        pres_tarif_sr AS senior, pres_tarif_mgr AS manager, t_dos_type
    FROM type_facture
    JOIN type_operation ON type_facture.t_fac_rf_ope = type_operation.t_ope_id
    JOIN type_dossier ON type_facture.t_fac_rf_typdos=type_dossier.t_dos_id
    JOIN type_ligne ON type_ligne.t_lig_rf_typ_fac=type_facture.t_fac_id
    JOIN prestation ON type_dossier.t_dos_id=prestation.pres_rf_typ_dossier
    JOIN nomenclature ON nomenclature.nom_id=prestation.pres_rf_nom
WHERE t_fac_id= :id_facture";
$result_t_facture = $pdo->prepare($stmt_t_facture);
$result_t_facture->bindParam(":id_facture", $id_facture);
$result_t_facture->execute();

?>
<!-- Contenu principal de la page -->
<div class="container">
    <!--Creation d'un formulaire avec la validation Bootstrap-->
    <form id="formNewFacture" action="index.php?action=insertFacture" method="post" role="form" data-toggle="validator"> 
        <h2>Nouvelle facture automatique</h2>           
        <!--Panel qui contient les infos du dossier-->
        <div class="panel panel-default" onmouseover="this.className='panel panel-primary';" onmouseout="this.className='panel panel-default';">
            <div class="panel-heading">Informations sur le dossier</div><br />           
            <div class="form-group">
                <label class="control-label" for="dossier">Dossier associé</label>
            </div>    
            
            <!-- Table -->
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Objet</th>
                        <th scope="col">Client</th>
                        <th scope="col">Date</th>
                        <th scope="col">Type</th>
                    </tr>
                </thead>
                <tbody id='infosDossier'>
                   <td><?php echo $_POST['num_dossier'];?></td>
                    <td><?php echo $_POST['objet'];?></td>
                    <td><?php echo $_POST['client'];?></td>
                    <td><?php echo $_POST['creadate'];?></td>
                    <td><?php echo $_POST['type_dossier'];?></td>
                </tbody>
            </table>              
        </div>
        <!--Panel qui contient les infos générales de la facture-->
        <div class="panel panel-default" onmouseover="this.className='panel panel-primary';" onmouseout="this.className='panel panel-default';">
            <div class="panel-heading">Informations sur la facture</div><br /> 
            <div class="form-group">
                <label class="control-label" for="operation">Opération :</label><br />             
                <?php if($t_ope != null){ ?> 
                	<input type="text" name="operation" value="<?php echo $t_ope; ?>" class="form-control" readonly><br /> 
               <?php }else{ ?>
                <select name="operation" id="operation" required class="form-control select2">
                    <option></option> 
                <?php //On affiche toutes les operations comme des options du select
                foreach($result_ope->fetchAll(PDO::FETCH_OBJ) as $ope) { ?>
                    <option value="<?php echo $ope->t_ope_id; ?>"><?php echo $ope->t_ope_libelle; ?></option>
                <?php }} ?>
                </select>
            </div>
            <!--Renseignement de la zone geographique-->
            <div class="form-group">
                <label class="control-label" for="area">Prestation pour un dossier : </label><br />
                <input type="radio" name="area" value="FR" checked> Français
                <input type="radio" name="area" value="E"> Etranger
            </div>
            <!--On choisit les consultants sur cette facture-->
            <div class="form-group">
                <label class="control-label" for="consultants">Consultants :</label>
                <select name="consultants" id="consultants" required class="form-control select2" multiple="multiple">
                    <option></option> 
                <?php //On affiche tous les utilisateurs de type consultant comme des options du select
                foreach($result_cons->fetchAll(PDO::FETCH_OBJ) as $cons) { ?>
                    <option value="<?php echo $cons->uti_id; ?>"><?php echo $cons->uti_prenom . " " . $cons->uti_nom; ?></option>
                <?php } ?>
                </select>
            </div>
            <!--On gere ici la repartition des consultants soit par un select, soit avec un slider (les deux sont liés)-->
            <div class="form-group">
                <label class="control-label" for="repartition">Répartition des consultants (reste pour l'administratif):</label>
                <div class="input-group">
                    <span class="input-group-addon">
                        <select id="pourcentage_select" class="form-inline" onchange="checkRepartition(this.value);">
                            <?php for($i=0; $i<=100; $i+=5) { ?>
                            <option <?php if($i == 50) { echo "selected"; } ?> value="<?php echo $i; ?>"><?php echo $i; ?></option>
                            <?php } ?>
                        </select>
                    </span>
                    <input name="repartition" id="repartition" onchange="checkRepartition(this.value);" type="range" min="0" max="100" step="5" required class="form-control">
                    <span id="pourcentage" class="input-group-addon">50%</span>
                </div>
            </div>
            <div class="progress">
                <div class="progress-bar progress-bar-primary" id="pourcentage_cons_div" style="width: 50%;">
                    <span id="pourcentage_cons">50%</span>
                </div>
                <div class="progress-bar progress-bar-info" id="pourcentage_admin_div" style="width: 50%;">
                    <span id="pourcentage_admin">50%</span>
                </div>
            </div>    
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label" for="dateFacture">Date :</label>
                    <input class="form-control" name="dateFacture" type="text" required id="dateFacture" value="<?php echo date("Y-m-d"); ?>" readonly>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label" for="dateEcheance">Date échéance :</label>
                    <input class="form-control" name="dateEcheance" type="text" required id="dateEcheance" value="<?php $date = date("Y-m-d"); echo date('Y-m-d', strtotime($date . ' + 30 days')); ?>" readonly>
                </div> 
            </div>   
            <div class="col-md-4">       
                <div class="form-group">
                    <label class="control-label" for="dateProforma">Date proforma :</label>
                    <input class="form-control" name="dateProforma" type="text" required id="dateProforma" value="<?php echo date("Y-m-d"); ?>" readonly>
                </div> 
            </div>    
            <div class="form-inline">
                <label class="control-label" for="type_facture">Type :</label><br />
                <select name="type_facture" id="type_facture" class="form-control" style="width: 49.5%;">
                	<?php if(strtolower($tfact_type)=="facture") { ?>
                		 <option id="F" readonly>Facture</option>
                	<?php }elseif(strtolower($tfact_type)=="avoir"){?>
                		<option id="A" readonly>Avoir</option> 
                	<?php }else{?>
	                    <option id="F" selected>Facture</option> 
	                    <option id="A">Avoir</option> 
                    <?php }?>
                </select>
                <select name="type_proforma" id="type_proforma" required class="form-control" style="width: 49.5%;">
                    <option id="AV" selected>Proforma à valider</option> 
                    <option id="V">Proforma validée CPV</option> 
                    <option id="E">Proforma envoyée au client</option> 
                    <option id="A">Proforma acceptée</option> 
                </select>
            </div><br />
            <div class="form-group">
                <label class="control-label" for="objet">Objet :</label>
                <input class="form-control" name="objet" type="text" required id="objet" value="<?php echo $tfact_objet; ?>" >
            </div> 
        </div>
        <!--Panel qui contient les reglement effectués-->
        <div class="panel panel-default" onmouseover="this.className='panel panel-primary';" onmouseout="this.className='panel panel-default';">
            <div class="panel-heading">Réglements</div><br />
            <!-- Table -->
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">Date</th>
                        <th scope="col">Montant</th>
                        <th scope="col">Devise</th>
                        <th scope="col">Supprimer</th>
                    </tr>
                </thead>
                <tbody id='listeReglements'></tbody>
            </table>
            <br />
            <!--Bouton pour appeler le modal d'ajout d'un reglement-->
            <div class="form-group">
                <button type="button" class="btn btn-default" onclick="genererModalReglement('modalReglement');"><i class='icon-plus fa fa-plus'></i> Ajouter un réglement</button>
            </div>
            <!--input pour compter le nombre de reglements ajoutes en tout (meme si elles ont ete supprimees ensuite)-->
            <div class="form-group" hidden>
                <input name="nbReglementsTot" id="nbReglementsTot" type="number" value="0" required class="form-control">
            </div>
            <!--modal pour ajouter ou modifier un reglement-->
            <div id="modalReglement"></div>
        </div>
        <!--Panel qui contient les infos des lignes de factures-->
        <div class="panel panel-default" onmouseover="this.className='panel panel-primary';" onmouseout="this.className='panel panel-default';">
            <div class="panel-heading">Lignes de facture</div><br />
            <!-- Table -->
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">Code</th>
                        <th scope="col">Libellé</th>
                        <th scope="col">Type</th>
                        <th scope="col">TVA (dossier Français)</th>
                        <th scope="col">Tarif</th>
                        <th scope="col">Quantité</th>
                        <th scope="col">Total</th>
                        <th scope="col">Modifier</th>
                        <th scope="col">Supprimer</th>
                    </tr>
                </thead>
                <tbody id='listeLignesFacture'>
                <?php // On affiche les entites disponibles
                    $i=1;
                    foreach($result_t_facture->fetchAll(PDO::FETCH_OBJ) as $facture) { ?>
                        <tr>
                            <th><?php echo $facture->nom_code;?></th>
                            <th><?php echo $facture->libelle ;?></th>
                            <th><?php echo $facture->typetarif ;?></th>
                            <th><input type="numeric" name="tva<?php echo $i; ?>" id="tva<?php echo $i; ?>" onkeyup="total('#total1');" class="form-control" ></th>
                            <th><?php 
								if($facture->typetarif=="tarif") {//frais
									echo $tarifm = $facture->tarif_standard ; 
								}elseif($facture->typetarif=="honos") {//forfetaire ou honos
									echo $tarifm = $facture->tarif_standard ; 
								}else{ //taxes
									echo $tarifm = $facture->tarif_standard ;
								}
                            ?>
                            
                            </th>
                            <th><input type="numeric" name="qte<?php echo $i; ?>" id="qte<?php echo $i; ?>" onkeyup="total('#total1');" class="form-control" ></th>
                            <th><input name="total<?php echo $i; ?>" type="text" disabled class="form-control" id="total<?php echo $i; ?>"></th>
                        </tr>
                        
                    <?php $i++; } ?>   
                </tbody>
            </table>
            <br />
            <!--Bouton pour appeler le modal d'ajout d'une ligne de facture-->
            <div class="form-group">
                <button type="button" class="btn btn-default" onclick="genererModalLigneFacture('modalLigneFacture',0);"><i class='icon-plus fa fa-plus'></i> Ajouter une ligne de facture</button>
            </div>
            <!--input pour compter le nombre de lignes de facture ajoutees (au moins une necessaire)-->
            <div class="form-group">
                <input name="nbLignesFac" id="nbLignesFac" style="display: none;" type="number" value="0" min='1' required class="form-control" data-error="Veuillez ajouter au moins une ligne de facture">   
                <div class="help-block with-errors"></div>
            </div>
            <!--input pour compter le nombre de ligne de facture ajoutees en tout (meme si elles ont ete supprimees ensuite)-->
            <div class="form-group" hidden>
                <input name="nbLignesFacTot" id="nbLignesFacTot" type="number" value="0" required class="form-control">
            </div>
            <!--modal pour ajouter ou modifier une ligne de facture-->
            <div id="modalLigneFacture"></div>
        </div>
        <!--Panel qui contient les achats                                       -->
        <div class="panel panel-default" onmouseover="this.className='panel panel-primary';" onmouseout="this.className='panel panel-default';">
            <div class="panel-heading">Achats</div><br />
            <!-- Table -->
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">Code</th>
                        <th scope="col">Libellé</th>
                        <th scope="col">CPV</th>
                        <th scope="col">Fournisseur</th>
                        <th scope="col">Devise</th>
                        <th scope="col">Tarif</th>
                        <th scope="col">Quantité</th>
                        <th scope="col">Total Achat</th>
                        <th scope="col">Total Vente €</th>
                        <th scope="col">Réel ?</th>
                        <th scope="col">Modifier</th>
                        <th scope="col">Supprimer</th>
                    </tr>
                </thead>
                <tbody id='listeAchats'></tbody>
            </table>
            <br />
            <!--Bouton pour appeler le modal d'ajout d'une ligne de facture-->
            <div class="form-group">
                <button type="button" class="btn btn-default" id="modalAjoutAchat" <?php if(filter_input(INPUT_GET, 'id') == NULL) { ?> onclick="genererModalAchat('modalAchat',0,document.getElementById('dossier').value);" <?php } else { ?> onclick="genererModalAchat('modalAchat',0,'<?php echo filter_input(INPUT_GET, 'id'); ?>');" <?php } ?> disabled><i class='icon-plus fa fa-plus'></i> Ajouter un achat</button>
            </div>
            <!--input pour compter le nombre d'achats ajoutes en tout (meme si elles ont ete supprimees ensuite)-->
            <div class="form-group" hidden>
                <input name="nbAchatsTot" id="nbAchatsTot" type="number" value="0" required class="form-control">
            </div>
            <!--modal pour ajouter ou modifier un achat-->
            <div id="modalAchat"></div>
        </div>
        <div>
            <a href="#" onclick="history.back()" class="btn btn-default" title="Annuler">Annuler</a>
            <input type="submit" name="button" class="btn btn-primary" id="button" value="Ajouter">
	</div>        
    </form>
</div>
<script type="text/javascript" charset="utf-8">
    //Pour chaque input select2, on les definit comme select2 et on leur donne un placeholder specifique    
    //Pour regler les conflits entre l'utilisation des modals et des select2, on appelle les select2 avec un alias different de $
    jQuery.noConflict();
    $(document).ready(function() {
        jQuery("#dossier").select2({
            placeholder: "Choisissez un dossier..."
        });
        jQuery("#operation").select2({
            placeholder: "Choisissez une opération..."
        });
        jQuery("#consultants").select2({
            placeholder: "Choisissez un consultant...",
            maximumSelectionLength: 3
        });
        <?php //On verifie si on nous a passé un dossier ou non
        if (filter_input(INPUT_GET, 'id') != NULL) { ?>
            genererInfosDossier('infosDossier', '<?php echo filter_input(INPUT_GET, 'id') ?>');
            genererObjetFacture('#objet', '<?php echo filter_input(INPUT_GET, 'id') ?>');
        <?php } ?>
    });
</script>
