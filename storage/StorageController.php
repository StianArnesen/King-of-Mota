<?php


/*klasse




	func konstruktør()
	{
	
	}
	
	func getSpaceUsedInStorage() // Kan -OG SKAL. brukes til å hente sum av antall elementer i et gitt lager. Vil også brukes likt med Backpack/my items.
	{
	
	}
	func getTotalSpaceOfStorage()	// Skal brukes til å returnere den totale størrelsen i et gitt lager. Brukes også for backpack.
	{
		
	}
	func getSpaceLeftInStorage() // Hent antall ledige plasser i et gitt lager. Samme for Backpack. Returnerer: int[ totalSpace - usedSpace ]
	{
		
	}
*/

class StorageController
{

	private $CONNECTION;

	private $USER;
	private $USER_ID;

	private $STORAGE_ID;

	public function __construct()
	{
		if($this->_connect())
		{

			if($this->_loadUser())
			{
				
			}
			else
			{
				die("Failed to load user!");
			}
		}
		else
		{
			die("Connection failed!");
		}
	}
	public function getSpaceUsedInStorage($STORAGE_ID)
	{
		$VALIDATED_AMOUNT = $this->validateSpaceUsedInStorage($STORAGE_ID);
		
		return $VALIDATED_AMOUNT;
	}
	private function validateSpaceUsedInStorage($STORAGE_ID)
	{
		$USER_ID = $this->USER->getUserId();

		$SQL = "SELECT SUM(item_amount) AS items FROM inventory WHERE storage_id='$STORAGE_ID' AND user_id='$USER_ID'";
		
		if($QUERY = mysqli_query($this->CONNECTION, $SQL))
		{
			if($RESULT = mysqli_fetch_array($QUERY))
			{
				return 0 + $RESULT['items']; // Antall elementer funnet, i tillegg til antallet for hvert element.
			}
		}
		return 0;	
	}
	public function getItemsInStorage($STORAGE_ID)
	{
		$USER = $this->USER;
		$USER_ID = $USER->getUserId();
		
		$SQL = "SELECT items.type, items.name, items.item_power, storage_units.storage_id WHERE ";
	}
	public function getLabProductsFromStorage($STORAGE_ID = null)
	{
		if(! isset($STORAGE_ID)){
			$STORAGE_ID = $this->getBackpackStorageID();
		}
		$USER 		= $this->USER;
		$USER_ID 	= $USER->getUserId();

		$SQL = "SELECT inventory.item_amount, inventory.inv_id, inventory.item_info_a, lab_products.* FROM inventory INNER JOIN lab_products
		ON lab_products.id=inventory.item_id 
		WHERE (inventory.inventory_type=1 AND inventory.user_id='$USER_ID' AND inventory.storage_id='$STORAGE_ID')
		";

		if($QUERY = mysqli_query($this->CONNECTION, $SQL))
		{
			$ITEMS = array();
			while($RESULT = mysqli_fetch_array($QUERY))
			{
				$ID 	= $RESULT['id'];
				$INV_ID = $RESULT['inv_id'];
				$NAME 	= $RESULT['name'];
				$DESC 	= $RESULT['description'];
				$IMG 	= $RESULT['image'];
				$AMOUNT = $RESULT['item_amount'];
				$QUALITY = $RESULT['item_info_a'];

				$ITEM = array(
					"id" 			=> $ID,
					"inv_id" 		=> $INV_ID,
					"name" 			=> $NAME,
					"desc" 			=> $DESC,
					"img" 			=> $IMG,
					"amount" 		=> $AMOUNT,
					"quality" 		=> $QUALITY
					);

				array_push($ITEMS, $ITEM);
			}
			return $ITEMS;
		}
		return null;
	}
	public function getRandomListOfQuality($LEVEL)
	{
		$R = array();
		$AVG = 0;

		for($i = 0; $i < 250; $i++)
		{
			$AVG += $this->generateQualityPercentage($LEVEL);
			array_push($R, $this->generateQualityPercentage($LEVEL));
		}

		$AVG = "AVG : " . $AVG/sizeof($R);
		array_push($R, $AVG);

		return $R;
	}
	private function generateQualityPercentage($QUALITY_LEVEL)
	{
		$RANDOM_QUALITY	= mt_rand(0,100000);	
		$QUALITY		= $RANDOM_QUALITY/100000;

		return (($QUALITY_LEVEL - 1) * 20)  + $QUALITY*20;
		//return pow($QUALITY,1 + (3/sqrt($QUALITY_LEVEL))) * 100;
	}
	private function addSpaceUsedInStorage($STORAGE_ID, $ADD_SPACE_USED)
	{
		$USER_ID = $this->USER_ID;
		
		$SQL = "UPDATE storage_units SET storage_used_space=storage_used_space+$ADD_SPACE_USED WHERE (user_id='$USER_ID' AND id='$STORAGE_ID')";

		if(mysqli_query($this->CONNECTION, $SQL))
		{
			return true;
		}
		return false;
	}
	public function insertLabProductToInventory($PRODUCT, $AMOUNT, $QUALITY_LEVEL) // Param: Array med product info. Insert item to inventory..
	{
		$USER_ID 		= $this->USER_ID;

		$STORAGE_ID 	= $this->getBackpackStorageID();

		$MAX_SPACE 		= $this->getStorageCapacity($STORAGE_ID);
		$CUR_SPACE 		= $this->getSpaceUsedInStorage($STORAGE_ID);
		
		$ITEM_ID 		= $PRODUCT['id'];
		$ITEM_AMOUNT 	= $AMOUNT;
		$ITEM_TYPE 		= 1;
		$ITEM_QUALITY 	= $this->generateQualityPercentage($QUALITY_LEVEL);

		if($CUR_SPACE + $ITEM_AMOUNT <= $MAX_SPACE)
		{
			$SQL = "INSERT INTO inventory(user_id, item_id, item_amount, storage_id, inventory_type, item_info_a) VALUES($USER_ID, $ITEM_ID, $ITEM_AMOUNT, $STORAGE_ID, $ITEM_TYPE, $ITEM_QUALITY)";
			if(mysqli_query($this->CONNECTION, $SQL))
			{
				if($this->addSpaceUsedInStorage($STORAGE_ID, $ITEM_AMOUNT))
				{
					return array(
					"result" 		=> true,
					"item_quality" 	=> $ITEM_QUALITY
					);																	// COMPLETE: TRUE	
				}
				
			}
		}
		else
		{
			return array("ERR" => "SPACE_LIMIT");
		}
		return false;																			//COMPLETE: FALSE
	}
	public function insertItemToInventory($PRODUCT, $AMOUNT) // Param: Array med product info. Insert item to inventory..
	{
		$USER_ID 		= $this->USER_ID;

		$STORAGE_ID 	= $this->getBackpackStorageID();

		$MAX_SPACE 		= $this->getStorageCapacity($STORAGE_ID);
		$CUR_SPACE 		= $this->getSpaceUsedInStorage($STORAGE_ID);

		$ITEM_ID 		= $PRODUCT['id'];
		$ITEM_AMOUNT 	= $AMOUNT;
		$INV_TYPE 		= 0;


		$STACK_ID = null;



		if($CUR_SPACE + $ITEM_AMOUNT <= $MAX_SPACE)
		{
			if($STACK_ID = $this->getInventoryStackIdIfFound($ITEM_ID, 0, $STORAGE_ID, 0))
			{
				if($this->updateInventoryItemStackAmount($STACK_ID, $ITEM_AMOUNT))
				{
					return array(
						"result" 		=> true
					);
				}
			}

			$SQL = "INSERT INTO inventory(user_id, item_id, item_amount, storage_id, inventory_type) VALUES($USER_ID, $ITEM_ID, $ITEM_AMOUNT, $STORAGE_ID, $INV_TYPE)";
			if(mysqli_query($this->CONNECTION, $SQL))
			{
				if($this->addSpaceUsedInStorage($STORAGE_ID, $ITEM_AMOUNT))
				{
					return array(
						"result" 		=> true
					);
				}
			}
		}
		else
		{
			return array("ERR" => "SPACE_LIMIT");
		}
		return false;																			//COMPLETE: FALSE
	}
	private function updateInventoryItemStackAmount($INV_ID, $ADD_AMOUNT)
	{
		$SQL = "UPDATE inventory SET item_amount= item_amount +$ADD_AMOUNT WHERE inv_id='$INV_ID'";
		if(mysqli_query($this->CONNECTION, $SQL))
		{
			return true;
		}
		return false;
	}
	private function getInventoryStackIdIfFound($ITEM_ID, $INVENTORY_TYPE, $STORAGE_ID, $INV_STATUS_TYPE) // item id and where to select item form. (items & lab_products)
	{
		$SQL = "SELECT inv_id FROM inventory WHERE (item_id='$ITEM_ID' AND inventory_type='$INVENTORY_TYPE' AND inv_item_status_type='$INV_STATUS_TYPE' AND storage_id='$STORAGE_ID')";
		if($QUERY = mysqli_query($this->CONNECTION, $SQL))
		{
			if($RESULT = mysqli_fetch_row($QUERY))
			{
				return $RESULT[0];
			}
		}
		return false;
	}
	public function getBackpackStorageID()
	{
		$USER = $this->USER;
		$USER_ID = $USER->getUserId();
		
		if(isset($this->STORAGE_ID))
		{
			return $this->STORAGE_ID;
		}
		
		$SQL = "SELECT id FROM storage_units WHERE user_id='$USER_ID' LIMIT 1";
		if($QUERY = mysqli_query($this->CONNECTION, $SQL))
		{
			if($RESULT = mysqli_fetch_row($QUERY))
			{
				$this->STORAGE_ID = $RESULT[0];
				return $RESULT[0];	
			}
		}
		return false;
	}
	public function getStorageInfo($STORAGE_ID)
	{
		if(! is_numeric($STORAGE_ID)){
			return false;
		}

		$USER_ID = $this->USER_ID;

		$SQL = "SELECT * FROM storage_units WHERE id='$STORAGE_ID' AND user_id='$USER_ID' AND enabled=1 LIMIT 1";

		if($QUERY = mysqli_query($this->CONNECTION, $SQL))
		{
			if($RESULT = mysqli_fetch_array($QUERY))
			{
				$TITLE 			= $RESULT['storage_title'];
				$LOCATION 		= $RESULT['storage_location'];
				$STORAGE_TYPE 	= $RESULT['storage_type'];
				$SPACE_TOTAL 	= $RESULT['storage_space'];
				
				$SPACE_USED 	= $this->getSpaceUsedInStorage($STORAGE_ID);

				$STORAGE_LEVEL 	= $RESULT['storage_level'];

				$UPGRADE_PRICE 	= $this->getStorageUpgradePrice($STORAGE_LEVEL);

				$R = array(
					"title" 		=> $TITLE,
					"location" 		=> $LOCATION,
					"storage_type" 	=> $STORAGE_TYPE,
					"storage_total" => $SPACE_TOTAL,
					"storage_used" 	=> $SPACE_USED,
					"storage_level" => $STORAGE_LEVEL,
					"upgrade_price" => $UPGRADE_PRICE
					);

				return $R;
			}
		}
		return array(
			"ERR" => "QUERY_FAILED"
			);
	}
	public function getStorageUpgradePrice($STORAGE_LEVEL)
	{
		return 7000 * pow($STORAGE_LEVEL, 2);
	}
	public function getStorageLevel($STORAGE_ID)
	{
		$USER_ID = $this->USER_ID;

		$SQL = "SELECT storage_level FROM storage_units WHERE id='$STORAGE_ID' AND user_id='$USER_ID'";
		
		if($QUERY = mysqli_query($this->CONNECTION, $SQL))
		{
			if($RESULT = mysqli_fetch_row($QUERY))
			{
				return $RESULT[0];
			}
		}	
		return "failed to get max capacity!";
	}
	public function getStorageCapacity($STORAGE_ID)
	{
		$SQL = "SELECT storage_space FROM storage_units WHERE id='$STORAGE_ID'";
		if($QUERY = mysqli_query($this->CONNECTION, $SQL))
		{
			if($RESULT = mysqli_fetch_row($QUERY))
			{
				return $RESULT[0];
			}
		}	
		return "failed to get max capacity!";
	}
	public function getAllStorageUnits()
	{
		$USER_ID = $this->USER_ID;

		$SQL = "SELECT * FROM storage_units WHERE user_id='$USER_ID' AND storage_type=0";
		
		if($QUERY = mysqli_query($this->CONNECTION, $SQL))
		{
			$RESULT_ARRAY_LIST = array();

			while($STORAGE_UNIT = mysqli_fetch_array($QUERY))
			{
				$TITLE 	= $STORAGE_UNIT['storage_title'];
				$HEALTH = $STORAGE_UNIT['storage_health'];
				$LEVEL 	= $STORAGE_UNIT['storage_level'];
				$IMG 	= $this->getStorageUnitImageByLevel($LEVEL);

				$SPACE_USED 	= $STORAGE_UNIT['storage_used_space'];
				$SPACE_TOTAL 	= $STORAGE_UNIT['storage_space'];

				$STORAGE_UNIT_INFO = array(
					"storage_title" 	=> $TITLE,
					"storage_health" 	=> $HEALTH,
					"storage_level" 	=> $LEVEL,
					"storage_img" 		=> $IMG,
					"space_used" 		=> $SPACE_USED,
					"space_total" 		=> $SPACE_TOTAL
					);

				array_push($RESULT_ARRAY_LIST, $STORAGE_UNIT_INFO);
			}
			return $RESULT_ARRAY_LIST;
		}
		return array(
			"ERR" => "QUERY_FAILED"
			);
	}


