<?php
/********************************************
 * model.php                            *
 * Gère les modifications dans la BD         *
 * pour les modeles                      *
 *                                           *
 * Auteurs : Anne-Sophie Balestra            *
 *           Abdoul Wahab Haidara            *
 *           Yvan-Christian Maso             *
 *           Baptiste Quere                  *
 *           Yoann Le Taillanter             *
 *                                           *
 * Date de creation : 23/03/2015             *
 ********************************************/

//Connexion a la base de données
$pdo = new SPDO();

/* On verifie l'action demandee */
if (filter_input(INPUT_GET, 'action') != NULL) {
    /* En fonction de la page passee en action, on se dirige vers la page correspondante */
    switch (filter_input(INPUT_GET, 'action')) {
        /* Ajout d'un nouveau modele */
        case('insertModel'):
            /* Erreur a retourner si besoin */
            $error = "Certains champs n'ont pas été remplis correctement. Merci de recommencer.";

            /* Recuperation des inputs avec verification de leur initialisation  
             * Si on trouve une erreur, on renvoie l'utilisateur sur la page de creation de modele */
            $name = "";
            if (filter_input(INPUT_POST, 'name') != NULL) {
                $name = filter_input(INPUT_POST, 'name');
            } else {
                returnToCreateModel($error);
            }

            $type_dossier = "";
            if (filter_input(INPUT_POST, 'type_dossier') != NULL) {
                $type_dossier = filter_input(INPUT_POST, 'type_dossier');
            } else {
                returnToCreateModel($error);
            }

            $objet = "";
            if (filter_input(INPUT_POST, 'objet') != NULL) {
                $objet = filter_input(INPUT_POST, 'objet');
            } else {
                returnToCreateModel($error);
            }

            $language = "";
            if (filter_input(INPUT_POST, 'language') != NULL) {
                $language = filter_input(INPUT_POST, 'language');
            } else {
                returnToCreateModel($error);
            }

            $TVA = "";
            if (filter_input(INPUT_POST, 'TVA') != NULL) {
                $TVA = filter_input(INPUT_POST, 'TVA');
            } else {
                returnToCreateModel($error);
            }

            $area = "";
            if (filter_input(INPUT_POST, 'area') != NULL) {
                $area = filter_input(INPUT_POST, 'area');
            } else {
                returnToCreateModel($error);
            }


            /* Verification des inputs */
            if($name == "") {
                returnToCreateModel($error);
            }

            /* Creation des requetes d'insertions et ajout dans la base */
            $insert_string = "INSERT INTO type_facture "
                . "(t_fac_id, t_fac_modelname, t_fac_rf_typdos, t_fac_rf_ent, t_fac_creadate, "
                . "t_fac_moddate, t_fac_creauser, t_fac_moduser, t_fac_type, t_fac_objet, "
                . "t_fac_rf_ope, t_fac_tauxtva, t_fac_langue, t_fac_area) VALUES "
                . "(:id, :name, :type_dos, :type_ent, :creadate, "
                . ":moddate, :creauser, :moduser, :factype, :objet, "
                . ":ope, :tauxtva, :langue, :area)";

            $stmt_insert = $pdo->prepare($insert_string);

            //On lie les parametres recuperés via le formulaire pour les associer a la requete
            $stmt_insert->bindParam(':id', $id);
            $stmt_insert->bindParam(':name', $name);
            $stmt_insert->bindParam(':type_dos', $type_dossier);
            $stmt_insert->bindParam(':tauxtva', $TVA);
            $stmt_insert->bindParam(':langue', $language);
            $stmt_insert->bindParam(':objet', $objet);
            $stmt_insert->bindParam(':area', $area);

            $stmt_insert->bindParam(':type_ent', $type_ent);
            $stmt_insert->bindParam(':creadate', $creadate);
            $stmt_insert->bindParam(':moddate', $moddate);
            $stmt_insert->bindParam(':creauser', $creauser);
            $stmt_insert->bindParam(':moduser', $moduser);
            $stmt_insert->bindParam(':factype', $factype);
            $stmt_insert->bindParam(':ope', $ope);


            //Remplissage des champs non demandees dans le formulaire
            $id = generateId("TYP", "re", "type_facture");
            //On recupere la date actuelle
            $creadate = date(date("Y-m-d H:i:s"));
            $moddate = date(date("Y-m-d H:i:s"));
            //On recupere l'utilisateur qui a fait l'ajout
            $creauser = "GLS";
            $moduser = "GLS";


            //On execute la requete
            $stmt_insert->execute();

            //On retourne a la page d'accueil
            //returnToIndex();
            //break;
    }
}


/*****
 * returnToCreateModel : renvoie l'utilisateur sur la page de creation d'un modele s'il a mal rempli le formulaire
 *
 * String p_error: message d'erreur a afficher quand l'utilisateur est renvoyé sur la page
 ***/
function returnToCreateModel($p_error){
    echo "<script>alert(\"" . $p_error . "\");window.location.href='index.php?action=createModel';</script>";
    exit;
    }
