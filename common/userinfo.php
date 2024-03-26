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

            $data_user_id = $data_row[0];
            $data_username = $data_row[1];
            $data_money = $data_row[2];
            $data_level = $data_row[3];
            $data_current_exp = $data_row[4];
            $data_next_level_exp = $data_row[5];
            $data_profile_picture = $data_row[6];
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

    if(isset($_GET['get_info']))
    {
        $number = 1234.56;
        $english_format_number = number_format($number);
        $getValue = $_GET['get_info'];


        if($getValue == 0)
        {
            echo 'Money: <span class="item_price_label">' . number_format($data_money, 0, '.', ',') . '</span>$';
        }
        else if($getValue == 1)
        {
            echo 'Level: <span>' . $data_level . '</span>';
        }
        else if($getValue == 2)
        {
            echo '<div id="progressbar_level">
              <div id="level_progressbar" style="width:' . ($data_current_exp/$data_next_level_exp)*100 . '%"></div>
            </div>
            <div id="exp_status_text">EXP: <span id="exp_span_pre">' . $data_current_exp . '</span> / <span id="exp_span_after">'. $data_next_level_exp .'</span></div>';
        }

    }

function getNumeric($n)
{
    if($n>1000000000000) return round(($n/1000000000000),1).' t';
    else if($n>1000000000) return round(($n/1000000000),1).' b';
    else if($n>1000000) return round(($n/1000000),1).' m';
    else if($n>1000) return round(($n/1000),1).' k';

    return number_format($n);
}


?>
