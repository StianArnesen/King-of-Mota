<?php

define("ROOT_DIR", $_SERVER['DOCUMENT_ROOT']);

class PublicLab
{
	private $USER; 		//User object. 

	private $USER_ID; 

	private $CONNECTION; 

	private $STORAGE; 	// StorageController - Get backpack ID. etc...
	
	private $STORAGE_ID; // Backpack storage ID.
    
    private $labRequirements;


	public function __construct()
	{
		if($this->_connect())
		{
			if($this->_loadUser())
			{
				if($this->_loadStorageController())
				{
                    $this->mainInit();
				}
				else
				{
					die("Failed to load load storage controller");
				}
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
	private function mainInit() : bool 
    {
        // Set requirement to unlock lab. (ALL DATA WILL BE PUBLIC) 
        $this->labRequirements = array(
            'min_level' => 22,
            'min_money' => 1500000
        );
        
        return true;
    }
    public function getLabRequirements() : array 
    {
        return $this->labRequirements;
    }
	public function getLabSpaceList()
	{
		$USER_ID = $this->USER_ID;

		$SQL = "SELECT * FROM lab_space_list WHERE user_id='$USER_ID'";
		if($QUERY = mysqli_query($this->CONNECTION, $SQL))
		{
			$ARRAY = array();
			while($RESULT = mysqli_fetch_array($QUERY))
			{
				$ID 				= $RESULT['id'];
				$ACTIVE_ITEM_ID 	= $RESULT['active_lab_item_link_id'];

				$LAB_ITEM 			= $this->getLabSpaceAndProductInfo($ACTIVE_ITEM_ID);

				if(! $LAB_ITEM)
				{
					$ARRAY_ITEM = array(
					"id" 		=> $ID,
					"EMPTY" => true
					);
				}
				else
				{
					$ACTIVE_ITEM_IMG	= $LAB_ITEM['image'];
					$ACTIVE_ITEM_TITLE	= $LAB_ITEM['name'];
					
					$TIME_START 		= $LAB_ITEM['time_start'];
					$TIME_TOTAL 		= $LAB_ITEM['time_total'];

					$TIME_LEFT 			=  ($TIME_START + $TIME_TOTAL) - time();

					$ARRAY_ITEM = array(
					"id" 			=> $ID,
					"link_id" 		=> $ACTIVE_ITEM_ID,
					"img" 			=> $ACTIVE_ITEM_IMG,
					"title" 		=> $ACTIVE_ITEM_TITLE,
					"time_left" 	=> $TIME_LEFT,
					"time_total"	=> $TIME_TOTAL
					);	
				}
				
				array_push($ARRAY, $ARRAY_ITEM);
			}
			return (json_encode($ARRAY, JSON_PRETTY_PRINT));
		}
		return array("ERR" => "QUERY");
	}
	private function getProductIdFromActiveLabItems($ACTIVE_ITEM_ID)
	{
		$SQL = "SELECT lab_product_link_id FROM active_lab_items WHERE id='$ACTIVE_ITEM_ID' LIMIT 1";
		if($QUERY = mysqli_query($this->CONNECTION, $SQL))
		{
			if($RESULT = mysqli_fetch_row($QUERY))
			{
				return $RESULT[0];
			}
			return null;
		}
		return null;
	}
	public function getRandomListThing($LEVEL)
	{
		return $this->STORAGE->getRandomListOfQuality($LEVEL);
	}
	public function buyFirstTimeLabUnlock($LAB_TITLE)
	{
		$USER 		= $this->USER;
		$USER_ID 	= $this->USER_ID;

		$REQ_LEVEL 	= $this->labRequirements['min_level'];
		$REQ_MONEY 	= $this->labRequirements['min_money'];;

		if($this->doesUserMeetLabUnlockRequirements($REQ_LEVEL, $REQ_MONEY))
		{
			if(! $this->doesUserHaveLab($USER_ID))
			{
				if($LAB_ID = $this->insertNewLab($LAB_TITLE))
				{
					if($this->insertNewLabSpace($LAB_ID))
					{
						$CURRENT_MONEY 	= $USER->getMoney();
						$NEW_MONEY 		= $CURRENT_MONEY - $REQ_MONEY;

						if($USER->setMoney($NEW_MONEY))
						{
							return array("RESULT" => "TRUE");
						}
					}
				}
			}
		}		
		return array("RESULT" => "FAILED");
	}
	private function insertNewLabSpace($LAB_ID)
	{
		$USER_ID = $this->USER_ID;

		$SQL = "INSERT INTO lab_space_list(user_id, lab_link_id) VALUES('$USER_ID', '$LAB_ID')";

		if(mysqli_query($this->CONNECTION, $SQL))
		{
			return true;
		}
		return false;
	}
	private function doesUserMeetLabUnlockRequirements($REQ_LEVEL, $REQ_MONEY)
	{
		$USER = $this->USER;
		
		if($USER->getLevel() < $REQ_LEVEL){
			return false;
		}
		if($USER->getMoney() < $REQ_MONEY){
			return false;
		}
		return true;
	}
	private function doesUserHaveLab($USER_ID)
	{
		$SQL = "SELECT id FROM lab_list WHERE user_id='$USER_ID' LIMIT 1";

		if($QUERY = mysqli_query($this->CONNECTION, $SQL))
		{
			if($RESULT = mysqli_fetch_row($QUERY))
			{
				if(isset($RESULT[0]))
				{
					if(is_numeric($RESULT[0]))
					{
						return true;
					}
				}
			}
		}
		return false;
	}
	private function insertNewLab($LAB_TITLE)
	{
		$USER_ID = $this->USER_ID;

		$SQL = "INSERT INTO lab_list(user_id, lab_title) VALUES('$USER_ID', '$LAB_TITLE')";
		if(mysqli_query($this->CONNECTION, $SQL))
		{
			return mysqli_insert_id($this->CONNECTION);
		}
		return false;
	}

	private function updateOrRemoveInventoryItemsToIngredientsAmount($INGREDIENTS_LINK_ID)
	{
		$USER_ID = $this->USER_ID;
		$STORAGE_ID = $this->STORAGE_ID;

		$SQL = "UPDATE inventory 
		INNER JOIN lab_ingredients ON lab_ingredients.item_link_id = inventory.item_id
		SET inventory.item_amount = inventory.item_amount - lab_ingredients.item_amount
		WHERE inventory.user_id='$USER_ID'
		AND inventory.inv_item_status_type = lab_ingredients.item_status_type 
		AND inventory.storage_id='$STORAGE_ID' AND lab_ingredients.lab_product_id='$INGREDIENTS_LINK_ID'";

		if(mysqli_query($this->CONNECTION, $SQL))
		{
			$SQL_DELETE = "DELETE inventory.* FROM inventory 
				INNER JOIN lab_ingredients ON lab_ingredients.item_link_id = inventory.item_id
				WHERE inventory.item_amount <= 0
				AND inventory.user_id='$USER_ID'
				AND inventory.inv_item_status_type = lab_ingredients.item_status_type 
				AND inventory.storage_id='$STORAGE_ID' AND lab_ingredients.lab_product_id='$INGREDIENTS_LINK_ID'";

			if(mysqli_query($this->CONNECTION, $SQL_DELETE))
			{
				return true;
			}
			else
			{
				$ERR = mysqli_error($this->CONNECTION);
				return  array(
					"DBUG_MSG" => "FAILED TO DELETE INVENTORY",
					"ERR" => "DELETE_INV"
					);
			}
		}
		return  array(
				"ERR" 		=> "UPDATE_INV",
				"DBUG_MSG" 	=> "QUERY RETURNED NO ERROR"
				);
		

	}
	public function startProduction($SPACE_ID, $PRODUCT_ID)
	{
		 if($R = $this->isLabSpaceInUse($SPACE_ID)) // Sjekk at lab_space ikke er i bruk. (-1)
		 {
		 	return array(
		 		"WARN_DIALOG" 	=> "in_use",
		 		"ERR" 			=> $R['ERR']
			 	);
		 }
		 if($this->checkInventoryForIngredientsMatch($PRODUCT_ID)) // Sjekk om spilleren oppfyller krav for inventory i ingrediensene for produktet.
		 {
		 	if($UPDATE_INV_RESULT = $this->updateOrRemoveInventoryItemsToIngredientsAmount($PRODUCT_ID))
		 	{
		 		if(isset($UPDATE_INV_RESULT['ERR']))
		 		{
		 			$DBUG_MSG = $UPDATE_INV_RESULT['DBUG_MSG'];
		 			return array(
				 		"WARN_DIALOG" 	=> "INVENTORY_UPDATE_OR_DELETE_FAILED",
				 		"ERR"			=> $DBUG_MSG
				 		);
		 		}
		 		if($ACTIVE_ITEM_ID = $this->insertActiveLabElementAndUpdateLabSpaceLink($SPACE_ID, $PRODUCT_ID))
			 	{
					$LAB_ITEM = $this->getLabSpaceAndProductInfo($ACTIVE_ITEM_ID);

					if(! $LAB_ITEM)
					{
						$ARRAY_ITEM = array(
						"id" 		=> $SPACE_ID,
						"EMPTY" => true
						);
					}
					else
					{
						$ACTIVE_ITEM_IMG	= $LAB_ITEM['image'];
						$ACTIVE_ITEM_TITLE	= $LAB_ITEM['name'];
						
						$TIME_START 		= $LAB_ITEM['time_start'];
						$TIME_TOTAL 		= $LAB_ITEM['time_total'];

						$TIME_LEFT 			=  ($TIME_START + $TIME_TOTAL) - time();

						$ARRAY_ITEM = array(
						"id" 			=> $SPACE_ID,
						"link_id" 		=> $ACTIVE_ITEM_ID,
						"img" 			=> $ACTIVE_ITEM_IMG,
						"title" 		=> $ACTIVE_ITEM_TITLE,
						"time_left" 	=> $TIME_LEFT,
						"time_total"	=> $TIME_TOTAL
						);	
					}

			 		return $ARRAY_ITEM;
			 	}
			 	else
			 	{
			 		return array(
				 		"WARN_DIALOG" => "FAILED_TO_INSERT_ACTIVE_ITEM",
				 		"ERR"			=> "FAILED"
				 		);
			 	}
		 	}
		 	else
		 	{
		 		return array(
			 		"WARN_DIALOG" => "INVENTORY_UPDATE_FAILED",
			 		"ERR"			=> "UPDATE_INV_FAILED"
			 		);
		 	}
		 }
		 else
		 {
		 	return array(
		 		"WARN_DIALOG" => "ITEM_MISSMATCH",
		 		"ERR"			=> "FAILED"
		 		);
		 }

	}
	private function isLabSpaceInUse($SPACE_ID)
	{
		$USER_ID = $this->USER_ID;

		$SQL = "SELECT active_lab_item_link_id FROM lab_space_list WHERE id='$SPACE_ID' AND user_id='$USER_ID' LIMIT 1";
		if($QUERY = mysqli_query($this->CONNECTION, $SQL))
		{
			if($RESULT = mysqli_fetch_row($QUERY))
			{
				if($RESULT[0] == -1)
				{
					return false;
				}
				else
				{
					return true;
				}
			}
			return array("ERR" => "Failed to fetch results.");
		}
		return array("ERR" => "Query failed.");
	}
	public function collectLabSpace($SPACE_ID)
	{
		$LAB_INFO = $this->getLabInfo();

		$LAB_QUALITY_LEVEL = $LAB_INFO['lab_quality_level'];

		if($LAB_PRODUCT = $this->getActiveLabItem($SPACE_ID))
		{
			if($MSG = $this->isLabSpaceItemComplete($SPACE_ID))
			{
				$STORAGE = $this->STORAGE;
				if($INSERT_RESULT = $STORAGE->insertLabProductToInventory($LAB_PRODUCT, 1, $LAB_QUALITY_LEVEL))
				{
					if(isset($INSERT_RESULT['ERR']))
					{
						return $INSERT_RESULT;
					}
					
					$ACTIVE_ID = $LAB_PRODUCT['active_id'];

					if($this->removeActiveLabItemAndUpdateLabSpaceLinkId($ACTIVE_ID, $SPACE_ID))
					{
						if(isset($INSERT_RESULT['result']) && $INSERT_RESULT['result'] == true)
						{
							$QUALITY 	= $INSERT_RESULT['item_quality'];
							$PROD_NAME 	= $LAB_PRODUCT['name'];
							$PROD_IMG	= $LAB_PRODUCT['image'];

							$COLLECT_INFO = array(
								"product_name" 		=> $PROD_NAME,
								"product_img" 		=> $PROD_IMG,
								"product_quality" 	=> $QUALITY

								);

							return array(
								"id" 	=> $SPACE_ID,
								"EMPTY" => true,
								"DBUG_MSG" => (isset($MSG['ERR']))?$MSG['ERR']: "NULL",
								"collect_info" => $COLLECT_INFO
							);	
						}
						
					}
					
				}
				else
				{
					
				}
				
			}
			else
			{
				return array(
				"WARN_DIALOG" => "NOT_COMPLETE"
				);
			}
		}
		else
		{
			return array(
			"id" 	=> $SPACE_ID,
			"EMPTY" => true,
			"WARN_DIALOG" => "EMPTY_SPACE"
			);
		}
	}
	private function removeActiveLabItemAndUpdateLabSpaceLinkId($ACTIVE_ID, $SPACE_ID)
	{
		$USER_ID = $this->USER_ID;

		$SQL = "DELETE FROM active_lab_items WHERE id='$ACTIVE_ID' AND user_id='$USER_ID'";
		
		if(mysqli_query($this->CONNECTION, $SQL))
		{
			if($this->updateLabSpaceItemLink($SPACE_ID, -1))
			{
				return true;
			}
		}
		return false;
	}
	private function getActiveLabItem($SPACE_ID)
	{
		$SQL = "SELECT product.*, active_item.id AS active_id FROM lab_products AS product
		 INNER JOIN active_lab_items AS active_item 
		  ON active_item.lab_product_link_id = product.id
		 LEFT JOIN lab_space_list AS lab
		  ON lab.active_lab_item_link_id = active_item.id  
		 WHERE lab.id='$SPACE_ID' LIMIT 1";


		if($QUERY = mysqli_query($this->CONNECTION, $SQL))
		{
			if($RESULT = mysqli_fetch_array($QUERY))
			{
				return $RESULT;
			}
		}
		return false;
	}
	private function isLabSpaceItemComplete($SPACE_ID)
	{
		$SQL = "SELECT active_lab_items.time_start, active_lab_items.time_total FROM active_lab_items INNER JOIN lab_space_list ON lab_space_list.active_lab_item_link_id = active_lab_items.id WHERE lab_space_list.id='$SPACE_ID' LIMIT 1";

		if($QUERY = mysqli_query($this->CONNECTION, $SQL))
		{
			if($RESULT = mysqli_fetch_row($QUERY))
			{
				$TIME_LEFT = ($RESULT[0] + $RESULT[1]) - time();
				if($TIME_LEFT <= 0)
				{
					return true;
				}
				return false;
			}
		}
		return array("ERR" => "Query failed.");
	}
	private function insertActiveLabElementAndUpdateLabSpaceLink($SPACE_ID, $PRODUCT_ID)
	{
		$USER_ID = $this->USER_ID;
		
		$TIME_END = $this->getLabProductTime($PRODUCT_ID);
		$TIME_START = time();

		$AMOUNT = 0;

		$SQL = "INSERT INTO active_lab_items(user_id, lab_product_link_id, lab_product_amount, time_start, time_total) 
		VALUES('$USER_ID', '$PRODUCT_ID', '$AMOUNT', '$TIME_START', '$TIME_END')";

		if(mysqli_query($this->CONNECTION, $SQL))
		{
			$INSERT_ID = mysqli_insert_id($this->CONNECTION);
			
			if($this->updateLabSpaceItemLink($SPACE_ID, $INSERT_ID))
			{
				return $INSERT_ID;
			}
		}
		return false;
	}
	private function updateLabSpaceItemLink($SPACE_ID,$INSERT_ID)
	{
		$USER_ID = $this->USER_ID;

		$SQL = "UPDATE lab_space_list SET active_lab_item_link_id='$INSERT_ID' WHERE (id='$SPACE_ID' AND user_id='$USER_ID')";

		if(mysqli_query($this->CONNECTION, $SQL))
		{
			return true;
		}

		return false;
	}
	private function getLabProductInfo($PRODUCT_ID)
	{
		$SQL = "SELECT * FROM lab_products WHERE id='$PRODUCT_ID'";

		if($QUERY = mysqli_query($this->CONNECTION, $SQL))
		{
			if($RESULT = mysqli_fetch_array($QUERY))
			{
				return $RESULT;
			}
			return array("ERR" => "FETCH");	
		}
		return array("ERR" => "QUERY");
	}
	private function getLabProductTime($PRODUCT_ID)
	{
		$SQL = "SELECT production_time FROM lab_products WHERE id='$PRODUCT_ID'";

		if($QUERY = mysqli_query($this->CONNECTION, $SQL))
		{
			if($RESULT = mysqli_fetch_array($QUERY))
			{
				return $RESULT[0];
			}
			return 0;	
		}
		return 0;
	}
	
	private function checkInventoryForIngredientsMatch($PRODUCT_ID)
	{

		if(is_numeric($PRODUCT_ID))
		{
			$SQL = "SELECT lab_ingredients.item_amount, lab_ingredients.item_link_id, lab_ingredients.item_status_type FROM items
			INNER JOIN lab_ingredients
			ON items.id = lab_ingredients.item_link_id
			WHERE lab_ingredients.lab_product_id = '$PRODUCT_ID'";
			
			$QUERY = mysqli_query($this->CONNECTION, $SQL);
			
			if($QUERY)
			{
				while($ITEM = mysqli_fetch_array($QUERY))
				{
					$ITEM_ID 			= $ITEM['item_link_id'];
					$ITEM_STATUS_TYPE 	= $ITEM['item_status_type'];

					$ITEMS_IN_INVENTORY = $this->getItemAmountInInventory($ITEM_ID, $ITEM_STATUS_TYPE);
					
					$AMOUNT 	= $ITEM['item_amount'];
					
					if($AMOUNT > $ITEMS_IN_INVENTORY)
					{
						return false;
					}
				}
				return true;
			}
			else
			{
				$ERROR = mysqli_error($this->CONNECTION);
				return array(
					"ERROR" => "QUERY FAILED",
					"QUERY" => $ERROR
				);
			}
		}
	return array("ERROR" => "INVALID INPUT");	

	}
	public function getValidLabProductsToUse()
	{
		$SQL 			= "SELECT * FROM lab_products";
		$SQL_MAX_LIST 	= "SELECT MAX(production_time), MAX(power) FROM lab_products";
		
		if($QUERY = mysqli_query($this->CONNECTION, $SQL))
		{
			$MAX_POWER 	= 0;
			$MAX_TIME 	= 0;

			if($QUERY_MAX = mysqli_query($this->CONNECTION, $SQL_MAX_LIST))
			{
				$MAX_LIST = mysqli_fetch_array($QUERY_MAX);

				$MAX_TIME 	= $MAX_LIST[0];
				$MAX_POWER 	= $MAX_LIST[1];
			}

			$LIST = array();
			while($PRODUCT = mysqli_fetch_array($QUERY))
			{
				$ID 		= $PRODUCT['id'];
				$NAME 		= $PRODUCT['name'];
				$TYPE 		= $PRODUCT['type'];
				$POWER 		= $PRODUCT['power'];
                $EXP_GAIN   = $PRODUCT['exp_gain'];
				$TIME 		= $PRODUCT['production_time'];
				$IMG 		= $PRODUCT['image'];
				$DESC 		= $PRODUCT['description'];
				$INGR_LINK 	= $PRODUCT['ingredients_link_id'];

				$INGREDIENTS = $this->getProductIngredients($INGR_LINK);

				$VALID = $INGREDIENTS[sizeof($INGREDIENTS) - 1]['valid_inv'];

				$ITEM_INFO = array(
					"id" 	        => $ID,
					"name" 	        => $NAME,
					"type" 	        => $TYPE,
					"power"         => $POWER,
					"time"	        => $TIME,
					"img" 	        => $IMG,
					"desc"	        => $DESC,
					"ingredients"   => $INGREDIENTS,
					"valid"         => $VALID,
					"max_power"     => $MAX_POWER,
					"max_time"      => $MAX_TIME,
                    "exp_gain"      => $EXP_GAIN
                );

				array_push($LIST, $ITEM_INFO);
			}
			return $LIST;
		}
		return array("ERROR" => "QUERY FAILED");
	}
	private function getProductIngredients($PRODUCT_LINK_ID)
	{
		if(is_numeric($PRODUCT_LINK_ID))
		{
			$SQL = "SELECT items.id, items.name, lab_ingredients.item_amount, lab_ingredients.item_status_type, items.picture FROM items
			INNER JOIN lab_ingredients
			ON items.id = lab_ingredients.item_link_id

			WHERE lab_ingredients.lab_product_id = '$PRODUCT_LINK_ID'";

			$QUERY = mysqli_query($this->CONNECTION, $SQL);
			
			if($QUERY)
			{
				$BOOL_VALID_INV = true;		//Control var for inventory matchup.

				$REUSLT_LIST = array();
				while($ITEM = mysqli_fetch_array($QUERY))
				{
					$ITEM_ID 			= $ITEM['id'];
					$ITEM_STATUS_TYPE 	= $ITEM['item_status_type'];

					$ITEMS_IN_INVENTORY = $this->getItemAmountInInventory($ITEM_ID, $ITEM_STATUS_TYPE);

					$NAME 				= $ITEM['name'];
					$ITEM_ID 			= $ITEM['id'];
					$AMOUNT 			= $ITEM['item_amount'];
					$CUR_AMOUNT 		= $ITEMS_IN_INVENTORY;

					$IMG				= $ITEM['picture'];

					if($BOOL_VALID_INV)
					{
						if($AMOUNT > $ITEMS_IN_INVENTORY)
						{
							$BOOL_VALID_INV = false;
						}
					}

					$ITEM_ARRAY = array(
						"name" 			=> $NAME,
						"item_id" 		=> $ITEM_ID,
						"cur_amount" 	=> $CUR_AMOUNT,
						"amount" 		=> $AMOUNT,
						"img"			=> $IMG
					);
					array_push($REUSLT_LIST, $ITEM_ARRAY);
				}
				array_push($REUSLT_LIST, array("valid_inv" => $BOOL_VALID_INV));
				return $REUSLT_LIST;
			}
			else
			{
				$ERROR = mysqli_error($this->CONNECTION);
				return array(
					"ERROR" => "QUERY FAILED",
					"QUERY" => $ERROR
				);
			}
		}
		else
		{
			return array("ERROR" => "INVALID INPUT");	
		}
	}
	public function getLabLevelUpgradePrice()
	{
		if($LAB = $this->getLabInfo())
		{
			$LAB_LEVEL = $LAB['lab_level'];
			
			return pow($LAB_LEVEL*2000, 1.7);
		}
		return false;
	}
	public function getLabQualityLevelUpgradePrice()
	{
		if($LAB = $this->getLabInfo())
		{
			$QUALITY_LEVEL = $LAB['lab_quality_level'];

			return pow($QUALITY_LEVEL*8500, 1.8);
		}
		return false;
	}
	public function upgradeLabLevel()
	{
		$USER_ID = $this->USER->getUserId();

		if($LAB = $this->getLabInfo())
		{
			$LAB_ID 	= $LAB['id'];
			$LAB_LEVEL 	= $LAB['lab_level'] + 1;

			if($this->USER->getMoney() >= $this->getLabLevelUpgradePrice())
			{
				if($this->insertNewLabSpace($LAB_ID))
				{
					$SQL = "UPDATE lab_list SET lab_level='$LAB_LEVEL' WHERE id='$LAB_ID' AND user_id='$USER_ID'";
					if(mysqli_query($this->CONNECTION, $SQL))
					{
						$NEW_MONEY = $this->USER->getMoney() - $this->getLabLevelUpgradePrice();
						if($this->USER->setMoney($NEW_MONEY))
						{
							return true;
						}
					}
				}
			}
		}
		return false;
	}
	public function upgradeLabQualityLevel()
	{
		$USER_ID = $this->USER->getUserId();

		if($LAB = $this->getLabInfo())
		{
			$LAB_ID 			= $LAB['id'];
			$LAB_QUALITY_LEVEL 	= $LAB['lab_quality_level'] + 1;

			if($LAB_QUALITY_LEVEL > 5)
			{
				return false;
			}

			if($this->USER->getMoney() >= $this->getLabQualityLevelUpgradePrice())
			{
				$SQL = "UPDATE lab_list SET lab_quality_level='$LAB_QUALITY_LEVEL' WHERE id='$LAB_ID' AND user_id='$USER_ID'";

				if(mysqli_query($this->CONNECTION, $SQL))
				{
					$NEW_MONEY = $this->USER->getMoney() - $this->getLabQualityLevelUpgradePrice();
					if($this->USER->setMoney($NEW_MONEY))
					{
						return true;
					}
				}
			}
		}
		return false;
	}
	private function getLabSpaceAndProductInfo($ACTIVE_ID)
	{
		$SQL = "SELECT lab_products.*, active_lab_items.time_start, active_lab_items.time_total FROM lab_products INNER JOIN active_lab_items ON active_lab_items.lab_product_link_id = lab_products.id WHERE active_lab_items.id='$ACTIVE_ID'";
		
		if($QUERY = mysqli_query($this->CONNECTION, $SQL))
		{
			if($RESULT = mysqli_fetch_array($QUERY))
			{
				return $RESULT;
			}
		}
		return false; // Returned if empty.
	}
	private function getItemAmountInInventory($ITEM_ID, $ITEM_STATUS_TYPE) // Hent antall elementer av product/item i inventory, og sum(size, n). (n elementer, størrelse per element)
	{
		$USER_ID 	= $this->USER_ID;

		$STORAGE_ID = $this->STORAGE_ID;


		$SQL = "SELECT item_amount FROM inventory WHERE user_id='$USER_ID' AND item_id='$ITEM_ID' AND storage_id='$STORAGE_ID' AND inv_item_status_type=$ITEM_STATUS_TYPE LIMIT 1";
		
		if($QUERY = mysqli_query($this->CONNECTION, $SQL))
		{
			if($RESULT = mysqli_fetch_array($QUERY))
			{
				return $RESULT[0];
			}
		}
		return 0;
	}
    // Returns lab info from 'lab_list' - In DB. (array)
    //      - If not found, function returns requirements to unlock lab (array)
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
	public function getLabImage($LAB_LEVEL)
	{
		switch($LAB_LEVEL)
		{
			case 1:
				return "img/lab/lab/lab_01.png";

			default:
				return "img/lab/lab/lab_01.png";
		}
		return "img/lab/lab/lab_01.png";
	}


	private function _loadUser()
	{
		$USER = null;
		require_once(__DIR__ . "/../common/session/sessioninfo.php");
		if($USER = new User(-1,-1))
		{
			$this->USER = $USER;
			
			if($USER->isLoggedIn())
			{
				$this->USER_ID = $USER->getUserId();
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
	private function _loadStorageController()
	{
		require_once( ROOT_DIR . "storage/StorageController.php");

		$TMP_STORAGE = new StorageController();

		if($this->STORAGE = $TMP_STORAGE)
		{
			if($this->STORAGE_ID = $TMP_STORAGE->getBackpackStorageID())
			{
				return true;	
			}
			
		}
		return false;

	}
}




if(isset($_POST['get_unlock_req']))
{
    $LAB = new PublicLab();

    die(json_encode($LAB->getLabRequirements(), JSON_PRETTY_PRINT));
}
else if(isset($_POST['get_lab_info']))
{
	$LAB = new PublicLab();
    
	die(json_encode($LAB->getLabInfo(), JSON_PRETTY_PRINT) );

}
else if(isset($_POST['get_space_list']))
{
	$LAB = new PublicLab();

	die($LAB->getLabSpaceList());
}
else if(isset($_POST['get_setup_lab']))
{
	$LAB = new PublicLab();

	die(json_encode($LAB->getValidLabProductsToUse(), JSON_PRETTY_PRINT) );
}
else if(isset($_POST['set_lab_space']))
{
	$PRODUCT_ID = $_POST['product_id'];
	$SPACE_ID 	= $_POST['set_lab_space'];

	if(is_numeric($SPACE_ID) && is_numeric($PRODUCT_ID))
	{
		$LAB = new PublicLab();

		die(json_encode($LAB->startProduction($SPACE_ID, $PRODUCT_ID), JSON_PRETTY_PRINT) );
	}	
}
else if(isset($_POST['collect_lab_space']))
{
	$SPACE_ID 	= $_POST['collect_lab_space'];

	if(is_numeric($SPACE_ID))
	{
		$LAB = new PublicLab();

		die(json_encode($LAB->collectLabSpace($SPACE_ID), JSON_PRETTY_PRINT) );
	}	
}
else if(isset($_POST['buy_lab_unlock']))
{
	$LAB = new PublicLab();

	die(json_encode($LAB->buyFirstTimeLabUnlock("Beginner lab"), JSON_PRETTY_PRINT) );
}
else if(isset($_POST['get_lab_upgrade_price_list']))
{
	$LAB = new PublicLab();

	$LAB_LEVEL 	= $LAB->getLabLevelUpgradePrice();
	$QUALITY 	= $LAB->getLabQualityLevelUpgradePrice();

	$R = array(
		"quality_price" => $QUALITY,
		"lab_price" => $LAB_LEVEL
	);

	die(json_encode($R, JSON_PRETTY_PRINT) );
}
else if(isset($_POST['upgrade_lab_level']))
{
	$LAB = new PublicLab();

	die(json_encode($LAB->upgradeLabLevel(), JSON_PRETTY_PRINT) );
}
else if(isset($_POST['upgrade_lab_quality_level']))
{
	$LAB = new PublicLab();

	die(json_encode($LAB->upgradeLabQualityLevel(), JSON_PRETTY_PRINT) );
}
else if(isset($_GET['RAND_QUAL']))
{
	$LAB = new PublicLab();

	$LEVEL = $_GET['RAND_QUAL'];

	die(json_encode($LAB->getRandomListThing($LEVEL), JSON_PRETTY_PRINT));
}
