<?php
/*ON charge les librairies de pChart qui se trouve dans le dossier class pour qu'il puisse afficher un graphique*/
include("class/pData.class.php");
include("class/pDraw.class.php");
include("class/pImage.class.php");
require_once("../../../BDD/SPDO.php");

class RequeteDiagram{

    private $nom_table; //On récupère le nom de la table
    private $regroupement; //On récupère le type de regroupement ex:jour, semaine, mois ou année
   	private $nomImage; //On récupère le nom de l'image
   	private $jours; //On recupère les jours qui ont été selectionné
	private $tailleJours;//On recupère la taille du tableau contenant le nombre de jours selectionné
	private $moisForm;//On recupère les mois qui ont été selectionné
	private $tailleMois;//On recupère les jours qui ont été selectionné
	private $semaine;//On recupère les semaines qui ont été selectionnées
	private $tailleSemaines;//On recupère la taille des semaines selectionnées
	private $tableauHonos;//Pour stocker les données pour les honoraires
	private $tableauTaxes;//Pour stocker les données pour les taxes
	private $tableauFrais;//Pour stocker les données pour les frais
	private $tableauNbreAchats;//Pour stocker les données pour le nombre d'achat
	private $tableauMontAchats;//Pour stocker les données pour le montant d'achat
	private $tableauRepartMontant;//Pour stocker les données pour le montant des repartions
	private $pdo; //instance pour se connecter à la base de données
	private $exportCSV;//recupere le choix pour exporter ou non les requetes dans une fichier excel


    public function __construct(){
        $this->nom_table=$_POST['nom_table'];
        $this->nomImage=$_POST['nom_histo']; 
        $this->regroupement=$_POST['regp'];
        $this->exportCSV=$_POST['export_csv'];
        $this->tableauHonos=array();
		$this->tableauTaxes=array();
		$this->tableauFrais=array();
		$this->tableauNbreAchats=array();
		$this->tableauMontAchats=array();
		$this->tableauRepartMontant=array();
		$this->pdo = new SPDO();
        //initialisation des variables jour, semaine, mois, annee
        if($this->regroupement=="jour"){
			$this->jours=$_POST['multijrs'];
			$this->tailleJours=count($this->jours);
			$this->moisForm=$_POST['mois'];
		}else if ($this->regroupement=="semaine") {
			$this->semaine=$_POST['multisemaines'];
		    $this->tailleSemaines=count($this->semaine);
		} else if ($this->regroupement=="mois") {
			$this->moisForm=$_POST['multimois'];
			$this->tailleMois=count($this->moisForm);
		} 
    }

    //cette fonction permet de creer un fichier csv
	public function pdoToCsv($titre, $nomFichier, $tableauResultats){
	    //On passe en parametre le tableau contenant le resultat de la requete "$tableauResults"

	    $csv = "";//declaration d'un csv vide
	    $csv .=$titre.";"."\n; \n"; //On ajoute un tire au fichier csv comme par exemple le nom de la table 
	    
	    $tailleTableau = count($tableauResultats);	    
		 for ($n=0; $n<$tailleTableau; $n++){ 
		 	$index=key($tableauResultats[$n]);
		 	$value=$tableauResultats[$n][$index];
		 	$csv .= $index.";".$value.";";
		 	$csv.="\n";
		 }
			     
	    $csvFile=fopen($nomFichier.".csv", 'a');
	    fputs($csvFile, $csv);
	    fclose($csvFile);	     
	}

