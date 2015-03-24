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

//On va chercher les entites possibles pour un dossier (brevet ou juridique)
$stmt_t_dos_ent = "SELECT DISTINCT(t_dos_entite) FROM type_dossier ORDER BY t_dos_entite";
$result_t_dos_ent = $pdo->prepare($stmt_t_dos_ent);
$result_t_dos_ent->execute();

//On recupere les types de dossiers existants
$stmt_type_dossier = "SELECT DISTINCT(t_dos_type) FROM type_dossier ORDER BY t_dos_type";
$result_type_dossier = $pdo->prepare ( $stmt_type_dossier );
$result_type_dossier->execute();

?>
<!-- Contenu principal de la page -->
<div class="container">

    <!--Creation d'un formulaire avec la validation Bootstrap-->
    <form id="formNewModel" action="index.php?action=insertModel" method="post" role="form" data-toggle="validator">
		<h2>Nouveau Modèle</h2>
        <!--Renseignement du nom du modele-->
		<div class="form-group">
			<label class="control-label" for="name">Nom du modèle :</label>
			<input name="name" type="text" required class="form-control" id="name" maxlength="255" data-error="Veuillez entrer le nom du modèle">
			<div class="help-block with-errors"></div>
		</div>

        <!--Renseignement de la zone geographique-->
		<div class="form-group">
			<label class="control-label" for="area">Zone géographique </label>
			<input type="radio" name="area" value="France" checked> France
            <input type="radio" name="area" value="Etranger"> Etranger
		</div>

        <!--On demande a l'utilisateur quel est le type de dossier concerne par le modele de facture-->
		<div class="form-group">
			<label class="control-label" for="ent_dossier">Type de dossier :</label>
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

        <!--Renseignement de l'objet de la facture-->
		<div class="form-group">
			<label class="control-label" for="objet">Objet de la facture :</label>
			<input name="objet" type="text" required class="form-control" id="objet" maxlength="255" data-error="Veuillez entrer l'objet de la facture">
			<div class="help-block with-errors"></div>
		</div>

        <!--Renseignement de la langue de la facture-->
		<div class="form-group">
			<label class="control-label" for="language">Langue de la facture :</label>
			<input name="language" type="text" value="Français" required class="form-control" id="language" maxlength="255" data-error="Veuillez entrer la langue de la facture">
			<div class="help-block with-errors"></div>
		</div>

        <!--Renseignement du taux de TVA-->
		<div class="form-group">
			<label class="control-label" for="TVA">TVA :</label> 
			<input name="TVA" type="number" value="20" required class="form-control" id="TVA" maxlength="255" data-error="Veuillez entrer le taux de TVA">
			<div class="help-block with-errors"></div>
		</div>

        <!--Validation du formulaire-->
		<div>
			<input type="submit" name="button" class="btn btn-success" id="button" value="Ajouter">
            <a href="#" onclick="history.back()" class="btn btn-danger" title="Annuler">Annuler</a>
		</div>
		
	</form>
</div>


<script type="text/javascript" charset="utf-8">
    $(document).ready(function() {
        $("#ent_dossier").select2({
            placeholder: "Choisissez une entité..."
        });
        $("#type_dossier").select2({
            placeholder: "Choisissez un type de dossier..."
        });
    });
</script>