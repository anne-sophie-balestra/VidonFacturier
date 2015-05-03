<?php
/********************************************
* updateStatutFacture.php                   *
* Change le statut d'une facture            *
*                                           *
* Auteurs : Anne-Sophie Balestra            *
*           Abdoul Wahab Haidara            *
*           Yvan-Christian Maso             *
*           Baptiste Quere                  *
*           Yoann Le Taillanter             *
*                                           *
* Date de creation : 22/04/2015             *
********************************************/

//Connexion a la base de données
$pdo = new SPDO();

if(isset($_POST)){

    $error = "Veuillez selectionner au moins une facture";

    // On recupere les ID des factures des cases cochees.
    if($_POST['selection'] != NULL){
        $select = $_POST['selection'];
    } else {
        returnToListeFactureIndiv($error);
    }

    // Puis la valeur du statut a mettre a jour.
    $statut = $_POST['status_update'];

    // Creation de la requete de mise a jour du statut.
    $req_update_fac_statut = "UPDATE facture f SET fac_status = :code_status WHERE f.fac_id = :fac_id";
    $update_fac_sta = $pdo->prepare($req_update_fac_statut);
    $update_fac_sta->bindParam(':code_status', $statut); // On bind le statut qui est identique pour toutes les factures.

    // Puis pour chaque ID du tableau, on bind et execute la requete.
    for($i = 0; $i < sizeof($select); $i++) {
        $update_fac_sta->bindParam(':fac_id', $select[$i]);
        $update_fac_sta->execute();
    }

    returnToListIndiv();
}

/*****
 * returnToCreateModel : renvoie l'utilisateur sur la page de creation d'un modele s'il a mal rempli le formulaire
 *
 * String p_error: message d'erreur a afficher quand l'utilisateur est renvoyé sur la page
 ***/
function returnToListeFactureIndiv($p_error){
    echo "<script>alert(\"" . $p_error . "\");window.location.href='index.php?action=listeFacturesInd';</script>";
    exit;
}



