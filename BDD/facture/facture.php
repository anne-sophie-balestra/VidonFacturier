<?php
/********************************************
* facture.php                               *
* Gère les modifications dans la BD         *
* pour les factures                         *
*                                           *
* Auteurs : Anne-Sophie Balestra            *
*           Abdoul Wahab Haidara            *
*           Yvan-Christian Maso             *
*           Baptiste Quere                  *
*           Yoann Le Taillanter             *
*                                           *
* Date de creation : 10/04/2015             *
********************************************/

//Connexion a la base de données
$pdo = new SPDO();

/* On verifie l'action demandee */
if (filter_input(INPUT_GET, 'action') != NULL) {
    /* En fonction de la page passee en action, on se dirige vers la page correspondante */
    switch (filter_input(INPUT_GET, 'action')) {
        /* Ajout d'une nouvelle facture */
        case('insertFacture'):                        
            /* Erreur a retourner si besoin */
            $error = "Certains champs n'ont pas été remplis correctement. Merci de recommencer.";
            
            /* Recuperation des inputs avec verification de leur initialisation  
             * Si on trouve une erreur, on renvoie l'utilisateur sur la page de creation d'e prestation'une facture */
            
            /**
             * Infos GENERALES
             */
            
            $dossier = "";
            if (filter_input(INPUT_POST, 'dos_id') != NULL) {
                $dossier = filter_input(INPUT_POST, 'dos_id');
            } else {
                returnToCreateFacture($error . " : 1");
            }
            
            $entite = "";
            if (filter_input(INPUT_POST, 'ent_id') != NULL) {
                $entite = filter_input(INPUT_POST, 'ent_id');
            } else {
                returnToCreateFacture($error . " : 2");
            }
            
            $operation = "";
            if (filter_input(INPUT_POST, 'operation') != NULL) {
                $operation = filter_input(INPUT_POST, 'operation');
            } else {
                returnToCreateFacture($error . " : 3");
            }
            //On recupere le libelle de l'opération associée
            $stmt = "SELECT t_ope_libelle FROM type_operation WHERE t_ope_id = :operation";
            $result_operation = $pdo->prepare($stmt);
            $result_operation->bindParam(":operation", $operation);
            $result_operation->execute();
            $operationInfo = $result_operation->fetch(PDO::FETCH_OBJ);
            $operationLib = $operationInfo->t_ope_libelle;
                        
            $area = "";
            if (filter_input(INPUT_POST, 'area') != NULL) {
                $area = filter_input(INPUT_POST, 'area');
            } else {
                returnToCreateFacture($error . " : 4");
            }
             
            $nbConsultants = 0;
            $consultants = "";
            if (!is_array(filter_input(INPUT_POST, 'consultants'))) {
                foreach($_POST['consultants'] as $cons) {
                    $stmt_cons = "SELECT uti_initial FROM utilisateur WHERE uti_id = :id";
                    $result_cons = $pdo->prepare($stmt_cons);
                    $result_cons->bindParam(":id", $cons);
                    $result_cons->execute();
                    $consultant = $result_cons->fetch(PDO::FETCH_OBJ);
                    $consultants .= ($nbConsultants == 0 ? $consultant->uti_initial : "/" . $consultant->uti_initial);
                    $nbConsultants++;
                }
            } else {
                returnToCreateFacture($error . " : 5");
            }    
            $repartition = 0;
            if (filter_input(INPUT_POST, 'repartition') != NULL) {
                $repartition = filter_input(INPUT_POST, 'repartition');
            } else {
                returnToCreateFacture($error . " : 6");
            }
            
            $dateFacture = "";
            if (filter_input(INPUT_POST, 'dateFacture') != NULL) {
                $dateFacture = filter_input(INPUT_POST, 'dateFacture');
            } else {
                returnToCreateFacture($error . " : 7");
            }
            
            $dateEcheance = "";
            if (filter_input(INPUT_POST, 'dateEcheance') != NULL) {
                $dateEcheance = filter_input(INPUT_POST, 'dateEcheance');
            } else {
                returnToCreateFacture($error . " : 8");
            }
            
            $dateProforma = "";
            if (filter_input(INPUT_POST, 'dateProforma') != NULL) {
                $dateProforma = filter_input(INPUT_POST, 'dateProforma');
            } else {
                returnToCreateFacture($error . " : 9");
            }
            
            $typeFacture = "";
            if (filter_input(INPUT_POST, 'type_facture') != NULL) {
                $typeFacture = filter_input(INPUT_POST, 'type_facture');
            } else {
                returnToCreateFacture($error . " : 10");
            }
            
            $typeProforma = "";
            if (filter_input(INPUT_POST, 'type_proforma') != NULL) {
                $typeProforma = filter_input(INPUT_POST, 'type_proforma');
            } else {
                returnToCreateFacture($error . " : 11");
            }
            
            $objet = "";
            if (filter_input(INPUT_POST, 'objet') != NULL) {
                $objet = filter_input(INPUT_POST, 'objet');
            } else {
                returnToCreateFacture($error . " : 12");
            }
            
            $total_honos = 0;
            if (filter_input(INPUT_POST, 'total_honos') != NULL) {
                $total_honos = filter_input(INPUT_POST, 'total_honos');
            } else {
                returnToCreateFacture($error . " : 13");
            }
            
            $total_frais = 0;
            if (filter_input(INPUT_POST, 'total_frais') != NULL) {
                $total_frais = filter_input(INPUT_POST, 'total_frais');
            } else {
                returnToCreateFacture($error . " : 14");
            }
            
            $total_taxes = 0;
            if (filter_input(INPUT_POST, 'total_taxes') != NULL) {
                $total_taxes = filter_input(INPUT_POST, 'total_taxes');
            } else {
                returnToCreateFacture($error . " : 15");
            }
            
            $total_achats = 0;
            if (filter_input(INPUT_POST, 'total_achats') != NULL) {
                $total_achats = filter_input(INPUT_POST, 'total_achats');
            } else {
                returnToCreateFacture($error . " : 16");
            }
            
            $montantHT = 0;
            if (filter_input(INPUT_POST, 'montantht') != NULL) {
                $montantHT = filter_input(INPUT_POST, 'montantht');
            } else {
                returnToCreateFacture($error . " : 17");
            }
            
            $montantTTC = 0;
            if (filter_input(INPUT_POST, 'montantttc') != NULL) {
                $montantTTC = filter_input(INPUT_POST, 'montantttc');
            } else {
                returnToCreateFacture($error . " : 18");
            }
            
            /**
             * Partie REGLEMENTS
             */            
            $nbReglements = 0;
            if (filter_input(INPUT_POST, 'nbReglementsTot') != NULL) {
                $nbReglements = filter_input(INPUT_POST, 'nbReglementsTot');
            } else {
                returnToCreateFacture($error . " : 19");
            }
            
            //On parcours les reglements ajoutés pour les ajouter a un array
            $reglements = array();
            for($i=1;$i<=$nbReglements;$i++) {
                //Si la ligne n'a pas ete supprimée, on recupere les infos du reglements
                if(filter_input(INPUT_POST, 'suppReg' . $i) == NULL) {
                    //Date du reglement
                    $dateReg = "";
                    if (filter_input(INPUT_POST, 'dateReg' . $i) != NULL) {
                        $dateReg = filter_input(INPUT_POST, 'dateReg' . $i);
                    } else {
                        returnToCreateFacture($error . " : 20");
                    }
                    //Devise du reglement
                    $deviseReg = "";
                    if (filter_input(INPUT_POST, 'deviseReg' . $i) != NULL) {
                        $deviseReg = filter_input(INPUT_POST, 'deviseReg' . $i);
                    } else {
                        returnToCreateFacture($error . " : 21");
                    }                  
                    //Montant dans la devise choisie
                    $montantReg = "";
                    if (filter_input(INPUT_POST, 'montantReg' . $i) != NULL) {
                        $montantReg = filter_input(INPUT_POST, 'montantReg' . $i);
                    } else {
                        returnToCreateFacture($error . " : 22");
                    }                  
                    //Montant en euro
                    $montantEuroReg = "";
                    if (filter_input(INPUT_POST, 'montantEuroReg' . $i) != NULL) {
                        $montantEuroReg = filter_input(INPUT_POST, 'montantEuroReg' . $i);
                    } else {
                        returnToCreateFacture($error . " : 23");
                    }  
                    
                    //On cree l'array correspondant au reglement, pour ensuite l'inserer dans la liste des reglements
                    $reglement = array();
                    $reglement['date'] = $dateReg;
                    $reglement['devise'] = $deviseReg;
                    $reglement['montant'] = $montantReg;
                    $reglement['montantEuro'] = $montantEuroReg;
                    $reglements[$i] = $reglement;
                }
            }
            
            /**
             * Partie LIGNES DE FACTURES
             */         
            $nbLignesFac = 0;
            if (filter_input(INPUT_POST, 'nbLignesFacTot') != NULL) {
                $nbLignesFac = filter_input(INPUT_POST, 'nbLignesFacTot');
            } else {
                returnToCreateFacture($error . " : 24");
            }
            
            //On parcours les lignes de facture ajoutées pour les ajouter a un array
            $lignesFacture = array();
            for($i=1;$i<=$nbLignesFac;$i++) {
                //Si la ligne n'a pas ete supprimée, on recupere les infos de la ligne de facture
                if(filter_input(INPUT_POST, 'suppLigne' . $i) == NULL) {                    
                    //Code de nomenclature
                    $codeLigne = "";
                    if (filter_input(INPUT_POST, 'codeLigne' . $i) != NULL) {
                        $codeLigne = filter_input(INPUT_POST, 'codeLigne' . $i);
                    } else {
                        returnToCreateFacture($error . " : 25");
                    }
                    //Libelle de la ligne de facture
                    $libelleLigne = "";
                    if (filter_input(INPUT_POST, 'libelleLigne' . $i) != NULL) {
                        $libelleLigne = filter_input(INPUT_POST, 'libelleLigne' . $i);
                    } else {
                        returnToCreateFacture($error . " : 26");
                    }
                    //type de la ligne de facture (honos, frais ou taxes)
                    $typeLigne = "";
                    if (filter_input(INPUT_POST, 'typeLigne' . $i) != NULL) {
                        $typeLigne = filter_input(INPUT_POST, 'typeLigne' . $i);
                    } else {
                        returnToCreateFacture($error . " : 27");
                    }
                    //On renomme le type de la ligne en honos, frais ou taxes (au lieu de H, F et T)
                    $typeLibLigne = "";
                    switch($typeLigne) {
                        case 'H': $typeLibLigne = "honos"; break;
                        case 'F': $typeLibLigne = "frais"; break;
                        case 'T': $typeLibLigne = "taxes"; break;
                    }
                    //pourcentage de TVA de la ligne de facture
                    $tvaLigne = 0;
                    if (filter_input(INPUT_POST, 'tvaLigne' . $i) != NULL) {
                        $tvaLigne = filter_input(INPUT_POST, 'tvaLigne' . $i);
                    } else {
                        returnToCreateFacture($error . " : 28");
                    }
                    //tarif de la ligne de facture
                    $tarifLigne = 0;
                    if (filter_input(INPUT_POST, 'tarifLigne' . $i) != NULL) {
                        $tarifLigne = filter_input(INPUT_POST, 'tarifLigne' . $i);
                    } else {
                        returnToCreateFacture($error . " : 29");
                    }
                    //nombre de prestations demandées
                    $quantiteLigne = 0;
                    if (filter_input(INPUT_POST, 'quantiteLigne' . $i) != NULL) {
                        $quantiteLigne = filter_input(INPUT_POST, 'quantiteLigne' . $i);
                    } else {
                        returnToCreateFacture($error . " : 30");
                    }
                    //montant total de la ligne de facture
                    $totalLigne = 0;
                    if (filter_input(INPUT_POST, 'totalLigne' . $i) != NULL) {
                        $totalLigne = filter_input(INPUT_POST, 'totalLigne' . $i);
                    } else {
                        returnToCreateFacture($error . " : 31");
                    }
                    
                    //On cree un array qui va contenir les infos de la ligne et qu'on ajoutera a la liste des lignes de facture
                    $ligneFacture = array();
                    $ligneFacture['code'] = $codeLigne;
                    $ligneFacture['libelle'] = $libelleLigne;
                    $ligneFacture['type'] = $typeLibLigne;
                    $ligneFacture['tva'] = $tvaLigne;
                    $ligneFacture['tarif'] = $tarifLigne;
                    $ligneFacture['quantite'] = $quantiteLigne;
                    $ligneFacture['total'] = $totalLigne;
                    $lignesFacture[$i] = $ligneFacture;
                }
            }
            
            /**
             * Partie ACHATS
             */
            $nbAchats = 0;
            if (filter_input(INPUT_POST, 'nbAchatsTot') != NULL) {
                $nbAchats = filter_input(INPUT_POST, 'nbAchatsTot');
            } else {
                returnToCreateFacture($error . " : 32");
            }
            
            //On parcours les achats ajoutés pour les ajouter a un array
            $achats = array();
            for($i=1;$i<=$nbAchats;$i++) {
                //Si l'achat n'a pas ete supprimé, on recupere les infos de l'achat
                if(filter_input(INPUT_POST, 'suppAchat' . $i) == NULL) {       
                    //Code de nomenclature
                    $codeAchat = "";
                    if (filter_input(INPUT_POST, 'codeAchat' . $i) != NULL) {
                        $codeAchat = filter_input(INPUT_POST, 'codeAchat' . $i);
                    } else {
                        returnToCreateFacture($error . " : 33");
                    }                    
                    //Libelle de l'achat
                    $libelleAchat = "";
                    if (filter_input(INPUT_POST, 'libelleAchat' . $i) != NULL) {
                        $libelleAchat = filter_input(INPUT_POST, 'libelleAchat' . $i);
                    } else {
                        returnToCreateFacture($error . " : 34");
                    }                    
                    //Consultant de l'achat
                    $cpvAchat = "";
                    if (filter_input(INPUT_POST, 'cpvAchat' . $i) != NULL) {
                        $cons = filter_input(INPUT_POST, 'cpvAchat' . $i);
                        $result_cons->execute();
                        $consultant = $result_cons->fetch(PDO::FETCH_OBJ);
                        $cpvAchat = $consultant->uti_initial;
                    } else {
                        returnToCreateFacture($error . " : 35");
                    }                    
                    //True si l'achat est en litige
                    $litigeAchat = false;
                    if (filter_input(INPUT_POST, 'litigeAchat' . $i) != NULL) {
                        $litigeAchat = (filter_input(INPUT_POST, 'litigeAchat' . $i) == 'true' ? true : false);
                    } else {
                        returnToCreateFacture($error . " : 36");
                    }                    
                    //True si l'achat est en complément
                    $complementAchat = false;
                    if (filter_input(INPUT_POST, 'complementAchat' . $i) != NULL) {
                        $complementAchat = (filter_input(INPUT_POST, 'complementAchat' . $i) == 'true' ? true : false);
                    } else {
                        returnToCreateFacture($error . " : 37");
                    }                    
                    //Founisseur de l'achat
                    $fournisseurAchat = "";
                    if (filter_input(INPUT_POST, 'fournisseurAchat' . $i) != NULL) {
                        $fournisseurAchat = filter_input(INPUT_POST, 'fournisseurAchat' . $i);
                    } else {
                        returnToCreateFacture($error . " : 38");
                    }                    
                    //Devise pour l'achat
                    $deviseAchat = "";
                    if (filter_input(INPUT_POST, 'deviseAchat' . $i) != NULL) {
                        $deviseAchat = filter_input(INPUT_POST, 'deviseAchat' . $i);
                    } else {
                        returnToCreateFacture($error . " : 39");
                    }                    
                    //Taux appliqué a la devise pour l'achat
                    $tauxAchat = "";
                    if (filter_input(INPUT_POST, 'tauxAchat' . $i) != NULL) {
                        $tauxAchat = filter_input(INPUT_POST, 'tauxAchat' . $i);
                    } else {
                        returnToCreateFacture($error . " : 40");
                    }  
                    //Nombre de prestations pour cet achat
                    $quantiteAchat = 0;
                    if (filter_input(INPUT_POST, 'quantiteAchat' . $i) != NULL) {
                        $quantiteAchat = filter_input(INPUT_POST, 'quantiteAchat' . $i);
                    } else {
                        returnToCreateFacture($error . " : 41");
                    }  
                    //Montant unitaire provisionnel ou non
                    $prixachatunitprov = 0;
                    $prixachatunit = 0;
                    if (filter_input(INPUT_POST, 'tarif_uAchat' . $i) != NULL) {
                        $prixachatunitprov = (filter_input(INPUT_POST, 'provAchat' . $i) == 'true' ? filter_input(INPUT_POST, 'tarif_uAchat' . $i) : 0);
                        $prixachatunit = filter_input(INPUT_POST, 'tarif_uAchat' . $i);
                    } else {
                        returnToCreateFacture($error . " : 42");
                    }  
                    //Montant de revente de l'achat unitaire
                    $prixrevente = 0;
                    if (filter_input(INPUT_POST, 'tarifReventeAchat' . $i) != NULL) {
                        $prixrevente= filter_input(INPUT_POST, 'tarifReventeAchat' . $i);
                    } else {
                        returnToCreateFacture($error . " : 43");
                    }  
                    //Numero de la facture fournisseur
                    $numffoAchat = "";
                    if (filter_input(INPUT_POST, 'num_ffoAchat' . $i) != NULL) {
                        $numffoAchat = filter_input(INPUT_POST, 'num_ffoAchat' . $i);
                    } else {
                        returnToCreateFacture($error . " : 44");
                    }  
                    //Date echeance de l'achat
                    $echeanceAchat = "";
                    if (filter_input(INPUT_POST, 'dateEcheanceAchat' . $i) != NULL) {
                        $echeanceAchat = filter_input(INPUT_POST, 'dateEcheanceAchat' . $i);
                    } else {
                        returnToCreateFacture($error . " : 45");
                    }  
                    //Date de prefecturation de l'achat
                    $prefacturationAchat = "";
                    if (filter_input(INPUT_POST, 'datePrefacturationAchat' . $i) != NULL) {
                        $prefacturationAchat = filter_input(INPUT_POST, 'datePrefacturationAchat' . $i);
                    } else {
                        returnToCreateFacture($error . " : 45");
                    }  
                    //Date de reglement de l'achat
                    $reglementAchat = "";
                    if (filter_input(INPUT_POST, 'dateReglementAchat' . $i) != NULL) {
                        $reglementAchat = filter_input(INPUT_POST, 'dateReglementAchat' . $i);
                    }
                    //Achat retrocede ou non
                    $visaAchat = false;
                    if (filter_input(INPUT_POST, 'dateEcheanceAchat' . $i) != NULL) {
                        $visaAchat = (filter_input(INPUT_POST, 'visaAchat' . $i) == "true" ? true : false);
                    } 
                    //BAP
                    $bapAchat = false;
                    if (filter_input(INPUT_POST, 'bapAchat' . $i) != NULL) {
                        $bapAchat = (filter_input(INPUT_POST, 'bapAchat' . $i) == "true" ? true : false);
                    } 
                    //BAP user
                    $quibapAchat = false;
                    if (filter_input(INPUT_POST, 'bap_cpvAchat' . $i) != NULL) {
                        $quibapAchat = filter_input(INPUT_POST, 'bap_cpvAchat' . $i);
                    }
                    //BAP date
                    $datebapAchat = false;
                    if (filter_input(INPUT_POST, 'bap_dateAchat' . $i) != NULL) {
                        $datebapAchat = filter_input(INPUT_POST, 'bap_dateAchat' . $i);
                    }
                    
                    //On cree un array qui va contenir les infos de l'achat et qu'on ajoutera a la liste des achats
                    $achat = array();
                    $achat['code'] = $codeAchat;
                    $achat['quantite'] = $quantiteAchat;
                    $achat['prestataire'] = $fournisseurAchat;
                    $achat['libelle'] = $libelleAchat;
                    $achat['devise'] = $deviseAchat;
                    $achat['prixachatunit'] = $prixachatunit;
                    $achat['prixrevente'] = $prixrevente;
                    $achat['prixachatunitprov'] = $prixachatunitprov;
                    $achat['cpv'] = $cpvAchat;
                    $achat['litige'] = $litigeAchat;
                    $achat['complement'] = $complementAchat;
                    $achat['numffo'] = $numffoAchat;
                    $achat['echeance'] = $echeanceAchat;
                    $achat['prefacturation'] = $prefacturationAchat;
                    $achat['reglement'] = $reglementAchat;
                    $achat['tauxdevise'] = $tauxAchat;
                    $achat['retrocede'] = $visaAchat;
                    $achat['bap'] = $bapAchat;
                    $achat['quibap'] = $quibapAchat;
                    $achat['datebap'] = $datebapAchat;
                    $achats[$i] = $achat;      
                }
            }
            
            //On recupere la date actuelle
            $date = date(date("Y-m-d H:i:s"));
            //On prend le user courant
            $user = "GLS";
            
            /**
             * Creation de l'OPERATION de la facture
             */
            $insert_operation = "INSERT INTO operation " 
                    . "(ope_id, ope_creadate, ope_moddate, ope_creauser, ope_moduser, " 
                    . "ope_titre, ope_datecloture, ope_open, ope_rf_fac, " 
                    . "ope_rf_dos, ope_num, ope_flag, ope_usercloture, ope_type) VALUES "
                    . "(:id, :creadate, :moddate, :creauser, :moduser, " 
                    . ":titre, :datecloture, :open, :facture, :dossier, :num, " 
                    . ":flag, :usercloture, :type)";
            $stmt_operation = $pdo->prepare($insert_operation);
            //on genere l'id de l'operation
            $idOpe = generateId("OPE", "re", "operation");
            
            //On lie les parametres recuperés via le formulaire pour les associer a la requete
            $stmt_operation->bindParam(':id', $idOpe);
            $stmt_operation->bindParam(':creadate', $date);
            $stmt_operation->bindParam(':moddate', $date);
            $stmt_operation->bindParam(':creauser', $user);
            $stmt_operation->bindParam(':moduser', $user);
            $titre = "Titre " . $idOpe;
            $stmt_operation->bindParam(':titre', $titre);
            $stmt_operation->bindValue(':datecloture', NULL);
            $stmt_operation->bindValue(':open', 'TRUE');
            $stmt_operation->bindValue(':facture', NULL);
            $stmt_operation->bindParam(':dossier', $dossier);
            $stmt_operation->bindValue(':num', 1);
            $stmt_operation->bindValue(':flag', 'FALSE');
            $stmt_operation->bindValue(':usercloture', NULL);
            $stmt_operation->bindParam(':type', $operationLib);
            
            $stmt_operation->execute();
            
            /**
             * Creation de la FACTURE
             */            
            /* Creation des requetes d'insertions et ajout dans la base */
            $insert_facture = "INSERT INTO facture " 
                    . "(fac_id, fac_creadate, fac_moddate, fac_creauser, fac_moduser, " 
                    . "fac_type, fac_rf_dos, fac_rf_ent, fac_objet, fac_date, fac_echeance, fac_impression, " 
                    . "fac_export, fac_num, fac_status, fac_rf_ope, " 
                    . "fac_honoraires, fac_retro, fac_taxes, fac_frais, " 
                    . "fac_montantht, fac_devise, fac_tauxdevise, fac_tauxtva, fac_tva, "
                    . "fac_montantht_dev, fac_datepaiement, fac_responsable, fac_langue, fac_proforma, "
                    . "fac_group, fac_reference, fac_dateproforma, fac_rf_adr, fac_restantdu, "
                    . "fac_mntttc, fac_fretr, fac_pole) VALUES "
                    . "(:id, :creadate, :moddate, :creauser, :moduser, " 
                    . ":type, :dossier, :entite, :objet, :date, :echeance, :impression, " 
                    . ":export, :num, :status, :operation, " 
                    . ":honos, :retro, :taxes, :frais, " 
                    . ":montantht, :devise, :tauxdevise, :tauxtva, :tva, " 
                    . ":montantht_dev, :datepaiement, :responsable, :langue, :proforma, " 
                    . ":groupe, :reference, :dateproforma, :adresse, :restantdu, :mntttc, :fretr, :pole)";
            $stmt_facture = $pdo->prepare($insert_facture);
            //On genere un id, un num de facture et un num de proforma
            $idFac = generateId("FAC", "re", "facture");
            $num = generateId("", "", "numfacture12014");
            $proforma = generateId("", "", "proforma");
            
            //On lie les parametres recuperés via le formulaire pour les associer a la requete
            $stmt_facture->bindParam(':id', $idFac);
            $stmt_facture->bindParam(':creadate', $date);
            $stmt_facture->bindParam(':moddate', $date);
            $stmt_facture->bindParam(':creauser', $user);
            $stmt_facture->bindParam(':moduser', $user);
            $stmt_facture->bindParam(':type', $typeFacture);
            $stmt_facture->bindParam(':dossier', $dossier);
            $stmt_facture->bindParam(':entite', $entite);
            $stmt_facture->bindParam(':objet', $objet);
            $stmt_facture->bindParam(':date', $dateFacture);
            $stmt_facture->bindParam(':echeance', $dateEcheance);
            $stmt_facture->bindValue(':impression', NULL);
            $stmt_facture->bindValue(':export', NULL);
            $stmt_facture->bindParam(':num', $num);
            $stmt_facture->bindValue(':status', $typeProforma);
            $stmt_facture->bindParam(':operation', $idOpe);
            $stmt_facture->bindParam(':honos', $total_honos);
            $stmt_facture->bindParam(':retro', $total_achats);
            $stmt_facture->bindParam(':taxes', $total_taxes);
            $stmt_facture->bindParam(':frais', $total_frais);
            $stmt_facture->bindParam(':montantht', $montantHT);
            $stmt_facture->bindValue(':devise', "EUR");
            $stmt_facture->bindValue(':tauxdevise', 1);
            $stmt_facture->bindValue(':tauxtva', 20);
            $stmt_facture->bindValue(':tva', 0);
            $stmt_facture->bindParam(':montantht_dev', $montantHT);
            $stmt_facture->bindValue(':datepaiement', NULL);
            $stmt_facture->bindParam(':responsable', $user);
            $stmt_facture->bindValue(':langue', NULL);
            $stmt_facture->bindParam(':proforma', $proforma);
            $stmt_facture->bindValue(':groupe', NULL);
            $stmt_facture->bindValue(':reference', NULL);
            $stmt_facture->bindParam(':dateproforma', $dateProforma);
            $stmt_facture->bindValue(':adresse', NULL);
            $stmt_facture->bindParam(':restantdu', $montantTTC);
            $stmt_facture->bindParam(':mntttc', $montantTTC);
            $stmt_facture->bindValue(':fretr', NULL);
            $stmt_facture->bindValue(':pole', NULL);
            
            $stmt_facture->execute();
            
            /**
             * Creation des REGLEMENTS de la facture
             */
            $insert_reglement = "INSERT INTO reglement " 
                    . "(reg_id, reg_creadate, reg_moddate, reg_creauser, reg_moduser, " 
                    . "reg_rf_fac, reg_date, reg_montant, reg_devise, reg_references) VALUES "
                    . "(:id, :creadate, :moddate, :creauser, :moduser, " 
                    . ":facture, :date, :montant, :devise, :references)";
            $stmt_reglement = $pdo->prepare($insert_reglement);
            
            //On lie les parametres recuperés via le formulaire pour les associer a la requete
            $stmt_reglement->bindParam(':id', $idReg);
            $stmt_reglement->bindParam(':creadate', $date);
            $stmt_reglement->bindParam(':moddate', $date);
            $stmt_reglement->bindParam(':creauser', $user);
            $stmt_reglement->bindParam(':moduser', $user);
            $stmt_reglement->bindParam(':facture', $idFac);
            $stmt_reglement->bindParam(':date', $dateReglement);
            $stmt_reglement->bindParam(':montant', $montantReglement);
            $stmt_reglement->bindParam(':devise', $deviseReglement);
            $stmt_reglement->bindValue(':references', NULL);
            
            //Pour les parametres qui changent en fonction du rerglements, on les parcourt
            for($i=1;$i<=$nbReglements;$i++){
                if(filter_input(INPUT_POST, 'supp' . $i) == NULL) {
                    //on genere l'id du reglement
                    $idReg = generateId("REG", "re", "reglement");
                    if(is_array($reglements[$i])) {
                        $dateReglement = $reglements[$i]['date'];
                        $montantReglement = $reglements[$i]['montantEuro'];
                        $deviseReglement = $reglements[$i]['devise'];
                    }
                    //On execute la requete
                    $stmt_reglement->execute();
                }
            }
            
            /**
             * Creation des LIGNES DE FACTURE
             */
            $insert_ligne = "INSERT INTO lignefacture " 
                    . "(lig_id, lig_creadate, lig_moddate, lig_creauser, lig_moduser, " 
                    . "lig_rubrique, lig_code, lig_libelle, lig_rf_fac, lig_tauxtva, lig_tva, "
                    . "lig_total_dev, lig_montant, lig_nb, lig_total, "
                    . "lig_rf_act, lig_rang, lig_typeligne) VALUES "
                    . "(:id, :creadate, :moddate, :creauser, :moduser, " 
                    . ":rubrique, :code, :libelle, :facture, :tauxtva, "
                    . ":tva, :totaldev, :montant, :nb, :total, :act, :rang, :type)";
            $stmt_ligne = $pdo->prepare($insert_ligne);
            
            //On lie les parametres recuperés via le formulaire pour les associer a la requete
            $stmt_ligne->bindParam(':id', $idLig);
            $stmt_ligne->bindParam(':creadate', $date);
            $stmt_ligne->bindParam(':moddate', $date);
            $stmt_ligne->bindParam(':creauser', $user);
            $stmt_ligne->bindParam(':moduser', $user);
            $stmt_ligne->bindValue(':rubrique', "facture");
            $stmt_ligne->bindParam(':code', $code);
            $stmt_ligne->bindParam(':libelle', $libelle);
            $stmt_ligne->bindParam(':facture', $idFac);
            $stmt_ligne->bindParam(':tauxtva', $tauxtva);
            $stmt_ligne->bindParam(':tva', $tva);
            $stmt_ligne->bindParam(':totaldev', $totaldev);
            $stmt_ligne->bindParam(':montant', $montant);
            $stmt_ligne->bindParam(':nb', $nb);
            $stmt_ligne->bindParam(':total', $total);
            $stmt_ligne->bindValue(':act', NULL);
            $stmt_ligne->bindValue(':rang', 0);
            $stmt_ligne->bindParam(':type', $type);
            
            //Pour les parametres qui changent en fonction de la ligne de facture, on les parcourt
            for($i=1;$i<=$nbLignesFac;$i++){
                if(filter_input(INPUT_POST, 'supp' . $i) == NULL) {
                    //on genere l'id de la ligne de facture
                    $idLig = generateId("LIG", "re", "lignefacture");
                    if(is_array($lignesFacture[$i])) {
                        $code = $lignesFacture[$i]['code'];
                        $libelle = $lignesFacture[$i]['libelle'];
                        $tauxtva = $lignesFacture[$i]['tva'];
                        $totaldev = $lignesFacture[$i]['total'];
                        $montant = $lignesFacture[$i]['tarif'];
                        $tva = $montant*$tauxtva;
                        $nb = $lignesFacture[$i]['quantite'];
                        $total = $lignesFacture[$i]['total'];
                        $type = $lignesFacture[$i]['type'];
                    }
                    //On execute la requete
                    $stmt_ligne->execute();
                }
            }
            
            /**
             * Creation des ACHATS
             */
            $insert_achat = "INSERT INTO achat " 
                    . "(ach_id, ach_creadate, ach_moddate, ach_creauser, ach_moduser, " 
                    . "ach_rf_dos, ach_rf_nom, ach_nb, ach_rf_ent, ach_prestairenom, "
                    . "ach_libelle, ach_devise, ach_taxe, ach_prixachatunit, ach_prixrevente, "
                    . "ach_prixachatunitprov, ach_cpv, ach_enlitige, ach_complementfact, ach_numffo, "
                    . "ach_tauxdevise_sav, ach_dprefacturation, ach_dreglement, "
                    . "ach_echeance, ach_remarque, "
                    . "ach_tauxdevise, ach_retrocede, ach_rf_fac, ach_bap, ach_quibap, ach_datebap) VALUES "
                    . "(:id, :creadate, :moddate, :creauser, :moduser, " 
                    . ":dossier, :code, :nb, :entite, :prestataire, "
                    . ":libelle, :devise, :taxe, :prixachatunit, :prixrevente, :prixachatunitprov, "
                    . ":cpv, :litige, :complement, :numffo, :tauxdevise, "
                    . ":prefacturation, :reglement, :echeance, :remarque, "
                    . ":tauxdevise, :retrocede, :facture, :bap, :quibap, :datebap)";
            $stmt_achat = $pdo->prepare($insert_achat);
            
            //On lie les parametres recuperés via le formulaire pour les associer a la requete
            $stmt_achat->bindParam(':id', $idAch);
            $stmt_achat->bindParam(':creadate', $date);
            $stmt_achat->bindParam(':moddate', $date);
            $stmt_achat->bindParam(':creauser', $user);
            $stmt_achat->bindParam(':moduser', $user);
            $stmt_achat->bindParam(':dossier', $dossier);
            $stmt_achat->bindParam(':code', $code);
            $stmt_achat->bindParam(':nb', $nb);
            $stmt_achat->bindParam(':entite', $entite);
            $stmt_achat->bindParam(':prestataire', $prestataire);
            $stmt_achat->bindParam(':libelle', $libelle);
            $stmt_achat->bindParam(':devise', $devise);
            $stmt_achat->bindValue(':taxe', 0);
            $stmt_achat->bindParam(':prixachatunit', $prixachatunit);
            $stmt_achat->bindParam(':prixrevente', $prixrevente);
            $stmt_achat->bindParam(':prixachatunitprov', $prixachatunitprov);
            $stmt_achat->bindParam(':cpv', $cpv);
            $stmt_achat->bindParam(':litige', $litige);
            $stmt_achat->bindParam(':complement', $complement);
            $stmt_achat->bindParam(':numffo', $numffo);
            $stmt_achat->bindParam(':tauxdevise', $tauxdevise);
            $stmt_achat->bindParam(':prefacturation', $prefacturation);
            $stmt_achat->bindParam(':reglement', $reglementInsert);
//            $stmt_achat->bindValue(':reglement', NULL);
            $stmt_achat->bindParam(':echeance', $echeance);
            $stmt_achat->bindValue(':remarque', "");
            $stmt_achat->bindParam(':tauxdevise', $tauxdevise);
            $stmt_achat->bindParam(':retrocede', $retrocede);
            $stmt_achat->bindParam(':facture', $idFac);
            $stmt_achat->bindParam(':bap', $bap);
            $stmt_achat->bindParam(':quibap', $quibap);
            $stmt_achat->bindParam(':datebap', $datebap);
            
            //Pour les parametres qui changent en fonction de l'achat, on les parcourt
            for($i=1;$i<=$nbAchats;$i++){
                if(filter_input(INPUT_POST, 'supp' . $i) == NULL) {
                    //on genere l'id de l'achat
                    $idAch = generateId("ACH", "re", "achat");
                    if(is_array($achats[$i])) {
                        var_dump($achats[$i]);
                        $code = $achats[$i]['code'];
                        $nb = $achats[$i]['quantite'];
                        $prestataire = $achats[$i]['prestataire'];
                        $libelle = $achats[$i]['libelle'];
                        $devise = $achats[$i]['devise'];
                        $prixachatunit = $achats[$i]['prixachatunit'];
                        $prixrevente = $achats[$i]['prixrevente'];
                        $prixachatunitprov = $achats[$i]['prixachatunitprov'];
                        $cpv = $achats[$i]['cpv'];
                        $litige = $achats[$i]['litige'];
                        $complement = $achats[$i]['complement'];
                        $numffo = $achats[$i]['numffo'];
                        $echeance = $achats[$i]['echeance'];
                        $prefacturation = $achats[$i]['prefacturation'];
                        $reglementInsert = (isset($achats[$i]['reglement']) ? $achats[$i]['reglement'] : '0000-00-00');
                        $tauxdevise = $achats[$i]['tauxdevise'];
                        $retrocede = $achats[$i]['retrocede'];
                        $bap = $achats[$i]['bap'];
                        $quibap = $achats[$i]['quibap'];
                        $datebap = (isset($achats[$i]['datebap']) ? $achats[$i]['datebap'] : '0000-00-00');
                    }
                    //On execute la requete
                    //$stmt_achat->execute();
                }
            }
            returnToListIndiv();
            break;
    }
}

/*****
 * returnToCreateFacture : renvoie l'utilisateur sur la page de creation d'une facture s'il a mal rempli le formulaire
 * 
 * String p_error: message d'erreur a afficher quand l'utilisateur est renvoyé sur la page 
 ***/
function returnToCreateFacture($p_error){
    //window.location.href='index.php?action=createFacture';
    echo "<script>alert(\"" . $p_error . "\");</script>";
    exit;
}
