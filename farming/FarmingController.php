<?php
/**
 * Created by Stian Arnesen.
 * User: StianDesktop
 * Date: 2016-01-26
 * Time: 18:11
 */
$FarmingController = new FarmingController();
if(isset($_POST['upgrade_farm'])){
    die(json_encode($FarmingController->upgradeFarmLevel()));
}
else if(isset($_POST['upgrade_light'])){
    die(json_encode($FarmingController->upgradeLightLevel()));
}
else if(isset($_POST['upgrade_air'])){
    die(json_encode($FarmingController->upgradeAirLevel()));
}
else if(isset($_POST['upgrade_soil'])){
    die(json_encode($FarmingController->upgradeSoilLevel()));
}

class FarmingController
{
    private $dbCon;

    private $USER;

    private $CURRENT_FARM_INFO;

    private $CURRENT_UPGRADE_LEVELS;


    public function __construct()
    {
        if($this->_connect()){
            if($this->_loadUserInfo()){
                if($this->CURRENT_FARM_INFO = $this->getCurrentFarmInfo()){

                }
                else{
                    die("3: Failed to load farm info! -> constructor(3)->getFarmInfo()");
                }
            }
            else{
                die("2: Failed to load user!");
            }
        }
        else{
            die("1: Failed to connect!");
        }
    }

