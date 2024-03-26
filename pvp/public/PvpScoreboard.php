<?php


/**
 * Class PvpUserScoreboard
 */
class PvpUserScoreboard
{
    private $CONNECTION;

    private $USER;


    private $publicUserInfo;

    public function __construct()
    {
        if($this->_connect())
        {
            if($this->_loadPublicUserInfo())
            {

            }
        }
    }
    private function _loadPublicUserInfo()
    {
        if($this->publicUserInfo = new PublicUserInfo())
        {
            return true;
        }
        return false;
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return bool|mysqli_result|null
     */
    public function getPvpScoreBoardQueryArray($limit, $offset = 0)
    {
        $SQL = "SELECT * FROM attack_user_stats ORDER BY pvp_score DESC LIMIT $limit, $offset";

        if($QUERY = mysqli_query($this->CONNECTION, $SQL))
        {
            return $QUERY;
        }
        return null;
    }

    /**
     * @param $ACTIVE_USER_ID
     * @param $LIMIT
     * @param $OFFSET
     * @return string
     */
    public function getPublicScoreboard($ACTIVE_USER_ID, $limit, $offset)
    {
        // The final HTML for the scoreboard. Filled inside the scoreboard_loop.
        $html_final_scoreboard = "";

        $RANK = 0;

        $pvp_scoreboard_query = $this->getPvpScoreBoardQueryArray($limit, $offset);

        while($player = mysqli_fetch_array($pvp_scoreboard_query))
        {
            $RANK++;

            $USER_ID = $player['user_id'];

            $bool_player_current = $ACTIVE_USER_ID == $USER_ID; // Boolean: Is the current player item the current logged in player ?


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

            $html_final_scoreboard .=       "<div class='tab-item-main-image'> ";
            $html_final_scoreboard .=           "<img src='$USER_IMAGE'> ";
            $html_final_scoreboard .=       "</div>";

            if(! $bool_player_current)
            {
                $HTML_ONCLICK = "";
                $html_final_scoreboard .=       "<div class='attack-button' onclick='attackUser(\"$USER_ID\", \"" . str_replace('"', '\"', $USERNAME) . "\")'>Attack!</div>";
            }

            $html_final_scoreboard .=   "</div>";
            $html_final_scoreboard .= "</div>";
        }

        return $html_final_scoreboard;
    }
    private function _connect()
    {
        return true;
    }
}


