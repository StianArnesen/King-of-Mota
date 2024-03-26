<?php

define("INVENTRY_TYPE_SHOP_ITEM", 0);
define("INVENTRY_TYPE_LAB_ITEM", 1);


$ROOT = $_SERVER['DOCUMENT_ROOT'];

require($ROOT . "layout/inventory-item/InventoryItem.php");

$UTIL = new GardenUtils();

if(isset($_POST['grow_list'])){
	die($UTIL->getGrowSpaceList());
}
else if(isset($_POST['get_confirm_buy_space']))
{
	die($UTIL->getBuySpaceConfirmDialog());
}
else if(isset($_POST['buy_more_space']))
{
	die($UTIL->buyMoreSpace("New space"));
}
else if(isset($_POST['get_space_setup']))
{
	$ID = strip_tags($_POST['get_space_setup']);

	die($UTIL->getGrowSpaceSetupHtml($ID));
}
else if(isset($_POST['getGrowSpaceItems']))
{
	die($UTIL->getAllGrowingSpace());
}

else if(isset($_GET['getInventoryPage']))
{
	if(is_numeric($_GET['getInventoryPage']))
	{
		$PAGE = $_POST['getInventoryPage'];
		die($UTIL->getInventoryViewHtml(12, $PAGE));
	}
}
else
{
	
}

class GardenUtils
{
	private $dbCon;

	private $user;

	private $ROOT;

	private $STORAGE;

	private $BACKPACK_ID;

