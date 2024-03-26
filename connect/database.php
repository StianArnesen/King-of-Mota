<?php

class Database {

    private static $db;
    private $connection;

    private function __construct() {
        if($this->connect())
        {

        }
    }
    private function connect()
    {
        $ROOT = $_SERVER['DOCUMENT_ROOT'];
        
        include_once($ROOT.  "/connect/connection.php");

        $StaticConnectionClass = new StaticConnection();

        $CON_PARAM = $StaticConnectionClass->getSecureConnectionParams();

        if($this->connection = mysqli_connect($CON_PARAM[0],$CON_PARAM[1], $CON_PARAM[2],$CON_PARAM[3]))
        {
            return true;
        }
        return false;
    }

    function __destruct() {
        
    }

    public static function getConnection() {
        if (self::$db == null) {
            self::$db = new Database();
        }
        return self::$db->connection;
    }
}

?>