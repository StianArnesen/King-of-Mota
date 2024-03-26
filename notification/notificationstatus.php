<?php

    include_once("domainname.php");


    session_start();

    if(isset($_SESSION['game_username']))
    {
        $dbCon = mysqli_connect("localhost", "Stian Arnesen", "dynamicgaming", "motagamedata");

        if($dbCon)
        {
            $session_username = $_SESSION['game_username'];

            $sqlCommands = "SELECT id, username, money, level, current_exp, next_level_exp, profile_picture FROM users WHERE username='$session_username'";

            $query = mysqli_query($dbCon, $sqlCommands);
            $data_row = mysqli_fetch_row($query);

            $data_user_id = $data_row[0];
            $data_username = $data_row[1];
            $data_money = $data_row[2];
            $data_level = $data_row[3];
            $data_current_exp = $data_row[4];
            $data_next_level_exp = $data_row[5];
            $data_profile_picture = $data_row[6];

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