	private function getStorageUnitImageByLevel($LEVEL)
	{
		$PRE_URL = "img/storage/"; // Base location

		switch($LEVEL)
		{
			case 1:
				return $PRE_URL . "storage_unit_small.png";
			case 2:
				return $PRE_URL . "Container_gross.gif";
			default:
				return $PRE_URL . "Container_gross.gif";
		}
	}


	private function _loadUser()
	{
		require_once( __DIR__ . "/../common/session/sessionInfo.php");

		if(! isset($_SESSION))
		{
			session_start();
		}

		if(isset($_SESSION['game_username']) && isset($_SESSION['game_user_id']))
		{
			$ID = ($_SESSION['game_user_id']);
			$NAME = ($_SESSION['game_username']);

			if(is_numeric($ID))
			{
				$USER = new User($ID, $NAME);
				if($this->USER = $USER)
				{
					$this->USER_ID = $USER->getUserId();
					return true;
				}	
			}
		}

		return false;
	}
	public function upgradeStorage($STORAGE_ID)
	{
		$USER 			= $this->USER;
		$USER_ID 		= $USER->getUserId();

		$LEVEL 			= $this->getStorageLevel($STORAGE_ID);

		$STORAGE_SPACE 	= $this->getStorageCapacity($STORAGE_ID);

		$STORAGE_PRICE 	= $this->getStorageUpgradePrice($LEVEL);

		$NEW_STORAGE_SPACE 	= $STORAGE_SPACE + 3;

		$USER_MONEY		= $USER->getMoney();

		if($USER_MONEY >= $STORAGE_PRICE)
		{
			$LEVEL += 1;
			$SQL = "UPDATE storage_units 
			SET storage_level='$LEVEL', 
			storage_space='$NEW_STORAGE_SPACE'
			WHERE user_id='$USER_ID' AND id='$STORAGE_ID'";

			if(mysqli_query($this->CONNECTION, $SQL))
			{
				$NEW_MONEY = $USER_MONEY - $STORAGE_PRICE;
				if($USER->setMoney($NEW_MONEY))
				{
					$STORAGE_INFO = $this->getStorageInfo($STORAGE_ID);
					return $STORAGE_INFO;
				}
				
			}
		}
		else
		{
			return array(
				"ERR"			=> "MONEY_ERR",
				"WARN_DIALOG" 	=> "MONEY"
			);
		}
		return array(
			"ERR"			=> "QUERY_ERR",
			"WARN_DIALOG" 	=> "UNKNOWN"
		);

	}
	public function _CLOSE_CONNECTION()
	{
		mysqli_close($this->CONNECTION);
	}
	private function _connect()
	{
		require_once( __DIR__ . "/../connect/database.php");

		if($this->CONNECTION = Database::getConnection())
		{
			return true;
		}
		return false;
	}
}


if(isset($_POST['get_backpack_info']))
{
	$STORAGE = new StorageController();
	$STORAGE_INFO = $STORAGE->getStorageInfo($STORAGE->getBackpackStorageID());

	die(json_encode($STORAGE_INFO, JSON_PRETTY_PRINT));
}
if(isset($_POST['upgrade_backpack']))
{
	$STORAGE = new StorageController();
	$STORAGE_INFO = $STORAGE->upgradeStorage($STORAGE->getBackpackStorageID());

	die(json_encode($STORAGE_INFO, JSON_PRETTY_PRINT));
}
else if(isset($_POST['GET_STORAGE_LIST']))
{
	$STORAGE = new StorageController();
	$STORAGE_INFO = $STORAGE->getAllStorageUnits();

	die(json_encode($STORAGE_INFO, JSON_PRETTY_PRINT));
}
