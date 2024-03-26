<?php


class RegisterController
{
	private $CONNECTED;

	private $CONNECTION;

	public function __construct()
	{
		$this->_connect();
	}
	private function _connect()
	{
		$ROOT = $_SERVER['DOCUMENT_ROOT'];
		require_once($ROOT . "/connect/Database.php");
		
		$this->CONNECTED = ($this->CONNECTION = Database::getConnection())? true : false;
	}
	public function _CLOSE_CONNECTION() // Close connection
	{
		mysqli_close($this->CONNECTION);
		
		$this->CONNECTED = false;
		
		return true;
	}
	public function addNewUser($USERNAME, $PASSWORD, $EMAIL)
	{
		$PASSWORD 		= mysqli_real_escape_string($this->CONNECTION, $PASSWORD);
		$pre_salt 		= strtr(base64_encode(random_bytes(16)), '+', '.');
		$final_salt 	= sprintf("$2a$%02d$", 10) . $pre_salt;
		$final_password = crypt($PASSWORD, $final_salt);


		$SQL = "INSERT INTO users (username, password, email, salt) VALUES ('$USERNAME', '$final_password', '$EMAIL', '$final_salt')";

		if(mysqli_query($this->CONNECTION, $SQL))
		{
			$USER_ID = mysqli_insert_id($this->CONNECTION);
			if($this->initNewUserItems($USERNAME, $USER_ID))
			{
				return true;
			}
		}
		return false;
	}
	private function initNewUserItems($USERNAME, $USER_ID)
	{
		require_once("InitUser.php");

		if($INIT_USER = new initNewUser())
		{
			if($INIT_USER->_INIT_USER($USER_ID))
			{
				$this->updateSession($USER_ID, $USERNAME);
				return true;
			}
		}
		return false;
	}
	private function updateSession($USER_ID, $USERNAME)
	{
		if(! isset($_SESSION)){
			session_start();
		}

		$_SESSION['game_user_id'] = $USER_ID;
		$_SESSION['game_username'] = $USERNAME;
	}
	private function _ENCRYPT_DATA_AND_GET_HASH_SALT_ORIGINAL($DATA)
	{
		$cost = 10;
		$salt = strtr(base64_encode(bin2hex(random_bytes(16))), '+', '.');
		//$salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.'); Old function - Deprecated. Removed (19.09.2017)
		$salt = sprintf("$2a$%02d$", $cost) . $salt;
		$hash = crypt($DATA, $salt);

		return array(
			0 => $hash,
			1 => $salt
			);
	}
	public function validatePostResponse($USERNAME, $PASS, $PASS_RE, $MAIL)
	{
		$NAME_L 	= strlen($USERNAME);
		$PASS_L 	= strlen($PASS);

        $ERR_NUM    = 0;

		$valid      = true;


		/*
		 * Start of the HTML final status output.
		 *
		 *
		 * */

		$ERR_MSG 	= "<div class='input-warn-view'>";


		if(! filter_var($MAIL, FILTER_VALIDATE_EMAIL))
		{
			$ERR_MSG .= "<div class='input-warn-item'>Bruh. This mail ain't even legit, man!</div>";
            $ERR_NUM++;
			$valid      = false;
		}
		if($NAME_L < 5 || $NAME_L > 20 || $PASS_L < 7 || $PASS_L > 50 )
		{
			if($NAME_L < 5 || $NAME_L > 20)
			{
				$ERR_MSG .= "<div class='input-warn-item'>Sorry man, but your username must be between 5 and 20 characters long, dude.</div>";
                $ERR_NUM++;
			}
			if($PASS_L < 7 || $PASS_L > 50 )
			{
				$ERR_MSG .= "<div class='input-warn-item'>Ah, dude... Your password must be between 7 and 50 characters long, man.</div>";
                $ERR_NUM++;
			}
            $ERR_NUM++;
			$valid      = false;
		}
		if(! $this->VALIDATE_INPUT($USERNAME))
		{
			$ERR_MSG .= "<div class='input-warn-item'>I told you man... Your username cannot contain any special characters!</div>";
            $ERR_NUM++;
			$valid      = false;
		}
		if(! $this->VALIDATE_INPUT($PASS))
		{
			$ERR_MSG .= "<div class='input-warn-item'>Is this even reel-life, bro? Your password cannot contain any special characters!</div>";
            $ERR_NUM++;
			$valid      = false;
		}
		if($PASS === $PASS_RE && ($ERR_NUM == 0))
		{
			
		}
		else
		{
			$ERR_MSG .= "<div class='input-warn-item'>You better be high, man! These passwords don't event match!</div>";
            $ERR_NUM++;
			$valid      = false;
		}

		$ERR_MSG   .= "</div>";
		$STATUS     = "OK";
		
		
		if($ERR_NUM > 0){
			$ERR_MSG   .= 	"<div class='input-warn-view-title'>Man, please check your input and try again yo!</div>";
			$STATUS = "Failed to validate user. ";
		}

		return array(
			'VALID'     => $valid,
			'STATUS' 	=> $STATUS,
			'ERR_MSG' 	=> $ERR_MSG,
            'ERR_NUM'   => $ERR_NUM
		);

	}
	public function validateUsername($UNAME)
	{
		$LENGTH = strlen($UNAME);

		if($LENGTH < 5 || $LENGTH > 20)
		{
			return array( "ERR" => "INVALID_LENGTH");
		}
		if(! $this->VALIDATE_INPUT($UNAME))
		{
			return array( "ERR" => "INVALID_CHAR");
		}
		
		$SQL = "SELECT username FROM users WHERE username='$UNAME'";
		
		if($QUERY = mysqli_query($this->CONNECTION, $SQL))
		{
			if($RESULT = mysqli_fetch_row($QUERY))
			{
				if(isset($RESULT[0]) && $RESULT[0] == $UNAME)
				{			
					return array( "ERR" => "IN_USE");
				}
				else if(isset($RESULT[0]))
				{
					return array( "RESULT" => "NO_MATCH");
				}
				else
				{
					return array( "RESULT" => "NO_MATCH");	
				}
			}
			return array( "RESULT" => "NO_MATCH");
		}
		return array( "RESULT" => "QUERY_FAILED");
	}
	private function VALIDATE_INPUT($INPUT)
	{
		if(preg_match('/[^a-zA-Z0-9\.\_\-]/', $INPUT))
		{
			return false;
		}
		return true;
	}
}






if(isset($_POST['V_UNAME'])) 	// Check if username is valid.
{
	$UNAME = strip_tags($_POST['V_UNAME']);

	$regCtrl = new RegisterController();
	
	$RESULT = $regCtrl->validateUsername($UNAME);
	$regCtrl->_CLOSE_CONNECTION();
	die(json_encode($RESULT, JSON_PRETTY_PRINT) );

}