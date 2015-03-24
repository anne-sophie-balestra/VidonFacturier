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

//On recupere les types de dossiers existants
$stmt_type_dossier = "SELECT DISTINCT(t_dos_type) FROM type_dossier ORDER BY t_dos_type";
$result_type_dossier = $pdo->prepare ( $stmt_type_dossier );
$result_type_dossier->execute();

//On recupere les types d'operations existantes
$stmt_type_operation = "SELECT DISTINCT(t_ope_libelle) FROM type_operation ORDER BY t_ope_libelle";
$result_type_operation = $pdo->prepare ( $stmt_type_operation );
$result_type_operation->execute();

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

        <!--On demande a l'utilisateur le type de dossier et l'opération pour le modele de facture-->
		<div class="form-inline">
			<!-- Dossier -->
			<label class="control-label" for="t_dossier">Type de dossier :</label>
			<select name="type_dossier" id="t_dossier" required class="form-control select2">
				<option></option>
            <?php 
            foreach($result_type_dossier->fetchAll(PDO::FETCH_OBJ) as $type_dossier) { ?>
                 <option value="<?php echo $type_dossier->t_dos_type; ?>"><?php echo $type_dossier->t_dos_type; ?></option>
            <?php } ?>
            </select>
			
			<!-- Operation -->
			<label class="control-label" for="t_operation">Type d'op&egraveration :</label>
			<select name="type_operation" id="t_operation" required class="form-control select2">
				<option></option>
            <?php 
            foreach($result_type_operation->fetchAll(PDO::FETCH_OBJ) as $type_ope) { ?>
                 <option value="<?php echo $type_ope->t_ope_libelle; ?>"><?php echo $type_ope->t_ope_libelle; ?></option>
            <?php } ?>
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
        $("#type_dossier").select2({
            placeholder: "Choisissez un type de dossier..."
        });
    });
</script>