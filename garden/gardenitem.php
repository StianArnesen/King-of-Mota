<?php


class GrowingItem
{
	private $dbCon;

	private $item_link_id;

	private $space_id;

	public function __construct($growing_item_link_id, $space_id)
	{
		$this->item_link_id = $growing_item_link_id;
		$this->space_id = $space_id;
		$this->dbCon = Database::getConnection();
	}
	public function getItemLayout()
	{
		$RESULT = "";

		$ITEM_INFO = $this->getGrowInfo($this->item_link_id);

		$GROW_SPACE = $this->getGrowSpaceInfo($this->space_id);

		if(! isset($ITEM_INFO['item_id']))
		{
			$RESULT .= $this->getIdleGrowSpaceLayout();
			return $RESULT;
		}

		$ITEM = $this->getItemInfo($ITEM_INFO['item_id']);
		
		$ITEM_img = $ITEM['picture'];
		$ITEM_TITLE = $ITEM['name'];
		$ITEM_WAIT_ID = $this->item_link_id;
		
		$time_left = $ITEM_INFO['start'] + $ITEM_INFO['finish'] - time();
		$time_left_text = $this->secondsToTime($time_left);

		$WIDTH = ($time_left/($ITEM_INFO['finish'] + 1)) * 100;

		$time_total = $ITEM_INFO['finish'];

		if($time_left <= 0)
		{
			$HTML_ITEM_COMPLETE = "<div class='garden-item-complete-text'> <div class='garden-item-button-harvest'>Harvest !</div> </div>";
		}
		else{

			$HTML_ITEM_COMPLETE = "<div class='garden-item-complete-text' > <span>". $this->secondsToTime($time_left). "</span> </div>";
			$HTML_ITEM_COMPLETE .= "<div class='garden-item-time-left-div'>";
				$HTML_ITEM_COMPLETE .= "<div class='garden-item-time-left-bar' style='width: ". $WIDTH ."%'></div>";
			$HTML_ITEM_COMPLETE .= "</div>";
		}
		$SPACE_NAME = $GROW_SPACE['space_name'];
		
		$RESULT .= "<div class='garden-item item-active' onclick='showGrowSpace(". $this->space_id .")'>";
			$RESULT .= "<div class='garden-item-title'>". $this->getShortString($ITEM_TITLE, 11)  ."</div>";
			$RESULT .= $HTML_ITEM_COMPLETE;
			$RESULT .= "<div class='garden-item-img'> <img src='$ITEM_img'> </div>";
			$RESULT .= "<div class='time-left-div'>";
				$RESULT .= "<input type='hidden' name='item-time-left' class='garden-grid-item-time-left' value='$time_left'>";
				$RESULT .= "<input type='hidden' name='item-time-total' class='garden-grid-item-time-total' value='$time_total'>";
				$RESULT .= "<input type='hidden' name='item-wait-id' class='garden-grid-item-time-total' value='$ITEM_WAIT_ID'>";
			$RESULT .= "</div>";
		$RESULT .= "</div>";


		return $RESULT;
	}
	public function getEmptyItemLayout()
	{
		$RESULT = "<div class='garden-item buy-space' onclick='buyGrowSpace()'>";
		$RESULT .= "<div class='garden-item-title'>Buy more space</div>";
			$RESULT .= "<div class='add-item-img'> <img src='img/garden/add_space.png'> </div>";

		$RESULT .= "</div>";

		return $RESULT;
	}
	private function getIdleGrowSpaceLayout()
	{
		$GROW_SPACE = $this->getGrowSpaceInfo($this->space_id);

		$SPACE_NAME = "Empty pot";

		$IDLE_IMAGE = "img/garden/drop_weed.png";

		$RESULT = "<div class='garden-item item-empty' onclick='showGrowSpaceSetup(". $this->space_id .")'>";
			$RESULT .= "<div class='garden-item-title'>". $this->getShortString($SPACE_NAME, 11)  ."</div>";
			$RESULT .= "<div class='garden-item-img'> <img src='$IDLE_IMAGE' > </div>";

		$RESULT .= "</div>";

		return  $RESULT;
	}
	private function getShortString($STR, $LIMIT)
	{
		if(strlen($STR) > $LIMIT)
		{
			return substr($STR, 0, $LIMIT) . "...";
		}
		return $STR;
	}
	private function getGrowSpaceInfo($space_id)
	{
		$SQL = "SELECT * FROM grow_space WHERE space_id='$space_id'";
		$QUERY = mysqli_query($this->dbCon, $SQL);

		$RESULT = mysqli_fetch_array($QUERY);

		return $RESULT;
	}
	private function getItemInfo($ID)
	{
		$SQL = "SELECT * FROM items WHERE id='$ID'";
		$DO_SQL = mysqli_query($this->dbCon, $SQL);

		$RESULT = mysqli_fetch_array($DO_SQL);

		return $RESULT;

	}
	private function getGrowInfo($GROWING_LINK_ID)
	{
		$SQL = "SELECT * FROM item_waiting WHERE id='$GROWING_LINK_ID'";
		$DO_SQL = mysqli_query($this->dbCon, $SQL);

		$RESULT = mysqli_fetch_array($DO_SQL);

		return $RESULT;
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
}
