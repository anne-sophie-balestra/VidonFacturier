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

require_once("SPDO.php");

/* On verifie si une page a ete demandee */
if (filter_input(INPUT_GET, 'action') != NULL)
{ 
    /* En fonction de la page passee en action, on se dirige vers la page correspondante */
    switch (filter_input(INPUT_GET, 'action'))
    {
        /*
         * FONCTIONS AJAX
         */
        case('genererListeTypeDossier'):
            $entite = (filter_input(INPUT_GET, 'ent') != NULL ? filter_input(INPUT_GET, 'ent') : "");
            genererListeTypeDossier($entite);
            break;   
        
        case('genererInfosPrestation'):
            $nb = (filter_input(INPUT_GET, 'nb') != NULL ? filter_input(INPUT_GET, 'nb') : 1);
            $nom = (filter_input(INPUT_GET, 'nom') != NULL ? filter_input(INPUT_GET, 'nom') : "");
            genererInfosPrestation($nb, $nom);
            break;              
        
        case('genererTarifs'):
            $tt = (filter_input(INPUT_GET, 'tt') != NULL ? filter_input(INPUT_GET, 'tt') : "");
            $num = (filter_input(INPUT_GET, 'num') != NULL ? filter_input(INPUT_GET, 'num') : 1);
            genererTarifs($tt, $num);
            break;              
    }
}

/*****
 * genererListeTypeDossier : genere le select pour les types de dossier en fonction de l'entite (brevet ou juridique)
 *
 * @param String $p_entite : entite choisie
 ***/
function genererListeTypeDossier($p_entite)
{    
    $pdo = new SPDO;
    
    /* On recupere les types de dossier en fonction de l'entite */
    $stmt_t_dos_type = "SELECT t_dos_id, t_dos_type FROM type_dossier WHERE t_dos_entite = '" . $p_entite . "' ORDER BY t_dos_type";
    $result_t_dos_type = $pdo->prepare($stmt_t_dos_type);
    $result_t_dos_type->execute();
    
    $array_dos = array();    
    foreach($result_t_dos_type->fetchAll(PDO::FETCH_OBJ) as $t_dos_type) {
        $array_dos[$t_dos_type->t_dos_id] = $t_dos_type->t_dos_type;
    }
    echo json_encode($array_dos);
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
            <div class="panel-heading" style="cursor: pointer;" role="tab" id="title_<?php echo $p_nom . "_" . $i; ?>" data-toggle="collapse" data-parent="#infosPrestation" href="#<?php echo $p_nom . "_" . $i; ?>" aria-expanded="true" aria-controls="<?php echo $p_nom . "_" . $i; ?>">
                <h4 class="panel-title"><?php echo $p_nom . " " . $i; ?></h4>
            </div>
            <div id="<?php echo $p_nom . "_" . $i; ?>" class="panel-collapse collapse <?php if($i==1) { echo 'in';} ?>" role="tabpanel" aria-labelledby="title_<?php echo $p_nom . "_" . $i; ?>">
                <div class="panel-body">                    
                    <div class="form-group">
                        <label class="control-label" for="libelle<?php echo $i; ?>">Libellé :</label>
                        <input name="libelle<?php echo $i; ?>" type="text" required class="form-control" id="libelle<?php echo $i; ?>">
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="t_tarif<?php echo $i; ?>">Type de tarification :</label>
                        <select name="t_tarif<?php echo $i; ?>" id="t_tarif<?php echo $i; ?>" required class="form-control" onchange="genererTarifs('tarifs<?php echo $i; ?>', this.value, <?php echo $i; ?>);">
                            <option value="" disabled selected>Choisissez un type de tarification...</option>
                            <option value="F">Forfaitaire</option>
                            <option value="TH">Tarif Horaire</option>
                        </select>
                    </div>
                    <div id="tarifs<?php echo $i; ?>"></div>
                    <div class="form-group">
                        <label class="control-label" for="repartition<?php echo $i; ?>">Répartition des consultants :</label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <select id="pourcentage_select<?php echo $i; ?>" class="form-inline" onchange="document.getElementById('pourcentage<?php echo $i; ?>').innerHTML=this.value+'%';document.getElementById('repartition<?php echo $i; ?>').value=this.value;">
                                    <option>0</option>
                                    <option>5</option>
                                    <option>10</option>
                                    <option>15</option>
                                    <option>20</option>
                                    <option>25</option>
                                    <option>30</option>
                                    <option>35</option>
                                    <option>40</option>
                                    <option>45</option>
                                    <option selected>50</option>
                                    <option>55</option>
                                    <option>60</option>
                                    <option>65</option>
                                    <option>70</option>
                                    <option>75</option>
                                    <option>80</option>
                                    <option>85</option>
                                    <option>90</option>
                                    <option>95</option>
                                    <option>100</option>
                                </select>
                            </span>
                            <input name="repartition<?php echo $i; ?>" id="repartition<?php echo $i; ?>" onchange="document.getElementById('pourcentage<?php echo $i; ?>').innerHTML=this.value+'%';document.getElementById('pourcentage_select<?php echo $i; ?>').value=this.value;" type="range" min="0" max="100" step="5" required class="form-control">
                            <span id="pourcentage<?php echo $i; ?>" class="input-group-addon">50%</span>
                        </div>
                    </div>
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
    if($p_tt == "F") { ?>
        <div class="form-group">
            <label class="control-label" for="tarif<?php echo $p_num; ?>">Tarif :</label>
            <div class="input-group">
                <input name="tarif<?php echo $p_num; ?>" id="tarif<?php echo $p_num; ?>" type="text" pattern="\d+(\.\d{1,2})?" data-error='Veuillez renseigner un montant' required class="form-control">
                <span class="input-group-addon">€</span>
            </div>
            <div class="help-block with-errors"></div>
        </div>
    <?php } else { ?>
        <div class="form-group">
            <label class="control-label" for="tarif_jr<?php echo $p_num; ?>">Tarif junior :</label>
            <div class="input-group">
                <input name="tarif_jr<?php echo $p_num; ?>" id="tarif_jr<?php echo $p_num; ?>" type="text" pattern="\d+(\.\d{1,2})?" data-error='Veuillez renseigner un montant' required class="form-control">
                <span class="input-group-addon">€</span>
            </div>
            <div class="help-block with-errors"></div>
        </div>        
        <div class="form-group">
            <label class="control-label" for="tarif_sr<?php echo $p_num; ?>">Tarif senior :</label>
            <div class="input-group">
                <input name="tarif_sr<?php echo $p_num; ?>" id="tarif_sr<?php echo $p_num; ?>" type="text" pattern="\d+(\.\d{1,2})?" data-error='Veuillez renseigner un montant' required class="form-control">
                <span class="input-group-addon">€</span>
            </div>
            <div class="help-block with-errors"></div>
        </div>        
        <div class="form-group">
            <label class="control-label" for="tarif_mgr<?php echo $p_num; ?>">Tarif manager :</label>
            <div class="input-group">
                <input name="tarif_mgr<?php echo $p_num; ?>" id="tarif_mgr<?php echo $p_num; ?>" type="text" pattern="\d+(\.\d{1,2})?" data-error='Veuillez renseigner un montant' required class="form-control">
                <span class="input-group-addon">€</span>
            </div>
            <div class="help-block with-errors"></div>
        </div>        
    <?php }
}