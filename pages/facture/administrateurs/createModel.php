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

//On va chercher les entites possibles pour un dossier (brevet ou juridique)
$stmt_t_dos_ent = "SELECT DISTINCT(t_dos_entite) FROM type_dossier ORDER BY t_dos_entite";
$result_t_dos_ent = $pdo->prepare($stmt_t_dos_ent);
$result_t_dos_ent->execute();

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
			<!-- Operation -->
			<label class="control-label" for="t_operation">Type d'opération :</label>
			<select name="type_operation" id="t_operation" required onchange="genererListePresta('#select_presta', document.getElementById('ent_dossier').value, document.getElementById('type_dossier').value, this.value);" class="form-control select2">
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

		<!-- Bouton d'ajout d'une prestation au modèle -->
		<button type="button" class="btn btn-default" data-toggle="modal" data-target="#modalPresta">
		  <span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> Ajouter une prestation
		</button>

		<!--input pour compter le nombre de prestations ajoutees (au moins une necessaire)-->
        <div class="form-group" hidden>
            <input name="nbInfos" id="nbInfos" type="number" value="0" min='1' required class="form-control" data-error="Veuillez ajouter au moins une prestation">
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

		<!-- Modal -->
		<div class="modal fade" id="modalPresta" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		  <div class="modal-dialog">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title" id="myModalLabel">Choisissez la prestation à ajouter</h4>
		      </div>
		      <div class="modal-body">
		      	<div class="form-group">
	        		<!--On cree un select vide qui sera peuplé grace a un appel ajax-->
				    <select name="select_presta" id="select_presta" required class="form-control select2">
				    	<option></option>
				    </select>
		    	</div>

		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		        <button type="button" class="btn btn-primary" id="subAjout" data-dismiss="modal" onclick="ajouterPrestationModel('listePrestations');">Ajouter</button>
		      </div>
		    </div>
		  </div>
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
		$("#ent_dossier").select2({
			placeholder: "Choisissez une entité..."
		});
    });
</script>