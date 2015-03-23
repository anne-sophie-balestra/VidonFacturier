<?php
require_once("header.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Facture</title>
  
  <meta name="description" content="">
  <meta name="author" content="">
	<!-- <link href="css/bootstrap.min.css" rel="stylesheet"> -->	
</head>

<body>
<div class="container">

<div class="row clearfix">
		<div class="col-md-12 column">
			<h2 class="text-center text-danger">
					Facture
			</h2>
		</div>
	</div>
	<div class="row clearfix">
		<div class="col-md-11 column">
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
						<td>
							Depot
						</td>
						<td>
							08/12/2015
						</td>
						<td>
							Système de Détection des Drones
						</td>
						<td class="col-lg-1">
							EuroLogiciel
						</td>
					</tr>
					
	
				</tbody>
			</table>
			<h3>
			</h3>
		</div>
	</div>
	<div class="row clearfix">
		<div class="col-md-11 column">
			<h3>Lignes de Factures
			</h3>
			<table class="table  table-striped table-condensed table-hover">
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
		<div class="col-md-12 column">
	<button type="button" class="btn btn-warning"><em class="glyphicon glyphicon-plus-sign">Ajouter une Ligne de Facture</em></button>
		</div>
	</div>
	<div class="row clearfix">
		<div class="col-md-11 column">
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
		<div class="col-md-11 column">
	<button type="button" class="btn btn-warning"><em class="glyphicon glyphicon-plus-sign">Ajouter un Achat</em></button>
		</div>
</div>
	<div class="row clearfix">
		<div class="col-md-12 column">
		
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
  <div class="row clearfix col-xs-offset-5">
 <div class="form-group">
 
 <div class="controls form-inline">
  <div class="col-lg-offset-8 col-lg-3 col-xs-offset-5 input-group"> 
    <span class="input-group-addon">€</span>
    <input type="text" class="form-control" style="text-align:right" value="100">
   
  </div>

 
  <label class="control-label col-lg-1" style="text-align:left" for="montant">HT</label>
 
  </div>
 </div>
 </div> 
 
  <div class="row clearfix col-xs-offset-5">
 <div class="form-group">
 
 <div class="controls form-inline">
  <div class="col-lg-offset-8 col-lg-3 input-group"> 
    <span class="input-group-addon">%</span>
    <input type="text" class="form-control" style="text-align:right" value="19.6">
   
  </div>
  <label class="control-label col-lg-1" style="text-align:left" for="montant">TVA</label>
 
  </div>
 </div>
 </div>
 
  <div class="row clearfix">
 <div class="form-group">

 <div class="controls form-inline">
<div class=" col-lg-offset-7 col-lg-3 input-group">
 <div class="radio">
      <label><input type="radio" name="optradio">Honoraires</label>
    </div>
    <div class="radio">
      <label><input type="radio" name="optradio">Frais</label>
    </div>

</div>

<div class=" col-lg-2 input-group"> 
    <span class="input-group-addon">€</span>
    <input type="text" class="form-control" style="text-align:right" value="100">
   
  </div>

</div>
</div>
</div>

  <div class="row clearfix col-xs-offset-5">
 <div class="form-group">
 
 <div class="controls form-inline">
  <div class="col-lg-offset-8 col-lg-3 col-xs-offset-5 input-group"> 
    <span class="input-group-addon">€</span>
    <input type="text" class="form-control" style="text-align:right" value="100">
   
  </div>

 
  <label class="control-label col-lg-1" style="text-align:left" for="montant">TTC</label>
 
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
