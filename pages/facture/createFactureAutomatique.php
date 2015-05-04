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

$id_dos = filter_input(INPUT_POST, 'dos_id');
$id_facture = filter_input(INPUT_POST, 'nom_modele');
$t_ope = filter_input(INPUT_POST, 'type_operation');

/* On recupere les infos du dossier en fonction de son id */
$stmt = "SELECT dos_id, dos_type, dos_numcomplet, dos_creadate, dos_titre, dos_rf_ent, ent_raisoc FROM dossier, entite WHERE dos_rf_ent = ent_id AND dos_id = :dos";
$result_dossier = $pdo->prepare($stmt);
$result_dossier->bindParam(":dos", $id_dos);
$result_dossier->execute();
$dossier = $result_dossier->fetch(PDO::FETCH_OBJ);

//On recupere les infos du modèle
$stmt_tfac = "SELECT t_fac_type, t_fac_objet, t_fac_area FROM type_facture WHERE t_fac_id= :id_facture";
$result_tfac = $pdo->prepare($stmt_tfac);
$result_tfac->bindParam(":id_facture", $id_facture);
$result_tfac->execute();
$t_fac = $result_tfac->fetch(PDO::FETCH_OBJ);

$stmt_ope = "SELECT t_ope_id, t_ope_libelle FROM type_operation";
$result_ope = $pdo->prepare($stmt_ope);
$result_ope->execute();


//On recupere les utilisateurs
$stmt_cons = "SELECT uti_id, uti_nom, uti_prenom FROM utilisateur ORDER BY uti_initial";
$result_cons = $pdo->prepare($stmt_cons);
$result_cons->execute();

//On recupere les lignes de facture associées au modele
$stmt_lignes = "SELECT prestation.pres_id, prestation.pres_rf_nom, nomenclature.nom_code, prestation.pres_libelle_ligne_fac, "
        . "prestation.pres_type, prestation.pres_t_tarif, prestation.pres_tarif_std "
        . "FROM type_facture, type_ligne, prestation, nomenclature "
        . "WHERE t_fac_id = :id_facture AND type_facture.t_fac_id = type_ligne.t_lig_rf_typ_fac "
        . "AND type_ligne.t_lig_rf_pres = prestation.pres_id AND nomenclature.nom_id = prestation.pres_rf_nom";
