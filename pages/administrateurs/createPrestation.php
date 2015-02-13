<?php
/********************************************
* listeDossiers.php                         *
* Affiche tous les dossiers en liste        *
*                                           *
* Auteurs : Anne-Sophie Balestra            *
*           Abdoul Wahab Haidara            *
*           Yvan-Christian Maso             *
*           Baptiste Quere                  *
*           Yoann Le Taillanter             *
*                                           *
* Date de creation : 06/02/2015             *
********************************************/
?>
<!-- Contenu principal de la page -->
<div class="container">
    <form action="index.php?action=addPrestation" method="post" role="form"> 
        <h2>Nouvelle prestation</h2>
        <ul class="nav nav-tabs">
            <li role="Français" class="active"><a href="#FR">Français</a></li>
            <li role="Anglais"><a href="#EN">Anglais</a></li>
        </ul>
        <div id="FR">
            <div class="form-group">
                <label class="control-label" for="code">Code :</label>
                <input name="code" type="text" required class="form-control" id="code">
            </div>
            <div class="form-group">
                <label class="control-label" for="prestation">Prestation :</label>
                <input name="prestation" type="text" required class="form-control" id="prestation">
            </div>
            <div class="form-group">
                <label class="control-label" for="libelle">Libellé :</label>
                <input name="libelle" type="text" required class="form-control" id="libelle">
            </div>
            <div class="form-group">
                <label class="control-label" for="code_postal">Code postal :</label>
                <input name="code_postal" type="text" required class="form-control" id="code_postal">
            </div>
            <div class="form-group">
                <label class="control-label" for="tarif_std">Tarif standard :</label>
                <input name="tarif_std" type="text" required class="form-control" id="tarif_std">
            </div>
            <div class="form-group">
                <label class="control-label" for="office_id">Office :</label>
                <select name="office_id" id="office_id" class="form-control">
                    <option value="FR">Français</option>
                    <option value="US">Américain</option>
                </select>
            </div>
        </div>
        <div id="EN" style="display: none;">
        </div>
        <div>
            <input type="submit" name="button" class="btn btn-success" id="button" value="Ajouter">
            <a href="#" onclick="history.back()" class="btn btn-danger" title="Annuler">Annuler</a>
	</div>
    </form>
</div>

<script type="text/javascript" charset="utf-8">
    $(document).ready(function() {
        $(".js-example-basic-multiple").select2();
    });
</script>