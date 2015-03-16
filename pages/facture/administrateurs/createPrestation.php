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
* Date de creation : 06/02/2015             *
********************************************/
?>

<body onload="doOnLoad();">
	<div id="myForm" style="height:500px;"></div>
</body>

<script type="text/javascript">    
    dhtmlx.image_path='librairies/dhtmlxSuite/codebase/imgs';

    var myForm, formData;
    function doOnLoad() {
        formData = [
            { type:"fieldset" , name:"form_fieldset_1", label:"Nouvelle prestation", list:[
            { type:"combo" , name:"form_combo_2", label:"Opération", connector:"./data/data_combo.json", inputWidth:200, required:true, position:"label-top"  },
            { type:"fieldset" , name:"form_fieldset_3", label:"Type de dossier", list:[
            { type:"combo" , name:"form_combo_3", label:"Entité", connector:"./data/data_combo.json", inputWidth:200, required:true, position:"label-top"  },
            { type:"combo" , name:"form_combo_4", label:"Type", connector:"./data/data_combo.json", inputWidth:200, required:true, position:"label-top"  }
            ]  },
            { type:"combo" , name:"form_combo_5", label:"Code", connector:"./data/data_combo.json", inputWidth:200, required:true, position:"label-top"  },
            { type:"combo" , name:"form_combo_6", label:"Pays", connector:"./data/data_combo.json", inputWidth:200, required:true, position:"label-top"  },
            { type:"input" , name:"form_input_1", label:"Prestation", inputWidth:200, required:true, position:"label-top"  },
            { type:"input" , name:"form_input_2", label:"Répartition", inputWidth:200, value:"0", required:true, position:"label-top"  }
            ]  }
        ];        
        myForm = new dhtmlXForm("myForm", formData);
    }
</script>