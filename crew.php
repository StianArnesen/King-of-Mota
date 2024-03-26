<?php

	$NO_CREW = false;

	session_start();

	include("layout/bot_banner/bot_banner.php");

	$BOTTOM_BANNER = new BottomBanner();


	$dbCon = mysqli_connect("localhost", "Stian Arnesen", "dynamicgaming", "motagamedata");


	if(isset($_SESSION['game_username']))
    {
        if($dbCon)
        {						
            $session_username = $_SESSION['game_username'];

            $sqlCommands = "SELECT id, username, crew_id FROM users WHERE username='$session_username'";

            $query = mysqli_query($dbCon, $sqlCommands);
            $data_row = mysqli_fetch_row($query);

            $data_user_id = $data_row[0];
            $data_username = $data_row[1];
            $data_user_crew_id = $data_row[2];


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

	if(isset($_SESSION['game_username']) && ($_SESSION['game_username'] != "")){
		if(isset($_GET['crew_id'])){
			$crew_id = mysqli_real_escape_string($dbCon, $_GET['crew_id']);

			$sqlCommands = "SELECT * FROM crew WHERE crew_id='$crew_id'";



			$query = mysqli_query($dbCon, $sqlCommands);
            $data_row = mysqli_fetch_row($query);

				$data_crew_id = $data_row[0];

				$sqlCrewCommands = "SELECT * FROM crew WHERE crew_id='$crew_id'";

				$queryCrew = mysqli_query($dbCon, $sqlCrewCommands);
				$data_row_crew = mysqli_fetch_array($queryCrew);
		}
		else{
			$sqlCrewCommands = "SELECT * FROM crew WHERE crew_id='$data_user_crew_id'";

			$queryCrew = mysqli_query($dbCon, $sqlCrewCommands);
            $data_row_crew = mysqli_fetch_array($queryCrew);

			if($data_user_crew_id == -1)
			{
				$NO_CREW = true;
			}

			$crew_id = $data_row_crew[0];
		}

		if(isset($_POST["upload_image"]))
            {
                $imgId = uniqid();

                $target_dir = "img/uploads/";
                $filN = ($target_dir . $imgId);
                $target_file = $filN;
                $uploadOk = 1;
                $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
                // Check if image file is a actual image or fake image
                if(isset($_POST["upload_image"])) {
                    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
                    if($check !== false) {
                        echo "File is an image - " . $check["mime"] . ".";
                        $uploadOk = 1;
                    } else {
                        die("File is not an image.");
                        $uploadOk = 0;
                    }
                }
                // Check if file already exists
                if (file_exists($target_file)) {
                    die("Sorry, file already exists.");
                    $uploadOk = 0;
                }
                // Check file size
                if ($_FILES["fileToUpload"]["size"] > 500000) {
                    die("Sorry, your file is too large.");
                    $uploadOk = 0;
                }
                // Allow certain file formats
                if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                && $imageFileType != "gif" ) {
                    die("Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
                    $uploadOk = 0;
                }
                // Check if $uploadOk is set to 0 by an error
                if ($uploadOk == 0) {
                    die("Sorry, your file was not uploaded.");
                // if everything is ok, try to upload file
                } else {
                    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                        //die("The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.");

                        $full_dir = $target_file;

                        $update_data_query = "UPDATE crew SET crew_picture='$full_dir' WHERE crew_id='$crew_id'";
                        $run = mysqli_query($dbCon,$update_data_query);

                        if($run)
                        {

                        }
                        else
                        {
                            die("Failed to set crew picture!");
                        }

                    } else {
                        die("Sorry, there was an error uploading your file.");
                    }
                }
            }
	}else{
		die("You need to login first!");
	}
?>
<!DOCTYPE html>
<html>
	<head>
	    <link href="style/crew/style.css" rel="stylesheet" type="text/css">
		
		<meta charset="UTF-8">
		
		<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
		<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>

	    <title>King of Mota - My Crew</title>
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
			if(isset($crew_id) && ! $NO_CREW)
			{
				mysqli_set_charset($dbCon,'utf8');
				echo "<div id='crew_name'>";
					echo $data_row_crew[1];
				echo "</div>";

				echo "<div id='crew_image'>";
					echo "<img src='" . $data_row_crew[6] . "'>";
				echo "</div>";
				echo "<div id='crew_content'>";
				echo "<div id='crew_desc'>";
					echo "<span>" . $data_row_crew[2] . "</span>";
				echo "</div>";

				echo "<div id='crew_score'>";
					echo "Score: " . "<span>" . $data_row_crew[3] . "</span>";
				echo "</div>";
				echo "<div id='crew_money'>";
					echo "Money: " . "<span>" . $data_row_crew[4] . " $</span>";
				echo "</div> </div>";

				if($crew_id != $data_user_crew_id && $data_user_crew_id == -1)
				{
					echo "<div id='crew_join_form'>
							<input id='join_crew' type='button' value='Join'>
							<input type='hidden' id='crew_id' value='$crew_id'>
					</div>";
				}
				else if($data_user_crew_id == $crew_id)
				{
					echo "<div id='crew_join_form'>
							<input type='submit' id='leave_crew' value='Leave'>
					</div>";
				}

				$crew_leader_id = $data_row_crew['crew_leader'];

				if($data_user_id == $crew_leader_id)
                {
                    echo '<button id="change_crew_picture"> Change crew picture </button>';
                    echo '<form class="upload_form" action="crew.php" method="post" enctype="multipart/form-data">
                            Select image to upload:
                            <input type="file" name="fileToUpload" id="fileToUpload">
                            <input type="submit" value="Upload Image" name="upload_image">
                        </form>';
                }
			}
			else
			{
				if($NO_CREW)
				{
					echo "<h1>You are not a member of any crew.</h1>";
				}
				else
				{
					echo "This crew have been removed.";
				}

			}
		?>
    </div>

		<?php
			if(isset($crew_id))
			{

				echo "<div id='crew_members_view'>";
				echo "<div id='members_title'> <span> Members: </span> </div>";
				echo "<div id='crew_members_list'>";
					$crew_leader_id = $data_row_crew['crew_leader'];

					$getCrewLeader = "SELECT * FROM users WHERE id='$crew_leader_id' LIMIT 1";

					$doGetCrewLeader = mysqli_query($dbCon, $getCrewLeader);

					$CREW_LEADER = mysqli_fetch_array($doGetCrewLeader);

					echo "<div class='member_item'>
							<div class='member_username'> <span> <a href='". $CREW_LEADER['username'] ."'>". $CREW_LEADER['username'] . " </a> <img class='member_leader' src='img/icon/crew_king.png'> </span> </div>
							<div class='member_image'> <img src='". $CREW_LEADER['profile_picture'] . "'> </div>
						</div>";

					$sqlCommandsCrewList = "SELECT * FROM users WHERE crew_id='$crew_id'";

					$queryCrewList = mysqli_query($dbCon, $sqlCommandsCrewList);

					$crew_size = 0;
					while($data_row_crewList = mysqli_fetch_array($queryCrewList))
					{
						if($data_row_crewList['id'] != $crew_leader_id)
						{
							echo "<div class='member_item'>
								<div class='member_username'> <span> <a href='". $data_row_crewList['username'] ."'>". $data_row_crewList['username'] . " </a> </span> </div>
									<div class='member_image'> <img src='". $data_row_crewList['profile_picture'] ."'> </div>
							</div>";
						}
					}
				echo "</div> </div>";
			}
			else
			{

			}

		?>

</div>

</body>
</html>

<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>

<script>
	$("#change_crew_picture").click(function(){
                $(".upload_form").slideToggle(200);
            });
	$(document).ready(function(){

		$("#join_crew").click(function(){

			var c_id = $("#crew_id").val();

			joinCrew(c_id);
		});
		$("#leave_crew").click(function(){
			leaveCrew();
		});


		$("#memebers_title").click(function(){
			$("#crew_members_list").slideToggle(200);
		});

	});

	function joinCrew(CID)
	{
		$.post("/crew/crewFunction.php", {JOIN_CREW: CID}, function(RESULT)
		{
			console.log("Joining crew... RESPONSE: " + RESULT);
			
			location.href="/crew.php";
		});
	}
	function leaveCrew()
	{
		if(confirm("Are you sure you want to leave your current crew?"))
		{
			var D = 1;
			$.post("crew/crewFunction.php", {LEAVE_CREW: D}, function(RESULT)
			{
				location.reload();
			});

		}

	}

</script>
