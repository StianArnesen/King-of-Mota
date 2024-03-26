<?php

require("pvp/PvpController.php");
require("common/page.php");
require_once("common/session/sessioninfo.php");

require "user/PublicUserInfo.php";
require_once "utils/se_utils.php";                       // StaticEngine utils: Using getTimeFormatted() to convert unix timestamp to datetime.


/*
 * 1. $pageUtils: Initialize the page-utils for the top-banner and other globals. (style, javascript, etc...)
 *      1. getHeaderInfo();
 *      2. getTopBanner();
 *
 * 2. $pvpController: Create new instance of the PvpController class. Used to get:
 *   1. Users current attack info
 *   2. Levels
 *   3. Attack log.
 *
 * 3. $publicUserInfo: Create new instance of the PublicUserInfo class. Used to get all non-private user info.
 *
 * */
$pageUtils      = new PageClass();
$pvpController  = new PvpController();
$publicUserInfo = new PublicUserInfo();
$staticUtils    = new StaticUtils();


/*           Load the current user                       */
$USER           = new User(-1, -1);


//User info about pvp. Cointains info about player power_attack/power_defense and more.
$userPvpInfo    = $pvpController->getUserAttackInfoFromDatabase();
$pvp_userStats  = $pvpController->getUserStatsInfoFromDatabase();

$pvp_scoreboard_query = $pvpController->getPvpScoreBoardQueryArray();



if($pvp_userStats['num_attack'] > 0)
{
    $ATTACK_WIN_RATIO_CSS_WIDTH     = round((($pvp_userStats['num_attack_win'] / $pvp_userStats['num_attack']) * 100), 0, PHP_ROUND_HALF_ODD);

    $ATTACK_WIN_RATIO_CSS_WIDTH_STR = "width: " . $ATTACK_WIN_RATIO_CSS_WIDTH . "%;";
}
else
{
    $ATTACK_WIN_RATIO_CSS_WIDTH_STR = "width: 0;";
    $ATTACK_WIN_RATIO_CSS_WIDTH     = 0;
}



?>

