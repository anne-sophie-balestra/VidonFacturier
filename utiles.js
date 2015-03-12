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
            //get a reference to the select element
            var $select = $(p_id);
            $select.empty();    
            $select.select2('data', null);    
            $select.prop("disabled", false);
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
