<?php
/********************************************
* createPrestation.php                      *
* Formulaire de creation d'une prestation   *
*                                           *
* Auteurs : Anne-Sophie Balestra            *
*           Abdoul Wahab Haidara            *
*           Yvan-Christian Maso             *
*           Baptiste Quere                  *
*           Yoann Le Taillanter             *
*                                           *
* Date de creation : 06/02/2015             *
********************************************/

// Connexion a la base de donnees
$pdo = new SPDO();

//On recupere les differentes operations disponibles
$stmt_ope = "SELECT t_ope_id, t_ope_libelle FROM type_operation";
$result_ope = $pdo->prepare($stmt_ope);
$result_ope->execute();

//On va chercher les entites possibles pour un dossier (brevet ou juridique)
$stmt_t_dos_ent = "SELECT DISTINCT(t_dos_entite) FROM type_dossier ORDER BY t_dos_entite";
$result_t_dos_ent = $pdo->prepare($stmt_t_dos_ent);
$result_t_dos_ent->execute();

//On recupere les codes de nomenclatures auxquels on voudra associer des prestations
$stmt_nom = "SELECT nom_id, nom_code FROM nomenclature ORDER BY nom_code";
$result_nom = $pdo->prepare($stmt_nom);
$result_nom->execute();

//On recupere tous les pays qui peuvent etre associés à une prestation
$stmt_pays_reg = "SELECT DISTINCT(pay_region) FROM pays ORDER BY pay_region";
$result_pays_reg = $pdo->prepare($stmt_pays_reg);
$result_pays_reg->execute();
?>
<!-- Contenu principal de la page -->
<div class="container">
    <!--Creation d'un formulaire avec la validation Bootstrap-->
    <form id="formNewPrestation" action="index.php?action=insertPrestation" method="post" role="form" data-toggle="validator"> 
        <h2>Nouvelle prestation</h2>        
        <div class="form-group">
            <label class="control-label" for="remote">Remote :</label>
            <select name="remote" id="remote" required class="form-control select2">
                <option></option> 
            </select>
        </div>
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
        <div class="form-group">
            <label class="control-label" for="ent_dossier">Type de dossier :</label><br />
            <!--En changeant l'entite, nous allons charger le select type_dossier avec les types associés à l'entite choisie-->
            <select name="ent_dossier" id="ent_dossier" required onchange="genererListeTypeDossier('#type_dossier', this.value, true);" class="form-control select2">
                <option></option>
            <?php // On affiche les entites disponibles 
            foreach($result_t_dos_ent->fetchAll(PDO::FETCH_OBJ) as $t_dos_ent) { ?>
                <option value="<?php echo $t_dos_ent->t_dos_entite; ?>"><?php echo $t_dos_ent->t_dos_entite; ?></option>
            <?php } ?>
            </select>
        </div>
        <div class="form-group">
            <!--On cree un select vide qui sera peuplé grace a un appel ajax-->
            <select name="type_dossier" id="type_dossier" required class="form-control select2">
                <option></option>
            </select>
        </div>
        <div class="form-group">
            <label class="control-label" for="nom_code">Code :</label>
            <!--On affiche les codes de nomenclature dans le select--> 
            <select name="nom_code" id="nom_code" required class="form-control select2">
                <option></option>
            <?php foreach($result_nom->fetchAll(PDO::FETCH_OBJ) as $nom) { ?>
                <option value="<?php echo $nom->nom_id; ?>"><?php echo $nom->nom_code; ?></option>
            <?php } ?>
            </select>
        </div>
        <div class="form-group">
            <label class="control-label" for="pays">Pays :</label>
            <!--On affiche les pays en les groupant par regions-->
            <select name="pays" id="pays" required class="form-control select2">
                <option></option>
            <?php foreach($result_pays_reg->fetchAll(PDO::FETCH_OBJ) as $pays_reg) { ?>
                <optgroup label="<?php echo $pays_reg->pay_region; ?>">
                    <?php $stmt_pays = "SELECT pay_id, pay_nom FROM pays WHERE pay_region = '" . $pays_reg->pay_region . "' ORDER BY pay_nom";
                    $result_pays = $pdo->prepare($stmt_pays);
                    $result_pays->execute();
                    foreach($result_pays->fetchAll(PDO::FETCH_OBJ) as $pays) { ?>
                        <option value="<?php echo $pays->pay_id; ?>"><?php echo $pays->pay_nom; ?></option>
                    <?php } ?>
                </optgroup>
            <?php } ?>
            </select>
        </div>
        <div class="form-group">
            <label class="control-label" for="prestation">Prestation :</label>
            <!--on prend le nom general de la prestation, i.e. nom du modele-->
            <input name="prestation" type="text" required class="form-control" id="prestation" maxlength="255" data-error="Veuillez entrer le nom de la prestation générale">
            <div class="help-block with-errors"></div>
        </div>
        <!--On gere ici la repartition des consultants soit par un select, soit avec un slider (les deux sont liés)-->
        <div class="form-group">
            <label class="control-label" for="repartition">Répartition des consultants :</label>
            <div class="input-group">
                <span class="input-group-addon">
                    <select id="pourcentage_select" class="form-inline" onchange="document.getElementById('pourcentage').innerHTML=this.value+'%';document.getElementById('repartition').value=this.value;">
                        <?php for($i=0; $i<=100; $i+=5) { ?>
                            <option <?php if($i == 50) { echo "selected"; } ?>><?php echo $i; ?></option>
                        <?php } ?>
                    </select>
                </span>
                <input name="repartition" id="repartition" onchange="document.getElementById('pourcentage').innerHTML=this.value+'%';document.getElementById('pourcentage_select').value=this.value;" type="range" min="0" max="100" step="5" required class="form-control">
                <span id="pourcentage" class="input-group-addon">50%</span>
            </div>
        </div>
        
        <!--Bouton pour appeler le modal d'ajout d'une ligne de prestation-->
        <div class="form-group">
            <button type="button" class="btn btn-default" id="buttonModalAddInfoPrestation" onclick="genererModalLignePrestation('modalLignePrestation',0);"><i class='icon-plus fa fa-plus'></i> Ajouter une prestation</button>
        </div>
        <!--input pour compter le nombre de prestations ajoutees (au moins une necessaire)-->
        <div class="form-group">
            <input name="nbInfos" id="nbInfos" style="display: none;" type="number" value="0" min='1' required class="form-control" data-error="Veuillez ajouter au moins une ligne de prestation">   
            <div class="help-block with-errors"></div>
        </div>
        <!--input pour compter le nombre de prestations ajoutees en tout (meme si elles ont ete supprimees ensuite)-->
        <div class="form-group" hidden>
            <input name="nbInfosTot" id="nbInfosTot" type="number" value="0" required class="form-control">
        </div>
        
        <!--div qui contiendra les prestations ajoutees-->
        <div class="panel panel-default">
            <div class="panel-heading">Liste des prestations</div>
            <!-- Table -->
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Libellé</th>
                        <th scope="col">Type tarification</th>
                        <th scope="col">Tarif standard</th>
                        <th scope="col">Tarif junior</th>
                        <th scope="col">Tarif senior</th>
                        <th scope="col">Tarif manager</th>
                        <th scope="col">Modifier</th>
                    </tr>
                </thead>
                <tbody id='listePrestations'></tbody>
            </table>
        </div>
        
        <!--modal pour ajouter ou modifier une ligne de prestation-->
        <div id="modalLignePrestation"></div>
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
        jQuery("#operation").select2({
            placeholder: "Choisissez une opération..."
        });
        jQuery("#ent_dossier").select2({
            placeholder: "Choisissez une entité..."
        });
        jQuery("#type_dossier").select2({
            placeholder: "Choisissez un type..."
        });
        jQuery("#nom_code").select2({
            placeholder: "Choisissez un code..."
        });
        jQuery("#pays").select2({
            placeholder: "Choisissez un pays..."
        });
        
        jQuery("#remote").select2({
            placeholder: "Choisissez une option...", 
            minimumInputLength: 2,
            ajax: {
                url: "ajax.php?action=genererInfosRemote",
                dataType: 'json',
                data: function (params) {
                    return { q: params.term };
                },
                results: function (data) {
                    return { results: data };
                }
            } 
        });
    });
    
</script>
