<?php
/********************************************
* createPrestation.php                      *
* Formulaire de creation d'une prestation   *
*                                           *
* Auteurs : Anne-Sophie Balestra            *
*           Abdoul Wahab Haidara            *
*           Yvan-Christian Maso             *
*           Baptiste Quere                  *
*           Yoann Le Taillanter             *
*                                           *
* Date de creation : 06/02/2015             *
********************************************/

// Connexion a la base de donnees
$pdo = new SPDO();

//On recupere les differentes operations disponibles
$stmt_ope = "SELECT t_ope_id, t_ope_libelle FROM type_operation";
$result_ope = $pdo->prepare($stmt_ope);
$result_ope->execute();

//On va chercher les entites possibles pour un dossier (brevet ou juridique)
$stmt_t_dos_ent = "SELECT DISTINCT(t_dos_entite) FROM type_dossier ORDER BY t_dos_entite";
$result_t_dos_ent = $pdo->prepare($stmt_t_dos_ent);
$result_t_dos_ent->execute();

//On recupere les codes de nomenclatures auxquels on voudra associer des prestations
$stmt_nom = "SELECT nom_id, nom_code FROM nomenclature ORDER BY nom_code";
$result_nom = $pdo->prepare($stmt_nom);
$result_nom->execute();

//On recupere tous les pays qui peuvent etre associés à une prestation
$stmt_pays_reg = "SELECT DISTINCT(pay_region) FROM pays ORDER BY pay_region";
$result_pays_reg = $pdo->prepare($stmt_pays_reg);
$result_pays_reg->execute();
?>
<!-- Contenu principal de la page -->
<div class="container">
    <!--Creation d'un formulaire avec la validation Bootstrap-->
    <form id="formNewPrestation" action="index.php?action=insertPrestation" method="post" role="form" data-toggle="validator"> 
        <h2>Nouvelle prestation</h2>        
        <div class="form-group">
            <label class="control-label" for="operation">Opération :</label>
            <select name="operation" id="operation" required class="form-control select2">
                <option></option> 
            <?php //On affiche toutes les operations comme des options du select
            foreach($result_ope->fetchAll(PDO::FETCH_OBJ) as $ope) { ?>
                <option value="<?php echo $ope->t_ope_id; ?>"><?php echo $ope->t_ope_libelle; ?></option>
            <?php } ?>
            </select>
        </div>
        <div class="form-group">
            <label class="control-label" for="ent_dossier">Type de dossier :</label><br />
            <!--En changeant l'entite, nous allons charger le select type_dossier avec les types associés à l'entite choisie-->
            <select name="ent_dossier" id="ent_dossier" required onchange="genererListeTypeDossier('#type_dossier', this.value);" class="form-control select2">
                <option></option>
            <?php // On affiche les entites disponibles 
            foreach($result_t_dos_ent->fetchAll(PDO::FETCH_OBJ) as $t_dos_ent) { ?>
                <option value="<?php echo $t_dos_ent->t_dos_entite; ?>"><?php echo $t_dos_ent->t_dos_entite; ?></option>
            <?php } ?>
            </select>
        </div>
        <div class="form-group" id="type_dossier_div" hidden="true">
            <!--On cree un select vide qui sera peuplé grace a un appel ajax-->
            <select name="type_dossier" id="type_dossier" required class="form-control select2">
                <option></option>
            </select>
        </div>
        <div class="form-group">
            <label class="control-label" for="nom_code">Code :</label>
            <!--On affiche les codes de nomenclature dans le select--> 
            <select name="nom_code" id="nom_code" required class="form-control select2">
                <option></option>
            <?php foreach($result_nom->fetchAll(PDO::FETCH_OBJ) as $nom) { ?>
                <option value="<?php echo $nom->nom_id; ?>"><?php echo $nom->nom_code; ?></option>
            <?php } ?>
            </select>
        </div>
        <div class="form-group">
            <label class="control-label" for="pays">Pays :</label>
            <!--On affiche les pays en les groupant par regions-->
            <select name="pays" id="pays" required class="form-control select2">
                <option></option>
            <?php foreach($result_pays_reg->fetchAll(PDO::FETCH_OBJ) as $pays_reg) { ?>
                <optgroup label="<?php echo $pays_reg->pay_region; ?>">
                    <?php $stmt_pays = "SELECT pay_id, pay_nom FROM pays WHERE pay_region = '" . $pays_reg->pay_region . "' ORDER BY pay_nom";
                    $result_pays = $pdo->prepare($stmt_pays);
                    $result_pays->execute();
                    foreach($result_pays->fetchAll(PDO::FETCH_OBJ) as $pays) { ?>
                        <option value="<?php echo $pays->pay_id; ?>"><?php echo $pays->pay_nom; ?></option>
                    <?php } ?>
                </optgroup>
            <?php } ?>
            </select>
        </div>
        <div class="form-group">
            <label class="control-label" for="prestation">Prestation :</label>
            <!--on prend le nom general de la prestation, i.e. nom du modele-->
            <input name="prestation" type="text" required class="form-control" id="prestation" maxlength="255" data-error="Veuillez entrer le nom de la prestation générale">
            <div class="help-block with-errors"></div>
        </div>
        <!--On gere ici la repartition des consultants soit par un select, soit avec un slider (les deux sont liés)-->
        <div class="form-group">
            <label class="control-label" for="repartition">Répartition des consultants :</label>
            <div class="input-group">
                <span class="input-group-addon">
                    <select id="pourcentage_select" class="form-inline" onchange="document.getElementById('pourcentage').innerHTML=this.value+'%';document.getElementById('repartition').value=this.value;">
                        <?php for($i=0; $i<=100; $i+=5) { ?>
                            <option <?php if($i == 50) { echo "selected"; } ?>><?php echo $i; ?></option>
                        <?php } ?>
                    </select>
                </span>
                <input name="repartition" id="repartition" onchange="document.getElementById('pourcentage').innerHTML=this.value+'%';document.getElementById('pourcentage_select').value=this.value;" type="range" min="0" max="100" step="5" required class="form-control">
                <span id="pourcentage" class="input-group-addon">50%</span>
            </div>
        </div>
