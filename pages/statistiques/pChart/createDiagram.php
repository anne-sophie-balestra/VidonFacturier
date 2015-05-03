<?php
/********************************************
 * listeDossiers.php                         *
 * Affiche tous les dossiers en liste        *
 *                                           *
 * Auteurs : Anne-Sophie Balestra            *
 *           Abdoul Wahab Haidara            *
 *           Yvan-Christian Maso             *
 *           Baptiste Quere                  *
 *           Yoann Le Taillanter             *
 *                                           *
 * Date de creation : 08/04/2015             *
 ********************************************/
$pdo = new SPDO();

//On recupere toutes les années de la table achat
$stmt_achat_annees = "SELECT DISTINCT(EXTRACT(YEAR FROM ach_dateffo)) AS annee FROM achat ORDER BY annee ASC";
$result_achat_annees = $pdo->prepare($stmt_achat_annees);
$result_achat_annees->execute();

//On recupere toutes les années de la table facture 
$stmt_fac_annees = "SELECT DISTINCT(EXTRACT(YEAR FROM fac_date)) AS annee FROM facture ORDER BY annee ASC";
$result_fac_annees = $pdo->prepare($stmt_fac_annees);
$result_fac_annees->execute();

//On recupere toutes les années de la table ligne de facture 
$stmt_ligfac_annees = "SELECT DISTINCT(EXTRACT(YEAR FROM lig_creadate)) AS annee FROM lignefacture ORDER BY annee ASC";
$result_ligfac_annees = $pdo->prepare($stmt_ligfac_annees);
$result_ligfac_annees->execute();

//On recupere toutes les années de la table repartition
$stmt_repart_annees = "SELECT DISTINCT(EXTRACT(YEAR FROM rep_moddate)) AS annee FROM repartition ORDER BY annee ASC";
$result_repart_annees = $pdo->prepare($stmt_repart_annees);
$result_repart_annees->execute();

//***************************************************************************************************************
//On recupere toutes les entites de la table achat
$stmt_achat_entites = "SELECT DISTINCT(ent_raisoc), ach_rf_ent FROM achat JOIN entite ON achat.ach_rf_ent = entite.ent_id ORDER BY ent_raisoc ASC";
$result_achat_entites = $pdo->prepare($stmt_achat_entites);
$result_achat_entites->execute();

//On recupere toutes les entités clientes de la table facture 
$stmt_fac_clients = "SELECT DISTINCT(ent_raisoc), fac_rf_ent FROM facture JOIN entite ON facture.fac_rf_ent=entite.ent_id ORDER BY ent_raisoc ASC";
$result_fac_clients = $pdo->prepare($stmt_fac_clients);
$result_fac_clients->execute();

//On recupere toutes les entites clientes de la table ligne de facture 
$stmt_ligfac_clients = "SELECT DISTINCT (ent_raisoc) FROM lignefacture JOIN facture ON lignefacture.lig_rf_fac=facture.fac_id JOIN entite ON facture.fac_rf_ent=entite.ent_id ORDER BY ent_raisoc ASC ";
$result_ligfac_clients = $pdo->prepare($stmt_ligfac_clients);
$result_ligfac_clients->execute();

//On recupere toutes les entites de la table repartition
$stmt_repart_entites = "SELECT DISTINCT uti_id, uti_nom,uti_prenom FROM repartition JOIN utilisateur ON repartition.rep_rf_uti =utilisateur.uti_id ORDER BY uti_nom,uti_prenom ASC";
$result_repart_entites = $pdo->prepare($stmt_repart_entites);
$result_repart_entites->execute();


//echo mt_rand(1000, 10000);

?>
<!-- Contenu principal de la page -->
<div class="container">
<div class="col-md-10">

    <!--Creation d'un formulaire avec la validation Bootstrap-->
