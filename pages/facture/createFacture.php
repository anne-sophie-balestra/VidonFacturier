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

?>
<!-- Contenu principal de la page -->
<div class="container">
    <!--Creation d'un formulaire avec la validation Bootstrap-->
    <form id="formNewFacture" action="index.php?action=insertFacture" method="post" role="form" data-toggle="validator"> 
        <h2>Nouvelle facture</h2>           
        <!--Panel qui contient les infos du dossier-->
        <div class="panel panel-default">
            <div class="panel-heading">Informations sur le dossier</div><br />           
            <div class="form-group">
                <label class="control-label" for="dossier">Dossier associé :</label>
                <select id="dossier" class="form-control select2" required onchange="genererInfosDossier('infosDossier', this.value)">
                    <option></option>
                    <?php foreach($result_dossiers->fetchAll(PDO::FETCH_OBJ) as $dossier) { ?>
                    <option value="<?php echo $dossier->dos_id ?>"><?php echo $dossier->dos_numcomplet; ?></option>
                    <?php } ?>
                </select>
            </div>
            <!-- Table -->
            <table class="table">
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
        <!--Panel qui contient les infos des lignes de factures-->
        <div class="panel panel-default">
            <div class="panel-heading">Lignes de facture</div><br />
            <!-- Table -->
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Code</th>
                        <th scope="col">Libellé</th>
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
        <!--Panel qui contient les reglement effectués-->
        <div class="panel panel-default">
            <div class="panel-heading">Réglements</div><br />
            <!-- Table -->
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Date</th>
                        <th scope="col">Montant</th>
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
    });
    
</script>
