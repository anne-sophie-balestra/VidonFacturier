<?php
/********************************************
* ajax.php                                  *
* Contient les fonctions ajax               *
*                                           *
* Auteurs : Anne-Sophie Balestra            *
*           Abdoul Wahab Haidara            *
*           Yvan-Christian Maso             *
*           Baptiste Quere                  *
*           Yoann Le Taillanter             *
*                                           *
* Date de creation : 02/03/2015             *
********************************************/

require_once("BDD/SPDO.php");

/* On verifie si une page a ete demandee */
if (filter_input(INPUT_GET, 'action') != NULL) { 
    /* En fonction de la page passee en action, on se dirige vers la page correspondante */
    switch (filter_input(INPUT_GET, 'action')) {
        /*
         * FONCTIONS AJAX
         */
        //Permet de retourner un JSON contenant les types de dossier en fonction de l'entite choisie afin de peupler le select dans create prestation
        case('genererListeTypeDossier'):
            $entite = (filter_input(INPUT_GET, 'ent') != NULL ? filter_input(INPUT_GET, 'ent') : "");
            genererListeTypeDossier($entite);
            break;

        // Genere la liste des prestations piour la page createModel.php suivant l'entite, le dossier et l'operation.
        case('genererListePresta'):
            $dos = (filter_input(INPUT_GET, 'dos') != NULL ? filter_input(INPUT_GET, 'dos') : "");
            $ope = (filter_input(INPUT_GET, 'ope') != NULL ? filter_input(INPUT_GET, 'ope') : "");
            genererListePresta($dos, $ope);
            break; 
        
        //Genere le modal pour ajouter ou modifier une ligne de prestation dans create prestation
        case('genererModalLignePrestation'):
            $prestation = (filter_input(INPUT_GET, 'pre') != NULL ? filter_input(INPUT_GET, 'pre') : 0);
            genererModalLignePrestation($prestation);
            break;

        //Genere le modal pour modifier une prestation dans la liste des prestations
        case('genererModalPrestation'):
            $prestation = (filter_input(INPUT_GET, 'pre') != NULL ? filter_input(INPUT_GET, 'pre') : 0);
            genererModalPrestation($prestation);
            break;

        //Genere le modal pour modifier une prestation dans la liste des prestations
        case('listClient'):
            $term = (filter_input(INPUT_GET, 'q') != NULL ? filter_input(INPUT_GET, 'q') : "");
            getListeClient($term);
            break;

        //Genere une ligne de tableau dans contenant la prestation dans createModel.php
        case('getPrestationTabFromID'):
            $presta = (filter_input(INPUT_GET, 'presta') != NULL ? filter_input(INPUT_GET, 'presta') : "");
            $nbInfos = (filter_input(INPUT_GET, 'nbInfos') != NULL ? filter_input(INPUT_GET, 'nbInfos') : 0);
            $nbInfosTot = (filter_input(INPUT_GET, 'nbInfosTot') != NULL ? filter_input(INPUT_GET, 'nbInfosTot') : 0);
            getPrestationTabFromID($presta, $nbInfos, $nbInfosTot);
            break;

        //Genere une ligne de tableau dans contenant la prestation dans createModel.php
        case('genererInfosRemote'):
            genererListePaysRemote(filter_input(INPUT_GET, 'q'));
            break;              
    }
}

/*****
 * genererListePaysRemote : genere les infos du select pour les types de dossier en fonction de l'entite (brevet ou juridique)
 *
 * @param String $p_term : chaine de recherche
 ***/
function genererListePaysRemote($p_term) {    
    $pdo = new SPDO;
    
    /* On recupere les types de dossier en fonction de l'entite */
    $stmt_pays = "SELECT pay_id, pay_nom FROM pays WHERE pay_nom LIKE :term ORDER BY pay_nom";
    $result_pays = $pdo->prepare($stmt_pays);
    $term = $p_term . "%";
    $result_pays->bindParam(":term", $term);
    $result_pays->execute();
    
    //On cree un array avec l'id et le nom du type de dossier que l'on va retourner en JSON
       
    if($result_pays->rowcount() != 0) {
        foreach($result_pays->fetchAll(PDO::FETCH_OBJ) as $pays) {
            $json[] = array("id"=>$pays->pay_id,"text"=>$pays->pay_nom);
        }
    } else {
        $json[] = array("id"=>"0","text"=>"Aucun r&eacute;sultat trouv&eacute;..");
    }
    
    echo json_encode($json);
}

