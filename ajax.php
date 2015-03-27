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
if (filter_input(INPUT_GET, 'action') != NULL)
{ 
    /* En fonction de la page passee en action, on se dirige vers la page correspondante */
    switch (filter_input(INPUT_GET, 'action'))
    {
        /*
         * FONCTIONS AJAX
         */
        //Permet de retourner un JSON contenant les types de dossier en fonction de l'entite choisie afin de peupler le select dans create prestation
        case('genererListeTypeDossier'):
            $entite = (filter_input(INPUT_GET, 'ent') != NULL ? filter_input(INPUT_GET, 'ent') : "");
            genererListeTypeDossier($entite);
            break;
        
        //Permet de retourner un JSON contenant les prestations liees au code choisi dans la modification d'une prestation
        case('genererListePrestationsLiees'):
            $code = (filter_input(INPUT_GET, 'code') != NULL ? filter_input(INPUT_GET, 'code') : "");
            genererListePrestationsLiees($code);
            break;   

        //
        case('genererListePresta'):
            $dos = (filter_input(INPUT_GET, 'dos') != NULL ? filter_input(INPUT_GET, 'dos') : "");
            $ope = (filter_input(INPUT_GET, 'ope') != NULL ? filter_input(INPUT_GET, 'ope') : "");
            genererListePresta($dos,$ope);
            break; 
        
        //Cree les blocs d'informations pour les create prestation
        case('genererInfosPrestation'):
            $nb = (filter_input(INPUT_GET, 'nb') != NULL ? filter_input(INPUT_GET, 'nb') : 1);
            $nom = (filter_input(INPUT_GET, 'nom') != NULL ? filter_input(INPUT_GET, 'nom') : "");
            genererInfosPrestation($nb, $nom);
            break;              
        
        //Genere les informations de la prestation que nous pouvons modifier
        case('genererInfosPrestationUpdate'):
            $prestation = (filter_input(INPUT_GET, 'pre') != NULL ? filter_input(INPUT_GET, 'pre') : "");
            genererInfosPrestationUpdate($prestation);
            break;              
        
        //Genere les blocs de tarifs selon si on a choisit forfaitaire ou horaire
        case('genererTarifs'):
            $tt = (filter_input(INPUT_GET, 'tt') != NULL ? filter_input(INPUT_GET, 'tt') : "");
            $num = (filter_input(INPUT_GET, 'num') != NULL ? filter_input(INPUT_GET, 'num') : 1);
            genererTarifs($tt, $num);
            break;  

        //Genere une ligne de tableau dans contenant la prestation dans createModel.php
        case('getPrestationTabFromID'):
            $presta = (filter_input(INPUT_GET, 'presta') != NULL ? filter_input(INPUT_GET, 'presta') : "");
            $nbInfos = (filter_input(INPUT_GET, 'nbInfos') != NULL ? filter_input(INPUT_GET, 'nbInfos') : "");
            $nbInfosTot = (filter_input(INPUT_GET, 'nbInfosTot') != NULL ? filter_input(INPUT_GET, 'nbInfosTot') : "");
            getPrestationTabFromID($presta, nbInfos, nbInfosTot);
            break;              
    }
}

/*****
 * genererListeTypeDossier : genere les infos du select pour les types de dossier en fonction de l'entite (brevet ou juridique)
 *
 * @param String $p_entite : entite choisie
 ***/
