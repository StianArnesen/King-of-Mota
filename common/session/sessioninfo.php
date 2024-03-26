<?php


class User
{

    private $CONNECTION;

    private $BOOOLEAN_connected;

    private $userID;
    private $username;
    private $userPicture;
    private $userMoney;
    private $userCoins;

    private $userLevel;
    private $userExp;
    private $neededExp;

    private $userCrew;

    // Access level define the access level of the current user. (0: regular user, 1: moderator, 2: admin)
    private $ACCESS_LEVEL;

    private $ROOT_DIR;

    private $LOGGED_IN;

    private $MAX_EXP_FOR_ALL_LEVELS = 4020;


    public function __construct($ID = -1,$USERNAME = -1)
    {
        $_USERNAME          = $USERNAME;
        $_ID                = $ID;

        $this->LOGGED_IN    = false;

        if($ID == -1 || $USERNAME == -1)
        {
            if($this->loadSessionInfo())
            {
                $_USERNAME  = $this->username;
                $_ID        = $this->userID;
            }
            else
            {

            }
        }
        $this->ROOT_DIR     = $_SERVER['DOCUMENT_ROOT'];

        $this->BOOOLEAN_connected = false;

        $this->username     = $_USERNAME;
        $this->userID       = $_ID;

        if($this->connect())
        {
            if($this->loadUserInfo())
            {
                $this->LOGGED_IN = true;

                if($this->ACCESS_LEVEL >= 1)
                {

                }
                else
                {

                }

            }
            else
            {
                $this->LOGGED_IN = false;
            }
        }
        else
        {
            die("Connection failed. sessioninfo.kk");
        }
        if(! $this->LOGGED_IN)
        {
            header("location: index.php");
        }
    }
    public function getRank(){

        $USER_ID = $this->getUserId();
        
        $SQL = "SELECT id, level, FIND_IN_SET( level, (    
                  SELECT GROUP_CONCAT( level
                    ORDER BY level DESC ) 
                  FROM users )
                ) AS rank
                FROM users
                WHERE id='$USER_ID'";
        
        if($QUERY = mysqli_query($this->CONNECTION, $SQL)){
            $R = mysqli_fetch_array($QUERY);
            return $R['rank'];
        }
        return false;
    }
    private function loadSessionInfo()
    {
        if(! isset($_SESSION))
        {
            session_start();
        }
        if(isset($_SESSION['game_user_id']) && $_SESSION['game_user_id'] != "")
        {
            $this->userID   = $_SESSION['game_user_id'];
            $this->username = $_SESSION['game_username'];

            return true;
        }
        return false;
    }
    public function isLoggedIn()
    {
        return  $this->LOGGED_IN;
    }
    private function loadUserInfo()
    {
        if($this->BOOOLEAN_connected)
        {
            $ID = $this->userID;

            $SQL = "SELECT id, username, profile_picture, level, money, crew_id, current_exp, next_level_exp, user_access_level, g_coins FROM users WHERE id='$ID'";
            $QUERY = mysqli_query($this->CONNECTION, $SQL);

            $RESULT = mysqli_fetch_array($QUERY);

            if(isset($RESULT))
            {
                $this->username         = $RESULT['username'];
                $this->userPicture      = $RESULT['profile_picture'];
                $this->userLevel        = $RESULT['level'];
                $this->userMoney        = $RESULT['money'];
                $this->userCrew         = $RESULT['crew_id'];
                $this->userExp          = $RESULT['current_exp'];
                $this->neededExp        = $RESULT['next_level_exp'];
                $this->ACCESS_LEVEL     = $RESULT['user_access_level'];
                $this->userCoins        = $RESULT['g_coins'];

                if(! file_exists($_SERVER['DOCUMENT_ROOT'] . $this->userPicture))
                {
                    $this->userPicture = "img/0.jpg";                    
                }
                
                return true;
            }    
            
        }
        return false;
    }
    public function addMoney($MONEY)
    {
        if(is_numeric($MONEY) && $MONEY >= 0 && ($this->userMoney + $MONEY >= 0))
        {
            $USER_ID = $this->userID;
            $SQL = "UPDATE users SET money=money + $MONEY WHERE id='$USER_ID' LIMIT 1";
            if(mysqli_query($this->CONNECTION, $SQL)) {
                return true;
            }
        }
        return false;
    }
    public function subtractMoney($MONEY)
    {
        if($MONEY >= 0 && ($this->userMoney + $MONEY >= 0))
        {
            $USER_ID    = $this->userID;
            $SQL        = "UPDATE users SET money = money - $MONEY WHERE id='$USER_ID' LIMIT 1";
            
            if(mysqli_query($this->CONNECTION, $SQL)) 
            {
                $SQL = "UPDATE user_stats SET money_used = money_used - $MONEY WHERE user_id='$USER_ID' LIMIT 1";
                
                if(mysqli_query($this->CONNECTION, $SQL)) 
                {
                    return true;
                }                
            }            

        }
        return false;
    }
    public function setMoney($MONEY)
    {
        if(is_numeric($MONEY) && $MONEY >= 0 && ($this->userMoney + $MONEY >= 0))
        {
            $USER_ID = $this->userID;
            $SQL = "UPDATE users SET money=$MONEY WHERE id=$USER_ID LIMIT 1";
            if(mysqli_query($this->CONNECTION, $SQL))
            {
                return true;
            }
        }
        return false;
    }
    public function addUserExp($EXP_AMOUNT)
    {
        $USER_ID = $this->userID;

        $USER_CURRENT_LEVEL = $this->userLevel;
        $USER_CURRENT_EXP   = $this->userExp;
        $USER_EXP_LIMIT     = $this->neededExp;

        $NEW_EXP            = $USER_CURRENT_EXP + $EXP_AMOUNT;
        $NEW_EXP_LIMIT      = $USER_EXP_LIMIT;
        $NEW_LEVEL          = $USER_CURRENT_LEVEL;

        if($NEW_EXP >= $USER_EXP_LIMIT)
        {
            $NEW_LEVEL++;
            $NEW_EXP       %= $USER_EXP_LIMIT;
            $NEW_EXP_LIMIT = $this->getNewExpLimit();
        }

        $SQL = "UPDATE users SET level='$NEW_LEVEL', current_exp='$NEW_EXP', next_level_exp='$NEW_EXP_LIMIT' WHERE id='$USER_ID'";

        if($QUERY = mysqli_query($this->CONNECTION, $SQL))
        {
            return true;
        }

        return false;
    }
    private function getNewExpLimit(){
        $CALC_EXP_FOR_NEXT_LEVEL = round(pow(((1 / ($this->getLevel())) + 1.4),$this->getLevel()-1), PHP_ROUND_HALF_UP) * 50;
        
        return (($CALC_EXP_FOR_NEXT_LEVEL) > $this->MAX_EXP_FOR_ALL_LEVELS)? $CALC_EXP_FOR_NEXT_LEVEL : $this->MAX_EXP_FOR_ALL_LEVELS ;
    }
    public function getUserImage()
    {
        return $this->userPicture;
    }
    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userID;
    }
    public function getUsername()
    {
        return $this->username;
    }
    public function getCrewId()
    {
        return $this->userCrew;
    }
    public function getLevel()
    {
        return $this->userLevel;
    }
    public function getExp()
    {
        return $this->userExp;
    }
    public function getMoney()
    {
        return $this->userMoney;
    }
    public function getCoins()
    {
        return $this->userCoins;
    }
    public function getNeededExp()
    {
        return $this->neededExp;
    }

    public function subtractCoins($coins)
    {
        if(is_numeric($coins) && ($coins >= 0) && ($coins + $this->getCoins() >= 0))
        {
            $USER_ID    = $this->userID;

            $SQL        = "UPDATE users SET g_coins=g_coins - '$coins' WHERE id='$USER_ID' LIMIT 1";

            if(mysqli_query($this->CONNECTION, $SQL))
            {
                return true;
            }
        }
        return false;
    }
    private function giveCoins($amount)
    {
        if($amount > 0)
        {
            $USER_ID    = $this->getUserId();

            $SQL        = "UPDATE users SET g_coins=g_coins + '$amount' WHERE id='$USER_ID' LIMIT 1";

            if($QUERY = mysqli_query($this->CONNECTION, $SQL))
            {
                return true;
            }
            return false;
        }
    }

    public function giveRandomCoin()
    {
        $randomNumber       = rand(0, 100);

        if($randomNumber   <= 20)
        {
            if($this->giveCoins(1))
            {
                return 1;
            }
        }
        return 0;
    }


    public function getUserAccessLevel()
    {
        return $this->ACCESS_LEVEL;
    }
    public function isUserAdmin()
    {
        return $this->ACCESS_LEVEL == 2;
    }

    private function connect()
    {
        include_once($this->ROOT_DIR . "/connect/connection.php");

        $StaticConnectionClass = new StaticConnection();

        $CON_PARAM = $StaticConnectionClass->getSecureConnectionParams();

        if($this->CONNECTION = mysqli_connect($CON_PARAM[0],$CON_PARAM[1], $CON_PARAM[2],$CON_PARAM[3]))
        {
            $this->BOOOLEAN_connected = true;
            return true;
        }
        return false;
    }
}
