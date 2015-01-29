<?php
/********************************************
* footer.php                                *
* Fermeture de Html + script Js             *
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
        <!-- Bootstrap --> 
        <script type="text/javascript" src="librairies/bootstrap-3.3.2-dist/js/bootstrap.min.js"></script> 
    </body>
    <footer class="footer">
        <div class="well-sm">
            <?php echo (date('Y')!="2015" ? " - " . date('Y') : ""); ?>2015. Solent 2. Tous droits réservés. Facturuer version 1.0
        </div>
    </footer>
</html>