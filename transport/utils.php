<?php

include("common/session/sessioninfo.php");

class TransportUtils
{

	private $connection;

	private $current_user;

	private $ROOT_DIR;

	public function __construct()
	{
		$this->ROOT_DIR = $_SERVER['DOCUMENT_ROOT'];

		if($this->connect())
		{
			session_start();

			$session_uid = $_SESSION['game_user_id'];
			$session_username = $_SESSION['game_username'];

			$this->current_user = new User($session_uid, $session_username);
		}
		else
		{
			die("Connection failed! - Transport Utils");
		}
	}
	private function connect(){
		try{
			$this->connection = mysqli_connect("localhost", "Stian Arnesen", "dynamicgaming", "motagamedata");
			return true;
		}
		catch(Exception $e)
		{
		    echo $e->getMessage();
		    return false;
		}
		
	}
	public function getTransportList($result_amount)
	{
		$user_id = $this->current_user->getUserId();

		$RESULT = "";

		if($this->connection)
		{
			$SQL = "SELECT * FROM transport_list WHERE user_id='$user_id' LIMIT $result_amount";
			$DO_SQL = mysqli_query($this->connection, $SQL);

			while($T_ITEM = mysqli_fetch_array($DO_SQL))
			{
				$V_ID = $T_ITEM['vehicle_id'];

				$V_SQL = "SELECT * FROM transport_vehicle WHERE id='$V_ID'";
				$V_DO_SQL = mysqli_query($this->connection, $V_SQL);
				
				$V_ITEM = mysqli_fetch_array($V_DO_SQL);

					$V_ID = $V_ITEM['item_id'];

				$VS_SQL = "SELECT * FROM items WHERE id='$V_ID'";

				$VS_DO_SQL = mysqli_query($this->connection, $VS_SQL);

				$VS_ITEM = mysqli_fetch_array($VS_DO_SQL);

					$ITEM_IMG = $VS_ITEM['picture'];

				$PROGRESS_PERCENT = round(100*($T_ITEM['start_time'] / $T_ITEM['end_time']));

				$CARGO_ID = $V_ITEM['cargo_id'];

				$cargoSql = "SELECT * FROM cargo_list WHERE id='$CARGO_ID'";
				$doGetCargo = mysqli_query($this->connection, $cargoSql);



					$cargo_weight = 0;


				while($CARGO_ITEM = mysqli_fetch_array($doGetCargo))
				{
					$cargo_weight += $CARGO_ITEM['item_amount'];
				}



				$RESULT .= '<div class="transport_item"> ';
					$RESULT .= '<div class="transport_item_image"> <img src="'. $ITEM_IMG  .'"></div>';
					$RESULT .= '<div class="transport_item_distance">'. $T_ITEM['distance'] .' km</div>';
					$RESULT .= '<div class="transport_item_weight">'. $cargo_weight .' g</div>';
					$RESULT .= '<div class="transport_item_progress_bar" style="width: '. $PROGRESS_PERCENT .'%; background-color: white; padding: 7px; display: inline-table">'. $PROGRESS_PERCENT .'%</div>';
				$RESULT .= '</div>';
			}
		}
		return $RESULT;
	}
}