<?php
/********************************************
* utiles.php                                *
* fonctions utiles partout                  *
*                                           *
* Auteurs : Anne-Sophie Balestra            *
*           Abdoul Wahab Haidara            *
*           Yvan-Christian Maso             *
*           Baptiste Quere                  *
*           Yoann Le Taillanter             *
*                                           *
* Date de creation : 13/03/2015             *
********************************************/

require_once("BDD/SPDO.php");

/*****
 * generateId : permet de creer un ID en 11 caracteres 
 * 
 * String p_key: 3 premieres lettres de la table
 * String p_reg: region (re pour rennes, na pour nantes...)
 * String p_table: nom de la table pour laquelle il faut générer un id
 ***/
function generateId($p_key, $p_reg, $p_table) {
    /* Connexion a la base de donnees */
    $pdo = new SPDO();
    
    /* On cherche dans la table sequence si notre table est referencee */ 
    $stmt_sequence = $pdo->prepare("SELECT nextval FROM sequence WHERE clef = :key");
    $stmt_sequence->bindParam(":key", $p_table);
    $stmt_sequence->execute();
    $newnum = "";
    //Si nous avons trouvé notre table
    if($stmt_sequence->rowCount() != 0) {
        //On va chercher la ligne dans la base de donnees
        $sequence = $stmt_sequence->fetch(PDO::FETCH_OBJ);
        //On recupere la valeur de nextval pour l'incrementer afin de creer notre nouvelle valeur
        $oldnum = base_convert($sequence->nextval, 36, 10);
        $newnum = substr("000000".base_convert(($oldnum+1), 10, 36),-6,6);
        //On modifie notre table sequence afin de lui donner le nouveau numero que nous venons de generer
        $stmt_update = $pdo->prepare("UPDATE sequence SET nextval = :next WHERE clef = :key");
        $stmt_update->bindParam(":next", $newnum);
        $stmt_update->bindParam(":key", $p_table);
        $stmt_update->execute();
    }
    // si nous n'avons pas trouvé notre table
    else {
        //on cree un num pour le nouvel id
        $newnum = "000001";
        //On l'insere dans la table sequence avec le nom de la table
        $stmt_insert = $pdo->prepare("INSERT INTO sequence (clef, nextval) VALUES (:key, :next)");
        $stmt_insert->bindParam(":key", $p_table);
        $stmt_insert->bindParam(":next", $newnum);
        $stmt_insert->execute();
    }
    //on retourne l'id du style TABre000001
    return $p_key . $p_reg . $newnum;
}