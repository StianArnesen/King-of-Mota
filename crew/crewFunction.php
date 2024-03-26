<?php


class crewFunction
{
	private $connection; //Connection to database.

	private $C_USER; //All info about the current user. 

	public function getConnection()
	{
		return $this->connection;
	}

	public function __construct()
	{
		$this->mainInit();
	}
	public function mainInit()
	{
		if($this->connect())
		{
			$this->AUTH_USER();
		}
	}
	private function connect()
	{
		include_once("connection.php");
		
		if($this->connection = $dbCon)
		{
			if($this->connected())
			{
				$this->AUTH_USER();
				return true;
			}
		}
		else
		{
			return false;
			die("Connection failed! <br>");
		}
	}

	public function connected()
	{
		if($this->connection = $this->getConnection())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	private function AUTH_USER()
	{
		session_start();

		$SESS_USR_NAME = $_SESSION['game_username'];

		if(isset($SESS_USR_NAME) &&  $SESS_USR_NAME != "")
		{
			$QUERY_GET_USER_INFO = "SELECT * FROM users WHERE username='$SESS_USR_NAME'"; //Get userinfo from DB, and validate results with session data.

			$RESULT_USER_INFO = mysqli_query($this->connection, $QUERY_GET_USER_INFO); // Fetch the results.

			$this->C_USER = mysqli_fetch_array($RESULT_USER_INFO);

			if($this->C_USER['username'] == $SESS_USR_NAME)
			{
				return true;
			}
		}
		else
		{
			return false;
			die("User authentication failed!");
		}
	}
	public function getUserCrew() // Return the crew id of the current users crew. (IF ANY)
	{
		if($this->connected())
		{
			if(isset($this->C_USER['crew_id']))
			{
				if($this->C_USER['crew_id'] != -1)
				{
					$CREW_ID = $this->C_USER['crew_id'];

					return $CREW_ID;
				}
				else
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	public function userJoinCrew($CREW_ID)
	{	

		$C_USER_ID = $this->C_USER['id'];

		$C_CREW_ID = $this->C_USER['crew_id'];


		$QUERY_GET_CREW_INFO = "SELECT * FROM crew WHERE crew_id='$CREW_ID'";

		$RESULT_CREW_INFO = mysqli_query($this->connection, $QUERY_GET_CREW_INFO);

		$ARRAY_CREW_INFO = mysqli_fetch_array($RESULT_CREW_INFO);

		if(! isset($ARRAY_CREW_INFO))
		{
			return "Invalid crewID!";
		}
		if($C_CREW_ID == -1)
		{
			$QUERY_SET_USER_CREW = "UPDATE users SET crew_id='$CREW_ID' WHERE id='$C_USER_ID'";

			$RESULT_SET_USER_CREW = mysqli_query($this->connection, $QUERY_SET_USER_CREW);

			
			$NOT_TYPE = 15; // User added to your crew.
			$NOT_TIME = time();
			$NOT_USER_ID = $ARRAY_CREW_INFO['crew_leader'];

			$QUERY_SEND_NOTIFICATION_TO_CREW_LEADER = "INSERT INTO notifications(user_id, not_type, user_id_a, not_time) VALUES ('$NOT_USER_ID', '$NOT_TYPE', '$C_USER_ID', '$NOT_TIME')";

			$RESULT_SEND_NOTIFICATION_TO_CREW_LEADER = mysqli_query($this->connection, $QUERY_SEND_NOTIFICATION_TO_CREW_LEADER);


			return "SUCCESS!";
		}
		else
		{
			return "User already in crew!";
		}
			
	}
	public function userSendCrewRequest($CREW_ID)
	{
		$C_USER_ID = $this->C_USER['id'];
		
		$QUERY_GET_CREW_INFO = "SELECT * FROM crew WHERE crew_id='$CREW_ID'";
		
		$RESULT_CREW_INFO = mysqli_query($this->connection, $QUERY_GET_CREW_INFO);
		
		$ARRAY_CREW_INFO = mysqli_fetch_array($RESULT_CREW_INFO);


		if(isset($ARRAY_CREW_INFO['crew_id']))
		{
			$NOT_TYPE = 16; // User Crew Request. 
			$NOT_TIME = time();
			$NOT_USER_ID = $ARRAY_CREW_INFO['crew_leader'];

			$QUERY_SEND_NOTIFICATION_TO_CREW_LEADER = "INSERT INTO notifications(user_id, not_type, user_id_a, not_time) VALUES ('$NOT_USER_ID', '$NOT_TYPE', '$C_USER_ID', '$NOT_TIME')";

			$RESULT_SEND_NOTIFICATION_TO_CREW_LEADER = mysqli_query($this->connection, $QUERY_SEND_NOTIFICATION_TO_CREW_LEADER);
		}
	}
	public function addUserToMyCrew($USER_ID)
	{
		$C_USER_ID = $this->C_USER['id'];
		$C_CREW_ID = $this->C_USER['crew_id'];
		
		$QUERY_GET_CREW_INFO = "SELECT * FROM crew WHERE crew_id='$C_CREW_ID'";
		
		$RESULT_CREW_INFO = mysqli_query($this->connection, $QUERY_GET_CREW_INFO);
		
		$ARRAY_CREW_INFO = mysqli_fetch_array($RESULT_CREW_INFO);

		if(isset($ARRAY_CREW_INFO['crew_id']))
		{
			$QUERY_GET_USER_INFO = "SELECT crew_id FROM users WHERE id='$USER_ID'";
		
			$RESULT_GET_USER_INFO = mysqli_query($this->connection, $QUERY_GET_USER_INFO);

			$ARRAY_USER_INFO = mysqli_fetch_array($RESULT_GET_USER_INFO);

			if($ARRAY_USER_INFO['crew_id'] == -1)
			{
				$NOT_TYPE = 17; // User accepted to join crew. 
				$NOT_TIME = time();
				$NOT_USER_ID = $USER_ID;

				$QUERY_SEND_NOTIFICATION_TO_USER = "INSERT INTO notifications(user_id, not_type, user_id_a, not_time) VALUES ('$NOT_USER_ID', '$NOT_TYPE', '$C_USER_ID', '$NOT_TIME')";

				$RESULT_SEND_NOTIFICATION_TO_USER = mysqli_query($this->connection, $QUERY_SEND_NOTIFICATION_TO_USER);

				return "Success!";
			}
			else
			{
				return "User already in crew!";
			}
			
		}
		else
		{
			return "Error: 103";
		}
	}
	public function getCrewRequestList()
	{
		if($this->connected())
		{

		}
	}
	public function canAttackCrew($CREW_ATTACK_ID)
	{
		$SQL = "SELECT ";
	}
	public function userLeaveCrew()
	{
		$C_USER_ID = $this->C_USER['id'];
		
		$NO_CREW_ID = -1;

		$QUERY_SET_USER_CREW = "UPDATE users SET crew_id='$NO_CREW_ID' WHERE id='$C_USER_ID'";

		$RESULT_SET_USER_CREW = mysqli_query($this->connection, $QUERY_SET_USER_CREW);

		return "User left crew!";
	}
}

$CREW = new crewFunction();
if(isset($_POST['USER_JOIN_CREW']))
{
	echo "Result: " . $CREW->addUserToMyCrew($_POST['USER_JOIN_CREW']);
}
else if(isset($_POST['JOIN_CREW']))
{
	echo "Result: " . $CREW->userJoinCrew($_POST['JOIN_CREW']);
}
else if(isset($_POST['LEAVE_CREW']))
{
	echo "Result: " . $CREW->userLeaveCrew();
}