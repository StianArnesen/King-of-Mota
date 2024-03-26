<?php

define("ROOT_DIR", $_SERVER['DOCUMENT_ROOT'], true);

class CraftingController
{
    private $CONNECTION;

    private $USER;

    public function __construct()
    {
        if($this->_connect())
        {
            if($this->_loadUser())
            {

            }
        }
    }

    public function getQueryAllCraftingItems()
    {
        $SQL = "SELECT * FROM all_crafting_items";

        if($QUERY = mysqli_query($this->CONNECTION, $SQL))
        {
            return $QUERY;
        }
        return null;
    }
    public function getQueryItemsRequiredToCraftItem($TARGET_ITEM_ID, $TARGET_ITEM_TIRE)
    {
        $SQL = "SELECT  all_crafting_items.*, all_crafting_requirements.req_item_amount 
                FROM    all_crafting_items 
                  INNER JOIN  all_crafting_requirements 
                  ON  all_crafting_requirements.req_item_id = all_crafting_items.item_id 
                  AND all_crafting_requirements.req_item_tire = all_crafting_items.item_tire
                WHERE motagamedata.all_crafting_requirements.target_item_id='$TARGET_ITEM_ID' 
                AND motagamedata.all_crafting_items.item_tire='$TARGET_ITEM_TIRE'";

        if($QUERY = mysqli_query($this->CONNECTION, $SQL))
        {
            return $QUERY;
        }

        return null;
    }

    private function _loadUser()
    {
        require_once ROOT_DIR . "/common/session/sessioninfo.php";
        if($this->USER = new User())
        {
            if($this->USER->isLoggedIn())
            {
                return true;
            }
        }
        return false;
    }
    private function _connect()
    {
        require_once ROOT_DIR . "/connect/database.php";

        if($this->CONNECTION = Database::getConnection())
        {
            return true;
        }

        return false;
    }
}