	public function __construct()
	{
		if($this->connect())
		{
			if($this->loadUser())
			{
				if($this->loadStorage())
				{

				}
			}
			else
			{
				die("USER FAILED! [GARDEN UTILS: 20]");
			}
		}
		else
		{
			die("CONNECTION FAILED! [GARDEN UTILS: 30]");
		}
	}
	private function loadUser()
	{
		include_once($this->ROOT . "/common/session/sessioninfo.php");

		if($this->user = new User(-1, -1))
		{
			return true;
		}
		return false;
	}
	private function loadStorage()
	{
		require_once( __DIR__ . "../../storage/StorageController.php");
		$STORAGE = new StorageController();
		if($this->STORAGE = $STORAGE)
		{
			$this->BACKPACK_ID = $STORAGE->getBackpackStorageID();

			$this->STORAGE_SPACE_LEFT = $STORAGE->getStorageCapacity($this->BACKPACK_ID) - $STORAGE->getSpaceUsedInStorage($this->BACKPACK_ID);
			return true;
		}
		return false;
	}
	public function getAllGrowingSpace()
	{
		$r = "";

		$USER = $this->user;
		$USER_ID = $USER->getUserId();

		if($this->dbCon)
        {
			include_once("gardenitem.php");
			
            $getGrowingItemsSQL = "SELECT * FROM grow_space WHERE space_user_id='$USER_ID'";
            $doGetGrowingItems = mysqli_query($this->dbCon, $getGrowingItemsSQL);

            while($GROW_SPACE = mysqli_fetch_array($doGetGrowingItems))
            {
                $item_waiting_id = $GROW_SPACE['space_plant_id'];
                $space_id = $GROW_SPACE['space_id'];

                $G_ITEM_LAYOUT = new GrowingItem($item_waiting_id, $space_id);
                $r .= $G_ITEM_LAYOUT->getItemLayout();
            }
        }
        return $r;
	}
	public function getGrowSpaceList()
	{
		$USER_ID = $this->user->getUserId();

		$SQL = "SELECT * FROM grow_space WHERE space_user_id='$USER_ID'";

		$ARRAY_RESULT = array();

		if($QUERY = mysqli_query($this->dbCon, $SQL))
		{
			while($GROW_SPACE = mysqli_fetch_array($QUERY))
			{
				$GROW_SPACE_ARRAY       = array();

				$GROW_SPACE_ID          = $GROW_SPACE['space_id'];
				$ITEM_WAITING_ID        = $GROW_SPACE['space_plant_id'];

                if($ITEM_WAITING_INFO   = $this->getItemWaitingInfo($ITEM_WAITING_ID))
				{

				}
				else
				{
					$GROW_SPACE_ARRAY['space_id']   =	$GROW_SPACE_ID;
					$GROW_SPACE_ARRAY['empty'] 	    = 	"1";

					array_push($ARRAY_RESULT, $GROW_SPACE_ARRAY);
					
					continue;
				}

				$ITEM_ID    = $ITEM_WAITING_INFO['item_id'];
				$ITEM_INFO  = $this->getItemInfo($ITEM_ID);

				$TIME_START = $ITEM_WAITING_INFO['start'];
				$TIME_TOTAL = $ITEM_WAITING_INFO['finish'];

				$TIME_LEFT  = ($TIME_START + $TIME_TOTAL) - time();

				$ITEM_NAME  = $ITEM_INFO['name'];
				$ITEM_IMAGE = $ITEM_INFO['picture'];

				$GROW_SPACE_ARRAY['item_name'] 		= 	$ITEM_NAME;
				$GROW_SPACE_ARRAY['time_left'] 		= 	$TIME_LEFT;
				$GROW_SPACE_ARRAY['time_start'] 	= 	$TIME_START;
				$GROW_SPACE_ARRAY['time_total'] 	= 	$TIME_TOTAL;
				$GROW_SPACE_ARRAY['item_img'] 		= 	$ITEM_IMAGE;
				$GROW_SPACE_ARRAY['item_wid'] 		= 	$ITEM_WAITING_ID;
				$GROW_SPACE_ARRAY['space_id']		=	$GROW_SPACE_ID;

				array_push($ARRAY_RESULT, $GROW_SPACE_ARRAY);
			}
		}
		else{
			return json_encode($ARRAY_RESULT, JSON_PRETTY_PRINT);
		}
		return json_encode($ARRAY_RESULT, JSON_PRETTY_PRINT);
	}
	public function getGrowSpaceSetupHtml($space_id)
	{
		if(! $this->canChangeSpaceItems($space_id))
		{
			$error_array = array(
				'STATUS'    => "FAILED",
				'ERROR'     => "SPACE_IN_USE",
				'DIALOG_MSG' 	=> "This space is already in use! Please refresh this page to (hopefully)fix this problem."
			);

			return json_encode($error_array);
		}
		$r = "";

		$r .= 	"<div class='grow-space-setup-dialog' id='setup-dialog'>";
		$r .= 		"<div class='setup-dialog title'> <span> Growspace setup </span> </div>";
		$r .= "<div id='grow-space-inv-view'>";
			$r .= $this->getInventoryViewHtml(12, 0, $space_id);
		$r .= "</div>";
		$r .= $this->getGrowSpaceDropViewHtml();
		$r .= 	"</div>";

		return $r;
	}
	private function getGrowSpaceDropViewHtml()
	{
		$r 	=	"";
		return  $r;
	}
	private function getValidItemsToGrow()
	{
		$QUERY = $this->getUnlockedSeedsQuery();

		$UNLOCKS_ARRAY = array();

		while($UNLOCK = mysqli_fetch_array($QUERY))
		{
			array_push($UNLOCKS_ARRAY, $this->getItemInfo($UNLOCK['item_id']));
		}
		return json_encode($UNLOCKS_ARRAY, JSON_PRETTY_PRINT);
	}

