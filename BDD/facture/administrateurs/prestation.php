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
            
            $type = "";
            if (filter_input(INPUT_POST, 'type') != NULL) {
                $type = filter_input(INPUT_POST, 'type');
            } else {
                returnToCreatePrestation($error);
            }
            
            //On fais un switch pour donner un libelle a la lettre du type choisi
            $type_lib = "";
            switch($type) {
                case "H": $type_lib = "honos"; break;
                case "F": $type_lib = "frais"; break;
                case "T": $type_lib = "taxes"; break;
            }
            
            $prestation = "";
            if (filter_input(INPUT_POST, 'prestation') != NULL) {
                $prestation = filter_input(INPUT_POST, 'prestation');
            } else {
                returnToCreatePrestation($error);
            }
            
            $pays = "";
            if (filter_input(INPUT_POST, 'pays') != NULL) {
                $pays = filter_input(INPUT_POST, 'pays');
            } else {
                returnToCreatePrestation($error);
            }
            
            $repartition = 0;
            if (filter_input(INPUT_POST, 'repartition') != NULL) {
                $repartition = filter_input(INPUT_POST, 'repartition');
            } else {
                returnToCreatePrestation($error);
            }
            
            $nbInfosTot = 0;
            if (filter_input(INPUT_POST, 'nbInfosTot') != NULL) {
                $nbInfosTot = filter_input(INPUT_POST, 'nbInfosTot');
            } else {
                returnToCreatePrestation($error);
            }
            
            $libelles = array();
            $type_tarifs = array();
            $tarifs = array();
            
            for($i=1; $i<=$nbInfosTot;$i++) {
                //On verifie que la ligne n'a pas ete supprimée en dynamique
                if(filter_input(INPUT_POST, 'supp' . $i) == NULL) {
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
                        if (filter_input(INPUT_POST, 'tarif_std' . $i) != NULL) {
                            $tarifs[$i] = filter_input(INPUT_POST, 'tarif_std' . $i);
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
                    . "pres_id_general, pres_rf_nom, pres_type, pres_prestation, pres_libelle_ligne_fac, pres_t_tarif, pres_tarif_std, " 
                    . "pres_tarif_jr, pres_tarif_sr, pres_tarif_mgr, pres_repartition_cons, " 
                    . "pres_rf_pay, pres_rf_typ_dossier, pres_rf_typ_operation) VALUES "
                    . "(:id, :creadate, :moddate, :creauser, :moduser, " 
                    . ":id_gen, :nom_code, :type, :prestation, :libelle, :type_tarif, :tarif_std, " 
                    . ":tarif_jr, :tarif_sr, :tarif_mgr, :repartition, " 
                    . ":pays, :type_dossier, :operation)";
            $stmt_insert = $pdo->prepare($insert_string);
            
            //On lie les parametres recuperés via le formulaire pour les associer a la requete
            $stmt_insert->bindParam(':id', $id);
            $stmt_insert->bindParam(':creadate', $date);
            $stmt_insert->bindParam(':moddate', $date);
            $stmt_insert->bindParam(':creauser', $user);
            $stmt_insert->bindParam(':moduser', $user);
            $stmt_insert->bindParam(':id_gen', $id_gen);
            $stmt_insert->bindParam(':nom_code', $nom_code);
            $stmt_insert->bindParam(':type', $type_lib);
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
            for($i=1;$i<=$nbInfosTot;$i++){
                if(filter_input(INPUT_POST, 'supp' . $i) == NULL) {
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
            }
            //On retourne a la page de la liste des prestations
            returnToListePrestations("");
            break;
            
        case('changePrestation'):
            /* Erreur a retourner si besoin */
            $error = "La modification de la prestation n'a pas pu être effectuée correctement. Merci de recommencer.";
            
            /* Recuperation des inputs avec verification de leur initialisation  
             * Si on trouve une erreur, on renvoie l'utilisateur sur la page de creation de prestation */
            $id_gen = "";
            //Contient l'id de la prestation (afin d'associer les nouvelles lignes a cette prestation)
            if (filter_input(INPUT_POST, 'pres_id_general') != NULL) {
                $id_gen = filter_input(INPUT_POST, 'pres_id_general');
            } else {
                returnToListePrestations($error);
            }
            
            $operation = "";
            if (filter_input(INPUT_POST, 'operation') != NULL) {
                $operation = filter_input(INPUT_POST, 'operation');
            } else {
                returnToListePrestations($error);
            }
            
            $type_dossier = "";
            if (filter_input(INPUT_POST, 'type_dossier') != NULL) {
                $type_dossier = filter_input(INPUT_POST, 'type_dossier');
            } else {
                returnToListePrestations($error);
            }
            
            $nom_code = "";
            if (filter_input(INPUT_POST, 'nom_code') != NULL) {
                $nom_code = filter_input(INPUT_POST, 'nom_code');
            } else {
                returnToListePrestations($error);
            }
            
            $type = "";
            if (filter_input(INPUT_POST, 'type') != NULL) {
                $type = filter_input(INPUT_POST, 'type');
            } else {
                returnToListePrestations($error);
            }
            
            //On fais un switch pour donner un libelle a la lettre du type choisi
            $type_lib = "";
            switch($type) {
                case "H": $type_lib = "honos"; break;
                case "F": $type_lib = "frais"; break;
                case "T": $type_lib = "taxes"; break;
            }
            
            $prestation = "";
            if (filter_input(INPUT_POST, 'prestation') != NULL) {
                $prestation = filter_input(INPUT_POST, 'prestation');
            } else {
                returnToListePrestations($error);
            }
            
            $pays = "";
            if (filter_input(INPUT_POST, 'pays') != NULL) {
                $pays = filter_input(INPUT_POST, 'pays');
            } else {
                returnToListePrestations($error);
            }
            
            $repartition = 0;
            if (filter_input(INPUT_POST, 'repartition') != NULL) {
                $repartition = filter_input(INPUT_POST, 'repartition');
            } else {
                returnToListePrestations($error);
            }
            
            $nbInfosTot = 0;
            if (filter_input(INPUT_POST, 'nbInfosTot') != NULL) {
                $nbInfosTot = filter_input(INPUT_POST, 'nbInfosTot');
            } else {
                returnToListePrestations($error);
            }
            
            //On recupere les id des prestations liées a la prestation generale que nous voulons modifier afin de faire des updates dessus
            $result_prestations = $pdo->prepare("SELECT pres_id FROM prestation WHERE pres_id_general = :id_gen");
            $result_prestations->bindParam(":id_gen", $id_gen);
            $result_prestations->execute();
            
            $presIdUp = array();
            $libellesUp = array();
            $type_tarifsUp = array();
            $tarifsUp = array();
                
            //On parcours ces prestations pour pouvoir les recuperer
            foreach($result_prestations->fetchAll(PDO::FETCH_OBJ) as $pres_id) {
                $libellesUp[$pres_id->pres_id] = "";
                if (filter_input(INPUT_POST, 'libelle' . $pres_id->pres_id) != NULL) {
                    $libellesUp[$pres_id->pres_id] = filter_input(INPUT_POST, 'libelle' . $pres_id->pres_id);
                } else {
                returnToListePrestations($error);
                }

                $type_tarifsUp[$pres_id->pres_id] = "";
                if (filter_input(INPUT_POST, 't_tarif' . $pres_id->pres_id) != NULL) {
                    $type_tarifsUp[$pres_id->pres_id] = filter_input(INPUT_POST, 't_tarif' . $pres_id->pres_id);
                } else {
                    returnToListePrestations($error);
                }

                $tarifsUp[$pres_id->pres_id] = "";
                if($type_tarifsUp[$pres_id->pres_id] == 'F') {
                    if (filter_input(INPUT_POST, 'tarif_std' . $pres_id->pres_id) != NULL) {
                        $tarifsUp[$pres_id->pres_id] = filter_input(INPUT_POST, 'tarif_std' . $pres_id->pres_id);
                    } else {
                        returnToListePrestations($error);
                    }
                } else {
                    $tarifsUp[$pres_id->pres_id] = array();
                    if (filter_input(INPUT_POST, 'tarif_jr' . $pres_id->pres_id) != NULL) {
                        array_push($tarifsUp[$pres_id->pres_id], filter_input(INPUT_POST, 'tarif_jr' . $pres_id->pres_id));
                    } else {
                        returnToListePrestations($error);
                    }
                    if (filter_input(INPUT_POST, 'tarif_sr' . $pres_id->pres_id) != NULL) {
                        array_push($tarifsUp[$pres_id->pres_id], filter_input(INPUT_POST, 'tarif_sr' . $pres_id->pres_id));
                    } else {
                        returnToListePrestations($error);
                    }
                    if (filter_input(INPUT_POST, 'tarif_mgr' . $pres_id->pres_id) != NULL) {
                        array_push($tarifsUp[$pres_id->pres_id], filter_input(INPUT_POST, 'tarif_mgr' . $pres_id->pres_id));
                    } else {
                        returnToListePrestations($error);
                    }
                }
            }
            
            /* Verification des inputs */
            foreach ($libellesUp as $lib) {
                if($lib == "") {
                    returnToListePrestations($error);
                }
            }

            foreach ($type_tarifsUp as $t_tarif) {
                if($t_tarif == "") {
                    returnToListePrestations($error);
                }
            }

            foreach ($tarifsUp as $tarif) {
                if(is_array($tarif)) {
                    foreach ($tarif as $tarif_spec) {
                        if(!is_numeric($tarif_spec)) {
                            returnToListePrestations($error);
                        }
                    }
                } else {
                    if(!is_numeric($tarif)) {
                        returnToListePrestations($error);
                    }
                }
            }
            
            //On recupere les inputs pour des nouvelles lignes de prestation seulement si on en a
            if($nbInfosTot != 0) {
                
                $libellesAdd = array();
                $type_tarifsAdd = array();
                $tarifsAdd = array();
                
                //On va parcourir les nouvelles lignes de prestation
                for($i=1; $i<=$nbInfosTot;$i++) {
                    //On verifie que la ligne n'a pas ete supprimée en dynamique
                    if(filter_input(INPUT_POST, 'supp' . $i) == NULL) {
                        $libellesAdd[$i] = "";
                        if (filter_input(INPUT_POST, 'libelle' . $i) != NULL) {
                            $libellesAdd[$i] = filter_input(INPUT_POST, 'libelle' . $i);
                        } else {
                        returnToListePrestations($error);
                        }

                        $type_tarifsAdd[$i] = "";
                        if (filter_input(INPUT_POST, 't_tarif' . $i) != NULL) {
                            $type_tarifsAdd[$i] = filter_input(INPUT_POST, 't_tarif' . $i);
                        } else {
                            returnToListePrestations($error);
                        }

                        $tarifsAdd[$i] = "";
                        if($type_tarifsAdd[$i] == 'F') {
                            if (filter_input(INPUT_POST, 'tarif_std' . $i) != NULL) {
                                $tarifsAdd[$i] = filter_input(INPUT_POST, 'tarif_std' . $i);
                            } else {
                                returnToListePrestations($error);
                            }
                        } else {
                            $tarifsAdd[$i] = array();
                            if (filter_input(INPUT_POST, 'tarif_jr' . $i) != NULL) {
                                array_push($tarifsAdd[$i], filter_input(INPUT_POST, 'tarif_jr' . $i));
                            } else {
                                returnToListePrestations($error);
                            }
                            if (filter_input(INPUT_POST, 'tarif_sr' . $i) != NULL) {
                                array_push($tarifsAdd[$i], filter_input(INPUT_POST, 'tarif_sr' . $i));
                            } else {
                                returnToListePrestations($error);
                            }
                            if (filter_input(INPUT_POST, 'tarif_mgr' . $i) != NULL) {
                                array_push($tarifsAdd[$i], filter_input(INPUT_POST, 'tarif_mgr' . $i));
                            } else {
                                returnToListePrestations($error);
                            }
                        }
                    }                
                }
                /* Verification des inputs */
                foreach ($libellesAdd as $lib) {
                    if($lib == "") {
                        returnToListePrestations($error);
                    }
                }

                foreach ($type_tarifsAdd as $t_tarif) {
                    if($t_tarif == "") {
                        returnToListePrestations($error);
                    }
                }

                foreach ($tarifsAdd as $tarif) {
                    if(is_array($tarif)) {
                        foreach ($tarif as $tarif_spec) {
                            if(!is_numeric($tarif_spec)) {
                                returnToListePrestations($error);
                            }
                        }
                    } else {
                        if(!is_numeric($tarif)) {
                            returnToListePrestations($error);
                        }
                    }
                }
            }
            
            /* Verification des inputs */
            if($prestation == "") {
                returnToListePrestations($error);
            }
                       
            //On recupere la date actuelle
            $date = date(date("Y-m-d H:i:s"));
            //On recupere l'utilisateur qui a fait l'ajout
            $user = "GLS";
            
            /* On update la partie general de la prestation */
            $update_gen_string = "UPDATE prestation " 
                    . "SET pres_moddate = :moddate, pres_moduser = :moduser, " 
                    . "pres_rf_nom = :nom_code, pres_type = :type, pres_prestation = :prestation, pres_repartition_cons = :repartition, " 
                    . "pres_rf_pay = :pays, pres_rf_typ_dossier = :type_dossier, pres_rf_typ_operation = :operation "
                    . "WHERE pres_id_general = :id_gen";
            
            $stmt_update_gen = $pdo->prepare($update_gen_string);
            
            //On lie les parametres recuperés via le formulaire pour les associer a la requete
            $stmt_update_gen->bindParam(':moddate', $date);
            $stmt_update_gen->bindParam(':moduser', $user);
            $stmt_update_gen->bindParam(':nom_code', $nom_code);
            $stmt_update_gen->bindParam(':type', $type_lib);
            $stmt_update_gen->bindParam(':prestation', $prestation);
            $stmt_update_gen->bindParam(':repartition', $repartition);
            $stmt_update_gen->bindParam(':pays', $pays);
            $stmt_update_gen->bindParam(':type_dossier', $type_dossier);
            $stmt_update_gen->bindParam(':operation', $operation);
            $stmt_update_gen->bindParam(':id_gen', $id_gen);
            
            $stmt_update_gen->execute();
            
            //On update les lignes de prestation
            $result_prestations->execute();
            //On parcours ces prestations pour pouvoir les recuperer
            foreach($result_prestations->fetchAll(PDO::FETCH_OBJ) as $pres_id) {
                $update_string = "UPDATE prestation " 
                        . "SET pres_libelle_ligne_fac = :libelle, pres_t_tarif = :t_tarif, pres_tarif_std = :tarif_std, " 
                        . "pres_tarif_jr = :tarif_jr, pres_tarif_sr = :tarif_sr, pres_tarif_mgr = :tarif_mgr "
                        . "WHERE pres_id = :pres_id";
                
                $stmt_update = $pdo->prepare($update_string);
                
                $libelle = $libellesUp[$pres_id->pres_id];
                $type_tarif = $type_tarifsUp[$pres_id->pres_id];
                if(is_array($tarifsUp[$pres_id->pres_id])) {
                    $tarif_std = 0;
                    $tarif_jr = $tarifsUp[$pres_id->pres_id][0];
                    $tarif_sr = $tarifsUp[$pres_id->pres_id][1];
                    $tarif_mgr = $tarifsUp[$pres_id->pres_id][2];
                } else {
                    $tarif_std = $tarifsUp[$pres_id->pres_id];
                    $tarif_jr = 0;
                    $tarif_sr = 0;
                    $tarif_mgr = 0;
                }

                //On lie les parametres recuperés via le formulaire pour les associer a la requete
                $stmt_update->bindParam(':libelle', $libelle);
                $stmt_update->bindParam(':t_tarif', $type_tarif);
                $stmt_update->bindParam(':tarif_std', $tarif_std);
                $stmt_update->bindParam(':tarif_jr', $tarif_jr);
                $stmt_update->bindParam(':tarif_sr', $tarif_sr);
                $stmt_update->bindParam(':tarif_mgr', $tarif_mgr);
                $stmt_update->bindParam(':pres_id', $pres_id->pres_id);
                
                //On execute la requete
                $stmt_update->execute(); 
            }
            
            //On essaie d'ajouter des lignes seulement si on en trouve
            if($nbInfosTot != 0) {
                /* On cree la requete pour ajouter les nouvelles lignes de prestation */
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
                for($i=1;$i<=$nbInfosTot;$i++){
                    if(filter_input(INPUT_POST, 'supp' . $i) == NULL) {
                        //On genere un id 
                        $id = generateId("PRE", "re", "prestation");
                        $libelle = $libellesAdd[$i];
                        $type_tarif = $type_tarifsAdd[$i];
                        if(is_array($tarifsAdd[$i])) {
                            $tarif_std = 0;
                            $tarif_jr = $tarifsAdd[$i][0];
                            $tarif_sr = $tarifsAdd[$i][1];
                            $tarif_mgr = $tarifsAdd[$i][2];
                        } else {
                            $tarif_std = $tarifsAdd[$i];
                            $tarif_jr = 0;
                            $tarif_sr = 0;
                            $tarif_mgr = 0;
                        }
                        //On execute la requete
                        $stmt_insert->execute(); 
                    }
                }
            }
            //On retourne a la page de la liste des prestations
            returnToListePrestations("");
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

/*****
 * returnToListePrestations : renvoie l'utilisateur sur la page de la liste des prestations
 * 
 * String p_error: message d'erreur a afficher quand l'utilisateur est renvoyé sur la page 
 ****/
function returnToListePrestations($p_error){
    echo "<script>";
    if($p_error != "") {
        echo "alert(\"" . $p_error . "\");";
    }
    echo "window.location.href='index.php?action=listePrestations';";
    echo "</script>";
    exit;
}