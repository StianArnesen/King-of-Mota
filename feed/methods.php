<?php

class SocialMethods
{
    private $CONNECTION;

    private $BOOL_CONNECTED;

    private $CURRENT_USER;

    private $ROOT;

    public function __construct()
    {
        $this->ROOT = $_SERVER['DOCUMENT_ROOT'];
        $BOOL_CONNECTED = false;

        if($this->SECURE_CONNECT())
        {
            $BOOL_CONNECTED = true;

            authenticateUser($_SESSION['game_username']);
        }
    }
    private function SECURE_CONNECT()
    {
        include_once("connect/connection.php");

        $STATIC_CONNECTION = new StaticConnection();

        return $this->CONNECTION = $STATIC_CONNECTION->SECURE_CONNECTION()? 1:0;
    }
    public function writeComment($STATUS_LINK_ID, $DATA)
    {
        $FROM_USER_ID = $this->

        $SQL = "INSERT INTO wall_status_comment(status_link_id, from_user_id, comment_data, comment_date) VALUES ()";
    }
    private function authenticateUser($USERNAME)
    {

        if($USERNAME)
        {
            $SQL_USER = "SELECT id, username FROM users WHERE username='$USERNAME'";
            $QUERY_USER = mysqli_query($this->SECURE_CONNECTION, $SQL_USER);

            $RESULT = mysqli_fetch_array($QUERY_USER);

            if(isset($RESULT['username']))
            {
                $r_username = $RESULT['username'];
                $r_id = $RESULT['id'];

                //TRACE echo "<h3>User found: $r_username</h3>";

                include_once($this->ROOT . "/common/session/sessioninfo.php");
                $this->CURRENT_USER = new User($r_id, $r_username);

                return true;
            }
            else {
                return false;
            }

        }
        return false;
    }

}