    //On calcule les nombres et les montants des achats 
    public function calculAchat(){
		$fournisseur=$_POST['corresp_entite'];
		$periode=$_POST['achat_periode'];
		//$nomFrs=array("Nom fournisseur =>".$fournisseur);
		$tableauCSV=array();//contient tous les resulats qui seront exporter en csv
		//array_push($tableauCSV, $nomFrs);//On met le nom du 
		$stmt_achatcpt = "SELECT count(ach_id) AS nbachats, "; 
		$stmt_achatmont = "SELECT SUM((ach_prixachatunit*ach_nb)) AS montants, "; 

		/**
		On verifie si le type de regroupement est en jour alors on effectue une nouvelle 
		pour prendre uniquement par le jour ou les jours ainsi que le mois et l'année choisi.
		Par contre on ne peut choisir qu'une seule année et un seul mois à la fois. Si vous choississez plusieurs 
		années ou mois la requete ne prendra en compte que les 1ère qui ont été selectionné
		*/
		if($this->regroupement=="jour"){
			if($fournisseur!="tous"){
				$stmt_achatcpt .=" EXTRACT(DAY FROM ach_dateffo) AS jrs FROM achat JOIN entite ON achat.ach_rf_ent=entite.ent_id WHERE EXTRACT(MONTH FROM ach_dateffo)=".$this->moisForm." AND EXTRACT(YEAR FROM ach_dateffo)=".$periode[0]." AND ach_rf_ent='$fournisseur' AND ";
				$stmt_achatmont .=" EXTRACT(DAY FROM ach_dateffo) AS jrs FROM achat JOIN entite ON achat.ach_rf_ent=entite.ent_id WHERE EXTRACT(MONTH FROM ach_dateffo)=".$this->moisForm." AND EXTRACT(YEAR FROM ach_dateffo)=".$periode[0]." AND ach_rf_ent='$fournisseur' AND ";
			}else{
				$stmt_achatcpt .=" EXTRACT(DAY FROM ach_dateffo) AS jrs FROM achat JOIN entite ON achat.ach_rf_ent=entite.ent_id WHERE EXTRACT(MONTH FROM ach_dateffo)=".$this->moisForm." AND EXTRACT(YEAR FROM ach_dateffo)=".$periode[0]." AND ";
				$stmt_achatmont .=" EXTRACT(DAY FROM ach_dateffo) AS jrs FROM achat JOIN entite ON achat.ach_rf_ent=entite.ent_id WHERE EXTRACT(MONTH FROM ach_dateffo)=".$this->moisForm." AND EXTRACT(YEAR FROM ach_dateffo)=".$periode[0]." AND ";
			}
			for ($j=0; $j<$this->tailleJours ; $j++) { 
				if($j<=($this->tailleJours-2) && $this->tailleJours!=1){
					$stmt_achatcpt .=" EXTRACT(DAY FROM ach_dateffo)= ".$this->getJour()[$j]." OR";
					$stmt_achatmont .=" EXTRACT(DAY FROM ach_dateffo)=".$this->getJour()[$j]." OR";
				}
				else{
					$stmt_achatcpt .=" EXTRACT(DAY FROM ach_dateffo)= ".$this->getJour()[$j];
					$stmt_achatmont .=" EXTRACT(DAY FROM ach_dateffo)= ".$this->getJour()[$j];
				}
			}
			$stmt_achatcpt .=" GROUP BY jrs ORDER BY jrs";
			$stmt_achatmont .=" GROUP BY jrs ORDER BY jrs";
			//pour calculer le nombre d'achat pour jour pour un donné et une année, qui peut être affecter à un fournisseur ou non
			$result_achatcpt = $this->getPdo()->prepare($stmt_achatcpt);
			$result_achatcpt->execute();
			$nomColNb=array("jour=>NbAchats");
			array_push($tableauCSV, $nomColNb);
			foreach($result_achatcpt->fetchAll(PDO::FETCH_OBJ) as $achatcpt) {
				$tableau=array($achatcpt->jrs=>$achatcpt->nbachats);
				array_push($this->tableauNbreAchats, $tableau);
				array_push($tableauCSV, $tableau);
			}


			//pour calculer le montant d'un achat pour jour pour un donné et une année, qui peut être affecter à un fournisseur ou non
			$result_achatmont = $this->getPdo()->prepare($stmt_achatmont);
			$result_achatmont->execute();
			$nomColMontant=array("jour=>MontAchats");
			array_push($tableauCSV, $nomColMontant);
			foreach($result_achatmont->fetchAll(PDO::FETCH_OBJ) as $achatmont) {
				$tableau=array($achatmont->jrs=>$achatmont->montants);
				array_push($this->tableauMontAchats, $tableau);
				array_push($tableauCSV, $tableau);
			}

		}


		/**
		On verifie si le type de regroupement est en semaine alors on effectue une nouvelle 
		pour prendre uniquement par le semaine ou les semaines ainsi que l'année choisie.
		Par contre on ne peut choisir qu'une seule année à la fois. Si vous choississez plusieurs 
		années la requete ne prendra en compte que la 1ère qui a été selectionné
		*/
		else if($this->regroupement=="semaine"){
			if($fournisseur!="tous"){
				$stmt_achatcpt .=" EXTRACT(WEEK FROM ach_dateffo) AS semaines FROM achat JOIN entite ON achat.ach_rf_ent=entite.ent_id WHERE EXTRACT(YEAR FROM ach_dateffo)=".$periode[0]." AND ach_rf_ent='$fournisseur' AND ";
				$stmt_achatmont .=" EXTRACT(WEEK FROM ach_dateffo) AS semaines FROM achat JOIN entite ON achat.ach_rf_ent=entite.ent_id WHERE EXTRACT(YEAR FROM ach_dateffo)=".$periode[0]." AND ach_rf_ent='$fournisseur' AND ";
			}else{
				$stmt_achatcpt .=" EXTRACT(WEEK FROM ach_dateffo) AS semaines FROM achat JOIN entite ON achat.ach_rf_ent=entite.ent_id WHERE EXTRACT(YEAR FROM ach_dateffo)=".$periode[0]." AND ";
				$stmt_achatmont .=" EXTRACT(WEEK FROM ach_dateffo) AS semaines FROM achat JOIN entite ON achat.ach_rf_ent=entite.ent_id WHERE EXTRACT(YEAR FROM ach_dateffo)=".$periode[0]." AND ";
			}
			for ($s=0; $s<$this->tailleSemaines ; $s++) { 
				if($s<=($this->tailleSemaines-2) && $this->tailleSemaines!=1){
					$stmt_achatcpt .=" EXTRACT(WEEK FROM ach_dateffo)= ".$this->semaine[$s]." OR";
					$stmt_achatmont .=" EXTRACT(WEEK FROM ach_dateffo)= ".$this->semaine[$s]." OR";
				}
				else{
					$stmt_achatcpt .=" EXTRACT(WEEK FROM ach_dateffo)= ".$this->semaine[$s];
					$stmt_achatmont .=" EXTRACT(WEEK FROM ach_dateffo)= ".$this->semaine[$s];
				}
			}
			$stmt_achatcpt .=" GROUP BY semaines ORDER BY semaines";
			$stmt_achatmont .=" GROUP BY semaines ORDER BY semaines";
			//pour calculer le nombre d'achat pour jour pour un donné et une année, qui peut être affecter à un fournisseur ou non
			$result_achatcpt = $this->getPdo()->prepare($stmt_achatcpt);
			$result_achatcpt->execute();
			$nomColNb=array("semaine=>NbAchats");
			array_push($tableauCSV, $nomColNb);
			foreach($result_achatcpt->fetchAll(PDO::FETCH_OBJ) as $achatcpt) {
				$tableau=array($achatcpt->semaines=>$achatcpt->nbachats);
				array_push($this->tableauNbreAchats,$tableau);
				array_push($tableauCSV, $tableau);
			}

			//pour calculer le montant d'un achat pour jour pour un donné et une année, qui peut être affecter à un fournisseur ou non
			$result_achatmont = $this->getPdo()->prepare($stmt_achatmont);
			$result_achatmont->execute();
			$nomColMontant=array("semaine=>MontAchats");
			array_push($tableauCSV, $nomColMontant);
			foreach($result_achatmont->fetchAll(PDO::FETCH_OBJ) as $achatmont) {
				$tableau=array($achatmont->semaines=>$achatmont->montants);
				array_push($this->tableauMontAchats,$tableau);
				array_push($tableauCSV, $tableau);
			}

		}

		/**
		On verifie si le type de regroupement est en mois alors on effectue une nouvelle 
		pour prendre uniquement par le mois ou les mois ainsi que l'année choisie.
		Par contre on ne peut choisir qu'une seule année à la fois, ici aussi. Si vous choississez plusieurs 
		années la requete ne prendra en compte que la 1ère qui a été selectionné
		*/
		else if($this->regroupement=="mois"){
			if($fournisseur!="tous"){
				$stmt_achatcpt .=" EXTRACT(MONTH FROM ach_dateffo) AS months FROM achat JOIN entite ON achat.ach_rf_ent=entite.ent_id WHERE EXTRACT(YEAR FROM ach_dateffo)=".$periode[0]." AND ach_rf_ent='$fournisseur' AND ";
				$stmt_achatmont .=" EXTRACT(MONTH FROM ach_dateffo) AS months FROM achat JOIN entite ON achat.ach_rf_ent=entite.ent_id WHERE EXTRACT(YEAR FROM ach_dateffo)=".$periode[0]." AND ach_rf_ent='$fournisseur' AND ";
			}else{
				$stmt_achatcpt .=" EXTRACT(MONTH FROM ach_dateffo) AS months FROM achat JOIN entite ON achat.ach_rf_ent=entite.ent_id WHERE EXTRACT(YEAR FROM ach_dateffo)=".$periode[0]." AND ";
				$stmt_achatmont .=" EXTRACT(MONTH FROM ach_dateffo) AS months FROM achat JOIN entite ON achat.ach_rf_ent=entite.ent_id WHERE EXTRACT(YEAR FROM ach_dateffo)=".$periode[0]." AND ";
			}
			for ($m=0; $m<$this->tailleMois ; $m++) { 
				if($m<=($this->tailleMois-2) && $this->tailleMois!=1){
					$stmt_achatcpt .=" EXTRACT(MONTH FROM ach_dateffo)= ".$this->moisForm[$m]." OR ";
					$stmt_achatmont .=" EXTRACT(MONTH FROM ach_dateffo)= ".$this->moisForm[$m]." OR ";
				}
				else{
					$stmt_achatcpt .=" EXTRACT(MONTH FROM ach_dateffo)= ".$this->moisForm[$m];
					$stmt_achatmont .=" EXTRACT(MONTH FROM ach_dateffo)= ".$this->moisForm[$m];
				}
			}
			$stmt_achatcpt .=" GROUP BY months ORDER BY months";
			$stmt_achatmont .=" GROUP BY months ORDER BY months";
			//pour calculer le nombre d'achat pour jour pour un donné et une année, qui peut être affecter à un fournisseur ou non
			$result_achatcpt = $this->getPdo()->prepare($stmt_achatcpt);
			$result_achatcpt->execute();
			$nomColNb=array("mois=>NbAchats");
			array_push($tableauCSV, $nomColNb);
			foreach($result_achatcpt->fetchAll(PDO::FETCH_OBJ) as $achatcpt) {
				$tableau=array($achatcpt->months=>$achatcpt->nbachats);
				array_push($this->tableauNbreAchats,$tableau);
				array_push($tableauCSV, $tableau);
			}

			//pour calculer le montant d'un achat pour jour pour un donné et une année, qui peut être affecter à un fournisseur ou non
			$result_achatmont = $this->getPdo()->prepare($stmt_achatmont);
			$result_achatmont->execute();
			$nomColMontant=array("mois=>MontAchats");
			array_push($tableauCSV, $nomColMontant);
			foreach($result_achatmont->fetchAll(PDO::FETCH_OBJ) as $achatmont) {
				$tableau=array($achatmont->months=>$achatmont->montants);
				array_push($this->tableauMontAchats,$tableau);
				array_push($tableauCSV, $tableau);
			}

		}

		/**
		On verifie si le type de regroupement est en année alors on effectue une nouvelle 
		pour prendre uniquement par l'année ou les années
		*/
		else if($this->regroupement=="annee"){
			$tailleAnnees=count($periode);
			if($fournisseur!="tous"){
				$stmt_achatcpt .=" EXTRACT(YEAR FROM ach_dateffo) AS annees FROM achat JOIN entite ON achat.ach_rf_ent=entite.ent_id WHERE ach_rf_ent='$fournisseur' AND ";
				$stmt_achatmont .=" EXTRACT(YEAR FROM ach_dateffo) AS annees FROM achat JOIN entite ON achat.ach_rf_ent=entite.ent_id WHERE ach_rf_ent='$fournisseur' AND ";
			}else{
				$stmt_achatcpt .=" EXTRACT(YEAR FROM ach_dateffo) AS annees FROM achat JOIN entite ON achat.ach_rf_ent=entite.ent_id WHERE ";
				$stmt_achatmont .=" EXTRACT(YEAR FROM ach_dateffo) AS annees FROM achat JOIN entite ON achat.ach_rf_ent=entite.ent_id WHERE ";
			}
			for($a=0; $a<$tailleAnnees ; $a++) { 
				if($a<=($tailleAnnees-2) && $tailleAnnees!=1){
					$stmt_achatcpt .=" EXTRACT(YEAR FROM ach_dateffo)=$periode[$a] OR";
					$stmt_achatmont .=" EXTRACT(YEAR FROM ach_dateffo)=$periode[$a] OR";
				}
				else{
					$stmt_achatcpt .=" EXTRACT(YEAR FROM ach_dateffo)=$periode[$a] ";
					$stmt_achatmont .=" EXTRACT(YEAR FROM ach_dateffo)=$periode[$a] ";
				}
			}
			$stmt_achatcpt .=" GROUP BY annees ORDER BY annees";
			$stmt_achatmont .=" GROUP BY annees ORDER BY annees";
			//pour calculer le nombre d'achat pour jour pour un donné et une année, qui peut être affecter à un fournisseur ou non
			$result_achatcpt = $this->getPdo()->prepare($stmt_achatcpt);
			$result_achatcpt->execute();
			$nomColNb=array("annee=>NbAchats");
			array_push($tableauCSV, $nomColNb);
			foreach($result_achatcpt->fetchAll(PDO::FETCH_OBJ) as $achatcpt) {
				$tableau=array($achatcpt->annees=>$achatcpt->nbachats);
				array_push($this->tableauNbreAchats,$tableau);
				array_push($tableauCSV, $tableau);
			}

			//pour calculer le montant d'un achat pour jour pour un donné et une année, qui peut être affecter à un fournisseur ou non
			$result_achatmont = $this->getPdo()->prepare($stmt_achatmont);
			$result_achatmont->execute();
			$nomColMontant=array("annee=>MontAchats");
			array_push($tableauCSV, $nomColMontant);
			foreach($result_achatmont->fetchAll(PDO::FETCH_OBJ) as $achatmont) {
				$tableau=array($achatmont->annees=>$achatmont->montants);
				array_push($this->tableauMontAchats,$tableau);
				array_push($tableauCSV, $tableau);
			}

		}
		//on verifie si on doit créer ou non le fichier csv
		if ($this->exportCSV !="non") {
				$this->pdoToCsv("Table Achat", $this->nomImage, $tableauCSV);
		}	

	}//fin de la methode de calcul des montants de la table achat  

