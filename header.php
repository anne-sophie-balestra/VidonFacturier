<?php
/********************************************
* header.php                                *
* Ouverture de Html + menu                  *
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
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Solent 2 - Facturier</title>
        <!-- CSS -->
        <link rel="stylesheet" type="text/css" href="librairies/bootstrap-3.3.2-dist/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="librairies/bootstrap-3.3.2-dist/css/bootstrap-theme.min.css">
        <link rel="stylesheet" type="text/css" href="librairies/font-awesome-4.3.0/css/font-awesome.min.css">
    </head>

    <body>
        <nav class="navbar navbar-inverse" role="navigation">
            <div class="container">
                <div class="collapse navbar-collapse navbar-ex1-collapse">
                    <ul class="nav navbar-nav">
                        <li class="text-center"><a href="index.php"><i class="icon-home fa fa-home fa-2x icon-2x"></i><br> Accueil</a></li>
                        <li class="dropdown text-center">
                            <a href="#listeDossiers" class="text-center" data-toggle="dropdown">
                                <i class="glyphicon-th-list fa fa-th-list fa-2x icon-2x"></i><br>Liste dossiers
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>