    public function getCurrentFarmName(){
        return $this->CURRENT_FARM_INFO['farm_name'];
    }
    public function getCurrentFarmImage(){
        return $this->CURRENT_FARM_INFO['farm_image'];
    }
    public function getCurrentFarmLevel(){
        return $this->CURRENT_FARM_INFO['farm_level'];
    }
    public function getCurrentUpgradeItemsInfoArray(){
        return $this->CURRENT_UPGRADE_LEVELS;
    }
    public function getUpgradeItemsPriceList(){

        $FARM_LEVEL     = $this->CURRENT_UPGRADE_LEVELS['farm_id'];
        $LIGHT_LEVEL    = $this->CURRENT_UPGRADE_LEVELS['light_level'];
        $AIR_LEVEL      = $this->CURRENT_UPGRADE_LEVELS['air_level'];
        $SOIL_LEVEL     = $this->CURRENT_UPGRADE_LEVELS['soil_level'];


        $FARM_PRICE     = round( (750 * (pow(2.5, $FARM_LEVEL)) ), 0, PHP_ROUND_HALF_UP);
        
        $LIGHT_PRICE    = round( (250 * (pow(2.1, $LIGHT_LEVEL)) ), 0, PHP_ROUND_HALF_UP);
        
        $AIR_PRICE      = round( (350 * (pow(2.2, $AIR_LEVEL)) ), 0, PHP_ROUND_HALF_UP);
        
        $SOIL_PRICE     = round( (350 * (pow(2, ($SOIL_LEVEL-1))) ), 0, PHP_ROUND_HALF_UP) -150;

        $RESULT = array(
            'farm_upgrade_price'    => $FARM_PRICE,
            'light_upgrade_price'   => $LIGHT_PRICE,
            'air_upgrade_price'     => $AIR_PRICE,
            'soil_upgrade_price'    => $SOIL_PRICE
        );
        return $RESULT;
    }
    public function upgradeFarmLevel(){
        
        $FARM_LEVEL = $this->CURRENT_UPGRADE_LEVELS['farm_id'] + 1;
        $UPGRADE_PRICE_LIST = $this->getUpgradeItemsPriceList();
        $USER       = $this->USER;
        $USER_ID    = $USER->getUserId();
        
        $FARM_UPGRADE_PRICE = $UPGRADE_PRICE_LIST['farm_upgrade_price'];

        $RESULT = array();

        if($USER->getMoney() >= $FARM_UPGRADE_PRICE)
        {
            $SQL = "UPDATE user_farm_list SET farm_id='$FARM_LEVEL' WHERE user_id='$USER_ID'";

            if($this->USER->subtractMoney($FARM_UPGRADE_PRICE))
            {
                if(mysqli_query($this->dbCon, $SQL))
                {
                    if($this->addSpace("New space")){
                        return array(
                            'STATUS' => "OK"
                        );
                    }
	                return array(
                        'STATUS'        => "FAILED",
                        'DIALOG_MSG'    => "The server could not create a new grow space for you! Please report this at the feedback page! @upgradeFarmLevel()"
                    );
                }
                $SQL_ERROR_MSG =  mysqli_error($this->dbCon);
	            return array(
                    'STATUS'        => "FAILED",
                    'DIALOG_MSG'    => "MySQL query failed! Query error: " . $SQL_ERROR_MSG
                );
            }
            return array(
                'STATUS'        => "FAILED",
                'DIALOG_MSG'    => "Failed to subract money from user!"
            );
        }
        return array(
            'STATUS'        => "FAILED",
            'DIALOG_MSG'    => "You dont have enough money to buy this upgrade!"
        );
    }
    public function upgradeLightLevel(){
        $LIGHT_LEVEL = $this->CURRENT_UPGRADE_LEVELS['light_level'] + 1;

        $UPGRADE_PRICE_LIST = $this->getUpgradeItemsPriceList();

        $USER = $this->USER;
        $USER_ID = $USER->getUserId();

        $UPGRADE_PRICE = $UPGRADE_PRICE_LIST['light_upgrade_price'];

        if($USER->getMoney() >= $UPGRADE_PRICE){
            $SQL = "UPDATE user_farm_list SET light_level='$LIGHT_LEVEL' WHERE user_id='$USER_ID'";

            if($this->USER->subtractMoney($UPGRADE_PRICE))
            {
                if(mysqli_query($this->dbCon, $SQL)){
                    return array(
                        'STATUS' => "OK"
                    );
                }
                return array(
                    'STATUS'        => "FAILED",
                    'DIALOG_MSG'    => "MySQL query failed! Query error: " . mysqli_error($this->dbCon)
                );
            }
            return array(
                'STATUS'        => "FAILED",
                'DIALOG_MSG'    => "Failed to subract money from user!"
            );
        }
        return array(
            'STATUS'        => "FAILED",
            'DIALOG_MSG'    => "You dont have enough money to buy this upgrade!"
        );
    }
    public function upgradeAirLevel(){
        $AIR_LEVEL = $this->CURRENT_UPGRADE_LEVELS['air_level'] + 1;

        $UPGRADE_PRICE_LIST = $this->getUpgradeItemsPriceList();

        $USER = $this->USER;
        $USER_ID = $USER->getUserId();

        $UPGRADE_PRICE = $UPGRADE_PRICE_LIST['air_upgrade_price'];

        if($USER->getMoney() >= $UPGRADE_PRICE)
        {
            $SQL = "UPDATE user_farm_list SET air_level='$AIR_LEVEL' WHERE user_id='$USER_ID'";

            if($this->USER->subtractMoney($UPGRADE_PRICE))
            {
                if(mysqli_query($this->dbCon, $SQL)){
                    return array(
                        'STATUS' => "OK"
                    );
                }
                return array(
                    'STATUS'        => "FAILED",
                    'DIALOG_MSG'    => "MySQL query failed! Query error: " . mysqli_error($this->dbCon)
                );
            }
            return array(
                'STATUS'        => "FAILED",
                'DIALOG_MSG'    => "Failed to subract money from user!"
            );
        }
        return array(
            'STATUS'        => "FAILED",
            'DIALOG_MSG'    => "You dont have enough money to buy this upgrade!"
        );
    }
    public function upgradeSoilLevel(){
        $SOIL_LEVEL = $this->CURRENT_UPGRADE_LEVELS['soil_level'] + 1;

        $UPGRADE_PRICE_LIST = $this->getUpgradeItemsPriceList();

        $USER = $this->USER;
        $USER_ID = $USER->getUserId();

        $UPGRADE_PRICE = $UPGRADE_PRICE_LIST['soil_upgrade_price'];

        if($USER->getMoney() >= $UPGRADE_PRICE)
        {
            $SQL = "UPDATE user_farm_list SET soil_level='$SOIL_LEVEL' WHERE user_id='$USER_ID'";

            if($this->USER->subtractMoney($UPGRADE_PRICE))
            {
                if($QUERY = mysqli_query($this->dbCon, $SQL))
                {
                    return array(
                        'STATUS' => "OK"
                    );
                }
                return array(
                    'STATUS'        => "FAILED",
                    'DIALOG_MSG'    => "MySQL query failed! Query error: " . mysqli_error($this->dbCon)
                );
            }
            return array(
                'STATUS'        => "FAILED",
                'DIALOG_MSG'    => "Failed to subract money from user!"
            );
        }
        return array(
            'STATUS'        => "FAILED",
            'DIALOG_MSG'    => "You dont have enough money to buy this upgrade!"
        );
    }
    private function addSpace($space_name){

        $USER = $this->USER;

        $USER_ID = $USER->getUserId();

        $SQL = "INSERT INTO grow_space(space_user_id, space_name) VALUES ('$USER_ID','$space_name')";
        if($DO_SQL = mysqli_query($this->dbCon, $SQL)){
            return true;
        }

        return false;
    }