	//On calcule les montants des honos, des taxes et des frais de la table facture 
    public function calculFacture(){
		$entite=$_POST['entiteVidon'];
		$periode=$_POST['fac_periode'];
		$clientFact=$_POST['fac_client'];
		$tableauCSV=array();//contient tous les resulats qui seront exporter en csv

		$stmt_facture = "SELECT  SUM(fac_honoraires) AS honos, SUM(fac_taxes) AS taxes,SUM(fac_frais) AS frais, "; 
		
		//On calcule les differents montans par jour
		if($this->regroupement=="jour"){
			if($clientFact!="tous"){
				$stmt_facture .="EXTRACT(DAY FROM fac_date) AS jrs FROM facture JOIN entite ON facture.fac_rf_ent=entite.ent_id WHERE fac_pole=$entite AND  EXTRACT(MONTH FROM fac_date)=".$this->moisForm." AND EXTRACT(YEAR FROM fac_date)=".$periode[0]." AND ent_raisoc='$clientFact' AND ";
			}else{
				$stmt_facture .="EXTRACT(DAY FROM fac_date) AS jrs FROM facture WHERE fac_pole=$entite AND  EXTRACT(MONTH FROM fac_date)=".$this->moisForm." AND EXTRACT(YEAR FROM fac_date)=".$periode[0]." AND ";
			}
			for ($j=0; $j<$this->tailleJours ; $j++) { 
				if($j<=($this->tailleJours-2) && $this->tailleJours!=1){
					$stmt_facture .=" EXTRACT(DAY FROM fac_date)=".$this->jours[$j]." OR";
				}
				else{
					$stmt_facture .=" EXTRACT(DAY FROM fac_date)=".$this->jours[$j];
				}
			}
			$stmt_facture .=" GROUP BY jrs ORDER BY jrs";

			$result_facture = $this->getPdo()->prepare($stmt_facture);
			$result_facture->execute();
			foreach($result_facture->fetchAll(PDO::FETCH_OBJ) as $fact) {
				$tableauh=array($fact->jrs=>$fact->honos);
				$tableaut=array($fact->jrs=>$fact->taxes);
				$tableauf=array($fact->jrs=>$fact->frais);
				array_push($this->tableauHonos,$tableauh);
				array_push($this->tableauTaxes,$tableaut);
				array_push($this->tableauFrais,$tableauf);
				array_push($tableauCSV, array("Honoraires par jour"));
				array_push($tableauCSV,$tableauh);
				array_push($tableauCSV, array("Taxes par jour"));
				array_push($tableauCSV,$tableaut);
				array_push($tableauCSV, array("Frais par jour"));
				array_push($tableauCSV,$tableauf);

			}

        //On calcule les differents montans par semaine
		}else if($this->regroupement=="semaine"){
			if($clientFact!="tous"){
				$stmt_facture .=" EXTRACT(WEEK FROM fac_date) AS semaines FROM facture JOIN entite ON facture.fac_rf_ent=entite.ent_id WHERE EXTRACT(YEAR FROM fac_date)=".$periode[0]." AND ent_raisoc='$clientFact' AND ";
			}else{
				$stmt_facture .=" EXTRACT(WEEK FROM fac_date) AS semaines FROM facture WHERE EXTRACT(YEAR FROM fac_date)=".$periode[0]." AND ";
			}
			for ($s=0; $s<$this->tailleSemaines ; $s++) { 
				if($s<=($this->tailleSemaines-2) && $this->tailleSemaines!=1){
					$stmt_facture .=" EXTRACT(WEEK FROM fac_date)=".$this->semaine[$s]." OR";
				}
				else{
					$stmt_facture .=" EXTRACT(WEEK FROM fac_date)=".$this->semaine[$s];
				}
			}
			$stmt_facture .=" GROUP BY semaines ORDER BY semaines";
			$result_facture = $this->getPdo()->prepare($stmt_facture);
			$result_facture->execute();
			foreach($result_facture->fetchAll(PDO::FETCH_OBJ) as $fact) {
				$tableauh=array($fact->semaines=>$fact->honos);
				$tableaut=array($fact->semaines=>$fact->taxes);
				$tableauf=array($fact->semaines=>$fact->frais);
				array_push($this->tableauHonos,$tableauh);
				array_push($this->tableauTaxes,$tableaut);
				array_push($this->tableauFrais,$tableauf);
				array_push($tableauCSV, array("Honoraires par semaine"));
				array_push($tableauCSV,$tableauh);
				array_push($tableauCSV, array("Taxes par semaine"));
				array_push($tableauCSV,$tableaut);
				array_push($tableauCSV, array("Frais par semaine"));
				array_push($tableauCSV,$tableauf);
			}

		//On calcule les differents montans par mois
		}else if($this->regroupement=="mois"){
			if($clientFact!="tous"){
				$stmt_facture .=" EXTRACT(MONTH FROM fac_date) AS months FROM facture JOIN entite ON facture.fac_rf_ent=entite.ent_id WHERE EXTRACT(YEAR FROM fac_date)=".$periode[0]." AND ent_raisoc='$clientFact' AND ";
			}else{
				$stmt_facture .=" EXTRACT(MONTH FROM fac_date) AS months FROM facture WHERE EXTRACT(YEAR FROM fac_date)=".$periode[0]." AND ";
			}
			for ($m=0; $m<$this->tailleMois ; $m++) { 
				if($m<=($this->tailleMois-2) && $this->tailleMois!=1){
					$stmt_facture .=" EXTRACT(MONTH FROM fac_date)=".$this->moisForm[$m]." OR"; }
				else{ $stmt_facture .=" EXTRACT(MONTH FROM fac_date)=".$this->moisForm[$m]; }
			}
			$stmt_facture .=" GROUP BY months ORDER BY months";

			$result_facture = $this->getPdo()->prepare($stmt_facture);
			$result_facture->execute();
			foreach($result_facture->fetchAll(PDO::FETCH_OBJ) as $fact) {
				$tableauh=array($fact->months=>$fact->honos);
				$tableaut=array($fact->months=>$fact->taxes);
				$tableauf=array($fact->months=>$fact->frais);
				array_push($this->tableauHonos,$tableauh);
				array_push($this->tableauTaxes,$tableaut);
				array_push($this->tableauFrais,$tableauf);
				array_push($tableauCSV, array("Honoraires par mois"));
				array_push($tableauCSV,$tableauh);
				array_push($tableauCSV, array("Taxes par mois"));
				array_push($tableauCSV,$tableaut);
				array_push($tableauCSV, array("Frais par mois"));
				array_push($tableauCSV,$tableauf);
			}
		//On calcule les differents montans par annee
		}else if($this->regroupement=="annee"){
			$tailleAnnees=count($periode);
			if($clientFact!="tous"){
				$stmt_facture .=" EXTRACT(YEAR FROM fac_date) AS annees FROM facture JOIN entite ON facture.fac_rf_ent=entite.ent_id WHERE ent_raisoc='$clientFact' AND ";
			}else{
				$stmt_facture .=" EXTRACT(YEAR FROM fac_date) AS annees FROM facture WHERE "; }
			for ($a=0; $a<$tailleAnnees ; $a++) { 
				if($a<=($tailleAnnees-2) && $tailleAnnees!=1){
					$stmt_facture .=" EXTRACT(YEAR FROM fac_date)=$periode[$a] OR";	}
				else{ $stmt_facture .=" EXTRACT(YEAR FROM fac_date)=$periode[$a] ";	}
			}

			$stmt_facture .=" GROUP BY annees ORDER BY annees";
			$result_facture = $this->getPdo()->prepare($stmt_facture);
			$result_facture->execute();
			foreach($result_facture->fetchAll(PDO::FETCH_OBJ) as $fact) {
				$tableauh=array($fact->annees=>$fact->honos);
				$tableaut=array($fact->annees=>$fact->taxes);
				$tableauf=array($fact->annees=>$fact->frais);
				array_push($this->tableauHonos,$tableauh);
				array_push($this->tableauTaxes,$tableaut);
				array_push($this->tableauFrais,$tableauf);
				array_push($tableauCSV, array("Honoraires par annee"));
				array_push($tableauCSV,$tableauh);
				array_push($tableauCSV, array("Taxes par annee"));
				array_push($tableauCSV,$tableaut);
				array_push($tableauCSV, array("Frais par annee"));
				array_push($tableauCSV,$tableauf);
			}

		}
	 

		//on verifie si on doit créer ou non le fichier csv
		if ($this->exportCSV !="non") {
				$this->pdoToCsv("Table Facture", $this->nomImage, $tableauCSV);
		}	
	}//fin de la methode de calcul des montants de la table facture  

