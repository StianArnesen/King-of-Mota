<?php

/**
 * User: StianDesktop
 * Date: 2016-01-05
 * Time: 18:52
 */

require_once("StorageController.php");
if(isset($_POST['trash_items']))
{
    $clean_values = array();
    foreach($_POST['trash_items'] as $value){
        $clean_values[] = ($value);
    }
    $STORAGE_UNIT_CONTROLLER = new StorageUnitController();

    die($STORAGE_UNIT_CONTROLLER->deleteItemsFromStorage($clean_values));
}
else if(isset($_POST['items_price']))
{
    $clean_values = array();
    foreach($_POST['items_price'] as $value){
        $clean_values[] = ($value);
    }
    $STORAGE_UNIT_CONTROLLER = new StorageUnitController();

    die($STORAGE_UNIT_CONTROLLER->getPriceOfItems($clean_values));
}
else if(isset($_POST['get_storage_list'])){



    $STORAGE            = new StorageUnitController();
    $STORAGE_CONTROLLER = new StorageController();

    $BACKPACK_ID        = $STORAGE_CONTROLLER->getBackpackStorageID();

    $BACKPACK_SPACE_TOTAL       = $STORAGE_CONTROLLER->getStorageCapacity($BACKPACK_ID);
    $BACKPACK_SPACE_USED        = $STORAGE_CONTROLLER->getSpaceUsedInStorage($BACKPACK_ID);


    $storage_units  = $STORAGE->getAllStorageUnits();

    $RESULT         = "<div class='storage-view-overlay'>";

    $RESULT         = "<div class='storage-info-title'>Select storage: </div>";

    while($UNIT = mysqli_fetch_array($storage_units))
    {
        $STORAGE_TITLE = $UNIT['storage_used_space'];


        $CURRENT_STORAGE_ID = $UNIT['id'];

        $STORAGE_LEVEL      = $UNIT['storage_level'];

        $USED_SPACE     = $STORAGE_CONTROLLER->getSpaceUsedInStorage($CURRENT_STORAGE_ID);
        $TOTAL_SPACE    = $STORAGE_CONTROLLER->getStorageCapacity($CURRENT_STORAGE_ID);
        $STORAGE_IMAGE  = $STORAGE->getStorageUnitImage($STORAGE_LEVEL);

        $UNIT_ARRAY = array();


        $RESULT .= "<div class='storage-unit' onclick='moveItemsToStorage($CURRENT_STORAGE_ID)'>";
        $RESULT .= "<input type='hidden' value='$CURRENT_STORAGE_ID' name='storage-unit-input-id' class='storage-unit-input-id'>";
        $RESULT .= "<div class='storage-unit-title'>";
        $RESULT .= "<div class='unit-title'>". $UNIT['storage_title'] ."</div>";

        $RESULT .= "</div>";

        $RESULT .= "<img class='storage-unit-image' src='$STORAGE_IMAGE'>";


        $RESULT .= "<div class='storage-unit-space-size'> ";
        $RESULT .= "<div class='space-size-used'>". $USED_SPACE ." / </div>";
        $RESULT .= "<div class='space-size-total'>". $TOTAL_SPACE ."</div>";
        $RESULT .= "</div>";
        $RESULT .= "</div>";

    }


    /*             BACKPACK ELEMENT                 */

    $RESULT .= "<div class='storage-unit' onclick='moveItemsToBackpack()'>";
    $RESULT .= "    <div class='storage-unit-title'>";
    $RESULT .= "        <div class='unit-title'>Backpack</div>";
    $RESULT .= "    </div>";
    $RESULT .= "    <img class='storage-unit-image' src='img/storage_inventory/inventory.png'>";
    $RESULT .= "    <div class='storage-unit-space-size'> ";
    $RESULT .= "        <div class='space-size-used'>".$BACKPACK_SPACE_USED ." / </div>";
    $RESULT .= "        <div class='space-size-total'>". $BACKPACK_SPACE_TOTAL ."</div>";
    $RESULT .= "    </div>";
    $RESULT .= "</div>";

    $RESULT .= "</div>";

    die($RESULT);
}
else if(isset($_POST['move_inv_items_id'])){

    $STORAGE = new StorageUnitController();

    $clean_values = array();
    foreach($_POST['move_inv_items_id'] as $value){
        $clean_values[] = ($value);
    }

    $STORAGE_ID = $_POST['move_to_storage_id'];

    die($STORAGE->moveItemsToStorage($clean_values, $STORAGE_ID));
}
else if(isset($_POST['move_inv_items_id_to_backpack'])){

    $STORAGE = new StorageUnitController();

    $clean_values = array();
    foreach($_POST['move_inv_items_id_to_backpack'] as $value){
        $clean_values[] = ($value);
    }

    $STORAGE_ID = $STORAGE->getBackpackStorageID();

    die($STORAGE->moveItemsToStorage($clean_values, $STORAGE_ID));
}
else if(isset($_POST['BUY_STORAGE'])){

    $STORAGE = new StorageUnitController();
    
    die($STORAGE->buyStorage());
}
else if(isset($_GET['BUY_STORAGE_PRICE'])){

    $STORAGE = new StorageUnitController();
    
    die($STORAGE->getStorageUnitPrice());
}
else if(isset($_POST['GET_STORAGE_UNIT_INFO']))
{
    $STORAGE_ID = $_POST['GET_STORAGE_UNIT_INFO'];

    if(is_numeric($STORAGE_ID))
    {
        // TO-DO: Add public method to get storage-unit info. [upgrade_price, image, space-total, space-used];
        $STORAGE = new StorageUnitController();

        die(json_encode($STORAGE->getStorageUnitInfoUser($STORAGE_ID), JSON_PRETTY_PRINT));
    }
}

