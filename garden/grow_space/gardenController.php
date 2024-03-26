<?php

$gardenController = new GardenController();

$TIME_START     = microtime(true);         // Execution time debug 14.09.2017 - Stiarn5

if(isset($_POST['SPACE_ID']))
{
	if(! isset($_SESSION))
	{
		session_start();
	}

	$space_id       = $_POST['SPACE_ID'];

	$plant_inv_id   = $_POST['PLANT_ID'];
	
	
	$RESULT = $gardenController->setGrowSpace($space_id, $plant_inv_id);
	
	$TIME_END       = microtime(true);         // Execution time debug 14.09.2017 - Stiarn5
	
	$TIME_TOTAL     = $TIME_END - $TIME_START;
	
	$DEBUG_RESULT = array(
		'execution_time' => $TIME_TOTAL
	);
	
	array_push($RESULT, $DEBUG_RESULT);
	
	die(json_encode($RESULT, JSON_PRETTY_PRINT));

	//$g->setGrowSpace(1,1, 783, 790, 788, 789);
}
else if(isset($_POST['harvest_plant_wait_id']))
{
	$WAITING_ID = ($_POST['harvest_plant_wait_id']);

	$RESULT = $gardenController->harvestItem($WAITING_ID);

	$TIME_END       = microtime(true);         // Execution time debug 14.09.2017 - Stiarn5

	$TIME_TOTAL     = $TIME_END - $TIME_START;

	$DEBUG_RESULT = array(
		'execution_time' => $TIME_TOTAL
	);

	array_push($RESULT, $DEBUG_RESULT);

	die(json_encode($RESULT, JSON_PRETTY_PRINT));
}
else if(isset($_POST['get_light_boost']))
{
	die($gardenController->getGrowingSpeedWithBoost());
}
else
{
	die("Wrong format");
}


/**
 * Class GardenController
 */
class GardenController
{
	/**
	 * @var
	 */
	private $SPACE_MAX_VALUE;

	/**
	 * @var
	 */
	private $PLANT_MAX_WEIGHT;

	/**
	 * @var
	 */
	private $dbCon;

	/**
	 * @var
	 */
	private $USER;

	/**
	 * @var
	 */
	private $staticEngine;

	/**
	 * @var
	 */
	private $SERVER_ROOT;

	/**
	 * @var
	 */
	private $FARMING_CONTROLLER;

	/**
	 * @var
	 */
	private $FARM_UPGRADE_ITEM_LEVELS_LIST;

	/**
	 * @var
	 */
	private $BACKPACK_ID;


	/**
	 * @var
	 */
	private $STATS_CONTROLLER;

	/**
	 * @var
	 */
	private $STORAGE_SPACE_LEFT;


	/**
	 * @var
	 */
	private $STORAGE;

	/**
	 * GardenController constructor.
	 */
	public function __construct()
	{
        $this->SERVER_ROOT = $_SERVER['DOCUMENT_ROOT'];
		if($this->connect())
		{
			if($this->loadUser())
			{
				if($this->loadStorage())
				{
					if($this->loadFarmingController())
					{
						$this->setValues();
						if($this->loadStatsController())
						{

						}
						else
						{
							die("Failed to load statistics-controller!");
						}
					}
				}
				else
				{
					die("FAILED TO LOAD STORAGE!");
				}
			}
		}
	}

	/**
	 * @return bool
	 */
	private function loadStatsController()
	{
		require_once( __DIR__ . "/../../stats/StatsController.php");

		if($this->STATS_CONTROLLER = new StatsController())
		{
			return true;
		}
		return false;
	}

	/**
	 * @return bool
	 */
	private function loadFarmingController(){

		$ROOT = $_SERVER['DOCUMENT_ROOT'];

		include($ROOT . 'farming/FarmingController.php');
		
		if($this->FARMING_CONTROLLER = new FarmingController()){
			if($this->FARM_UPGRADE_ITEM_LEVELS_LIST = $this->FARMING_CONTROLLER->getCurrentUpgradeItemsInfoArray()){
				return true;
			}
		}
		return false;
	}

