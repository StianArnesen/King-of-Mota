<?php


class ItemUtils
{
    private $CONNECTION;

    public function __construct()
    {
        if($this->_connect())
        {
            
        }
    }

    public function getItemInfo($ID)
    {
        $SQL = "SELECT * FROM items WHERE id='$ID' LIMIT 1";
        $QUERY = mysqli_query($this->CONNECTION, $SQL);

        return mysqli_fetch_array($QUERY);
    }

    private function _connect()
    {
        require_once(__DIR__ . "/../connect/database.php");

        if($this->CONNECTION = Database::getConnection())
        {
            return true;
        }

        return false;
    }
}