function genererListeTypeDossier($p_entite)
{    
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
 * genererListePrestationsLiees : genere les infos du select pour les prestations liees au code de nomenclature choisi
 *
 * @param String $p_code : code choisi
 ***/
function genererListePrestationsLiees($p_code)
{    
    $pdo = new SPDO;
    
    /* On recupere les prestations liees au code */
    $stmt_prestation = "SELECT DISTINCT(pres_prestation) FROM prestation WHERE pres_rf_nom = :code ORDER BY pres_prestation";
    $result_prestation = $pdo->prepare($stmt_prestation);
    $result_prestation->bindParam(":code", $p_code);
    $result_prestation->execute();
    
    //On cree un array avec le nom de la prestation que l'on va retourner en JSON
    $array_pres = array();    
    foreach($result_prestation->fetchAll(PDO::FETCH_OBJ) as $presta) {
        /* On recupere les prestations liees au code */
        $stmt_prestation_infos = "SELECT pres_id, pres_libelle_ligne_fac FROM prestation WHERE pres_rf_nom = :code AND pres_prestation = :prestation ORDER BY pres_prestation";
        $result_prestation_infos = $pdo->prepare($stmt_prestation_infos);
        $result_prestation_infos->bindParam(":code", $p_code);
        $result_prestation_infos->bindParam(":prestation", $presta->pres_prestation);
        $result_prestation_infos->execute();
        foreach($result_prestation_infos->fetchAll(PDO::FETCH_OBJ) as $info) {
            $array_pres[$presta->pres_prestation][$info->pres_id] = $info->pres_libelle_ligne_fac;
        }
    }
    echo json_encode($array_pres);
}

/*****
 * genererInfosPrestation : genere le select pour les types de dossier en fonction de l'entite (brevet ou juridique)
 *
 * @param int $p_nb : nombre de blocs à générer
 * @param String $p_nom : nom des blocs
 ***/
function genererInfosPrestation($p_nb, $p_nom)
{   
    for($i=1;$i<=$p_nb;$i++) { ?>        
        <div class="panel panel-default">
            <!--On cree des panneaux en mode Accordeon pour afficher les informations liées a une ligne de prestation-->
            <div class="panel-heading" style="cursor: pointer;" role="tab" id="title_<?php echo $p_nom . "_" . $i; ?>" data-toggle="collapse" data-parent="#infosPrestation" href="#<?php echo $p_nom . "_" . $i; ?>" aria-expanded="true" aria-controls="<?php echo $p_nom . "_" . $i; ?>">
                <h4 class="panel-title"><?php echo $p_nom . " " . $i; ?></h4>
            </div>
            <div id="<?php echo $p_nom . "_" . $i; ?>" class="panel-collapse collapse <?php if($i==1) { echo 'in';} ?>" role="tabpanel" aria-labelledby="title_<?php echo $p_nom . "_" . $i; ?>">
                <div class="panel-body">                    
                    <div class="form-group">
                        <label class="control-label" for="libelle<?php echo $i; ?>">Libellé :</label>
                        <input name="libelle<?php echo $i; ?>" type="text" required class="form-control" id="libelle<?php echo $i; ?>" maxlength="255">
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="t_tarif<?php echo $i; ?>">Type de tarification :</label>
                        <!--On choisit le type de tarification et on genere les champs qu'il faut en fonction-->
                        <select name="t_tarif<?php echo $i; ?>" id="t_tarif<?php echo $i; ?>" required class="form-control" onchange="genererTarifs('tarifs<?php echo $i; ?>', this.value, <?php echo $i; ?>);">
                            <option value="" disabled selected>Choisissez un type de tarification...</option>
                            <option value="F">Forfaitaire</option>
                            <option value="TH">Tarif Horaire</option>
                        </select>
                    </div>
                    <div id="tarifs<?php echo $i; ?>"></div>
                </div>
            </div>
        </div>
    <?php }
}

/*****
 * genererTarifs : genere les inputs pour inserer les tarifs
 *
 * @param String $p_tt : type de tarification choisi
 * @param int $p_num : numero de la prestation en cours
 ***/
function genererTarifs($p_tt, $p_num)
{    
    //Si on a choisit une tarification forfaitaire, on a un seul tarif
    if($p_tt == "F") { ?>
        <div class="form-group">
            <label class="control-label" for="tarif<?php // echo $p_num; ?>">Tarif :</label>
            <div class="input-group">
                <input name="tarif<?php // echo $p_num; ?>" id="tarif<?php // echo $p_num; ?>" type="text" pattern="\d+(\.\d{1,2})?" data-error='Veuillez renseigner un montant (ex: 400.50)' required class="form-control">
                <span class="input-group-addon">€</span>
            </div>
            <div class="help-block with-errors"></div>
        </div>
    <?php } 
    //Si on a choisit une tarification horaire, on a trois tarifs (junior, senior et manager)
    else { ?>
        <div class="form-group">
            <label class="control-label" for="tarif_jr<?php // echo $p_num; ?>">Tarif junior :</label>
            <div class="input-group">
                <input name="tarif_jr<?php // echo $p_num; ?>" id="tarif_jr<?php // echo $p_num; ?>" type="text" pattern="\d+(\.\d{1,2})?" data-error='Veuillez renseigner un montant (ex: 400.50)' required class="form-control">
                <span class="input-group-addon">€</span>
            </div>
            <div class="help-block with-errors"></div>
        </div>        
        <div class="form-group">
            <label class="control-label" for="tarif_sr<?php // echo $p_num; ?>">Tarif senior :</label>
            <div class="input-group">
                <input name="tarif_sr<?php // echo $p_num; ?>" id="tarif_sr<?php // echo $p_num; ?>" type="text" pattern="\d+(\.\d{1,2})?" data-error='Veuillez renseigner un montant (ex: 400.50)' required class="form-control">
                <span class="input-group-addon">€</span>
            </div>
            <div class="help-block with-errors"></div>
        </div>        
        <div class="form-group">
            <label class="control-label" for="tarif_mgr<?php // echo $p_num; ?>">Tarif manager :</label>
            <div class="input-group">
                <input name="tarif_mgr<?php // echo $p_num; ?>" id="tarif_mgr<?php // echo $p_num; ?>" type="text" pattern="\d+(\.\d{1,2})?" data-error='Veuillez renseigner un montant (ex: 400.50)' required class="form-control">
                <span class="input-group-addon">€</span>
            </div>
            <div class="help-block with-errors"></div>
        </div>        
    <?php }
}

/*****
 * genererListePresta : genere le select pour la list des presta dans la création d'un modèle (type_facture)
 *
 * @param String $t_dos : type de dossier
 * @param String $t_ope : type d'opération
 ***/
function genererListePresta($t_dos, $t_ope)
{    
    $pdo = new SPDO;
    
    /* On recupere les types de dossier en fonction de l'entite */
    $stmt_presta_list = "SELECT pres_libelle_ligne_fac, pres_id FROM prestation";
     /*   "SELECT p.pres_libelle_ligne_fac FROM prestation p, dossier d, operation o";
        WHERE p.pres_rf_typ_dossier = d.t_dos_id
        AND p.pres_rf_typ_operation = o.t_ope_id
        AND o.ope_type = '".$t_dos;*/
    $result_presta_list = $pdo->prepare($stmt_presta_list);
    $result_presta_list->execute();
    
    //On cree un array avec l'id et le nom du type de presta que l'on va retourner en JSON
    $array_presta = array();    
    foreach($result_presta_list->fetchAll(PDO::FETCH_OBJ) as $presta_list) {
        $array_presta[$presta_list->pres_id] = $presta_list->pres_libelle_ligne_fac;
    }
    echo json_encode($array_presta);
}


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
        foreach($result_presta_list->fetchAll(PDO::FETCH_OBJ) as $presta_list) ?>
            <tr id="ligne<?php echo $nbInfosTot; ?>"
            <input type="hidden" value="<?php echo $presta_list->pres_id; ?> " name="presta_id_<?php echo $nbInfosTot; ?>" id="presta_id_<?php echo $nbInfosTot; ?>"/>
            <td><?php echo $presta_list->pres_libelle_ligne_fac; ?></td>
            <td><?php echo $presta_list->pres_t_tarif; ?></td>
            <td><?php echo $presta_list->pres_tarif_std; ?></td>
            <td><?php echo $presta_list->pres_tarif_jr; ?></td>
            <td><?php echo $presta_list->tarif_sr; ?></td>
            <td><?php echo $presta_list->tarif_mgr; ?></td>
            <td align="center"><a class='btn btn-danger btn-sm' onclick='supModelPresta(<?php echo $nbInfosTot; ?>)'><i class='icon-plus fa fa-edit'></i> Supprimer</a></td>
            <?php 
        }
    }