class StorageUnitController
{
    private $CONNECTION;

    private $ROOT;

    private $USER;

    private $STORAGE_CONTROLLER;

    public function __construct()
    {
        $this->ROOT = $_SERVER['DOCUMENT_ROOT'];
        if($this->connectToDatabase())
        {
            if($this->loadUser()){
                if($this->loadStorageController())
                {

                }
                else{
                    die("Failed to load StorageController!");
                }
            }
            else{
                die("Failed to load user!");
            }
        }
        else{
            die("<h1 style='font-size: 55px;'>Connection failed!<h1>");
        }
    }
    private function loadStorageController()
    {
        if($this->STORAGE_CONTROLLER = new StorageController())
        {
            return true;
        }
        return false;
    }
    private function loadUser()
    {
        require_once($this->ROOT . "common/session/sessioninfo.php");

        $USER = new User(-1, -1);

        if($this->USER = $USER){
            if($USER->isLoggedIn())
            {
                $this->USER_ID = $USER->getUserId();
                return true;    
            }
        }
        return false;
    }
    private function connectToDatabase()
    {
        include_once($this->ROOT . "connect/connection.php");

        $con = new StaticConnection();

        $parm = $con->getSecureConnectionParams();

        if($this->CONNECTION = mysqli_connect($parm[0], $parm[1], $parm[2], $parm[3]))
        {
            return true;
        }
        return false;
    }
    public function getItemAmountOfStorageUnit($STORAGE_UNIT_ID)
    {
        $SQL = "SELECT * FROM inventory WHERE storage_id='$STORAGE_UNIT_ID'";

        if($QUERY = mysqli_query($this->CONNECTION, $SQL))
        {
            return mysqli_num_rows($QUERY);
        }

        return 0;
    }
    public function getBackpackStorageID()
    {
        $USER_ID = $this->USER->getUserId();

        $SQL = "SELECT id FROM storage_units WHERE user_id='$USER_ID' AND storage_type=1 LIMIT 1";
        if($QUERY = mysqli_query($this->CONNECTION, $SQL))
        {
            if($RESULT = mysqli_fetch_row($QUERY))
            {
                return $RESULT[0];  
            }
        }
        return false;
    }
    public function getSingleStorageUnitInfo($STORAGE_ID)
    {
        $SQL = "SELECT * FROM storage_units WHERE storage_id='$STORAGE_ID' LIMIT 1";
        if($QUERY = mysqli_query($this->CONNECTION, $SQL))
        {
            if($RESULT = mysqli_fetch_array($QUERY))
            {
                return $RESULT;
            }
        }
        return false;
    }
    public function getAllStorageUnits()
    {
        $USER_ID = $this->USER_ID;

        $SQL = "SELECT * FROM storage_units WHERE user_id='$USER_ID' AND storage_type='0'";
        $QUERY = mysqli_query($this->CONNECTION, $SQL);

        return $QUERY;
    }
    public function getAmountOfStorageUnits() // Return the total number of storage-units that the player currently owns.
    {
        $USER_ID = $this->USER->getUserId();

        $SQL = "SELECT COUNT(*) FROM storage_units WHERE user_id='$USER_ID' AND storage_type = 0"; // Select number of storage-units, where the item is not a backpack.

        if($QUERY = mysqli_query($this->CONNECTION, $SQL))
        {
            $RESULT = mysqli_fetch_row($QUERY);

            return $RESULT[0];
        }

        return null;
    }
    public function moveItemsToStorage($ITEMS, $STORAGE_ID)
    {
        $USER_ID = $this->USER->getUserId();

        $SPACE_USED = (int)$this->STORAGE_CONTROLLER->getSpaceUsedInStorage($STORAGE_ID);

        if($SPACE_USED + (int)$this->getTotalItemsCountSpace($ITEMS) <= (int)$this->STORAGE_CONTROLLER->getStorageCapacity($STORAGE_ID))
        {
            $this->updateAddStorageSizeUsed($ITEMS, $STORAGE_ID);

            $SQL = 'UPDATE inventory SET storage_id='. $STORAGE_ID .' WHERE user_id='. $USER_ID . ' AND inv_id in ('.implode(',',$ITEMS).')';
            if($QUERY = mysqli_query($this->CONNECTION, $SQL)){
                return "success";
            }
            return "!ERROR! -> Failed to move items to storage! \n STORAGE_ID: " . $STORAGE_ID;
        }
        else
        {
            return "!ERROR! -> Not enough space to move selected item(s)! \n Space used: " . $SPACE_USED  ." \n Space needed: " . $this->getTotalItemsCountSpace($ITEMS);
        }
    }
    public function buyStorage(){
        $USER = $this->USER;
        
        $USER_ID = $USER->getUserId();

        $SQL = "SELECT id FROM storage_units WHERE user_id='$USER_ID'";
        
        $STORAGE_PRICE = $this->getStorageUnitPrice();
        
        if($USER->getMoney() > $STORAGE_PRICE){
            if($this->addStorage()){
                if($USER->addMoney(-$STORAGE_PRICE)){
                    return "Buy success!";
                }
            }
        }
        else
        {
            return "money_err";
        }
        return "Failed to buy storage unit!";
    }
    public function getStorageUnitPrice(){

        $USER = $this->USER;

        $USER_ID = $USER->getUserId();

        $SQL = "SELECT id FROM storage_units WHERE user_id='$USER_ID'";

        if($QUERY = mysqli_query($this->CONNECTION, $SQL)){
            if($STORAGE_AMOUNT = mysqli_num_rows($QUERY)){
                return  pow($STORAGE_AMOUNT*600, 1.6);
            }
        }
        return (11*((1)/10)) + 150;
    }
    public function getStorageUnitUpgradePrice($STORAGE_UNIT_ID){ // Get price of the selected storage-unit space upgrade.

        if(! is_numeric($STORAGE_UNIT_ID))
        {
            return
                array(
                'success' => false,
                'ERR_MSG' => "NUMERIC_SECURITY"
                );
        }

        $USER = $this->USER;

        $USER_ID = $USER->getUserId();

        $SQL = "SELECT storage_level FROM storage_units WHERE id='$STORAGE_UNIT_ID' AND user_id='$USER_ID' LIMIT 1";

        if($QUERY = mysqli_query($this->CONNECTION, $SQL))
        {
            if($RESULT = mysqli_fetch_array($QUERY))
            {
                $STORAGE_LEVEL = $RESULT[0];
                
                return pow($STORAGE_LEVEL*300, 2);
            }
        }
        return array(
            'success'   => false,
            'ERR_MSG'   => "SQL_NO_MATCH",
        );
    }

