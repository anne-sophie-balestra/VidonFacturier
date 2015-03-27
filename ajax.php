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

        //
        case('genererListePresta'):
            $dos = (filter_input(INPUT_GET, 'dos') != NULL ? filter_input(INPUT_GET, 'dos') : "");
            genererListePresta($dos,"test");
            break;         
        
        //Genere les blocs de tarifs selon si on a choisit forfaitaire ou horaire
        case('genererModalLignePrestation'):
            $prestation = (filter_input(INPUT_GET, 'pre') != NULL ? filter_input(INPUT_GET, 'pre') : 0);
            genererModalLignePrestation($prestation);
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
 * genererListePresta : genere le select pour la list des presta dans la création d'un modèle (type_facture)
 *
 * @param String $t_dossier_rf : type de dossier
 * @param String $t_ope : type d'opération
 ***/
function genererListePresta($t_dossier_rf, $t_ope)
{    
    $pdo = new SPDO;
    
    /* On recupere les types de dossier en fonction de l'entite */
    $stmt_presta_list =
        "SELECT pres_libelle_ligne_fac FROM prestation";
     /*   WHERE dos.t_dos_id = '" . $t_dossier_rf . "' 
        AND 
        ORDER BY t_dos_type";*/
    $result_presta_list = $pdo->prepare($stmt_presta_list);
    $result_presta_list->execute();
    
    //On cree un array avec l'id et le nom du type de dossier que l'on va retourner en JSON
    $array_presta = array();    
    foreach($result_presta_list->fetchAll(PDO::FETCH_OBJ) as $presta_list) {
        $array_presta[$presta_list->pres_libelle_ligne_fac] = $presta_list->pres_libelle_ligne_fac;
    }
    echo json_encode($array_presta);
}

/*****
 * genererModalLignePrestation : genere le modal pour ajouter une ligne de prestation dans createPrestation
 * 
 * @param int $prestation : contient le numero de la ligne de prestation si c'est une modification, 0 si c est un ajout
 ***/
function genererModalLignePrestation($prestation)
{  
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
                    <button type="button" class="btn btn-primary" id="subAction" data-dismiss="modal" <?php if($prestation != 0) { ?> onclick="modifierPrestationForm('ligne<?php echo $prestation; ?>', <?php echo $prestation; ?>);" <?php } else { ?> onclick="ajouterPrestationForm('listePrestations');"  disabled <?php }?>><?php echo $actionVerbe; ?></button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
<?php }


