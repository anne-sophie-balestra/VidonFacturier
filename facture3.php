<?php
//echo 
?>


<!DOCTYPE html>
<html>
<head>
	<title>Création d'une Facture Manuelle</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<link rel="stylesheet" type="text/css" href="grid_server/codebase/dhtmlx.css"/>
	<script src="librairies/dhtmlxSuite/dhtmlx.css"></script>
     <script src="ibrairies/dhtmlxSuite/dhtmlx.js"></script>
	<!--  <link rel="stylesheet" type="text/css" href="grid_server/codebase/dhtmlx.css"/>
	<link rel="stylesheet" type="text/css" href="grid_server/codebase/dhtmlxform.css"/>
	<script src="grid_server/codebase/dhtmlx.js"></script>-->
	<script>
		var myForm, formData;
		function doOnLoad()
		{
			formData=[
						
		{type: "container", name: "myGridDossier", label: "Dossier", inputWidth: 390, inputHeight: 200},
		{type: "container", name: "myGridLigne", label: "Ligne de factures", inputWidth: 390, inputHeight: 200},
		{type: "container", name: "myGridAchat", label: "Achats", inputWidth: 390, inputHeight: 200},
		{type: "fieldset", label: "Editer une Facture ", inputWidth: "*", list:[
		{type:"settings",position:"label-left"},
		{type:"label",list:[
		{type:"select",name:"Type",label:"Montant H.T",options:
			[
			 {text:"Avoir",value:"Avoir"},
			 {text:"Facture",value:"Facture"}
			]},
			{type:"newcolumn"},
		{type:"select",options:
			[
			 {text:"Facture",value:"Facture avec Proforma"},
			 {text:"Proforma",value:"Proforma"},
			]}]},
		{type: "input", name: "objet", label: "Objet ", rows: 5, inputWidth: 400, value: "", required: true, validate:"NotEmpty", 
				  info: true, note: {text: "Veuillez saisir l'objet de la facture, SVP."
			}},

		{type: "label", list:[
		{type: "input",name:"ht",label:"Montant H.T ",inputWidth:45, value:"20", required:false },
		{type: "newcolumn"},
		{type: "input",name:"Taxes",label:"Taxes",inputWidth:45, value:"20", required:false },
		{type: "newcolumn"},
		{type: "input",name:"tva",label:"TVA",inputWidth:45, value:"20", required:false },
		]},
		

		{type: "radio", name: "frais", value:"1" , label: "Honoraires", checked: true},
		{type: "radio", name: "frais", value:"2", label: "Frais",position:"label-right"},
		{type:"input",name:"",inputWidth:45,value:"1000", required:false,position:"label-right"},
		{type:"input",name:"TTC",inputWidth:45,label:"Montant TTC",value:"1000", required:false}]}];

			myForm = new dhtmlXForm("myForm", formData);
			                                          	                            
		}


		
		function doFormValidate() {
			myForm.validate();
		}
		function resetValidateData() {
			myForm.resetValidateCss();
		}
	</script>
</head>
<body onload="doOnLoad();">
	<div id="myForm"></div>
</body>
</html>