<!--     //pages/statistiques/pChart/createHistogramme.php  requeteCreateDiagram
BDD/testMultiSelect.php
-->   
    <form id="formNewModel" action="pages/statistiques/pChart/requeteCreateDiagram.php" method="post" role="form" data-toggle="validator">
        <h2>Création de diagramme histogramme</h2>

        <div class="form-group">
            <label class="control-label" for="nom_histo">Nom de l'histogramme :</label><br/>
            <input type="text" name="nom_histo" id="nom_histo" class="form-control" required placeholder="Donnez un nom au diagramme...">
        </div>
        <div class="form-group">
            <label class="control-label" for="export_csv">Exporter en CSV :</label><br/>
            <select name="export_csv" id="export_csv" required class="form-control">
                <option value="oui">Oui</option>
                <option value="non" selected>Non</option>                
            </select>
        </div>
        
        <div class="form-group">
            <label class="control-label" for="nom_table">Nom de la table :</label><br/>
            <select name="nom_table" id="nom_table" required class="form-control" onchange="afficherPeriode(this.value);">
                <option value="tous"></option>
                <option value="achat">Achat</option>
                <option value="facture">Facture</option>
                <option value="lignefacture">Ligne de facture</option>
                <option value="repartition">Repartition</option>
            </select>
        </div>
        <div class="form-group" id="correspondant" style="display: none;">
            <label class="control-label" for="corresp_entite">Fournisseur :</label>
            <select name="corresp_entite" id="corresp_entite" class="form-control">
                 <option value="tous"></option>
                 <?php
                foreach($result_achat_entites->fetchAll(PDO::FETCH_OBJ) as $entiteach) { ?>
                <option value="<?php echo $entiteach->ach_rf_ent; ?>"><?php echo $entiteach->ent_raisoc; ?></option>
                <?php } ?>
             </select>
        </div>  
        <div class="form-group" id="ent_vidon" style="display: none;">
            <label class="control-label" for="entiteVidon">Entite Vidon :</label>
            <select name="entiteVidon" id="entiteVidon" class="form-control">
                 <option value="0" selected>Brevet et Stratégie</option>
                 <option value="1">Marque et Jurique PI </option>
                 <option value="2">Holding (CPV)</option>
             </select>
        </div>
        <div class="form-group" id="cli_fac" style="display: none;">
            <label class="control-label" for="fac_client">Entite client facture :</label>
            <select name="fac_client" id="fac_client" class="form-control">
                <option value="tous"></option>
                 <?php
                foreach($result_fac_clients->fetchAll(PDO::FETCH_OBJ) as $client) { ?>
                <option value="<?php echo $client->ent_raisoc; ?>"><?php echo $client->ent_raisoc; ?></option>
                <?php } ?>
             </select>
        </div>   

        <div class="form-group" id="cli_ligfac" style="display: none;">
            <label class="control-label" for="ligfac_client">Entite client ligne facture :</label>
            <select name="ligfac_client" id="ligfac_client" class="form-control">
                 <option value="tous"></option>
                 <?php // On affiche les années disponibles de la table facture
                foreach($result_ligfac_clients->fetchAll(PDO::FETCH_OBJ) as $client) { ?>
                <option value="<?php echo $client->ent_raisoc; ?>"><?php echo $client->ent_raisoc; ?></option>
                <?php } ?>
            </select>
        </div>         
        <div class="form-group" id="utilisateur" style="display: none;">
            <label class="control-label" for="util_entite">Utilisateur :</label>
            <select name="util_entite[]" id="util_entite" class="form-control" multiple="multiple">
                 <option value="tous"></option>
                 <?php // On affiche les années disponibles de la table facture
                foreach($result_repart_entites->fetchAll(PDO::FETCH_OBJ) as $entite) { ?>
                <option value="<?php echo $entite->uti_id; ?>"><?php echo $entite->uti_nom." ".$entite->uti_prenom; ?></option>
                <?php } ?>
             </select>
        </div>  
            
        <div class="form-group" id="type_regp" style="display: none;">
            <label class="control-label" for="nom_table">Type de regroupement :</label><br/>
            <select name="regp" id="regp" required class="form-control" onchange="afficherPeriode(this.value);">
                <option value="tous" selected></option>
                <option value="jour">Jour</option>
                <option value="semaine">Semaine</option>
                <option value="mois">Mois</option>
                <option value="annee">Année</option>
            </select>
        </div>
        <div class="form-group" id="multi_jours" style="display: none;">
            <label class="control-label" for="multijrs">Selectionner les jours :</label><br/>
            <select name="multijrs[]" id="multijrs" class="form-control" multiple="multiple">
                  <option value="1" selected>1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option>
                  <option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option>
                  <option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option>
                  <option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="18">18</option><option value="19">19</option><option value="20">20</option>
                  <option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option>
                  <option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option>
            </select>
        </div>

        <div class="form-group" id="multi_semaines" style="display: none;">
            <label class="control-label" for="nom_table">Selectionner la semaine :</label><br/>
            <select name="multisemaines[]" id="multisemaines" class="form-control" multiple="multiple">
                 <option value="1" selected>1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option>
                 <option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option>
                 <option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option>
                 <option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="18">18</option><option value="19">19</option><option value="20">20</option>
                 <option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option>
                 <option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option>
                 <option value="32">32</option><option value="33">33</option><option value="34">34</option><option value="35">35</option>
                 <option value="36">36</option><option value="37">37</option><option value="38">38</option><option value="39">39</option><option value="40">40</option>
                 <option value="41">41</option><option value="42">42</option><option value="43">43</option><option value="44">44</option><option value="45">45</option>
                 <option value="46">46</option><option value="47">47</option><option value="48">48</option><option value="49">49</option><option value="50">50</option>
                 <option value="51">51</option><option value="52">52</option>           
            </select>
        </div>

         <div class="form-group" id="multi_mois" style="display: none;">
            <label class="control-label" for="multimois">Selectionner le mois :</label><br/>
           <select name="multimois[]" id="multimois" class="form-control" multiple="multiple">
              <option value="tous"></option>
              <option value="1" selected>Janvier</option><option value="2">Février</option><option value="3">Mars</option>
              <option value="4">Avril</option><option value="5">Mai</option><option value="6">Juin</option>
              <option value="7">Juillet</option><option value="8">Août</option><option value="9">Septembre</option>
              <option value="10">Octobre</option><option value="11">Novembre</option><option value="12">Décembre</option>
          </select>
        </div>

        <!--Le mois qui sera afficher si le type d eregroupement est en jour -->
        <div class="form-group" id="pl_mois" style="display: none;">
            <label class="control-label" for="mois">Selectionner le mois :</label><br/>
           <select name="mois" id="mois" class="form-control">
              <option value="tous"></option>
              <option value="1" selected>Janvier</option><option value="2">Février</option><option value="3">Mars</option>
              <option value="4">Avril</option><option value="5">Mai</option><option value="6">Juin</option>
              <option value="7">Juillet</option><option value="8">Août</option><option value="9">Septembre</option>
              <option value="10">Octobre</option><option value="11">Novembre</option><option value="12">Décembre</option>
          </select>
        </div>

        <!-- La periode qui sera afficher lorsque le choix du type de regroupement est en mois ou année -->  
        <div class="form-group" id="p_achat" style="display: none;">
            <label class="control-label" for="achat_periode">Période achat :</label>
            <select name="achat_periode[]" id="achat_periode" class="form-control" multiple="multiple">
                 <option value="tous"></option>
                 <?php // On affiche les années disponibles de la table facture
                foreach($result_achat_annees->fetchAll(PDO::FETCH_OBJ) as $periodeach) { ?>
                <option value="<?php echo $periodeach->annee; ?>"><?php echo $periodeach->annee; ?></option>
                <?php } ?>
             </select>
        </div>

        <div class="form-group" id="p_fact" style="display: none;">
            <label class="control-label" for="fac_periode">Période facture :</label>
            <select name="fac_periode[]" id="fac_periode" class="form-control" multiple="multiple">
                 <option value="tous"></option>
                 <?php // On affiche les années disponibles de la table facture
                foreach($result_fac_annees->fetchAll(PDO::FETCH_OBJ) as $periode) { ?>
                <option value="<?php echo $periode->annee; ?>"><?php echo $periode->annee; ?></option>
                <?php } ?>
             </select>
        </div>

        
        <div class="form-group" id="p_ligfact" style="display: none;">
            <label class="control-label" for="ligfac_periode">Période ligne de facture :</label>
            <select name="ligfac_periode[]" id="ligfac_periode" class="form-control" multiple="multiple">
                 <option value="tous"></option>
                 <?php // On affiche les années disponibles de la table facture
                foreach($result_ligfac_annees->fetchAll(PDO::FETCH_OBJ) as $periode) { ?>
                <option value="<?php echo $periode->annee; ?>"><?php echo $periode->annee; ?></option>
                <?php } ?>
             </select>
        </div>
        <div class="form-group" id="p_repart" style="display: none;">
            <label class="control-label" for="repart_periode">Période repartition :</label>
            <select name="repart_periode[]" id="repart_periode" class="form-control" multiple="multiple">
                 <option value="tous"></option>
                 <?php // On affiche les années disponibles de la table facture
                foreach($result_repart_annees->fetchAll(PDO::FETCH_OBJ) as $periode) { ?>
                <option value="<?php echo $periode->annee; ?>"><?php echo $periode->annee; ?></option>
                <?php } ?>
             </select>
        </div>

        <div>
            <input type="submit" name="button" onclick="validation();" class="btn btn-success" id="button" value="Valider">
            <a href="#" onclick="history.back()" class="btn btn-danger" title="Annuler">Annuler</a>
        </div>
        
    </form>

    
</div>
</div>
<script type="text/javascript">
    function validation(){
     var selectedList = [],
         selectBox = document.getElementById("multijrs"),
         i;
  
        for (i=0; i < selectBox.length; i++) 
        {
            if (selectBox[i].selected) 
            {
                selectedList.push(selectBox[i]);
            }
        }
        //alert(selectedList.length);
        if (selectedList.length>1) {
        document.location.href="createDiagram.php";
        }
    }
</script>