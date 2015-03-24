<?php
require_once "BDD/SPDO.php";
/********************************************
affiche tous les elements du dosssier       *
*                                                                                        *
* Auteurs : Anne-Sophie Balestra            *
*           Abdoul Wahab Haidara            *
*           Yvan-Christian Maso             *
*           Baptiste Quere                  *
*           Yoann Le Taillanter             *
*                                           *
* Date de creation : 23/03/2015           *
********************************************/
 
 
 /*
  * generer tous les elements du dossier
  */
 
function generateElementDossier($id)
{
	$pdo=new SPDO();
	$stmt_dos="select dos_numcomplet,dos_type,dos_titre,dos_pays,dos_creadate,dos_titulaire_saisi from dossier where dossier.dos_id='".$id."'";
	$result=$pdo->prepare($stmt_dos);

	$list_dossier=array();
	$result->execute();
	
	foreach($result->fetchAll(PDO::FETCH_OBJ) as $row)
	
	{
		$list_dossier[]=$row;
		
	}
return $list_dossier;		
}






