<?php
/********************************************
* updatePrestation.php                      *
* Formulaire de modification d'une presta   *
*                                           *
* Auteurs : Anne-Sophie Balestra            *
*           Abdoul Wahab Haidara            *
*           Yvan-Christian Maso             *
*           Baptiste Quere                  *
*           Yoann Le Taillanter             *
*                                           *
* Date de creation : 23/03/2015             *
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
    <!--Formulaire de modification d'une prestation avec la validation Bootstrap-->
    <form id="formChangePrestation" action="index.php?action=changePrestation" method="post" role="form" data-toggle="validator"> 
        <h2>Modification de prestation</h2>
        <div class="form-group">
            <label class="control-label" for="nom_code">Code associé :</label>
            <!--On affiche les codes de nomenclature dans le select--> 
            <select name="nom_code" id="nom_code" required onchange="genererListePrestationsLiees('#prestations', this.value);" class="form-control select2">
                <option></option>
            <?php foreach($result_nom->fetchAll(PDO::FETCH_OBJ) as $nom) { ?>
                <option value="<?php echo $nom->nom_id; ?>"><?php echo $nom->nom_code; ?></option>
            <?php } ?>
            </select>
        </div>
        <div id="prestations_div" class="form-group" hidden="">            
            <label class="control-label" for="prestations">Prestation :</label>
            <select name="prestations" id="prestations" required onchange="genererInfosPrestationUpdate('#infosPrestation', this.value);" class="form-control select2">
                <option></option>
            </select>
        </div>
        <div id="infosPrestation" class="form-group">            
        </div>
        <div>
            <input type="submit" name="button" class="btn btn-success" id="button" value="Ajouter">
            <a href="#" onclick="history.back()" class="btn btn-danger" title="Annuler">Annuler</a>
	</div>
    </form>
</div>
<script type="text/javascript" charset="utf-8">
    //Pour chaque input select2, on les definit comme select2 et on leur donne un placeholder specifique
    $(document).ready(function() {
        $("#nom_code").select2({
            placeholder: "Choisissez un code..."
        });
        $("#prestations").select2({
            placeholder: "Choisissez une prestation..."
        });
    });
</script>