<html>
    <head>
        <?php echo $pageUtils->getHeaderInfo();?>

        <link href="style/arena/arena.less" rel="stylesheet" type="text/css">
        <script src="script/pvp/pvpscript.js" type="text/javascript"  ></script>

        <title>King of Mota | Arena</title>

    </head>
    <body>
        <?php echo $pageUtils->getTopBanner();?>

        <div id="arena-view" class="main">
            <div class="page-title">PVP Arena</div>

            <div id="user-info-view" class="page-view">
                <div class="view-title">Overview</div>
                <div class="page-view-container">
                    <div class="upgrades-view">

                        <div class="upgrade-item" id="button-upgrade-offense">
                            <div class="upgrade-item-title">Offense</div>
                            <img class="upgrade-item-image global_item-image" src="img/pvp/icons/offence.png">
                            <div class="upgrade-item-value"><?echo "Level: <div id='upgrade_value_offense_level'>" . $userPvpInfo['power_attack'] ;?> </div> </div>
                            <div class="upgrade-item-price">
                                <div class="upgrade-item-price-value" id="upgrade_value_price_offense"> <?php echo $staticUtils::currencyFormat($pvpController->getOffenseLevelUpgradePrice()); ?> $</div>
                            </div>


                        </div>
                        <div class="upgrade-item" id="button-upgrade-defense">
                            <div class="upgrade-item-title">Defense</div>
                            <img class="upgrade-item-image global_item-image" src="img/pvp/icons/defence.png">
                            <div class="upgrade-item-value"><?echo "Level: <div id='upgrade_value_defense_level'>" . $userPvpInfo['power_defend'];?> </div> </div>
                            <div class="upgrade-item-price">
                                <div class="upgrade-item-price-value" id="upgrade_value_price_defense"> <?php echo $staticUtils::currencyFormat($pvpController->getDefenseLevelUpgradePrice()); ?> $</div>
                            </div>

                        </div>
                        <div class="upgrade-item" id="button-upgrade-loot">
                            <div class="upgrade-item-title">Loot</div>
                            <img class="upgrade-item-image global_item-image" src="img/pvp/icons/loot_icon.png">
                            <div class="upgrade-item-value"><?echo "Level: <div id='upgrade_value_loot_level'>" . $userPvpInfo['power_loot'];?> </div> </div>
                            <div class="upgrade-item-price">
                                <div class="upgrade-item-price-value" id="upgrade_value_price_loot"> <?php echo $staticUtils::currencyFormat($pvpController->getLootLevelUpgradePrice()); ?> $</div>
                            </div>

                        </div>

                        <div class="stats-view">

                            <div class="stats-progressbar-view">
                                <div class="stats-progressbar-container">
                                    <input type="hidden" class="target-width" value="<? echo $ATTACK_WIN_RATIO_CSS_WIDTH; ?>" />
                                </div>
                                <div class="progressbar-win-ratio-text"><?php echo $ATTACK_WIN_RATIO_CSS_WIDTH . " %";?></div>
                            </div>

                            <div class="stats-view-div">
                                <div class="stats-item"><div class='stats-item-text won'>Attacks won: </div> <div class='stats-green'><?echo $pvp_userStats['num_attack_win'];?> </div> </div>
                                <div class="stats-item"><div class='stats-item-text lost'>Attacks lost: </div> <div class='stats-red'><?echo $pvp_userStats['num_attack'] - $pvp_userStats['num_attack_win']; ?> </div> </div>
                            </div>

                            <div class="stats-view-div">
                                <div class="stats-item"><div class='stats-item-text'>Defends won: </div><div class='stats-green'><?echo $pvp_userStats['num_defend_win'];?> </div> </div>
                                <div class="stats-item"><div class='stats-item-text'>Defends lost: </div> <div class='stats-red'><?echo $pvp_userStats['num_defend'] - $pvp_userStats['num_defend_win'];?> </div> </div>
                            </div>

                            <div class="pvp-user-stats-score">
                                <div class="pvp-user-stats-score-text">PVP Score: </div>
                                    <img class="pvp-view-icon" src="/img/pvp/icons/pvp_score.png">
                                <div class="pvp-score"> <? echo  $pvp_userStats['pvp_score'];?> p </div>
                            </div>
                            <div class="stamina-view">
                                <div class="pvp-user-stats-stamina-text">PVP Stamina</div>
                                    <img class="pvp-view-icon" src="/img/pvp/icons/stamina_icon.png">
                                <div class="pvp-stamina"> <? echo  $userPvpInfo['stamina_current'] . " / " . $userPvpInfo['stamina_max'];?> </div>
                                <div class="pvp-user-stats-stamina-time">
                                    <?
                                    if($pvpController->getTimeLeftTillNextStaminaRefill() > 0)
                                    {
                                        echo  "Refill in: <div class='pvp-stamina'>" . StaticUtils::getFormattedTimeToCleanText(StaticUtils::getTimeFormatted($pvpController->getTimeLeftTillNextStaminaRefill())) . "</div>";
                                    }
                                    else
                                    {
                                        $pvpController->doStaminaRoutineCheck();
                                    }
                                    ?>
                                </div>
                            </div>





                        </div>

                    </div>
                </div>
            </div>

            <div class="page-view">
                <div class="view-title">Match history</div>
                <div class="page-view-container">
                    <div class="list-div-25-title">Match</div>
                    <div class="list-div-25-title">Score</div>
                    <div class="list-div-25-title">Profit</div>
                    <div id="match-history-list">

                        <?php

                        $match_history_query = $pvpController->getUserMatchHistoryQueryArray();

                        $html_result = "";      // The final HTML of the match-history. Printed after loop.

                        $n_total_matches = 0;   // The total number of matches shown (value increased in while-loop).


                        while($matchItem = mysqli_fetch_array($match_history_query))
                        {
                            //Total number of matches so far in loop.
                            $n_total_matches++;

                            //User ID
                            $user_id_attack     = $matchItem['user_id_attack'];
                            $user_id_defend     = $matchItem['user_id_defend'];

                            //Match winner user_id

                            $winner_user_id     = $matchItem['winner_user_id'];
                            $loser_user_id      = $matchItem['user_id_defend'];

                            $winner_score_add   = $matchItem['score_change_winner'];
                            $loser_score_add    = $matchItem['score_change_loser'];

                            $player_score_change_final      = "+" . $matchItem['score_change_winner'];

                            if($winner_user_id == $matchItem['user_id_defend'])
                            {
                                $loser_user_id              = $matchItem['user_id_attack'];
                                $player_score_change_final  = $matchItem['score_change_loser'];
                            }



                            // Boolean to check if the current user won this match.
                            $bool_match_won = $winner_user_id == $USER->getUserId();




                            //Attacker - user info
                            $publicUserInfo->loadUserInfoById($user_id_attack);
                            $USER_INFO_ATTACK   = $publicUserInfo->getUserInfo();

                            $username_attacker  = $USER_INFO_ATTACK['username'];
                            $image_attacker     = $USER_INFO_ATTACK['profile_picture'];


                            //Defender - user info
                            $publicUserInfo->loadUserInfoById($user_id_defend);
                            $USER_INFO_DEFEND   = $publicUserInfo->getUserInfo();
                            
                            $username_defender  = $USER_INFO_DEFEND['username'];
                            $image_defender     = $USER_INFO_DEFEND['profile_picture'];


                            //Misc Match info. [match timestamp]


                            $match_timestamp    = $matchItem['attack_time_start'];                                                                          //Integer timestamp (unix) from start of the attack
                            $match_time         = StaticUtils::getFormattedTimeToCleanText(StaticUtils::getTimeFormatted(time() - $match_timestamp));       // Clean text that shows the start time of the match


                            //Find the current user of the session between attacker and defender. Always display the current user first.

                            $match_title = "Defence <div class='match-item-username'><a href='/$username_attacker'>" . $username_attacker . "</a></div>";

                            if($user_id_attack == $USER->getUserId())   //Check if the session-user is the attacker (TRUE: The attacker is the current user).
                            {
                                $match_title    = "Attack  <div class='match-item-username'> <a href='/$username_defender'>" . $username_defender . "</a></div>";
                            }


                            $ATTACK_BUTTON_USER_ID = $winner_user_id;
                            if($bool_match_won)
                            {
                                $ATTACK_BUTTON_USER_ID = $loser_user_id;
                            }

                            //Add custom classes (style) to the 'match-item' based on the winner of the match.


                            $user_money_balance_change  = -$matchItem['winner_award_money'];
                            $css_match_item_extra_class = "match-lost";
                            $css_profit_extra_class     = "money-lost";

                            $text_match_result_status   = "LOST";
                            //Check if the user won the match, and add extra-classes based on this.
                            if($bool_match_won)
                            {
                                $text_match_result_status   = "WON";
                                $user_money_balance_change  = '+'. $matchItem['winner_award_money'];
                                $css_profit_extra_class     = "money-won";
                                $css_match_item_extra_class = "match-won";
                            }


                            //Populate and structure the match-info (html_match_info) - variable with html to the drop-down match-info.

                            $html_match_info  = "<div class='match-result-info'>";
                            $html_match_info .=      "<div class='match-result-profit $css_profit_extra_class'>Money: $user_money_balance_change $</div>";
                            $html_match_info .=      "<div class='match-result-profit $css_profit_extra_class'>PVP Score: $player_score_change_final</div>";
                            $html_match_info .=      "<div class='attack-button' onclick='attackUser(". $ATTACK_BUTTON_USER_ID . ", ". $username_defender .")'>Attack!</div>";
                            $html_match_info .= "</div>";


                            //Generate prepared HTML ready for output
                            $html_result    .= '<div class="match-item tab-item-title '.$css_match_item_extra_class.'">';
                            $html_result    .=     '<div class="match-item-title-view tab-container-invisible">';
                            $html_result    .=         '<div class="match-item-title list-div-25">  '. $match_title    .' </div>';
                            $html_result    .=         '<div class="match-item-result-text list-div-25 '.$css_profit_extra_class.'">  '. $user_money_balance_change    .' $</div>';
                            $html_result    .=         '<div class="match-item-result-text list-div-25 '.$css_profit_extra_class.'">  '. $player_score_change_final    .' p</div>';
                            $html_result    .=         '<div class="match-item-time">   '. $match_time     .'</div>';
                            $html_result    .=     '</div>';
                            $html_result    .=     '<div class="tab-item-container">'. $html_match_info .'</div>';
                            $html_result    .= '</div>';
                        }


                        //Output alternative text in cases where there are no matches to show.
                        if($n_total_matches > 0)
                        {
                            echo $html_result;
                        }
                        else
                        {
                            echo "<div class='match-item-empty'>Nothing to show...</div>";
                        }

                        ?>
                    </div>
                </div>
            </div>

            <div class="page-view">
                <div class="view-title">Leaderboard</div>
                <div class="page-view-container" id="pvp-scoreboard">
                    <div class="scoreboard-title">
                        <div class="list-div-15-title">Rank</div>
                        <div class="list-div-25-title">Username</div>
                        <div class="list-div-25-title">Score</div>
                    </div>
                    <?php

                    // The final HTML for the scoreboard. Filled inside the scoreboard_loop.
                    $html_final_scoreboard = "";

                    $RANK = 0;

                    while($player = mysqli_fetch_array($pvp_scoreboard_query))
                    {
                        $RANK++;

                        $USER_ID = $player['user_id'];

                        $bool_player_current = $USER->getUserId() == $USER_ID; // Boolean: Is the current player item the current logged in player ?


                        $css_username_extra_class = "";
                        if($bool_player_current)
                        {
                            $css_username_extra_class = "scoreboard-player-me";
                        }

                        //get user-info by id.
                        $publicUserInfo->loadUserInfoById($USER_ID);
                        $USER_INFO      = $publicUserInfo->getUserInfo();

                        $USER_IMAGE     = $USER_INFO['profile_picture'];

                        $USERNAME   = $USER_INFO['username'];
                        $PVP_SCORE  = $player['pvp_score'];

                        $html_final_scoreboard .= "<div class='scoreboard-player-item tab-item-title $css_username_extra_class'>";
                        $html_final_scoreboard .=   "<div class='scoreboard-player-item-rank list-div-15'>$RANK</div>";
                        $html_final_scoreboard .=   "<div class='scoreboard-player-item-username list-div-25'>$USERNAME</div>";
                        $html_final_scoreboard .=   "<div class='scoreboard-player-item-score list-div-25'>$PVP_SCORE p</div>";
                        $html_final_scoreboard .=   "<div class='tab-item-container'>";
                        if(! $bool_player_current)
                        {
                            $HTML_ONCLICK = "";
                            $html_final_scoreboard .=       "<div class='attack-button' onclick='attackUser(\"$USER_ID\", \"" . str_replace('"', '\"', $USERNAME) . "\")'>Attack!</div>";
                            
                        }

                        $html_final_scoreboard .=   "</div>";
                        $html_final_scoreboard .= "</div>";
                    }

                    echo $html_final_scoreboard;

                    ?>
                </div>
            </div>


        </div>

    </body>
</html>