	private function getUnlockedSeedsQuery(){
		$USER = $this->USER;
		$USER_ID = $USER->getUserId();

		$SQL = "SELECT * FROM user_unlocks WHERE user_id='$USER_ID' AND unlock_type=1";

		if($QUERY = mysqli_query($this->CONNECTION, $SQL)){
			return $QUERY;
		}
		return false;
	}
	private function getGrowSpaceInfo($SPACE_ID)
	{
		$USER_ID = $this->user->getUserId();

		$SQL = "SELECT * FROM grow_space WHERE space_id='$SPACE_ID' AND space_user_id='$USER_ID'";

		if($QUERY = mysqli_query($this->dbCon, $SQL))
		{
			return mysqli_fetch_array($QUERY);
		}
		return null;
	}
	public function getInventoryViewHtml($LIMIT, $PAGE, $SPACE_ID)
	{
		$ITEMS_QUERY = $this->getValidInventoryItems($LIMIT, $PAGE);

		if($SPACE_INFO = $this->getGrowSpaceInfo($SPACE_ID))
		{
			$LEVEL_LIGHT = $SPACE_INFO['space_light'];
		}


		$R = "<div class='inventory-grid-view'><div class='inventory-grid-title'> <span>Inventory</span> </div> ";

		$R .= "<div class='inventory-grid-items-view' id='inv-grid-items'>";

		$INV_SIZE = 0;

		while($ITEM = mysqli_fetch_array($ITEMS_QUERY))
		{
			$INV_SIZE++;

			$ITEM_IMG       = $ITEM['picture'];
			$ITEM_TITLE     = $ITEM['name'];
			$ITEM_INV_ID    = $ITEM['inv_id'];
            $ITEM_ID        = $ITEM['item_id'];
			$ITEM_TYPE      = $ITEM['type'];
			$ITEM_AMOUNT    = $ITEM['item_amount'];

            $INVENTORY_ITEM_LAYOUT = new InventoryItem($ITEM_ID, $ITEM_INV_ID, $ITEM_TITLE, $ITEM_IMG, $ITEM_AMOUNT);

            $R             .= $INVENTORY_ITEM_LAYOUT->getInventoryItemHtml();
		}

		if($INV_SIZE == 0)
		{
			echo "<div class='inventory-empty-msg'>No items in inventory</div>";
		}

		$R .= "</div>";

		$R .= "<div class='space-upgrade-view'>";
		$R .= "<div class='space-upgrade-view-title'>Upgrades</div>";
		$R .= "<div id='space-upgrade-items'>";
		$R .= "<div class='space-upgrade-item'>";
		$R .= "<div class='space-upgrade-item-title'>Light level: $LEVEL_LIGHT</div>";
		$R .= "<img class='upgrade-item-image' src='img/icon/light.png'>";
		$R .= "</div>";
		$R .= "</div>";
		$R .= "</div>";

		return $R;
	}
	private function getMaxValidInventoryItems()
	{
		return 12;
	}
	private function getValidInventoryItems($LIMIT, $PAGE)
	{
		$USER 			= $this->user;
		$USER_ID 		= $USER->getUserId();
		$BACKPACK_ID 	= $this->BACKPACK_ID;

		$OFFSET = $LIMIT * $PAGE;

		$SQL = "SELECT items.*, inventory.* FROM items
 				INNER JOIN inventory ON inventory.item_id = items.id
 				WHERE inventory.user_id='$USER_ID'
				AND inventory.inventory_type=". INVENTRY_TYPE_SHOP_ITEM ."
 				AND (items.sub_type=0 ) AND inventory.storage_id='$BACKPACK_ID' AND inventory.inv_item_status_type=0 ORDER BY items.type
 				LIMIT $LIMIT OFFSET $OFFSET";

		$QUERY = mysqli_query($this->dbCon, $SQL);


		return $QUERY;
	}
	private function getShortString($STR, $LIMIT)
	{
		if(strlen($STR) > $LIMIT)
		{
			return substr($STR, 0, $LIMIT) . "...";
		}
		return $STR;
	}
	private function getItemInfo($ID)
	{
		$SQL = "SELECT * FROM items WHERE id='$ID' LIMIT 1";
		$QUERY = mysqli_query($this->dbCon, $SQL);

		return mysqli_fetch_array($QUERY);
	}
	private function getItemWaitingInfo($ID)
	{
		$SQL = "SELECT * FROM item_waiting WHERE id='$ID'";
		$QUERY = mysqli_query($this->dbCon, $SQL);

		return mysqli_fetch_array($QUERY);
	}
	private function getInventoryToItems($TYPE, $INVENTORY_ARRAY)
	{

	}
	private function getInventoryQuery()
	{
		$USER = $this->user;
		$USER_ID = $USER->getUserId();

		$SQL = "SELECT * FROM inventory WHERE user_id='$USER_ID'";
		$QUERY = mysqli_query($this->dbCon, $SQL);

		return $QUERY;
	}
	private function canChangeSpaceItems($SPACE_ID)