	//On calcule les montants des honos, des taxes et des frais de la table facture 
    public function calculLigneFacture(){
		$entite=$_POST['entiteVidon'];
		$clientligneFact=$_POST['ligfac_client'];
		$periode=$_POST['ligfac_periode'];
		$tableauCSV=array();
		$stmt_ligfacthonos = "SELECT SUM(lig_montant) AS honos, ";
		$stmt_ligfacttaxes = "SELECT SUM(lig_montant) AS taxes, ";
		$stmt_ligfactfrais = "SELECT SUM(lig_montant) AS frais, ";
		
		/**
		On verifie si le type de regroupement est en jour alors on effectue une nouvelle 
		pour prendre uniquement par le jour ou les jours ainsi que le mois et l'année choisi.
		Par contre on ne peut choisir qu'une seule année et un seul mois à la fois. Si vous choississez plusieurs 
		années ou mois la requete ne prendra en compte que les 1ère qui ont été selectionné
		*/
		if($this->regroupement=="jour"){
			if($clientligneFact!="tous"){
				$stmt_ligfacthonos .="EXTRACT(DAY FROM fac_date) AS jrs FROM lignefacture JOIN facture ON lignefacture.lig_rf_fac=facture.fac_id JOIN entite ON facture.fac_rf_ent=entite.ent_id WHERE fac_pole=$entite AND  EXTRACT(MONTH FROM fac_date)=".$this->moisForm." AND EXTRACT(YEAR FROM fac_date)=".$periode[0]." AND ent_raisoc='$clientligneFact' AND lig_typeligne='honos' AND";
				$stmt_ligfacttaxes .="EXTRACT(DAY FROM fac_date) AS jrs FROM lignefacture JOIN facture ON lignefacture.lig_rf_fac=facture.fac_id JOIN entite ON facture.fac_rf_ent=entite.ent_id WHERE fac_pole=$entite AND  EXTRACT(MONTH FROM fac_date)=".$this->moisForm." AND EXTRACT(YEAR FROM fac_date)=".$periode[0]." AND ent_raisoc='$clientligneFact' AND lig_typeligne='taxes' AND";
				$stmt_ligfactfrais .="EXTRACT(DAY FROM fac_date) AS jrs FROM lignefacture JOIN facture ON lignefacture.lig_rf_fac=facture.fac_id JOIN entite ON facture.fac_rf_ent=entite.ent_id WHERE fac_pole=$entite AND  EXTRACT(MONTH FROM fac_date)=".$this->moisForm." AND EXTRACT(YEAR FROM fac_date)=".$periode[0]." AND ent_raisoc='$clientligneFact' AND lig_typeligne='frais' AND";
			}else{
				$stmt_ligfacthonos .="EXTRACT(DAY FROM fac_date) AS jrs FROM lignefacture JOIN facture ON lignefacture.lig_rf_fac=facture.fac_id WHERE fac_pole=$entite AND  EXTRACT(MONTH FROM fac_date)=".$this->moisForm." AND EXTRACT(YEAR FROM fac_date)=".$periode[0]." AND lig_typeligne='honos' AND";
				$stmt_ligfacttaxes .="EXTRACT(DAY FROM fac_date) AS jrs FROM lignefacture JOIN facture ON lignefacture.lig_rf_fac=facture.fac_id WHERE fac_pole=$entite AND  EXTRACT(MONTH FROM fac_date)=".$this->moisForm." AND EXTRACT(YEAR FROM fac_date)=".$periode[0]." AND lig_typeligne='taxes' AND";
				$stmt_ligfactfrais .="EXTRACT(DAY FROM fac_date) AS jrs FROM lignefacture JOIN facture ON lignefacture.lig_rf_fac=facture.fac_id WHERE fac_pole=$entite AND  EXTRACT(MONTH FROM fac_date)=".$this->moisForm." AND EXTRACT(YEAR FROM fac_date)=".$periode[0]." AND lig_typeligne='frais' AND";
			}
			for ($j=0; $j<$this->tailleJours ; $j++) { 
				if($j<=($this->tailleJours-2) && $this->tailleJours!=1){
					$stmt_ligfacthonos .=" EXTRACT(DAY FROM fac_date)=".$this->jours[$j]." OR";
					$stmt_ligfacttaxes .=" EXTRACT(DAY FROM fac_date)=".$this->jours[$j]." OR";
					$stmt_ligfactfrais .=" EXTRACT(DAY FROM fac_date)=".$this->jours[$j]." OR";
				}
				else{
					$stmt_ligfacthonos .=" EXTRACT(DAY FROM fac_date)=".$this->jours[$j];
					$stmt_ligfacttaxes .=" EXTRACT(DAY FROM fac_date)=".$this->jours[$j];
					$stmt_ligfactfrais .=" EXTRACT(DAY FROM fac_date)=".$this->jours[$j];
				}
			}
			$stmt_ligfacthonos .=" GROUP BY jrs ORDER BY jrs";
			$stmt_ligfacttaxes .=" GROUP BY jrs ORDER BY jrs";
			$stmt_ligfactfrais .=" GROUP BY jrs ORDER BY jrs";
		
			$result_ligfacthonos = $this->getPdo()->prepare($stmt_ligfacthonos);
			$result_ligfacthonos->execute();
			array_push($tableauCSV, array("Honoraires par jour"));
			foreach($result_ligfacthonos->fetchAll(PDO::FETCH_OBJ) as $lighonos) {
				$tableauh=array($lighonos->jrs=>$lighonos->honos);
				array_push($this->tableauHonos,$tableauh);
				array_push($tableauCSV,$tableauh);	
			}

			$result_ligfacttaxes = $this->getPdo()->prepare($stmt_ligfacttaxes);
			$result_ligfacttaxes->execute();
			array_push($tableauCSV, array("Taxes par jour"));
			foreach($result_ligfacttaxes->fetchAll(PDO::FETCH_OBJ) as $ligtaxes) {
				$tableaut=array($ligtaxes->jrs=>$ligtaxes->taxes);
				array_push($this->tableauTaxes,$tableaut);
				array_push($tableauCSV,$tableaut);
			}

			$result_ligfactfrais = $this->getPdo()->prepare($stmt_ligfactfrais);
			$result_ligfactfrais->execute();
			array_push($tableauCSV, array("Frais par jour"));
			foreach($result_ligfactfrais->fetchAll(PDO::FETCH_OBJ) as $ligfrais) {
				$tableauf=array($ligfrais->jrs=>$ligfrais->frais);
				array_push($this->tableauFrais,$tableauf);				
				array_push($tableauCSV,$tableauf);
			}

		}

		/**
		On verifie si le type de regroupement est en semaine alors on effectue une nouvelle 
		pour prendre uniquement par le semaine ou les semaines ainsi que l'année choisie.
		Par contre on ne peut choisir qu'une seule année à la fois. Si vous choississez plusieurs 
		années la requete ne prendra en compte que la 1ère qui a été selectionné
		*/
		else if($this->regroupement=="semaine"){
			if($clientligneFact!="tous"){
				$stmt_ligfacthonos .="EXTRACT(WEEK FROM fac_date) AS semaines FROM lignefacture JOIN facture ON lignefacture.lig_rf_fac=facture.fac_id JOIN entite ON facture.fac_rf_ent=entite.ent_id WHERE fac_pole=$entite AND EXTRACT(YEAR FROM fac_date)=".$periode[0]." AND ent_raisoc='$clientligneFact' AND lig_typeligne='honos' AND";
				$stmt_ligfacttaxes .="EXTRACT(WEEK FROM fac_date) AS semaines FROM lignefacture JOIN facture ON lignefacture.lig_rf_fac=facture.fac_id JOIN entite ON facture.fac_rf_ent=entite.ent_id WHERE fac_pole=$entite AND EXTRACT(YEAR FROM fac_date)=".$periode[0]." AND ent_raisoc='$clientligneFact' AND lig_typeligne='taxes' AND";
				$stmt_ligfactfrais .="EXTRACT(WEEK FROM fac_date) AS semaines FROM lignefacture JOIN facture ON lignefacture.lig_rf_fac=facture.fac_id JOIN entite ON facture.fac_rf_ent=entite.ent_id WHERE fac_pole=$entite AND EXTRACT(YEAR FROM fac_date)=".$periode[0]." AND ent_raisoc='$clientligneFact' AND lig_typeligne='frais' AND";
			}else{
				$stmt_ligfacthonos .="EXTRACT(WEEK FROM fac_date) AS semaines FROM lignefacture JOIN facture ON lignefacture.lig_rf_fac=facture.fac_id WHERE fac_pole=$entite AND EXTRACT(YEAR FROM fac_date)=".$periode[0]." AND lig_typeligne='honos' AND";
				$stmt_ligfacttaxes .="EXTRACT(WEEK FROM fac_date) AS semaines FROM lignefacture JOIN facture ON lignefacture.lig_rf_fac=facture.fac_id WHERE fac_pole=$entite AND EXTRACT(YEAR FROM fac_date)=".$periode[0]." AND lig_typeligne='taxes' AND";
				$stmt_ligfactfrais .="EXTRACT(WEEK FROM fac_date) AS semaines FROM lignefacture JOIN facture ON lignefacture.lig_rf_fac=facture.fac_id WHERE fac_pole=$entite AND EXTRACT(YEAR FROM fac_date)=".$periode[0]." AND lig_typeligne='frais' AND";
			}
			for ($s=0; $s<$this->tailleSemaines ; $s++){ 
				if($s<=($this->tailleSemaines-2) && $this->tailleSemaines!=1){
					$stmt_ligfacthonos .=" EXTRACT(WEEK FROM fac_date)=".$this->semaine[$s]." OR";
					$stmt_ligfacttaxes .=" EXTRACT(WEEK FROM fac_date)=".$this->semaine[$s]." OR";
					$stmt_ligfactfrais .=" EXTRACT(WEEK FROM fac_date)=".$this->semaine[$s]." OR";
				}
				else{
					$stmt_ligfacthonos .=" EXTRACT(WEEK FROM fac_date)=".$this->semaine[$s];
					$stmt_ligfacttaxes .=" EXTRACT(WEEK FROM fac_date)=".$this->semaine[$s];
					$stmt_ligfactfrais .=" EXTRACT(WEEK FROM fac_date)=".$this->semaine[$s];
				}
			}
			$stmt_ligfacthonos .=" GROUP BY semaines ORDER BY semaines";
			$stmt_ligfacttaxes .=" GROUP BY semaines ORDER BY semaines";
			$stmt_ligfactfrais .=" GROUP BY semaines ORDER BY semaines";
		
			$result_ligfacthonos = $this->getPdo()->prepare($stmt_ligfacthonos);
			$result_ligfacthonos->execute();
			array_push($tableauCSV, array("Honoraires par semaine"));
			foreach($result_ligfacthonos->fetchAll(PDO::FETCH_OBJ) as $lighonos) {
				$tableauh=array($lighonos->semaines=>$lighonos->honos);
				array_push($this->tableauHonos,$tableauh);				
				array_push($tableauCSV,$tableauh);	
			}

			$result_ligfacttaxes = $this->getPdo()->prepare($stmt_ligfacttaxes);
			$result_ligfacttaxes->execute();
			array_push($tableauCSV, array("Taxes par semaine"));
			foreach($result_ligfacttaxes->fetchAll(PDO::FETCH_OBJ) as $ligtaxes) {
				$tableaut=array($ligtaxes->semaines=>$ligtaxes->taxes);
				array_push($this->tableauTaxes,$tableaut);				
				array_push($tableauCSV,$tableaut);
			}

			$result_ligfactfrais = $this->getPdo()->prepare($stmt_ligfactfrais);
			$result_ligfactfrais->execute();
			array_push($tableauCSV, array("Frais par semaine"));
			foreach($result_ligfactfrais->fetchAll(PDO::FETCH_OBJ) as $ligfrais) {
				$tableauf=array($ligfrais->semaines=>$ligfrais->frais);
				array_push($this->tableauFrais,$tableauf);				
				array_push($tableauCSV,$tableauf);
			}

		}

		/**
		On verifie si le type de regroupement est en mois alors on effectue une nouvelle 
		pour prendre uniquement par le mois ou les mois ainsi que l'année choisie.
		Par contre on ne peut choisir qu'une seule année à la fois, ici aussi. Si vous choississez plusieurs 
		années la requete ne prendra en compte que la 1ère qui a été selectionné
		*/
		else if($this->regroupement=="mois"){
			if($clientligneFact!="tous"){
				$stmt_ligfacthonos .="EXTRACT(MONTH FROM fac_date) AS months FROM lignefacture JOIN facture ON lignefacture.lig_rf_fac=facture.fac_id JOIN entite ON facture.fac_rf_ent=entite.ent_id WHERE fac_pole=$entite AND EXTRACT(YEAR FROM fac_date)=".$periode[0]." AND ent_raisoc='$clientligneFact' AND lig_typeligne='honos' AND";
				$stmt_ligfacttaxes .="EXTRACT(MONTH FROM fac_date) AS months FROM lignefacture JOIN facture ON lignefacture.lig_rf_fac=facture.fac_id JOIN entite ON facture.fac_rf_ent=entite.ent_id WHERE fac_pole=$entite AND EXTRACT(YEAR FROM fac_date)=".$periode[0]." AND ent_raisoc='$clientligneFact' AND lig_typeligne='taxes' AND";
				$stmt_ligfactfrais .="EXTRACT(MONTH FROM fac_date) AS months FROM lignefacture JOIN facture ON lignefacture.lig_rf_fac=facture.fac_id JOIN entite ON facture.fac_rf_ent=entite.ent_id WHERE fac_pole=$entite AND EXTRACT(YEAR FROM fac_date)=".$periode[0]." AND ent_raisoc='$clientligneFact' AND lig_typeligne='frais' AND";
			}else{
				$stmt_ligfacthonos .="EXTRACT(MONTH FROM fac_date) AS months FROM lignefacture JOIN facture ON lignefacture.lig_rf_fac=facture.fac_id WHERE fac_pole=$entite AND EXTRACT(YEAR FROM fac_date)=".$periode[0]." AND lig_typeligne='honos' AND";
				$stmt_ligfacttaxes .="EXTRACT(MONTH FROM fac_date) AS months FROM lignefacture JOIN facture ON lignefacture.lig_rf_fac=facture.fac_id WHERE fac_pole=$entite AND EXTRACT(YEAR FROM fac_date)=".$periode[0]." AND lig_typeligne='taxes' AND";
				$stmt_ligfactfrais .="EXTRACT(MONTH FROM fac_date) AS months FROM lignefacture JOIN facture ON lignefacture.lig_rf_fac=facture.fac_id WHERE fac_pole=$entite AND EXTRACT(YEAR FROM fac_date)=".$periode[0]." AND lig_typeligne='frais' AND";
			}
			for ($m=0; $m<$this->tailleMois ; $m++){ 
				if($m<=($this->tailleMois-2) && $this->tailleMois!=1){
					$stmt_ligfacthonos .=" EXTRACT(MONTH FROM fac_date)=".$this->moisForm[$m]." OR";
					$stmt_ligfacttaxes .=" EXTRACT(MONTH FROM fac_date)=".$this->moisForm[$m]." OR";
					$stmt_ligfactfrais .=" EXTRACT(MONTH FROM fac_date)=".$this->moisForm[$m]." OR";
				}
				else{
					$stmt_ligfacthonos .=" EXTRACT(MONTH FROM fac_date)=".$this->moisForm[$m];
					$stmt_ligfacttaxes .=" EXTRACT(MONTH FROM fac_date)=".$this->moisForm[$m];
					$stmt_ligfactfrais .=" EXTRACT(MONTH FROM fac_date)=".$this->moisForm[$m];
				}
			}
			$stmt_ligfacthonos .=" GROUP BY months ORDER BY months";
			$stmt_ligfacttaxes .=" GROUP BY months ORDER BY months";
			$stmt_ligfactfrais .=" GROUP BY months ORDER BY months";
		
			$result_ligfacthonos = $this->getPdo()->prepare($stmt_ligfacthonos);
			$result_ligfacthonos->execute();
			array_push($tableauCSV, array("Honoraires par mois"));
			foreach($result_ligfacthonos->fetchAll(PDO::FETCH_OBJ) as $lighonos) {
				$tableauh=array($lighonos->months=>$lighonos->honos);
				array_push($this->tableauHonos,$tableauh);				
				array_push($tableauCSV,$tableauh);	
			}

			$result_ligfacttaxes = $this->getPdo()->prepare($stmt_ligfacttaxes);
			$result_ligfacttaxes->execute();
			array_push($tableauCSV, array("Taxes par mois"));
			foreach($result_ligfacttaxes->fetchAll(PDO::FETCH_OBJ) as $ligtaxes) {
				$tableaut=array($ligtaxes->months=>$ligtaxes->taxes);
				array_push($this->tableauTaxes,$tableaut);
				array_push($tableauCSV,$tableaut);
			}

			$result_ligfactfrais = $this->getPdo()->prepare($stmt_ligfactfrais);
			$result_ligfactfrais->execute();
			array_push($tableauCSV, array("Frais par mois"));
			foreach($result_ligfactfrais->fetchAll(PDO::FETCH_OBJ) as $ligfrais) {
				$tableauf=array($ligfrais->months=>$ligfrais->frais);
				array_push($this->tableauFrais,$tableauf);
				array_push($tableauCSV,$tableauf);
			}

		}//fin du calcul par mois

		/**
		On verifie si le type de regroupement est en année alors on effectue une nouvelle 
		pour prendre uniquement par l'année ou les années
		*/
		else if($this->regroupement=="annee"){
			$tailleAnnees=count($periode);
			if($clientligneFact!="tous"){
				$stmt_ligfacthonos .="EXTRACT(YEAR FROM fac_date) AS annees FROM lignefacture JOIN facture ON lignefacture.lig_rf_fac=facture.fac_id JOIN entite ON facture.fac_rf_ent=entite.ent_id WHERE fac_pole=$entite AND ent_raisoc='$clientligneFact' AND lig_typeligne='honos' AND";
				$stmt_ligfacttaxes .="EXTRACT(YEAR FROM fac_date) AS annees FROM lignefacture JOIN facture ON lignefacture.lig_rf_fac=facture.fac_id JOIN entite ON facture.fac_rf_ent=entite.ent_id WHERE fac_pole=$entite AND ent_raisoc='$clientligneFact' AND lig_typeligne='taxes' AND";
				$stmt_ligfactfrais .="EXTRACT(YEAR FROM fac_date) AS annees FROM lignefacture JOIN facture ON lignefacture.lig_rf_fac=facture.fac_id JOIN entite ON facture.fac_rf_ent=entite.ent_id WHERE fac_pole=$entite AND ent_raisoc='$clientligneFact' AND lig_typeligne='frais' AND";
			}else{
				$stmt_ligfacthonos .="EXTRACT(YEAR FROM fac_date) AS annees FROM lignefacture JOIN facture ON lignefacture.lig_rf_fac=facture.fac_id WHERE fac_pole=$entite AND lig_typeligne='honos' AND";
				$stmt_ligfacttaxes .="EXTRACT(YEAR FROM fac_date) AS annees FROM lignefacture JOIN facture ON lignefacture.lig_rf_fac=facture.fac_id WHERE fac_pole=$entite AND lig_typeligne='taxes' AND";
				$stmt_ligfactfrais .="EXTRACT(YEAR FROM fac_date) AS annees FROM lignefacture JOIN facture ON lignefacture.lig_rf_fac=facture.fac_id WHERE fac_pole=$entite AND lig_typeligne='frais' AND";
			}
			for ($a=0; $a<$tailleAnnees ; $a++){ 
				if($a<=($tailleAnnees-2) && $tailleAnnees!=1){
					$stmt_ligfacthonos .=" EXTRACT(YEAR FROM fac_date)=$periode[$a] OR";
					$stmt_ligfacttaxes .=" EXTRACT(YEAR FROM fac_date)=$periode[$a] OR";
					$stmt_ligfactfrais .=" EXTRACT(YEAR FROM fac_date)=$periode[$a] OR";
				}
				else{
					$stmt_ligfacthonos .=" EXTRACT(YEAR FROM fac_date)=$periode[$a] ";
					$stmt_ligfacttaxes .=" EXTRACT(YEAR FROM fac_date)=$periode[$a] ";
					$stmt_ligfactfrais .=" EXTRACT(YEAR FROM fac_date)=$periode[$a] ";
				}
			}
			$stmt_ligfacthonos .=" GROUP BY annees ORDER BY annees";
			$stmt_ligfacttaxes .=" GROUP BY annees ORDER BY annees";
			$stmt_ligfactfrais .=" GROUP BY annees ORDER BY annees";
		
			$result_ligfacthonos = $this->getPdo()->prepare($stmt_ligfacthonos);
			$result_ligfacthonos->execute();
			array_push($tableauCSV, array("Honoraires par annee"));
			foreach($result_ligfacthonos->fetchAll(PDO::FETCH_OBJ) as $lighonos) {
				$tableauh=array($lighonos->annees=>$lighonos->honos);
				array_push($this->tableauHonos,$tableauh);
				array_push($tableauCSV,$tableauh);	
			}

			$result_ligfacttaxes = $this->getPdo()->prepare($stmt_ligfacttaxes);
			$result_ligfacttaxes->execute();
			array_push($tableauCSV, array("Taxes par annee"));
			foreach($result_ligfacttaxes->fetchAll(PDO::FETCH_OBJ) as $ligtaxes) {
				$tableaut=array($ligtaxes->annees=>$ligtaxes->taxes);
				array_push($this->tableauTaxes,$tableaut);				
				array_push($tableauCSV,$tableaut);
			}

			$result_ligfactfrais = $this->getPdo()->prepare($stmt_ligfactfrais);
			$result_ligfactfrais->execute();
			array_push($tableauCSV, array("Frais par annee"));
			foreach($result_ligfactfrais->fetchAll(PDO::FETCH_OBJ) as $ligfrais) {
				$tableauf=array($ligfrais->annees=>$ligfrais->frais);
				array_push($this->tableauFrais,$tableauf);				
				array_push($tableauCSV,$tableauf);
			}

		}//fin du calcul par annéethis->


		//on verifie si on doit créer ou non le fichier csv
		if ($this->exportCSV !="non") {
				$this->pdoToCsv("Table Facture", $this->nomImage, $tableauCSV);
		}	
	}//fin de la methode de calcul des montants de la table ligne de facture  