	/**
	 * @return bool
	 */
	private function loadStorage()
	{
		require_once( __DIR__ . "/../../storage/StorageController.php");

		$STORAGE = new StorageController();
		if($this->STORAGE = $STORAGE)
		{
			$this->BACKPACK_ID = $STORAGE->getBackpackStorageID();

			$this->STORAGE_SPACE_LEFT = $STORAGE->getStorageCapacity($this->BACKPACK_ID) - $STORAGE->getSpaceUsedInStorage($this->BACKPACK_ID);
			return true;
		}
		return false;
	}

	/**
	 * @return bool
	 */
	private function loadUser()
	{
		include("../../common/session/sessioninfo.php");

		if(! isset($_SESSION))
		{
			session_start();
		}

		$NAME = $_SESSION['game_username'];
		$ID = $_SESSION['game_user_id'];

		if($this->USER = new User($ID, $NAME)){
			return true;
		}
		return false;
	}

	/**
	 *
	 */
	private function setValues()
	{
		$this->SPACE_MAX_VALUE = 1000;
		$this->PLANT_MAX_WEIGHT = 5000;
	}

	/**
	 * @return number
	 */
	public function getSoilBoost(){
        $USER_UPGRADE   = $this->FARM_UPGRADE_ITEM_LEVELS_LIST;
        return pow(1.2, $USER_UPGRADE[3] - 1);
    }

	/**
	 * @param $GROWING_ITEM_ID
	 *
	 * @return array
	 */
	public function harvestItem($GROWING_ITEM_ID)
	{

		if($this->growingItemComplete($GROWING_ITEM_ID))
		{
			if(! $GROWING_ITEM   = $this->getGrowingItem($GROWING_ITEM_ID))
			{
				return array(
					'STATUS'    => "FAILED",
					'ERROR'     => "GROW_ITEM_FETCH_FAILED"
				);
			}

			$ITEM           = $this->getItemInfo($GROWING_ITEM['item_id']);

			$EXP_AMOUNT     = round( ($ITEM['item_info_a'] * $this->getSoilBoost()), 0, PHP_ROUND_HALF_UP);


			if($this->STORAGE_SPACE_LEFT > 0)
			{
				if($this->USER->addUserExp($EXP_AMOUNT))
				{
					if($this->moveFromWaitingToInventory($GROWING_ITEM_ID))
					{
						if($this->removeGrowingItem($GROWING_ITEM_ID))
						{
							$this->setGrowSpaceStatusByItemWaitingId($GROWING_ITEM_ID, 0);

                            $COINS_GIVEN = $this->USER->giveRandomCoin();

							$ERROR = "NAN";

							if($this->STATS_CONTROLLER->updatePlantHarvestAmount(1))
							{

							}
							else
							{
								return array(
							    	'STATUS'        => "FAILED",
									'ERROR'     	=> "ITEM_WAIT_REMOVE_FAILED",
									'DIALOG_MSG' 	=> "The plant was harvested, but your stats could not be updated for some reason...",
									'ERROR_CODE'	=> "405fa814a3eab9fb8ef390cc03d2cb1b"

								);
							}


							return array(
							    'STATUS'        => "OK",
								'SPACE_INFO' 	=> $this->getGrowingItemInfo($GROWING_ITEM_ID),
								'EXP'			=> $EXP_AMOUNT,
								'ERROR'			=> $ERROR,
                                'G_COINS'       => $COINS_GIVEN

							);
						}
                        return array(
                            'STATUS'    => "FAILED",
						    'ERROR'     => "ITEM_WAIT_REMOVE_FAILED",
							'DIALOG_MSG' 	=> "The plant was inserted to your invetory, but the plant could not be removed from the grow space. ",
							'ERROR_CODE'	=> "ebe2a147ce34192984b69e9436a4b798"
                        );
					}
					return array(
						'STATUS'    => "FAILED",
						'ERROR'     => "ITEM_WAIT_TO_INVENTORY_INSERTION_FAILED",
						'DIALOG_MSG' 	=> "An error occured when trying to insert the item to your inventory ",
						'ERROR_CODE'	=> "81f5262a0f78b695a69b383b3dcb2365"
					);
				}
                return array(
                    'STATUS'    => "FAILED",
				    'ERROR'     => "EXP_ADD_FAILED",
					'DIALOG_MSG' 	=> "Failed to add EXP. Operation stopped",
					'ERROR_CODE'	=> "c3e9391b5e18a24d190215863327e2ca"

                );
			}
			else
			{
				return array(
				    'STATUS'    => "FAILED",
				    'ERROR'     => "SPACE_FULL",
					'DIALOG_MSG' 	=> "Your space seems to be full. Please move some items or sell them at the <a href='market.php'>market.</a>",
					'ERROR_CODE'	=> "ac2de312b0e000c08fb88894c2ab4359"
                );
			}
		}
		else
		{
			if(! $this->getGrowingItem($GROWING_ITEM_ID)){
				return array(
					'STATUS'    => "FAILED",
					'ERROR'     => "NOT_FOUND",
					'DIALOG_MSG' 	=> "The plant you are trying to harvest could not be found... Try refreshing your page to make sure that the plant was not already harvested.",
					'ERROR_CODE'	=> "30f0e00216dc86952ae6e046a3722894"
				);
			}
            return array(
                'STATUS'    => "FAILED",
                'ERROR'     => "NOT_COMPLETE",
				'DIALOG_MSG' 	=> "This plant is not yet done growing! Please update this page to make sure that the plant is done growing.",
				'ERROR_CODE'	=> "95100917238ba059cf18bd844574d50f"
			);
		}
		
	}

