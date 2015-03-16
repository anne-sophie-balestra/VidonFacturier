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
        <script src="librairies/dhtmlxSuite/codebase/dhtmlx.js" type="text/javascript"></script>
        <link rel="stylesheet" type="text/css" href="librairies/dhtmlxSuite/codebase/dhtmlx.css">

        <script>
            var myMenu;
            function doInitMenu() {
            	//Créer un nouveau menu
                myMenu = new dhtmlXMenuObject("parentId");

                // Inflate la structure du menu dans le fichier data/menu.xml
                myMenu.loadStruct("data/menu.xml", function(){
                    
                    //Gestion des évènements lorsqu'on clique sur les éléments du menu.
                    myMenu.attachEvent("onClick", function(id){
                        switch(id) {
                            case "fac_mod":
                                document.location.href="index.php?action=fac_mod"; 
                                break;
                            case "fac_man":
                                document.location.href="index.php?action=fac_man"; 
                                break;
                            case "pre_new":
                                document.location.href="index.php?action=pre_new"; 
                                break;
                            case "pre_list":
                                document.location.href="index.php?action=pre_list";
                                break;
                            case "mod_new":
                                document.location.href="index.php?action=mod_new"; 
                                break;
                            case "mod_list":
                                document.location.href="index.php?action=mod_list"; 
                                break;
                            case "ftQuit":
                                alert("Quit");
                        } 
                    });

                });
            }
        </script>  

    </head>

    <body onload="doInitMenu();doOnLoad();"> 
        <div id="parentId"></div>