	//On calcule les montants des honos, des taxes et des frais de la table facture 
    public function calculRepartition(){
		$periode=$_POST['repart_periode'];
		$utilisateurs=$_POST['util_entite'];
		$tailleUtil=count($utilisateurs);
		$tableauCSV=array();	

		$stmt_repart = "SELECT SUM(fac_montantht*rep_pourcentage/100) AS montant, uti_nom FROM repartition JOIN facture ON repartition.rep_rf_fac=facture.fac_id JOIN utilisateur ON repartition.rep_rf_uti=utilisateur.uti_id WHERE ";
		
		//On effectue le calcul uniquement pour 1->jour => 1->mois=> 1->annee donnés
		if($this->regroupement=="jour"){
			$stmt_repart .=" EXTRACT(YEAR FROM fac_date)=".$periode[0]." AND EXTRACT(DAY FROM fac_date)=".$this->jours[0]." AND EXTRACT(MONTH FROM fac_date)=".$this->moisForm[0]; 
			array_push($tableauCSV, array("jour"));
		}

		//On effectue le calcul uniquement pour 1->semaine=>1->annee donnés
		else if($this->regroupement=="semaine"){
			$stmt_repart .=" EXTRACT(YEAR FROM fac_date)=".$periode[0]." AND EXTRACT(WEEK FROM fac_date)=".$this->semaine[0]; 
			array_push($tableauCSV, array("semaine"));
		}

		//On effectue le calcul uniquement pour 1->mois=> 1->annee donnés
		else if($this->regroupement=="mois"){
			$stmt_repart .=" EXTRACT(YEAR FROM fac_date)=".$periode[0]." AND EXTRACT(MONTH FROM fac_date)=".$this->moisForm[0]; 
			array_push($tableauCSV, array("mois"));
		}

		//On effectue le calcul uniquement pour 1->annee donnée
		else if($this->regroupement=="annee"){
			$stmt_repart .=" EXTRACT(YEAR FROM fac_date)=$periode[0]";	
			array_push($tableauCSV, array("annee"));	
		}
		if($utilisateurs[0]!="tous"){ 	
			$stmt_repart .=" AND ";
			for ($m=0; $m<$tailleUtil ; $m++) { 
				if($m<=($tailleUtil-2) && $tailleUtil!=1){
					$stmt_repart .=" uti_id='$utilisateurs[$m]' OR";
				}
				else{
					$stmt_repart .=" uti_id='$utilisateurs[$m]' ";
				}
			}
		}

		$stmt_repart .=" GROUP BY uti_nom ORDER BY uti_nom";
		$result_repart = $this->getPdo()->prepare($stmt_repart);
		$result_repart->execute();		
		foreach($result_repart->fetchAll(PDO::FETCH_OBJ) as $repart) {
			$tableau=array($repart->uti_nom=>round($repart->montant));
			array_push($this->tableauRepartMontant,$tableau);
			array_push($tableauCSV,$tableau);
		}

		//on verifie si on doit créer ou non le fichier csv
		if ($this->exportCSV !="non") {
				$this->pdoToCsv("Table Facture", $this->nomImage, $tableauCSV);
		}	
	}//fin de la fonction sur le calcul des montants de la table repartition

