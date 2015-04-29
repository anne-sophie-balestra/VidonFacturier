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


            case('genererListeNomModele'):
            	$model = (filter_input(INPUT_GET, 'mod') != NULL ? filter_input(INPUT_GET, 'mod') : "");
            	genererListeNomModele($model);
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

        //permet de retourner le taux de change de la devise
        case('changeDevise'):
            $devise = (filter_input(INPUT_GET, 'dev') != NULL ? filter_input(INPUT_GET, 'dev') : "");
            changeDevise($devise);
            break;

        //Genere une ligne de tableau dans contenant la prestation dans createModel.php
        case('getPrestationTabFromID'):
            $presta = (filter_input(INPUT_GET, 'presta') != NULL ? filter_input(INPUT_GET, 'presta') : "");
            $nbInfos = (filter_input(INPUT_GET, 'nbInfos') != NULL ? filter_input(INPUT_GET, 'nbInfos') : 0);
            $nbInfosTot = (filter_input(INPUT_GET, 'nbInfosTot') != NULL ? filter_input(INPUT_GET, 'nbInfosTot') : 0);
            $lib = (filter_input(INPUT_GET, 'lib') != NULL ? filter_input(INPUT_GET, 'lib') : "");
            getPrestationTabFromID($presta, $nbInfos, $nbInfosTot,$lib);
            break;       

        //Genere les infos du dossier associé a la facture dans createFacture
        case('genererInfosDossier'):
            $dossier = (filter_input(INPUT_GET, 'dos') != NULL ? filter_input(INPUT_GET, 'dos') : "");
            genererInfosDossier($dossier);
            break;              

        //Genere l'objet de la facture en fonction du dossier dans createFacture
        case('genererObjetFacture'):
            $dossier = (filter_input(INPUT_GET, 'dos') != NULL ? filter_input(INPUT_GET, 'dos') : "");
            genererObjetFacture($dossier);
            break;              

        //Genere le modal pour ajouter ou modifier une ligne de facture dans create facture
        case('genererModalLigneFacture'):
            $ligneFac = (filter_input(INPUT_GET, 'lf') != NULL ? filter_input(INPUT_GET, 'lf') : 0);
            genererModalLigneFacture($ligneFac);
            break;

        //Genere le modal pour ajouter ou modifier une ligne de facture dans create facture
        case('genererModalModelLigne'):
            $model_id = (filter_input(INPUT_GET, 'lig') != NULL ? filter_input(INPUT_GET, 'lig') : 0);
            genererModalModelLigne($model_id);
            break;    

        //Genere le modal pour ajouter ou modifier une ligne de facture dans create facture
        case('genererLibelleCode'):
            $code = (filter_input(INPUT_GET, 'code') != NULL ? filter_input(INPUT_GET, 'code') : "");
            genererLibelleCode($code);
            break;            

        //Genere le modal pour ajouter ou modifier un achat dans create facture
        case('genererModalAchat'):
            $achat = (filter_input(INPUT_GET, 'ac') != NULL ? filter_input(INPUT_GET, 'ac') : 0);
            $dossier = (filter_input(INPUT_GET, 'dos') != NULL ? filter_input(INPUT_GET, 'dos') : "");
            genererModalAchat($achat, $dossier);
            break; 

        //Genere le select avec les factures associées au dossier pour lier des achats
        case('genererFacturesAchat'):
            $dossier = (filter_input(INPUT_GET, 'dos') != NULL ? filter_input(INPUT_GET, 'dos') : "");
            $facture = (filter_input(INPUT_GET, 'fac') != NULL ? filter_input(INPUT_GET, 'fac') : "");
            genererFacturesAchat($dossier, $facture);
            break;             

        //Genere la date liée a la facture
        case('genererDateFacture'):
            $facture = (filter_input(INPUT_GET, 'fac') != NULL ? filter_input(INPUT_GET, 'fac') : "");
            genererDateFacture($facture);
            break;             

        //Genere le modal pour ajouter un reglement dans create facture
        case('genererModalReglement'):
            genererModalReglement();
            break;


    }
}

/*****
 * genererInfosDossier : genere les infos du dossier associé a la facture que nous voulons créer
 *
 * @param String $p_dos : id du dossier
 ***/
function genererInfosDossier($p_dos) {    
    $pdo = new SPDO;
    
    /* On recupere les infos du dossier en fonction de son id */
    $stmt = "SELECT dos_id, dos_type, dos_numcomplet, dos_creadate, dos_titre, ent_raisoc FROM dossier, entite WHERE dos_rf_ent = ent_id AND dos_id = :dos";
    $result_dossier = $pdo->prepare($stmt);
    $result_dossier->bindParam(":dos", $p_dos);
    $result_dossier->execute();
    $dossier = $result_dossier->fetch(PDO::FETCH_OBJ);
    ?>
    <td><span class="badge"><?php echo $dossier->dos_numcomplet; ?></span></td>
    <td><?php echo $dossier->dos_titre; ?></td>
    <td><?php echo $dossier->ent_raisoc; ?></td>
    <td><?php echo substr($dossier->dos_creadate, 0, 11); ?></td>
    <td><?php echo $dossier->dos_type; ?></td>
<?php }

/*****
 * genererFacturesAchat : genere les factures liées a un dossier pour répercuter un achat
 *
 * @param String $p_dos : id du dossier
 * @param String $p_fac : id de la facture si on modifie l'achat
 ***/
