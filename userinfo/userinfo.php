<?php
    require_once ("../connect/database.php");
    require_once ("../common/session/sessioninfo.php");

    $USER = new User();

    if($USER->isLoggedIn())
    {
        $data_user_id           = $USER->getUserId();
        $data_username          = $USER->getUsername();
        $data_money             = $USER->getMoney();
        $data_level             = $USER->getLevel();
        $data_current_exp       = $USER->getExp();
        $data_next_level_exp    = $USER->getNeededExp();
        $data_profile_picture   = $USER->getUserImage();
        $user_access_level      = $USER->getUserAccessLevel();
        $g_coins                = $USER->getCoins();
        
        if(file_exists(__DIR__ . "..2/" . $data_profile_picture))
        {
            //
        }
        else
        {
            //$data_profile_picture = "img/0.jpg";
            
        }

        $RESULT = array(
            'username'      => $data_username,
            'money'         => $data_money,
            'level'         => $data_level,
            'img'           => $data_profile_picture,
            'exp_cur'       => $data_current_exp,
            'exp_target'    => $data_next_level_exp,
            'access_level'  => $user_access_level,
            'g_coins'       => $g_coins
        );
        die(json_encode($RESULT, JSON_PRETTY_PRINT));

    }
    else
    {
        $RESULT = array(
            'login_status'    => "LOGIN_ERR",
            'username' => "LOGIN_ERR",
            'money' => "LOGIN_ERR",
            'level' => "LOGIN_ERR",
            'img' => "LOGIN_ERR",
            'exp_cur' => "LOGIN_ERR",
            'exp_target' => "LOGIN_ERR"
        );
        die(json_encode($RESULT, JSON_PRETTY_PRINT));
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
