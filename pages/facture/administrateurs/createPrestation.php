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

$stmt_ope = "SELECT t_ope_id, t_ope_libelle FROM type_operation ORDER BY t_ope_libelle";
$result_ope = $pdo->prepare($stmt_ope);
$result_ope->execute();

$stmt_t_dos = "SELECT t_dos_id, t_dos_entite, t_dos_type FROM type_dossier ORDER BY t_dos_entite, t_dos_type";
$result_t_dos = $pdo->prepare($stmt_t_dos);
$result_t_dos->execute();
?>
<!-- Contenu principal de la page -->
<div class="container">
    <form action="index.php?action=addPrestation" method="post" role="form"> 
        <h2>Nouvelle prestation</h2>        
        <div class="form-group">
            <label class="control-label" for="operation">Opération :</label>
            <select name="operation" id="operation" class="form-control select2">
                <option></option>
            <?php foreach($result_ope->fetchAll(PDO::FETCH_OBJ) as $ope) { ?>
                <option value="<?php echo $ope->t_ope_id; ?>"><?php echo $ope->t_ope_libelle; ?></option>
            <?php } ?>
            </select>
        </div>
        <div class="form-group form-inline">
            <label class="control-label" for="ent_dossier">Type de dossier :</label><br />
            <select name="ent_dossier" id="type_dossier" class="form-control select2" style="width: 49%; margin-right: 1.5%">
                <option></option>
            <?php foreach($result_t_dos->fetchAll(PDO::FETCH_OBJ) as $t_dos) { ?>
                <option value="<?php echo $t_dos->t_dos_id; ?>"><?php echo $t_dos->t_dos_entite; ?></option>
            <?php } ?>
            </select>
            <select name="type_dossier" id="type_dossier" class="form-control select2" style="width: 49%;">
                <option></option>
            <?php foreach($result_t_dos->fetchAll(PDO::FETCH_OBJ) as $t_dos) { ?>
                <option value="<?php echo $t_dos->t_dos_id; ?>"><?php echo $t_dos->t_dos_type; ?></option>
            <?php } ?>
            </select>
        </div>
        <div class="form-group">
            <label class="control-label" for="nom_code">Code :</label>
            <select name="nom_code" id="nom_code" class="form-control select2">
                <option></option>
            <?php foreach($result_nom->fetchAll(PDO::FETCH_OBJ) as $nom) { ?>
                <option value="<?php echo $nom->nom_id; ?>"><?php echo $nom->nom_code; ?></option>
            <?php } ?>
            </select>
        </div>
        <div class="form-group">
            <label class="control-label" for="prestation">Prestation :</label>
            <input name="prestation" type="text" required class="form-control" id="prestation">
        </div>
        <div class="form-group">
            <label class="control-label" for="libelle">Libellé :</label>
            <input name="libelle" type="text" required class="form-control" id="libelle">
        </div>
        <div class="form-group">
            <label class="control-label" for="tarif_std">Tarif standard :</label>
            <input name="tarif_std" type="text" required class="form-control" id="tarif_std">
        </div>
        <div class="form-group">
            <label class="control-label" for="office_id">Office :</label>
            <select name="office_id" id="office_id" class="form-control">
                <option value="FR">Français</option>
                <option value="US">Américain</option>
            </select>
        </div>
        <div>
            <input type="submit" name="button" class="btn btn-success" id="button" value="Ajouter">
            <a href="#" onclick="history.back()" class="btn btn-danger" title="Annuler">Annuler</a>
	</div>
    </form>
</div>

<!-- JQuery --> 
<script type="text/javascript" src="http://code.jquery.com/jquery-1.11.1.min.js"></script> 
<!-- Select2 -->  
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.2/select2.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.2/select2.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.2/select2_locale_fr.js"></script>
<script type="text/javascript" charset="utf-8">
    $(document).ready(function() {
        $(".select2").select2({placeholder: "Choisissez..."});
    });
</script>