/*****
 * genererListeTypeDossier : genere les infos du select pour les types de dossier en fonction de l'entite (brevet ou juridique)
 *
 * @param String $p_entite : entite choisie
 ***/
function genererListeTypeDossier($p_entite) {    
    $pdo = new SPDO;
    
    /* On recupere les types de dossier en fonction de l'entite */
    $stmt_t_dos_type = "SELECT t_dos_id, t_dos_type FROM type_dossier WHERE t_dos_entite = :entite ORDER BY t_dos_type";
    $result_t_dos_type = $pdo->prepare($stmt_t_dos_type);
    $result_t_dos_type->bindParam(":entite", $p_entite);
    $result_t_dos_type->execute();
    
    //On cree un array avec l'id et le nom du type de dossier que l'on va retourner en JSON
    $array_dos = array();    
    foreach($result_t_dos_type->fetchAll(PDO::FETCH_OBJ) as $t_dos_type) {
        $array_dos[$t_dos_type->t_dos_id] = $t_dos_type->t_dos_type;
    }
    echo json_encode($array_dos);
}

/*****
 * genererListePresta : genere le select pour la list des presta dans la création d'un modèle (type_facture)
 *
 * @param String $ent : entite de la presta
 * @param String $t_dos : type de dossier
 * @param String $t_ope : type d'opération
 ***/
function genererListePresta($t_dos, $t_ope)
{    
    $pdo = new SPDO;
    
    /* On recupere les types de dossier en fonction de l'entite */
    $stmt_presta_list =
      "SELECT p.pres_libelle_ligne_fac, p.pres_id FROM prestation p
        WHERE p.pres_rf_typ_dossier = :dossier
        AND pres_rf_typ_operation = :operation";
    
    $result_presta_list = $pdo->prepare($stmt_presta_list);
    $result_presta_list->bindParam(':dossier', $t_dos);
    $result_presta_list->bindParam(':operation', $t_ope);
    $result_presta_list->execute();
    
    //On cree un array avec l'id et le nom du type de presta que l'on va retourner en JSON
    $array_presta = array();    
    foreach($result_presta_list->fetchAll(PDO::FETCH_OBJ) as $presta_list) {
        $array_presta[$presta_list->pres_id] = $presta_list->pres_libelle_ligne_fac;
    }
    echo json_encode($array_presta);
}

/*****
 * genererModalLignePrestation : genere le modal pour ajouter ou modifier une ligne de prestation dans createPrestation
 * 
 * @param int $prestation : contient le numero de la ligne de prestation si c'est une modification, 0 si c est un ajout
 ***/
