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

/*
 * checkRepartition : genere les bonnes repartitions en fonction de ce qui est choisit
 *
 * @param p_value : Contient la repartition pour les consultants
 ***/
function checkRepartition(p_value){
    document.getElementById('pourcentage_select').value=p_value;
    document.getElementById('pourcentage').innerHTML=p_value+'%';
    document.getElementById('repartition').value=p_value;
    document.getElementById('pourcentage_cons').innerHTML=p_value+'%';
    document.getElementById('pourcentage_cons_div').style.width=p_value+'%';
    document.getElementById('pourcentage_admin').innerHTML=(100-parseInt(p_value))+'%';
    document.getElementById('pourcentage_admin_div').style.width=(100-parseInt(p_value)+'%');
}

/*****
 * genererInfosDossier : genere les infos du dossier choisi pour la création d'une facture
 * Fonction AJAX qui passe par le fichier ajax.php. Paramètre de l'url : action.
 *
 * @param p_id : Contient l'id de l'element a modifier.
 * @param p_value : Contient l'id du dossier
 ***/
function genererInfosDossier(p_id, p_value) {
    // Appel la fonction qui crée un objet XmlHttp.
    var xmlHttp = GetXmlHttpObject(); 
    
    // Vérifie si le navigateur supporte l'AJAX
    if (xmlHttp == null) {
        alert ("Votre navigateur ne supporte pas AJAX");
        return;
    } 
    // Création de l'url envoyee à l'aiguilleur.
    var url= "ajax.php?action=genererInfosDossier&dos=" + p_value;
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
 * genererObjetFacture : genere l'objet de la facture (reprend l'objet du dossier)
 * Fonction AJAX qui passe par le fichier ajax.php. Paramètre de l'url : action.
 *
 * @param p_id : Contient l'id de l'element a modifier.
 * @param p_value : Contient l'id du dossier
 ***/