	/**
	 * @param $ITEM_WAIT_ID
	 * @param $STATUS
	 *
	 * @return bool
	 */
	private function setGrowSpaceStatusByItemWaitingId($ITEM_WAIT_ID, $STATUS)
	{
		$USER = $this->USER;
		$USER_ID = $USER->getUserId();

		$SQL = "UPDATE grow_space SET space_status='$STATUS' WHERE space_plant_id='$ITEM_WAIT_ID' AND space_user_id='$USER_ID'";
		if($QUERY = mysqli_query($this->dbCon, $SQL)){
			return true;
		}
		return false;
	}
    /*

        function getGrowingItemInfo

        Returns pure array containing public secure information about single growspace;

    */
	/**
	 * @param $WAIT_ID
	 *
	 * @return array|string
	 */
	private function getGrowingItemInfo($WAIT_ID)
	{
		$USER_ID = $this->USER->getUserId();

		$SQL = "SELECT * FROM grow_space WHERE space_plant_id='$WAIT_ID' AND space_user_id='$USER_ID' LIMIT 1";


		if($QUERY = mysqli_query($this->dbCon, $SQL))
		{
			if($SPACE_ITEM = mysqli_fetch_array($QUERY))
			{
				if($SPACE_ID = $SPACE_ITEM['space_id'])
				{
					if($GROWING_ITEM_INFO = $this->getGrowingItem($WAIT_ID))
					{
						$RESULT_ARRAY                       = array();

						$ITEM_ID                            = $GROWING_ITEM_INFO['item_id'];
						$ITEM_INFO                          = $this->getItemInfo($ITEM_ID);

						$ITEM_NAME 							= $ITEM_INFO['name'];
						$ITEM_IMAGE 						= $ITEM_INFO['picture'];

						$TIME_START 						= $GROWING_ITEM_INFO['start'];
						$TIME_FINISH 						= $GROWING_ITEM_INFO['finish'];
						$TIME_LEFT 							= ($TIME_START + $TIME_FINISH) - time();
						$TIME_TOTAL 						=  $TIME_FINISH;


						$ITEM_WAITING_ID 					= $GROWING_ITEM_INFO['id'];



						/*
						    STORE RESULT IN ARRAY AND RETURN

						*/

						$RESULT_ARRAY['item_name'] 			= 	$ITEM_NAME;
						$RESULT_ARRAY['time_left'] 			= 	$TIME_LEFT;
						$RESULT_ARRAY['time_start'] 		= 	$TIME_START;
						$RESULT_ARRAY['time_total'] 		= 	$TIME_TOTAL;
						$RESULT_ARRAY['item_img'] 			= 	$ITEM_IMAGE;
						$RESULT_ARRAY['item_wid'] 			= 	$ITEM_WAITING_ID;
						$RESULT_ARRAY['space_id']			=	$SPACE_ID;

						return $RESULT_ARRAY;
						
					}
				}
			}
		}
		else
		{
			$RESULT_ARRAY = array('empty' => 1);
			return $RESULT_ARRAY;
		}
		
		$RESULT_ARRAY = array('empty' => 1, 'space_id' => $SPACE_ID);

		return $RESULT_ARRAY;
	}