function genererModalLignePrestation($prestation) {  
    $action = "Ajout";
    $actionVerbe = "Ajouter";
    if($prestation != 0){
        $action = "Modification";
        $actionVerbe = "Modifier";
    }
    ?>  
    <!--Ajout des lignes de prestations par modal-->
    <div class="modal fade" role="dialog" aria-labelledby="modalInfoPrestation" aria-hidden="true" id="modalInfoPrestation">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="modalInfoPrestationLabel"><?php echo $action; ?> d'une ligne de prestation</h4>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">                               
                        <div class="form-group">
                            <label class="control-label" for="libelle">Libellé :</label>
                            <input name="libelle" type="text" required onkeyup="checkLignePrestation('subAction');" class="form-control" id="libelle" maxlength="255">
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="t_tarif">Type de tarification :</label>
                            <!--On choisit le type de tarification et on genere les champs qu'il faut en fonction-->
                            <select name="t_tarif" id="t_tarif" required class="form-control" onchange="afficherTarifs(this.value);checkLignePrestation('subAction');">
                                <option value="" disabled selected>Choisissez un type de tarification...</option>
                                <option value="F">Forfaitaire</option>
                                <option value="TH">Tarif Horaire</option>
                            </select>
                        </div>
                        <div class="form-group" id="tarif_std_div" style="display: none;">
                            <label class="control-label" for="tarif_std">Tarif :</label>
                            <div class="input-group">
                                <input name="tarif_std" id="tarif_std" type="text" onkeyup="checkLignePrestation('subAction');" pattern="\d+(\.\d{1,2})?" data-error='Veuillez renseigner un montant (ex: 400.50)' required class="form-control">
                                <span class="input-group-addon">€</span>
                            </div>
                            <div class="help-block with-errors"></div>
                        </div>
                        <div class="form-group" id="tarif_jr_div" style="display: none;">
                            <label class="control-label" for="tarif_jr">Tarif junior :</label>
                            <div class="input-group">
                                <input name="tarif_jr" id="tarif_jr" type="text" onkeyup="checkLignePrestation('subAction');" pattern="\d+(\.\d{1,2})?" data-error='Veuillez renseigner un montant (ex: 400.50)' required class="form-control">
                                <span class="input-group-addon">€</span>
                            </div>
                            <div class="help-block with-errors"></div>
                        </div>        
                        <div class="form-group" id="tarif_sr_div" style="display: none;">
                            <label class="control-label" for="tarif_sr">Tarif senior :</label>
                            <div class="input-group">
                                <input name="tarif_sr" id="tarif_sr" type="text" onkeyup="checkLignePrestation('subAction');" pattern="\d+(\.\d{1,2})?" data-error='Veuillez renseigner un montant (ex: 400.50)' required class="form-control">
                                <span class="input-group-addon">€</span>
                            </div>
                            <div class="help-block with-errors"></div>
                        </div>        
                        <div class="form-group" id="tarif_mgr_div" style="display: none;">
                            <label class="control-label" for="tarif_mgr">Tarif manager :</label>
                            <div class="input-group">
                                <input name="tarif_mgr" id="tarif_mgr" type="text" onkeyup="checkLignePrestation('subAction');" pattern="\d+(\.\d{1,2})?" data-error='Veuillez renseigner un montant (ex: 400.50)' required class="form-control">
                                <span class="input-group-addon">€</span>
                            </div>
                            <div class="help-block with-errors"></div>
                        </div>                       
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-primary" id="subAction" data-dismiss="modal" <?php if($prestation != 0) { ?> onclick="modifierPrestationForm('ligne<?php echo $prestation; ?>', <?php echo $prestation; ?>, true);" <?php } else { ?> onclick="ajouterPrestationForm('listePrestations', true);"  disabled <?php }?>><?php echo $actionVerbe; ?></button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
<?php }

/*****
 * genererModalPrestation : genere le modal pour modifier une prestation dans la liste des prestations
 * 
 * @param int $prestation : contient l'id general de la prestation a modifier
 ***/
