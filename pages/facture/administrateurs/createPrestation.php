<?php
/********************************************
* listeDossiers.php                         *
* Affiche tous les dossiers en liste        *
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
            <select name="ent_dossier" id="ent_dossier" required onchange="genererListeTypeDossier('#type_dossier', this.value);" class="form-control select2">
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
                        <option>0</option>
                        <option>5</option>
                        <option>10</option>
                        <option>15</option>
                        <option>20</option>
                        <option>25</option>
                        <option>30</option>
                        <option>35</option>
                        <option>40</option>
                        <option>45</option>
                        <option selected>50</option>
                        <option>55</option>
                        <option>60</option>
                        <option>65</option>
                        <option>70</option>
                        <option>75</option>
                        <option>80</option>
                        <option>85</option>
                        <option>90</option>
                        <option>95</option>
                        <option>100</option>
                    </select>
                </span>
                <input name="repartition" id="repartition" onchange="document.getElementById('pourcentage').innerHTML=this.value+'%';document.getElementById('pourcentage_select').value=this.value;" type="range" min="0" max="100" step="5" required class="form-control">
                <span id="pourcentage" class="input-group-addon">50%</span>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label" for="nb_infos_prestation">Nombre de prestations liées :</label>
            <!--On demande le nombre de prestations que nous voulons liées, afin de generer le bon nombre de blocs d'infos à remplir-->
            <input name="nbInfos" id="nbInfos" onkeyup="genererInfosPrestation('infosPrestation', $('#nbInfos').val(), 'Informations');" type="number" value="0" min='1' required class="form-control" data-error="Veuillez entrer le nombre de prestations associées à créer (au moins une)">
            <div class="help-block with-errors"></div>
        </div>
        <!--div qui contiendra les informations sur les lignes de prestations-->
        <div class="panel-group" id="infosPrestation" role="tablist" aria-multiselectable="true"></div>
        <div>
            <input type="submit" name="button" class="btn btn-success" id="button" value="Ajouter">
            <a href="#" onclick="history.back()" class="btn btn-danger" title="Annuler">Annuler</a>
	</div>
    </form>
</div>
<script type="text/javascript" charset="utf-8">
    //Pour chaque input select2, on les definit comme select2 et on leur donne un placeholder specifique
    $(document).ready(function() {
        $("#operation").select2({
            placeholder: "Choisissez une opération..."
        });
        $("#ent_dossier").select2({
            placeholder: "Choisissez une entité..."
        });
        $("#type_dossier").select2({
            placeholder: "Choisissez un type..."
        });
        $("#nom_code").select2({
            placeholder: "Choisissez un code..."
        });
        $("#pays").select2({
            placeholder: "Choisissez un pays..."
        });
    });
</script>