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
//On lance une session pour conserver les données issues du dossier
//session_start();

/* Ajout en-tete avec le menu */
/*require_once("header.php");
require_once("BDD/SPDO.php");
require_once("ajax.php");
require_once("utiles.php");
*/
//On récupère l'id du dossier à travers l'url de la page
$url = "http://".$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];
$idPage = stristr($url, '&id=');
$id = substr($idPage, 4);

$pdo = new SPDO();

//On recupere les informations sur le dossier choisi 
$stmt_dossiers = "SELECT dos_id, dos_type , dos_numcomplet, dos_titre, dos_refclient, dos_rf_int, dos_statut, dos_creadate  FROM dossier WHERE dos_id='$id' AND EXTRACT(YEAR FROM dos_creadate) = " . (date('Y')-1) . " OR EXTRACT(YEAR FROM dos_creadate) = " . (date('Y'));
$result_dossiers = $pdo->prepare($stmt_dossiers);
$result_dossiers->execute();
//$result1 = $result_dossiers->fetchAll();

$stmt_tdossier = "SELECT dos_type  FROM dossier WHERE dos_id='$id' ";
$result_tdossier = $pdo->prepare($stmt_tdossier);
$result_tdossier->execute();
$result = $result_tdossier->fetchAll();

//var_dump($result1);


//On recupere le type de dossier issue de la requete ci-dessus
$t_dossier = $result[0]['dos_type'];

$stmt_t_ope = "SELECT t_ope_id, t_ope_libelle FROM type_operation JOIN type_facture ON type_facture.t_fac_rf_ope=type_operation.t_ope_id JOIN type_dossier ON type_facture.t_fac_rf_typdos=type_dossier.t_dos_id WHERE t_dos_type = :t_dossier ORDER BY t_dos_type";
$result_t_ope = $pdo->prepare($stmt_t_ope);
$result_t_ope->bindParam(":t_dossier", $t_dossier);
$result_t_ope->execute();



?>
<!-- Contenu principal de la page -->
<div class="container">
<div class="col-md-10">

    <!--Creation d'un formulaire avec la validation Bootstrap-->
    <form id="formNewModel" action="index.php?action=createFactureAutomatique" method="post" role="form" data-toggle="validator">
        <h2>Création automatique de facture</h2>

        <div class="form-group">
   
            <?php // On affiche les données sur le dossier
                foreach($result_dossiers->fetchAll(PDO::FETCH_OBJ) as $dossier) { ?>
                 <label class="control-label" for="num_dossier">Numéro du dossier :</label><br/>
                 <input type="text" name="num_dossier" value="<?php echo $dossier->dos_numcomplet; ?>" class="form-control" readonly><br>
                 
                 <label class="control-label" for="type_dossier">Type de dossier :</label><br/>
                 <input type="text" name="type_dossier" value="<?php echo $dossier->dos_type; ?>" onload="genererListeTypeOperation('#type_operation', type_dossier.value);" class="form-control" readonly><br/>
                  
                 <label class="control-label" for="objet_dossier">Objet du dossier :</label><br/>
                 <textarea rows="3" cols="30" name="objet" class="form-control" readonly><?php echo $dossier->dos_titre; ?></textarea><br/>

                 <label class="control-label" for="client">Client :</label><br/>
                 <input type="text" name="client" value="<?php echo $dossier->dos_refclient; ?>" class="form-control" readonly><br>

                 <input type="hidden" name="creadate" value="<?php echo substr($dossier->dos_creadate, 0, 11); ?>" class="form-control" readonly>

            <?php } ?>
        </div>
        

        <!--On demande a l'utilisateur le type de l'opération pour le modele de facture-->
        <div class="form-group">
            <!-- Operation -->
            <label class="control-label" for="ent_dossier">Type d'opération :</label>
            <select name="type_operation" id="type_operation" required onchange="genererListeNomModele('#nom_modele', type_dossier.value);" class="form-control select2">
                 <option></option>
                 <?php // On affiche les entites disponibles
                foreach($result_t_ope->fetchAll(PDO::FETCH_OBJ) as $t_ope) { ?>
                <option value="<?php echo $t_ope->t_ope_libelle; ?>"><?php echo $t_ope->t_ope_libelle; ?></option>
                <?php } ?>
             </select>
        </div>

         <!--Renseignement du nom du modele-->
       <div class="form-group">
            <label class="control-label" for="t_operation">Nom du modèle :</label>
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

 <script type="text/javascript">
var url = document.location.href;
idPage = url.substring(url.lastIndexOf( "&" )+4 );
//alert(idPage);
 </script>