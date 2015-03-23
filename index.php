<?php
/********************************************
* index.php                                 *
* Redirection des pages                     *
*                                           *
* Auteurs : Anne-Sophie Balestra            *
*           Abdoul Wahab Haidara            *
*           Yvan-Christian Maso             *
*           Baptiste Quere                  *
*           Yoann Le Taillanter             *
*                                           *
* Date de creation : 29/01/2015             *
********************************************/

/* Ajout en-tete avec le menu */
require_once("header.php");
        
require_once("BDD/SPDO.php");
require_once("ajax.php");
require_once("utiles.php");

/* On verifie si une page a ete demandee */
if (filter_input(INPUT_GET, 'action') != NULL) {
    /* En fonction de la page passee en action, on se dirige vers la page correspondante */
    switch (filter_input(INPUT_GET, 'action')) {
        /* Onglet Dossiers */
        case('listeDossiers'):
            require_once("pages/dossier/listeDossiers.php");
            break;
        
        /* Onglet Factures */
        //Formulaire de creation d'une prestation
        case('createPrestation'):
            require_once("pages/facture/administrateurs/createPrestation.php");
            break;
        
        //Recuperation des infos envoyées par le formulaire de creation de prestation avec verification php et insertion dans la base de donnees
        case('insertPrestation'):
            require_once("BDD/facture/administrateurs/prestation.php");
            break;
        
        //Formulaire de modification d'une prestation
        case('updatePrestation'):
            require_once("pages/facture/administrateurs/updatePrestation.php");
            break;
        
        //Recuperation des infos envoyées par le formulaire de modification de prestation avec verification php et modification/insertion dans la base de donnees
        case('changePrestation'):
            require_once("BDD/facture/administrateurs/prestation.php");
            break;
    }
}

/* Ajout pied de page */
require_once("footer.php");