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

//var_dump($_POST);

$modele = $_POST['nom_modele'];
$t_ope = $_POST['type_operation'];

$stmt_t_facture ="SELECT t_fac_id AS idFacture,t_fac_objet AS objetFacture,t_fac_modelname AS modele,
        pres_id AS idPrestation,pres_rf_nom AS codeNomenclature,nom_code, pres_libelle_ligne_fac AS libelle,pres_t_tarif AS typeTarif, pres_tarif_std AS forfetaire, pres_tarif_jr AS junior, 
        pres_tarif_sr AS senior, pres_tarif_mgr AS manager, t_dos_type
    FROM type_facture 
    JOIN type_operation ON type_facture.t_fac_rf_ope = type_operation.t_ope_id 
    JOIN type_dossier ON type_facture.t_fac_rf_typdos=type_dossier.t_dos_id
    JOIN type_ligne ON type_ligne.t_lig_rf_typ_fac=type_facture.t_fac_id
    JOIN prestation ON type_dossier.t_dos_id=prestation.pres_rf_typ_dossier
    JOIN nomenclature ON nomenclature.nom_id=prestation.pres_rf_nom
WHERE t_fac_id= :id_facture";
$result_t_facture = $pdo->prepare($stmt_t_facture);
$result_t_facture->bindParam(":id_facture", $modele);
$result_t_facture->execute();

/*foreach ($result_t_facture as $key) {
    print_r($key);
}*/

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
                <label class="control-label" for="dossier">Dossier associé</label>
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
                    <td><?php echo $_POST['num_dossier'];?></td>
                    <td><?php echo $_POST['objet'];?></td>
                    <td><?php echo $_POST['client'];?></td>
                    <td><?php echo $_POST['creadate'];?></td>
                    <td><?php echo $_POST['type_dossier'];?></td>
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
                <tbody id='listeLignesFacture'>
                    <?php // On affiche les entites disponibles
                    $i=1;
                    foreach($result_t_facture->fetchAll(PDO::FETCH_OBJ) as $facture) { ?>
                        <tr>
                            <td><?php echo $facture->nom_code;?></td>
                            <td><?php echo $facture->libelle ;?></td>
                            <td><?php echo $tarifm = $facture->forfetaire ; ?>
                            <input type="hidden" name="tarifm<?php echo $i; ?>" id="tarifm<?php echo $i; ?>" value="<?php echo $tarifm ?>" class="form-control">
                            </td>
                            <td><input type="numeric" name="qte<?php echo $i; ?>" id="qte<?php echo $i; ?>" onkeyup="total('#total1');" class="form-control" ></td>
                            <td><input name="total<?php echo $i; ?>" type="text" disabled class="form-control" id="total<?php echo $i; ?>"></td>
                        </tr>
                        
                    <?php $i++; } ?> 
                </tbody>
            </table>
            <br/>
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
    
    function total(p_id){

    //on recupere la quantite et la tarif
    var qte = document.getElementById('qte1').value;
    var tarifm=document.getElementById('tarifm2').value;
    //alert(qte);
    //On calcule le total
    if((qte != "") || (isANumber(qte))) {
               document.getElementById('qte1').value = qte;
               document.getElementById('total1').value = tarifm*qte;
    }else {
        document.getElementById('total1').value = 0;
    }
}
 
</script>