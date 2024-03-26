<?php 
    

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

            if($query)
            {
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
                die("Failed to validate user!");
            }
        }
        else
        {
            die("Failed to connect to database!");
            $LOGIN_ERR = 2;
        }
    }
    else
    {
        die("Session failed!");
    }

    function insertNotification($TOUSERID, $FROMUSERID){

        if(isset($TOUSERID) && isset($FROMUSERID))
        {
            $dbCon = mysqli_connect("localhost", "Stian Arnesen", "dynamicgaming", "motagamedata");

            $toUserID = $TOUSERID;
            $notType2 = 2;
            $notTime2 = time();

            $notQuery2 = "INSERT INTO notifications (user_id, not_type, user_id_a, user_id_b ,not_time) VALUES ('$toUserID', '$notType2', '$FROMUSERID', '$FROMUSERID', '$notTime2')";

            $doNotQuery = mysqli_query($dbCon, $notQuery2);

            if($doNotQuery)
            {
                return "Success!";
            }
            else
            {
                return "Failed to insert notification!";
            }
        }
        else
        {
            return "Failed to insert notification! Function parameters not valid";
        }
    }

    if(isset($_POST['GSPUD']) && isset($_POST['GPUUID'])) // Get status update data, Get post update user id.
    {
        if($dbCon)
        {
            $post_text_data_wall = strip_tags($_POST['GSPUD']); // Get status post update data

            if(strlen($post_text_data_wall) >= 3)
            {
                $status_from = $data_user_id;
                $status_to = $_POST['GPUUID'];

                $submitStatusQuery = "INSERT INTO wall_status (status_from_user_id, status_to_user_id, status_data) VALUES ('$status_from', '$status_to', '$post_text_data_wall')";
                
                $submitQuery = mysqli_query($dbCon, $submitStatusQuery);

                echo insertNotification($status_to, $data_user_id);
            }
            else
            {
                
            }
        }
        else
        {
            die("Connection failed!");
        }
    }
    else{
        
    }