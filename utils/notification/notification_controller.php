<?php

class NotificationController
{
	private $_CONNECTION;

	private $_USER;

	public function __construct()
	{
		if($this->connect())
		{
			if($this->loadUser())
			{
				
			}
			else
			{
				die('not logged in.');
			}
		}
		else
		{
			die('Connection failed.');
		}
	}
	public function getNotificationListJSON($offset, $amount)
	{
		$USER = $this->_USER;

		$USER_ID = $USER->getUserId();

		$SQL = "SELECT * FROM notifications WHERE user_id='$USER_ID' ORDER BY not_time DESC LIMIT $offset, $amount ";

		if($QUERY = mysqli_query( $this->_CONNECTION,$SQL))
		{
			$RESULT = array();
			while($MSG = mysqli_fetch_array($QUERY))
			{

                $USR_ID_A   = $MSG['user_id_a'];
                $USR_ID_B   = $MSG['user_id_b'];

				$USER_A     = $this->getUserInfo($USR_ID_A);
                $USER_B     = $this->getUserInfo($USR_ID_B);
				
				$NOT_ID 	= $MSG['not_id'];
				$NOT_TYPE 	= $MSG['not_type'];
				$NOT_STATUS = $MSG['not_status'];
				$NOT_TIME 	= ($MSG['not_time'] - time());
				
				if(isset($USER_A) && $USER_A)
				{
					$USR_USERNAME_A = $USER_A['username'];
					$IMG 	        = $USER_A['image'];
				}
				else
				{
					$USR_USERNAME_A = 'undefined';
					$IMG			= $USER_B['image'];
				}

				if(isset($USER_B) && $USER_B)
				{
					$USR_USERNAME_B = $USER_B['username'];
					$IMG 			= $USER_B['image'];
				}
				else
				{
					$USR_USERNAME_B = 'undefined';
					$IMG			= $USER_A['image'];
				}

				$A = array(
					"id" 			=> $NOT_ID,
					"type" 			=> $NOT_TYPE,
					"username_a" 	=> $USR_USERNAME_A,
                    "user_id_a"     => $USR_ID_A,
					"img" 			=> $IMG,
					"username_b" 	=> $USR_USERNAME_B,
                    "user_id_b"     => $USR_ID_B,
					"status" 		=> $NOT_STATUS,
					"time" 			=> $NOT_TIME
					);
				array_push($RESULT, $A);
			}
			return $RESULT;
			
		}
		die("Failed to get notifications");
	}
	private function getUserInfo($id)
	{
		$SQL = "SELECT profile_picture, username FROM users WHERE id='$id'";

		if($QUERY = mysqli_query($this->_CONNECTION, $SQL))
		{
			if($USER= mysqli_fetch_array($QUERY))
			{
				$result = array(
					"username" 	=> $USER['username'],
					"image" 	=> $USER['profile_picture']
					);
				return $result;
			}
		}
		return false;
	}
	private function loadUser()
	{
		require_once("../../common/session/sessioninfo.php");
		
		if(! isset($_SESSION))
		{
			session_start();
		}

		if(isset($_SESSION['game_user_id']) && is_numeric($_SESSION['game_user_id']))
		{
			$id = $_SESSION['game_user_id'];
			$name = $_SESSION['game_username'];

			if($this->_USER = new User($id, $name))
			{
				return true;
			}
		}
		return false;
	}
	private function connect()
	{
		require_once("../../connect/database.php");
		if($this->_CONNECTION = Database::getConnection())
		{
			return true;
		}
		return false;
	}
}


if(isset($_POST['get_notifications']))
{
	if(is_numeric($_POST['not_offset']))
	{
		$offset = $_POST['not_offset'];

		$not = new NotificationController();

		$RESULT = $not->getNotificationListJSON($offset, 15);

		echo(JSON_ENCODE($RESULT, JSON_PRETTY_PRINT));
		die("");
	}
}
else
{
	die(JSON_ENCODE(array('ERROR' => 'FORMAT_ERR'), JSON_PRETTY_PRINT));
}