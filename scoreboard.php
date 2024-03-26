<?php

function GET_ROOT(){
	return $_SERVER["DOCUMENT_ROOT"];
}

class scoreboard
{
	protected $CONNECTION;

	protected $SESSION_USERNAME;

	protected $SESSION_STATUS_VALID;

	protected $USER_CONTROLLER;


	protected $USER;


	public function __construct()
	{
		if($this->_connect())
		{
			$this->main_init();
		}
	}
	private function main_init()
	{
		if($this->_loadUser())
		{
			if(! $this->loadUserController())
			{
                die("dude, we failed to load your user... Please report this to developer plz.");
			}
		}
		else
		{
			die("scoreboard.php -> Failed to load user in class.");
		}
	}
	private function loadUserController()
	{
		require("user/UserController.php");

		if($this->USER_CONTROLLER = new UserController())
		{
			return true;
		}

		return false;
	}
	private function _loadUser()
	{
		require_once "common/session/sessioninfo.php";

		if($this->USER = new User())
		{
			return true;
		}
		return false;
	}

	public function _connect()
	{
		require_once("connect/database.php");

		if($this->CONNECTION = Database::getConnection())
		{
			return true;
		}
			return false;
	}

	public function getTopMenuBanner()
	{
        $TOP_BANNER = fopen(GET_ROOT() . "layout/top_banner/top_banner.php", "r") or die("Unable to load TopBanner!");
		return fread($TOP_BANNER,filesize(GET_ROOT() . "layout/top_banner/top_banner.php"));
	}
	public function getUserRank($USER_ID = null)
	{
		$USER = $this->USER->getUserId();

		$SQL = "SELECT DISTINCT users.* , user_stats.* FROM users INNER JOIN user_stats ON user_stats.user_id = users.id ORDER BY level DESC, current_exp DESC";
		$QUERY = mysqli_query($this->CONNECTION, $SQL);
	}
	public function getScoreBoard($RESULT_LIMIT, $ORDER_BY)
	{
		$RESULT = "";

		$SQL = "SELECT DISTINCT users.* FROM users ORDER BY $ORDER_BY DESC, current_exp DESC";
		$QUERY = mysqli_query($this->CONNECTION, $SQL);

		if($this->USER->isLoggedIn())
		{
			$RANK = 0;

			while($USER = mysqli_fetch_array($QUERY) )
			{
				if($RANK < $RESULT_LIMIT)
				{
					$IMG 		= $USER['profile_picture'];
					$USERNAME 	= $USER['username'];
					$USER_ID 	= $USER['id'];
					$LEVEL 		= $USER['level'];
					$IS_ADMIN 	= ($USER['user_access_level'] >= 2);
					if($IS_ADMIN){
						continue;
					}
					$RANK++;
				
					$RANK_IMG = "";

					$XTRA_USERNAME_CLASS_HTML = ""; // Linking to extra class for style sheet. - username field. (Empty as default).

					if($RANK == 1)
					{
						$RANK_IMG = "<div class='user_item_rank_icon'> <img src='img/icon/ranks/gold.png'> </div>";
						$XTRA_USERNAME_CLASS_HTML = "rank-first";
					}
					else if($RANK == 2)
					{
						$RANK_IMG = "<div class='user_item_rank_icon'> <img src='img/icon/ranks/silver.png' > </div>";
						$XTRA_USERNAME_CLASS_HTML = "rank-second";
					}
					else if($RANK == 3)
					{
						$RANK_IMG = "<div class='user_item_rank_icon'> <img src='img/icon/ranks/bronze.png' > </div>";
						$XTRA_USERNAME_CLASS_HTML = "rank-third";
					}
					else
					{
						$RANK_IMG = "<div class='user_item_rank'>#$RANK</div>";
					}

					if($USER_AWARDS = $this->USER_CONTROLLER->getUserAwardList($USER_ID))
					{

					}
					else{
						die("FAILED TO LOAD AWARDS!");
					}

					$RESULT .=
					"<div class='user_item'>
					<div class='user_username'>
						<div class='username-container'>$RANK_IMG <a href='$USERNAME' class='$XTRA_USERNAME_CLASS_HTML'> $USERNAME </a></div>
					 </div>
					 
						<img  class='user_image' src='$IMG'>
							<div class='user_item_info'>
								<div class='user_level'>Level: ". $LEVEL ."</div>
								<div class='user-award-list'>
									<div class='user-award-title'> Awards:  </div>
								";

								while($AWARD = mysqli_fetch_array($USER_AWARDS))
								{
									$A_IMAGE 	= $AWARD['image']; // Award image
									$DESC 		= $AWARD['description']; // Award desc
									$TITLE 		= $AWARD['title']; // Award title

									$RESULT .= "<div class='award-item'>
													<img title='$DESC' class='award-icon' src='$A_IMAGE'>
													<div class='title'>$TITLE</div>
												</div>
												";
								}

							$RESULT .= "</div>
						</div>
					</div>";
				}
			}
				
		}
		return $RESULT;
	}
}

include("layout/bot_banner/bot_banner.php");
include("common/page.php");

$PAGE = new PageClass();

$BOTTOM_BANNER = new BottomBanner();

$SCORE = new scoreboard();

if(isset($_POST['rank-order-by']))
{
	$ORDER_BY_VAL = $_POST['rank-order-by'];	
}
else
{
	$ORDER_BY_VAL = "level";
}

?>



<html>
	<head>
		<link rel="stylesheet" type="text/css" href="style/scoreboard/style.css">
		<?php echo $PAGE->getHeaderInfo();?>
		<title>King of Mota | Ranks</title>
	</head>
	
	<?echo $SCORE->getTopMenuBanner()?>

	<body>
		<div id="scoreboard_list_view">
			<div id='scoreboard_view_title'>Scoreboard</div>
			<div id='scoreboard_view_title_sub'>Top 10</div>
			<form action="scoreboard.php" enctype="application/x-www-form-urlencoded" method="post">
				Order by:
				<select name="rank-order-by" onchange="this.form.submit()">
					<option name="level" value="level">Level</option>
					<option name="money" value="money">Money</option>
				</select>
			</form>
		</div>
		<div id="user_list">
			<?echo $SCORE->getScoreBoard(250, $ORDER_BY_VAL)?>
		</div>
		<div id="bottom_banner_full_view">
			<? echo $BOTTOM_BANNER->getBottomBanner();?>
		</div>
	</body>
</html>
