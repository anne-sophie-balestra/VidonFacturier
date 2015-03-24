<?php
require_once("header.php");
require_once("modeleFacture.php");
$id=$_GET['id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Facture</title>
  
  
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
  <meta name="description" content="">
  <meta name="author" content="">
	
	
	
	<script type="text/javascript">


	function ajouterLigne()
	{
		var tableau = document.getElementById("tableau_facture");

		var ligne = tableau.insertRow(-1);//on a ajouté une ligne

		var colonne1 = ligne.insertCell(0);//on a une ajouté une cellule
		colonne1.innerHTML += document.getElementById("prestation_text").value;//on y met le contenu de titre

		var colonne2 = ligne.insertCell(1);//on ajoute la seconde cellule
		colonne2.innerHTML += document.getElementById("libelle_text").value;


		var date=new Date();

		var colonne3 = ligne.insertCell(2);
	 
		colonne3.innerHTML +=date.getDate()+'/'+(date.getMonth()+1)+'/'+date.getFullYear(); 
		

		var colonne4 = ligne.insertCell(3);//on ajoute la seconde cellule
		colonne4.innerHTML += document.getElementById("montant_text").value;

		var colonne5 = ligne.insertCell(4);//on ajoute la seconde cellule
		colonne5.innerHTML += document.getElementById("qte_text").value;

		var colonne6 = ligne.insertCell(5);//on ajoute la seconde cellule
		colonne6.innerHTML += document.getElementById("montant_text").value*document.getElementById("qte_text").value;
		
		 parent.document.getElementById('annuler_bouton').click();
		

	}






	</script>
	
	
	
	
	
	
	
	
	
	
	
	
</head>

<body>
<div class="container">

<div class="row clearfix">
		<div class="col-md-10 column">
			<h2 class="text-center text-danger">
					Facture
			</h2>
		</div>
	</div>
	<div class="row clearfix">
		<div class="col-md-10 column">
			<h3>Informations sur le Dossier</h3>
			<table class="table table-bordered table-striped table-condensed">
				<tbody>
					<tr class="success">
						<td >
						Type de Dossier
						</td>
						<td>Date du Dossier
						</td>
						<td>
							Objet du Dossier
						</td>
						<td>
							Client
						</td>
					</tr>
					<tr class="active">
					<?php 
					$dos=generateElementDossier($id);
					foreach ($dos as $row)
					{
					?>
						<td>
							<?php echo $row->dos_type ;?>
						</td>
						<td>
							<?php echo $row->dos_creadate ;?>
						</td>
						<td>
							<?php echo $row->dos_titre ;?>
						</td>
						<td class="col-lg-1">
						<?php echo $row->dos_titulaire_saisi ;?>
						</td>
					</tr>
					<?php }?>
	
				</tbody>
			</table>
			<h3>
			</h3>
		</div>
	</div>
	<div class="row clearfix">
		<div class="col-md-10 column">
			<h3>Lignes de Factures
			</h3>
			<table class="table  table-striped table-condensed table-hover" id="tableau_facture">
				<thead>
					<tr class="warning">
						<th class="col-lg-1">
						Code
						</th >
						<th class="col-lg-2">
						Libellé
						</th >
						<th class="col-lg-1">
							Date
						</th>
						<th class="col-lg-2">
							Montant	
						</th>
						<th class="col-lg-1">
						Qte
						</th>
						<th class="col-lg-1">
				Total
						</th>
						<th class="col-lg-1">
					
						</th>
						<th class="col-lg-1">
				
						</th>
						
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="col-lg-1">
							1
						</td>
						<td class="col-lg-2">
					Dépot de Brevet Européen
						</td>
						<td class="col-lg-1">
							01/04/2012
						</td>
						<td class="col-lg-2">
							200
						</td>
						<td class="col-lg-1">
							1
						</td>
						<td class="col-lg-1">
							200
						</td>
						<td class="col-lg-1">
						<a href="#"><em class="glyphicon glyphicon-pencil"></em></a>
						</td>
						<td class="col-lg-1">
						<a href="#"><em class="glyphicon glyphicon-remove-sign"></em></a>
						</td>
						
					</tr>
					<tr>
						<td class="col-lg-1">
							1
						</td>
						<td class="col-lg-2">
					Dépot de Brevet Européen
						</td>
						<td class="col-lg-1">
							01/04/2012
						</td>
						<td class="col-lg-2">
							200
						</td>
						<td class="col-lg-1">
							1
						</td>
						<td class="col-lg-1">
							200
						</td>
						<td class="col-lg-1">
						<a href="#"><em class="glyphicon glyphicon-pencil"></em></a>
						</td>
						<td class="col-lg-1">
						<a href="#"><em class="glyphicon glyphicon-remove-sign"></em></a>
						</td>
						
					</tr>
				</tbody>
			</table>
		</div>
	</div>	
		<div class="row clearfix">
		
	<button type="button" class="btn btn-warning " data-toggle="modal"
   data-target="#largeModal"><em class="glyphicon glyphicon-plus-sign">Ajouter une Ligne de Facture</em></button>
	
 
    <div id="largeModal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">Ajout une Ligne de Facture</h4>
                </div>
                <div class="modal-body">
                    
                    
                    <form class="form-horizontal" role="form">
						<div class="form-group">
							 <label for="inputEmail3" class="col-sm-3 control-label">Code de la Prestation</label>
							<div class="col-sm-9">
								<input class="form-control" id="prestation_text" type="text" />
							</div>
						</div>
						<div class="form-group">
							 <label for="libelle" class="col-sm-3 control-label">Libelle</label>
							<div class="col-sm-9">
								<input class="form-control" id="libelle_text" type="text" />
							</div>
						</div>
						
						<div class="form-group">
							 <label for="Montant" class="col-sm-3 control-label">Montant</label>
							<div class="col-sm-9">
								<input class="form-control" id="montant_text" type="text" />
							</div>
						</div>
						
						<div class="form-group">
							 <label for="Qte" class="col-sm-3 control-label">Qte</label>
							<div class="col-sm-9">
								<input class="form-control" id="qte_text" type="text" />
							</div>
						</div>
						
						<div class="form-group">
							 <label for="inputPassword3" class="col-sm-3 control-label">Total</label>
							<div class="col-sm-9">
								<input class="form-control" id="total_text" type="text" />
							</div>
						</div>
						
						
						
					</form>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" id="annuler_bouton" class="btn btn-default" data-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-success" onclick="ajouterLigne();">Ajouter</button>
                </div>
            </div>
        </div>
    </div>	
	
	
	
	
	
	
	
	
	
	
	
  
</div>
		
		
		
		
		
		
		
		
		
		
		
		
		
		
	</div>
	<div class="row clearfix">
		<div class="col-md-10 column">
			<h3>Achats
			</h3>
			<table class="table table-hover table-condensed">
				<thead>
					<tr class="warning">
						<th class="col-lg-1">
						Code
						</th>
						<th class="col-lg-2">
						Libellé
						</th>
						<th class="col-lg-1">
							Date
						</th>
						<th class="col-lg-2">
							Montant	
						</th>
						<th class="col-lg-1">
						Qte
						</th>
						<th class="col-lg-1">
					Total
						</th>
						<th class="col-lg-1">
				
						</th>
						<th class="default-col-lg-1">
					
						</th>
						
					</tr>
				</thead>
				<tbody>
					<tr >
						<td class="col-lg-1">
							2
						</td>
						<td class="col-lg-2">
				Taxe d'Habitation
						</td>
						<td class="col-lg-1">
							01/04/2012
						</td>
						<td class="col-lg-2">
							200
						</td>
						<td class="col-lg-1">
							1
						</td>
						<td class="col-lg-1">
							200
						</td>
						<td class="col-lg-1">
						<a href="#"><em class="glyphicon glyphicon-pencil"></em></a>
						</td>
						<td class="col-lg-1">
						<a href="#"><em class="glyphicon glyphicon-remove-sign"></em></a>
						</td>
					</tr>
					<tr class="warning">
						<td class="col-lg-1">
							2
						</td>
						<td class="col-lg-2">
				Taxe d'Habitation
						</td>
						<td class="col-lg-1">
							01/04/2012
						</td>
						<td class="col-lg-2">
							200
						</td>
						<td class="col-lg-1">
							1
						</td>
						<td class="col-lg-1">
							200
						</td>
						<td class="col-lg-1">
						<a href="#"><em class="glyphicon glyphicon-pencil"></em></a>
						</td>
						<td class="col-lg-1">
						<a href="#"><em class="glyphicon glyphicon-remove-sign"></em></a>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		</div>
			
<div class="row clearfix">
		<div class="col-md-10 column">
	<button type="button" class="btn btn-warning"><em class="glyphicon glyphicon-plus-sign">Ajouter un Achat</em></button>
		</div>
</div>
	<div class="row clearfix">
		<div class="col-md-10 column">
		
	<form class="form-horizontal" id="container">
	

<legend class="row">Editer une facture</legend>
  <div class="form-group">
  
  <div class="controls form-inline">
<label class="control-label col-sm-1" for="Type">Type</label>



<!-- Text input-->
<div class="row">
  <div class="col-xs-2">
   <select class="form-control col-sm-5">
  <option>Facture</option>
  <option>Avoir</option>
  
  
</select>
  </div>
  <div class="col-xs-4">
    <select class="form-control col-sm-5">
  <option>Facture avec Proforma</option>
  <option>Proforma Valide</option>
  <option>Proforma</option>
  <option>Proforma</option>
  
</select>
  </div>
  
</div>



</div>
</div>
<div class="form-group">
  <div class="controls form-inline">
<label class="control-label col-sm-1" for="Object">Objet</label>
<div class="row">
<div class="col-xs-6">

<textarea class="form-control" rows="3" cols="52" id="comment"></textarea>
</div>
</div> 
</div>
</div>
  <div class="row clearfix col-xs-offset-4">
 <div class="form-group">
 
 <div class="controls form-inline">
  <div class="col-lg-offset-6 col-lg-3 col-xs-offset-5 input-group"> 
    <span class="input-group-addon">€</span>
    <input type="text" class="form-control" style="text-align:right" value="100">
   
  </div>

 
  <label class="control-label col-lg-2" style="text-align:left" for="montant">Montant HT</label>
 
  </div>
 </div>
 </div> 
 
  <div class="row clearfix col-xs-offset-4">
 <div class="form-group">
 
 <div class="controls form-inline">
  <div class="col-lg-offset-6 col-lg-3 input-group"> 
    <span class="input-group-addon">%</span>
    <input type="text" class="form-control" style="text-align:right" value="19.6">
   
  </div>
  <label class="control-label col-lg-2" style="text-align:left" for="montant">TVA</label>
 
  </div>
 </div>
 </div>
 
  <div class="row clearfix">
 <div class="form-group">

 <div class="controls form-inline">
<div class=" col-lg-offset-4 col-lg-3 input-group">
 <div class="radio">
      <label><input type="radio" name="optradio">Honoraires</label>
    </div>
    <div class="radio">
      <label><input type="radio" name="optradio">Frais</label>
    </div>
 <div class="radio">
      <label><input type="radio" name="optradio">Taxes</label>
    </div>
    
</div>

<div class=" col-lg-offset-2 col-lg-2 input-group"> 
    <span class="input-group-addon">€</span>
    <input type="text" class="form-control" style="text-align:right" value="100">
   
  </div>

</div>
</div>
</div>

  <div class="row clearfix col-xs-offset-4">
 <div class="form-group">
 
 <div class="controls form-inline">
  <div class="col-lg-offset-5 col-lg-3 col-xs-offset-5 input-group"> 
    <span class="input-group-addon">€</span>
    <input type="text" class="form-control" style="text-align:right" value="100">
   
  </div>

 
  <label class="control-label col-lg-3" style="text-align:left" for="montant">Montant TTC</label>
 
  </div>
 </div>
 </div>
  
  
  
 

 <div class="form-group">
<div class="col-sm-offset-4 col-sm-10">
        <button type="submit" class="btn btn-default">Annuler</button>
        <button type="submit" class="btn btn-default">Enregister</button>
        <button type="submit" class="btn btn-default">Valider</button>
      </div>
 
 </div> 
		
		
</form>
</div>
</div>
</div>
</body>
</html>
