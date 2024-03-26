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


    if(isset($_POST['accept_friend_user_id']))
    {
        $dbConUpdateFriend = mysql_connect("localhost", "Stian Arnesen", "dynamicgaming", "motagamedata");
        if($dbConUpdateFriend)
        {
            mysql_select_db("motagamedata");
            $updateFriendId = ($_POST['accept_friend_user_id']);

            $updateFriendQuery = "UPDATE friend_list SET status='1' WHERE user_id='$updateFriendId'";
            $doUpdateFriendQuery = mysql_query($updateFriendQuery);    
        }
    }

    
    require "common/page.php";
    
    $PAGE = new PageClass();


?>
<html>
    <head>
        <link href="style/friends/style.css" rel="stylesheet" type="text/css">
        <?php echo $PAGE->getHeaderInfo() ?>
        <title>King of Mota - Friends</title>
    </head>
    
    <body>
    <?php echo $PAGE->getTopBanner() ?>
        <div id='friend_list_view'>
            <div id='friend_list_title'>
               My friends:
            </div>
            <div id='friend_list_items'>
                
            </div>
        </div>
        
    </body>
</html>

<script>

$(document).ready(function(){
	$("#friend_list_items").load("user_active_status/active.php");
	$("#friend_list_view").fadeIn(300);
    console.log("UPDATE DISABLED!");
	
	/*var canUpdate = true;
    
    $("#friend_list_items").load("user_active_status/active.php");
    var update = function()
    {
        if(canUpdate)
        {
            console.log("Update!");
            $("#friend_list_items").load("user_active_status/active.php");    

            canUpdate = false;
        }
    };

    $("#friend_list_view").fadeIn(300);

    $(document).mousemove(update);

    setInterval(function(){
        canUpdate = true;

    }, 1000);*/

});

</script>