	/**
	 * @param $WAITING_ID
	 *
	 * @return bool
	 */
	private function moveFromWaitingToInventory($WAITING_ID)
	{
		$ITEM       = $this->getGrowingItem($WAITING_ID);

        $USER_ID    = $this->USER->getUserId();


		
		$ITEM_ID            = $ITEM['item_id'];
		
		$ITEM_AMOUNT        = $ITEM['item_amount'];
		
		$ITEM_STATUS_TYPE   = 1;

		$INV_STORAGE_ID     = $this->BACKPACK_ID;


		$SQL = "INSERT INTO inventory(user_id, item_id, item_amount, inv_item_status_type, storage_id) VALUES('$USER_ID', '$ITEM_ID', '$ITEM_AMOUNT', '$ITEM_STATUS_TYPE', '$INV_STORAGE_ID')";

		if($INV_ITEM_STACK_ID_AND_AMOUNT = $this->getInventoryIdOfItemStackIfFound($USER_ID, $ITEM_ID, $ITEM_STATUS_TYPE))
		{
			if(! $INV_ITEM_STACK_ID_AND_AMOUNT)
			{
				die("STACK NOT FOUND!");
			}
			
			$INV_ID             = $INV_ITEM_STACK_ID_AND_AMOUNT['inv_id'];
			
			$INV_ITEM_AMOUNT    = $INV_ITEM_STACK_ID_AND_AMOUNT['item_amount'] + $ITEM_AMOUNT;

			$SQL                = "UPDATE inventory SET item_amount='$INV_ITEM_AMOUNT' WHERE inv_id='$INV_ID' AND storage_id='$INV_STORAGE_ID' LIMIT 1";
		}
		if(mysqli_query($this->dbCon, $SQL) ) {
			return true;
		}

		return false;
	}

	/**
	 * @param $USER_ID
	 * @param $ITEM_ID
	 * @param $ITEM_STATUS_TYPE
	 *
	 * @return array|bool|null
	 */
	private function getInventoryIdOfItemStackIfFound($USER_ID, $ITEM_ID, $ITEM_STATUS_TYPE)
	{
		$S_ID = $this->BACKPACK_ID;

		$SQL = "SELECT inv_id, item_amount FROM inventory WHERE user_id='$USER_ID' AND (item_id='$ITEM_ID' AND inv_item_status_type='$ITEM_STATUS_TYPE' AND storage_id='$S_ID') LIMIT 1";
		$QUERY = mysqli_query($this->dbCon, $SQL);

		if($RESULT = mysqli_fetch_array($QUERY)){
			return $RESULT;
		}

		return false;
	}

	/**
	 * @param $GROWING_ITEM_ID
	 *
	 * @return bool|mysqli_result
	 */
	private function removeGrowingItem($GROWING_ITEM_ID)
	{
		$SQL = "DELETE FROM item_waiting WHERE id='$GROWING_ITEM_ID' LIMIT 1";
		return ($QUERY = mysqli_query($this->dbCon, $SQL));
	}

