<?php

class CrewMethod
{
    private $CONNECTION;

    public function __construct()
    {
        if($this->connect())
        {

        }
    }
    private function connect()
    {
        include_once("../connect/connection.php");

        $StaticConnection = new StaticConnection();

        if($this->CONNECTION = $StaticConnection->SECURE_CONNECTION)
        {
            return true;
        }
        return false;
    }
}

$CREW = new CrewMethod();