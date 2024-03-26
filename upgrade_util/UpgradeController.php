<?php
/**
 * Created by Stian Arnesen.
 * User: Stian
 * Date: 2016-01-29
 * Time: 09:24
 */

class UpgradeController
{
    private $USER;

    private $dbCon;

    public function __construct()
    {
        if($this->connect()){
          if($this->loadUser()){

          }
        }
    }
    public function upgradeLightLevel(){

    }
    private function connect()
  	{
  		$ROOT = $_SERVER['DOCUMENT_ROOT'];

  		require_once($ROOT . "/connect/database.php");

  		if($this->dbCon = Database::getConnection())
  		{
  			return true;
  		}
			return false;
  	}
    private function loadUser()
  	{
  		include("../../common/session/sessioninfo.php");

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
    private function getUserItemLevels(){
        return true;
    }

}
