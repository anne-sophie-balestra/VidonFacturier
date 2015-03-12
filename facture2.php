<?php
require_once("header.php");
?>

<html>
<head>
<body>
<div>
<h1><small>Editer une facture</small></h1>
</div>

<a href="#" class="btn btn-primary btn-lg btn-info" role="button">Listes de Prestations</a>
<a href="#" class="btn btn-default btn-lg btn-primary" role="button">Listes des Achats</a>
<a href="#" class="btn btn-default btn-lg btn-warning" role="button">Listes des Repartitions</a>

<div>
<form class="form-horizontal " id="container">
<legend>Editer une Facture</legend>
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
<div class="col-xs-2">

<textarea class="form-control" rows="3" cols="52" id="comment"></textarea>
</div>
</div> 
</div>
</div>
  
 <div class="form-group">
 
 <div class="controls form-inline">
 <label class="control-label col-sm-1" for="montant">Montant</label>
 <div class="row">
 <div class="col-xs-2">
       <input type="text" class="form-control" id="montantht" placeholder="">
 </div>
 <div class="col-xs-1">
      <label class="control-label" for="Object">MontantTTC</label>
</div>
<div class="col-xs-2">
	 <input type="text" class="form-control" id="montantht" placeholder="">
 </div>
 <div class="col-xs-2">
      <label class="control-label" for="Object">Taxes</label>
</div>
<div class="col-xs-2">
	 <input type="text" class="form-control " id="montantht" placeholder="">
 </div>
 </div>
 </div>
 </div> 
  
  <div class="form-group">
 
 <div class="controls form-inline">
 <label class="control-label col-sm-1" for="montant">Honoraires</label>
 <div class="row">
 <div class="col-xs-2">
       <input type="text" class="form-control" id="honoraires" placeholder="">
 </div>
 <div class="col-xs-1">
      <label class="control-label" for="frais">Frais</label>
</div>
<div class="col-xs-2">
	 <input type="text" class="form-control" id="fraistext" placeholder="">
 </div>
 <div class="col-xs-2">
      <label class="control-label " for="Object">Restant du</label>
</div>
<div col="col-xs-2">
	 <input type="text" class="form-control col-offset-sm-2"  id="restantdu" placeholder="">
 </div>
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
  
  
  
  
  
  
  
  </div>
 <!--   <div class="form-group">
    <label for="inputPassword3" class="col-sm-2 control-label">Password</label>
    <div class="col-sm-10">
      <input type="password" class="form-control" id="inputPassword3" placeholder="Password">
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <div class="checkbox">
        <label>
          <input type="checkbox"> Remember me
        </label>
      </div>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <button type="submit" class="btn btn-default">Sign in</button>
    </div>
  </div>
  -->
</form>
</div>

</body>
</head>
</html>