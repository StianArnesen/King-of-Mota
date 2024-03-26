<?php

        /* DB: all_crafting_items [VIEW OF ALL CRAFTING ITEMS - WITH BASE INFO]

        -----------------------------------------------------------------------
        1. Connect;
        2. Load user;
        3. if user is admin, self drop of items is allowed.
        4. dropRandomItem();
        -----------------------------------------------------------------------

        */

$ROOT = $_SERVER['DOCUMENT_ROOT'];

if(! defined("DIR_ROOT"))
{
    define("DIR_ROOT", $ROOT, true);
}

$itemDropController = new ItemDropController();

if(isset($_GET['drop_item-id']))
{
    $ITEM_ID    = $_GET['drop_item-id'];
    $ITEM_TIRE  = $_GET['drop_item-tire'];

    if($itemDropController->dropItem($ITEM_ID, $ITEM_TIRE))
    {
        echo "Item inserted!";
    }
    else
    {
        echo "Failed to insert item";
    }

}

class ItemDropController
{

    /** @var User $USER */
    private $USER;

    /** @var Database $CONNECTION */
    private $CONNECTION;

    /** @var InventoryController $INVENTORY_CONTROLLER */
    private $INVENTORY_CONTROLLER;

    public function __construct()
    {
        if($this->_connect())
        {
            if($this->_loadUserInfo())
            {
                if($this->_loadInventoryController())
                {

                }
            }

        }

    }
    private function _loadInventoryController()
    {
        require_once DIR_ROOT . "/inventory/InventoryController.php";

        if($this->INVENTORY_CONTROLLER = new InventoryController())
        {
            return true;
        }
        return false;
    }



    public function dropItem($ITEM_ID, $ITEM_TIRE)
    {
        $ITEM_AMOUNT    = 1;
        $STATUS         = 0;

        if($this->INVENTORY_CONTROLLER->insertItemToInventory($ITEM_ID, $ITEM_AMOUNT, $ITEM_TIRE,  $STATUS))
        {
            return true;
        }
        return false;
    }

    public function dropRandomItem()
    {
        if($this->USER->isUserAdmin())
        {
                /*
                 *  TODO - Finish this shiet
                */
        }
        return false;
    }

    private function _connect()
    {
        require_once(DIR_ROOT . "/connect/database.php");

        if($this->CONNECTION = Database::getConnection()){
            return true;
        }

        return false;
    }
    private function _loadUserInfo()
    {
        require_once(DIR_ROOT . "common/session/sessioninfo.php");
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