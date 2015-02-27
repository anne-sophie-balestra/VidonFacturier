<?php
require_once("codebase/connector/grid_connector.php"); // includes the appropriate connector file
require_once("codebase/connector/db_postgre.php");

$res=pg_connect("host=localhost port=5432 dbname=Solent4 user=postgres password=postgres");              //connects to a server that contains the desired DB
                              // connects to the DB. 'sampledb' is the name of our DB
$conn = new GridConnector($res,"Postgre");                    // connector initialization
$conn->render_table("dossier","dos_id","dos_num,dos_type,dos_titre,dos_titulaire_saisi");   // data configuration
