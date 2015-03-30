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
  <meta name="description" content="">
  <meta name="author" content="">
	

	<script type="text/javascript">

	var i=1;
/*
 * Ajouter une ligne de facture dans le tableau
 * Ajouter un input cachés des valeurs à de la ligne de facture insérée 
 */

	function ajouterLigne()
	{ 
			
		var tableau = document.getElementById("tableau_facture");
		var arraylines=document.getElementById("tableau_facture");
		var ligne = tableau.insertRow(-1);//on a ajouté une ligne
		ligne.id="ligne"+i;
		var index=ligne.id;
		var colonne1 = ligne.insertCell(0);//on a une ajouté la prestation	
		colonne1.innerHTML += document.getElementById("prestation_text").value;//on y met le contenu de titre

		var colonne2 = ligne.insertCell(1);//on ajoute le libelle
		colonne2.innerHTML += document.getElementById("libelle_text").value;


		var row=ligne.rowIndex;
		row=row+1;
		var date=new Date();
		var colonne3 = ligne.insertCell(2);
	 	colonne3.innerHTML +=date.getDate()+'/'+(date.getMonth()+1)+'/'+date.getFullYear(); 
		
		var colonne4 = ligne.insertCell(3);//on ajoute le montant
		colonne4.innerHTML += document.getElementById("montant_text").value;

		var colonne5 = ligne.insertCell(4);//on ajoute la quantite
		colonne5.innerHTML += document.getElementById("qte_text").value;

		var colonne6 = ligne.insertCell(5);//
		colonne6.innerHTML += document.getElementById("montant_text").value*document.getElementById("qte_text").value;

		var colonne6 = ligne.insertCell(6);//
		colonne6.innerHTML +="<a onclick=\"modifier("+row+"\);\" data-toggle=\"modal\" data-target=\"#shortModal\"><em class=\"glyphicon glyphicon-pencil\"></em></a>";

		var colonne7 = ligne.insertCell(7);//
		colonne7.innerHTML +="<a href=\"#\" onclick=supprimer("+row+"\);><em class=\"glyphicon glyphicon-remove-sign\"></em></a>";
		
		var divParent = document.getElementById('new_input');
		 
	    // création des libelles
	    var nouveauInputPrestation = document.createElement('input');
	    var nouvelLigne = document.createElement('div'+i);
	    nouvelLigne.id='LigneFacture'+i;
	    
	    // input de Prestation
	    nouveauInputPrestation.name = 'prestation'+i;
	    nouveauInputPrestation.id = 'prestation'+i;
	    nouveauInputPrestation.type = 'hidden';
	    nouveauInputPrestation.value=colonne1.innerHTML;
	      
	    var nouveauInputLibelle = document.createElement('input');
	    	 
	    // input de Libelle
	    nouveauInputLibelle.name = 'libelle'+i;
	    nouveauInputLibelle.id = 'libelle'+i;
	    nouveauInputLibelle.type = 'hidden';
	    nouveauInputLibelle.value=colonne2.innerHTML;

	    var nouveauInputMontant = document.createElement('input');
		 
	   //input de Montant
	    nouveauInputMontant.name = 'Montant'+i;
	    nouveauInputMontant.id = 'Montant'+i;
	    nouveauInputMontant.type = 'hidden';
	    nouveauInputMontant.value=colonne4.innerHTML;

	    var nouveauInputQte = document.createElement('input');
		 
	    // input de Qte
	    nouveauInputQte.name = 'Qte'+i;
	    nouveauInputQte.id = 'Qte'+i;
	    nouveauInputQte.type = 'hidden';
	    nouveauInputQte.value=colonne5.innerHTML;
	    
	    var nouveauInputTotal = document.createElement('input');
		 
	    // input de Total
	    nouveauInputTotal.name = 'Total'+i;
	    nouveauInputTotal.id = 'Total'+i;
	    nouveauInputTotal.type = 'hidden';
	    nouveauInputTotal.value=colonne6.innerHTML;

	    divParent.appendChild(nouvelLigne);

	    nouvelLigne.appendChild(nouveauInputPrestation);
	    nouvelLigne.appendChild(nouveauInputLibelle);
	    nouvelLigne.appendChild(nouveauInputMontant);
	    nouvelLigne.appendChild(nouveauInputQte);
	    nouvelLigne.appendChild(nouveauInputTotal);

		
		document.getElementById("prestation_text").value="";
		document.getElementById("libelle_text").value="";

		document.getElementById("montant_text").value="";
		document.getElementById("qte_text").value="";
		alert(i);
		i=i+1;
	    parent.document.getElementById('annuler_bouton').click();  
	}

