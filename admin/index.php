<?php

require 'utils.php';
require("../connect/database.php");

$connection = Database::getConnection();

global $connection;

if(! isset($_SESSION)){
    session_start();
}



$UTIL = new AdminUtils();

$VALID_UNAME = "admin";
$VALID_PASS = "HomoFagget";



$LOGGED_IN = false;

if(isset($_SESSION['admin_username'])){
    $session_uname = $_SESSION['admin_username'];
    $session_pass = $_SESSION['admin_pass'];    
    if($session_uname == $VALID_UNAME){
        if($session_pass == $VALID_PASS){
            $LOGGED_IN = true;
        }
        else{
            $LOGGED_IN = false;
        }
    }
}
else if(isset($_POST['username']) && isset($_POST['password'])){

    $uname = $_POST['username'];

    $pass = $_POST['password'];

    if($uname == "admin"){
        if($pass == "HomoFagget"){
            $_SESSION['admin_username'] = "admin";
            $_SESSION['admin_pass'] = "HomoFagget";

            $LOGGED_IN = true;
        }
        else
        {
            $LOGGED_IN = false;
        }
    }
    else
    {
            $LOGGED_IN = false;
    }
}

if($LOGGED_IN)
{
    if(isset($_POST['reset_uid']))
    {
        $UID = strip_tags($_POST['reset_uid']);
        die($UTIL->resetUser($UID));
    }
}

function getUserListQuery($LIMIT, $userRDER){

    $connectione = Database::getConnection();

    $SQL = "SELECT * FROM users ORDER BY $userRDER LIMIT $LIMIT";

    if($QUERY = mysqli_query($connectione, $SQL)){
        return $QUERY;
    }
}

function secondsToTime($seconds) 
    {
        $t = round($seconds);
        $t_new = sprintf('%02d:%02d:%02d', ($t/3600),($t/60%60), $t%60);

        $str_time = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $t_new);

        sscanf($t_new, "%d:%d:%d", $hours, $minutes, $seconds);

        $h = ($hours > 0)? $hours . "h ": "";
        $m = ($minutes > 0)? $minutes . "m ": "";
        $s = ($seconds > 0)? $seconds . "s ": "";

        $time_seconds = $h . $m . $s;

      return $time_seconds;
    }


?>


<html>
    <head>
        <!--        HTML HEAD       -->
    </head>

    <body style="background-color: rgba(60,60,60,1)">
        <!--        HTML BODY       -->

        <?php

        if(! $LOGGED_IN){
            echo '<form class="login-form" method="post" action="index.php">
                <input type="text" name="username" >
                <input type="password" name="password" >

                <input type="submit" name="submit" >
             </form>';
        }
        else{
                        
            if(! isset($_POST['max_result'])){
                $MAX_RESULTS = 150;
                $userRDER_BY = "last_active DESC";
            }
            else
            {
                $MAX_RESULTS    = $_POST['max_result'];
                $userRDER_BY       = $_POST['order_by'];
            }

            echo "<form method='post' action='index.php'>
                <input type='number' name='max_result' value='$MAX_RESULTS'>
                <input type='text' name='order_by' value='$userRDER_BY'>

                <input type='submit' name='submit_data'>
            </form>";

            $userListQuery = getUserListQuery($MAX_RESULTS, $userRDER_BY);

            $RESULT_NUM = 1;
            
            require "../utils/se_utils.php";
            
            while($user = mysqli_fetch_array($userListQuery))
            {
                $ID         = $user['id'];
                $name       = $user['username'];
                $level      = $user['level'];
                $lastActive = StaticUtils::getTimeFormatted(time() - $user['last_active']);
                
                $date       = StaticUtils::getFormattedTimeToCleanText($lastActive);

                echo "<span style='color: white; background-color: red; padding: 5px;'> $RESULT_NUM </span>";

               
                
                echo "<div class='user-item' style='height: 100px; background-color: black; color: white; margin-bottom: 15px; padding: 5px'>
                    <div class='username'>$name</div>
                    <div class='level'>Level: $level</div>
                    <div class='last-active'>$date ago</div>
                    <input type='button' onclick='resetUser($ID)' name='reset_uid' value='$ID'>
                </div>";

                $RESULT_NUM++;
            }
        }
        ?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    
    <script type="text/javascript">
        function resetUser(id)
        {
            if(confirm("Reset user?"))
            {
                $.post("index.php", {reset_uid: id}, function(data){
                    alert("Reset user, result: \n" + data);
                });
            }
        }
    </script>
    
    </body>

</html>


