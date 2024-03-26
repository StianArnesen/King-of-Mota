<?php 
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


    if(isset($_POST['add_user_id']))
    {
        if($dbCon)
        {
            $addFriendId = ($_POST['add_user_id']);

            $checkFriendStatusQuery = "SELECT status FROM friend_list WHERE (friend_id='$addFriendId' AND user_id='$data_user_id') OR (friend_id='$data_user_id' AND user_id='$addFriendId')";
            $doCheckFriendStatus = mysqli_query($dbCon, $checkFriendStatusQuery);

            $FRIEND_STATUS = mysqli_fetch_row($doCheckFriendStatus);

            if(! isset($FRIEND_STATUS[0]))
            {                
                $addFriendQuery = "INSERT INTO friend_list (user_id, friend_id) VALUES ('$data_user_id', '$addFriendId')";
                $doAddFriendQuery = mysqli_query($dbCon, $addFriendQuery);

                $notification_type = 0;
                $notification_time = time();
                $addNotificationQ = "INSERT INTO notifications (user_id, not_type, user_id_a, not_time) VALUES ('$addFriendId', '$notification_type', '$data_user_id', '$notification_time')";
                mysqli_query($dbCon, $addNotificationQ);
            }
        }
    }

    if(isset($_POST['accept_friend_user_id']))
    {
        if($dbCon)
        {

            $updateFriendId = ($_POST['accept_friend_user_id']);
            
            $checkFriendStatusQuery = "SELECT status FROM friend_list WHERE (friend_id='$updateFriendId' AND user_id='$data_user_id') OR (friend_id='$data_user_id' AND user_id='$updateFriendId')";
            $doCheckFriendStatus = mysqli_query($dbCon, $checkFriendStatusQuery);

            $FRIEND_STATUS = mysqli_fetch_row($doCheckFriendStatus);

            if(isset($FRIEND_STATUS[0]))
            {
                if($FRIEND_STATUS[0] == 2)
                {
                    $notification_type2 = 1; // Friend request accepted.
                    $notification_time2 = time();
                    $addNotificationQ2 = "INSERT INTO notifications (user_id, not_type, user_id_b, not_time) VALUES ('$updateFriendId', '$notification_type2', '$data_user_id', '$notification_time2')";
                    mysqli_query($dbCon, $addNotificationQ2);

                    $updateFriendQuery = "UPDATE friend_list SET status='1' WHERE (user_id='$updateFriendId' AND friend_id='$data_user_id')";
                    $doUpdateFriendQuery = mysqli_query($dbCon, $updateFriendQuery);
                }
            }
        }
    }

?>


<?php
        $btn_file = fopen("layout/top_banner/top_banner.php", "r") or die("Unable to open file!");
        echo fread($btn_file,filesize("layout/top_banner/top_banner.php"));
        fclose($btn_file);
?>


