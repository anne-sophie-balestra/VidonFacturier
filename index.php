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
* Date de dernière modif : 29/01/2015       *
********************************************/

/* Ajout en-tete avec le menu */
require_once("header.php");

/* On verifie si une page a ete demandee */
if (isset($_GET['action']))
{ 
    /* En fonction de la page passee en action, on se dirige vers la page correspondante */
    switch ($_GET['action'])
    {
        case('listeDossiers'):
            require_once("pages/listeDossiers");
            break;
    }
}

/* Ajout pied de page */
require_once("footer.php");
?>