/*
1.Récuperation des lignes
2.Recuperation de la ligne concernée à travers le tableau des lignes
3.Afficher tous les elements de la ligne(colonnes) dans le modal appélé
*/

		function miseajourdefinitif(index)
		{

			/* Recuperation des cellules de la ligne*/
			
			var arrayLignes = document.getElementById("tableau_facture").rows;		
			var ligne=arrayLignes[index];

			var prestation=ligne.cells[0].innerHTML;
		    var libelle=ligne.cells[1].innerHTML;
		    
		    var date=new Date().getDate()+'/'+(new Date().getMonth()+1)+'/'+new Date().getFullYear();
		    
		    var montant=ligne.cells[3].innerHTML;
		    var qte=ligne.cells[4].innerHTML;
		    var total=ligne.cells[5].innerHTML;
			
			
			/* Modification du tableau avec les input modifies */
			ligne.cells[0].innerHTML=document.getElementById('prestation_text_mod').value;
			ligne.cells[1].innerHTML=document.getElementById("libelle_text_mod").value;
			ligne.cells[2].innerHTML=date;
			ligne.cells[3].innerHTML=document.getElementById("montant_text_mod").value;
			ligne.cells[4].innerHTML=document.getElementById("qte_text_mod").value;   
			total=document.getElementById("montant_text_mod").value*document.getElementById("qte_text_mod").value;
			ligne.cells[5].innerHTML=total;
				    
			/*modification des input caches avec les elements du tableau*/	
			document.getElementById("prestation"+index).value=ligne.cells[0].innerHTML;
			document.getElementById("libelle"+index).value=ligne.cells[1].innerHTML;
			document.getElementById("Montant"+index).value=ligne.cells[3].innerHTML;
			document.getElementById("Qte"+index).value=ligne.cells[4].innerHTML;
			document.getElementById("Total"+index).value=ligne.cells[5].innerHTML;

			parent.document.getElementById('update_annuler_button').click(); 
			
			 document.getElementById("prestation_text_mod").value="";
			 document.getElementById("libelle_text_mod").value="";
			 document.getElementById("montant_text_mod").value="";
			 document.getElementById("qte_text_mod").value="";   	
	
		}


       function modifier(index)
		{

    	
		var updatebuttons=document.getElementById("update_button");
		var arrayLignes = document.getElementById("tableau_facture").rows;	
		index=index-1;
		var ligne=arrayLignes[index];

		var prestation=ligne.cells[0].innerHTML;
	    var libelle=ligne.cells[1].innerHTML;
	    var montant=ligne.cells[3].innerHTML;
	    var qte=ligne.cells[4].innerHTML;
	       
	    document.getElementById("prestation_text_mod").value=prestation;
	    document.getElementById("libelle_text_mod").value=libelle;
	    document.getElementById("montant_text_mod").value=montant;
	    document.getElementById("qte_text_mod").value=qte;   
	    update_button.setAttribute('onclick','miseajourdefinitif('+index+')');
		}

/* Recuperer l'index de la ligne et la supprimer
 * Supprimer le div des input cachés et le supprimer
 * 
 */
		function supprimer(index)
		{

			
			if (confirm('Etes-vous sur de vouloir supprimer cette ligne de facture?'))
			{
			var arrayLignes = document.getElementById("tableau_facture").rows;	
			index=index-1;	
			var ligne=arrayLignes[index];
			alert(index);

			document.getElementById("tableau_facture").deleteRow(index);
			document.getElementById("new_input").removeChild(document.getElementById("LigneFacture"+(index)));			
			}
			else
			{
				
			return false;
			
			}
	
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
					<?php 
					}
					?>
	
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
					
				</tbody>
			</table>
		</div>
	</div>	
		<div class="row clearfix">
		
	<button type="button" class="btn btn-warning " data-toggle="modal"
   data-target="#largeModal"><em class="glyphicon glyphicon-plus-sign">Ajouter une Ligne de Facture</em></button>
	
	<!-- Modale de l'ajout de la facture -->
 
    <div id="largeModal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">Ajout une Ligne de Facture</h4>
                </div>
                <div class="modal-body">   
                    <form class="form-horizontal" role="form" method="post">
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
								<input class="form-control" name="montant_text" id="montant_text" type="text" />
							</div>
						</div>
						
						<div class="form-group">
							 <label for="Qte" class="col-sm-3 control-label">Qte</label>
							<div class="col-sm-9">
								<input class="form-control" id="qte_text" type="text" />
							</div>
						</div>
					</form>
        
                </div>
                <div class="modal-footer">
                    <button type="button" name="ajouter_ligne" id="annuler_bouton" class="btn btn-default" data-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-success" onclick="ajouterLigne();">Ajouter</button>
                </div>
            </div>
        </div>
    </div>	
	
	
	
	<!-- Modale de la Modification -->
	  <div id="shortModal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h4 class="modal-title">Editer une Ligne de Facture</h4>
                </div>
                <div class="modal-body">   
                    <form class="form-horizontal" role="form" method="post">
						<div class="form-group">
							 <label for="inputEmail3" class="col-sm-3 control-label">Code de la Prestation</label>
							<div class="col-sm-9">
								<input class="form-control" id="prestation_text_mod" type="text" />
							</div>
						</div>
						<div class="form-group">
							 <label for="libelle" class="col-sm-3 control-label">Libelle</label>
							<div class="col-sm-9">
								<input class="form-control" id="libelle_text_mod" type="text" />
							</div>
						</div>
						
						<div class="form-group">
							 <label for="Montant" class="col-sm-3 control-label">Montant</label>
							<div class="col-sm-9">
								<input class="form-control" name="montant_text" id="montant_text_mod" type="text" />
							</div>
						</div>
						
						<div class="form-group">
							 <label for="Qte" class="col-sm-3 control-label">Qte</label>
							<div class="col-sm-9">
								<input class="form-control" id="qte_text_mod" type="text" />
							</div>
						</div>
						
						
						
					</form>
                    
                </div>
                <div class="modal-footer" id="update_buttons">
                    <button type="button" id="update_annuler_button" name="ajouter_ligne" id="annuler_bouton" class="btn btn-default" data-dismiss="modal">Annuler</button>
                    <button type="button" id="update_button" class="btn btn-success">Modifier</button>
                    
                </div>
            </div>
        </div>
    </div>
	

	<div id="new_input" type="hidden">
	
	
	
	
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
    <input type="text" class="form-control" style="text-align:right"  >
   
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