<html>
    <head>
        <link href="style/addfriend/style.css" rel="stylesheet" type="text/css">
        <title>King of Mota - Add friend</title>
    </head>

    <body>
        <div id="add_friend_view">
            <div id="add_friend_content">
                <h1>Add friends by username:</h1>
                <div id="search_bar">
                    <form id="search_form" method="post" action="addfriend.php">
                        <input type="text" name="friend_username" placeholder="Username:">
                        <input type="submit" value="Search">
                    </form>
                </div>
            </div>
            <div id="search_result">
                <?php
                    if(isset($_POST['friend_username']))
                    {
                        $SEND_REQUEST_TO_USERNAME = strip_tags(($_POST['friend_username']));
                    

                    $name_length = strlen($SEND_REQUEST_TO_USERNAME);

                    if(isset($SEND_REQUEST_TO_USERNAME) && ($name_length >= 4))
                    {
                        if($dbCon)
                        {
                            $userInfoQuery = "SELECT id, username, profile_picture FROM users WHERE (username LIKE '%$SEND_REQUEST_TO_USERNAME%')";
                            $doUserInfoQuery = mysqli_query($dbCon,$userInfoQuery);
                            
                            $result_amount = 0;
                            while($userInfoRow = mysqli_fetch_row($doUserInfoQuery))
                            {
                                if($userInfoRow[0] == $data_user_id)
                                {
                                    break;
                                }
                                $getFriendListQuery = "SELECT status, user_id FROM friend_list WHERE (friend_id='$userInfoRow[0]' AND user_id='$data_user_id') OR (friend_id='$data_user_id' AND user_id='$userInfoRow[0]')";
                                $doFriendListQuery = mysqli_query($dbCon, $getFriendListQuery);
                                
                                $friendStatus = -1;
                                
                                while($friendListItem = mysqli_fetch_row($doFriendListQuery))
                                {
                                    if($friendListItem[0] == 1)
                                    {
                                        $friendStatus = 1;
                                    }
                                    else if($friendListItem[0] == 2)
                                    {
                                        if($friendListItem[1] == $data_user_id)
                                        {
                                            $friendStatus = 2;
                                        }
                                        else if($friendListItem[1] != $data_user_id)
                                        {
                                            $friendStatus = 3;    
                                        }
                                    }
                                    else if($friendListItem[0] == 2)
                                    {
                                        if($friendListItem[1] != $data_user_id)
                                        {
                                            $friendStatus = 3;    
                                        }
                                    }
                                }
                                $result_amount++;
                                if($friendStatus == -1){
                                    echo "
                                    <div class='search_result_item'>
                                        <div class='user_username'>
                                            <a href=". $userInfoRow[1].">". $userInfoRow[1] . "</a>
                                        </div>
                                        <div class='user_image'>
                                            <img src='". $userInfoRow[2] ."'>
                                        </div>
                                        <div class='user_add_friend'>
                                            <form method='post' action='addfriend.php'>
                                                <input type='hidden' name='add_user_id' value='$userInfoRow[0]'>
                                                <input type='submit' value='add friend'>
                                            </form>
                                        </div>
                                    </div>

                                    ";    
                                }
                                else if($friendStatus == 1)
                                {
                                    echo "<div class='search_result_item'>
                                        <div class='user_username'>
                                            <a href=". $userInfoRow[1].">". $userInfoRow[1] . "</a>
                                        </div>
                                        <div class='user_image'>
                                            <img src='". $userInfoRow[2] ."'>
                                        </div>
                                        <div class='user_add_friend'>
                                            <form method='post' action='addfriend.php'>
                                                <input type='submit' class='btn-status-active' disabled value='Friends'>
                                            </form>
                                        </div>
                                    </div>

                                    ";  
                                }
                                else if($friendStatus == 2)
                                {
                                    echo "<div class='search_result_item'>
                                        <div class='user_username'>
                                            <a href=". $userInfoRow[1].">". $userInfoRow[1] . "</a>
                                        </div>
                                        <div class='user_image'>
                                            <img src='". $userInfoRow[2] ."'>
                                        </div>
                                        <div class='user_add_friend'>
                                            <form method='post' action='addfriend.php'>
                                                <input type='submit' disabled class='btn-status-waiting' value='Request sent'>
                                            </form>
                                        </div>
                                    </div>

                                    ";  
                                }
                                else if($friendStatus == 3)
                                {
                                    echo "<div class='search_result_item'>
                                        <div class='user_username'>
                                            <a href=". $userInfoRow[1].">". $userInfoRow[1] . "</a>
                                        </div>
                                        <div class='user_image'>
                                            <img src='". $userInfoRow[2] ."'>
                                        </div>
                                        <div class='user_add_friend'>
                                            <form method='post' action='addfriend.php'>
                                                <input type='hidden' name='accept_friend_user_id' value='$userInfoRow[0]'>
                                                <input type='submit' class='btn-status-active'  value='Accept friend request'>
                                            </form>
                                        </div>
                                    </div>

                                    ";  
                                }
                                

                                //$friend_user_id = $

                                //$checkFriendListCommands = "SELECT id FROM friend_list WHERE user_id='$friend_user_id'"; 
                            }
                            if($result_amount == 0)
                            {
                                echo "<h2>No results</h2>";
                            }
                        }
                        else
                        {
                            die("Connection failed!");
                        }
                    }
                    else if(isset($_POST['friend_username']))
                    {
                        echo "<h2>Search must be atleast 4 characters</h2>";
                    }
                    else
                    {
                        echo "<h2>Search results will appear here</h2>";
                    }
                }
                else
                {
                    echo "<h2>Search results will appear here</h2>";
                }
                ?>
            </div>
        </div>
    </body>
</html>

