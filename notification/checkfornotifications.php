<?php



class checkforupdate
{
	public $SESSION_USERNAME;

	public $connection;

	private function getSessionInfo()
	{
		session_start();
		
		echo "Validating session... <br>";
		if(isset($_SESSION['game_username']))
		{
			$this->SESSION_USERNAME = $_SESSION['game_username'];
			echo "Session is valid. <br>";
		}
		else
		{
			die("<h1>Failed to validate session! Error: 1 <h1>");
		}
		return;
	}

	private function getUserInfo()
	{
		$query = "SELECT username, id FROM users WHERE username='$this->SESSION_USERNAME'";
		$result = mysqli_query($this->connection, $query);

		$row = mysqli_fetch_row($result);

		if(isset($row))
		{
			if($row[0] == $this->SESSION_USERNAME){
				echo "Username match session. <br>";
			}
			else
			{
				die("Username - session - missmatch!");
			}
		}
		else
		{
			die("Session username not found in database!");
		}

		return;
	}

	public function checkGardenItems()
	{
		$query = "SELECT * FROM item_waiting WHERE user_id='$this->SESSION_USERNAME'";

		return;
	}
	
	public function __construct()
	{
		include_once("connection.php");

		$this->connection = $dbCon;

		if($this->connection)
		{
			$this->getSessionInfo();
			$this->getUserInfo();
			$this->checkGardenItems();
		}
		else
		{
			die("Connection failed");
		}
		return;
	}
}

$UPDATE = new checkforupdate();