	{
		$USER_ID = $this->user->getUserId();

		$SQL = "SELECT grow_space.space_status, item_waiting.id AS plant_id FROM grow_space LEFT OUTER JOIN item_waiting ON item_waiting.id=grow_space.space_plant_id WHERE grow_space.space_id='$SPACE_ID' AND grow_space.space_user_id='$USER_ID'";

        if($QUERY = mysqli_query($this->dbCon, $SQL))
        {
            if($QUERY_R = mysqli_fetch_array($QUERY))
            {
                $SPACE_STATUS   = $QUERY_R['space_status'];
                $PLANT_ID       = $QUERY_R['plant_id'];

                if(is_null($PLANT_ID) || $SPACE_STATUS == 0)
                {
                    return true;
                }
            }
        }
        return false;
	}
	public function buyMoreSpace($space_name)
	{
		$USER = $this->user;

		$space_size = $this->getSpaceAmount();

		$space_price = $this->getNewSpacePrice($space_size);

		if($USER->getMoney() > $space_price)
		{
			if($this->addSpace($space_name, $space_price)){
				return 0;
			}
		}
		else
		{
			return "You need more money!";
		}
		return 2;
	}

	private function getNewSpacePrice($space_size)
	{
		return 250 + ($space_size * 175);
	}
	private function addSpace($space_name, $price){

		$USER = $this->user;

		$USER_ID = $USER->getUserId();

		$SQL = "INSERT INTO grow_space(space_user_id, space_name) VALUES ('$USER_ID','$space_name')";
		$DO_SQL = mysqli_query($this->dbCon, $SQL);

		$this->subtractPlayerMoney($price);

		return true;
	}
	private function subtractPlayerMoney($amount){

		$USER = $this->user;

		$CURRENT_MONEY = $USER->getMoney();

		$USER_ID = $USER->getUserId();

		$NEW_MONEY = $CURRENT_MONEY - $amount;

		$SQL = "UPDATE users SET money='$NEW_MONEY' WHERE id='$USER_ID'";
		$DO_SQL = mysqli_query($this->dbCon, $SQL);

	}
	private function getSpaceAmount(){

		$USER = $this->user;
		$USER_ID = $USER->getUserId();

		$SQL = "SELECT * FROM grow_space WHERE space_user_id='$USER_ID'";
		$DO_SQL = mysqli_query($this->dbCon, $SQL);

		return mysqli_num_rows($DO_SQL);
	}
	public function getBuySpaceConfirmDialog()
	{
		$PRICE = $this->getNewSpacePrice($this->getSpaceAmount());

		$RESULT = "";
			$RESULT .= "<div class='buy-space-confirm-dialog' id='confirm-dialog'>";
				$RESULT .= "<div class='dialog-title'><span>Confirm purchase</span></div>";
				$RESULT .= "<div class='dialog-text-view'>";
				$RESULT .= "<div class='dialog-desc'>Buy +1 space for your plants to grow.</div>";
					$RESULT .= "<div class='confirm-dialog price-label'> Price: <span>" . $PRICE . " $</span></div>";
					$RESULT .= "<button class='purchase-dialog-button' onclick='confirmBuySpace()'>Buy</button>";
				$RESULT .= "</div>";
			$RESULT .= "</div>";


		return $RESULT;
	}
	private function connect()
	{

		$this->ROOT = $_SERVER['DOCUMENT_ROOT'];

		require_once($this->ROOT . "/connect/database.php");

		if($this->dbCon = Database::getConnection())
		{
			return true;
		}
		else{
			return false;
		}
	}

}
