<?php

class RemoteAddressLog
{

	private $CONNECTION;

	public function __construct()
	{
		if($this->_connect())
		{

		}
	}
	public function insertAddressToDatabaseLog($_IP, $_USER_ID)
	{
		$SQL = "INSERT INTO user_ip(user_id, ip_address) VALUES('$_USER_ID', '$_IP')";
		if($QUERY = mysqli_query($this->CONNECTION,$SQL))
		{
			return true;
		}
		return false;
	}
	private function _connect()
	{
		require_once( __DIR__ . "/../../connect/database.php");

		if($this->CONNECTION = Database::getConnection()){
			return true;
		}
		else{
			return false;
		}
	}
}