function genererModalPrestation($prestation) {     
    // Connexion a la base de donnees
    $pdo = new SPDO();
    //On cree la requete pour recupérer les infos générales de la prestation
    $stmt_presta = "SELECT DISTINCT(pres_id_general), pres_prestation, pres_repartition_cons, pres_rf_nom, nom_code, pres_rf_pay, pay_nom, " 
            . "pres_rf_typ_operation, t_ope_libelle, pres_rf_typ_dossier, t_dos_entite, t_dos_type " 
            . "FROM prestation, nomenclature, pays, type_operation, type_dossier " 
            . "WHERE pres_rf_nom = nom_id "
            . "AND pres_rf_pay = pay_id "
            . "AND pres_rf_typ_operation = t_ope_id "
            . "AND pres_rf_typ_dossier = t_dos_id "
            . "AND pres_id_general = :prestation";
    $result_presta = $pdo->prepare($stmt_presta);
    $result_presta->bindParam(":prestation", $prestation);
    $result_presta->execute();
    
    $presta = $result_presta->fetch(PDO::FETCH_OBJ);
    
    //On cree la requete pour recupérer les lignes de prestation liées à la prestation générale
    $stmt_presta_infos = "SELECT pres_id, pres_libelle_ligne_fac, pres_t_tarif, pres_tarif_std, pres_tarif_jr, pres_tarif_sr, pres_tarif_mgr "
            . "FROM prestation " 
            . "WHERE pres_id_general = :idGen";
    $result_presta_infos = $pdo->prepare($stmt_presta_infos);
    $result_presta_infos->bindParam(":idGen", $presta->pres_id_general);
    $result_presta_infos->execute();
    
    //On recupere les differentes operations disponibles
    $stmt_ope = "SELECT t_ope_id, t_ope_libelle FROM type_operation";
    $result_ope = $pdo->prepare($stmt_ope);
    $result_ope->execute();

    //On va chercher les entites possibles pour un dossier (brevet ou juridique)
    $stmt_t_dos_ent = "SELECT DISTINCT(t_dos_entite) FROM type_dossier ORDER BY t_dos_entite";
    $result_t_dos_ent = $pdo->prepare($stmt_t_dos_ent);
    $result_t_dos_ent->execute();

    //On va chercher les types de dossier qui correspondent a l'entité de la prestation que nous voulons modifier
    $stmt_t_dos_type = "SELECT t_dos_id, t_dos_type FROM type_dossier WHERE t_dos_entite = :entite ORDER BY t_dos_type";
    $result_t_dos_type = $pdo->prepare($stmt_t_dos_type);
    $result_t_dos_type->bindParam(":entite", $presta->t_dos_entite);
    $result_t_dos_type->execute();

    //On recupere les codes de nomenclatures auxquels on voudra associer des prestations
    $stmt_nom = "SELECT nom_id, nom_code FROM nomenclature ORDER BY nom_code";
    $result_nom = $pdo->prepare($stmt_nom);
    $result_nom->execute();

    //On recupere tous les pays qui peuvent etre associés à une prestation
    $stmt_pays_reg = "SELECT DISTINCT(pay_region) FROM pays ORDER BY pay_region";
    $result_pays_reg = $pdo->prepare($stmt_pays_reg);
    $result_pays_reg->execute();
    ?>
    <!--Ajout des lignes de prestations par modal-->
    <!--Creation du formulaire pour afficher les infos de la prestation et la modifier-->
    <form id="formUpdatePrestation" action="index.php?action=changePrestation" method="post" role="form" data-toggle="validator">  
        <div class="modal fade" role="dialog" aria-labelledby="modalInfoPrestationGenerale" aria-hidden="true" id="modalInfoPrestationGenerale">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">   
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="modalInfoPrestationGeneraleLabel">Modification d'une prestation</h4>
                        </div>
                        <div class="modal-body">
                            <div class="container-fluid"> 
                                <input name="pres_id_general" type="hidden" value="<?php echo $presta->pres_id_general; ?>" required class="form-control" id="pres_id_general">
                                <div role="tabpanel">
                                    <!-- Nav tabs -->
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li role="presentation" class="active"><a href="#general" aria-controls="general" role="tab" data-toggle="tab">Général</a></li>
                                        <li role="presentation"><a href="#lignes" aria-controls="lignes" role="tab" data-toggle="tab">Lignes de prestation</a></li>
                                    </ul>
                                    <br />
                                    <div class="tab-content">
                                        <div role="tabpanel" class="tab-pane active" id="general">
                                            <div class="form-group">
                                                <label class="control-label" for="operation">Opération :</label>
                                                <select name="operation" id="operation" required class="form-control">
                                                <?php //On affiche toutes les operations comme des options du select
                                                foreach($result_ope->fetchAll(PDO::FETCH_OBJ) as $ope) { ?>
                                                    <option value="<?php echo $ope->t_ope_id; ?>" <?php if($presta->pres_rf_typ_operation == $ope->t_ope_id) { echo "selected"; } ?>><?php echo $ope->t_ope_libelle; ?></option>
                                                <?php } ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label" for="ent_dossier">Type de dossier :</label><br />
                                                <!--En changeant l'entite, nous allons charger le select type_dossier avec les types associés à l'entite choisie-->
                                                <select name="ent_dossier" id="ent_dossier" required onchange="genererListeTypeDossier('#type_dossier', this.value, false);" class="form-control">
                                                <?php // On affiche les entites disponibles 
                                                foreach($result_t_dos_ent->fetchAll(PDO::FETCH_OBJ) as $t_dos_ent) { ?>
                                                    <option value="<?php echo $t_dos_ent->t_dos_entite; ?>" <?php if($presta->t_dos_entite == $t_dos_ent->t_dos_entite) { echo "selected"; } ?>><?php echo $t_dos_ent->t_dos_entite; ?></option>
                                                <?php } ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <!--On cree un select vide qui sera peuplé grace a un appel ajax-->
                                                <select name="type_dossier" id="type_dossier" required class="form-control">
                                                <?php // On affiche les entites disponibles 
                                                foreach($result_t_dos_type->fetchAll(PDO::FETCH_OBJ) as $t_dos_type) { ?>
                                                    <option value="<?php echo $t_dos_type->t_dos_id; ?>" <?php if($presta->pres_rf_typ_dossier == $t_dos_type->t_dos_id) { echo "selected"; } ?>><?php echo $t_dos_type->t_dos_type; ?></option>
                                                <?php } ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label" for="nom_code">Code :</label>
                                                <!--On affiche les codes de nomenclature dans le select--> 
                                                <select name="nom_code" id="nom_code" required class="form-control">
                                                <?php foreach($result_nom->fetchAll(PDO::FETCH_OBJ) as $nom) { ?>
                                                    <option value="<?php echo $nom->nom_id; ?>" <?php if($presta->pres_rf_nom == $nom->nom_id) { echo "selected"; } ?>><?php echo $nom->nom_code; ?></option>
                                                <?php } ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label" for="pays">Pays :</label>
                                                <!--On affiche les pays en les groupant par regions-->
                                                <select name="pays" id="pays" required class="form-control">
                                                <?php foreach($result_pays_reg->fetchAll(PDO::FETCH_OBJ) as $pays_reg) { ?>
                                                    <optgroup label="<?php echo $pays_reg->pay_region; ?>">
                                                        <?php $stmt_pays = "SELECT pay_id, pay_nom FROM pays WHERE pay_region = '" . $pays_reg->pay_region . "' ORDER BY pay_nom";
                                                        $result_pays = $pdo->prepare($stmt_pays);
                                                        $result_pays->execute();
                                                        foreach($result_pays->fetchAll(PDO::FETCH_OBJ) as $pays) { ?>
                                                            <option value="<?php echo $pays->pay_id; ?>" <?php if($presta->pres_rf_pay == $pays->pay_id) { echo "selected"; } ?>><?php echo $pays->pay_nom; ?></option>
                                                        <?php } ?>
                                                    </optgroup>
                                                <?php } ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label" for="prestation">Prestation :</label>
                                                <!--on prend le nom general de la prestation, i.e. nom du modele-->
                                                <input name="prestation" type="text" value="<?php echo $presta->pres_prestation; ?>" required class="form-control" id="prestation" maxlength="255" data-error="Veuillez entrer le nom de la prestation générale">
                                                <div class="help-block with-errors"></div>
                                            </div>
                                            <!--On gere ici la repartition des consultants soit par un select, soit avec un slider (les deux sont liés)-->
                                            <div class="form-group">
                                                <label class="control-label" for="repartition">Répartition des consultants :</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <select id="pourcentage_select" class="form-inline" onchange="document.getElementById('pourcentage').innerHTML=this.value+'%';document.getElementById('repartition').value=this.value;">
                                                            <?php for($i=0; $i<=100; $i+=5) { ?>
                                                                <option <?php if($i == $presta->pres_repartition_cons) { echo "selected"; } ?>><?php echo $i; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </span>
                                                    <input name="repartition" value="<?php echo $presta->pres_repartition_cons; ?>" id="repartition" onchange="document.getElementById('pourcentage').innerHTML=this.value+'%';document.getElementById('pourcentage_select').value=this.value;" type="range" min="0" max="100" step="5" required class="form-control">
                                                    <span id="pourcentage" class="input-group-addon"><?php echo $presta->pres_repartition_cons; ?>%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div role="tabpanel" class="tab-pane" id="lignes">  
                                            <!--div qui contiendra le pseudo formulaire d'ajout d'une ligne de prestation -->
                                            <div class="panel panel-default">
                                                <div class="panel-heading" id='panel_action'>Ajout d'une ligne de prestation</div>
                                                <div class="form-group">
                                                    <label class="control-label" for="libelle">Libellé :</label>
                                                    <input name="libelle" type="text" onkeyup="checkLignePrestation('subAction');" class="form-control" id="libelle" maxlength="255">
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label" for="t_tarif">Type de tarification :</label>
                                                    <!--On choisit le type de tarification et on genere les champs qu'il faut en fonction-->
                                                    <select name="t_tarif" id="t_tarif" class="form-control" onchange="afficherTarifs(this.value);checkLignePrestation('subAction');">
                                                        <option value="" disabled selected>Choisissez un type de tarification...</option>
                                                        <option value="F">Forfaitaire</option>
                                                        <option value="TH">Tarif Horaire</option>
                                                    </select>
                                                </div>
                                                <div class="form-group" id="tarif_std_div" style="display: none;">
                                                    <label class="control-label" for="tarif_std">Tarif :</label>
                                                    <div class="input-group">
                                                        <input name="tarif_std" id="tarif_std" type="text" onkeyup="checkLignePrestation('subAction');" pattern="\d+(\.\d{1,2})?" data-error='Veuillez renseigner un montant (ex: 400.50)' class="form-control">
                                                        <span class="input-group-addon">€</span>
                                                    </div>
                                                    <div class="help-block with-errors"></div>
                                                </div>
                                                <div class="form-group" id="tarif_jr_div" style="display: none;">
                                                    <label class="control-label" for="tarif_jr">Tarif junior :</label>
                                                    <div class="input-group">
                                                        <input name="tarif_jr" id="tarif_jr" type="text" onkeyup="checkLignePrestation('subAction');" pattern="\d+(\.\d{1,2})?" data-error='Veuillez renseigner un montant (ex: 400.50)' class="form-control">
                                                        <span class="input-group-addon">€</span>
                                                    </div>
                                                    <div class="help-block with-errors"></div>
                                                </div>        
                                                <div class="form-group" id="tarif_sr_div" style="display: none;">
                                                    <label class="control-label" for="tarif_sr">Tarif senior :</label>
                                                    <div class="input-group">
                                                        <input name="tarif_sr" id="tarif_sr" type="text" onkeyup="checkLignePrestation('subAction');" pattern="\d+(\.\d{1,2})?" data-error='Veuillez renseigner un montant (ex: 400.50)' class="form-control">
                                                        <span class="input-group-addon">€</span>
                                                    </div>
                                                    <div class="help-block with-errors"></div>
                                                </div>        
                                                <div class="form-group" id="tarif_mgr_div" style="display: none;">
                                                    <label class="control-label" for="tarif_mgr">Tarif manager :</label>
                                                    <div class="input-group">
                                                        <input name="tarif_mgr" id="tarif_mgr" type="text" onkeyup="checkLignePrestation('subAction');" pattern="\d+(\.\d{1,2})?" data-error='Veuillez renseigner un montant (ex: 400.50)' class="form-control">
                                                        <span class="input-group-addon">€</span>
                                                    </div>
                                                    <div class="help-block with-errors"></div>
                                                </div> 
                                                <!--Bouton pou ajouter ou modifier une ligne de prestation-->
                                                <div class="form-group" id="button_action">
                                                    <button type="button" class="btn btn-default" disabled name="subAction" id="subAction" onclick="ajouterPrestationForm('listePrestations', false);"><i class='icon-plus fa fa-plus'></i> Ajouter une prestation</button>
                                                </div>
                                            </div>
                                            <!--input pour compter le nombre de prestations ajoutees (au moins une necessaire)-->
                                            <div class="form-group">
                                                <input name="nbInfos" id="nbInfos" style="display: none;" type="number" value="0" class="form-control" data-error="Veuillez ajouter au moins une ligne de prestation">   
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
                                                            <th scope="col">Libellé</th>
                                                            <th scope="col">Type tarification</th>
                                                            <th scope="col">Tarif standard</th>
                                                            <th scope="col">Tarif junior</th>
                                                            <th scope="col">Tarif senior</th>
                                                            <th scope="col">Tarif manager</th>
                                                            <th scope="col">Modifier</th>
                                                            <th scope="col">Supprimer</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id='listePrestations'>
                                                        <?php foreach($result_presta_infos->fetchAll(PDO::FETCH_OBJ) as $info) { ?> 
                                                        <tr id='ligne<?php echo $info->pres_id; ?>'> 
                                                            <td> <?php echo $info->pres_libelle_ligne_fac; ?>
                                                                <input type='hidden' value='<?php echo $info->pres_libelle_ligne_fac; ?>' name='libelle<?php echo $info->pres_id; ?>' id='libelle<?php echo $info->pres_id; ?>'/>
                                                            </td>
                                                            <td><?php if($info->pres_t_tarif == "F") { echo "Forfaitaire"; } else { echo "Tarif horaire"; } ?>
                                                                <input type='hidden' value='<?php echo $info->pres_t_tarif; ?>' name='t_tarif<?php echo $info->pres_id; ?>' id='t_tarif<?php echo $info->pres_id; ?>'/>
                                                            </td>
                                                            <td><?php echo $info->pres_tarif_std; ?>
                                                                <input type='hidden' value='<?php echo $info->pres_tarif_std; ?>' name='tarif_std<?php echo $info->pres_id; ?>' id='tarif_std<?php echo $info->pres_id; ?>'/>
                                                            </td>       
                                                            <td><?php echo $info->pres_tarif_jr; ?>
                                                                <input type='hidden' value='<?php echo $info->pres_tarif_jr; ?>' name='tarif_jr<?php echo $info->pres_id; ?>' id='tarif_jr<?php echo $info->pres_id; ?>'/>
                                                            </td>
                                                            <td><?php echo $info->pres_tarif_sr; ?>
                                                                <input type='hidden' value='<?php echo $info->pres_tarif_sr; ?>' name='tarif_sr<?php echo $info->pres_id; ?>' id='tarif_sr<?php echo $info->pres_id; ?>'/>
                                                            </td>
                                                            <td><?php echo $info->pres_tarif_mgr; ?>
                                                                <input type='hidden' value='<?php echo $info->pres_tarif_mgr; ?>' name='tarif_mgr<?php echo $info->pres_id; ?>' id='tarif_mgr<?php echo $info->pres_id; ?>'/>
                                                            </td>
                                                            <td align='center'>
                                                                <a class='btn btn-primary btn-sm' onclick='modifierPrestation("<?php echo $info->pres_id; ?>")'><i class='icon-plus fa fa-edit'></i> Modifier</a>
                                                            </td>
                                                            <td align='center'>
                                                                <a class='btn btn-danger btn-sm' disabled><i class='icon- fa fa-remove'></i> Supprimer</a>
                                                            </td>
                                                        </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <!--modal pour ajouter ou modifier une ligne de prestation-->
                                            <div id="modalLignePrestation"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                            <input type="submit" class="btn btn-primary" id="button" value="Modifier">
                        </div>         
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    </form>
<?php }

/**
 *   getPrestationTabFromID : Retourne une ligne de tableau comprenant la prestation ajoutée dans createModel.php
 *   @param String $id_presta : id de la prestation à ajouter.
 */
function getPrestationTabFromID($id_presta, $nbInfos, $nbInfosTot) {

    $pdo = new SPDO;

    /* On recupere les infos à insérer dans notre ligne de tableau */
    $stmt_presta_tab_model = "SELECT pres_id, pres_libelle_ligne_fac, pres_t_tarif, pres_tarif_std, pres_tarif_jr, pres_tarif_sr, pres_tarif_mgr 
                                FROM prestation 
                                WHERE pres_id='".$id_presta."'";
    $result_presta_list = $pdo->prepare($stmt_presta_tab_model);
    $result_presta_list->execute();

    if($id_presta != null) {
        foreach ($result_presta_list->fetchAll(PDO::FETCH_OBJ) as $presta_list) { ?>
            <tr id="ligne<?php echo $nbInfosTot; ?>">
                <input type="hidden" value="<?php echo $presta_list->pres_id; ?>"
                       name="presta_id_<?php echo $nbInfosTot; ?>" id="presta_id_<?php echo $nbInfosTot; ?>"/>
                <td id="pres_libelle_ligne_fac<?php echo $nbInfosTot; ?>"><?php if (isset($presta_list->pres_libelle_ligne_fac)) echo $presta_list->pres_libelle_ligne_fac; ?></td>
                <td id="pres_t_tarif<?php echo $nbInfosTot; ?>"><?php if (isset($presta_list->pres_t_tarif)) echo $presta_list->pres_t_tarif; ?></td>
                <td id="pres_tarif_std<?php echo $nbInfosTot; ?>"><?php if (isset($presta_list->pres_tarif_std)) echo $presta_list->pres_tarif_std; ?></td>
                <td id="pres_tarif_jr<?php echo $nbInfosTot; ?>"><?php if (isset($presta_list->pres_tarif_jr)) echo $presta_list->pres_tarif_jr; ?></td>
                <td id="pres_tarif_sr<?php echo $nbInfosTot; ?>"><?php if (isset($presta_list->tarif_sr)) echo $presta_list->tarif_sr; ?></td>
                <td id="pres_tarif_mgr<?php echo $nbInfosTot; ?>"><?php if (isset($presta_list->tarif_mgr)) echo $presta_list->tarif_mgr; ?></td>
                <td><a class='btn btn-danger btn-sm' onclick='supModelPresta(<?php echo $nbInfosTot; ?>)'><i
                            class='icon-plus fa fa-edit'></i> Supprimer</a></td>
            </tr>
        <?php
        }
    }
}
