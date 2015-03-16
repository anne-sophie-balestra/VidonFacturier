<?php
/********************************************
* SPDO.php                                  *
* gestion de la BDD (PDO)                   *
*                                           *
* Auteurs : Anne-Sophie Balestra            *
*           Abdoul Wahab Haidara            *
*           Yvan-Christian Maso             *
*           Baptiste Quere                  *
*           Yoann Le Taillanter             *
*                                           *
* Date de creation : 09/02/2015             *
********************************************/

class SPDO extends PDO
{
    private $driver = 'pgsql';
    private $host = 'localhost';
    private $port = '5432';
    private $dbname = 'solent2';
    private $username = 'Miage2015';
    private $password = 'miage2015';


    //constructor for parent class
    public function __construct (){

        /** @var $strConnection string */
        $strConnection = "{$this->driver}:host={$this->host};port={$this->port};dbname={$this->dbname}";

        parent::__construct($strConnection, $this->username, $this->password);
        parent::setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    }
}