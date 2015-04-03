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

            //var_dump($_POST);
            //exit();

            /* Recuperation des inputs avec verification de leur initialisation  
             * Si on trouve une erreur, on renvoie l'utilisateur sur la page de creation de modele */
            $name = "";
            if (filter_input(INPUT_POST, 'name') != NULL) {
                $name = filter_input(INPUT_POST, 'name');
            } else {
                returnToCreateModel($error);
            }

            $client = "";
            if (filter_input(INPUT_POST, 'client') != NULL) {
                $client = filter_input(INPUT_POST, 'client');
            } else {
                returnToCreateModel($error);
            }

            $area = "";
            if (filter_input(INPUT_POST, 'area') != NULL) {
                $area = filter_input(INPUT_POST, 'area');
            } else {
                returnToCreateModel($error);
            }

            $type_dossier = "";
            if (filter_input(INPUT_POST, 'type_dossier') != NULL) {
                $type_dossier = filter_input(INPUT_POST, 'type_dossier');
            } else {
                returnToCreateModel($error);
            }

            $ope = "";
            if (filter_input(INPUT_POST, 'type_operation') != NULL) {
                $ope = filter_input(INPUT_POST, 'type_operation');
            } else {
                returnToCreateModel($error);
            }


            $type_facture = "";
            if (filter_input(INPUT_POST, 'type') != NULL) {
                $type_facture = filter_input(INPUT_POST, 'type');
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

            $nbInfosTot = 0;
            if (filter_input(INPUT_POST, 'nbInfosTot') != NULL) {
                $nbInfosTot = filter_input(INPUT_POST, 'nbInfosTot');
            } else {
                returnToCreatePrestation($error);
            }

            /* Verification des inputs */
            if($name == "") {
                returnToCreateModel($error);
            }

            $presta_id = array();
            $presta_lib = array();
            // On recupere dans une boucle les infos concernant les lignes de presta. du modele.
            for($i=0; $i<$nbInfosTot;$i++) {
                //On verifie que la ligne n'a pas ete supprimée en dynamique
                if(filter_input(INPUT_POST, 'supp' . $i) == NULL) {

                    $presta_id[$i] = "";
                    if (filter_input(INPUT_POST, 'presta_id_' . $i) != NULL) {
                        $presta_id[$i] = filter_input(INPUT_POST, 'presta_id_' . $i);
                    } else {
                        returnToCreateModel($error);
                    }

                    $presta_lib[$i] = "";
                    if (filter_input(INPUT_POST, 'presta_lib_' . $i) != NULL) {
                        $presta_lib[$i] = filter_input(INPUT_POST, 'presta_lib_' . $i);
                    } else {
                        returnToCreateModel($error);
                    }
                }
            }

            // Remplissage des champs non demandees dans le formulaire
            $id_fac = generateId("TFA", "re", "type_facture");
            // On recupere la date actuelle
            $creadate = date(date("Y-m-d H:i:s"));
            $moddate = date(date("Y-m-d H:i:s"));
            // On recupere l'utilisateur qui a fait l'ajout (statique car pas encore de gestion de session + user)
            $creauser = "GLS";
            $moduser = "GLS";

            // Creation des requetes d'insertions et ajout dans la base du modele
            $insert_string = "INSERT INTO type_facture "
                . "(t_fac_id, t_fac_rf_typdos, t_fac_rf_ent, t_fac_creadate, "
                . "t_fac_moddate, t_fac_creauser, t_fac_moduser, t_fac_type, t_fac_objet, "
                . "t_fac_rf_ope, t_fac_langue, t_fac_area, t_fac_modelname) VALUES "
                . "(:id, :type_dos, :ent, :creadate, "
                . ":moddate, :creauser, :moduser, :factype, :objet, "
                . ":ope, :langue, :area, :name)";

            $stmt_insert = $pdo->prepare($insert_string);

            // On lie les parametres recuperés via le formulaire pour les associer a la requete
            $stmt_insert->bindParam(':id', $id_fac);
            $stmt_insert->bindParam(':type_dos', $type_dossier);
            $stmt_insert->bindParam(':ent', $client);
            $stmt_insert->bindParam(':creadate', $creadate);
            $stmt_insert->bindParam(':moddate', $moddate);
            $stmt_insert->bindParam(':creauser', $creauser);
            $stmt_insert->bindParam(':moduser', $moduser);
            $stmt_insert->bindParam(':factype', $type_facture);
            $stmt_insert->bindParam(':objet', $objet);
            $stmt_insert->bindParam(':ope', $ope);
            $stmt_insert->bindParam(':langue', $language);
            $stmt_insert->bindParam(':area', $area);
            $stmt_insert->bindParam(':name', $name);

            // On execute la requete
            $stmt_insert->execute();

            // Creation des requetes d'insertions et ajout dans la base des lignes
            $insert_string = "INSERT INTO type_ligne "
                . "(t_lig_id, t_lig_rf_pres, t_lig_creadate, t_lig_moddate,"
                . "t_lig_creauser, t_lig_moduser, t_lig_rf_typ_fac, t_lig_libelle)"
                . "VALUES"
                . "(:id, :rf_pres, :creadate, :moddate,"
                . ":creauser, :moduser, :rf_typ_fac, :libelle)";

            $stmt_insert = $pdo->prepare($insert_string);

            for($i=0; $i<$nbInfosTot;$i++){

                if(isset($presta_id[$i])) {

                    $id_lig = generateId("TLI", "re", "type_ligne");

                    // On lie les parametres recuperés via le formulaire pour les associer a la requete
                    $stmt_insert->bindParam(':id', $id_lig);
                    $stmt_insert->bindParam(':rf_pres', $presta_id[$i]);
                    $stmt_insert->bindParam(':creadate', $creadate);
                    $stmt_insert->bindParam(':moddate', $moddate);
                    $stmt_insert->bindParam(':creauser', $creauser);
                    $stmt_insert->bindParam(':moduser', $moduser);
                    $stmt_insert->bindParam(':rf_typ_fac', $id_fac);
                    $stmt_insert->bindParam(':libelle', $presta_lib[$i]);

                    // On execute la requete
                    $stmt_insert->execute();
                }
            }

        //var_dump(sizeof($presta_id));
        //exit();

            // On retourne a la page d'accueil
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
