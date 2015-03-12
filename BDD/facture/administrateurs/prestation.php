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

$pdo = new SPDO();

/* On verifie l'action demandee */
if (filter_input(INPUT_GET, 'action') != NULL) {
    /* En fonction de la page passee en action, on se dirige vers la page correspondante */
    switch (filter_input(INPUT_GET, 'action')) {
        /* Ajout d'une nouvelle prestation */
        case('insertPrestation'):
            var_dump($_POST);
            /* Recuperation des inputs */
            $all = array();
            $operation = filter_input(INPUT_POST, 'operation');
            $all['operation'] = $operation;
            $ent_dossier = filter_input(INPUT_POST, 'ent_dossier');
            $all['ent_dossier'] = $ent_dossier;
            $type_dossier = filter_input(INPUT_POST, 'type_dossier');
            $all['type_dossier'] = $type_dossier;
            $nom_code = filter_input(INPUT_POST, 'nom_code');
            $all['nom_code'] = $nom_code;
            $pays = filter_input(INPUT_POST, 'pays');
            $all['pays'] = $pays;
            $prestation = filter_input(INPUT_POST, 'prestation');
            $all['prestation'] = $prestation;
            $nbInfos = filter_input(INPUT_POST, 'nbInfos');
            $all['nbInfos'] = $nbInfos;
            $libelles = array();
            $type_tarifs = array();
            $tarifs = array();
            $repartitions = array();
            for($i=1; $i<=$nbInfos;$i++) {
                $libelles[$i] = filter_input(INPUT_POST, 'libelle' . $i);
                $type_tarifs[$i] = filter_input(INPUT_POST, 't_tarif' . $i);
                if($type_tarifs[$i] == 'F') {
                    $tarifs[$i] = filter_input(INPUT_POST, 'tarif' . $i);
                } else {
                    $tarifs[$i] = array();
                    array_push($tarifs[$i], filter_input(INPUT_POST, 'tarif_jr' . $i));
                    array_push($tarifs[$i], filter_input(INPUT_POST, 'tarif_sr' . $i));
                    array_push($tarifs[$i], filter_input(INPUT_POST, 'tarif_mgr' . $i));
                }                    
                $repartitions[$i] = filter_input(INPUT_POST, 'repartition' . $i);
            }
            $all['libelles'] = $libelles;
            $all['type_tarifs'] = $type_tarifs;
            $all['tarifs'] = $tarifs;
            $all['repartitions'] = $repartitions;

            var_dump($all);
            /* Verification des inputs */
            /* Creation des requetes d'insertions */
            /* Ajout dans la base */
            break;
    }
}