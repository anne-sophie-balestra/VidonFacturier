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

pg_connect("host=localhost port=5432 dbname=solent2 user=Miage2015 password=miage2015");

/* On verifie si une page a ete demandee */
if (filter_input(INPUT_GET, 'action') != NULL) { 
    /* En fonction de la page passee en action, on se dirige vers la page correspondante */
    switch (filter_input(INPUT_GET, 'action')) {
        case('listeDossiers'):
            require_once("pages/dossier/listeDossiers.php");
            break;
    }
}

/* Ajout pied de page */
require_once("footer.php");