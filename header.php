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
        <link rel="stylesheet" type="text/css" href="librairies/DataTables-1.10.4/media/css/jquery.dataTables.min.css">
    </head>

    <body>
        <nav class="navbar navbar-inverse nav-stacked" role="navigation">            
            <a class="navbar-brand" href="index.php"><img src="img/logo.png" width="75" height="63" style="margin-top: -10%; margin-left: 15%;"></a>
            <div class="container">
                <div class="collapse navbar-collapse navbar-ex1-collapse">
                    <ul class="nav navbar-nav">
                        <li class="text-center"><a href="index.php"><i class="icon-home fa fa-home fa-2x icon-2x"></i><br>Accueil</a></li>
                        <li class="text-center"><a href="#agenda"><i class="icon-calendar fa fa-calendar fa-2x icon-2x"></i><br>Agenda</a></li>
                        <li class="text-center"><a href="#societes-contacts"><i class="icon-briefcase fa fa-briefcase fa-2x icon-2x"></i><br>Sociétés/Contacts</a></li>
                        <li class="text-center"><a href="index.php?action=listeDossiers"><i class="icon-folder-open fa fa-folder-open fa-2x icon-2x"></i><br>Dossiers</a></li>
                        <li class="text-center"><a href="#communications"><i class="icon-phone fa fa-phone fa-2x icon-2x"></i><br>Communications</a></li>
                        <li class="text-center"><a href="#actions"><i class="icon-dashboard fa fa-dashboard fa-2x icon-2x"></i><br>Actions</a></li>
                        <li class="text-center"><a href="#factures"><i class="icon-list-alt fa fa-list-alt fa-2x icon-2x"></i><br>Factures</a></li>
                        <li class="text-center"><a href="#autres"><i class="icon-shopping-cart fa fa-shopping-cart fa-2x icon-2x"></i><br>Autres</a></li>
                    </ul>
                </div>
            </div>
        </nav>