	/**
	 * @param $GROWING_ITEM_ID
	 *
	 * @return bool
	 */
	private function growingItemComplete($GROWING_ITEM_ID)
	{
		$SQL = "SELECT start, finish FROM item_waiting WHERE id='$GROWING_ITEM_ID' LIMIT 1";
		$QUERY = mysqli_query($this->dbCon, $SQL);

		$RESULT = mysqli_fetch_row($QUERY);

		if($RESULT)
		{
			$TIME_LEFT = $RESULT[0] + $RESULT[1] - time();
			return ($TIME_LEFT < 0);
		}
		return false;

	}
    /*
     * @setGrowSpace
     *
     * */
	/**
	 * @param $SPACE_ID
	 * @param $INV_PLANT_ID
	 */
	public function setGrowSpace($SPACE_ID, $INV_PLANT_ID)
	{
        $USER_ID = $this->USER->getUserId();

		$PLANT_ITEM = $this->getItemInfo($this->getInventoryToItemId($INV_PLANT_ID));
		$PLANT_ID = $PLANT_ITEM['id'];

		if($this->growSpaceInUse($SPACE_ID)) // If growspace is in use, cancel.
		{
			$ERROR_ARRAY = array('ERROR' => 'SPACE_IN_USE');
			return $ERROR_ARRAY; // Growspace is already in use.
		}
		else if( isset($PLANT_ITEM['sub_type']) && $PLANT_ITEM['sub_type'] == 0 )
		{
			if($RESULT = $this->updateGrowSpace($USER_ID, $SPACE_ID, $PLANT_ID))
			{
				if($this->updateOrRemoveInventoryItem($INV_PLANT_ID)){
					return $RESULT; // Successfully inserted grow-item and removed from inventory. 
				}
			}
		}
		die("SOMETHING_WRONG");
	}

	/**
	 * @param $USER_ID
	 * @param $SPACE_ID
	 * @param $PLANT_ID
	 *
	 * @return bool|string
	 */
	private function updateGrowSpace($USER_ID, $SPACE_ID, $PLANT_ID)
	{
		$RESULT = null;

		$PLANT_ID_NEW = $this->insertGrowItemAndGetId($USER_ID, $PLANT_ID);

		$SQL = "UPDATE grow_space SET space_plant_id='$PLANT_ID_NEW', space_status='1' WHERE space_user_id='$USER_ID' AND space_id='$SPACE_ID' LIMIT 1";
		if($QUERY = mysqli_query($this->dbCon, $SQL)) 
		{
			$RESULT = $this->getGrowingItemInfo($PLANT_ID_NEW);
		}
		return $RESULT;
	}

	/**
	 * @param $INV_PLANT_ID
	 *
	 * @return bool
	 */
	private function updateOrRemoveInventoryItem($INV_PLANT_ID)
	{
		$SQL    = "SELECT inventory.item_amount, inventory.inv_id FROM inventory WHERE inventory.inv_id='$INV_PLANT_ID' LIMIT 1";
		$QUERY  = mysqli_query($this->dbCon, $SQL);

		if($INV_ITEM = mysqli_fetch_array($QUERY))
		{
			$AMOUNT = $INV_ITEM['item_amount'] - 1;
			$INV_ID = $INV_ITEM['inv_id'];

				if($AMOUNT > 0)
				{
					if($this->setInventoryItemAmount($INV_ID, $AMOUNT))
					{
						return true;
					}
				}
				else
				{
					if($this->removeInventoryItem($INV_ID))
					{
						return true;
					}
				}
			}
		return false;
	}

