<?php

class StatsController
{
    private $CONNECTION;

    private $IS_CONNECTED;

    private $USER;

    public function __construct()
    {
        if($this->_CONNECT())
        {
            if($this->_LOAD_USER())
            {
                $this->IS_CONNECTED = true;
                $this->doRoutineCheck();
            }
        }
    }
    private function doRoutineCheck()
    {
        if(! $this->doesUserHaveStatTrackInsideDatabase())
        {
            if($this->insertNewStatTrackerForCurrentUser())
            {
                return true;
            }
        }
        else
        {
            return true;
        }

    }

    private function doesUserHaveStatTrackInsideDatabase()
    {
        if($this->IS_CONNECTED)
        {
            $USER_ID = $this->USER->getUserId();

            $SQL = "SELECT id FROM user_stats WHERE user_id = '$USER_ID'";

            if($QUERY = mysqli_query($this->CONNECTION, $SQL))
            {
                if($RESULT = mysqli_fetch_row($QUERY))
                {
                    if(isset($RESULT))
                    {
                        if(isset($RESULT[0]))
                        {
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }
    private function insertNewStatTrackerForCurrentUser()
    {
        if($this->IS_CONNECTED)
        {
            $USER_ID = $this->USER->getUserId();

            $SQL = "INSERT INTO user_stats(user_id) VALUES('$USER_ID')";

            if(mysqli_query($this->CONNECTION, $SQL))
            {
                return true;
            }
        }
        return false;
    }
    public function updatePlantHarvestAmount($AMOUNT_ADD)
    {
        if($this->IS_CONNECTED)
        {
            $USER_ID = $this->USER->getUserId();
            $SQL = "UPDATE user_stats SET plant_harvest_amount = plant_harvest_amount + '$AMOUNT_ADD' WHERE user_id = '$USER_ID'";

            if(mysqli_query($this->CONNECTION, $SQL))
            {
                return true;
            }
        }
        return false;
    }
    public function updateMoneyEarned($AMOUNT_ADD)
    {
        if($this->IS_CONNECTED)
        {
            $USER_ID = $this->USER->getUserId();
            $SQL = "UPDATE user_stats SET money_total = money_total + '$AMOUNT_ADD' WHERE user_id = '$USER_ID'";

            if(mysqli_query($this->CONNECTION, $SQL))
            {
                return true;
            }
        }
        return false;
    }
    public function getLastLevelForUser()
    {
        if($this->IS_CONNECTED)
        {
            $USER_ID = $this->USER->getUserId();
            $SQL = "SELECT last_level FROM user_stats WHERE user_id = '$USER_ID'";

            if($QUERY = mysqli_query($this->CONNECTION, $SQL))
            {
                if($RESULT = mysqli_fetch_array($QUERY))
                {
                    return $RESULT[0];
                }
            }
        }
        return false;
    }
    public function getAllStatsFromUser()
    {
        if($this->IS_CONNECTED)
        {
            $USER_ID = $this->USER->getUserId();
            $SQL = "SELECT * FROM user_stats WHERE user_id = '$USER_ID'";

            if($QUERY = mysqli_query($this->CONNECTION, $SQL))
            {
                if($RESULT = mysqli_fetch_array($QUERY))
                {
                    return $RESULT;
                }
            }
        }
        return false;
    }
    private function _LOAD_USER()
    {
        require_once(__DIR__ . "/../common/session/sessioninfo.php");

        if($this->USER = new User(-1, -1))
        {
            if($this->USER->isLoggedIn())
            {
                return true;
            }
        }

        return false;
    }
    private function _CONNECT()
    {
        require_once(__DIR__ . "/../connect/database.php");

        if($this->CONNECTION = Database::getConnection())
        {
            return true;
        }

        return false;
    }


}