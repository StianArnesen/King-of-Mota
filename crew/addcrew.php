<?php
    session_start();

    $dbCon = mysqli_connect("localhost", "Stian Arnesen", "dynamicgaming", "motagamedata");

    if(isset($_SESSION['game_username']) && ($_SESSION['game_username'] != ""))
    {

    }
    else
    {
        header("Location: index.php");
    }
	
    if(isset($_SESSION['game_username']))
    {
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


    if(isset($_POST['INGCN']) && isset($_POST['INGCIMG']) && isset($_POST['INGCPRC']) && isset($_POST['INGCDESC']))
    {
        $crewName = strtolower(strip_tags($_POST['INGCN']));
        $crewImage = $_POST['INGCIMG'];
        $crewPrivacy = strtolower(strip_tags($_POST['INGCPRC']));
        $crewDecription = strtolower(strip_tags($_POST['INGCDESC']));


        $checkCrewName = "SELECT * FROM crew WHERE crew_name='$crewName'";
        $doCrewNameQuery = mysqli_query($dbCon, $checkCrewName);

        $crew_row = mysqli_fetch_row($doCrewNameQuery);

        if(isset($crew_row[0]))
        {
            echo "This crew name is already taken! <br> Please choose a different name for your crew.";
            exit;
        }
        if(strlen($crewName) >= 5)
        {
            $PRIVACY = 0;

            if($crewPrivacy == "private")
            {
                $PRIVACY = 1;
            }
            else if ($crewPrivacy == "invite only")
            {
                $PRIVACY = 2;
            }
            else
            {
                $PRIVACY = 0;
            }

            $insert_crew_query = "INSERT INTO crew (crew_name, crew_description, crew_privacy, crew_leader) VALUES ('$crewName', '$crewDecription', '$PRIVACY', '$data_user_id')";
            $insertCrew = mysqli_query($dbCon, $insert_crew_query);

            $getCrewIdQ = "SELECT crew_id FROM crew WHERE crew_name='$crewName'";
            $doCrewId = mysqli_query($dbCon, $getCrewIdQ);
            $crew_iddd = mysqli_fetch_row($doCrewId);

            $updateUserCrew = "UPDATE users SET crew_id='$crew_iddd[0]' WHERE id='$data_user_id'";
            $updateUserQuery = mysqli_query($dbCon, $updateUserCrew);
            if(mysqli_error($dbCon))
            {
                echo "Mysql responded with error: " . mysqli_error($dbCon);
            }
            else
            {
                echo "Crew was successfully created!";
            }
        }
    }
    else
    {
        echo "Failed to create crew!";
    }

?>