function genererFacturesAchat($p_dos, $p_fac) {    
    $pdo = new SPDO;
    
    /* On recupere les factures en fonction du dossier */
    $stmt = "SELECT fac_id, fac_num, fac_objet FROM facture WHERE fac_rf_dos = :dos";
    $result_factures = $pdo->prepare($stmt);
    $result_factures->bindParam(":dos", $p_dos);
    $result_factures->execute();
    ?>
    
    <label class="control-label" for="fac_rf">Référence facture :</label>
    <select name="fac_rf" id="fac_rf" required class="form-control" onchange="checkAchat('subAction');genererDateFacture('#date_fac_rf_reel', this.value);">
        <option value="" disabled <?php if($p_fac == 'false') { echo "selected"; } ?>>Choisissez une facture...</option>
        <?php //On affiche toutes les factures associées au dossier
        foreach($result_factures->fetchAll(PDO::FETCH_OBJ) as $fac) { ?>
            <option value="<?php echo $fac->fac_id; ?>" <?php if($p_fac == $fac->fac_id) { echo "selected"; } ?>><?php echo $fac->fac_num . " (" . $fac->fac_objet . ")"; ?></option>
        <?php } ?>
    </select>
<?php }

/*****
 * genererDateFacture : genere la date de la facture 
 *
 * @param String $p_fac : id de la facture
 ***/
function genererDateFacture($p_fac) {    
    $pdo = new SPDO;
    
    /* On recupere la date de la facture en fonction de son id */
    $stmt = "SELECT fac_date FROM facture WHERE fac_id = :fac";
    $result_facture = $pdo->prepare($stmt);
    $result_facture->bindParam(":fac", $p_fac);
    $result_facture->execute();
    $fac = $result_facture->fetch(PDO::FETCH_OBJ);
    echo substr($fac->fac_date,0,10);
}

/*****
 * genererObjetFacture : genere l'objet de la facture en fonction de celui du dossier
 *
 * @param String $p_dos : id du dossier
 ***/
function genererObjetFacture($p_dos) {    
    $pdo = new SPDO;
    
    /* On recupere les infos du dossier en fonction de son id */
    $stmt = "SELECT dos_titre FROM dossier WHERE dos_id = :dos";
    $result_dossier = $pdo->prepare($stmt);
    $result_dossier->bindParam(":dos", $p_dos);
    $result_dossier->execute();
    $dossier = $result_dossier->fetch(PDO::FETCH_OBJ);
    echo $dossier->dos_titre;
}

/*****
 * genererLibelleCode : genere le libelle associé au code de nomenclature choisi
 *
 * @param String $p_code : code de nomenclature
 ***/
