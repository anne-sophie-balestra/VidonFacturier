<!--
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
* Date de creation : 02/03/2015             *
********************************************/
-->
<!-- /Users/yoannlt/dev/workspace php/VidonFacturier/librairies/dhtmlxSuite_v413_std/codebase -->

<div id="form_container" style="width:280px;height:250px;"></div>

<script src="http://localhost:8888/solent2/lienfacturier/librairies/dhtmlxSuite_v413_std/codebase/dhtmlx.js" type="text/javascript"></script>
<script>
  formStructure = [
    {type:"settings",position:"label-top"},
    {type: "fieldset",name:"calculator", label: "Calculator", list:[
      {type: "input", name: 'firstNum', label: 'First number:'},
      {type:"input", name:"secNum", label:"Second number:"},
      {type:"input", name:"resNum", label:"Result:"},
      {type:"newcolumn"},
      {type:"button", name:"plus", width:20,offsetTop:2, value:"+"},
      {type:"button", name:"minus",width:20,offsetTop:10, value:"-"},
      {type:"button", name:"multiply",width:20,offsetTop:10, value:"*"},
      {type:"button", name:"divide",width:20,offsetTop:10, value:"/"}
    ]}
  ];

  var myForm = new dhtmlXForm("form_container",formStructure);

  myForm.attachEvent("onButtonClick", function(id){
        var res, num1, num2;
    num1 = parseInt(myForm.getItemValue("firstNum"));// returns the value of item
    num2 = parseInt(myForm.getItemValue("secNum")); // returns the value of item
    if (id=="plus") //defines addition
        { res=num1+num2;}
    else if (id=="minus") //defines subtraction
        {res=num1-num2;}
    else if (id=="multiply")//defines multiplication
        {res=num1*num2;}
    else if (id =="divide")//defines division
        {  if (num2==0) //if division by zero - generates a message
               {alert("Error.Division by zero!");res="";}
           else {res=num1/num2;}
        }
    myForm.setItemValue("resNum",res);// sets the value of item
  })
</script>