$result_lignes = $pdo->prepare($stmt_lignes);
$result_lignes->bindParam(":id_facture", $id_facture);
$result_lignes->execute();
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
                    <td><?php echo $dossier->dos_numcomplet;?><input type="hidden" value="<?php echo $dossier->dos_id; ?>" id="dos_id" name="dos_id"/></td>
                    <td><?php echo $dossier->dos_titre;?></td>
                    <td><?php echo $dossier->ent_raisoc;?><input type="hidden" value="<?php echo $dossier->dos_rf_ent; ?>" id="ent_id" name="ent_id"/></td>
                    <td><?php echo substr($dossier->dos_creadate, 0, 11);?></td>
                    <td><?php echo $dossier->dos_type;?></td>
                </tbody>
            </table>              
        </div>
        <!--Panel qui contient les infos générales de la facture-->
        <div class="panel panel-default" onmouseover="this.className='panel panel-primary';" onmouseout="this.className='panel panel-default';">
            <div class="panel-heading">Informations sur la facture</div><br /> 
            <div class="form-group">
                <label class="control-label" for="operation">Opération :</label><br />        
                <select name="operation" id="operation" required class="form-control select2">
                    <option></option> 
                <?php //On affiche toutes les operations comme des options du select
                foreach($result_ope->fetchAll(PDO::FETCH_OBJ) as $ope) { ?>
                    <option value="<?php echo $ope->t_ope_id; ?>" <?php if($ope->t_ope_id == $t_ope) { echo "selected"; } ?>><?php echo $ope->t_ope_libelle; ?></option>
                <?php } ?>
                </select>
            </div>
            <!--Renseignement de la zone geographique-->
            <div class="form-group">
                <label class="control-label" for="area">Prestation pour un dossier : </label><br />
                <input type="radio" name="area" id="FR" value="FR" <?php if($t_fac->t_fac_area == "France") { echo "checked"; } ?>> Français
                <input type="radio" name="area" id="E" value="E" <?php if($t_fac->t_fac_area != "France") { echo "checked"; } ?>> Etranger
            </div>
            <!--On choisit les consultants sur cette facture-->
            <div class="form-group">
                <label class="control-label" for="consultants[]">Consultants :</label>
                <select name="consultants[]" id="consultants" required class="form-control select2" multiple="multiple">
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
                    <option value="0" selected>Proforma à valider</option> 
                    <option value="1">Proforma validée CPV</option> 
                    <option value="2">Proforma envoyée au client</option> 
                    <option value="3">Proforma acceptée</option> 
                </select>
            </div><br />
            <div class="form-group">
                <label class="control-label" for="objet">Objet :</label>
                <input class="form-control" name="objet" type="text" required id="objet" value="<?php echo $t_fac->t_fac_objet; ?>" >
            </div><br />
            <div class="row">                
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label" for="total_honos">Honoraires :</label>
                        <div class="input-group">
                            <input class="form-control" name="total_honos" type="text" readonly required id="total_honos" value="0">
                            <span class="input-group-addon">€</span>
                        </div>
                    </div> 
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label" for="total_frais">Frais :</label>
                        <div class="input-group">
                            <input class="form-control" name="total_frais" type="text" readonly required id="total_frais" value="0">
                            <span class="input-group-addon">€</span>
                        </div>
                    </div> 
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label" for="total_taxes">Taxes :</label>
                        <div class="input-group">
                            <input class="form-control" name="total_taxes" type="text" readonly required id="total_taxes" value="0">
                            <span class="input-group-addon">€</span>
                        </div>
                    </div> 
                </div>
            </div>
            <div class="row"> 
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label" for="total_achats">Achats :</label>
                        <div class="input-group">
                            <input class="form-control" name="total_achats" type="text" readonly required id="total_achats" value="0">
                            <span class="input-group-addon">€</span>
                        </div>
                    </div> 
                </div>   
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label" for="total_reglements">Réglements :</label>
                        <div class="input-group">
                            <input class="form-control" name="total_reglements" type="text" readonly required id="total_reglements" value="0">
                            <span class="input-group-addon">€</span>
                        </div>
                    </div> 
                </div>
            </div>
            <div class="row">                
                <div class="col-md-6"></div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label" for="montantht">Montant HT :</label>
                        <div class="input-group">
                            <input class="form-control" name="montantht" type="text" readonly required id="montantht" value="0">
                            <span class="input-group-addon">€</span>
                        </div>
                    </div> 
                </div>
            </div>
            <div class="row">                
                <div class="col-md-6"> </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label" for="montantttc">Montant TTC :</label>
                        <div class="input-group">
                            <input class="form-control" name="montantttc" type="text" readonly required id="montantttc" value="0">
                            <span class="input-group-addon">€</span>
                        </div>
                    </div> 
                </div>
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
                <?php // On affiche les prestations ajoutées pour ce modele
                    $i=1;
                    foreach($result_lignes->fetchAll(PDO::FETCH_OBJ) as $ligne) { 
                        $type_lib = "";
                        $type_initial = "";
                        switch($ligne->pres_type) {
                            case 'honos' : $type_lib = "Honoraires"; $type_initial = "H"; break;
                            case 'frais' : $type_lib = "Frais"; $type_initial = "F"; break;
                            case 'taxes' : $type_lib = "Taxes"; $type_initial = "T"; break;
                        }
                        ?>
                        <tr id='ligneLigne<?php echo $i; ?>'> 
                            <td><?php echo $ligne->nom_code; ?>
                            <input type='hidden' value='<?php echo $ligne->pres_rf_nom; ?>' name='codeLigne<?php echo $i; ?>' id='codeLigne<?php echo $i; ?>'/></td>
                            <td><?php echo $ligne->pres_libelle_ligne_fac; ?>
                            <input type='hidden' value="<?php echo $ligne->pres_libelle_ligne_fac; ?>" name='libelleLigne<?php echo $i; ?>' id='libelleLigne<?php echo $i; ?>'/></td>
                            <td><?php echo $type_lib; ?>
                            <input type='hidden' value='<?php echo $type_initial; ?>' name='typeLigne<?php echo $i; ?>' id='typeLigne<?php echo $i; ?>'/></td>
                            <td>
                                <select name="tvaLigne<?php echo $i; ?>" id="tvaLigne<?php echo $i; ?>" required class="form-control">
                                    <option value="0" selected>0</option>
                                    <option value="20">20</option>
                                </select>
                            </td>
                            <td><?php if($ligne->pres_t_tarif == "F") { 
                                echo $ligne->pres_tarif_std; ?>
                                <input type='hidden' value='<?php echo $ligne->pres_tarif_std; ?>' name='tarifLigne<?php echo $i; ?>' id='tarifLigne<?php echo $i; ?>'/>
                            <?php } else { ?>
                                <select name="t_cons<?php echo $i; ?>" id="t_cons<?php echo $i; ?>" required class="form-control" onchange="getTarif('tarifLigne<?php echo $i; ?>', this.value, '<?php echo $ligne->pres_id; ?>');calculerTotal('totalLigne<?php echo $i; ?>', document.getElementById('quantiteLigne<?php echo $i; ?>').value, this.value)">
                                    <option></option>
                                    <option value="jr">Junior</option>
                                    <option value="sr">Senior</option>
                                    <option value="mgr">Manager</option>
                                </select>  
                                <input type='text' value='0' name='tarifLigne<?php echo $i; ?>' id='tarifLigne<?php echo $i; ?>' readonly/>
                            <?php } ?>
                            </td>
                            <td><input type='number' value="0" name='quantiteLigne<?php echo $i; ?>' id='quantiteLigne<?php echo $i; ?>' required onkeyup="calculerTotal('totalLigne<?php echo $i; ?>', this.value, document.getElementById('tarifLigne<?php echo $i; ?>').value);"/></td>
                            <td><input type='text' value='' name='totalLigne<?php echo $i; ?>' id='totalLigne<?php echo $i; ?>'/></td>
                            <td align='center'>
                                <a class='btn btn-primary btn-sm' onclick='genererModalLigneFacture("modalLigneFacture", <?php echo $i; ?>, true)'>
                                <i class='icon-plus fa fa-edit'></i> Modifier</a>
                            </td>
                            <td align='center'>
                                <a class='btn btn-danger btn-sm' onclick='supprimerLigneFactureForm(<?php echo $i; ?>)'><i class='icon- fa fa-remove'></i> Supprimer</a>
                            </td>
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
                <input name="nbLignesFac" id="nbLignesFac" style="display: none;" type="number" value="<?php echo $result_lignes->rowCount(); ?>" min='1' required class="form-control" data-error="Veuillez ajouter au moins une ligne de facture">   
                <div class="help-block with-errors"></div>
            </div>
            <!--input pour compter le nombre de ligne de facture ajoutees en tout (meme si elles ont ete supprimees ensuite)-->
            <div class="form-group" hidden>
                <input name="nbLignesFacTot" id="nbLignesFacTot" type="number" value="<?php echo $result_lignes->rowCount(); ?>" required class="form-control">
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
