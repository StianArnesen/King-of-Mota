<?php


    function calcTime($TIME){
        
        $TMP_SEC = (time() - $TIME);
        $TMP_MIN = 0;
        $TMP_HOUR = 0;
        $TMP_DAYS = 0;

        $MIN;
        $HOUR;
        $DAYS;
        
        $FINAL_TIME;


        if($TMP_SEC > 60)
        {
            while($TMP_SEC >= 60)
            {
                $TMP_SEC-=60;
                $TMP_MIN++;
            }
            $MIN = $TMP_MIN;
            if($MIN > 1)
            {
                $FINAL_TIME = $MIN . " minutes";
            }
            else
            {
                $FINAL_TIME = $MIN . " minute";
            }

            if($TMP_MIN > 60)
            {
                while($TMP_MIN >= 60)
                {
                    $TMP_MIN-=60;
                    $TMP_HOUR++;
                }
                $HOUR = $TMP_HOUR;
                if($HOUR > 1)
                {
                    $FINAL_TIME = $HOUR . " hours";
                }
                else
                {
                    $FINAL_TIME = $HOUR . " hour";
                }
                
                if($TMP_HOUR > 24)
                {
                    while($TMP_HOUR >= 24)
                    {
                        $TMP_HOUR-=24;
                        $TMP_DAYS++;
                    }
                    $DAYS = $TMP_DAYS;
                    if($DAYS > 1)
                    {
                        $FINAL_TIME = $DAYS . " days";
                    }
                    else
                    {
                        $FINAL_TIME = $DAYS . " day";
                    }
                }
            }
        }
        else
        {
            $FINAL_TIME = "Less than one minute";
        }

        $FINAL_TIME .= " ago";
        return $FINAL_TIME;
    }



    session_start();
    
    $dbCon = mysqli_connect("localhost", "Stian Arnesen", "dynamicgaming", "motagamedata");

    if(isset($_SESSION['game_username']))
    {
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

    if($dbCon)
    {
        if(isset($_GET['requesttype']))
        {
            $request_type = $_GET['requesttype'];
        }

        if(isset($request_type) && $request_type == 0)
        {
            $getFriendListQuery = "SELECT * FROM friend_list WHERE (friend_id='$data_user_id' AND status='2') ";
        }
        else
        {
            $getFriendListQuery = "SELECT * FROM friend_list WHERE (user_id='$data_user_id' AND status='1') OR (friend_id='$data_user_id' AND status='1')";
        }
        
        $doGetFriendList = mysqli_query($dbCon, $getFriendListQuery);
        
        $online_status_bool = -1;

        $result_amount = 0;
        while($FRIEND = mysqli_fetch_row($doGetFriendList))
        {
            if($FRIEND[1] == $data_user_id)
            {
                $FRIEND_ID = $FRIEND[2];
            }
            else
            {
                $FRIEND_ID = $FRIEND[1];
            }
            $result_amount++;
            $getFriendUserInfo = "SELECT id, username, level, profile_picture, last_active FROM users WHERE id='$FRIEND_ID'";

            $doGetFriendUserInfo = mysqli_query($dbCon,$getFriendUserInfo);

            $USER_FRIEND = mysqli_fetch_row($doGetFriendUserInfo);

            $lastActiveTime = $USER_FRIEND[4];

            $online_status_bool = -1;

            $lastActiveTMP_seconds = (time() - $USER_FRIEND[4]);

            if($lastActiveTMP_seconds <= 200)
            {
                $online_status_bool = 1;
            }
            else
            {
                $online_status_bool = -1;
            }
            echo 
            "<div class='friend_item'>

                <div class='friend_item_username'>
                    <a href='". $USER_FRIEND[1] ."'>". $USER_FRIEND[1] . "</a>
                </div>

                <div class='friend_item_img'>
                    <img src='". $USER_FRIEND[3] . "'>
                </div>
                <div class='friend_info'>
                        Level: ". $USER_FRIEND[2] . "
                </div>
                <div class='online_status' id='status_usr_id_". $USER_FRIEND[0] ."'>";

                if($online_status_bool == 1)
                {
                    echo "<span style='background-color: rgba(100,250,100,0.3);'>Online</span>";
                }
                else
                {
                    echo "<span style='background-color: rgba(200,100,100,0.5);'>Offline</span>";
                }
                
                echo "
                </div>";
                if($online_status_bool != 1)
                {
                    echo "<span style='width: 500px; margin-left: 100px;'>Active ". calcTime($lastActiveTime) ."</span><br>";
                }
               

                if(isset($request_type) && $request_type == 0)
                {
                    echo "
                    <div class='user_add_friend'>
                        <form method='post' action='addfriend.php'>
                            <input type='hidden' id='accept_friend_user_id' name='accept_friend_user_id' value='$FRIEND_ID'>
                            <input type='submit' value='Accept'>
                        </form>
                     </div>";
                }

            echo "</div>";
        }
        if($result_amount == 0)
        {
            if(! isset($request_type))
            {
                echo "<h2>You dont have any friends</h2>";
            }
            else
            {
                echo "<h2>No pending requests</h2>";    
            }
            
        }
    }
    else
    {
        echo "<div id='fetch_error'>";
            echo "Failed to fetch friend list!";
        echo "</div>";
    }
?>