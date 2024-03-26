<?php


class InitNewUser
{
    private $dbCon;

    private $USER_ID;

    public function __construct()
    {
        $this->_connect();
    }
    public function _INIT_USER($USER_ID)
    {
        $this->USER_ID = $USER_ID;

        if(! $this->_INIT_USER_FARM()){
            return false;
        }
        if(! $this->_INIT_USER_GROW_SPACE()){
            return false;
        }
        if(! $this->_INIT_USER_STORAGE()){
            return false;
        }
        if(! $this->_INIT_USER_SETTINGS())
        {
            return false;
        }
        return true;
    }
    private function _INIT_USER_SETTINGS()
    {

        $USER_ID = $this->USER_ID;
        $SQL = "INSERT INTO user_settings(user_id) VALUES('$USER_ID')";
        $QUERY = mysqli_query($this->dbCon, $SQL);
        
        return ($QUERY)? true : false;
    }
    private function _INIT_USER_STORAGE()
    {
        $TITLE  = "Backpack";
        $TYPE   = 1;
        $SQL    = "INSERT INTO storage_units(user_id, storage_title, storage_type) VALUES('$this->USER_ID', '$TITLE', '$TYPE')";
        $QUERY  = mysqli_query($this->dbCon, $SQL);
        if(! $QUERY){
            return false;
        }
        return true;
    }
    private function isUserHasFarm()
    {
            $USER_ID = $this->USER_ID;

            $SQL = "SELECT * FROM user_farm_list WHERE user_id='$USER_ID'";
            $QUERY = mysqli_query($this->dbCon, $SQL);
            $RESULT = mysqli_fetch_array($QUERY);
            if($QUERY){
                if($RESULT){
                    if($RESULT['user_id'] == $USER_ID){
                            return true;
                    }
                }
            }
            return false;
    }
    private function _INIT_USER_FARM(){
        if(! $this->isUserHasFarm()){
            $USER_ID = $this->USER_ID;
            $SQL = "INSERT INTO user_farm_list(user_id) VALUES($USER_ID)";

            if($QUERY = mysqli_query($this->dbCon, $SQL)){
                return true;
            }
        }
        return false;
    }
    private function _INIT_USER_GROW_SPACE(){
        $USER_ID    = $this->USER_ID;
        $SQL        = "INSERT INTO grow_space(space_user_id) VALUES($USER_ID)";
        $QUERY      = mysqli_query($this->dbCon, $SQL);
        return ($QUERY)? true : false;
    }
    private function _connect()
    {
        $ROOT = $_SERVER['DOCUMENT_ROOT'];

        require_once($ROOT . "/connect/database.php");
        $this->dbCon = Database::getConnection();
        if($this->dbCon){
            return true;
        }

        return false;
    }
}