<!--        <div class="form-group">
            <label class="control-label" for="nb_infos_prestation">Nombre de prestations liées :</label>
            On demande le nombre de prestations que nous voulons liées, afin de generer le bon nombre de blocs d'infos à remplir
            <input name="nbInfos" id="nbInfos" onkeyup="genererInfosPrestation('infosPrestation', $('#nbInfos').val(), 'Informations');" type="number" value="0" min='1' required class="form-control" data-error="Veuillez entrer le nombre de prestations associées à créer (au moins une)">
            <div class="help-block with-errors"></div>
        </div>-->
        
        <!--Bouton pour appeler le modal d'ajout d'une ligne de prestation-->
        <div class="form-group">
            <button type="button" data-toggle="modal" class="btn btn-default" data-target="#addInfoPrestation"><i class='icon-plus fa fa-plus'></i> Ajouter une prestation</button>
        </div>
        <!--input pour compter le nombre de prestations ajoutees (au moins une necessaire)-->
        <div class="form-group" hidden>
            <input name="nbInfos" id="nbInfos" type="number" value="0" min='1' required class="form-control" data-error="Veuillez ajouter au moins une prestation">
            <div class="help-block with-errors"></div>            
        </div>
        <!--input pour compter le nombre de prestations ajoutees en tout (meme si elles ont ete supprimees ensuite)-->
        <div class="form-group" hidden>
            <input name="nbInfosTot" id="nbInfosTot" type="number" value="0" required class="form-control">
        </div>
        <!--div qui contiendra les prestations ajoutees-->
        <div class="panel panel-default">
            <div class="panel-heading">Liste des prestations</div>
            <!-- Table -->
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Libellé</th>
                        <th scope="col">Type tarification</th>
                        <th scope="col">Tarif standard</th>
                        <th scope="col">Tarif junior</th>
                        <th scope="col">Tarif senior</th>
                        <th scope="col">Tarif manager</th>
                        <th scope="col">Modifier</th>
                    </tr>
                </thead>
                <tbody id='listePrestations'></tbody>
            </table>
        </div>
        <!--<ul id='listePrestations' class="list-group"></ul>-->
        <!--Ajout des lignes de prestations par modal-->
        <div class="modal fade" role="dialog" aria-labelledby="addInfoPrestation" aria-hidden="true" id="addInfoPrestation">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="addInfoPrestationLabel">Ajout d'une ligne de prestation</h4>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">                               
                            <div class="form-group">
                                <label class="control-label" for="libelle">Libellé :</label>
                                <input name="libelle" type="text" required onkeyup="checkAddUpdateLignePrestation('subAjout');" class="form-control" id="libelle" maxlength="255">
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="t_tarif">Type de tarification :</label>
                                <!--On choisit le type de tarification et on genere les champs qu'il faut en fonction-->
                                <select name="t_tarif" id="t_tarif" required class="form-control" onchange="afficherTarifs(this.value);checkAddUpdateLignePrestation('subAjout');">
                                    <option value="" disabled selected>Choisissez un type de tarification...</option>
                                    <option value="F">Forfaitaire</option>
                                    <option value="TH">Tarif Horaire</option>
                                </select>
                            </div>
                            <div class="form-group" id="tarif_std_div" style="display: none;">
                                <label class="control-label" for="tarif_std">Tarif :</label>
                                <div class="input-group">
                                    <input name="tarif_std" id="tarif_std" type="text" onkeyup="checkAddUpdateLignePrestation('subAjout');" pattern="\d+(\.\d{1,2})?" data-error='Veuillez renseigner un montant (ex: 400.50)' required class="form-control">
                                    <span class="input-group-addon">€</span>
                                </div>
                                <div class="help-block with-errors"></div>
                            </div>
                            <div class="form-group" id="tarif_jr_div" style="display: none;">
                                <label class="control-label" for="tarif_jr">Tarif junior :</label>
                                <div class="input-group">
                                    <input name="tarif_jr" id="tarif_jr" type="text" onkeyup="checkAddUpdateLignePrestation('subAjout');" pattern="\d+(\.\d{1,2})?" data-error='Veuillez renseigner un montant (ex: 400.50)' required class="form-control">
                                    <span class="input-group-addon">€</span>
                                </div>
                                <div class="help-block with-errors"></div>
                            </div>        
                            <div class="form-group" id="tarif_sr_div" style="display: none;">
                                <label class="control-label" for="tarif_sr">Tarif senior :</label>
                                <div class="input-group">
                                    <input name="tarif_sr" id="tarif_sr" type="text" onkeyup="checkAddUpdateLignePrestation('subAjout');" pattern="\d+(\.\d{1,2})?" data-error='Veuillez renseigner un montant (ex: 400.50)' required class="form-control">
                                    <span class="input-group-addon">€</span>
                                </div>
                                <div class="help-block with-errors"></div>
                            </div>        
                            <div class="form-group" id="tarif_mgr_div" style="display: none;">
                                <label class="control-label" for="tarif_mgr">Tarif manager :</label>
                                <div class="input-group">
                                    <input name="tarif_mgr" id="tarif_mgr" type="text" onkeyup="checkAddUpdateLignePrestation('subAjout');" pattern="\d+(\.\d{1,2})?" data-error='Veuillez renseigner un montant (ex: 400.50)' required class="form-control">
                                    <span class="input-group-addon">€</span>
                                </div>
                                <div class="help-block with-errors"></div>
                            </div>                       
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                        <button type="button" class="btn btn-primary" id="subAjout" disabled data-dismiss="modal" onclick="ajouterPrestationForm('listePrestations');">Ajouter</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        
        <!--div qui contiendra les informations sur les lignes de prestations-->
        <div class="panel-group" id="infosPrestation" role="tablist" aria-multiselectable="true"></div>
        <div>
            <a href="#" onclick="history.back()" class="btn btn-default" title="Annuler">Annuler</a>
            <input type="submit" name="button" class="btn btn-primary" id="button" value="Ajouter">
	</div>        
    </form>
</div>
<script type="text/javascript" charset="utf-8">
    //Pour chaque input select2, on les definit comme select2 et on leur donne un placeholder specifique
    $(document).ready(function() {
        $("#operation").select2({
            placeholder: "Choisissez une opération..."
        });
        $("#ent_dossier").select2({
            placeholder: "Choisissez une entité..."
        });
        $("#type_dossier").select2({
            placeholder: "Choisissez un type..."
        });
        $("#nom_code").select2({
            placeholder: "Choisissez un code..."
        });
        $("#pays").select2({
            placeholder: "Choisissez un pays..."
        });
    });
</script>