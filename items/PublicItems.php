<?php

class PublicItems
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
            else
            {
                die("PvpController: User failed to load!");
            }
        }
        else
        {
            die("PvpController: Connection failed!");
        }
    }
    public function getItemsUnlockedInCurrentLevel()
    {
        $USER_LEVEL = $this->USER->getLevel();

        $SQL = "SELECT picture, name  FROM items WHERE min_level='$USER_LEVEL' AND item_active='1'";

        if($QUERY = mysqli_query($this->CONNECTION, $SQL))
        {
            $RESULT_ARRAY = array();

            while($ITEM = mysqli_fetch_array($QUERY))
            {
                $NAME   = $ITEM['name'];
                $IMG    = $ITEM['picture'];

                $ITEM_ARRAY = array(
                    'NAME'  => $NAME,
                    'IMG'   => $IMG
                );

                array_push($RESULT_ARRAY, $ITEM_ARRAY);
            }
            return $RESULT_ARRAY;
        }
        return array(
            'STATUS'    => "FAILED",
            'MSG'       => "QUERY_ERROR"
        );
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
    private function _loadUser()
    {
        require_once( __DIR__ . "/../common/session/sessioninfo.php");

        if($this->USER = new User(-1, -1))
        {
            if($this->USER->isLoggedIn())
            {
                return true;
            }
        }

        return false;
    }
}

if(isset($_POST['GET_UNLOCKED_ITEMS']))
{
    $publicItems = new PublicItems();

    die( json_encode($publicItems->getItemsUnlockedInCurrentLevel(), JSON_PRETTY_PRINT));
}