<?php

$ROOT  = $_SERVER['DOCUMENT_ROOT'];
header('Content-Type: text/html; charset=utf-8');
require($ROOT . "prepared/Database.php");

if(isset($_POST['item_id']))
{
  $ID = $_POST['item_id'];

  $ITEM = new ItemInfo($ID);

  $r = $ITEM->getItemHTML();

  die($r);
}

die();

class ItemInfo
{
  private $dbCon;

  private $HTML;

  public function __construct($item_id)
  {
    $this->HTML = "<meta charset='UTF-8'><meta http-equiv='Content-Type' content='text/html;charset=ISO-8859-1'> <div class='item-info-overlay'>";
    if(! $this->connect())
    {
      $this->HTML .= "Connection Failed!";
    }
    else {
      $this->loadItem($item_id);
    }
  }
  private function loadItem($ID)
  {
    $connection = $this->dbCon;
    $connection->set_charset('utf8');


    if(! $connection->connect_error)
    {
      $QUERY = "SELECT name, beskrivelse, picture FROM items WHERE id=?";

      if($stmt = $connection->prepare($QUERY)){
        if($stmt->bind_param('i', $ID) && $stmt->execute())
        {
          if($stmt->bind_result($ITEM_NAME, $ITEM_DESCRIPTION, $ITEM_IMAGE))
          {
            $stmt->fetch();
            $this->HTML .= "<div class='item-info-overlay-title'><span>$ITEM_NAME</span></div>";
            $this->HTML .= "<img src='$ITEM_IMAGE' class='item-info-overlay-image'>";
            $this->HTML .= "<div class='item-info-overlay-desc'>" . $this->getExcerpt($ITEM_DESCRIPTION, 150) . "</div>";
          }
          else {
            $this->HTML .= "<strong>Failed to bind result!</strong>";
          }

        }
        else {
          die("Failed to bind parameters!");
        }
      }
      else {

      }
    }
    $this->HTML .= "</div>";
  }
  private function getExcerpt($str, $limit)
  {
    if(strlen($str) > $limit)
    {
      return substr($str, 0, $limit) . "...";
    }
    return $str;
  }
  public function getItemHTML()
  {
    return $this->HTML;
  }
  private function connect(){
    if($this->dbCon = PreparedDatabase::getConnection()){
      return true;
    }
    return false;
  }
}
