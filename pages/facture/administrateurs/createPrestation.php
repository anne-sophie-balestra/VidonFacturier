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

$pdo = new SPDO();
$stmt_nom = "SELECT nom_id, nom_code FROM nomenclature ORDER BY nom_code";
$result_nom = $pdo->prepare($stmt_nom);
$result_nom->execute();

$stmt_ope = "SELECT t_ope_id, t_ope_libelle FROM type_operation";
$result_ope = $pdo->prepare($stmt_ope);
$result_ope->execute();

$stmt_t_dos_ent = "SELECT DISTINCT(t_dos_entite) FROM type_dossier ORDER BY t_dos_entite";
$result_t_dos_ent = $pdo->prepare($stmt_t_dos_ent);
$result_t_dos_ent->execute();

$stmt_pays_reg = "SELECT DISTINCT(pay_region) FROM pays ORDER BY pay_region";
$result_pays_reg = $pdo->prepare($stmt_pays_reg);
$result_pays_reg->execute();
?>
<!-- Contenu principal de la page -->
<div class="container">
    <form action="index.php?action=addPrestation" method="post" role="form"> 
        <h2>Nouvelle prestation</h2>        
        <div class="form-group">
            <label class="control-label" for="operation">Opération :</label>
            <select name="operation" id="operation" required class="form-control select2">
                <option></option>
            <?php foreach($result_ope->fetchAll(PDO::FETCH_OBJ) as $ope) { ?>
                <option value="<?php echo $ope->t_ope_id; ?>"><?php echo $ope->t_ope_libelle; ?></option>
            <?php } ?>
            </select>
        </div>
        <div class="form-group form-inline">
            <label class="control-label" for="ent_dossier">Type de dossier :</label><br />
            <select name="ent_dossier" id="ent_dossier" required onchange="genererListeTypeDossier('#type_dossier', this.value);" class="form-control select2" style="width: 49%; margin-right: 1.5%">
                <option></option>
            <?php foreach($result_t_dos_ent->fetchAll(PDO::FETCH_OBJ) as $t_dos_ent) { ?>
                <option value="<?php echo $t_dos_ent->t_dos_entite; ?>"><?php echo $t_dos_ent->t_dos_entite; ?></option>
            <?php } ?>
            </select>
            <select name="type_dossier" id="type_dossier" required class="form-control select2" style="width: 49%;" disabled>
                <option></option>
            </select>
        </div>
        <div class="form-group">
            <label class="control-label" for="nom_code">Code :</label>
            <select name="nom_code" id="nom_code" required class="form-control select2">
                <option></option>
            <?php foreach($result_nom->fetchAll(PDO::FETCH_OBJ) as $nom) { ?>
                <option value="<?php echo $nom->nom_id; ?>"><?php echo $nom->nom_code; ?></option>
            <?php } ?>
            </select>
        </div>
        <div class="form-group">
            <label class="control-label" for="pays">Pays :</label>
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
            <input name="prestation" type="text" required class="form-control" id="prestation">
        </div>
        <div class="form-group">
            <label class="control-label" for="nb_infos_prestation">Nombre de prestations liées :</label>
            <div class="input-group">
                <span class="input-group-btn">
                    <button onclick="genererInfosPrestation('infosPrestation', $('#nbInfos').val(), 'Informations');" class="btn btn-default" type="button">Créer</button>
                </span>
                <input name="nbInfos" id="nbInfos" type="number" value='1' required class="form-control">
            </div>
        </div>
        <div class="panel-group" id="infosPrestation" role="tablist" aria-multiselectable="true"></div>
        <div>
            <input type="submit" name="button" class="btn btn-success" id="button" value="Ajouter">
            <a href="#" onclick="history.back()" class="btn btn-danger" title="Annuler">Annuler</a>
	</div>
    </form>
</div>
<script type="text/javascript" charset="utf-8">
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