<?php



class AdminUtils
{
	private $START_MONEY;
	private $START_EXP_NEXT;

	private $DEFAULT_IMG;

	private $CONNECTION;

	public function __construct()
	{
		$this->START_MONEY      = 0;
		$this->START_EXP_NEXT   = 25;

		$this->DEFAULT_IMG      = "img/0.jpg";

		if($this->connect())
		{
			
		}
		else
		{
			die("Failed to connect to db.\n");
		}
	}
	public function resetAllUsers()
    {
        $SQL = "SELECT id FROM users";

        if($QUERY = mysqli_query($this->CONNECTION, $SQL))
        {
            while($USER_ID = mysqli_fetch_array($QUERY))
            {
                $this->resetUser($USER_ID[0]);
                echo "RESET COMPLETE FOR USER " . $USER_ID[0] . " \n";
            }
        }
        echo "! ! ! ALL RESET COMPLETE FOR USERS ! ! ! \n";
    }
	public function resetUser($USER_ID)
	{
		if($this->resetUserInfo($USER_ID))
		{
			echo "User info cleared. \n";	
		}
		else
		{
			echo "	Failed to clear user info! \n";
		}

		if($this->resetFarm($USER_ID))
		{
			echo "User farm cleared. \n";
		}
		else
		{
			echo "	Failed to clear farm! \n";
		}

		if($this->resetGarden($USER_ID))
		{
			echo "User grow_space cleared.\n";
		}
		else
		{
			echo "	Failed to clear grow_space!\n";
		}
		if($this->resetLab($USER_ID))
		{
			echo "User lab cleared.\n";
		}
		else
		{
			echo "	Failed to clear lab!\n";
		}
		if($this->resetInventory($USER_ID))
		{
			echo "User inventory cleared. \n";
		}
		else
		{
			echo "	Failed to clear inventory! \n";
		}
		if($this->resetStorage($USER_ID))
		{
			echo "User inventory-storage cleared. \n";
		}
		else
		{
			echo "	Failed to clear storage! \n";
		}
		if($this->insertStorageBackpack($USER_ID))
		{
			echo "User backpack storage inserted. \n";
		}
		else
		{
			echo "	Failed to insert backpack!! \n";
		}
		
	}
	private function resetLab($USER_ID)
	{
		$SQL = "DELETE FROM lab_list WHERE user_id='$USER_ID'";
		if($QUERY = mysqli_query($this->CONNECTION, $SQL))
		{
			$SQL2 = "DELETE FROM lab_space_list WHERE user_id='$USER_ID'";
			if($QUERY2 = mysqli_query($this->CONNECTION, $SQL2))
			{
				$SQL3 = "DELETE FROM active_lab_items WHERE user_id='$USER_ID'";
				if($QUERY3 = mysqli_query($this->CONNECTION, $SQL3))
				{
					return true;
				}
			}
		}
		return false;
	}
	private function resetStorage($USER_ID)
	{
		$SQL = "DELETE FROM storage_units WHERE user_id='$USER_ID'";
		if($QUERY = mysqli_query($this->CONNECTION, $SQL))
		{
			return true;
		}
		return false;
	}
	private function insertStorageBackpack($USER_ID)
	{
        $TITLE = "Backpack";
        $TYPE = 1;

        $SQL = "INSERT INTO storage_units(user_id, storage_title, storage_type, storage_space) VALUES('$USER_ID', '$TITLE', '$TYPE', 3)";
        
        if($QUERY = mysqli_query($this->CONNECTION, $SQL))
        {
            return true;
        }
        return false;
	}
	private function resetInventory($USER_ID)
	{
		$SQL = "DELETE FROM inventory WHERE user_id='$USER_ID'";
		if($QUERY = mysqli_query($this->CONNECTION, $SQL))
		{
			return true;
		}
		return false;
	}
	private function resetGarden($USER_ID)
	{
		$SQL = "DELETE FROM grow_space WHERE space_user_id='$USER_ID'";
		if($QUERY = mysqli_query($this->CONNECTION, $SQL))
		{
			$SQL2 = "INSERT INTO grow_space(space_user_id) VALUE('$USER_ID')";
			if($QUERY2 = mysqli_query($this->CONNECTION, $SQL2))
			{
				return true;
			}
		}
		return false;
	}
	private function resetFarm($USER_ID)
	{
		$SQL = "UPDATE user_farm_list SET farm_id=1, light_level=1, air_level=1, soil_level=1 WHERE user_id='$USER_ID'";
		if($QUERY = mysqli_query($this->CONNECTION, $SQL))
		{
			return true;
		}
		return false;
	}
	private function resetUserInfo($USER_ID)
	{
		$SQL = "UPDATE users SET level=1, money='$this->START_MONEY', crew_id=-1, current_exp=0, next_level_exp='$this->START_EXP_NEXT' WHERE id='$USER_ID'";
		if($QUERY = mysqli_query($this->CONNECTION, $SQL))
		{
			return true;
		}
		return false;
	}

	private function connect()
	{
		require_once("../connect/database.php");

		if($this->CONNECTION = Database::getCONNECTION())
		{
			if(isset($this->CONNECTION) && $this->CONNECTION != null)
			{
				return true;
			}
		}
		return false;
	}
}


if(isset($_GET['reset_all']))
{
    $UTIL = new AdminUtils();
    $UTIL->resetAllUsers();
}
