/********************************************
* footer.php                                *
* Fermeture de Html + script Js             *
*                                           *
* Auteurs : Anne-Sophie Balestra            *
*           Abdoul Wahab Haidara            *
*           Yvan-Christian Maso             *
*           Baptiste Quere                  *
*           Yoann Le Taillanter             *
*                                           *
* Date de creation : 06/02/2015             *
********************************************/


CREATE TABLE type_dossier
(
    t_dos_id VARCHAR(11) NOT NULL PRIMARY KEY,
    t_dos_creadate TIMESTAMP, 
    t_dos_moddate TIMESTAMP,
    t_dos_entite VARCHAR(255),
    t_dos_type VARCHAR(255)
);

CREATE TABLE type_operation
(
    t_ope_id VARCHAR(11) NOT NULL PRIMARY KEY,
    t_ope_creadate TIMESTAMP,
    t_ope_moddate TIMESTAMP,
    t_ope_libelle VARCHAR(255)
);

CREATE TABLE prestation 
(
    pres_id VARCHAR(11) NOT NULL PRIMARY KEY,
    pres_creadate TIMESTAMP,
    pres_moddate TIMESTAMP,
    pres_rf_nom_code VARCHAR(255),
    pres_prestation VARCHAR(255), 
    pres_libelle VARCHAR(255), 
    pres_t_tarif VARCHAR(10),
    pres_tarif_std NUMERIC(10,2) DEFAULT 0,
    pres_repartition_cons NUMERIC (3),
    pres_pays VARCHAR(255),
    pres_rf_type_dossier VARCHAR(11),
    pres_rf_typ_operation VARCHAR(11),
    FOREIGN KEY (pres_re_type_dossier) REFERENCES type_dossier(t_dos_id), 
    FOREIGN KEY (pres_re_type_operation) REFERENCES type_operation(t_ope_id) 
);