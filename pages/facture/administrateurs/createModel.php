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

$stmt_type_dossier = "SELECT DISTINCT(t_dos_type) FROM type_dossier ORDER BY t_dos_type";
$result_type_dossier = $pdo->prepare ( $stmt_type_dossier );
$result_type_dossier->execute();

?>
<!-- Contenu principal de la page -->
<div class="container">
	<form id="formNewModel" action="index.php?action=insertModel" method="post" role="form" data-toggle="validator">
		<h2>Nouveau Modèle</h2>
		<div class="form-group">
			<label class="control-label" for="name">Nom du modèle :</label>
			<input name="name" type="text" required class="form-control" id="name" maxlength="255" data-error="Veuillez entrer le nom du modèle">
			<div class="help-block with-errors"></div>
		</div>
		
		<div class="form-group">
			<label class="control-label" for="operation">Zone géographique </label>
			<input type="radio" name="area" value="France" checked> France <input
				type="radio" name="area" value="Etranger"> Etranger
		</div>

		<div class="form-group">
			<label class="control-label" for="operation">Type de dossier :</label>
			<select name="type_dossier" id="type_dossier" required class="form-control select2">
				<option></option>
            <?php 
            foreach($result_type_dossier->fetchAll(PDO::FETCH_OBJ) as $type_dossier) { ?>
                 <option value="<?php echo $type_dossier->t_dos_type; ?>"><?php echo $type_dossier->t_dos_type; ?></option>
            <?php } ?>
                    
            </select>
		</div>
		
		<div class="form-group">
			<label class="control-label" for="objet">Objet de la facture :</label>
			<input name="objet" type="text" required class="form-control" id="objet" maxlength="255" data-error="Veuillez entrer l'objet de la facture">
			<div class="help-block with-errors"></div>
		</div>

		<div class="form-group">
			<label class="control-label" for="language">Langue de la facture :</label>
			<input name="language" type="text" value="Français" required class="form-control" id="language" maxlength="255" data-error="Veuillez entrer la langue de la facture">
			<div class="help-block with-errors"></div>
		</div>
		
		<div class="form-group">
			<label class="control-label" for="TVA">TVA :</label> 
			<input name="TVA" type="number" value="20" required class="form-control" id="number" maxlength="255" data-error="Veuillez entrer le taux de TVA">
			<div class="help-block with-errors"></div>
		</div>
			
		<div>
			<input type="submit" name="button" class="btn btn-success" id="button" value="Ajouter"> <a href="#" onclick="history.back()" class="btn btn-danger" title="Annuler">Annuler</a>
		</div>
		
	</form>
</div>


<script type="text/javascript" charset="utf-8">
    $(document).ready(function() {
        $("#operation").select2({
            placeholder: "Choisissez une opÃ©ration..."
        });
        $("#ent_dossier").select2({
            placeholder: "Choisissez une entitÃ©..."
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