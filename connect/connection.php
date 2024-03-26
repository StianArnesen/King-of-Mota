<?php


class StaticConnection
{
    private $mysql_host;
    private $mysql_database;
    private $mysql_user;
    private $mysql_password;

    public $CONNECTION;
    public $SECURE_CONNECTION;

    private $BOOL_CONNECTED;

    public function __construct()
    {
        $this->mysql_host = "localhost";
        $this->mysql_database = "motagamedata";
        $this->mysql_user = "Stian Arnesen";
        $this->mysql_password = "";

        if($this->connect())
        {
            $this->BOOL_CONNECTED = true;
        }
        else
        {
            $this->BOOL_CONNECTED = false;
        }
        if($this->secureConnect())
        {

        }
    }
    public function getSecureConnectionParams()
    {
        $RESULT = array();

        $RESULT[0] = $this->mysql_host;
        $RESULT[1] = $this->mysql_user;
        $RESULT[2] = $this->mysql_password;
        $RESULT[3] = $this->mysql_database;

        return $RESULT;
    }
    private function connect()
    {
        $this->CONNECTION = mysqli_connect($this->mysql_host, $this->mysql_user, $this->mysql_password, $this->mysql_database);
        if($this->CONNECTION) return true;
        else
            return false;
    }
    private function secureConnect()
    {
        if($this->SECURE_CONNECTION = new mysqli($this->mysql_host, $this->mysql_user, $this->mysql_password, $this->mysql_database))
        {
            return true;
        }
        return false;
    }
    public function SECURE_CONNECTION()
    {
    	$secCon = new mysqli($this->mysql_host, $this->mysql_user, $this->mysql_password, $this->mysql_database);

        return $secCon;
    }
}