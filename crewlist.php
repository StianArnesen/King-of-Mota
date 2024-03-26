<?php
	session_start();

	include("layout/bot_banner/bot_banner.php");
	include("crew/warInfo.php");

	$BOTTOM_BANNER = new BottomBanner();


	$WAR_INFO = new WarInfo();

	if(isset($_SESSION['game_username']) && ($_SESSION['game_username'] != ""))
	{

	}
	else
	{
		die("You need to login first!");
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<link href="style/crewlist/style.css" rel="stylesheet" type="text/css">
		<title>King of Mota - Crew list</title>
	</head>

	<body>
		<?php
            $btn_file = fopen("layout/top_banner/top_banner.php", "r") or die("Unable to open file!");
                echo fread($btn_file,filesize("layout/top_banner/top_banner.php"));
                fclose($btn_file);
        ?>

<div id="crew">
	
    <div id="crew_view">
		<?php
			//Find all users in this crew;

			$session_user_id = $_SESSION['game_user_id'];

			$dbCon = mysqli_connect("localhost", "Stian Arnesen", "dynamicgaming", "motagamedata");

			$sqlCommands = "SELECT crew_id FROM users WHERE id='$session_user_id'";

			$query = mysqli_query($dbCon, $sqlCommands);
            $data_row = mysqli_fetch_row($query);

			$data_crew_id = $data_row[0];

			$sqlCrewCommands = "SELECT * FROM crew ORDER BY crew_score DESC";

			$queryCrew = mysqli_query($dbCon, $sqlCrewCommands);

			mysqli_set_charset($dbCon,'utf8');

			$form_id = 0;
			$crew_rank = 1;
			while($data_row_crew = mysqli_fetch_array($queryCrew))
			{
				$crew_id = $data_row_crew[0];

				$sqlCommandsCrewList = "SELECT * FROM users WHERE crew_id='$crew_id'";

				$queryCrewList = mysqli_query($dbCon, $sqlCommandsCrewList);

				$crew_size = 0;
				while($data_row_crewList = mysqli_fetch_array($queryCrewList))
				{
					$crew_size++;
				}

				echo "<div class='crew_item'  onclick='showCrew($crew_id)''>";

						echo "<div class='crew_name'>";
							echo $data_row_crew[1];
						echo "</div>";


						echo "<div class='crew_image'>";
							echo "<img src='" . $data_row_crew[6] . "'>";
						echo "</div>";



						echo "<div class='crew_info_field'>";

								echo "<div class='crew_desc'>";
									echo "$data_row_crew[1]: <br>" . "<span>" . $data_row_crew[2] . "</span>";
								echo "</div> <br>";

								echo "<div class='crew_score'>";
									echo "Score: " . "<span>" . $data_row_crew[3] . "</span>";
								echo "</div>";
								echo "<div class='crew_money'>";
									echo "Money: " . "<span>" . $data_row_crew[4] . " $</span>";
								echo "</div>";
								echo "<div class='crew_members_amount'>";
									echo "Members: " . "<span>" . $crew_size  . "</span>";

							echo "</div>";

						echo "</div>";

				echo "</div>";
				$form_id++;
				$crew_rank++;
			}
		?>
    </div>

</div>

		<link href="context_menu/style.css" rel="stylesheet">

<div id="context_menu">
	<ul>
		<li><a href="#">Attack</a></li>
		<li><a href="#">Show crew</a></li>
		<li><a href="#">Send message</a></li>
	</ul>
</div>

	<div id="bottom_banner_full_view">
		<? echo $BOTTOM_BANNER->getBottomBanner();?>
	</div>

</body>
</html>

<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<script>
$(document).ready(function(){

    $("#crew_view").fadeIn(200);
});


document.addEventListener( "contextmenu", function(e) {
	console.log("Context menu!");
	e.preventDefault();
	showContextMenu();
});

var mouseX = 0;
var mouseY = 0;

$(document).mousemove(function(event){
		mouseX = event.pageX;
		mouseY = event.pageY;
});


	var context_showing = false;

$(document).click(function(){
	if(context_showing)
	{
		hideContextMenu();
	}
});

	function showCrew(id)
	{
		window.location.href = 'crew.php?crew_id=' + id;
	}

	function showContextMenu()
	{
		$("#context_menu").slideDown(100);
		$("#context_menu").offset({left: mouseX, top: mouseY});

		context_showing = true;
	}
	function hideContextMenu()
	{
		if(context_showing){
			context_showing = false;
			$("#context_menu").slideUp(100);
		}
	}

</script>
