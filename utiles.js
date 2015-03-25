/********************************************
* utiles.js                                 *
* Fonctions js utilisees partout            *
*                                           *
* Auteurs : Anne-Sophie Balestra            *
*           Abdoul Wahab Haidara            *
*           Yvan-Christian Maso             *
*           Baptiste Quere                  *
*           Yoann Le Taillanter             *
*                                           *
* Date de creation : 02/03/2015             *
********************************************/

/*****
 * GetXmlHttpObject : Crée un objet XMLHttpObject selon le navigateur.
 *
 * @return XMLHttpRequest xmlHttp : Retourne l'élément créé.
 */
function GetXmlHttpObject()
{
    var xmlHttp= null; 

    try
    {
        // Crée un objet si le navigateur est Firefox, Opera 8.0+ ou Safari.
        xmlHttp=new XMLHttpRequest();
    }
    catch (e)
    {
        // S'il y a un erreur, on crée un élément pour Internet Explorer.
        try
        {
            xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
        }
        catch (e)
        {
            xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
    }

    return xmlHttp;
}

/*****
 * genererListeTypeDossier : genere le select contenant les types de dossier en fonction de l'entite
 * Fonction AJAX qui passe par le fichier ajax.php. Paramètre de l'url : action.
 *
 * @param String p_id : Contient l'id de l'element a modifier.
 * @param String p_value: Contient l'entite choisie (brevet ou juridique)
 ***/
function genererListeTypeDossier(p_id, p_value) {
    // Appel la fonction qui crée un objet XmlHttp.
    var xmlHttp = GetXmlHttpObject(); 
    
    // Vérifie si le navigateur supporte l'AJAX
    if (xmlHttp == null) {
        alert ("Votre navigateur ne supporte pas AJAX");
        return;
    } 
    // Création de l'url envoyee à l'aiguilleur.
    var url= "ajax.php?action=genererListeTypeDossier&ent=" + p_value;
    // Création de la fonction qui sera appelé au changement de statut.
    xmlHttp.onreadystatechange= function StateChanged() {
        if (xmlHttp.readyState == 4) {
            var jsonData = $.parseJSON(xmlHttp.responseText);
            //on recupere la reference a l'element qui encadre notre select afin de l'afficher
            var $div = $(p_id+'_div');
            $div.show();
            
            //on recupere la reference a l'element select que l'on veut peupler
            var $select = $(p_id);
            $select.empty();    
            $select.select2('data', null);    
            $select.append('<option></option>');
            $select.select2({placeholder:"Choisissez un type..."});
            $.each(jsonData,function(key, value) 
            {
                $select.append('<option value=' + key + '>' + value + '</option>');
            });
        };
    };
    xmlHttp.open("GET",url,true); // Ouvre l'url
    xmlHttp.send(null); 
}

/*****
 * genererListePrestationsLiees : genere le select contenant les prestations liees au code choisi
 * Fonction AJAX qui passe par le fichier ajax.php. Paramètre de l'url : action.
 *
 * @param String p_id : Contient l'id de l'element a modifier.
 * @param String p_value: Contient le code choisi
 ***/
function genererListePrestationsLiees(p_id, p_value) {
    // Appel la fonction qui crée un objet XmlHttp.
    var xmlHttp = GetXmlHttpObject(); 
    
    // Vérifie si le navigateur supporte l'AJAX
    if (xmlHttp == null) {
        alert ("Votre navigateur ne supporte pas AJAX");
        return;
    } 
    // Création de l'url envoyee à l'aiguilleur.
    var url= "ajax.php?action=genererListePrestationsLiees&code=" + p_value;
    // Création de la fonction qui sera appelé au changement de statut.
    xmlHttp.onreadystatechange= function StateChanged() {
        if (xmlHttp.readyState == 4) {
            var jsonData = $.parseJSON(xmlHttp.responseText);
            //on recupere la reference a l'element qui encadre notre select afin de l'afficher
            var $div = $(p_id+'_div');
            $div.show();
            
            //on recupere la reference a l'element select que l'on veut peupler
            var $select = $(p_id);
            $select.empty();    
            $select.select2('data', null);    
            $select.append('<option></option>');
            $select.select2({placeholder:"Choisissez une prestation..."});
            $.each(jsonData,function(key, value) 
            {
                $select.append('<optgroup label="' + key + '">');
                $.each(value,function(key_second, value_second) 
                {
                    $select.append('<option value=' + key_second + '>' + value_second + '</option>');
                });
                $select.append('</optgroup>');
            });
        };
    };
    xmlHttp.open("GET",url,true); // Ouvre l'url
    xmlHttp.send(null); 
}

/*****
 * genererInfosPrestation : genere les div pour chaque sous-prestation
 * Fonction AJAX qui passe par le fichier ajax.php. Paramètre de l'url : action.
 *
 * @param String p_id : Contient l'id de l'element a modifier.
 * @param int p_value: Contient le nombre de sous-prestation à creer
 * @param String p_nom: Contient le nom des sections
 ***/
function genererInfosPrestation(p_id, p_value, p_nom){
    // Appel la fonction qui crée un objet XmlHttp.
    var xmlHttp = GetXmlHttpObject(); 

    // Vérifie si le navigateur supporte l'AJAX
    if (xmlHttp == null) {
        alert ("Votre navigateur ne supporte pas AJAX");
        return;
    } 
    // Création de l'url envoyee à l'aiguilleur.
    var url= "ajax.php?action=genererInfosPrestation&nb=" + p_value + "&nom=" + p_nom;
    // Création de la fonction qui sera appelé au changement de statut.
    xmlHttp.onreadystatechange= function StateChanged() {
        if (xmlHttp.readyState == 4) {
            document.getElementById(p_id).innerHTML = xmlHttp.responseText;
        };
    };
    xmlHttp.open("GET",url,true); // Ouvre l'url
    xmlHttp.send(null); 
}

/*****
 * genererInfosPrestationUpdate : genere les infos liees a la prestation en update
 * Fonction AJAX qui passe par le fichier ajax.php. Paramètre de l'url : action.
 *
 * @param String p_id : Contient l'id de l'element a modifier.
 * @param int p_value: Contient la prestation a modifier
 ***/
function genererInfosPrestationUpdate(p_id, p_value){
    // Appel la fonction qui crée un objet XmlHttp.
    var xmlHttp = GetXmlHttpObject(); 

    // Vérifie si le navigateur supporte l'AJAX
    if (xmlHttp == null) {
        alert ("Votre navigateur ne supporte pas AJAX");
        return;
    } 
    // Création de l'url envoyee à l'aiguilleur.
    var url= "ajax.php?action=genererInfosPrestation&pre=" + p_value;
    // Création de la fonction qui sera appelé au changement de statut.
    xmlHttp.onreadystatechange= function StateChanged() {
        if (xmlHttp.readyState == 4) {
            document.getElementById(p_id).innerHTML = xmlHttp.responseText;
        };
    };
    xmlHttp.open("GET",url,true); // Ouvre l'url
    xmlHttp.send(null); 
}

/*****
 * genererTarifs : genere les infos pour creer les tarifs pratiques
 * Fonction AJAX qui passe par le fichier ajax.php. Paramètre de l'url : action.
 *
 * @param String p_id : Contient l'id de l'element a modifier.
 * @param String p_value: Contient le type de tarification
 * @param int p_num: Contient le numero de la prestation en cours de remplissage
 ***/
function genererTarifs(p_id, p_value, p_num){
    // Appel la fonction qui crée un objet XmlHttp.
    var xmlHttp = GetXmlHttpObject(); 

    // Vérifie si le navigateur supporte l'AJAX
    if (xmlHttp == null) {
        alert ("Votre navigateur ne supporte pas AJAX");
        return;
    } 
    // Création de l'url envoyee à l'aiguilleur.
    var url= "ajax.php?action=genererTarifs&tt=" + p_value + "&num=" + p_num;

    // Création de la fonction qui sera appelé au changement de statut.
    xmlHttp.onreadystatechange= function StateChanged() {
        if (xmlHttp.readyState == 4) {
            document.getElementById(p_id).innerHTML = xmlHttp.responseText;
        };
    };
    xmlHttp.open("GET",url,true); // Ouvre l'url
    xmlHttp.send(null);
}

/*****
 * genererListePresta : genere le select contenant les presta
 * Fonction AJAX qui passe par le fichier ajax.php. Paramètre de l'url : action.
 *
 * @param String p_id : Contient l'id de l'element a modifier.
 * @param String p_value: Contient l'entite choisie (brevet ou juridique)
 ***/
function genererListePresta(p_id, p_value) {
    // Appel la fonction qui crée un objet XmlHttp.
    var xmlHttp = GetXmlHttpObject(); 
    
    // Vérifie si le navigateur supporte l'AJAX
    if (xmlHttp == null) {
        alert ("Votre navigateur ne supporte pas AJAX");
        return;
    } 
    // Création de l'url envoyee à l'aiguilleur.
    var url= "ajax.php?action=genererListePresta&dos=" + p_value;
    // Création de la fonction qui sera appelé au changement de statut.
    xmlHttp.onreadystatechange= function StateChanged() {
        if (xmlHttp.readyState == 4) {
            var jsonData = $.parseJSON(xmlHttp.responseText);
            //on recupere la reference a l'element select que l'on veut peupler
            var $select = $(p_id);
            $select.empty();    
            //$select.select2('data', null);    
            $select.append('<option></option>');
            //$select.select2({placeholder:"Choisissez une prestation ..."});
            $.each(jsonData,function(key, value) 
            {
                $select.append('<option value=' + key + '>' + value + '</option>');
            });
        };
    };
    xmlHttp.open("GET",url,true); // Ouvre l'url
    xmlHttp.send(null); 

/*
 * afficherTarifs : affiche les tarifs en fonction du type de tarification dans le modal
 *
 * @param String p_value : Contient le type de tarification
 ***/
function afficherTarifs(p_value){
    if(p_value == "F") {
        document.getElementById('tarif_std_div').style.display= "block";
        document.getElementById('tarif_jr_div').style.display= "none";
        document.getElementById('tarif_sr_div').style.display= "none";
        document.getElementById('tarif_mgr_div').style.display= "none";
    } else {
        document.getElementById('tarif_std_div').style.display= "none";
        document.getElementById('tarif_jr_div').style.display= "block";
        document.getElementById('tarif_sr_div').style.display= "block";
        document.getElementById('tarif_mgr_div').style.display= "block";
    }
}

/*****
 * ajouterPrestationForm : cree les input d'une ligne de prestation dans create prestation (grace au modal)
 *
 * @param String p_id : Contient l'id de l'element a modifier.
 ***/
function ajouterPrestationForm(p_id){
    //on recupere le nombre de prestations qui ont été ajoutées jusqu'a maintenant (moins celles qui ont ete supprimées)
    var nbInfos = parseInt(document.getElementById('nbInfos').value);
    //on recupere le nombre de prestations qui ont été ajoutées jusqu'a maintenant (y compris celles supprimées)
    var nbInfosTot = parseInt(document.getElementById('nbInfosTot').value);
    
    //on recupere le libelle de la ligne de prestation
    var libelle = document.getElementById('libelle').value;
    //on recupere le type de tarification
    var t_tarif =  document.getElementById('t_tarif').value;
    var t_tarif_lib = "";
    //On initialise nos variables pour les tarifs
    var tarif_std = "-";
    var tarif_jr = "-";
    var tarif_sr = "-";
    var tarif_mgr = "-";
    //En fonction du type de tarification, on aura un ou trois champs de tarif a recuperer
    if(t_tarif == "F") {
        tarif_std = document.getElementById('tarif_std').value;
        t_tarif_lib = "Forfaitaire";
    } else {
        tarif_jr = document.getElementById('tarif_jr').value;
        tarif_sr = document.getElementById('tarif_sr').value;
        tarif_mgr = document.getElementById('tarif_mgr').value;
        t_tarif_lib = "Horaire";
    }    
    
    //On augmente le nombre de prestations ajoutées
    document.getElementById('nbInfos').value = parseInt(nbInfos+1); 
    document.getElementById('nbInfosTot').value = parseInt(nbInfosTot+1); 
    //On recupere ce qu'il y avait deja dans la table
    var element = document.getElementById(p_id).innerHTML;
    
    //On cree la ligne dans la table
    var ligne = "<tr id='ligne" + document.getElementById('nbInfosTot').value + "'>" 
                +"<td><span class='badge'>" + document.getElementById('nbInfosTot').value + "</span></td>"
                +"<td>" + libelle 
                + "<input type='hidden' value='" + libelle + "' name='libelle" + document.getElementById('nbInfosTot').value + "' id='libelle" + document.getElementById('nbInfosTot').value + "'/></td>"
                +"<td>" + t_tarif_lib
                +"<input type='hidden' value='" + t_tarif + "' name='t_tarif" + document.getElementById('nbInfosTot').value + "' id='t_tarif" + document.getElementById('nbInfosTot').value + "'/></td>"
                +"<td>" + tarif_std
                +"<input type='hidden' value='" + tarif_std + "' name='tarif_std" + document.getElementById('nbInfosTot').value + "' id='tarif_std" + document.getElementById('nbInfosTot').value + "'/></td>"        
                +"<td>" + tarif_jr
                +"<input type='hidden' value='" + tarif_jr + "' name='tarif_jr" + document.getElementById('nbInfosTot').value + "' id='tarif_jr" + document.getElementById('nbInfosTot').value + "'/></td>"
                +"<td>" + tarif_sr
                +"<input type='hidden' value='" + tarif_sr + "' name='tarif_sr" + document.getElementById('nbInfosTot').value + "' id='tarif_sr" + document.getElementById('nbInfosTot').value + "'/></td>"
                +"<td>" + tarif_mgr
                +"<input type='hidden' value='" + tarif_mgr + "' name='tarif_mgr" + document.getElementById('nbInfosTot').value + "' id='tarif_mgr" + document.getElementById('nbInfosTot').value + "'/></td>"
                +"<td align='center'>"
                    +"<a class='btn btn-primary btn-sm' onclick='modifierPrestationForm(" + document.getElementById('nbInfosTot').value + ")'><i class='icon-plus fa fa-edit'></i> Modifier</a>"
                +"</td>"
                +"<td align='center'>"
                    +"<a class='btn btn-danger btn-sm' onclick='supprimerPrestationForm(" + document.getElementById('nbInfosTot').value + ")'><i class='icon- fa fa-remove'></i> Supprimer</a>"
                +"</td>"
            +"</tr>";
    document.getElementById(p_id).innerHTML = element + ligne;
}

/*****
 * supprimerPrestationForm : supprime la ligne de prestation choisie (cree grace au modal)
 *
 * @param String p_num : Contient le numero de la ligne a supprimer
 ***/
function supprimerPrestationForm(p_num){
    //on recupere le nombre de prestations qui ont été ajoutées jusqu'a maintenant (moins celles qui ont ete supprimées)
    var nbInfos = parseInt(document.getElementById('nbInfos').value);
    
    //On decrement le nombre de prestations ajoutées
    document.getElementById('nbInfos').value = parseInt(nbInfos+1);  
    
    //On cree la ligne dans la table
    var ligne = "<input type='hidden' value='" + p_num + "' name='supp" + document.getElementById('nbInfosTot').value + "' id='supp" + document.getElementById('nbInfosTot').value + "'/>";
    document.getElementById('ligne'+p_num).innerHTML = ligne;
}

/*****
 * checkAddUpdateLignePrestation : Verifie que les champs soient bien remplis pour ajouter ou modifier une ligne de prestation
 *
 * @param String p_id : Contient l'id du bouton de submit de la modal a bloquer ou non
 ***/
function checkAddUpdateLignePrestation(p_id){
    //si tous les champs sont remplis correctement, alors le bouton de submit du modal sera activé
    var buttonOk = true;
    
    //on recupere le libelle de la ligne de prestation
    var libelle = document.getElementById('libelle').value;
    if(libelle == "")
        buttonOk =false;
    
    //on recupere le type de tarification
    var t_tarif =  document.getElementById('t_tarif').value;
    var t_tarif_lib = "";
    
    //On initialise nos variables pour les tarifs
    var tarif_std = "-";
    var tarif_jr = "-";
    var tarif_sr = "-";
    var tarif_mgr = "-";
    
    //En fonction du type de tarification, on aura un ou trois champs de tarif a recuperer
    if(t_tarif == "F") {
        tarif_std = document.getElementById('tarif_std').value;
        if(!isANumber(tarif_std))
            buttonOk = false;
        t_tarif_lib = "Forfaitaire";
    } else if(t_tarif == "TH") {
        tarif_jr = document.getElementById('tarif_jr').value;
        if(!isANumber(tarif_jr))
            buttonOk = false;
        tarif_sr = document.getElementById('tarif_sr').value;
        if(!isANumber(tarif_sr))
            buttonOk = false;
        tarif_mgr = document.getElementById('tarif_mgr').value;
        if(!isANumber(tarif_mgr))
            buttonOk = false;
        t_tarif_lib = "Horaire";
    } else {
        buttonOk = false;
    }
    
    if(buttonOk)
        document.getElementById(p_id).disabled = false;
    else
        document.getElementById(p_id).disabled = true;    
}

function isANumber( n ) {
    var numStr = /^(\d+\.?\d*)$/;
    return numStr.test(n.toString());
}
