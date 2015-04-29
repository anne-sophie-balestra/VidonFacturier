<?php
/********************************************
* facture.php                               *
* Gère les modifications dans la BD         *
* pour les factures                         *
*                                           *
* Auteurs : Anne-Sophie Balestra            *
*           Abdoul Wahab Haidara            *
*           Yvan-Christian Maso             *
*           Baptiste Quere                  *
*           Yoann Le Taillanter             *
*                                           *
* Date de creation : 10/04/2015             *
********************************************/

//Connexion a la base de données
$pdo = new SPDO();

/* On verifie l'action demandee */
if (filter_input(INPUT_GET, 'action') != NULL) {
    /* En fonction de la page passee en action, on se dirige vers la page correspondante */
    switch (filter_input(INPUT_GET, 'action')) {
        /* Ajout d'une nouvelle facture */
        case('insertFacture'):                        
            /* Erreur a retourner si besoin */
            $error = "Certains champs n'ont pas été remplis correctement. Merci de recommencer.";
            var_dump($_POST);
            exit;
            break;
    }
}

/*****
 * returnToCreateFacture : renvoie l'utilisateur sur la page de creation d'une facture s'il a mal rempli le formulaire
 * 
 * String p_error: message d'erreur a afficher quand l'utilisateur est renvoyé sur la page 
 ***/
function returnToCreateFacture($p_error){
    echo "<script>alert(\"" . $p_error . "\");window.location.href='index.php?action=createFacture';</script>";
    exit;
}