    //le getter qui retourne le nom du diagramme et de l'image
    public function getNomImage(){
        return $this->nomImage;
    }

    //le getter qui retourne le nom de la table
    public function getNom_table(){
        return $this->nom_table;
    }

    //le getter qui retourne le contenu du tableau tableauHonos
    public function getTableauHonos(){
        return $this->tableauHonos;
    }
    //le getter qui retourne le contenu du tableau tableauTaxes
    public function getTableauTaxes(){
        return $this->tableauTaxes;
    }
    //le getter qui retourne le contenu du tableau tableauFrais
    public function getTableauFrais(){
        return $this->tableauFrais;
    }

    //le getter qui retourne le contenu du tableau tableauMontAchats
    public function getTableauMontAchats(){
        return $this->tableauMontAchats;
    }

    //le getter qui retourne le contenu du tableau tableauNbreAchats
    public function getTableauNbreAchats(){
        return $this->tableauNbreAchats;
    }

    //le getter qui retourne le contenu du tableau tableauRepartMontant
    public function getTableauRepartMontant(){
        return $this->tableauRepartMontant;
    }

    //le getter qui retourne le contenu du tableau tableauRepartMontant
    public function getJour(){
        return $this->jours;
    }

    //getteur pour retourne la connexion
    public function getPdo(){
        return $this->pdo;
    }

}

