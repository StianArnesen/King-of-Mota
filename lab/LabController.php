<?php


class LabController
{
	private $CONNECTION;

	private $USER;

	public function __construct()
	{
		if($this->_connect())
		{
			if($this->_loadUser())
			{
				
			}
			else
			{
				die("Failed to load userinfo!");
			}
		}
		else
		{
			die("LabController: Connection failed! ");
		}
	}


	public function getLabInfo()
	{
		$USER = $this->USER;
		$USER_ID = $USER->getUserId();

		$SQL = "SELECT * FROM lab_list WHERE user_id='$USER_ID' LIMIT 1";
		if($QUERY = mysqli_query($this->CONNECTION, $SQL))
		{
			if($RESULT = mysqli_fetch_array($QUERY))
			{
				return $RESULT;
			}
		}
		return null;
	}


	private function _loadUser()
	{
		$USER;
		require_once(__DIR__ . "/../common/session/sessioninfo.php");
		if($USER = new User(-1,-1))
		{
			$this->USER = $USER;

			if($USER->isLoggedIn())
			{
				return true;
			}
		}
		return false;
	}
	private function _connect()
	{
		require_once( __DIR__ . "/../connect/Database.php");

		if($this->CONNECTION = Database::getConnection())
		{
			return true;
		}
		return false;
	}
}