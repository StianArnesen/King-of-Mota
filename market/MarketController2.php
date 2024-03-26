<?php
/**
 * Created by PhpStorm.
 * User: StianDesktop
 * Date: 2016-01-26
 * Time: 20:33
 */

if(isset($_POST['get_inv_item_val'])) {
    $clean_values = array();
    foreach($_POST['get_inv_item_val'] as $value){
        $clean_values[] = ($value);
    }
    $Market = new MarketController();

    die(json_encode( $Market->getTotalValueOfItems($clean_values),  JSON_PRETTY_PRINT));
}
else if(isset($_POST['sell_items_list'])){
    $clean_values = array();
    foreach($_POST['sell_items_list'] as $value){
        $clean_values[] = ($value);
    }
    $Market = new MarketController();

    die($Market->sellItemsListedFromInventory($clean_values));
}


class MarketController
{
    private $dbCon;
    
    private $USER;

    private $ROOT;

    private $farmingController;

    private $MARKET_BOOST;

    public function __construct()
    {
        $this->ROOT = $_SERVER['DOCUMENT_ROOT'];

        if($this->_connect()){
            if($this->_loadUserInfo()){
                if($this->loadFarmingController()){
                    if($this->loadStorage())
                    {

                    }
                }
                else{
                    die("Failed to load farmingController!");
                }
            }
            else{
                die("Failed to load user info to MarketController!");
            }
        }
        else{
            die("Connection failed! MarketController [__construct->connection]");
        }
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
    private function loadFarmingController(){
        
        include_once($this->ROOT . "farming/FarmingController.php");

        if($this->farmingController = new FarmingController()){

            $FARM_CTRL = $this->farmingController;

            $UPGRADE_LEVELS_LIST = $FARM_CTRL->getUpgradeLevelList();

            $this->MARKET_BOOST = pow(1.2, ($UPGRADE_LEVELS_LIST['air_level'] - 1) );

            return true;
        }

        return false;
    }
    public function getValidUserInventoryToSellQuery(){

        $USER = $this->USER;

        $USER_ID = $USER->getUserId();

        $SQL = "SELECT inventory.*, items.* FROM inventory INNER JOIN items ON inventory.item_id=items.id WHERE user_id='$USER_ID' AND storage_id='$this->BACKPACK_ID' AND inv_item_status_type='1' ";
        $QUERY = mysqli_query($this->dbCon, $SQL);

        return $QUERY;
    }
    public function getTotalValueOfItems($INV_ITEM_ID_ARRAY){
        
        $USER = $this->USER;

        $USER_ID = $USER->getUserId();

        $SQL = "SELECT inventory.*, items.* FROM inventory INNER JOIN items ON (inventory.item_id=items.id AND inventory.inv_id IN (".implode(',',$INV_ITEM_ID_ARRAY).")) AND inventory.storage_id='$this->BACKPACK_ID' AND inventory.user_id=$USER_ID";

        $RESULT = "THIS SHOULD NOT BE RETURNED :)";


        $RESULT_ARRAY = array();
        $RESULT_ITEM_ARRAY = array();



        include_once($this->ROOT . "utils/se_utils.php");

        $TOTAL_ITEMS_VALUE = 0;

        $TOTAL_VALUE_NO_BOOST = 0;

        if($QUERY = mysqli_query($this->dbCon, $SQL)){

            while($ITEM = mysqli_fetch_array($QUERY))
            {
                $ITEM_NAME              = $ITEM['name'];
                $ITEM_SINGLE_VALUE      = ($ITEM['item_power']) + $ITEM['pris'];
                $ITEM_BOOST_VALUE       = ($ITEM['item_power'] *$this->MARKET_BOOST) + $ITEM['pris'];

                $ITEM_AMOUNT            = $ITEM['item_amount'];

                $ITEM_STACK_TOTAL_VALUE     = $ITEM_BOOST_VALUE * $ITEM_AMOUNT;
                $ITEM_STACK_SINGLE_VALUE    = $ITEM_SINGLE_VALUE * $ITEM_AMOUNT;

                $TOTAL_ITEMS_VALUE      += $ITEM_STACK_TOTAL_VALUE;
                $TOTAL_VALUE_NO_BOOST   += $ITEM_STACK_SINGLE_VALUE;


                $ITEM_INFO_ARRAY = array(
                    "item_name"         => $ITEM_NAME,
                    "item_value_single" => $ITEM_SINGLE_VALUE,
                    "item_amount"       => $ITEM_AMOUNT,

                    "stack_value"       => $ITEM_STACK_SINGLE_VALUE,
                    "stack_value_total" => $ITEM_STACK_TOTAL_VALUE,

                    );
                array_push($RESULT_ITEM_ARRAY, $ITEM_INFO_ARRAY);
            }
            
            $ROUND_BOOST = floor($this->MARKET_BOOST * 100);

            return $RESULT_ITEM_ARRAY;
        }
        else{
            $RESULT = "FAILED TO RETURN QUERY.";
        }

        return $RESULT;

    }
    private function getItemsListedFromInventoryValue($INV_ITEM_ID_ARRAY){

        $SQL = "SELECT inventory.*, items.* FROM inventory INNER JOIN items ON (inventory.item_id=items.id AND inventory.inv_id IN (".implode(',',$INV_ITEM_ID_ARRAY).")) AND inventory.storage_id='$this->BACKPACK_ID'";

        $TOTAL_ITEMS_VALUE = 0;

        if($QUERY = mysqli_query($this->dbCon, $SQL)){

            while($ITEM = mysqli_fetch_array($QUERY))
            {
            	$ITEM_PRICE = $ITEM['pris'];

            	$ITEM_POWER = $ITEM['item_power'];

                $ITEM_SINGLE_VALUE = $ITEM['item_power'] + $ITEM['pris'];

                $ITEM_AMOUNT = $ITEM['item_amount'];

                $ITEM_STACK_TOTAL_VALUE = (($ITEM_PRICE + $ITEM_POWER) * $this->MARKET_BOOST) * $ITEM_AMOUNT;

                $TOTAL_ITEMS_VALUE += $ITEM_STACK_TOTAL_VALUE;
            }
        }
        else{
            return "failed to fetch items.";
        }
        return $TOTAL_ITEMS_VALUE;
    }
    private function removeItemsListedFromInventory($INV_ITEM_ID_ARRAY){
        $USER = $this->USER;
        $USER_ID = $USER->getUserId();

        $SQL = 'DELETE FROM inventory WHERE user_id='. $USER_ID. ' AND inv_id IN ('. implode(',',$INV_ITEM_ID_ARRAY) .')';

        if($QUERY = mysqli_query($this->dbCon, $SQL)){
            return true;
        }
        return false;
    }
    public function sellItemsListedFromInventory($INV_ITEM_ID_ARRAY){
        $SQL = "SELECT inventory.*, items.* FROM inventory INNER JOIN items ON (inventory.item_id=items.id AND inventory.inv_id IN (".implode(',',$INV_ITEM_ID_ARRAY).")) AND inventory.storage_id='$this->BACKPACK_ID'";

        $TOTAL_ITEMS_VALUE = $this->getItemsListedFromInventoryValue($INV_ITEM_ID_ARRAY);

        if($this->removeItemsListedFromInventory($INV_ITEM_ID_ARRAY)){
            if($this->addPlayerMoney($TOTAL_ITEMS_VALUE)){
                return "1";
            }
            else{
                return "-1";
            }
        }


        return "0";
    }
    private function addPlayerMoney($AMOUNT){
        $USER = $this->USER;
        $USER_ID = $USER->getUserId();
        $USER_CURRENT_MONEY = $USER->getMoney();

        $NEW_MONEY = $USER_CURRENT_MONEY + $AMOUNT;

        $SQL = "UPDATE users SET money='$NEW_MONEY' WHERE id='$USER_ID'";

        if($QUERY = mysqli_query($this->dbCon, $SQL)){
            return true;
        }
        return false;
    }
    public function sellItemStackFromInventory($INVENTORY_ID){
        $USER = $this->USER;
        $USER_ID = $USER->getUserId();

        if($this->validateInventoryItemStackForSale($INVENTORY_ID, $USER_ID)){

            $MONEY_VAL = $this->getInventoryItemStackValueMoney($INVENTORY_ID, $USER_ID);

            $EXP_VAL = $this->getInventoryItemStackValueExp($INVENTORY_ID, $USER_ID);
        }
    }
    private function getInventoryItemStackValueMoney($INVENTORY_ID, $USER_ID){
        $SQL = "SELECT inventory.item_amount, items.* FROM inventory INNER JOIN items ON inventory.item_id=items.id WHERE user_id='$USER_ID' AND storage_id='$this->BACKPACK_ID' AND inv_item_status_type='1' AND inv_id='$INVENTORY_ID'";

        if($QUERY = mysqli_query($this->dbCon, $SQL)){

            $ITEM_MIX = mysqli_fetch_array($QUERY);

            $VAL = $ITEM_MIX['item_amount'] * ($ITEM_MIX['item_power']);

            return $VAL;
        }

        return false;
    }
    private function getInventoryItemStackValueExp($INVENTORY_ID, $USER_ID){
        $SQL = "SELECT inventory.item_amount, items.* FROM inventory INNER JOIN items ON inventory.item_id=items.id WHERE user_id='$USER_ID' AND storage_id='$this->BACKPACK_ID' AND inv_item_status_type='1' AND inv_id='$INVENTORY_ID'";

        if($QUERY = mysqli_query($this->dbCon, $SQL)){

            $ITEM_MIX = mysqli_fetch_array($QUERY);

            $VAL = $ITEM_MIX['item_amount'] * ($ITEM_MIX['item_info_a']); // Amount x THC level

            return $VAL;
        }

        return false;
    }
    private function validateInventoryItemStackForSale($INVENTORY_ID, $USER_ID){
        $SQL = "SELECT inv_id FROM inventory WHERE user_id='$USER_ID' AND inv_id='$INVENTORY_ID' AND storage_id='$this->BACKPACK_ID'";
        if($QUERY = mysqli_query($this->dbCon, $SQL)){
            return true;
        }
        return false;


    }
    private function _connect()
    {
        $ROOT = $_SERVER['DOCUMENT_ROOT'];

        require_once($ROOT . "/connect/database.php");
        if($this->dbCon = Database::getConnection()){
            return true;
        }

        return false;
    }
    private function _loadUserInfo()
    {
        $ROOT = $_SERVER['DOCUMENT_ROOT'];

        include_once($ROOT . "common/session/sessioninfo.php");

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
}
