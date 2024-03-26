<?php

class connection
{
	private $mysql_server;
	private $mysql_user;
	private $mysql_pass;
	private $mysql_db;

    private $CONNECTION;

	public function __construct()
	{
		$this->mysql_server = "localhost";
		$this->mysql_user = "Stian Arnesen";
		$this->mysql_pass = "dynamicgaming";
		$this->mysql_db = "se_log";
	}
    private function connect()
    {
        $this->CONNECTION = new mysqli($this->mysql_server, $this->mysql_user, $this->mysql_pass , $this->mysql_db);
    }
}
