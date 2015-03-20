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

/* On se connecte a la base */
$pdo = new SPDO;

/* On cree la requete pour recuperer les operations et on les stocke dans un JSON créé pour le DHTMLX */
$stmt_ope = "SELECT t_ope_id, t_ope_libelle FROM type_operation";
$json_ope = generateJSON("t_ope_id", "t_ope_libelle", $stmt_ope);

/* On cree la requete pour recuperer les entites de dossier distinctes et on les stocke dans un JSON créé pour le DHTMLX */
$stmt_ent = "SELECT DISTINCT(t_dos_entite) FROM type_dossier ORDER BY t_dos_entite";
$json_ent = generateJSON("t_dos_entite", "t_dos_entite", $stmt_ent);

/* On cree la requete pour recuperer les codes de nomenclatures et on les stocke dans un JSON créé pour le DHTMLX */
$stmt_code = "SELECT nom_id, nom_code FROM nomenclature ORDER BY nom_code";
$json_code = generateJSON("nom_id", "nom_code", $stmt_code);

/* On cree la requete pour recuperer les pays et on les stocke dans un JSON créé pour le DHTMLX */
$stmt_pays = "SELECT pay_id, pay_nom FROM pays ORDER BY pay_nom";
$json_pays = generateJSON("pay_id", "pay_nom", $stmt_pays);

?>
<div id="myForm" style="width:350px; height:160px; padding-left:100px;"></div>

<script type="text/javascript">    
    dhtmlx.image_path='librairies/dhtmlxSuite/codebase/imgs';

    var myForm, formData;
    function doOnLoad() {
        formData = [
            { type:"fieldset" , name:"form_fieldset_1", label:"Nouvelle prestation", list:[
            { type:"combo" , name:"form_combo_2", label:"Opération", options:<?php echo $json_ope;?>, inputWidth:200, required:true, position:"label-top"  },
            { type:"fieldset" , name:"form_fieldset_3", label:"Type de dossier", list:[
            { type:"combo" , name:"form_combo_3", label:"Entité", options:<?php echo $json_ent;?>, inputWidth:200, required:true, position:"label-top"  },
            { type:"combo" , name:"form_combo_4", label:"Type", connector:"./data/data_combo.json", inputWidth:200, required:true, position:"label-top"  }
            ]  },
            { type:"combo" , name:"form_combo_5", label:"Code", options:<?php echo $json_code;?>, inputWidth:200, required:true, position:"label-top"  },
            { type:"combo" , name:"form_combo_6", label:"Pays", options:<?php echo $json_pays;?>, inputWidth:200, required:true, position:"label-top"  },
            { type:"input" , name:"form_input_1", label:"Prestation", inputWidth:200, required:true, position:"label-top"  },
            { type:"input" , name:"form_input_2", label:"Répartition", inputWidth:200, value:"0", required:true, position:"label-top"  }
            ]}
        ];        
        myForm = new dhtmlXForm("myForm", formData)
    }
</script>