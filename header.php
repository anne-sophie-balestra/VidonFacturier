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
            	//Creer un nouveau menu
                myMenu = new dhtmlXMenuObject("parentId");

                // Inflate la structure du menu dans le fichier data/menu.xml
                myMenu.loadStruct("data/menu.xml", function(){
                    
                    //Gestion des evénements lorsqu'on clique sur les éléments du menu.
                    myMenu.attachEvent("onClick", function(id, zoneId, cas){
                        if ( id == "fTop") {
                            if(id == "fac_man") {
                                alert("facture manuelle!");                        
                            }
                        } else {
                            alert("test");
                        }
                    });

                });
            }
        </script>  

    </head>

    <body onload="doInitMenu();"> 
        <div id="parentId"></div>