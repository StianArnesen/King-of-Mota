<?php

$ROOT = $_SERVER['DOCUMENT_ROOT'];

require_once ($ROOT . "/common/session/sessioninfo.php");


class BottomBanner
{
    private $CONNECTION;

    private $USER;

    private $COMMUNICATOR;

    public function __construct()
    {
		
        if($this->connect())
        {
            if($this->getUserInfo())
            {
                $this->loadCommunicator();
            }
        }
        else
        {
            die("<h1>CONNECTION FAILED</h1>");
        }
    }
    private function loadCommunicator()
    {
        $this->COMMUNICATOR = new Communicator();
    }
    private function getUserInfo()
    {
        $ROOT = $_SERVER['DOCUMENT_ROOT'];
        include_once($ROOT . "/communication/communicator.php");

        if(! isset($_SESSION))
        {
            session_start();
        }
        if(isset($_SESSION['game_user_id']) && is_numeric($_SESSION['game_user_id']))
        {
            $ID = $_SESSION['game_user_id'];
            $USERNAME = $_SESSION['game_username'];

            if($this->USER = new User($ID, $USERNAME))
            {
                return true;
            }    
        }
        
        return false;
    }
    private function connect()
    {
        $ROOT = $_SERVER['DOCUMENT_ROOT'];
        include_once($ROOT."connect/connection.php");

        $SE_CONNECTION = new StaticConnection();

        $DB_PARAM = $SE_CONNECTION->getSecureConnectionParams();

        if($this->CONNECTION = mysqli_connect($DB_PARAM[0], $DB_PARAM[1], $DB_PARAM[2], $DB_PARAM[3]))
        {
            return true;
        }
        return false;
    }
    public function getBottomBanner()
    {
        return "";
        $RESULT     = "<link rel='stylesheet' href='layout/bot_banner/style.css'>";
        $RESULT    .= $this->getBannerScript();

        $CHAT_ITEMS = $this->COMMUNICATOR->getChatItems();

        $RESULT    .=
            '<div id="bottom_banner_view">
                <div id="bottom_banner_chat_view">'. $CHAT_ITEMS .'</div>
                <div id="bottom_banner_friend_list">
                    <div id="bottom_banner_friend_list_title"><span>Friends</span></div>

                    <div id="bottom_banner_friend_list_items">
                        '. $this->getFriendList() .'
                    </div>

                </div>

            </div>';

        return $RESULT;
    }
    private function getBannerScript()
    {
        $RESULT = "<script src='layout/bot_banner/script.js' type='text/javascript'> </script>";
        
        return $RESULT;
    }
    private function getExcerpt($str){
        if(strlen($str) >= 11){
            return  "..." . substr($str, 0, -4) ;
        }
        else
        {
            return  $str;
        }
   
    }
    private function getFriendList()
    {
        $RESULT = "";

        $GET_FRIEND_LIST_QUERY = $this->getFriendListQuery();

        while($FRIEND = mysqli_fetch_row($GET_FRIEND_LIST_QUERY))
        {
            if($FRIEND[1] == $this->USER->getUserId())
            {
                $FRIEND_ID = $FRIEND[2];
            }
            else
            {
                $FRIEND_ID = $FRIEND[1];
            }
            $FRIEND_INFO_QUERY = $this->getUserInfoID($FRIEND_ID);
            $FRIEND_INFO = mysqli_fetch_array($FRIEND_INFO_QUERY);

            $FRIEND_ONLINE_STATUS = $this->isUserOnline($FRIEND_INFO[4]);

            if($FRIEND_ONLINE_STATUS)
            {
                $BORDER_STYLE = "border: 2px solid lightgreen; box-shadow: 0px 0px 65px lightgreen; border-radius: 26px;";
            }
            else
            {
                $BORDER_STYLE = "border: 2px solid white; box-shadow: 0px 0px 25px red; border-radius: 26px;";
            }



            $RESULT .= '<div class="bottom_banner_friend_item">
                            <div class="bottom_banner_friend_item_picture" style="'. $BORDER_STYLE .'"><img src="'. $FRIEND_INFO[2] .'"></div>
                            <div class="bottom_banner_friend_item_last_active">'. $this->getLastActive($FRIEND_INFO[4]) .'</div>
                            <div class="bottom_banner_friend_item_username"><a>'. $this->getExcerpt($FRIEND_INFO[1]) .'</a></div>
                            <input type="hidden" class="bottom_banner_friend_username_value" value="'. $FRIEND_INFO[1]. '">
                        </div>';
        }

        return $RESULT;
    }
    private function getUserInfoID($ID)
    {
        $SQL = "SELECT id, username, profile_picture, level, last_active FROM users WHERE id='$ID'";
        $QUERY = mysqli_query($this->CONNECTION, $SQL);

        return $QUERY;
    }
    private function isUserOnline($LAST_ACTIVE)
    {
        if($this->getLastActive($LAST_ACTIVE) == 0)
        {
            return true;
        }
        return false;
    }
    private function getFriendListQuery()
    {
        $USER_ID = $this->USER->getUserId();

        $SQL = "SELECT DISTINCT friend_list.*
                FROM friend_list
                INNER JOIN users ON (friend_list.user_id = users.id)
                WHERE (friend_list.user_id = $USER_ID OR friend_list.friend_id = $USER_ID) AND friend_list.status = 1";
        $QUERY = mysqli_query($this->CONNECTION, $SQL);

        return $QUERY;
    }

    private function getLastActive($TIME){

        $TMP_SEC = (time() - $TIME);
        $TMP_MIN = 0;
        $TMP_HOUR = 0;
        $TMP_DAYS = 0;

        $MIN = 0;
        $HOUR = 0;
        $DAYS = 0;

        $FINAL_TIME = 0;


        if($TMP_SEC > 60)
        {
            while($TMP_SEC >= 60)
            {
                $TMP_SEC-=60;
                $TMP_MIN++;
            }
            $MIN = $TMP_MIN;
            if($MIN > 1)
            {
                $FINAL_TIME = $MIN . " m";
            }
            else
            {
                $FINAL_TIME = $MIN . " m";
            }

            if($TMP_MIN > 60)
            {
                while($TMP_MIN >= 60)
                {
                    $TMP_MIN-=60;
                    $TMP_HOUR++;
                }
                $HOUR = $TMP_HOUR;
                if($HOUR > 1)
                {
                    $FINAL_TIME = $HOUR . " h";
                }
                else
                {
                    $FINAL_TIME = $HOUR . " h";
                }

                if($TMP_HOUR > 24)
                {
                    while($TMP_HOUR >= 24)
                    {
                        $TMP_HOUR-=24;
                        $TMP_DAYS++;
                    }
                    $DAYS = $TMP_DAYS;
                    if($DAYS > 1)
                    {
                        $FINAL_TIME = $DAYS . " d";
                    }
                    else
                    {
                        $FINAL_TIME = $DAYS . " d";
                    }
                }
            }
        }
        else
        {
            $FINAL_TIME = "Online";
        }
        return $FINAL_TIME;
    }
}