	/**
	 * @param $INV_ID
	 *
	 * @return bool
	 */
	private function setInventoryItemInUse($INV_ID)
	{
		$INV_ITEM = $this->getInventoryItem($INV_ID);

		$INV_ITEM_AMOUNT = $INV_ITEM['item_amount'];

		if($INV_ITEM_AMOUNT > 1)
		{
			if($this->createInventoryItemDuplicate($INV_ITEM, 1))
			{
				return true;
			}
		}
		else if($INV_ITEM_AMOUNT == 1)
		{
			if($this->createInventoryItemDuplicate($INV_ITEM, 1))
			{
				return true;
			}
		}
        else
        {
        	
        }

		return false;
	}

	/**
	 * @param $INV_ITEM
	 * @param $ITEM_AMOUNT
	 *
	 * @return bool
	 */
	private function createInventoryItemDuplicate($INV_ITEM, $ITEM_AMOUNT){

		$D_USER_ID = $INV_ITEM['user_id'];
		$D_ITEM_ID = $INV_ITEM['item_id'];
		$D_ITEM_STORAGE_ID_= $INV_ITEM['storage_id'];

		$D_ITEM_STATUS_TYPE = 2; // 					[IN-USE]

		$SQL = "INSERT INTO inventory(user_id, item_id, item_amount, storage_id, inv_item_status_type) VALUES($D_USER_ID, $D_ITEM_ID, $ITEM_AMOUNT, $D_ITEM_STORAGE_ID_, $D_ITEM_STATUS_TYPE)";
        $QUERY = mysqli_query($this->dbCon, $SQL);
		if($QUERY) 
		{
			return true;
		}
		die("FAILED TO CREATE INVENTORY ITEM DUPE! GC[l:270]");

	}

	/**
	 * @param $INV_ID
	 *
	 * @return array|null
	 */
	private function getInventoryItem($INV_ID){

		$SQL = "SELECT * FROM inventory WHERE inv_id=$INV_ID LIMIT 1";

		$QUERY = mysqli_query($this->dbCon, $SQL);

		$INV_ITEM = mysqli_fetch_array($QUERY);

		return $INV_ITEM;
	}

	/**
	 * @param $INV_ID
	 *
	 * @return bool
	 */
	private function removeInventoryItem($INV_ID){
		$SQL = "DELETE FROM inventory WHERE inv_id='$INV_ID' LIMIT 1";
		if($QUERY = mysqli_query($this->dbCon, $SQL)) {
			return true;
		}
		return false;
	}

	/**
	 * @param $INV_ITEM_ID
	 * @param $AMOUNT
	 *
	 * @return bool
	 */
	private function setInventoryItemAmount($INV_ITEM_ID, $AMOUNT)
	{
		$SQL = "UPDATE inventory SET item_amount = $AMOUNT WHERE inv_id=$INV_ITEM_ID LIMIT 1";
		
		if($QUERY = mysqli_query($this->dbCon, $SQL)){
			return true;
		}
		return false;
	}

	/**
	 * @param $INV_ITEM_ID
	 *
	 * @return mixed
	 */
	private function getInventoryToItemId($INV_ITEM_ID)
	{
		$SQL = "SELECT item_id FROM inventory WHERE inv_id='$INV_ITEM_ID' LIMIT 1";
		$QUERY = mysqli_query($this->dbCon, $SQL);

		$RESULT = mysqli_fetch_row($QUERY);

		return $RESULT[0];
	}

	/**
	 * @param $item_waiting_id
	 *
	 * @return array|null
	 */
	private function getGrowingItem($item_waiting_id)
	{
		$USER = $this->USER;
		$USER_ID = $USER->getUserId();

		$SQL = "SELECT * FROM item_waiting WHERE id='$item_waiting_id' AND user_id='$USER_ID' LIMIT 1";
		$DO_SQL = mysqli_query($this->dbCon, $SQL);

		$RESULT = mysqli_fetch_array($DO_SQL);

		return $RESULT;
	}

