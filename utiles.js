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
function GetXmlHttpObject() {

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
 * @param p_id : Contient l'id de l'element a modifier.
 * @param p_value : Contient l'entite choisie (brevet ou juridique)
 * @param p_select2 : True si on veut remettre le select en select2
 ***/
function genererListeTypeDossier(p_id, p_value, p_select2) {
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
//            var $div = $(p_id+'_div');
//            $div.show();
            
            //on recupere la reference a l'element select que l'on veut peupler
            //Pour que les select2 marchent avec le modal sans conflit, on utilise un autre alias que $ pour les select2 (ici jQuery)
            var $select = jQuery(p_id);
            $select.empty();
            if(p_select2) {
                $select.select2('data', null);    
            }
            $select.append('<option></option>');
            if(p_select2) {
                $select.select2({placeholder:"Choisissez un type..."});
            }
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
 * genererListePresta : genere le select contenant les presta
 * Fonction AJAX qui passe par le fichier ajax.php. Paramètre de l'url : action.
 *
 * @param String p_id : Contient l'id de l'element a modifier.
 * @param String type_ent : entite
 * @param String type_dossier: Contient le type de dossier : Dessin/Modèle (Juridique) ou Brevet/Etude (Brevet)
 * @param String type_ope : Contient le type d'opération : ex : Délivrance, dépôt, enregistrement, etc ...
 ***/
function genererListePresta(p_id, type_dossier, type_ope ) {
    // Appel la fonction qui crée un objet XmlHttp.
    var xmlHttp = GetXmlHttpObject(); 
    
    // Vérifie si le navigateur supporte l'AJAX
    if (xmlHttp == null) {
        alert ("Votre navigateur ne supporte pas AJAX");
        return;
    } 
    // Création de l'url envoyee à l'aiguilleur.
    var url= "ajax.php?action=genererListePresta&dos=" + type_dossier + "&ope=" + type_ope;

    // Création de la fonction qui sera appelé au changement de statut.
    xmlHttp.onreadystatechange= function StateChanged() {
        if (xmlHttp.readyState == 4) {
            var jsonData = $.parseJSON(xmlHttp.responseText);
            //on recupere la reference a l'element select que l'on veut peupler
            var $select = jQuery(p_id);
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
}

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
    } else if(p_value == "TH") {
        document.getElementById('tarif_std_div').style.display= "none";
        document.getElementById('tarif_jr_div').style.display= "block";
        document.getElementById('tarif_sr_div').style.display= "block";
        document.getElementById('tarif_mgr_div').style.display= "block";
    } else {
        document.getElementById('tarif_std_div').style.display= "none";
        document.getElementById('tarif_jr_div').style.display= "none";
        document.getElementById('tarif_sr_div').style.display= "none";
        document.getElementById('tarif_mgr_div').style.display= "none";
    }
}

/*****
 * genererModalLignePrestation : genere le modal pour ajouter ou modifier une ligne de prestation dans createPrestation
 * Fonction AJAX qui passe par le fichier ajax.php. Paramètre de l'url : action.
 *
 * @param p_id : Contient l'id de l'element a modifier.
 * @param p_presta : Contient le numero de la prestation si on modifie une ligne (0 si c est un ajout)
 ***/
function genererModalLignePrestation(p_id, p_presta) {
    // Appel la fonction qui crée un objet XmlHttp.
    var xmlHttp = GetXmlHttpObject(); 
    
    // Vérifie si le navigateur supporte l'AJAX
    if (xmlHttp == null) {
        alert ("Votre navigateur ne supporte pas AJAX");
        return;
    } 
    // Création de l'url envoyee à l'aiguilleur.
    var url= "ajax.php?action=genererModalLignePrestation&pre=" + p_presta;
    // Création de la fonction qui sera appelé au changement de statut.
    xmlHttp.onreadystatechange= function StateChanged() {
        if (xmlHttp.readyState == 4) {
            document.getElementById(p_id).innerHTML = xmlHttp.responseText;
            //Si nous souhaitons modifier une ligne de prestation, nous allons preremplir le modal
            if(p_presta != 0) {
                $('#libelle').val($('#libelle'+p_presta).val());
                $('#t_tarif').val($('#t_tarif'+p_presta).val());
                if($('#t_tarif').val() == "F") {                    
                    $('#tarif_std').val($('#tarif_std'+p_presta).val());
                    $('#tarif_jr').val("");
                    $('#tarif_sr').val("");
                    $('#tarif_mgr').val("");
                } else {
                    $('#tarif_std').val("");
                    $('#tarif_jr').val($('#tarif_jr'+p_presta).val());
                    $('#tarif_sr').val($('#tarif_sr'+p_presta).val());
                    $('#tarif_mgr').val($('#tarif_mgr'+p_presta).val());
                }
                afficherTarifs($('#t_tarif').val());
            }
            $('#modalInfoPrestation').modal('toggle');
        };
    };
    xmlHttp.open("GET",url,true); // Ouvre l'url
    xmlHttp.send(null); 
}

/*****
 * genererModalPrestation : genere le modal pour modifier une prestation depuis la liste des prestations
 * Fonction AJAX qui passe par le fichier ajax.php. Paramètre de l'url : action.
 *
 * @param p_id : Contient l'id de l'element a modifier.
 * @param p_presta : Contient l'id general de la prestation a modifier
 ***/
function genererModalPrestation(p_id, p_presta) {
    // Appel la fonction qui crée un objet XmlHttp.
    var xmlHttp = GetXmlHttpObject(); 
    
    // Vérifie si le navigateur supporte l'AJAX
    if (xmlHttp == null) {
        alert ("Votre navigateur ne supporte pas AJAX");
        return;
    } 
    // Création de l'url envoyee à l'aiguilleur.
    var url= "ajax.php?action=genererModalPrestation&pre=" + p_presta;
    // Création de la fonction qui sera appelé au changement de statut.
    xmlHttp.onreadystatechange= function StateChanged() {
        if (xmlHttp.readyState == 4) {
            document.getElementById(p_id).innerHTML = xmlHttp.responseText;
            $('#modalInfoPrestationGenerale').modal('toggle');
        };
    };
    xmlHttp.open("GET",url,true); // Ouvre l'url
    xmlHttp.send(null); 
}

/*****
 * ajouterPrestationForm : cree les input d'une ligne de prestation dans create prestation (grace au modal)
 *
 * @param p_id : Contient l'id de l'element a modifier.
 * @param p_modal : true si on fait avec un modal
 ***/
function ajouterPrestationForm(p_id, p_modal){
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
                +"<a class='btn btn-primary btn-sm' onclick='";
    if(p_modal) {
        ligne += "genererModalLignePrestation(\"modalLignePrestation\"," + document.getElementById('nbInfosTot').value + ", " + p_modal + ")'>";
    } else {
        ligne += "modifierPrestation(" + document.getElementById('nbInfosTot').value + ")'>";
    }
    ligne += "<i class='icon-plus fa fa-edit'></i> Modifier</a>"
                +"</td>"
                +"<td align='center'>"
                    +"<a class='btn btn-danger btn-sm' onclick='supprimerPrestationForm(" + document.getElementById('nbInfosTot').value + ")'><i class='icon- fa fa-remove'></i> Supprimer</a>"
                +"</td>"
            +"</tr>";
    document.getElementById(p_id).innerHTML = element + ligne;
    //On supprime le modal en caché afin de pouvoir valider le formulaire (sinon le validator bootstrap trouve des inputs required non remplis dans le modal)
    if(p_modal) {
        document.getElementById('modalLignePrestation').innerHTML = "";
    } // sinon on vide les champs 
    else {
        $('#libelle').val("");
        $('#t_tarif').val("");                    
        $('#tarif_std').val("");
        $('#tarif_jr').val("");
        $('#tarif_sr').val("");
        $('#tarif_mgr').val("");
        afficherTarifs($('#t_tarif').val());
    }
}

/*****
 * modifierPrestation : permet de modifier la ligne de prestation ajouter dans le formulaire de modif dans listePrestations
 *
 * @param p_presta : Contient le numero de la ligne a modifier.
 ***/
function modifierPrestation(p_presta){
    
    //on recupere le libelle de la ligne de prestation que l'on veut modifier
    var libelle = document.getElementById('libelle' + p_presta).value;
    //on recupere le type de tarification
    var t_tarif =  document.getElementById('t_tarif' + p_presta).value;
    //On initialise nos variables pour les tarifs
    var tarif_std = "";
    var tarif_jr = "";
    var tarif_sr = "";
    var tarif_mgr = "";
    //En fonction du type de tarification, on aura un ou trois champs de tarif a recuperer
    if(t_tarif == "F") {
        tarif_std = document.getElementById('tarif_std' + p_presta).value;
    } else {
        tarif_jr = document.getElementById('tarif_jr' + p_presta).value;
        tarif_sr = document.getElementById('tarif_sr' + p_presta).value;
        tarif_mgr = document.getElementById('tarif_mgr' + p_presta).value;
    }    
    
    //On va modifier les inputs pour les preremplir avant de pouvoir faire la modification
    $('#libelle').val(libelle);
    $('#t_tarif').val(t_tarif);                    
    $('#tarif_std').val(tarif_std);
    $('#tarif_jr').val(tarif_jr);
    $('#tarif_sr').val(tarif_sr);
    $('#tarif_mgr').val(tarif_mgr);
    afficherTarifs(t_tarif);
    
    document.getElementById('button_action').innerHTML = "<button type='button' class='btn btn-default' name='subAction' id='subAction' onclick='modifierPrestationForm(\"ligne" + p_presta + "\", \"" + p_presta + "\", false);'><i class='icon-plus fa fa-edit'></i> Modifier la prestation</button>";
    document.getElementById('panel_action').innerHTML = "Modifier la ligne de prestation";
}

/*****
 * modifierPrestationForm : modifie les inputs de la ligne de prestation dans create prestation (grace au modal)
 *
 * @param p_id : Contient l'id de l'element a modifier.
 * @param p_presta : Contient le numero de la ligne a modifier.
 * @param p_modal : true si on modifie via un modal
 ***/
function modifierPrestationForm(p_id, p_presta, p_modal){
    
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
    //On cree la ligne dans la table
    var ligne = "<td>" + libelle 
                + "<input type='hidden' value='" + libelle + "' name='libelle" + p_presta + "' id='libelle" + p_presta + "'/></td>"
                +"<td>" + t_tarif_lib
                +"<input type='hidden' value='" + t_tarif + "' name='t_tarif" + p_presta + "' id='t_tarif" + p_presta + "'/></td>"
                +"<td>" + tarif_std
                +"<input type='hidden' value='" + tarif_std + "' name='tarif_std" + p_presta + "' id='tarif_std" + p_presta + "'/></td>"        
                +"<td>" + tarif_jr
                +"<input type='hidden' value='" + tarif_jr + "' name='tarif_jr" + p_presta + "' id='tarif_jr" + p_presta + "'/></td>"
                +"<td>" + tarif_sr
                +"<input type='hidden' value='" + tarif_sr + "' name='tarif_sr" + p_presta + "' id='tarif_sr" + p_presta + "'/></td>"
                +"<td>" + tarif_mgr
                +"<input type='hidden' value='" + tarif_mgr + "' name='tarif_mgr" + p_presta + "' id='tarif_mgr" + p_presta + "'/></td>"
                +"<td align='center'>"
                    +"<a class='btn btn-primary btn-sm' onclick='";
    if(p_modal) {
        ligne += "genererModalLignePrestation(\"modalLignePrestation\"," + p_presta + ", " + p_modal + ")'>";
    } else {
        ligne += "modifierPrestation(\"" + p_presta + "\")'>";
    }
    ligne += "<i class='icon-plus fa fa-edit'></i> Modifier</a>"
                +"</td>"
                +"<td align='center'>"
                    +"<a class='btn btn-danger btn-sm'";
    if(p_presta.substring(0,3)!= "PRE") {
        ligne += " onclick='supprimerPrestationForm(" + p_presta + ")'";
    } else {
        ligne += "disabled";
    }
    ligne += "><i class='icon- fa fa-remove'></i> Supprimer</a>";
                +"</td>";
        
    document.getElementById(p_id).innerHTML = ligne;
    //On supprime le modal en caché afin de pouvoir valider le formulaire (sinon le validator bootstrap trouve des inputs required non remplis dans le modal)
    if(p_modal) {
        document.getElementById('modalLignePrestation').innerHTML = "";
    } else {
        document.getElementById('button_action').innerHTML = "<button type='button' class='btn btn-default' disabled name='subAction' id='subAction' onclick='ajouterPrestationForm(\"listePrestations\", false);'><i class='icon-plus fa fa-plus'></i> Ajouter une prestation</button>";
        document.getElementById('panel_action').innerHTML = "Ajout d'une ligne de prestation";
        //on remet ensuite les inputs a vide
        $('#libelle').val("");
        $('#t_tarif').val("");                    
        $('#tarif_std').val("");
        $('#tarif_jr').val("");
        $('#tarif_sr').val("");
        $('#tarif_mgr').val("");
        afficherTarifs($('#t_tarif').val());
    }
}

/*****
 * supprimerPrestationForm : supprime la ligne de prestation choisie (cree grace au modal)
 *
 * @param p_num : Contient le numero de la ligne a supprimer
 ***/
function supprimerPrestationForm(p_num){
    //on recupere le nombre de prestations qui ont été ajoutées jusqu'a maintenant (moins celles qui ont ete supprimées)
    var nbInfos = parseInt(document.getElementById('nbInfos').value);
    
    //On decrement le nombre de prestations ajoutées
    document.getElementById('nbInfos').value = parseInt(nbInfos-1);  
    
    //On cree la ligne dans la table
    var ligne = "<input type='hidden' value='" + p_num + "' name='supp" + p_num + "' id='supp" + p_num + "'/>";
    document.getElementById('ligne'+p_num).innerHTML = ligne;
}

/*****
 * checkLignePrestation : Verifie que les champs soient bien remplis pour ajouter ou modifier une ligne de prestation
 *
 * @param p_id : Contient l'id du bouton de submit de la modal a bloquer ou non
 ***/
function checkLignePrestation(p_id){
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

/*****
 * isANumber : Verifie que le parametre est un nombre au format monétaire
 *
 * @param p_number : Contient la valeur a verifier
 ***/
function isANumber(p_number) {
    var numStr = /^(\d+\.?\d*)$/;
    return numStr.test(p_number.toString());
}

/**
* ajouterPrestationModel : Permet d'ajouter des lignes de prestations aux modèles (type_facture).
* @param String p_id : Contient l'ID du tableau ou les prestations seront ajoutées.
*/
function ajouterPrestationModel(p_id){

    // On recupere le nombre de prestations qui ont été ajoutées jusqu'a maintenant (moins celles qui ont ete supprimées)
    var nbInfos = parseInt(document.getElementById('nbInfos').value);

    // On recupere le nombre de prestations qui ont été ajoutées jusqu'a maintenant (y compris celles supprimées)
    var nbInfosTot = parseInt(document.getElementById('nbInfosTot').value);

    // On augmente le nombre de prestations ajoutées
    document.getElementById('nbInfos').value = parseInt(nbInfos+1); 
    document.getElementById('nbInfosTot').value = parseInt(nbInfosTot+1); 

    // Appel la fonction qui crée un objet XmlHttp.
    var xmlHttp = GetXmlHttpObject(); 

    // On recupere l'id de la prestation à ajouter.
    var id_pres = document.getElementById('select_presta').value;

    // On récupère les éléments du tableau HTML
    var element = document.getElementById(p_id).innerHTML;

    // Vérifie si le navigateur supporte l'AJAX
    if (xmlHttp == null) {
        alert ("Votre navigateur ne supporte pas AJAX");
        return;
    }
    // Création de l'url envoyee à l'aiguilleur.
    var url= "ajax.php?action=getPrestationTabFromID&presta=" + id_pres + "&nbInfos=" + nbInfos + "&nbInfosTot=" + nbInfosTot;

    // Création de la fonction qui sera appelé au changement de statut.
    xmlHttp.onreadystatechange= function StateChanged() {
        if (xmlHttp.readyState == 4) {
            document.getElementById(p_id).innerHTML = element + xmlHttp.responseText;
        };
    };
    xmlHttp.open("GET",url,true); // Ouvre l'url
    xmlHttp.send(null); 
}


/*****
 * supModelPresta : supprime une ligne de prestation dans la page de création d'un modèle.
 *
 * @param String p_lign_num : Contient le numero de la ligne à supprimer
 ***/
function supModelPresta(p_lign_num) {
    //on recupere le nombre de prestations qui ont été ajoutées jusqu'a maintenant (moins celles qui ont ete supprimées)
    var nbInfos = parseInt(document.getElementById('nbInfos').value);
    
    //On decrement le nombre de prestations ajoutées
    document.getElementById('nbInfos').value = parseInt(nbInfos+1);  
    
    //On cree la ligne dans la table
    var ligne = "<input type='hidden' value='" + p_lign_num + "' name='supp" + p_lign_num + "' id='supp" + p_lign_num + "'/>";
    document.getElementById('ligne'+p_lign_num).innerHTML = ligne;
}
