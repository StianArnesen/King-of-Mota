<?php



if(isset($_POST['space_id']))
{
	if(is_numeric($_POST['space_id']))
	{
		session_start();

		$space_id = $_POST['space_id'];
		$user_id = $_SESSION['game_user_id'];

		$GROW_SPACE = new GrowSpace($space_id, $user_id);

		die($GROW_SPACE->getGrowSpaceView());
	}
	else
	{
		die("ERROR: GrowSpace Item not found!");
	}

}



class GrowSpace
{
	private $dbCon;

	private $SPACE_ID;

	public function GrowSpace($GROW_SPACE_ID, $USER_ID)
	{
		if($this->connect())
		{
			$this->SPACE_ID = $GROW_SPACE_ID;
		}
	}
	public function getGrowSpaceView() //Info boks som viser info om dyrkestasjonen. (hvor mange gram weed produsert, gjenstående tid, weed plante, ventilasjon, lys)
	{
		$GROW_SPACE_INFO = $this->getGrowSpaceItem($this->SPACE_ID); //[space_id, space_user_id, space_plant_id, space_light, space_air_vent, space_pot, space_name].

		$TITLE = $GROW_SPACE_INFO['space_name'];
		$PLANT_ID = $GROW_SPACE_INFO['space_plant_id'];

		$GROWING_ITEM = $this->getGrowingItem($PLANT_ID);

		$TIME_LEFT = ($GROWING_ITEM['start'] + $GROWING_ITEM['finish']) - time();

		if($TIME_LEFT != 0)
		{
			$PLANT_TIME_LEFT_PERCENT =  $GROWING_ITEM['finish'] / $TIME_LEFT;
		}


		$PLANT_ITEM = $this->getItemInfo($GROWING_ITEM['item_id']);

		$result = "<meta charset='UTF-8'><meta http-equiv='Content-Type' content='text/html;charset=ISO-8859-1'>
		<div class='grow-space-item-view' id='grow-space-info-view'>";
			$result .= $this->getOverlayHandleBar($TITLE);
			$result .= $this->getPlantGridLayout($PLANT_ITEM);

			$result .= $this->getHiddenValues($GROWING_ITEM);
		$result .= "</div>";
		$result .= $this->getProgressLayout($GROWING_ITEM);

		return $result;
	}
	private function getOverlayHandleBar($TITLE)
	{
		$result = "<div class='grow-space-overlay-view-handle' id='overlay-data-handle'>";
			$result .= "<div class='grow-space-info-title'> <span>". $TITLE ."</span> </div>";
			$result .= "<div class='overlay-close-button' onclick='closeGrowSpace()'>x</div>";
		$result .= "</div>";
		return  $result;
	}
	private function getHiddenValues($growing_item_array)
	{
		$array = $growing_item_array;

		$time_total = ($array['start'] + $array['finish']);

		$time_finish = $array['finish'];

		$time_left = $time_total - time();

		$r = "<input type='hidden' id='hidden-time-left' value='". $time_left ."'>";
		$r .= "<input type='hidden' id='hidden-time-total' value='". $time_finish ."'>";

		return $r;
	}
	private function getSoilGridLayout($ITEM_INFO_ARRAY)
	{
		$ITEM_IMAGE = $ITEM_INFO_ARRAY['picture'];

		$ITEM_TITLE = $ITEM_INFO_ARRAY['name'];

		$ITEM_POWER = $ITEM_INFO_ARRAY['item_power'];
		$ITEM_GROW_TIME = $ITEM_INFO_ARRAY['grow_time'];
		$ITEM_TYPE 	= $ITEM_INFO_ARRAY['type'];

		$MAX_POWER = $this->getMaxPropertyOfItemType($ITEM_TYPE, "item_power");
		$MAX_GROW_TIME = $this->getMaxPropertyOfItemType($ITEM_TYPE, "grow_time");

		$result = "<div class='soil-item-info-view info-grid-item'>";
		$result .= "<div class='grid-item-title'>Soil: ". $ITEM_TITLE ."</div>";
		$result .= "<div class='soil-item image'> <img src='". $ITEM_IMAGE ."'> </div>";
		$result .= "<div class='item-info-view'>";
		$result .= "<div class='soil-item item-info-div title'> Power: <span>". $ITEM_POWER ." </span> </div>";
		$result .= '<div class="item-info-bar-div info-bar-power">
					<div class="item-info-bar" style="width: '. (($ITEM_POWER/$MAX_POWER)*99) .'%; background-color: rgba('. (255 - round(255 * ($ITEM_POWER/$MAX_POWER))) .','. (round(255 * ($ITEM_POWER/$MAX_POWER))) .',100,1);"></div>
			</div>';


		$result .= "</div>";


		$result .= "</div>";

		return $result;
	}
	private function getLightGridLayout($ITEM_INFO_ARRAY)
	{
		$ITEM_IMAGE = $ITEM_INFO_ARRAY['picture'];

		$ITEM_TITLE = $ITEM_INFO_ARRAY['name'];

		$ITEM_POWER = $ITEM_INFO_ARRAY['item_power'];
		$ITEM_GROW_TIME = $ITEM_INFO_ARRAY['grow_time'];
		$ITEM_TYPE 	= $ITEM_INFO_ARRAY['type'];

		$MAX_POWER = $this->getMaxPropertyOfItemType($ITEM_TYPE, "item_power");
		$MAX_GROW_TIME = $this->getMaxPropertyOfItemType($ITEM_TYPE, "grow_time");

		$result = "<div class='light-item-info-view info-grid-item'>";
			$result .= "<div class='grid-item-title'>Light: ". $ITEM_TITLE ."</div>";
			$result .= "<div class='light-item image'> <img src='". $ITEM_IMAGE ."'> </div>";
			$result .= "<div class='item-info-view'>";
			$result .= "<div class='light-item item-info-div title'> Power: <span>". $ITEM_POWER ." </span> </div>";
			$result .= '<div class="item-info-bar-div info-bar-power">
					<div class="item-info-bar" style="width: '. (($ITEM_POWER/$MAX_POWER)*99) .'%; background-color: rgba('. (255 - round(255 * ($ITEM_POWER/$MAX_POWER))) .','. (round(255 * ($ITEM_POWER/$MAX_POWER))) .',100,1);"></div>
			</div>';


			$result .= "</div>";


		$result .= "</div>";

		return $result;
	}
	private function getPlantGridLayout($ITEM_INFO_ARRAY)
	{
		$ITEM_IMAGE = $ITEM_INFO_ARRAY['picture'];

		$ITEM_TITLE = $ITEM_INFO_ARRAY['name'];

		$ITEM_POWER = $ITEM_INFO_ARRAY['item_power'];
		$ITEM_GROW_TIME = $ITEM_INFO_ARRAY['grow_time'];
		$ITEM_TYPE 	= $ITEM_INFO_ARRAY['type'];

		$MAX_POWER = $this->getMaxPropertyOfItemType($ITEM_TYPE, "item_power");
		$MAX_GROW_TIME = $this->getMaxPropertyOfItemType($ITEM_TYPE, "grow_time");

		$result = "<div class='plant-item-info-view info-grid-item'>";
			$result .= "<div class='grid-item-title'>Plant: ". $ITEM_TITLE ."</div>";
			$result .= "<div class='plant-item image'> <img src='". $ITEM_IMAGE ."'> </div>";
			$result .= "<div class='item-info-view'>";
			$result .= "<div class='plant-item item-info-div title'> Power: <span>". $ITEM_POWER ." </span> </div>";
			$result .= '<div class="item-info-bar-div info-bar-power">
					<div class="item-info-bar" style="width: '. (($ITEM_POWER/$MAX_POWER)*100) .'%; background-color: rgba('. (255 - round(255 * ($ITEM_POWER/$MAX_POWER))) .','. (round(255 * ($ITEM_POWER/$MAX_POWER))) .',100,1);"></div>
			</div>';

			$result .= "<div class='plant-item item-info-div title'> Grow time: <span>". $this->secondsToTime($ITEM_GROW_TIME) ." </span> </div>";
			$result .= '<div class="item-info-bar-div info-bar-grow-time">
					<div class="item-info-bar" style="width: '. (($ITEM_GROW_TIME/$MAX_GROW_TIME)*100) .'%; background-color: rgba('. (255 - round(255 * ($ITEM_GROW_TIME/$MAX_GROW_TIME))) .','. (round(255 * ($ITEM_GROW_TIME/$MAX_GROW_TIME))) .',35,1);"></div>
			</div>';


			$result .= "</div>";


		$result .= "</div>";

		return $result;
	}
	private function getMaxPropertyOfItemType($ITEM_TYPE, $PROPERTY)
	{
		$SQL = "SELECT MAX($PROPERTY) FROM items WHERE type='$ITEM_TYPE' AND item_active='1'";
		$QUERY  = mysqli_query($this->dbCon, $SQL);

		$RESULT = mysqli_fetch_row($QUERY);
		return $RESULT[0];
	}
	private function getAirGridLayout($ITEM_INFO_ARRAY)
	{
		$ITEM_IMAGE = $ITEM_INFO_ARRAY['picture'];

		$ITEM_TITLE = $ITEM_INFO_ARRAY['name'];

		$ITEM_POWER = $ITEM_INFO_ARRAY['item_power'];
		$ITEM_GROW_TIME = $ITEM_INFO_ARRAY['grow_time'];
		$ITEM_TYPE 	= $ITEM_INFO_ARRAY['type'];

		$MAX_POWER = $this->getMaxPropertyOfItemType($ITEM_TYPE, "item_power");
		$MAX_GROW_TIME = $this->getMaxPropertyOfItemType($ITEM_TYPE, "grow_time");

		$result = "<div class='air-item-info-view info-grid-item'>";
			$result .= "<div class='grid-item-title'>Air vent: ". $ITEM_TITLE ."</div>";
			$result .= "<div class='air-item image'> <img src='". $ITEM_IMAGE ."'> </div>";
			$result .= "<div class='item-info-view'>";
			$result .= "<div class='air-item item-info-div title'> Power: <span>". $ITEM_POWER ." </span> </div>";
			$result .= '<div class="item-info-bar-div info-bar-power">
					<div class="item-info-bar" style="width: '. (($ITEM_POWER/$MAX_POWER)*100) .'%; background-color: rgba('. (255 - round(255 * ($ITEM_POWER/$MAX_POWER))) .','. (round(255 * ($ITEM_POWER/$MAX_POWER))) .',100,1);"></div>
			</div>';

			$result .= "</div>";


		$result .= "</div>";

		return $result;
	}
	private function secondsToTime($seconds) {
		$t = round($seconds);
		$t_new = sprintf('%02d:%02d:%02d', ($t/3600),($t/60%60), $t%60);

		$str_time = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $t_new);