/**
*Cette classe permet de creer des histogrammes suivant les 4 tables: achat, facture, ligneFacture, 
*et repartition.
*/
class CreateHistogram{
		
	private $myData;
	private $requete;//instance de la RequeteDiagram pour récuperer les requetes

	function __construct(){
		$this->myData = new pData();/* On crée un nouvel objet contenant mes données pour le graphique */
		$this->requete = new RequeteDiagram(); 
		//print_r($this->requetegetTableauNbreAchats());
		
	}

	public function creationDiagram(){
		/**
		 *appel de la methode qui permet d'execution des requetes de la table achat
		 *de la classe RequeteDiagram
		 */
		//le tableau contenant le nombre des achats
		if ($this->requete->getNom_table()=="achat") {
				$this->requete->calculAchat();
				$tailleNbAchat = count($this->requete->getTableauNbreAchats());
			 	$keyNb=array();
			    $valueNb=array();
				 for ($n=0; $n<$tailleNbAchat; $n++){ 
				 	$index=key($this->requete->getTableauNbreAchats()[$n]);
				 	array_push($valueNb, $this->requete->getTableauNbreAchats()[$n][$index]);
				 	array_push($keyNb, $index);
				 }
				 
				 //le tableau contenant les montants des achats
				 $tailleMontAchat = count($this->requete->getTableauMontAchats());
				 $keyMont=array();
				 $valueMont=array();
				 for ($m=0; $m<$tailleMontAchat; $m++){ 
				 	$index=key($this->requete->getTableauMontAchats()[$m]);
				 	array_push($valueMont, $this->requete->getTableauMontAchats()[$m][$index]);
				 	array_push($keyMont, $index);
				 }
				 //creation de la 1ere serie representant les nombres des achats
				 $this->myData->addPoints($valueNb,"Serie1");
				 $this->myData->setSerieDescription("Serie1","Nbres Achats");
				 $this->myData->setSerieOnAxis("Serie1",0);

				//creation de la 1ere serie representant les montants des achats
				 $this->myData->addPoints($valueMont,"Serie2");
				 $this->myData->setSerieDescription("Serie2","Monts Achats");
				 $this->myData->setSerieOnAxis("Serie2",0);

				 /*On indique les données horizontales du graphique. Il doit y avoir le même nombre que pour ma série de données précédentes (logique)*/
				 $this->myData->addPoints($keyNb,"Absissa");
				 $this->myData->setAbscissa("Absissa");
		}//fin de la boucle if sur la partie achat


		 /**
		 *appel de la methode qui permet d'execution des requetes de la table facture
		 *de la classe RequeteDiagram
		 */
		 else if ($this->requete->getNom_table()=="facture"){
			 $this->requete->calculFacture();
			 $tailleFact= count($this->requete->getTableauHonos());
			 $tabHonos=array();
			 $tabTaxes=array();
			 $tabFrais=array();
			 $tabindice=array();
			 for ($i=0; $i<$tailleFact; $i++) { 
			 	$index=key($this->requete->getTableauHonos()[$i]);
			 	array_push($tabHonos, $this->requete->getTableauHonos()[$i][$index]);
			 	array_push($tabTaxes, $this->requete->getTableauTaxes()[$i][$index]);
			 	array_push($tabFrais, $this->requete->getTableauFrais()[$i][$index]);
			 	array_push($tabindice, $index);
			 	
			 }
			 /*On présente la série de données à utiliser pour le graphique et je détermine le titre de l'axe vertical avec setAxisName*/  
			 $this->myData->addPoints($tabHonos,"Serie1");
			 $this->myData->setSerieDescription("Serie1","Honos");
			 $this->myData->setSerieOnAxis("Serie1",0);

			 $this->myData->addPoints($tabFrais,"Serie2");
			 $this->myData->setSerieDescription("Serie2","Frais");
			 $this->myData->setSerieOnAxis("Serie2",0);

			 $this->myData->addPoints($tabTaxes,"Serie3");
			 $this->myData->setSerieDescription("Serie3","Taxes");
			 $this->myData->setSerieOnAxis("Serie3",0);
			 //pour définir la couleur de la serie Taxes
			 $this->myData->setPalette("Serie3",array("R"=>0,"G"=>225,"B"=>255));

			 /*On indique les données horizontales du graphique. Il doit y avoir le même nombre que pour ma série de données précédentes (logique)*/
			 $this->myData->addPoints($tabindice,"Absissa");
			 $this->myData->setAbscissa("Absissa");

		 }//fin de la boucle sur la partie facture

		 /**
		 *appel de la methode qui permet d'execution des requetes de la table ligne de facture
		 *de la classe RequeteDiagram
		 */
		 else if ($this->requete->getNom_table()=="lignefacture"){
			 $this->requete->calculLigneFacture();
			 $tailleligneFact= count($this->requete->getTableauHonos());
			 $tabHonos=array();
			 $tabTaxes=array();
			 $tabFrais=array();
			 $tabindice=array();
			 for ($i=0; $i<$tailleligneFact; $i++) { 
			 	$index=key($this->requete->getTableauHonos()[$i]);
			 	array_push($tabHonos, $this->requete->getTableauHonos()[$i][$index]);
			 	array_push($tabTaxes, $this->requete->getTableauTaxes()[$i][$index]);
			 	array_push($tabFrais, $this->requete->getTableauFrais()[$i][$index]);
			 	array_push($tabindice, $index);
			 	
			 }
			 /*On présente la série de données à utiliser pour le graphique et je détermine le titre de l'axe vertical avec setAxisName*/  
			 $this->myData->addPoints($tabHonos,"Serie1");
			 $this->myData->setSerieDescription("Serie1","Honos");
			 $this->myData->setSerieOnAxis("Serie1",0);

			 $this->myData->addPoints($tabFrais,"Serie2");
			 $this->myData->setSerieDescription("Serie2","Frais");
			 $this->myData->setSerieOnAxis("Serie2",0);

			 $this->myData->addPoints($tabTaxes,"Serie3");
			 $this->myData->setSerieDescription("Serie3","Taxes");
			 $this->myData->setSerieOnAxis("Serie3",0);
			 //pour définir la couleur de la serie Taxes
			 $this->myData->setPalette("Serie3",array("R"=>0,"G"=>225,"B"=>255));

			 /*On indique les données horizontales du graphique. Il doit y avoir le même nombre que pour ma série de données précédentes (logique)*/
			 $this->myData->addPoints($tabindice,"Absissa");
			 $this->myData->setAbscissa("Absissa");

		 }//fin de la boucle if sur la partie ligne de facture.

		/**
		 *appel de la methode qui permet d'execution des requetes de la table repartition
		 *de la classe RequeteDiagram
		 */
		else if ($this->requete->getNom_table()=="repartition"){
			 $this->requete->calculRepartition();
			 $tailleRepart= count($this->requete->getTableauRepartMontant());
			 $tabRepart=array();
			 $tabindiceRepart=array();
			 for ($r=0; $r<$tailleRepart; $r++) { 
			 	$index=key($this->requete->getTableauRepartMontant()[$r]);
			 	array_push($tabRepart, $this->requete->getTableauRepartMontant()[$r][$index]);
			 	array_push($tabindiceRepart,$index);			 	
			 }
			 $this->myData->addPoints($tabRepart,"Server");
			 $this->myData->setSerieDescription("Server", "Users");
			  
			 /*On indique les données horizontales du graphique. Il doit y avoir le même nombre que pour ma série de données précédentes (logique)*/
			 $this->myData->addPoints($tabindiceRepart,"contents");
			 $this->myData->setSerieDescription("contents","content");
			 $this->myData->setAbscissa("contents");
		 }

		 //le reste de la creation du diagrammes
		 $this->myData->setAxisPosition(0,AXIS_POSITION_LEFT);
		 $this->myData->setAxisName(0,"Chiffre d'affaires");
		 $this->myData->setAxisUnit(0,"");

		 /* On crée l'image qui contiendra mon graphique précédemment crée */
		 $myPicture = new pImage(1200,550,$this->myData);
		 $Settings = array("R"=>255, "G"=>255, "B"=>255, "Dash"=>1, "DashR"=>275, "DashG"=>275, "DashB"=>275);
		 $myPicture->drawFilledRectangle(0,0,1200,550,$Settings);

		 //permet de modifier la couleur d'arriere du diagramme
		 //$Settings = array("StartR"=>224, "StartG"=>228, "StartB"=>231, "EndR"=>230, "EndG"=>238, "EndB"=>250, "Alpha"=>50);
		 $Settings = array("StartR"=>224, "StartG"=>228, "StartB"=>231, "EndR"=>230, "EndG"=>238, "EndB"=>250, "Alpha"=>80);
		 $myPicture->drawGradientArea(0,0,1200,550,DIRECTION_VERTICAL,$Settings);

		 /* Je crée une bordure à mon image */
		 $myPicture->drawRectangle(0,0,1199,549,array("R"=>0,"G"=>0,"B"=>0));

		 $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>50,"G"=>50,"B"=>50,"Alpha"=>20));

		 /* J'indique le titre de mon graphique, son positionnement sur l'image et sa police */ 
		 $myPicture->setFontProperties(array("FontName"=>"fonts/calibri.ttf","FontSize"=>14));
		 $TextSettings = array("Align"=>TEXT_ALIGN_TOPLEFT, "R"=>9, "G"=>25, "B"=>31, "DrawBox"=>1, "BoxAlpha"=>30);
		 $myPicture->drawText(600,25,"Cabinet Vidon",$TextSettings);

		 $myPicture->setShadow(FALSE);
		 $myPicture->setGraphArea(80,50,1175,510);
		 $myPicture->setFontProperties(array("R"=>0,"G"=>0,"B"=>0,"FontName"=>"fonts/verdana.ttf","FontSize"=>8));

		 $Settings = array("Pos"=>SCALE_POS_LEFTRIGHT, "Mode"=>SCALE_MODE_ADDALL_START0, "LabelingMethod"=>LABELING_ALL
		 , "GridR"=>255, "GridG"=>255, "GridB"=>255, "GridAlpha"=>50, "TickR"=>0, "TickG"=>0, "TickB"=>0, "TickAlpha"=>50, "LabelRotation"=>0, "LabelSkip"=>1, "CycleBackground"=>1, "DrawXLines"=>1, "DrawSubTicks"=>1, "SubTickR"=>255, "SubTickG"=>0, "SubTickB"=>0, "SubTickAlpha"=>50, "DrawYLines"=>ALL);
		 $myPicture->drawScale($Settings);

		 $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>50,"G"=>50,"B"=>50,"Alpha"=>10));

		 $Config = array("DisplayValues"=>1, "Gradient"=>1, "AroundZero"=>1);
		 $myPicture->drawStackedBarChart($Config);

		 $Config = array("FontR"=>0, "FontG"=>0, "FontB"=>0, "FontName"=>"fonts/Bedizen.ttf", "FontSize"=>8, "Margin"=>6, "Alpha"=>30, "BoxSize"=>5, "Style"=>LEGEND_NOBORDER, "Mode"=>LEGEND_HORIZONTAL);
		 $myPicture->drawLegend(1062,16,$Config);

		 $myPicture->stroke();

		 /* J'indique le chemin où je souhaite que mon image soit créée */
		 $myPicture->Render("img/".$this->requete->getNomImage().".png");

	}//fin de la fonction creationDiagram

}// fin de la classe CreateHistogram
$instanceCreateHistogram=new CreateHistogram();
$instanceCreateHistogram->creationDiagram();
/*$froufrou = new RequeteDiagram();
$froufrou->caluclAchat();*/ //affiche "4"
//var_dump($froufrou->caluclFacture());
//var_dump($froufrou->caluclLigneFacture());
//var_dump($froufrou->caluclRepartition());
/*var_dump($froufrou->getTableauNbreAchats());
var_dump($froufrou->getTableauMontAchats());*/






?>

