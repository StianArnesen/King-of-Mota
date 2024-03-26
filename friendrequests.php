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
?>
<?php
        $btn_file = fopen("layout/top_banner/top_banner.php", "r") or die("Unable to open file!");
        echo fread($btn_file,filesize("layout/top_banner/top_banner.php"));
        fclose($btn_file);
?>

<html>
    <head>
        <link href="style/friends/style.css" rel="stylesheet" type="text/css">
        <title>King of Mota - Friend requests</title>
    </head>

    <body>
        <div id='friend_list_view'>
            <div id='friend_list_title'>
               Friend requests:
            </div>
            <div id='friend_list_items'>
                
            </div>
        </div>
    </body>
</html>

<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>

<script>

$(document).ready(function(){

    var canUpdate = true;
    
    $("#friend_list_items").load("user_active_status/active.php?requesttype=0");
    var update = function()
    {
        if(canUpdate)
        {
            console.log("Update!");
            $("#friend_list_items").load("user_active_status/active.php?requesttype=0");    

            canUpdate = false;
        }
    };
    $("#friend_list_view").fadeIn(300);
    $(document).mousemove(update);

    setInterval(function(){
        canUpdate = true;

    }, 1000);

});

</script>