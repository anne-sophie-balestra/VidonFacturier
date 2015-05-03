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

//On recupere les dossiers pour associer la facture a l'un d'eux
$stmt = "SELECT dos_id, dos_numcomplet FROM dossier WHERE EXTRACT(YEAR FROM dos_creadate) = " . (date('Y')-1) . " OR EXTRACT(YEAR FROM dos_creadate) = " . (date('Y'));
$result_dossiers = $pdo->prepare($stmt);
$result_dossiers->execute();

//On recupere les differentes operations disponibles
$stmt_ope = "SELECT t_ope_id, t_ope_libelle FROM type_operation";
$result_ope = $pdo->prepare($stmt_ope);
$result_ope->execute();

//On recupere les utilisateurs
$stmt_cons = "SELECT uti_id, uti_nom, uti_prenom FROM utilisateur ORDER BY uti_initial";
$result_cons = $pdo->prepare($stmt_cons);
$result_cons->execute();

?>
<!-- Contenu principal de la page -->
<div class="container">
    <!--Creation d'un formulaire avec la validation Bootstrap-->
    <form id="formNewFacture" action="index.php?action=insertFacture" method="post" role="form" data-toggle="validator"> 
        <h2>Nouvelle facture</h2>           
        <!--Panel qui contient les infos du dossier-->
        <div class="panel panel-default" onmouseover="this.className='panel panel-primary';" onmouseout="this.className='panel panel-default';">
            <div class="panel-heading">Informations sur le dossier</div><br />            
            <?php if(filter_input(INPUT_GET, 'id') == NULL) { ?>
                <div class="form-group">
                    <label class="control-label" for="dossier">Dossier associé :</label>
                    <select id="dossier" class="form-control select2" required onchange="genererInfosDossier('infosDossier', this.value);genererObjetFacture('#objet', this.value);">
                        <option></option>
                        <?php foreach($result_dossiers->fetchAll(PDO::FETCH_OBJ) as $dossier) { ?>
                        <option value="<?php echo $dossier->dos_id ?>" <?php if ((filter_input(INPUT_GET, 'id') != NULL) && (filter_input(INPUT_GET, 'id') == $dossier->dos_id)) { echo "selected"; } ?>><?php echo $dossier->dos_numcomplet; ?></option>
                        <?php } ?>
                    </select>
                </div>
            <?php } ?>
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
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tbody>
            </table>              
        </div>
        <!--Panel qui contient les infos générales de la facture-->
        <div class="panel panel-default" onmouseover="this.className='panel panel-primary';" onmouseout="this.className='panel panel-default';">
            <div class="panel-heading">Informations sur la facture</div><br /> 
            <div class="form-group">
                <label class="control-label" for="operation">Opération :</label>
                <select name="operation" id="operation" required class="form-control select2">
                    <option></option> 
                <?php //On affiche toutes les operations comme des options du select
                foreach($result_ope->fetchAll(PDO::FETCH_OBJ) as $ope) { ?>
                    <option value="<?php echo $ope->t_ope_id; ?>"><?php echo $ope->t_ope_libelle; ?></option>
                <?php } ?>
                </select>
            </div>
            <!--Renseignement de la zone geographique-->
            <div class="form-group">
                <label class="control-label" for="area">Prestation pour un dossier : </label><br />
                <input type="radio" name="area" id="FR" value="FR" checked onchange="changeMontantTTC(this.value);"> Français
                <input type="radio" name="area" id="E" value="E" onchange="changeMontantTTC(this.value);"> Etranger
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
                <select name="type_facture" id="type_facture" required class="form-control" style="width: 49.5%;">
                    <option id="F" selected>Facture</option> 
                    <option id="A">Avoir</option> 
                </select>
                <select name="type_proforma" id="type_proforma" required class="form-control" style="width: 49.5%;">
                    <option id="0" selected>Proforma à valider</option> 
                    <option id="1">Proforma validée CPV</option> 
                    <option id="2">Proforma envoyée au client</option> 
                    <option id="3">Proforma acceptée</option> 
                </select>
            </div><br />
            <div class="form-group">
                <label class="control-label" for="objet">Objet :</label>
                <input class="form-control" name="objet" type="text" required id="objet" value="">
            </div>
            <br />
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
                        <th scope="col">Montant €</th>
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
                <tbody id='listeLignesFacture'></tbody>
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
