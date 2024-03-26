<?php
    session_start();

    if(isset($_SESSION['game_username']) && ($_SESSION['game_username'] != ""))
    {

    }
    else
    {
        header("Location: index.php");
    }

    if(isset($_SESSION['game_username']))
    {
        $dbCon = mysqli_connect("localhost", "Stian Arnesen", "dynamicgaming", "motagamedata");

        if($dbCon)
        {
            $session_username = $_SESSION['game_username'];

            $sqlCommands = "SELECT id, username, password, money, level, profile_picture, current_exp, next_level_exp FROM users WHERE username='$session_username'";

            $query = mysqli_query($dbCon, $sqlCommands);
            $data_row = mysqli_fetch_row($query);

            $data_user_id = $data_row[0];
            $data_username = $data_row[1];
            $data_password = $data_row[2];
            $data_money = $data_row[3];
            $data_level = $data_row[4];
			$data_profile_picture = $data_row[5];
            $data_current_exp = $data_row[6];
            $data_next_level_exp = $data_row[7];


            $currentTime = time();

            $setLastActive_query = "UPDATE users SET last_active='$currentTime' WHERE id='$data_user_id'";
            $doLastActiveQuery = mysqli_query($dbCon,$setLastActive_query);
        }
        else
        {
            $LOGIN_ERR = 2;
        }
    }
    else
    {
        header("Location: index.php");
    }
?>

<html>
	<head>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    	<link href="style/newcrew/style.css" rel="stylesheet" type="text/css">
	</head>

	<?php
        $btn_file = fopen("layout/top_banner/top_banner.php", "r") or die("Unable to open file!");
        echo fread($btn_file,filesize("layout/top_banner/top_banner.php"));
        fclose($btn_file);
    ?>


	<body>
		<div id="crew_view">
			<div id="crew_view_title">
				<span>Start crew</span>
			</div>
			<div id="crew_view_desc">
				<div>
					<span> <span>Here you can create your own crew. The price to start a new crew is: 15,000 $<br><strong>NOTE: By creating a new crew, you will automatically leave your current crew, and moved to your new crew!</strong> </span> </span>
				</div>
			</div>

			<div id="new_crew_input">
				<div id="input_field">

					<input type"text" id="crew_input_name" placeholder="Crew name:"> <div id="name_msg"></div>
					<textarea type"text" id="crew_input_desc" placeholder="Crew description:"></textarea>  <div id="desc_msg"></div>
				</div>
				<span>Privacy: </span>
				<select id="crew_input_privacy">
					<option>Public</option>
					<option>Invite only</option>
					<option>Private</option>
				</select>

				<!--<div id="crew_image_upload">
                    <span>Crew image: </span>
                    <input type="file" name="crewImageFile" id="crewImageFile">
                    <input type="submit" value="Upload" name="submit" id="upload_image">
				</div>-->
			</div>
			<div id="create_crew_button">
				<?php
					if($data_money >= 15000)
					{
						echo '<button id="start_crew" onclick="newCrew();">Create crew</button>';
					}
					else
					{
						echo '<div id="start_crew">You need more money to start a crew</div>';
					}
				?>
			</div>
			<div id="create_crew_status"> </div>
		</div>

	</body>
</html>

<script>
	$(document).ready(function(){
		$("#crew_view").fadeIn(300);
		$("#profile_info_div").fadeIn(300);
	});


	$("#crew_input_name").change(function(){
		if($(this).val().length < 5)
		{
			$("#name_msg").html("Your crew name must contain atleast 5 characters");
		}
		else
		{
			validateName($(this).val());
		}
	});

	function validateName(ICNPST)
	{
		console.log("Sending!");
		$.post("checkcrewinfo.php", {ICN: ICNPST}, function(DATA)
		{
			$("#name_msg").html(DATA);
		});
	}

	var newCrew = function(){
		$("#start_crew").html("Loading...");


		var NAME = $("#crew_input_name").val();
		var DESC = $("#crew_input_desc").val();
		var IMAGE = "fag";
		var PRIVACY = $("#crew_input_privacy").val();

		$.post("crew/addcrew.php", {INGCN: NAME, INGCIMG: IMAGE, INGCPRC: PRIVACY, INGCDESC: DESC}, function(DATA)
		{
			$("#create_crew_status").html(DATA);
			console.log("Crew created");
		});
	}

</script>