function genererObjetFacture(p_id, p_value) {
    // Appel la fonction qui crée un objet XmlHttp.
    var xmlHttp = GetXmlHttpObject(); 
    
    // Vérifie si le navigateur supporte l'AJAX
    if (xmlHttp == null) {
        alert ("Votre navigateur ne supporte pas AJAX");
        return;
    } 
    // Création de l'url envoyee à l'aiguilleur.
    var url= "ajax.php?action=genererObjetFacture&dos=" + p_value;
    // Création de la fonction qui sera appelé au changement de statut.
    xmlHttp.onreadystatechange= function StateChanged() {
        if (xmlHttp.readyState == 4) {
            $(p_id).val(xmlHttp.responseText);
        };
    };
    xmlHttp.open("GET",url,true); // Ouvre l'url
    xmlHttp.send(null); 
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
 * genererModalModelLigne : genere le modal pour modifier une ligne (type_ligne) depuis la liste des Modeles.
 * Fonction AJAX qui passe par le fichier ajax.php. Paramètre de l'url : action.
 *
 * @param p_id : Contient l'id de l'element a modifier (le modal).
 * @param t_ligne : Contient l'id du modele a modifier.
 ***/
function genererModalModelLigne(p_id, t_ligne) {
    // Appel la fonction qui crée un objet XmlHttp.
    var xmlHttp = GetXmlHttpObject();

    // Vérifie si le navigateur supporte l'AJAX
    if (xmlHttp == null) {
        alert ("Votre navigateur ne supporte pas AJAX");
        return;
    }
    // Création de l'url envoyee à l'aiguilleur.
    var url= "ajax.php?action=genererModalModelLigne&lig=" + t_ligne;
    // Création de la fonction qui sera appelé au changement de statut.
    xmlHttp.onreadystatechange= function StateChanged() {
        if (xmlHttp.readyState == 4) {
            document.getElementById(p_id).innerHTML = xmlHttp.responseText;
            $('#modalInfoModel').modal('toggle');
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
 * ajouterLigneFormModel : creer les input pour ajouter une ligne dans update model
 *
 * @param p_id : Contient l'id de l'element a modifier.
 * @param p_modal : true si on fait avec un modal
 ***/
function ajouterLigneFormModel(p_id, p_modal){
    //on recupere le nombre de prestations qui ont été ajoutées jusqu'a maintenant (moins celles qui ont ete supprimées)
    var nbInfos = parseInt(document.getElementById('nbInfos').value);
    //on recupere le nombre de prestations qui ont été ajoutées jusqu'a maintenant (y compris celles supprimées)
    var nbInfosTot = parseInt(document.getElementById('nbInfosTot').value);

    // On recupere l'id de la prestation à ajouter.
    var id_pres = document.getElementById('select_presta').value;

    // On recup le libelle de la ligne
    var lig_libelle = document.getElementById('lig_libelle').value;

    // On récupère les éléments du tableau HTML
    var element = document.getElementById(p_id).innerHTML;

    //On augmente le nombre de prestations ajoutées
    document.getElementById('nbInfos').value = parseInt(nbInfos+1);
    document.getElementById('nbInfosTot').value = parseInt(nbInfosTot+1);

    // Vérifie si le navigateur supporte l'AJAX
    if (xmlHttp == null) {
        alert ("Votre navigateur ne supporte pas AJAX");
        return;
    }
    // Création de l'url envoyee à l'aiguilleur.
    var url= "ajax.php?action=getPrestationTabFromID&presta=" + id_pres + "&nbInfos=" + nbInfos + "&nbInfosTot=" + nbInfosTot + "&lib=" + lig_libelle;

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
    if(isANumber(p_presta)) {
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

    // On recup le libelle de la ligne
    var lig_libelle = document.getElementById('lig_libelle').value;

    // On récupère les éléments du tableau HTML
    var element = document.getElementById(p_id).innerHTML;

    // Vérifie si le navigateur supporte l'AJAX
    if (xmlHttp == null) {
        alert ("Votre navigateur ne supporte pas AJAX");
        return;
    }
    // Création de l'url envoyee à l'aiguilleur.
    var url= "ajax.php?action=getPrestationTabFromID&presta=" + id_pres + "&nbInfos=" + nbInfos + "&nbInfosTot=" + nbInfosTot + "&lib=" + lig_libelle;

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

/*****
 * genererModalLigneFacture : genere le modal pour ajouter ou modifier une ligne de facture dans createFacture
 * Fonction AJAX qui passe par le fichier ajax.php. Paramètre de l'url : action.
 *
 * @param p_id : Contient l'id de l'element a modifier.
 * @param p_ligneFac : Contient le numero de la ligne de facture si on modifie une ligne (0 si c est un ajout)
 ***/
function genererModalLigneFacture(p_id, p_ligneFac) {
    // Appel la fonction qui crée un objet XmlHttp.
    var xmlHttp = GetXmlHttpObject(); 
    
    // Vérifie si le navigateur supporte l'AJAX
    if (xmlHttp == null) {
        alert ("Votre navigateur ne supporte pas AJAX");
        return;
    } 
    // Création de l'url envoyee à l'aiguilleur.
    var url= "ajax.php?action=genererModalLigneFacture&lf=" + p_ligneFac;
    // Création de la fonction qui sera appelé au changement de statut.
    xmlHttp.onreadystatechange= function StateChanged() {
        if (xmlHttp.readyState == 4) {
            document.getElementById(p_id).innerHTML = xmlHttp.responseText;
            //Si nous souhaitons modifier une ligne de prestation, nous allons preremplir le modal
            if(p_ligneFac != 0) {
                $('#code').val($('#code'+p_ligneFac).val());
                $('#libelle').val($('#libelle'+p_ligneFac).val());
                //On regarde quel radio bouton est coché
                var radio = $('#type'+p_ligneFac).val();
                var radio_id;
                switch(radio) {
                    case "H" : radio_id = "honos"; break; 
                    case "F" : radio_id = "frais"; break; 
                    case "T" : radio_id = "taxes"; break; 
                }
                $('#'+radio_id).prop('checked', true);
                $('#tva').val($('#tva'+p_ligneFac).val());
                $('#tarif').val($('#tarif'+p_ligneFac).val());
                $('#quantite').val($('#quantite'+p_ligneFac).val());
                $('#total').val($('#total'+p_ligneFac).val());
            }
            $('#modalInfoLigneFacture').modal('toggle');
        };
    };
    xmlHttp.open("GET",url,true); // Ouvre l'url
    xmlHttp.send(null); 
}

/*****
 * ajouterLigneFactureForm : cree les input d'une ligne de facture dans create facture (grace au modal)
 *
 * @param p_id : Contient l'id de l'element a modifier.
 * @param p_modal : true si on fait avec un modal (false si on fait dans la modification d'une facture)
 ***/
function ajouterLigneFactureForm(p_id, p_modal){
    //on recupere le nombre de lignes de facture qui ont été ajoutées jusqu'a maintenant (moins celles qui ont ete supprimées)
    var nbLignesFac = parseInt(document.getElementById('nbLignesFac').value);
    //on recupere le nombre de lignes de facture qui ont été ajoutées jusqu'a maintenant (y compris celles supprimées)
    var nbLignesFacTot = parseInt(document.getElementById('nbLignesFacTot').value);
    
    //on recupere le code de la ligne de facture
    var code = document.getElementById('code').value;
    //on recupere le libelle de la ligne de facture
    var libelle = document.getElementById('libelle').value;
    //on recupere le type
    var type;
    var radios_types = document.getElementsByName('type_ligne');
    
    // loop through list of radio buttons
    for (var i=0, len=radios_types.length; i<len; i++) {
        if ( radios_types[i].checked ) { // radio checked?
            type = radios_types[i].value; // if so, hold its value in val
            break; // and break out of for loop
        }
    }
    
    var type_lib;
    switch (type) {
        case "H" : type_lib = "Honoraires"; break;
        case "F" : type_lib = "Frais"; break;
        case "T" : type_lib = "Taxes"; break;
    }
    //on recupere la TVA
    var tva =  document.getElementById('tva').value;
    //on recupere le tarif
    var tarif =  document.getElementById('tarif').value;
    //on recupere la quantite
    var quantite =  document.getElementById('quantite').value;
    //on recupere le montant total
    var total =  document.getElementById('total').value;
    
    //On augmente le nombre de prestations ajoutées
    document.getElementById('nbLignesFac').value = parseInt(nbLignesFac+1); 
    document.getElementById('nbLignesFacTot').value = parseInt(nbLignesFacTot+1); 
    
    //On recupere ce qu'il y avait deja dans la table pour ne pas l'ecraser
    var element = document.getElementById(p_id).innerHTML;
    
    //On cree la ligne dans la table
    var ligne = "<tr id='ligne" + document.getElementById('nbLignesFacTot').value + "'>" 
                +"<td>" + code 
                + "<input type='hidden' value='" + code + "' name='code" + document.getElementById('nbLignesFacTot').value + "' id='code" + document.getElementById('nbLignesFacTot').value + "'/></td>"
                +"<td>" + libelle 
                + "<input type='hidden' value=\"" +  libelle + "\" name='libelle" + document.getElementById('nbLignesFacTot').value + "' id='libelle" + document.getElementById('nbLignesFacTot').value + "'/></td>"
                +"<td>" + type_lib
                + "<input type='hidden' value='" + type + "' name='type" + document.getElementById('nbLignesFacTot').value + "' id='type" + document.getElementById('nbLignesFacTot').value + "'/></td>"
                +"<td>" + tva + "%"
                + "<input type='hidden' value='" + tva + "' name='tva" + document.getElementById('nbLignesFacTot').value + "' id='tva" + document.getElementById('nbLignesFacTot').value + "'/></td>"
                +"<td>" + tarif
                + "<input type='hidden' value='" + tarif + "' name='tarif" + document.getElementById('nbLignesFacTot').value + "' id='tarif" + document.getElementById('nbLignesFacTot').value + "'/></td>"
                +"<td>" + quantite
                + "<input type='hidden' value='" + quantite + "' name='quantite" + document.getElementById('nbLignesFacTot').value + "' id='quantite" + document.getElementById('nbLignesFacTot').value + "'/></td>"
               +"<td>" + total
                + "<input type='hidden' value='" + total + "' name='total" + document.getElementById('nbLignesFacTot').value + "' id='total" + document.getElementById('nbLignesFacTot').value + "'/></td>"
                +"<td>"
                +"<a class='btn btn-primary btn-sm' onclick='";
    if(p_modal) {
        ligne += "genererModalLigneFacture(\"modalLigneFacture\"," + document.getElementById('nbLignesFacTot').value + ", " + p_modal + ")'>";
    } else {
        ligne += "modifierLigneFacture(" + document.getElementById('nbLignesFacTot').value + ")'>";
    }
    ligne += "<i class='icon-plus fa fa-edit'></i> Modifier</a>"
                +"</td>"
                +"<td>"
                    +"<a class='btn btn-danger btn-sm' onclick='supprimerLigneFactureForm(" + document.getElementById('nbLignesFacTot').value + ")'><i class='icon- fa fa-remove'></i> Supprimer</a>"
                +"</td>"
            +"</tr>";
    document.getElementById(p_id).innerHTML = element + ligne;
    //On supprime le modal en caché afin de pouvoir valider le formulaire (sinon le validator bootstrap trouve des inputs required non remplis dans le modal)
    if(p_modal) {
        document.getElementById('modalLigneFacture').innerHTML = "";
    } // sinon on vide les champs 
    else {
        $('#code').val("");
        $('#libelle').val("");
        $('#type_ligne').prop("checked", false);
        $('#tva').val(0);
        $('#tarif').val("");                    
        $('#quantite').val(1);
        $('#total').val("");
    }
}

/*****
 * modifierLigneFactureForm : modifie les inputs de la ligne de facture dans create facture (grace au modal)
 *
 * @param p_id : Contient l'id de l'element a modifier.
 * @param p_ligneFac : Contient le numero de la ligne a modifier.
 * @param p_modal : true si on modifie via un modal
 ***/
function modifierLigneFactureForm(p_id, p_ligneFac, p_modal){   
    //on recupere le code de la ligne de facture
    var code = document.getElementById('code').value;
    //on recupere le libelle de la ligne de facture
    var libelle = document.getElementById('libelle').value;
    //on recupere le type
    var type;
    var radios_types = document.getElementsByName('type_ligne');
    
    // loop through list of radio buttons
    for (var i=0, len=radios_types.length; i<len; i++) {
        if ( radios_types[i].checked ) { // radio checked?
            type = radios_types[i].value; // if so, hold its value in val
            break; // and break out of for loop
        }
    }
    
    var type_lib;
    switch (type) {
        case "H" : type_lib = "Honoraires"; break;
        case "F" : type_lib = "Frais"; break;
        case "T" : type_lib = "Taxes"; break;
    }
    
    //on recupere la TVA
    var tva =  document.getElementById('tva').value;
    //on recupere le tarif
    var tarif =  document.getElementById('tarif').value;
    //on recupere la quantite
    var quantite =  document.getElementById('quantite').value;
    //on recupere le montant total
    var total =  document.getElementById('total').value;
    
    //On modifier la ligne dans la table
    var ligne = "<td>" + code 
                + "<input type='hidden' value='" + code + "' name='code" + p_ligneFac + "' id='code" + p_ligneFac + "'/></td>"
                +"<td>" + libelle
                +"<input type='hidden' value=\"" + libelle + "\" name='libelle" + p_ligneFac + "' id='libelle" + p_ligneFac + "'/></td>"
                +"<td>" + type_lib
                +"<input type='hidden' value='" + type + "' name='type" + p_ligneFac + "' id='type" + p_ligneFac + "'/></td>"
                +"<td>" + tva
                +"<input type='hidden' value='" + tva + "' name='tva" + p_ligneFac + "' id='tva" + p_ligneFac + "'/></td>"
                +"<td>" + tarif
                +"<input type='hidden' value='" + tarif + "' name='tarif" + p_ligneFac + "' id='tarif" + p_ligneFac + "'/></td>"
                +"<td>" + quantite
                +"<input type='hidden' value='" + quantite + "' name='quantite" + p_ligneFac + "' id='quantite" + p_ligneFac + "'/></td>"
                +"<td>" + total
                +"<input type='hidden' value='" + total + "' name='total" + p_ligneFac + "' id='total" + p_ligneFac + "'/></td>"
                +"<td align='center'>"
                    +"<a class='btn btn-primary btn-sm' onclick='";
    if(p_modal) {
        ligne += "genererModalLigneFacture(\"modalLigneFacture\"," + p_ligneFac + ", " + p_modal + ")'>";
    } else {
        ligne += "modifierLigneFacture(\"" + p_ligneFac + "\")'>";
    }
    ligne += "<i class='icon-plus fa fa-edit'></i> Modifier</a>"
                +"</td>"
                +"<td align='center'>"
                    +"<a class='btn btn-danger btn-sm'";
    if(isANumber(p_ligneFac)) {
        ligne += " onclick='supprimerLigneFactureForm(" + p_ligneFac + ")'";
    } else {
        ligne += "disabled";
    }
    ligne += "><i class='icon- fa fa-remove'></i> Supprimer</a>";
                +"</td>";
        
    document.getElementById(p_id).innerHTML = ligne;
    //On supprime le modal en caché afin de pouvoir valider le formulaire (sinon le validator bootstrap trouve des inputs required non remplis dans le modal)
    if(p_modal) {
        document.getElementById('modalLigneFacture').innerHTML = "";
    } else {
        document.getElementById('button_action').innerHTML = "<button type='button' class='btn btn-default' disabled name='subAction' id='subAction' onclick='ajouterLigneFactureForm(\"listeLignesFacture\", false);'><i class='icon-plus fa fa-plus'></i> Ajouter une ligne de facture</button>";
        document.getElementById('panel_action').innerHTML = "Ajout d'une ligne de facture";
        //on remet ensuite les inputs a vide
        $('#code').val("");
        $('#libelle').val("");
        $('#type_ligne').prop("checked", false);
        $('#tva').val(0);
        $('#tarif').val("");                    
        $('#quantite').val(1);
        $('#total').val("");
    }
}

/*****
 * supprimerLigneFactureForm : supprime la ligne de facture choisie (cree grace au modal)
 *
 * @param p_num : Contient le numero de la ligne a supprimer
 ***/
function supprimerLigneFactureForm(p_num){
    //on recupere le nombre de lignes de facture qui ont été ajoutées jusqu'a maintenant (moins celles qui ont ete supprimées)
    var nbLignesFac = parseInt(document.getElementById('nbLignesFac').value);
    
    //On decrement le nombre de lignes de facture ajoutées
    document.getElementById('nbLignesFac').value = parseInt(nbLignesFac-1);  
    
    //On modifier la ligne dans la table en mode supprimé
    var ligne = "<input type='hidden' value='" + p_num + "' name='supp" + p_num + "' id='supp" + p_num + "'/>";
    document.getElementById('ligne'+p_num).innerHTML = ligne;
}

/*****
 * checkLigneFacture : Verifie que les champs soient bien remplis pour ajouter ou modifier une ligne de facture
 *
 * @param p_id : Contient l'id du bouton de submit de la modal a bloquer ou non
 ***/
function checkLigneFacture(p_id){
    //si tous les champs sont remplis correctement, alors le bouton de submit du modal sera activé
    var buttonOk = true;
    var totalOk = true;
    
    var code = document.getElementById('code').value;
    if(code == "") {
        buttonOk = false;
    }
    //on recupere le libelle de la ligne de facture
    var libelle = document.getElementById('libelle').value;
    if(libelle == "") {
        buttonOk = false;
    }
    
    //on recupere le tarif
    var tarif =  document.getElementById('tarif').value;
    if(!isANumber(tarif)) {
        buttonOk = false;
        totalOk = false;
    }
    
    //on recupere la quantite
    var quantite = document.getElementById('quantite').value;
    if((quantite == "") || (!isANumber(quantite))) {
        buttonOk = false;
        totalOk = false;
    }
    
    //on recupere les types possibles pour verifier qu'un au moins est coché
    var honos = document.getElementById('honos').checked;
    var frais = document.getElementById('frais').checked;
    var taxes = document.getElementById('taxes').checked;
    if(!honos && !frais && !taxes) {
        buttonOk = false;
    }
    
    //on modifie le total
    if(totalOk) {
        document.getElementById('total').value = tarif*quantite;
    } else {
        document.getElementById('total').value = 0;
    }
    
    if(buttonOk)
        document.getElementById(p_id).disabled = false;
    else
        document.getElementById(p_id).disabled = true;    
}

/*****
 * genererLibelleCode : genere le libelle associé au code de nomenclature choisi pour l'ajout d'une ligne de facture via le modal
 * Fonction AJAX qui passe par le fichier ajax.php. Paramètre de l'url : action.
 *
 * @param p_id : Contient l'id de l'element a modifier.
 * @param p_value : Contient le code de nomenclature
 ***/
function genererLibelleCode(p_id, p_value) {
    // Appel la fonction qui crée un objet XmlHttp.
    var xmlHttp = GetXmlHttpObject(); 
    
    // Vérifie si le navigateur supporte l'AJAX
    if (xmlHttp == null) {
        alert ("Votre navigateur ne supporte pas AJAX");
        return;
    } 
    // Création de l'url envoyee à l'aiguilleur.
    var url= "ajax.php?action=genererLibelleCode&code=" + p_value;
    // Création de la fonction qui sera appelé au changement de statut.
    xmlHttp.onreadystatechange= function StateChanged() {
        $(p_id).val(xmlHttp.responseText);
    };
    xmlHttp.open("GET",url,true); // Ouvre l'url
    xmlHttp.send(null); 
}

/*****
 * genererModalAchat : genere le modal pour ajouter ou modifier un achat dans createFacture
 * Fonction AJAX qui passe par le fichier ajax.php. Paramètre de l'url : action.
 *
 * @param p_id : Contient l'id de l'element a modifier.
 * @param p_ligneFac : Contient le numero de l'achat si on modifie une ligne (0 si c est un ajout)
 ***/
function genererModalAchat(p_id, p_achat) {
    // Appel la fonction qui crée un objet XmlHttp.
    var xmlHttp = GetXmlHttpObject(); 
    
    // Vérifie si le navigateur supporte l'AJAX
    if (xmlHttp == null) {
        alert ("Votre navigateur ne supporte pas AJAX");
        return;
    } 
    // Création de l'url envoyee à l'aiguilleur.
    var url= "ajax.php?action=genererModalAchat&ac=" + p_achat;
    // Création de la fonction qui sera appelé au changement de statut.
    xmlHttp.onreadystatechange= function StateChanged() {
        if (xmlHttp.readyState == 4) {
            document.getElementById(p_id).innerHTML = xmlHttp.responseText;
            //Si nous souhaitons modifier une ligne de prestation, nous allons preremplir le modal
            if(p_achat != 0) {
                $('#code').val($('#code'+p_achat).val());
                $('#libelle').val($('#libelle'+p_achat).val());
                $('#tarif').val($('#tarif'+p_achat).val());
                $('#quantite').val($('#quantite'+p_achat).val());
                $('#total').val($('#total'+p_achat).val());
            }
            $('#modalInfoAchat').modal('toggle');
        };
    };
    xmlHttp.open("GET",url,true); // Ouvre l'url
    xmlHttp.send(null); 
}

/*****
 * ajouterAchatForm : cree les input d'un achat dans create facture (grace au modal)
 *
 * @param p_id : Contient l'id de l'element a modifier.
 * @param p_modal : true si on fait avec un modal (false si on fait dans la modification d'une facture)
 ***/
function ajouterAchatForm(p_id, p_modal){
    //on recupere le nombre d'achats qui ont été ajoutées jusqu'a maintenant (y compris ceux supprimées)
    var nbAchatsTot = parseInt(document.getElementById('nbLignesFacTot').value);
    
    //on recupere le code de l'achat
    var code = document.getElementById('code').value;
    //on recupere le libelle de l'achat
    var libelle = document.getElementById('libelle').value;
    //on recupere le tarif
    var tarif =  document.getElementById('tarif').value;
    //on recupere la quantite
    var quantite =  document.getElementById('quantite').value;
    //on recupere le montant total
    var total =  document.getElementById('total').value;
    
    //On augmente le nombre de prestations ajoutées
    document.getElementById('nbLignesFac').value = parseInt(nbLignesFac+1); 
    document.getElementById('nbLignesFacTot').value = parseInt(nbLignesFacTot+1); 
    
    //On recupere ce qu'il y avait deja dans la table pour ne pas l'ecraser
    var element = document.getElementById(p_id).innerHTML;
    
    //On cree la ligne dans la table
    var ligne = "<tr id='ligne" + document.getElementById('nbLignesFacTot').value + "'>" 
                +"<td>" + code 
                + "<input type='hidden' value='" + code + "' name='code" + document.getElementById('nbLignesFacTot').value + "' id='code" + document.getElementById('nbLignesFacTot').value + "'/></td>"
                +"<td>" + libelle 
                + "<input type='hidden' value='" + libelle + "' name='libelle" + document.getElementById('nbLignesFacTot').value + "' id='libelle" + document.getElementById('nbLignesFacTot').value + "'/></td>"
                +"<td>" + tarif
                + "<input type='hidden' value='" + tarif + "' name='tarif" + document.getElementById('nbLignesFacTot').value + "' id='tarif" + document.getElementById('nbLignesFacTot').value + "'/></td>"
                +"<td>" + quantite
                + "<input type='hidden' value='" + quantite + "' name='quantite" + document.getElementById('nbLignesFacTot').value + "' id='quantite" + document.getElementById('nbLignesFacTot').value + "'/></td>"
                +"<td>" + total
                + "<input type='hidden' value='" + total + "' name='total" + document.getElementById('nbLignesFacTot').value + "' id='total" + document.getElementById('nbLignesFacTot').value + "'/></td>"
                +"<td>"
                +"<a class='btn btn-primary btn-sm' onclick='";
    if(p_modal) {
        ligne += "genererModalLigneFacture(\"modalLigneFacture\"," + document.getElementById('nbLignesFacTot').value + ", " + p_modal + ")'>";
    } else {
        ligne += "modifierLigneFacture(" + document.getElementById('nbLignesFacTot').value + ")'>";
    }
    ligne += "<i class='icon-plus fa fa-edit'></i> Modifier</a>"
                +"</td>"
                +"<td>"
                    +"<a class='btn btn-danger btn-sm' onclick='supprimerLigneFactureForm(" + document.getElementById('nbLignesFacTot').value + ")'><i class='icon- fa fa-remove'></i> Supprimer</a>"
                +"</td>"
            +"</tr>";
    document.getElementById(p_id).innerHTML = element + ligne;
    //On supprime le modal en caché afin de pouvoir valider le formulaire (sinon le validator bootstrap trouve des inputs required non remplis dans le modal)
    if(p_modal) {
        document.getElementById('modalLigneFacture').innerHTML = "";
    } // sinon on vide les champs 
    else {
        $('#code').val("");
        $('#libelle').val("");
        $('#tarif').val("");                    
        $('#quantite').val(1);
        $('#total').val("");
    }
}

/*****
 * checkAchat : Verifie que les champs soient bien remplis pour ajouter un achat
 *
 * @param p_id : Contient l'id du bouton de submit de la modal a bloquer ou non
 ***/
function checkAchat(p_id){
    alert("check");
}

/*****
 * genererModalReglement : genere le modal pour ajouter un reglement dans createFacture
 * Fonction AJAX qui passe par le fichier ajax.php. Paramètre de l'url : action.
 *
 * @param p_id : Contient l'id de l'element a modifier.
 ***/
function genererModalReglement(p_id) {
    // Appel la fonction qui crée un objet XmlHttp.
    var xmlHttp = GetXmlHttpObject(); 
    
    // Vérifie si le navigateur supporte l'AJAX
    if (xmlHttp == null) {
        alert ("Votre navigateur ne supporte pas AJAX");
        return;
    } 
    // Création de l'url envoyee à l'aiguilleur.
    var url= "ajax.php?action=genererModalReglement";
    // Création de la fonction qui sera appelé au changement de statut.
    xmlHttp.onreadystatechange= function StateChanged() {
        if (xmlHttp.readyState == 4) {
            document.getElementById(p_id).innerHTML = xmlHttp.responseText;
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd',
                startDate: '-6m',
                endDate: 'd', 
                autoclose: true
            });
            $('#modalInfoReglement').modal('toggle');
        };
    };
    xmlHttp.open("GET",url,true); // Ouvre l'url
    xmlHttp.send(null); 
}

/*****
 * ajouterReglementForm : cree les input d'un reglement dans create facture (grace au modal)
 *
 * @param p_id : Contient l'id de l'element a modifier.
 * @param p_modal : true si on fait avec un modal (false si on fait dans la modification d'une facture)
 ***/
function ajouterReglementForm(p_id, p_modal){
    //on recupere le nombre de reglements qui ont été ajoutées jusqu'a maintenant (y compris celles supprimées)
    var nbReglementsTot = parseInt(document.getElementById('nbReglementsTot').value);
    
    //on recupere la date
    var date = document.getElementById('date').value;
    //on recupere le montant
    var montant = document.getElementById('montant').value;
    //on recupere la devise
    var devise = document.getElementById('devise').value;
    
    //On augmente le nombre de reglements ajoutées
    document.getElementById('nbReglementsTot').value = parseInt(nbReglementsTot+1); 
    
    //On recupere ce qu'il y avait deja dans la table pour ne pas l'ecraser
    var element = document.getElementById(p_id).innerHTML;
    
    //On cree la ligne dans la table
    var ligne = "<tr id='ligne" + document.getElementById('nbReglementsTot').value + "'>" 
                +"<td>" + date 
                + "<input type='hidden' value='" + date + "' name='date" + document.getElementById('nbReglementsTot').value + "' id='date" + document.getElementById('nbReglementsTot').value + "'/></td>"
                +"<td>" + montant
                + "<input type='hidden' value='" + montant + "' name='montant" + document.getElementById('nbReglementsTot').value + "' id='montant" + document.getElementById('nbReglementsTot').value + "'/></td>"
                +"<td>" + devise
                + "<input type='hidden' value='" + devise + "' name='devise" + document.getElementById('nbReglementsTot').value + "' id='devise" + document.getElementById('nbReglementsTot').value + "'/></td>"
                +"<td>"
                    +"<a class='btn btn-danger btn-sm' onclick='supprimerReglementForm(" + document.getElementById('nbReglementsTot').value + ")'><i class='icon- fa fa-remove'></i> Supprimer</a>"
                +"</td>"
            +"</tr>";
    document.getElementById(p_id).innerHTML = element + ligne;
    //On supprime le modal en caché afin de pouvoir valider le formulaire (sinon le validator bootstrap trouve des inputs required non remplis dans le modal)
    if(p_modal) {
        document.getElementById('modalReglement').innerHTML = "";
    } // sinon on vide les champs 
    else {
        $('#date').val("");
        $('#montant').val("");
    }
}

/*****
 * supprimerReglementForm : supprime la ligne du reglement choisi (cree grace au modal)
 *
 * @param p_num : Contient le numero de la ligne a supprimer
 ***/
function supprimerReglementForm(p_num){
    //On modifier la ligne dans la table en mode supprimé
    var ligne = "<input type='hidden' value='" + p_num + "' name='supp" + p_num + "' id='supp" + p_num + "'/>";
    document.getElementById('ligne'+p_num).innerHTML = ligne;
}

/*****
 * checkReglement : Verifie que les champs soient bien remplis pour ajouter un reglement
 *
 * @param p_id : Contient l'id du bouton de submit de la modal a bloquer ou non
 ***/
function checkReglement(p_id){
    //si tous les champs sont remplis correctement, alors le bouton de submit du modal sera activé
    var buttonOk = true;
    
    var date = document.getElementById('date').value;
    if(date == "") {
        buttonOk = false;
    }
    
    //on recupere le montant
    var montant =  document.getElementById('montant').value;
    if(!isANumber(montant)) {
        buttonOk = false;
    }
    
    if(buttonOk)
        document.getElementById(p_id).disabled = false;
    else
        document.getElementById(p_id).disabled = true;    
}

/*****
 * supModelPrestaUpdateEx : supprime une ligne de prestation existante dans la page modif des modeles
 *
 * @param String p_lign_id : id de la ligne a sup.
 * @param String num_lig : numero de la ligne dans le tableau
 ***/
function supModelPrestaUpdateEx(p_lign_id) {
    //On cree la ligne dans la table
    var ligne = "<input type='hidden' value='" + p_lign_id + "' name='" + p_lign_id + "' id='" + p_lign_id + "'/>";
    document.getElementById('ligne'+p_lign_id).innerHTML = ligne;
}

/******
 * emptyTabPresta : Vide le tableau lorsque le user change de type d'operation afin d'eviter les incoherences entre type d'ope et presta.
 *
 * @param String id : id du tableau a vider
 *****/
function emptyTabPresta(id_tab) {
    document.getElementById(id_tab).innerHTML  = "";
    document.getElementById("nbInfos").value = 0;
    document.getElementById("nbInfosTot").value = 0;
}




/*---fONCTION ABDOUL*/

function genererListeNomModele(p_id,p_value){
    // Appel la fonction qui cr�e un objet XmlHttp.
    var xmlHttp = GetXmlHttpObject(); 
    // V�rifie si le navigateur supporte l'AJAX
    if (xmlHttp == null) {
        alert ("Votre navigateur ne supporte pas AJAX");
        return;
    } 
    // Cr�ation de l'url envoyee � l'aiguilleur.
    var url= "ajax.php?action=genererListeNomModele&mod=" + p_value;

    // Cr�ation de la fonction qui sera appel� au changement de statut.
    xmlHttp.onreadystatechange= function StateChanged() {
        if (xmlHttp.readyState == 4) {
            var jsonData = $.parseJSON(xmlHttp.responseText);

            //on recupere la reference a l'element select que l'on veut peupler
            var $select = $(p_id);
            $select.empty();    
            //$select.select2('data', null);    
            $select.append('<option></option>');
            //$select.select2({placeholder:"Choisissez un type d'operation..."});
            $.each(jsonData,function(key, value) 
            {
                $select.append('<option value=' + key + '>' + value + '</option>');
            });
        };
    };
    xmlHttp.open("GET",url,true); // Ouvre l'url
    xmlHttp.send(null); 
}