	/**
	 * @param $SPACE_ID
	 *
	 * @return bool
	 */
	private function growSpaceInUse($SPACE_ID)
    {
        $USER_ID = $this->USER->getUserId();

        $SQL = "SELECT grow_space.space_status, item_waiting.id AS plant_id 
		FROM grow_space 
		LEFT OUTER JOIN item_waiting ON item_waiting.id=grow_space.space_plant_id 
		WHERE grow_space.space_id='$SPACE_ID' 
		AND grow_space.space_user_id='$USER_ID'";

        if($QUERY = mysqli_query($this->dbCon, $SQL))
        {
            if($QUERY_R = mysqli_fetch_array($QUERY))
            {
                $SPACE_STATUS   = $QUERY_R['space_status'];
                $PLANT_ID       = $QUERY_R['plant_id'];

                if(is_null($PLANT_ID) || $SPACE_STATUS == 0)
                {
                    return false;
                }
            }
        }
        return true;
    }

	/**
	 * @return float|int
	 */
	public function getGrowingSpeedWithBoost()
	{
		$UPGRADE_LEVELS = $this->FARM_UPGRADE_ITEM_LEVELS_LIST;
		return (8 / ($UPGRADE_LEVELS[1] + 7));
	}

	/**
	 * @param $USER_ID
	 * @param $PLANT_ID
	 *
	 * @return array|int|string
	 */
	private function insertGrowItemAndGetId($USER_ID, $PLANT_ID)
	{
		$PLANT_ITEM     = $this->getItemInfo($PLANT_ID);

		$TIME_START     = time();
		$TIME_FINISH    = $this->getGrowingSpeedWithBoost() * $PLANT_ITEM['grow_time'];
		$ITEM_AMOUNT    = 1;

		$SQL = "INSERT INTO item_waiting(start, finish, item_id, user_id, item_amount) VALUES('$TIME_START', '$TIME_FINISH', '$PLANT_ID', '$USER_ID', '$ITEM_AMOUNT')";

		if(mysqli_query($this->dbCon, $SQL))
		{
			$QUERY_ID = mysqli_insert_id($this->dbCon);

			return $QUERY_ID;
		}
		die("FATAL ERROR: mysqli err: " . mysqli_error($this->dbCon));
	}

	/**
	 * @param $PLANT_ID
	 * @param $LIGHT_ID
	 * @param $AIR_ID
	 * @param $SOIL_ID
	 *
	 * @return mixed
	 */
	private function getTotalPower($PLANT_ID, $LIGHT_ID, $AIR_ID, $SOIL_ID)
	{
		$PLANT_POWER 	= $this->getItemPower($PLANT_ID);
		$LIGHT_POWER 	= $this->getItemPower($LIGHT_ID);
		$AIR_POWER 		= $this->getItemPower($AIR_ID);
		$SOIL_POWER 	= $this->getItemPower($SOIL_ID);

		$TOTAL_POWER = $PLANT_POWER + $LIGHT_POWER + $AIR_POWER;

		return $TOTAL_POWER;
	}

	/**
	 * @param $item_id
	 *
	 * @return array|null
	 */
	private function getItemInfo($item_id)
	{
		$SQL = "SELECT * FROM items WHERE id='$item_id' LIMIT 1";
		$DO_SQL = mysqli_query($this->dbCon, $SQL);

		$RESULT = mysqli_fetch_array($DO_SQL);

		return $RESULT;
	}

	/**
	 * @param $ITEM_ID
	 *
	 * @return mixed
	 */
	private function getItemPower($ITEM_ID){
		$SQL 	= "SELECT item_power FROM items WHERE id='$ITEM_ID' LIMIT 1";
		$DO_SQL = mysqli_query($this->dbCon, $SQL);

		$R = mysqli_fetch_row($DO_SQL);

		return $R[0];
	}

	/**
	 * @return bool
	 */
	private function connect()
	{
		$ROOT = $_SERVER['DOCUMENT_ROOT'];

		require_once($ROOT . "/connect/database.php");
		$this->dbCon = Database::getConnection();

		return true;
	}

}