    public function getStorageUnitInfoUser($STORAGE_ID)
    {
        /*
         *  TYPE:       METHOD.
            RETURNS:     Array[upgrade_price, image, space-total, space-used]

        */

        $USER_ID = $this->USER->getUserId();

        $SQL = "SELECT storage_location, storage_space, storage_title, storage_level FROM storage_units WHERE id='$STORAGE_ID' AND user_id='$USER_ID'";

        if($QUERY = mysqli_query($this->CONNECTION, $SQL))
        {
            if($RESULT = mysqli_fetch_array($QUERY))
            {
                $STORAGE_LEVEL          = $RESULT['storage_level'];

                $STORAGE_UPGRADE_PRICE  = $this->getStorageUnitUpgradePrice($STORAGE_ID);
                $STORAGE_IMAGE          = $this->getStorageUnitImage($STORAGE_LEVEL);
                $STORAGE_SPACE_TOTAL    = $RESULT['storage_space'];
                $STORAGE_SPACE_USED     = $this->STORAGE_CONTROLLER->getSpaceUsedInStorage($STORAGE_ID);
                $STORAGE_LOCATION       = $RESULT['storage_location'];

                $STORAGE_TITLE          = $RESULT['storage_title'];

                return array(
                    'STORAGE_UPGRADE_PRICE' => $STORAGE_UPGRADE_PRICE,
                    'STORAGE_IMAGE'         => $STORAGE_IMAGE,
                    'STORAGE_SPACE_TOTAL'   => $STORAGE_SPACE_TOTAL,
                    'STORAGE_SPACE_USED'    => $STORAGE_SPACE_USED,
                    'STORAGE_LOCATION'      => $STORAGE_LOCATION,
                    'STORAGE_LEVEL'         => $STORAGE_LEVEL,
                    'STORAGE_TITLE'         => $STORAGE_TITLE
                );
            }
            else{
                return array(
                    'success'   => false,
                    'ERR_MSG'   => 'QUERY_FETCH_FAILED'
                );
            }
        }
        return array(
            'success'   => false,
            'ERR_MSG'   => 'SQL_FAILED'
        );

    }
    public function getStorageUnitImage($STORAGE_LEVEL)
    {
        switch ($STORAGE_LEVEL)
        {
            case 1:
                return "img/storage/storage_unit_medium.jpg";
            case 2:
                return "img/storage/storage_unit_large.jpg";
        }
        return null;
    }
    private function addStorage(){
        $USER = $this->USER;
        $USER_ID = $USER->getUserId();

        $SQL = "INSERT INTO storage_units(user_id) VALUES('$USER_ID')";

        if($QUERY = mysqli_query($this->CONNECTION, $SQL)){
            return true;
        }
        return false;
    }
    private function getTotalItemsCountSpace($ITEMS)
    {
        $SQL = 'SELECT SUM(item_amount) FROM inventory WHERE inv_id in ('.implode(',',$ITEMS).') ';
        if($QUERY = mysqli_query($this->CONNECTION, $SQL)){
            if($R = mysqli_fetch_row($QUERY))
            {
                return  (int)$R[0];
            }
        }
        return null;
    }
    private function updateAddStorageSizeUsed($ITEMS, $STORAGE_ID){
        $USER = $this->USER;

        $USER_ID = $USER->getUserId();

        $ITEM_SIZE = $this->getTotalItemsCountSpace($ITEMS);

        $NEW_SIZE = $this->getItemAmountOfStorageUnit($STORAGE_ID) + $ITEM_SIZE;

        //$SQL = 'SELECT inv_id FROM inventory WHERE storage_id='. $STORAGE_ID .' AND user_id='. $USER_ID . ' AND inv_id in ('.implode(',',$ITEMS).')';
        $SQL = "UPDATE storage_units SET storage_used_space='$NEW_SIZE' WHERE id='$STORAGE_ID' AND user_id='$USER_ID'";
        if($QUERY = mysqli_query($this->CONNECTION, $SQL)){
            return "success";
        }
        return "!error! -> failed to update storage size info!";
    }
    public function getPriceOfItems($ITEMS)
    {
        $USER_ID = $this->USER->getUserId();

        $SQL = 'SELECT items.pris, inventory.item_amount FROM items INNER JOIN inventory ON inventory.item_id=items.id WHERE inventory.user_id='. $USER_ID. ' AND inventory.inv_id in ('.implode(',',$ITEMS).')';

        if($QUERY = mysqli_query($this->CONNECTION, $SQL))
        {
            $VAL = 0;

            while($R = mysqli_fetch_array($QUERY))
            {
                if($R['pris'] > 0) {
                    $VAL += $R['pris'] * $R['item_amount'];
                }
            }
            return $VAL;
        }
        return -1;
    }
    public function deleteItemsFromStorage($ITEMS)
    {
        $PROFIT     = $this->getPriceOfItems($ITEMS);

        $USER       = $this->USER;

        $USER_ID    = $USER->getUserId();
        
        $SQL = 'DELETE FROM inventory WHERE user_id='. $USER_ID. ' AND inv_id in ('.implode(',',$ITEMS).')';
        if($QUERY = mysqli_query($this->CONNECTION, $SQL)){
            if($this->USER->addMoney($PROFIT)){
                return "YEP";    
            }
            return "Inventory item(s) deleted.\n failed to add money.";
        }

        return "Failed to delete items from storage!";
    }
}