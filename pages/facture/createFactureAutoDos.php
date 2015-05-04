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
 * Date de creation : 26/03/2015             *
 ********************************************/

//Connexion a la base
$pdo = new SPDO();

//On recupere l'id du dossier
$id = filter_input(INPUT_GET, 'id');

//On recupere les informations sur le dossier choisi 
$stmt_dossier = "SELECT dos_id, dos_type , dos_numcomplet, dos_titre, ent_raisoc, dos_rf_ent, dos_statut FROM dossier, entite WHERE dossier.dos_rf_ent = entite.ent_id AND dos_id = :id";
$result_dossier = $pdo->prepare($stmt_dossier);
$result_dossier->bindParam(":id", $id);
$result_dossier->execute();
$dossier = $result_dossier->fetch(PDO::FETCH_OBJ);

//On recupere le type de dossier issu de la requete ci-dessus
$t_dossier = $dossier->dos_type;

$stmt_t_ope = "SELECT t_ope_id, t_ope_libelle FROM type_operation";
$result_t_ope = $pdo->prepare($stmt_t_ope);
$result_t_ope->execute();
?>
<!-- Contenu principal de la page -->
<div class="container">
    <div class="col-md-10">
        <!--Creation d'un formulaire avec la validation Bootstrap-->
        <form id="formChoixModele" action="index.php?action=createFactureAutomatique" method="post" role="form" data-toggle="validator">
            <h2>Création automatique de facture</h2>
            <div class="form-group">
                <!--On affiche les données sur le dossier-->
                <label class="control-label" for="num_dossier">Numéro du dossier :</label><br/>
                <input type="text" name="num_dossier" value="<?php echo $dossier->dos_numcomplet; ?>" class="form-control" readonly><br>
                <input type="hidden" id="dos_id" name="dos_id" value="<?php echo $dossier->dos_id; ?>" class="form-control" ><br>

                <label class="control-label" for="type_dossier">Type de dossier :</label><br/>
                <input type="text" name="type_dossier" value="<?php echo $dossier->dos_type; ?>" onload="genererListeTypeOperation('#type_operation', type_dossier.value);" class="form-control" readonly><br/>

                <label class="control-label" for="objet_dossier">Objet du dossier :</label><br/>
                <textarea rows="3" cols="30" name="objet" class="form-control" readonly><?php echo $dossier->dos_titre; ?></textarea><br/>

                <label class="control-label" for="client">Client :</label><br/>
                <input type="text" name="client" value="<?php echo $dossier->ent_raisoc; ?>" class="form-control" readonly><br>
                <input type="hidden" id="ent_id" name="ent_id" value="<?php echo $dossier->dos_rf_ent; ?>" class="form-control"><br>
            </div>
            <!--On demande a l'utilisateur le type de l'opération pour le modele de facture-->
            <div class="form-group">
                <!-- Operation -->
                <label class="control-label" for="type_operation">Type d'opération :</label>
                <select name="type_operation" id="type_operation" required onchange="genererListeNomModele('#nom_modele', type_dossier.value);" class="form-control select2">
                     <option></option>
                     <?php // On affiche les entites disponibles
                    foreach($result_t_ope->fetchAll(PDO::FETCH_OBJ) as $t_ope) { ?>
                    <option value="<?php echo $t_ope->t_ope_id; ?>"><?php echo $t_ope->t_ope_libelle; ?></option>
                    <?php } ?>
                 </select>
            </div>
             <!--Renseignement du nom du modele-->
            <div class="form-group">
                <label class="control-label" for="nom_modele">Nom du modèle :</label>
                <select name="nom_modele" id="nom_modele" required  class="form-control select2">
                     <option></option>
                 </select>
           </div>
            <div>
                <input type="submit" name="button" class="btn btn-success" id="button" value="Valider">
                <a href="#" onclick="history.back()" class="btn btn-danger" title="Annuler">Annuler</a>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript" charset="utf-8">
    $(document).ready(function() {
        $("#type_dossier").select2({
            placeholder: "Choisissez un type de dossier..."
        });

    $("#ent_dossier").select2({
        placeholder: "Choisissez une entité..."
    });

    $("#nom_modele").select2({
         placeholder: "Choisissez le nom du modèle..."
     });
    $("#type_operation").select2({
         placeholder: "Choisissez le type d'opération..."
     });
     $("#client").select2({
         placeholder: "Choisissez le client..."
     });

    });
</script>