		sscanf($t_new, "%d:%d:%d", $hours, $minutes, $seconds);

		$h = ($hours > 0)? $hours . "h ": "";
		$m = ($minutes > 0)? $minutes . "m ": "";
		$s = ($seconds > 0)? $seconds . "s ": "";

		$time_seconds = $h . $m . $s;

	  return $time_seconds;
	}
	private function getProgressLayout($growing_item_array)
	{
		$array = $growing_item_array;

		$GROWING_ITEM_ID = $array['id'];

		$time_complete = $array['start'] + $array['finish'];
		$time_left = $time_complete - time();

		$BAR_WIDTH = ($time_left / ($array['finish'] + 1)) * 100;


		$TIME_LEFT_TEXT = "";

		if($time_left <= 0)
		{
			$TIME_LEFT_TEXT = "<div class='status-complete'> Complete! </div>";
		}
		else
		{
			$TIME_LEFT_TEXT = "Time left: <div class='status-time'>" . $this->secondsToTime($time_left) . " seconds </div>";
		}



		$r = "<div class='grow-space-progress-view'>";
			$r .= "<div class='grow-space-time-left' id='time-left-text'>". $TIME_LEFT_TEXT ."</div>";
			$r .= "<div class='progressbar-container'> <div class='grow-space-time-progressbar' id='grow-space-bar' style='width: ". $BAR_WIDTH . "%;'></div> </div>";
			if($time_left <= 0)
			{
				$r .= "<button class='grow-space-harvest-button' onclick='harvestItem(". $GROWING_ITEM_ID .")'>Harvest</button>";
			}


		$r .= "</div>";

		return $r;

	}
	private function getGrowSpaceItem($grow_space_id) // Hent data fra 'grow_space' i DB, array[space_id, space_user_id, space_plant_id, space_air_vent, space_pot, space_name]
	{
		$SQL = "SELECT * FROM grow_space WHERE space_id='$grow_space_id'";
		$DO_SQL = mysqli_query($this->dbCon, $SQL);

		$RESULT = mysqli_fetch_array($DO_SQL);

		return $RESULT;
	}
	private function getGrowingItem($item_waiting_id)
	{
		$SQL = "SELECT * FROM item_waiting WHERE id='$item_waiting_id'";
		$DO_SQL = mysqli_query($this->dbCon, $SQL);

		$RESULT = mysqli_fetch_array($DO_SQL);

		return $RESULT;
	}
	private function getItemInfo($item_id)
	{
		$SQL = "SELECT * FROM items WHERE id='$item_id'";
		$DO_SQL = mysqli_query($this->dbCon, $SQL);

		$RESULT = mysqli_fetch_array($DO_SQL);

		return $RESULT;
	}
	private function connect()
	{
		$ROOT = $_SERVER['DOCUMENT_ROOT'];

		require_once($ROOT . "/connect/database.php");

		if($this->dbCon = Database::getConnection())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}
