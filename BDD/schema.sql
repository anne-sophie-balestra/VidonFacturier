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

/* Drop de tables si existes */
DROP TABLE type_ligne;
DROP TABLE prestation;
DROP TABLE type_facture;
DROP TABLE type_dossier;
DROP TABLE type_operation;
DELETE FROM sequence WHERE clef = 'prestation';
DELETE FROM sequence WHERE clef = 'prestation_generale';

/* Creation de tables */
CREATE TABLE type_dossier
(
    t_dos_id VARCHAR(11) NOT NULL PRIMARY KEY,
    t_dos_creadate TIMESTAMP,
    t_dos_moddate TIMESTAMP,
    t_dos_creauser VARCHAR(3),
    t_dos_moduser VARCHAR(3),
    t_dos_entite VARCHAR(255),
    t_dos_type VARCHAR(255)
);

CREATE TABLE type_operation
(
    t_ope_id VARCHAR(11) NOT NULL PRIMARY KEY,
    t_ope_creadate TIMESTAMP,
    t_ope_moddate TIMESTAMP,
    t_ope_creauser VARCHAR(3),
    t_ope_moduser VARCHAR(3),
    t_ope_libelle VARCHAR(255)
);

CREATE TABLE prestation
(
    pres_id VARCHAR(11) NOT NULL PRIMARY KEY,
    pres_creadate TIMESTAMP,
    pres_moddate TIMESTAMP,
    pres_creauser VARCHAR(3),
    pres_moduser VARCHAR(3),
    pres_id_general VARCHAR(11),
    pres_rf_nom VARCHAR(11),
    pres_prestation VARCHAR(255),
    pres_type VARCHAR(5),
    pres_libelle_ligne_fac VARCHAR(255),
    pres_t_tarif VARCHAR(10),
    pres_tarif_std NUMERIC(10,2) DEFAULT 0,
    pres_tarif_jr NUMERIC(10,2) DEFAULT 0,
    pres_tarif_sr NUMERIC(10,2) DEFAULT 0,
    pres_tarif_mgr NUMERIC(10,2) DEFAULT 0,
    pres_repartition_cons NUMERIC (3),
    pres_rf_pay VARCHAR(11),
    pres_rf_typ_dossier VARCHAR(11),
    pres_rf_typ_operation VARCHAR(11),

    FOREIGN KEY (pres_rf_typ_dossier) REFERENCES type_dossier(t_dos_id),
    FOREIGN KEY (pres_rf_typ_operation) REFERENCES type_operation(t_ope_id),
    FOREIGN KEY (pres_rf_nom) REFERENCES nomenclature(nom_id),
    FOREIGN KEY (pres_rf_pay) REFERENCES pays(pay_id)
);

CREATE TABLE type_facture
(
    t_fac_id VARCHAR (11) NOT NULL PRIMARY KEY,
    t_fac_rf_typdos VARCHAR (11),
    t_fac_rf_ent VARCHAR(11),
    t_fac_modelname VARCHAR(255),
    t_fac_creadate TIMESTAMP WITH TIME ZONE,
    t_fac_moddate TIMESTAMP WITH TIME ZONE,
    t_fac_creauser VARCHAR(3),
    t_fac_moduser VARCHAR(3),
    t_fac_type VARCHAR(200),
    t_fac_objet VARCHAR(255),
    t_fac_rf_ope VARCHAR(11),
    t_fac_tauxtva NUMERIC,
    t_fac_langue VARCHAR (4),
    t_fac_area VARCHAR (50),

    FOREIGN KEY (t_fac_rf_typdos) REFERENCES type_dossier(t_dos_id),
    FOREIGN KEY (t_fac_rf_ent) REFERENCES entite(ent_id)
);

CREATE TABLE type_ligne
(
    t_lig_id VARCHAR(11) NOT NULL PRIMARY KEY,
    t_lig_rf_pres VARCHAR(11) NOT NULL,
    t_lig_creadate TIMESTAMP WITH TIME ZONE,
    t_lig_moddate TIMESTAMP WITH TIME ZONE,
    t_lig_creauser VARCHAR(3),
    t_lig_moduser VARCHAR(3),
    t_lig_rubrique VARCHAR(255),
    t_lig_libelle VARCHAR(255),
    t_lig_rf_typ_fac VARCHAR(11),

    FOREIGN KEY (t_lig_rf_typ_fac) REFERENCES type_facture(t_fac_id),
    FOREIGN KEY (t_lig_rf_pres) REFERENCES prestation(pres_id)
);

/* Insertion tables de typage */
INSERT INTO type_dossier (t_dos_id, t_dos_creadate, t_dos_moddate, t_dos_creauser, t_dos_moduser, t_dos_entite, t_dos_type) VALUES ('TDOre000001', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'GLS', 'GLS', 'Brevet', 'Brevet');
INSERT INTO type_dossier (t_dos_id, t_dos_creadate, t_dos_moddate, t_dos_creauser, t_dos_moduser, t_dos_entite, t_dos_type) VALUES ('TDOre000002', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'GLS', 'GLS', 'Brevet', 'Etude');
INSERT INTO type_dossier (t_dos_id, t_dos_creadate, t_dos_moddate, t_dos_creauser, t_dos_moduser, t_dos_entite, t_dos_type) VALUES ('TDOre000003', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'GLS', 'GLS', 'Juridique', 'DessinModele');
INSERT INTO type_dossier (t_dos_id, t_dos_creadate, t_dos_moddate, t_dos_creauser, t_dos_moduser, t_dos_entite, t_dos_type) VALUES ('TDOre000004', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'GLS', 'GLS', 'Juridique', 'Marque');

INSERT INTO type_operation (t_ope_id, t_ope_creadate, t_ope_moddate, t_ope_creauser, t_ope_moduser, t_ope_libelle) VALUES ('TOPre000001', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'GLS', 'GLS', 'Dépôt');
INSERT INTO type_operation (t_ope_id, t_ope_creadate, t_ope_moddate, t_ope_creauser, t_ope_moduser, t_ope_libelle) VALUES ('TOPre000002', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'GLS', 'GLS', 'Procédure');
INSERT INTO type_operation (t_ope_id, t_ope_creadate, t_ope_moddate, t_ope_creauser, t_ope_moduser, t_ope_libelle) VALUES ('TOPre000003', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'GLS', 'GLS', 'Délivrance');
INSERT INTO type_operation (t_ope_id, t_ope_creadate, t_ope_moddate, t_ope_creauser, t_ope_moduser, t_ope_libelle) VALUES ('TOPre000004', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'GLS', 'GLS', 'Enregistrement');
INSERT INTO type_operation (t_ope_id, t_ope_creadate, t_ope_moddate, t_ope_creauser, t_ope_moduser, t_ope_libelle) VALUES ('TOPre000005', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'GLS', 'GLS', 'Renouvellement');
INSERT INTO type_operation (t_ope_id, t_ope_creadate, t_ope_moddate, t_ope_creauser, t_ope_moduser, t_ope_libelle) VALUES ('TOPre000006', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'GLS', 'GLS', 'Surveillance');
INSERT INTO type_operation (t_ope_id, t_ope_creadate, t_ope_moddate, t_ope_creauser, t_ope_moduser, t_ope_libelle) VALUES ('TOPre000007', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'GLS', 'GLS', 'Recherches');
INSERT INTO type_operation (t_ope_id, t_ope_creadate, t_ope_moddate, t_ope_creauser, t_ope_moduser, t_ope_libelle) VALUES ('TOPre000008', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'GLS', 'GLS', 'Divers');

/* DUMMY type_facture */

