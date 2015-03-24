<?php
/********************************************
* updatePrestation.php                      *
* Formulaire de modification d'une presta   *
*                                           *
* Auteurs : Anne-Sophie Balestra            *
*           Abdoul Wahab Haidara            *
*           Yvan-Christian Maso             *
*           Baptiste Quere                  *
*           Yoann Le Taillanter             *
*                                           *
* Date de creation : 23/03/2015             *
********************************************/

// Connexion a la base de donnees
$pdo = new SPDO();

//On recupere les differentes operations disponibles
$stmt_ope = "SELECT t_ope_id, t_ope_libelle FROM type_operation";
$result_ope = $pdo->prepare($stmt_ope);
$result_ope->execute();
