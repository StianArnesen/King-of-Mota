<?php

/*
 * $SQL = "INSERT INTO inventory(user_id, item_id, item_amount, inv_item_status_type, storage_id) VALUES('$USER_ID', '$ITEM_ID', '$ITEM_AMOUNT', '$ITEM_STATUS_TYPE', '$INV_STORAGE_ID')";
 * */

$ROOT = $_SERVER['DOCUMENT_ROOT'];

if(! defined("DIR_ROOT"))
{
    define("DIR_ROOT", $ROOT, true);
}


class InventoryController
{
    /** @var User $USER */
    private $USER;

    /** @var mysqli $CONNECTION */
    private $CONNECTION;

    /** @var StorageController $STORAGE_CONTROLLER */
    private $STORAGE_CONTROLLER;


    public function __construct()
    {
        if($this->_connect())
        {
            if($this->_loadUserInfo())
            {
                if($this->_loadStorageController())
                {

                }
            }
        }
    }

    public function insertItemToInventory($ITEM_ID, $ITEM_AMOUNT, $INV_TYPE, $STATUS = 0, $ITEM_INFO_A = 0, $USER_ID = null, $STORAGE_ID = null)
    {

        if(is_null($USER_ID)) {
            $USER_ID = $this->USER->getUserId();
        }
        if(is_null($STORAGE_ID))
        {
            $STORAGE_ID = $this->STORAGE_CONTROLLER->getBackpackStorageID();
        }


        $SQL = "INSERT INTO inventory (user_id, item_id, item_amount, storage_id, inv_item_status_type, inventory_type, item_info_a) VALUES ($USER_ID, $ITEM_ID, $ITEM_AMOUNT, $STORAGE_ID, $STATUS, $INV_TYPE, $ITEM_INFO_A)";

        if($QUERY = mysqli_query($this->CONNECTION, $SQL))
        {
            return true;
        }
        return false;
    }

    private function _loadStorageController()
    {
        require_once(DIR_ROOT . "/storage/StorageController.php");
        if($this->STORAGE_CONTROLLER = new StorageController())
        {
            return true;
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