<?php

include("../connect/connection.php");

class NotificationClass
{
    private $SE_CON;

    public function __construct()
    {
        if($this->connect())
        {
          
        }
        else
        {
            die("Connection failed");
        }
    }
    public function insertNotificationUpvote($USER_A, $USER_B)
    {
        $TYPE = 5;
        $TIME = time();

        $SQL = "INSERT INTO notifications (user_id, not_type, user_id_a, user_id_b ,not_time) VALUES ('$USER_B', '$TYPE', '$USER_A', '$USER_B', '$TIME')";
        $QUERY = mysqli_query($this->SE_CON->CONNECTION, $SQL);
    }
    private function connect()
    {


        if($this->SE_CON = new StaticConnection())
        {
            return true;
        }
        return false;
    }

}