    private function getCurrentFarmInfo(){ // Returns the current user's farm info. Everything in user_farm_list with matching user_id.
        $USER_ID = $this->USER->getUserId();
		
        $SQL = "SELECT farm_id, light_level, air_level, soil_level FROM user_farm_list WHERE user_id='$USER_ID'";

        if($QUERY = mysqli_query($this->dbCon, $SQL)){

            $USER_FARM_INFO = mysqli_fetch_array($QUERY);

            $FARM_ID = $USER_FARM_INFO['farm_id'];
            $this->CURRENT_UPGRADE_LEVELS = $USER_FARM_INFO;

            if($USER_FARM_INFO = $this->getFarmInfo($FARM_ID)){

                return $USER_FARM_INFO;
            }
            return false;
        }
        return false;
    }
    public function getUpgradeLevelList(){

        $FARM_LEVEL = $this->CURRENT_UPGRADE_LEVELS['farm_id'];
        $LIGHT_LEVEL = $this->CURRENT_UPGRADE_LEVELS['light_level'];
        $AIR_LEVEL = $this->CURRENT_UPGRADE_LEVELS['air_level'];
        $SOIL_LEVEL = $this->CURRENT_UPGRADE_LEVELS['soil_level'];

        $RESULT = array(
            'farm_level'    => $FARM_LEVEL,
            'light_level'   => $LIGHT_LEVEL,
            'air_level'   => $AIR_LEVEL,
            'soil_level'   => $SOIL_LEVEL
            );

        return $RESULT;
    }
    public function getFarmInfo($FARM_LEVEL){
		if($FARM_LEVEL >= 6){
			$R = Array(
				'farm_id' => $FARM_LEVEL,
				'farm_level' => $FARM_LEVEL,
				'farm_name' => 'Master farm. Lvl: ' . ($FARM_LEVEL -5) ,
				'farm_image' => 'img/farm/farm_level_04.png',
			);
			
			return $R;
		}
        $SQL = "SELECT * FROM farm_level_list WHERE farm_level='$FARM_LEVEL'";

        if($QUERY = mysqli_query($this->dbCon, $SQL)){
            return mysqli_fetch_array($QUERY);
        }
        return false;
    }
    public function callUserFarmUpgrade(){
        $USER = $this->USER;
        
        $FARM_INFO = $this->getFarmInfo($this->getCurrentFarmLevel() + 1);
        
        if($USER->getMoney() >= $FARM_INFO['farm_price']){
			
        }
    }
    private function _loadUserInfo()
    {
        $ROOT = $_SERVER['DOCUMENT_ROOT'];

        include_once($ROOT . "common/session/sessioninfo.php");

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
    private function _connect()
    {
        $ROOT = $_SERVER['DOCUMENT_ROOT'];

        require_once($ROOT . "/connect/database.php");
        if($this->dbCon = Database::getConnection()){
            return true;
        }

        return false;
    }
}
