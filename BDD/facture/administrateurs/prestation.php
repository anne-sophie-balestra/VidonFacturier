<?php
/********************************************
* prestation.php                            *
* Gère les modifications dans la BD         *
* pour les prestations                      *
*                                           *
* Auteurs : Anne-Sophie Balestra            *
*           Abdoul Wahab Haidara            *
*           Yvan-Christian Maso             *
*           Baptiste Quere                  *
*           Yoann Le Taillanter             *
*                                           *
* Date de creation : 12/03/2015             *
********************************************/

//Connexion a la base de données
$pdo = new SPDO();

/* On verifie l'action demandee */
if (filter_input(INPUT_GET, 'action') != NULL) {
    /* En fonction de la page passee en action, on se dirige vers la page correspondante */
    switch (filter_input(INPUT_GET, 'action')) {
        /* Ajout d'une nouvelle prestation */
        case('insertPrestation'):
            /* Erreur a retourner si besoin */
            $error = "Certains champs n'ont pas été remplis correctement. Merci de recommencer.";
            
            /* Recuperation des inputs avec verification de leur initialisation  
             * Si on trouve une erreur, on renvoie l'utilisateur sur la page de creation de prestation */
            $operation = "";
            if (filter_input(INPUT_POST, 'operation') != NULL) {
                $operation = filter_input(INPUT_POST, 'operation');
            } else {
                returnToCreatePrestation($error);
            }
            
            $type_dossier = "";
            if (filter_input(INPUT_POST, 'type_dossier') != NULL) {
                $type_dossier = filter_input(INPUT_POST, 'type_dossier');
            } else {
                returnToCreatePrestation($error);
            }
            
            $nom_code = "";
            if (filter_input(INPUT_POST, 'nom_code') != NULL) {
                $nom_code = filter_input(INPUT_POST, 'nom_code');
            } else {
                returnToCreatePrestation($error);
            }
            
            $pays = "";
            if (filter_input(INPUT_POST, 'pays') != NULL) {
                $pays = filter_input(INPUT_POST, 'pays');
            } else {
                returnToCreatePrestation($error);
            }
            
            $prestation = "";
            if (filter_input(INPUT_POST, 'prestation') != NULL) {
                $prestation = filter_input(INPUT_POST, 'prestation');
            } else {
                returnToCreatePrestation($error);
            }
            
            $repartition = 0;
            if (filter_input(INPUT_POST, 'repartition') != NULL) {
                $repartition = filter_input(INPUT_POST, 'repartition');
            } else {
                returnToCreatePrestation($error);
            }
            
            $nbInfos = 0;
            if (filter_input(INPUT_POST, 'nbInfos') != NULL) {
                $nbInfos = filter_input(INPUT_POST, 'nbInfos');
            } else {
                returnToCreatePrestation($error);
            }
            
            $libelles = array();
            $type_tarifs = array();
            $tarifs = array();
            
            for($i=1; $i<=$nbInfos;$i++) {
                $libelles[$i] = "";
                if (filter_input(INPUT_POST, 'libelle' . $i) != NULL) {
                    $libelles[$i] = filter_input(INPUT_POST, 'libelle' . $i);
                } else {
                returnToCreatePrestation($error);
                }
                
                $type_tarifs[$i] = "";
                if (filter_input(INPUT_POST, 't_tarif' . $i) != NULL) {
                    $type_tarifs[$i] = filter_input(INPUT_POST, 't_tarif' . $i);
                } else {
                    returnToCreatePrestation($error);
                }
                
                $tarifs[$i] = "";
                if($type_tarifs[$i] == 'F') {
                    if (filter_input(INPUT_POST, 'tarif' . $i) != NULL) {
                        $tarifs[$i] = filter_input(INPUT_POST, 'tarif' . $i);
                    } else {
                        returnToCreatePrestation($error);
                    }
                } else {
                    $tarifs[$i] = array();
                    if (filter_input(INPUT_POST, 'tarif_jr' . $i) != NULL) {
                        array_push($tarifs[$i], filter_input(INPUT_POST, 'tarif_jr' . $i));
                    } else {
                        returnToCreatePrestation($error);
                    }
                    if (filter_input(INPUT_POST, 'tarif_sr' . $i) != NULL) {
                        array_push($tarifs[$i], filter_input(INPUT_POST, 'tarif_sr' . $i));
                    } else {
                        returnToCreatePrestation($error);
                    }
                    if (filter_input(INPUT_POST, 'tarif_mgr' . $i) != NULL) {
                        array_push($tarifs[$i], filter_input(INPUT_POST, 'tarif_mgr' . $i));
                    } else {
                        returnToCreatePrestation($error);
                    }
                }
            }

            /* Verification des inputs */
            if($prestation == "") {
                returnToCreatePrestation($error);
            }
            
            foreach ($libelles as $lib) {
                if($lib == "") {
                    returnToCreatePrestation($error);
                }
            }
            
            foreach ($type_tarifs as $t_tarif) {
                if($t_tarif == "") {
                    returnToCreatePrestation($error);
                }
            }
            
            foreach ($tarifs as $tarif) {
                if(is_array($tarif)) {
                    foreach ($tarif as $tarif_spec) {
                        if(!is_numeric($tarif_spec)) {
                            returnToCreatePrestation($error);
                        }
                    }
                } else {
                    if(!is_numeric($tarif)) {
                        returnToCreatePrestation($error);
                    }
                }
            }
            
            //On genere un id pour la prestation general qui permettra de regrouper toutes les lignes de cette prestation
            $id_gen = generateId("PGE", "re", "prestation_generale");
            
            /* Creation des requetes d'insertions et ajout dans la base */
            $insert_string = "INSERT INTO prestation " 
                    . "(pres_id, pres_creadate, pres_moddate, pres_creauser, pres_moduser, " 
                    . "pres_rf_nom, pres_id_general, pres_prestation, pres_libelle_ligne_fac, pres_t_tarif, pres_tarif_std, " 
                    . "pres_tarif_jr, pres_tarif_sr, pres_tarif_mgr, pres_repartition_cons, " 
                    . "pres_rf_pay, pres_rf_typ_dossier, pres_rf_typ_operation) VALUES "
                    . "(:id, :creadate, :moddate, :creauser, :moduser, " 
                    . ":nom_code, :id_gen, :prestation, :libelle, :type_tarif, :tarif_std, " 
                    . ":tarif_jr, :tarif_sr, :tarif_mgr, :repartition, " 
                    . ":pays, :type_dossier, :operation)";
            
            $stmt_insert = $pdo->prepare($insert_string);
            
            //On lie les parametres recuperés via le formulaire pour les associer a la requete
            $stmt_insert->bindParam(':id', $id);
            $stmt_insert->bindParam(':creadate', $date);
            $stmt_insert->bindParam(':moddate', $date);
            $stmt_insert->bindParam(':creauser', $user);
            $stmt_insert->bindParam(':moduser', $user);
            $stmt_insert->bindParam(':nom_code', $nom_code);
            $stmt_insert->bindParam(':id_gen', $id_gen);
            $stmt_insert->bindParam(':prestation', $prestation);
            $stmt_insert->bindParam(':libelle', $libelle);
            $stmt_insert->bindParam(':type_tarif', $type_tarif);
            $stmt_insert->bindParam(':tarif_std', $tarif_std);
            $stmt_insert->bindParam(':tarif_jr', $tarif_jr);
            $stmt_insert->bindParam(':tarif_sr', $tarif_sr);
            $stmt_insert->bindParam(':tarif_mgr', $tarif_mgr);
            $stmt_insert->bindParam(':repartition', $repartition);
            $stmt_insert->bindParam(':pays', $pays);
            $stmt_insert->bindParam(':type_dossier', $type_dossier);
            $stmt_insert->bindParam(':operation', $operation);
            
            //On cree autant de lignes dans la base que de lignes de prestation
            for($i=1;$i<=$nbInfos;$i++){
                //On genere un id 
                $id = generateId("PRE", "re", "prestation");
                //On recupere la date actuelle
                $date = date(date("Y-m-d H:i:s"));
                //On recupere l'utilisateur qui a fait l'ajout
                $user = "GLS";
                $libelle = $libelles[$i];
                $type_tarif = $type_tarifs[$i];
                if(is_array($tarifs[$i])) {
                    $tarif_std = 0;
                    $tarif_jr = $tarifs[$i][0];
                    $tarif_sr = $tarifs[$i][1];
                    $tarif_mgr = $tarifs[$i][2];
                } else {
                    $tarif_std = $tarifs[$i];
                    $tarif_jr = 0;
                    $tarif_sr = 0;
                    $tarif_mgr = 0;
                }
                //On execute la requete
                $stmt_insert->execute();                
            }
            //On retourne a la page d'accueil
            returnToIndex();
            break;
    }
}

/*****
 * returnToCreatePrestation : renvoie l'utilisateur sur la page de creation d'une prestation s'il a mal rempli le formulaire
 * 
 * String p_error: message d'erreur a afficher quand l'utilisateur est renvoyé sur la page 
 ***/
function returnToCreatePrestation($p_error){
    echo "<script>alert(\"" . $p_error . "\");window.location.href='index.php?action=createPrestation';</script>";
    exit;
}