function genererLibelleCode($p_code) {    
    //Connexion a la base
    $pdo = new SPDO;
    
    //On recupere les codes de nomenclatures auxquels on voudra associer des lignes de facture
    $stmt_nom = "SELECT nom_libelle FROM nomenclature WHERE nom_id = :code";
    $result_nom = $pdo->prepare($stmt_nom);
    $result_nom->bindParam(":code", $p_code);
    $result_nom->execute();
    $code = $result_nom->fetch(PDO::FETCH_OBJ);
    echo $code->nom_libelle;
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
/*
 * Fonction Abdoul
 */

function genererListeNomModele($t_dossier)
{
	$pdo = new SPDO;

	/* On recupere les types de dossier en fonction de l'entite */
	$stmt_model = "SELECT t_fac_id, t_fac_modelname FROM type_facture JOIN type_dossier ON type_facture.t_fac_rf_typdos=type_dossier.t_dos_id WHERE  t_dos_type= :t_dossier";
	$result_model = $pdo->prepare($stmt_model);
	$result_model->bindParam(":t_dossier", $t_dossier);
	$result_model->execute();
	//On cree un array avec l'id et le nom du type de dossier que l'on va retourner en JSON
	$array_model = array();
	foreach($result_model->fetchAll(PDO::FETCH_OBJ) as $model) {
		$array_model[$model->t_fac_id] = $model->t_fac_modelname;
	}
	echo json_encode($array_model);
}

/*****
 * changeDevise : permet de retourner le taux de change de la devise
 *
 * @param String $p_devise : devise choisie
 ***/
function changeDevise($p_devise) {    
    $pdo = new SPDO;
    
    //On recupere les differentes devises possibles
    $stmt_dev = "SELECT dev_iso, dev_cours FROM devise WHERE dev_iso = :devise";
    $result_dev = $pdo->prepare($stmt_dev);
    $result_dev->bindParam(":devise", $p_devise);
    $result_dev->execute();
    $devise = $result_dev->fetch(PDO::FETCH_OBJ);
    
    echo $devise->dev_cours;
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
    $stmt_presta = "SELECT DISTINCT(pres_id_general), pres_prestation, pres_repartition_cons, pres_rf_nom, nom_code, pres_type, pres_rf_pay, pay_nom, " 
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
                                                <select name="nom_code" id="nom_code" required class="form-control" onchange="checkTypePrestation($('#nom_code option:selected').text());">
                                                <?php foreach($result_nom->fetchAll(PDO::FETCH_OBJ) as $nom) { ?>
                                                    <option value="<?php echo $nom->nom_id; ?>" <?php if($presta->pres_rf_nom == $nom->nom_id) { echo "selected"; } ?>><?php echo $nom->nom_code; ?></option>
                                                <?php } ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label" for="type">Type : </label><br />
                                                <input type="radio" id="honos" name="type" value="H" <?php if($presta->pres_type == "honos") { echo "checked"; } else { echo "disabled"; } ?> required> Honoraires
                                                <input type="radio" id="frais" name="type" value="F" <?php if($presta->pres_type == "frais") { echo "checked"; } else if($presta->pres_type == "taxes") { echo "disabled"; } ?> required> Frais
                                                <input type="radio" id="taxes" name="type" value="T" <?php if($presta->pres_type == "taxes") { echo "checked"; } else { echo "disabled"; } ?> required> Taxes
                                            </div>  
                                            <div class="form-group">
                                                <label class="control-label" for="prestation">Prestation :</label>
                                                <!--on prend le nom general de la prestation, i.e. nom du modele-->
                                                <input name="prestation" type="text" value="<?php echo $presta->pres_prestation; ?>" required class="form-control" id="prestation" maxlength="255" data-error="Veuillez entrer le nom de la prestation générale">
                                                <div class="help-block with-errors"></div>
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
                                                <table class="table table-striped table-hover">
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
function getPrestationTabFromID($id_presta, $nbInfos, $nbInfosTot, $lib) {

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
                <td id="pres_libelle_ligne_fac<?php echo $nbInfosTot; ?>"><?php if (isset($lib)){ echo $lib; } else { $presta_list->pres_libelle_ligne_fac; $lib = $presta_list->pres_libelle_ligne_fac; } ?></td>
                <input type="hidden" value="<?php echo $lib; ?>"
                       name="presta_lib_<?php echo $nbInfosTot; ?>" id="presta_lib_<?php echo $lib; ?>"/>
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

/*****
 * genererModalLigneFacture : genere le modal pour ajouter ou modifier une ligne de facture dans createFacture
 * 
 * @param int $ligneFac : contient le numero de la ligne de facture si c'est une modification, 0 si c est un ajout
 ***/
function genererModalLigneFacture($ligneFac) {  
    //Connexion a la base
    $pdo = new SPDO();
    
    //On recupere les codes de nomenclatures auxquels on voudra associer des lignes de facture
    $stmt_nom = "SELECT nom_id, nom_code FROM nomenclature ORDER BY nom_code";
    $result_nom = $pdo->prepare($stmt_nom);
    $result_nom->execute();
    
    //Definit l'action qui sera faite
    $action = "Ajout";
    $actionVerbe = "Ajouter";
    if($ligneFac != 0){
        $action = "Modification";
        $actionVerbe = "Modifier";
    }
    ?>  
    <!--Ajout ou modification des lignes de facture par modal-->
    <div class="modal fade" role="dialog" aria-labelledby="modalInfoLigneFacture" aria-hidden="true" id="modalInfoLigneFacture">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="modalInfoLigneFactureLabel"><?php echo $action; ?> d'une ligne de facture</h4>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">                               
                        <div class="form-group">
                            <label class="control-label" for="code">Code :</label>
                            <select name="code" id="code" required class="form-control" onchange="checkLigneFacture('subAction');genererLibelleCode('#libelle',this.value);checkTypePrestation($('#code option:selected').text());">
                                <option value="" disabled selected>Choisissez un code...</option>
                                <?php foreach($result_nom->fetchAll(PDO::FETCH_OBJ) as $code) { ?>
                                <option value='<?php echo $code->nom_id ?>'><?php echo $code->nom_code; ?></option>;
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="libelle">Libellé :</label>
                            <input name="libelle" type="text" required onkeyup="checkLigneFacture('subAction');" class="form-control" id="libelle">
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="type_ligne">Type : </label><br />
                            <input type="radio" id="honos" name="type_ligne" value="H" required onchange="checkLigneFacture('subAction');"> Honoraires
                            <input type="radio" id="frais" name="type_ligne" value="F" required onchange="checkLigneFacture('subAction');"> Frais
                            <input type="radio" id="taxes" name="type_ligne" value="T" required onchange="checkLigneFacture('subAction');"> Taxes
                        </div>                    
                        <div class="form-group">
                            <label class="control-label" for="tva">TVA :</label>
                            <select name="tva" id="tva" required class="form-control" onchange="checkLigneFacture('subAction');">
                                <option value="0" selected>0</option>
                                <option value="20">20</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="tarif">Tarif :</label>
                            <div class="input-group">
                                <input name="tarif" id="tarif" type="text" onkeyup="checkLigneFacture('subAction');" pattern="\d+(\.\d{1,2})?" data-error='Veuillez renseigner un montant (ex: 400.50)' required class="form-control">
                                <span class="input-group-addon">€</span>
                            </div>
                            <div class="help-block with-errors"></div>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="Quantité">Quantité :</label>
                            <input name="quantite" type="number" value="1" min='1' required onkeyup="checkLigneFacture('subAction');" class="form-control" id="quantite">
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="Total">Total :</label>
                            <div class="input-group">
                                <input name="Total" value="0" type="text" required readonly class="form-control" id="total">
                                <span class="input-group-addon">€</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-primary" id="subAction" data-dismiss="modal" <?php if($ligneFac != 0) { ?> onclick="modifierLigneFactureForm('ligneLigne<?php echo $ligneFac; ?>', <?php echo $ligneFac; ?>, true);" <?php } else { ?> onclick="ajouterLigneFactureForm('listeLignesFacture', true);"  disabled <?php }?>><?php echo $actionVerbe; ?></button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
<?php }

/*****
 * genererModalAchat : genere le modal pour ajouter ou modifier un achat dans createFacture
 * 
 * @param int $achat : contient le numero de l'achat si c'est une modification, 0 si c est un ajout
 * @param String $dossier : contient le numero de dossier actuel
 ***/
function genererModalAchat($achat, $dossier) {  
    //Connexion a la base
    $pdo = new SPDO();
    
    //On recupere les codes de nomenclatures auxquels on voudra associer des achats
    $stmt_nom = "SELECT nom_id, nom_code FROM nomenclature ORDER BY nom_code";
    $result_nom = $pdo->prepare($stmt_nom);
    $result_nom->execute();
    
    //On recupere les utilisateurs
    $stmt_cons = "SELECT uti_id, uti_nom, uti_prenom FROM utilisateur ORDER BY uti_initial";
    $result_cons = $pdo->prepare($stmt_cons);
    $result_cons->execute();
    
    //On recupere les entites de nature Fournisseur
    $stmt_fournisseurs = "SELECT ent_id, ent_raisoc FROM entite WHERE ent_nature LIKE '%Fournisseur%' OR ent_nature LIKE '%fournisseur%' ORDER BY ent_raisoc";
    $result_fournisseurs = $pdo->prepare($stmt_fournisseurs);
    $result_fournisseurs->execute();
    
    //On recupere les differentes devises possibles
    $stmt_dev = "SELECT dev_iso, dev_cours FROM devise WHERE dev_iso <> '' ORDER BY dev_iso";
    $result_dev = $pdo->prepare($stmt_dev);
    $result_dev->execute();
    
    //Definit l'action qui sera faite
    $action = "Ajout";
    $actionVerbe = "Ajouter";
    if($achat != 0){
        $action = "Modification";
        $actionVerbe = "Modifier";
    }
    ?>  
    <!--Ajout ou modification des achats par modal-->
    <div class="modal fade" role="dialog" aria-labelledby="modalInfoAchat" aria-hidden="true" id="modalInfoAchat">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="modalInfoAchatLabel"><?php echo $action; ?> d'un achat</h4>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">     
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label" for="code">Code :</label>
                                <select name="code" id="code" required class="form-control" onchange="genererLibelleCode('#libelleAchat', this.value);checkAchat('subAction');">
                                    <option value="" disabled selected>Choisissez un code...</option>
                                    <?php foreach($result_nom->fetchAll(PDO::FETCH_OBJ) as $code) { ?>
                                    <option value='<?php echo $code->nom_id ?>'><?php echo $code->nom_code; ?></option>;
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label class="control-label" for="libelleAchat">Libellé :</label>
                                <input name="libelleAchat" type="text" required onkeyup="checkAchat('subAction');" class="form-control" id="libelleAchat">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <!--On choisit le consultant sur cet achat-->
                            <div class="form-group">
                                <label class="control-label" for="cpv">CPV :</label>
                                <select name="cpv" id="cpv" required class="form-control" onchange="checkAchat('subAction');">
                                    <option value="" disabled selected>Choisissez un CPV...</option>
                                <?php //On affiche tous les utilisateurs comme des options du select
                                foreach($result_cons->fetchAll(PDO::FETCH_OBJ) as $cons) { ?>
                                    <option value="<?php echo $cons->uti_id; ?>"><?php echo $cons->uti_prenom . " " . $cons->uti_nom; ?></option>
                                <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label" for="litige">En litige : </label>
                                <input name="litige" type="checkbox" id="litige">                                
                            </div>
                        </div>
                        <div class="col-md-10">
                            <div class="form-group">
                                <label class="control-label" for="complement">Complétement facture : </label>
                                <input name="complement" type="checkbox" id="complement">     
                            </div>
                        </div>
                        <div class="col-md-12">
                            <!--On choisit le fournisseur sur cet achat-->
                            <div class="form-group">
                                <label class="control-label" for="fournisseur">Fournisseur :</label>
                                <select name="fournisseur" id="fournisseur" required class="form-control" onchange="checkAchat('subAction');">
                                    <option value="" disabled selected>Choisissez un fournisseur...</option>
                                <?php //On affiche tous les fournisseurs comme des options du select
                                foreach($result_fournisseurs->fetchAll(PDO::FETCH_OBJ) as $fournisseur) { ?>
                                    <option value="<?php echo $fournisseur->ent_id; ?>"><?php echo $fournisseur->ent_raisoc; ?></option>
                                <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label" for="devise">Devise :</label>
                                <select name="devise" id="devise" required class="form-control" onchange="changeDevise('#taux', this.value, 'subAction', 'achat');">
                                    <?php foreach($result_dev->fetchAll(PDO::FETCH_OBJ) as $devise) { ?>0
                                    <option <?php if($devise->dev_iso == 'EUR') { echo "selected"; } ?> value="<?php echo $devise->dev_iso; ?>"><?php echo $devise->dev_iso; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label class="control-label" for="taux">Taux :</label>
                                <input name="taux" type="text" required onkeyup="checkAchat('subAction');" value="1" class="form-control" id="taux" pattern="\d+(\.\d*)?" data-error='Veuillez renseigner un montant (ex: 400.50)'>
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>
                        <!--Achat provisionnel ou réel-->
                        <div class="form-group">
                            <label class="control-label" for="reel">Achat : </label><br />
                            <div class="col-md-2">
                                <input type="radio" name="reel" value="P" id="P" required onchange="changerPanelAchat(this.value);checkAchat('subAction');"> Provisionnel
                            </div>
                            <div class="col-md-10">
                                <input type="radio" name="reel" value="R" id="R" required onchange="changerPanelAchat(this.value);checkAchat('subAction');"> Réel
                            </div>
                        </div>        
                        <br />
                        <div class="panel panel-default" id="panel_provisionnel" style="display: none;">
                            <div class="panel-heading">Provisionnel</div><br /> 
                            <h3>Achat</h3>
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label class="control-label" for="tarif_u_prov">Tarif unitaire :</label>
                                        <div class="input-group">
                                            <input name="tarif_u_prov" id="tarif_u_prov" type="text" onkeyup="checkAchat('subAction');" pattern="\d+(\.\d{1,2})?" data-error='Veuillez renseigner un montant (ex: 400.50)' required class="form-control">
                                            <span class="input-group-addon devise">EUR</span>
                                        </div>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label" for="quantite_prov">Quantité :</label>
                                        <input name="quantite_prov" id="quantite_prov" min="1" value="1" type="number" onkeyup="checkAchat('subAction');" required class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label class="control-label" for="montant_prov">Montant total :</label>
                                        <div class="input-group">
                                            <input name="montant_prov" id="montant_prov" type="text" readonly required class="form-control">
                                            <span class="input-group-addon devise">EUR</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input name="tarif_u_prov_marge" id="tarif_u_prov_marge" type="text" required class="form-control" readonly>
                                            <span class="input-group-addon">EUR</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2"></div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input name="montant_prov_marge" id="montant_prov_marge" type="text" readonly required class="form-control">
                                            <span class="input-group-addon">EUR</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label class="control-label" for="marge_prov">Marge prévisionnelle :</label>
                                        <input name="marge_prov" id="marge_prov" type="text"  min="-100" max="100" value="0" readonly required class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2"></div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label class="control-label" for="dateEcheance_prov">Date échéance prévisionnelle :</label>
                                        <input class="form-control datepicker" name="dateEcheance_prov" onchange="checkAchat('subAction');" data-date-format="yyyy-mm-dd" type="text" required id="dateEcheance_prov" value="">
                                    </div> 
                                </div>
                            </div>                            
                            <h3>Vente</h3>
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label class="control-label" for="tarif_u_revente_prov">Tarif de revente unitaire :</label>
                                        <div class="input-group">
                                            <input name="tarif_u_revente_prov" id="tarif_u_revente_prov" type="text" onkeyup="checkAchat('subAction');" pattern="\d+(\.\d{1,2})?" data-error='Veuillez renseigner un montant (ex: 400.50)' required class="form-control">
                                            <span class="input-group-addon">EUR</span>
                                        </div>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label" for="quantite_revente_prov">Quantité :</label>
                                        <input name="quantite_revente_prov" id="quantite_revente_prov" min="1" value="1" type="number" readonly required class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label class="control-label" for="montant_revente_prov">Montant total :</label>
                                        <div class="input-group">
                                            <input name="montant_revente_prov" id="montant_revente_prov" type="text" readonly required class="form-control">
                                            <span class="input-group-addon">EUR</span>
                                        </div>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label" for="date_fac_rf_prov">Date préfacturation :</label>
                                        <input class="form-control datepicker" name="date_fac_rf_prov" type="text" onchange="checkAchat('subAction');" id="date_fac_rf_prov" required value="" data-date-format="yyyy-mm-dd">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default" id="panel_reel" style="display: none;">
                            <div class="panel-heading">Réel</div><br />
                            <h3>Achat</h3>
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label class="control-label" for="tarif_u_reel">Tarif unitaire :</label>
                                        <div class="input-group">
                                            <input name="tarif_u_reel" id="tarif_u_reel" type="text" onkeyup="checkAchat('subAction');" pattern="\d+(\.\d{1,2})?" data-error='Veuillez renseigner un montant (ex: 400.50)' required class="form-control">
                                            <span class="input-group-addon devise">EUR</span>
                                        </div>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label" for="quantite_reel">Quantité :</label>
                                        <input name="quantite_reel" id="quantite_reel" min="1" value="1" type="number" onkeyup="checkAchat('subAction');" required class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label class="control-label" for="montant_reel">Montant total :</label>
                                        <div class="input-group">
                                            <input name="montant_reel" id="montant_reel" type="text" readonly required class="form-control">
                                            <span class="input-group-addon devise">EUR</span>
                                        </div>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input name="tarif_u_reel_marge" id="tarif_u_reel_marge" type="text" required class="form-control" readonly>
                                            <span class="input-group-addon">EUR</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2"></div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input name="montant_reel_marge" id="montant_reel_marge" type="text" readonly required class="form-control">
                                            <span class="input-group-addon">EUR</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label class="control-label" for="num_ffo">N°FFO :</label>
                                        <input class="form-control" name="num_ffo" type="text" required id="num_ffo" value="" onkeyup="checkAchat('subAction');">
                                    </div> 
                                </div>
                                <div class="col-md-2"></div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label class="control-label" for="marge_reel">Marge effective :</label>
                                        <input name="marge_reel" id="marge_reel" type="number" min="-100" max="100" value="0" readonly required class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label" for="dateFacure_reel">Date facture :</label>
                                        <input class="form-control datepicker" name="dateFacture_reel" onchange="checkAchat('subAction');" data-date-format="yyyy-mm-dd" type="text" required id="dateFacture_reel" value="">
                                    </div>  
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label" for="dateEcheance_reel">Date échéance :</label>
                                        <input class="form-control datepicker" name="dateEcheance_reel" onchange="checkAchat('subAction');" data-date-format="yyyy-mm-dd" type="text" required id="dateEcheance_reel" value="">
                                    </div> 
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label" for="dateReglement_reel">Date réglement :</label>
                                        <input class="form-control datepicker" name="dateReglement_reel" onchange="checkAchat('subAction');" data-date-format="yyyy-mm-dd" type="text" required id="dateReglement_reel" value="">
                                    </div> 
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label" for="bap">BAP : </label>
                                        <input name="bap" type="checkbox" id="bap" onchange="if(this.checked) { $('#bap_date').val('<?php echo date('Y-m-d'); ?>'); $('#bap_cpv').val('ASB'); } else { $('#bap_date').val(''); $('#bap_cpv').val(''); } checkAchat('subAction');">                                
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input class="form-control" name="bap_date" type="text" id="bap_date" value="" readonly>
                                    </div> 
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input class="form-control" name="bap_cpv" type="text" id="bap_cpv" value="" readonly>
                                    </div> 
                                </div>
                            </div>
                            <h3>Vente</h3>
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label class="control-label" for="tarif_u_revente_reel">Tarif de revente unitaire :</label>
                                        <div class="input-group">
                                            <input name="tarif_u_revente_reel" id="tarif_u_revente_reel" type="text" onkeyup="checkAchat('subAction');" pattern="\d+(\.\d{1,2})?" data-error='Veuillez renseigner un montant (ex: 400.50)' required class="form-control">
                                            <span class="input-group-addon">EUR</span>
                                        </div>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label" for="quantite_revente_reel">Quantité :</label>
                                        <input name="quantite_revente_reel" id="quantite_revente_reel" min="1" value="1" type="number" readonly required class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label class="control-label" for="montant_revente_reel">Montant total :</label>
                                        <div class="input-group">
                                            <input name="montant_revente_reel" id="montant_revente_reel" type="text" readonly required class="form-control">
                                            <span class="input-group-addon">EUR</span>
                                        </div>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                            </div><br />
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label class="control-label" for="visa">Visa de contrôle de marge : </label>
                                        <input name="visa" type="checkbox" id="visa" onchange="checkAchat('subAction');genererFacturesAchat('fac_rf_div', '<?php echo $dossier; ?>', false);">                                
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="form-group" id="fac_rf_div">
                                        <label class="control-label" for="fac_rf">Référence facture :</label>
                                        <select name="fac_rf" id="fac_rf" disabled required class="form-control">
                                            <option value="" disabled selected>Choisissez une facture...</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label" for="date_fac_rf_reel">Date préfacturation :</label>
                                        <input class="form-control datepicker" name="date_fac_rf_reel" type="text" id="date_fac_rf_reel" required value="" data-date-format="yyyy-mm-dd">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-primary" id="subAction" data-dismiss="modal" <?php if($achat != 0) { ?> onclick="modifierAchatForm('ligneAchat<?php echo $achat; ?>', <?php echo $achat; ?>, true, '<?php echo $dossier; ?>');" <?php } else { ?> onclick="ajouterAchatForm('listeAchats', true, '<?php echo $dossier; ?>');"  disabled <?php }?>><?php echo $actionVerbe; ?></button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
<?php }

/*****
 * genererModalReglement : genere le modal pour ajouter un reglement dans createFacture
 ***/
function genererModalReglement() {  
    //Connexion a la base
    $pdo = new SPDO();
    
    //On recupere les differentes devises possibles
    $stmt_dev = "SELECT dev_iso FROM devise WHERE dev_iso <> '' ORDER BY dev_iso";
    $result_dev = $pdo->prepare($stmt_dev);
    $result_dev->execute();
    ?>  
    <!--Ajout des reglements par modal-->
    <div class="modal fade" role="dialog" aria-labelledby="modalInfoReglement" aria-hidden="true" id="modalInfoReglement">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="modalInfoReglementLabel">Ajout d'un réglement</h4>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">                            
                        <div class="form-group">
                            <label class="control-label" for="date">Date :</label>
                            <input class="datepicker form-control" data-date-format="yyyy-mm-dd" name="date" type="text" required onkeyup="checkReglement('subAction');" id="date">
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label" for="devise">Devise :</label>
                                <select name="devise" id="devise" required class="form-control" onchange="changeDevise('#taux', this.value, 'subAction', 'reglement');">
                                    <?php foreach($result_dev->fetchAll(PDO::FETCH_OBJ) as $devise) { ?>0
                                    <option <?php if($devise->dev_iso == 'EUR') { echo "selected"; } ?> value="<?php echo $devise->dev_iso; ?>"><?php echo $devise->dev_iso; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label class="control-label" for="taux">Taux :</label>
                                <input name="taux" type="text" required onkeyup="checkReglement('subAction');" value="1" class="form-control" id="taux" pattern="\d+(\.\d*)?" data-error='Veuillez renseigner un montant (ex: 400.50)'>
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="montant">Montant :</label>
                            <div class="input-group">
                                <input name="montant" id="montant" type="text" onkeyup="checkReglement('subAction');" pattern="\d+(\.\d{1,2})?" data-error='Veuillez renseigner un montant (ex: 400.50)' required class="form-control">
                                <span class="input-group-addon devise">EUR</span>
                            </div>
                            <div class="help-block with-errors"></div>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="montantEuro">Montant total :</label>
                            <div class="input-group">
                                <input name="montantEuro" id="montantEuro" type="text" readonly required class="form-control">
                                <span class="input-group-addon">EUR</span>
                            </div>
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-primary" id="subAction" data-dismiss="modal" onclick="ajouterReglementForm('listeReglements', true);"  disabled >Ajouter</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
<?php }

/*****
 * genererModalModelLigne : genere le modal pour modifier une ligne de presta dans la liste des modeles
 *
 * @param int $modele : contient l'id du modele a modifier
 ***/
function genererModalModelLigne($modele_id) {

    // Connexion a la base de donnees
    $pdo = new SPDO();

    //On cree la requete pour recupérer les infos générales du modele
    $stmt_model = "SELECT t_fac_id, t_fac_rf_typdos, t_fac_rf_ent, t_fac_creadate, t_fac_moddate, t_fac_creauser, t_fac_moduser, t_fac_type,"
        ."t_fac_objet, t_fac_rf_ope, t_fac_langue, t_fac_area, t_fac_modelname, t_dos_entite, t_dos_id, t_ope_libelle, t_ope_id "
        ."FROM type_facture, type_dossier, type_operation "
        ."WHERE t_dos_id = t_fac_rf_typdos AND t_ope_id = t_fac_rf_ope AND t_fac_id = :modele_id";
    $result_model = $pdo->prepare($stmt_model);
    $result_model->bindParam(":modele_id", $modele_id);
    $result_model->execute();

    $modele = $result_model->fetch(PDO::FETCH_OBJ);

    //On cree la requete pour recupérer les lignes de presta (type_ligne) liées au modele
    $stmt_presta_ligne = "SELECT t_lig_id, t_lig_rf_pres, t_lig_creadate, t_lig_moddate, t_lig_creauser, t_lig_moduser, t_lig_rf_typ_fac, t_lig_libelle,"
    ."p.pres_t_tarif, pres_tarif_std, pres_tarif_jr, pres_tarif_sr, pres_tarif_mgr FROM type_ligne l, type_facture t, prestation p WHERE l.t_lig_rf_typ_fac = t.t_fac_id AND l.t_lig_rf_pres=p.pres_id AND t.t_fac_id = :id_fac";
    $result_presta_ligne = $pdo->prepare($stmt_presta_ligne);
    $result_presta_ligne->bindParam(":id_fac",$modele_id);
    $result_presta_ligne->execute();

    //$lignes = $result_presta_ligne->fetch(PDO::FETCH_OBJ);

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
    $result_t_dos_type->bindParam(":entite", $modele->t_dos_entite);
    $result_t_dos_type->execute();

    // On recupere les nom des clients
    $stmt_entite = "SELECT ent_id, ent_raisoc, ent_nature FROM entite WHERE ent_nature LIKE '%client%' OR ent_nature LIKE '%Client%'ORDER BY ent_raisoc";
    $result_entite = $pdo->prepare ( $stmt_entite );
    $result_entite->execute();

    // On recupere les types d'operations existantes
    $stmt_type_operation = "SELECT t_ope_id, t_ope_libelle FROM type_operation ORDER BY t_ope_libelle";
    $result_type_operation = $pdo->prepare ( $stmt_type_operation );
    $result_type_operation->execute();

    // On recupere les lignes de presta possible depuis ce modele
    $stmt_ligne_presta = "SELECT pres_libelle_ligne_fac FROM prestation p, type_facture t WHERE p.pres_rf_typ_dossier = t.t_fac_rf_typdos AND p.pres_rf_typ_operation = t.t_fac_rf_ope AND t_fac_id = :id";
    $result_t_presta = $pdo->prepare($stmt_ligne_presta);
    $result_t_presta->bindParam(":id", $modele->t_fac_id);
    $result_t_presta->execute();
    
    ?>
    <!--Ajout des lignes de prestations par modal-->
    <!--Creation du formulaire pour afficher les infos de la prestation et la modifier-->
    <form id="formUpdateModele" action="index.php?action=changeModele" method="post" role="form" data-toggle="validator">
        <div class="modal fade" role="dialog" aria-labelledby="modalInfoModel" aria-hidden="true" id="modalInfoModel">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="modalInfoModelLabel">Modification d'un Modèle</h4>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <input name="t_fac_id" type="hidden" value="<?php echo $modele->t_fac_id; ?>" required class="form-control" id="t_fac_id">
                            <div role="tabpanel">
                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active"><a href="#modele" aria-controls="modele" role="tab" data-toggle="tab">Modèle</a></li>
                                    <li role="presentation"><a href="#lignes" aria-controls="lignes" role="tab" data-toggle="tab">Lignes de prestations</a></li>
                                </ul>
                                <br />
                                <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane active" id="modele">

                                        <div class="form-group">
                                            <label class="control-label" for="name">Nom du modèle :</label>
                                            <!-- Nom du modèle -->
                                            <input name="name" type="text" value="<?php echo $modele->t_fac_modelname; ?>"  class="form-control" id="name" maxlength="255" data-error="Veuillez entrer le nom du modèle">
                                            <div class="help-block with-errors"></div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label" for="name">Client :</label>
                                            <!-- Choix du client-->
                                            <select name="client" id="client"  class="form-control">
                                                <?php //On affiche tous les clients
                                                foreach($result_entite->fetchAll(PDO::FETCH_OBJ) as $cli) { ?>
                                                    <option value="<?php echo $cli->ent_id; ?>" <?php if($modele->t_fac_rf_ent == $cli->ent_id) echo "selected"; ?>><?php echo $cli->ent_raisoc; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <!--On demande a l'utilisateur le type de dossier et l'opération pour le modele de facture-->
                                        <div class="form-group">
                                            <label class="control-label" for="ent_dossier">Type de dossier :</label><br />
                                            <!--En changeant l'entite, nous allons charger le select type_dossier avec les types associés à l'entite choisie-->
                                            <select name="ent_dossier" id="ent_dossier"  disabled onchange="genererListeTypeDossier('#type_dossier', this.value, false);" class="form-control select2">
                                                <option></option>
                                                <?php // On affiche les entites disponibles
                                                foreach($result_t_dos_ent->fetchAll(PDO::FETCH_OBJ) as $t_dos_ent) { ?>
                                                    <option value="<?php echo $t_dos_ent->t_dos_entite; ?>" <?php if($modele->t_dos_entite == $t_dos_ent->t_dos_entite) echo 'selected'; ?>><?php echo $t_dos_ent->t_dos_entite; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <!--On cree un select vide qui sera peuplé grace a un appel ajax-->
                                            <select name="type_dossier" id="type_dossier" disabled class="form-control">
                                                <?php // On affiche les entites disponibles
                                                foreach($result_t_dos_type->fetchAll(PDO::FETCH_OBJ) as $t_dos_type) { ?>
                                                    <option value="<?php echo $t_dos_type->t_dos_id; ?>" <?php if($modele->t_fac_rf_typdos == $t_dos_type->t_dos_id) echo 'selected';  ?>><?php echo $t_dos_type->t_dos_type; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <!-- Operation -->
                                            <label class="control-label" for="t_operation">Type d'opération :</label>
                                            <select name="type_operation" id="type_operation" disabled onchange="genererListePresta('#select_presta', document.getElementById('type_dossier').value, this.value);" class="form-control select2">
                                                <option></option>
                                                <?php
                                                foreach($result_type_operation->fetchAll(PDO::FETCH_OBJ) as $type_ope) { ?>
                                                    <option value="<?php echo $type_ope->t_ope_id; ?>" <?php if($modele->t_ope_libelle == $type_ope->t_ope_libelle) echo 'selected'; ?> ><?php echo $type_ope->t_ope_libelle; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <!--Renseignement du type de la facture-->
                                        <div class="form-group">
                                            <label class="control-label" for="type">Type de la facture :</label>
                                            <select name="type" id="type" class="form-control">
                                                <option></option>
                                                <option value="avoir" <?php if($modele->t_fac_type == "avoir") echo 'selected'; ?> >Avoir</option>
                                                <option value="facture" <?php if($modele->t_fac_type == "facture") echo 'selected'; ?> >Facture</option>
                                            </select>

                                            <div class="help-block with-errors"></div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label" for="objet">Objet de la facture :</label>
                                            <!-- Objet de la facture -->
                                            <input name="objet" type="text" value="<?php echo $modele->t_fac_objet; ?>" class="form-control" id="objet" maxlength="255" data-error="Veuillez entrer l'objet de la facture">
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>

                                    <!-- LIGNES DE PRESTATIONS -->

                                    <div role="tabpanel" class="tab-pane" id="lignes">
                                        <!--div qui contiendra le pseudo formulaire d'ajout d'une ligne de prestation -->
                                        <div class="panel panel-default">
                                            <div class="panel-heading" id='panel_action'>Ajout d'une ligne de prestation</div>

                                            <div class="form-group">
                                                <!--On cree un select vide qui sera peuplé grace a un appel ajax-->
                                                <select name="select_presta" id="select_presta" class="form-control select2" onChange="document.getElementById('lig_libelle').value = this.options[this.selectedIndex].innerHTML;">
                                                    <option></option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <input name="lig_libelle" type="text" class="form-control" id="lig_libelle" maxlength="255" data-error="Veuillez entrer le libelle de la ligne">
                                            </div>

                                            <!--input pour compter le nombre de prestations ajoutees (au moins une necessaire)-->
                                            <div class="form-group">
                                                <input name="nbInfos" id="nbInfos" style="display: none;" type="number" value=0 class="form-control" data-error="Veuillez ajouter au moins une ligne de prestation">
                                            </div>
                                            <!--input pour compter le nombre de prestations ajoutees en tout (meme si elles ont ete supprimees ensuite)-->
                                            <div class="form-group" hidden>
                                                <input name="nbInfosTot" id="nbInfosTot" type="number" value=0 required class="form-control">
                                            </div>

                                            <!--Bouton pou ajouter une ligne-->
                                            <div class="form-group" id="button_action">
                                                <button type="button" class="btn btn-default" name="subAction" id="subAction" onclick="ajouterPrestationModel('listePrestations');"><i class='icon-plus fa fa-plus'></i> Ajouter la ligne</button>
                                            </div>

                                            <!--div qui contiendra les prestations ajoutees-->
                                            <div class="panel panel-default">
                                                <div class="panel-heading">Liste des lignes de prestations</div>
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
                                                        <th scope="col">Supprimer</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody id='listePrestations'>
                                                        <?php foreach($result_presta_ligne->fetchAll(PDO::FETCH_OBJ) as $ligne) { ?>
                                                            <tr id='ligne<?php echo $ligne->t_lig_id  ?>'>
                                                                <td> <?php echo $ligne->t_lig_libelle; ?>
                                                                </td>
                                                                <td><?php if($ligne->pres_t_tarif == "F") { echo "Forfaitaire"; } else { echo "Tarif horaire"; } ?>
                                                                </td>
                                                                <td><?php echo $ligne->pres_tarif_std; ?>
                                                                </td>
                                                                <td><?php echo $ligne->pres_tarif_jr; ?>
                                                                </td>
                                                                <td><?php echo $ligne->pres_tarif_sr; ?>
                                                                </td>
                                                                <td><?php echo $ligne->pres_tarif_mgr; ?>
                                                                </td>
                                                                <td><a class='btn btn-danger btn-sm' onclick="supModelPrestaUpdateEx('<?php echo $ligne->t_lig_id; ?>')">
                                                                        <i class='icon-plus fa fa-remove'></i> Supprimer</a>
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
