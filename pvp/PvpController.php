<?php

if(isset($_POST['ATTACK_USER_ID']))
{
    $USER_ID = $_POST['ATTACK_USER_ID'];

    $PVP = new PvpController();
    die(json_encode($PVP->startNewAttackOnUser($USER_ID), JSON_PRETTY_PRINT));
}
else if(isset($_GET['INIT_ATTACK_TABLES']))
{
    $PVP = new PvpController();
    $PVP->initAllNeededTablesToDatabase();
}
else if(isset($_GET['RUN_STAMINA_ROUTINE_CHECK']))
{
    $PVP = new PvpController();
    if($PVP->doStaminaRoutineCheck())
    {
        die("STATUS => OK");
    }
    die("STATUS => FAILED");
}
else if(isset($_POST['UPGRADE_OPTION']))
{
    $RESULT = false;

    if($_POST['UPGRADE_OPTION'] == "UPGRADE_LEVEL_OFFENSE")
    {
        $PVP    = new PvpController();

        $RESULT = json_encode( $PVP->upgradeOffenseLevel(), JSON_PRETTY_PRINT );
    }
    else if($_POST['UPGRADE_OPTION'] == "UPGRADE_LEVEL_DEFENSE")
    {
        $PVP    = new PvpController();

        $RESULT = json_encode( $PVP->upgradeDefenseLevel(), JSON_PRETTY_PRINT );
    }
    else if($_POST['UPGRADE_OPTION'] == "UPGRADE_LEVEL_LOOT")
    {
        $PVP    = new PvpController();

        $RESULT = json_encode( $PVP->upgradeLootLevel(), JSON_PRETTY_PRINT );
    }
    die($RESULT);
}


class PvpController
{
    /**
     * @var mysqli
     */
    private $CONNECTION;


    /**
     * @var User
     */
    private $USER;

    public function __construct()
    {
        if($this->_connect())
        {
            if($this->_loadUser())
            {

            }
            else
            {
                die("PvpController: User failed to load!");
            }
        }
        else
        {
            die("PvpController: Connection failed!");
        }
    }


    /**
     * getUserAttackInfoFromDatabase()
     * - - - - - - - -
    *   @access public
    *   @param void
    *   @return array: [id, user_id, stamina_current, stamina_max, stamina_refill_time, power_attack, power_defend];
    * */
    public function getUserAttackInfoFromDatabase($USER_ID = null)
    {
        if(is_null($USER_ID))
        {
            $USER_ID = $this->USER->getUserId();
        }

        $SQL = "SELECT * FROM attack_user_info WHERE user_id='$USER_ID' LIMIT 1";

        if($QUERY = mysqli_query($this->CONNECTION, $SQL))
        {
            if($RESULT = mysqli_fetch_array($QUERY))
            {
                if($RESULT[0] != null)
                {
                    return $RESULT;
                }
            }
            else
            {
                if($this->insertPvpTablesForCurrentUser($USER_ID))
                {
                    return $this->getUserStatsInfoFromDatabase($USER_ID);
                }
            }
        }
        return null;
    }
    public function insertPvpTablesForCurrentUser($USER_ID = null)
    {
        if(is_null($USER_ID))
        {
            $USER_ID = $this->USER->getUserId();
        }

        $SQL_ATTACK_USER_INFO   = "INSERT INTO attack_user_info (user_id) VALUES ($USER_ID)";
        $SQL_ATTACK_USER_STATS  = "INSERT INTO attack_user_stats (user_id) VALUES ($USER_ID)";

        if($QUERY = mysqli_query($this->CONNECTION, $SQL_ATTACK_USER_INFO))
        {
            if($QUERY = mysqli_query($this->CONNECTION, $SQL_ATTACK_USER_STATS))
            {

            }
            return true;
        }
        return false;
    }

    /*      Get upgrade-prices
     *
     *          1.  getOffenseLevelUpgradePrice(offense_level: void)
     *              - Arg:  Current offense level. || VOID
     *              - Returns the price to the next level offense
     *
     *          2.  getDefenseLevelUpgradePrice(defense_level: void)
     *              - Arg:  Current defense level. || VOID
     *              - Returns the price to the next level offense
     *
     * */

    //
    public function getOffenseLevelUpgradePrice($ATTACK_LEVEL = null)
    {
        if(! isset($ATTACK_LEVEL) || $ATTACK_LEVEL == null)
        {
            $array_attack_info = $this->getUserAttackInfoFromDatabase();

            $ATTACK_LEVEL = $array_attack_info['power_attack'];
        }

        return round( (400 * (pow(2.3, $ATTACK_LEVEL)) ), 0, PHP_ROUND_HALF_UP);
    }
    public function getDefenseLevelUpgradePrice($DEFENSE_LEVEL = null)
    {
        if(! isset($DEFENSE_LEVEL) || $DEFENSE_LEVEL == null)
        {
            $array_attack_info = $this->getUserAttackInfoFromDatabase();

            $DEFENSE_LEVEL = $array_attack_info['power_defend'];
        }

        return round( (300 * (pow(2.4, $DEFENSE_LEVEL)) ), 0, PHP_ROUND_HALF_UP);
    }
    public function getLootLevelUpgradePrice($LOOT_LEVEL = null)
    {
        if(! isset($LOOT_LEVEL) || $LOOT_LEVEL == null)
        {
            $array_attack_info = $this->getUserAttackInfoFromDatabase();

            $LOOT_LEVEL = $array_attack_info['power_loot'];
        }

        return round( (1200 * (pow(2.4, $LOOT_LEVEL)) ), 0, PHP_ROUND_HALF_UP);
    }
    public function upgradeOffenseLevel()
    {
        $USER_ID    = $this->USER->getUserId();
        $USER_MONEY = $this->USER->getMoney();

        $UPGRADE_PRICE = $this->getOffenseLevelUpgradePrice();

        if($USER_MONEY >= $UPGRADE_PRICE && ($USER_MONEY != null && $UPGRADE_PRICE != null))
        {
            $upgrade_array = array(
                'power_offense' => 1,
                'user_id'       => $USER_ID
            );

            if($this->addUpgradeLevelValuesToDatabase($upgrade_array))
            {
                if($this->USER->subtractMoney($UPGRADE_PRICE))
                {

                    $NEXT_UPGRADE_PRICE = $this->getOffenseLevelUpgradePrice();

                    return array(
                        'STATUS'        => "OK",
                        'UPGRADE_PRICE' => $NEXT_UPGRADE_PRICE

                    );
                }
                else
                {
                    return array(
                        'STATUS'    => "FAILED",
                        'ERR_MSG'   => "DATABASE_USER_MONEY_UPDATE_ERROR"
                    );
                }
            }
            else
            {
                return array(
                    'STATUS'    => "FAILED",
                    'ERR_MSG'   => "DATABASE_UPDATE_LEVEL_UPGRADE_ERROR"
                );
            }
        }
        else
        {
            return array(
                'STATUS'    => "FAILED",
                'ERR_MSG'   => "MONEY_LOW"
            );
        }

    }
    public function upgradeDefenseLevel()
    {
        $USER_ID        = $this->USER->getUserId();
        $USER_MONEY     = $this->USER->getMoney();

        $UPGRADE_PRICE  = $this->getDefenseLevelUpgradePrice();

        if($USER_MONEY >= $UPGRADE_PRICE && ($USER_MONEY != null && $UPGRADE_PRICE != null))
        {
            $upgrade_array = array(
                'power_defense' => 1,
                'user_id'       => $USER_ID
            );

            if($this->addUpgradeLevelValuesToDatabase($upgrade_array))
            {
                if($this->USER->subtractMoney($UPGRADE_PRICE))
                {

                    $NEXT_UPGRADE_PRICE = $this->getDefenseLevelUpgradePrice();

                    return array(
                        'STATUS'        => "OK",
                        'UPGRADE_PRICE' => $NEXT_UPGRADE_PRICE

                    );
                }
                else
                {
                    return array(
                        'STATUS'    => "FAILED",
                        'ERR_MSG'   => "DATABASE_USER_MONEY_UPDATE_ERROR"
                    );
                }
            }
            else
            {
                return array(
                    'STATUS'    => "FAILED",
                    'ERR_MSG'   => "DATABASE_UPDATE_LEVEL_UPGRADE_ERROR"
                );
            }
        }
        else
        {
            return array(
                'STATUS'    => "FAILED",
                'ERR_MSG'   => "MONEY_LOW"
            );
        }

    }
    public function upgradeLootLevel()
    {
        $USER_ID        = $this->USER->getUserId();
        $USER_MONEY     = $this->USER->getMoney();

        $UPGRADE_PRICE = $this->getLootLevelUpgradePrice();

        if($USER_MONEY >= $UPGRADE_PRICE && ($USER_MONEY != null && $UPGRADE_PRICE != null))
        {
            $upgrade_array = array(
                'power_loot' => 1,
                'user_id'       => $USER_ID
            );

            if($this->addUpgradeLevelValuesToDatabase($upgrade_array))
            {
                if($this->USER->subtractMoney($UPGRADE_PRICE))
                {

                    $NEXT_UPGRADE_PRICE = $this->getLootLevelUpgradePrice();

                    return array(
                        'STATUS'        => "OK",
                        'UPGRADE_PRICE' => $NEXT_UPGRADE_PRICE

                    );
                }
                else
                {
                    return array(
                        'STATUS'    => "FAILED",
                        'ERR_MSG'   => "DATABASE_USER_MONEY_UPDATE_ERROR"
                    );
                }
            }
            else
            {
                return array(
                    'STATUS'    => "FAILED",
                    'ERR_MSG'   => "DATABASE_UPDATE_LEVEL_UPGRADE_ERROR"
                );
            }
        }
        else
        {
            return array(
                'STATUS'    => "FAILED",
                'ERR_MSG'   => "MONEY_LOW"
            );
        }

    }
    /*
     *  Set new values (add values) for any/all upgrade value in Database.
     *
     * Param: Array[power_offense, power_defense, power_loot];
     *
     * */

    /**
     * addUpgradeLevelValuesToDatabase()
     * - - - - - - - -
     *   @access private
     *   @param array: [power_offense, power_defense, power_loot, user_id];
     *   @return boolean: true -> The values where added successfully to table;
     * */
    private function addUpgradeLevelValuesToDatabase($arg)
    {
        $level_offense  = 0;
        $level_defense  = 0;
        $level_loot     = 0;

        if(isset($arg['power_offense']))
        {
            $level_offense  = $arg['power_offense'];
        }
        if(isset($arg['power_defense']))
        {
            $level_defense  = $arg['power_defense'];
        }
        if(isset($arg['power_loot']))
        {
            $level_loot     = $arg['power_loot'];
        }

        $USER_ID = $arg['user_id']    ?: null;

        if($USER_ID != null)
        {
            $SQL = "UPDATE attack_user_info SET power_attack=power_attack+$level_offense, power_defend=power_defend+$level_defense, power_loot = power_loot + $level_loot WHERE user_id='$USER_ID'";

            if($QUERY = mysqli_query($this->CONNECTION, $SQL))
            {
                return true;
            }
        }


        return false;

    }
    public function getTimeLeftTillNextStaminaRefill()
    {
        $pvp_user_info = $this->getUserAttackInfoFromDatabase();

        $stamina_refill_time        = $pvp_user_info['stamina_refill_time'];
        $stamina_refill_interval    = $pvp_user_info['power_stamina_refill_interval'];
        $current_time               = time();

        $time_left                  = ($stamina_refill_time + $stamina_refill_interval) - $current_time;

        return $time_left;
    }
    public function doStaminaRoutineCheck()
    {
        /*

         *      [Description]
         *  -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -    -   -   -
         *      Check how much stamina the user should have at the current point in time.
         *      This is based on when the last stamina-refill was. The refill will only occur if the following logic is true.

         *      [variables]
         *  -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -    -   -   -
         *      1.  $stamina_current             : INT; 0 => 1
         *      2.  $stamina_max                 : INT; 5 => 5
         *      3.  $last_stamina_refill_time    : INT; 0 => 2
         *      4.  $stamina_refill_interval     : INT; 2 => 2
         *      5.  $current_time                : LONG 8 => 8

         *      [Logic]
         *  -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -    -   -   -
         *
         * while( ($stamina_current < $stamina_max)     &&     ( $last_stamina_refill_time + $stamina_refill_interval < ($current_time) ) )
         * {
         *      $stamina_current++;
         *      $last_stamina_refill_time += $stamina_refill_interval;
         * }
         *
         *  [   Update the users pvp-info: 'attack_user_info' - here]
         *
         * */



        $pvp_user_info = $this->getUserAttackInfoFromDatabase();

        $stamina_current            = $pvp_user_info['stamina_current'];
        $stamina_max                = $pvp_user_info['stamina_max'];
        $stamina_refill_time        = $pvp_user_info['stamina_refill_time'];            // Timestamp in seconds.
        $stamina_refill_interval    = $pvp_user_info['power_stamina_refill_interval'];  // Interval for each refill in seconds.

        $current_time               = time();

        while($stamina_current < $stamina_max && ($stamina_refill_time + $stamina_refill_interval) <= $current_time )
        {
            $stamina_current++;
            $stamina_refill_time += $stamina_refill_interval;
        }

        if($this->setNewStaminaToPlayer($stamina_current, $stamina_refill_time))
        {
            return true;
        }
        return false;

    }
    private function setNewStaminaToPlayer($new_current_stamina, $new_stamina_refill_time)
    {
        $USER_ID = $this->USER->getUserId();

        $SQL = "UPDATE attack_user_info SET stamina_current = $new_current_stamina, stamina_refill_time = $new_stamina_refill_time WHERE user_id='$USER_ID'";

        if($QUERY = mysqli_query($this->CONNECTION, $SQL))
        {
            return true;
        }
        return false;
    }

    public function initAllNeededTablesToDatabase()
    {
        require_once __DIR__ . "/../utils/se_utils.php";

        $time_left = $this->getTimeLeftTillNextStaminaRefill();
        die("Stamina refill in: " . StaticUtils::getFormattedTimeToCleanText(StaticUtils::getTimeFormatted($time_left)) );
        $SQL_USERS = "SELECT id FROM users";

        if($QUERY_USERS = mysqli_query($this->CONNECTION, $SQL_USERS))
        {
            while($USER_ID_R = mysqli_fetch_row($QUERY_USERS))
            {
                $USER_ID = $USER_ID_R[0];

                $SQL_USER_INFO   = "INSERT INTO attack_user_info(user_id) VALUE('$USER_ID ')";
                if($QUERY_INFO  = mysqli_query($this->CONNECTION, $SQL_USER_INFO))
                {
                    echo "<strong>Inserted attack_user_info for user ID: " . $USER_ID . " <strong><br>";

                    $SQL_USER_STATS   = "INSERT INTO attack_user_stats(user_id) VALUE('$USER_ID ')";
                    if($QUERY_STATS  = mysqli_query($this->CONNECTION, $SQL_USER_STATS))
                    {
                        echo "<strong>Inserted attack_user_stats for user ID: " . $USER_ID . " <strong><br>";
                    }
                    else
                    {
                        echo "Failed to insert attack_user_stats for user ID: " . $USER_ID . "<br>";
                    }
                }
                else
                {
                    echo "Failed to insert attack_user_info for user ID: " . $USER_ID . "<br>";
                }

            }
        }
        else
        {
            echo "Query failed. <br>";
        }
        echo "<br> <strong>Query-queue complete! </strong> <br>";

    }
    public function getUserStatsInfoFromDatabase($USER_ID = null)
    {
        if(! isset($USER_ID))
        {
            $USER_ID = $this->USER->getUserId();
        }

        $SQL = "SELECT * FROM attack_user_stats WHERE user_id='$USER_ID' LIMIT 1";

        if($QUERY = mysqli_query($this->CONNECTION, $SQL))
        {
            if($RESULT = mysqli_fetch_array($QUERY))
            {
                if($RESULT[0] != null)
                {
                    return $RESULT;
                }
                else
                {
                    if($this->insertPvpTablesForCurrentUser($USER_ID))
                    {
                        return $this->getUserStatsInfoFromDatabase($USER_ID);
                    }
                }

            }
        }
        return null;
    }
    /**
     * getUserMatchHistoryQueryArray()
     * - - - - - - - -
     *   @access public
     *   @param void
     *   @return mysqli_result: [id, user_id, stamina_current, stamina_max, stamina_refill_time, power_attack, power_defend];
     * */
    public function getUserMatchHistoryQueryArray()
    {
        $USER_ID = $this->USER->getUserId();

        $SQL = "SELECT * FROM attack_user_list WHERE (user_id_attack = '$USER_ID' OR user_id_defend = '$USER_ID') ORDER BY attack_time_start DESC";

        if($QUERY = mysqli_query($this->CONNECTION, $SQL))
        {
            return $QUERY;
        }
        return null;
    }
    /**
     * getUserMatchHistoryQueryArray()
     * - - - - - - - -
     *   @access public
     *   @param void
     *   @return mysqli_result: [id, user_id, num_attack, num_defend, num_attack_win, num_defend_win, pvp_score];
     * */
    public function getPvpScoreBoardQueryArray()
    {
        $SQL = "SELECT * FROM attack_user_stats ORDER BY pvp_score DESC";

        if($QUERY = mysqli_query($this->CONNECTION, $SQL))
        {
            return $QUERY;
        }
        return null;
    }
    /*
     *
     * removeStaminaFromUser($CURRENT_STAMINA)
     *
     * Remove 1 (one) stamina from the current user, and update 'stamina_refill_time' if needed - (if 'stamina_current' < 'stamina_max' ).
     *
     *
     * */
    private function removeStaminaFromUser()
    {
        $USER_ID            = $this->USER->getUserId();

        $PVP_USER_INFO      = $this->getUserAttackInfoFromDatabase();

        $STAMINA_CURRENT    = $PVP_USER_INFO['stamina_current'];
        $STAMINA_MAX        = $PVP_USER_INFO['stamina_max'];

        if($STAMINA_CURRENT >= 1)
        {
            if($STAMINA_CURRENT < $STAMINA_MAX)
            {
                $SQL = "UPDATE attack_user_info SET stamina_current = stamina_current - 1 WHERE user_id='$USER_ID'";
            }
            else if($STAMINA_CURRENT == $STAMINA_MAX)
            {
                $CURRENT_TIME  = time();

                $SQL = "UPDATE attack_user_info SET stamina_current = stamina_current - 1, stamina_refill_time = '$CURRENT_TIME' WHERE user_id='$USER_ID'";
            }

            if($QUERY = mysqli_query($this->CONNECTION, $SQL))
            {
                return true;
            }
        }
        return false;
    }

    /**
     * getUserMatchHistoryQueryArray()
     * - - - - - - - -
     *   @access private
     *   @param array
     *   @return boolean
     * */
    private function updateUserPvpStats($ARG_ARRAY) // $ARG_ARRAY: Array as argument with indexes to describe amount to add for method.
    {
        $USER_ID        = $this->USER->getUserId();

        $NUM_ATTACK     = 0;
        $NUM_DEFEND     = 0;

        $NUM_ATTACK_WIN = 0;
        $NUM_DEFEND_WIN = 0;

        $PVP_SCORE      = 0;

        if(! isset ($ARG_ARRAY))
        {
            return "ARGUMENT_MISMATCH_IN_UPDATE_PVP_INFO";
        }

        if(isset ($ARG_ARRAY['user_id']) )
        {
            $USER_ID        = $ARG_ARRAY['user_id'];
        }
        if(isset ($ARG_ARRAY['pvp_score']) )
        {
            $PVP_SCORE      = $ARG_ARRAY['pvp_score'];
        }
        if(isset ($ARG_ARRAY['num_attack']) )
        {
            $NUM_ATTACK     = $ARG_ARRAY['num_attack'];
        }
        if(isset ($ARG_ARRAY['num_defend']) )
        {
            $NUM_DEFEND     = $ARG_ARRAY['num_defend'];
        }
        if(isset ($ARG_ARRAY['num_attack_win']) )
        {
            $NUM_ATTACK_WIN = $ARG_ARRAY['num_attack_win'];
        }
        if(isset ($ARG_ARRAY['num_defend_win']) )
        {
            $NUM_DEFEND_WIN = $ARG_ARRAY['num_defend_win'];
        }



        $SQL = "UPDATE attack_user_stats SET
                  pvp_score = pvp_score + '$PVP_SCORE',
                  num_attack = num_attack + '$NUM_ATTACK',
                  num_defend = num_defend + '$NUM_DEFEND', 
                  num_attack_win = num_attack_win + '$NUM_ATTACK_WIN', 
                  num_defend_win = num_defend_win + '$NUM_DEFEND_WIN'
                  WHERE (user_id = '$USER_ID')";


        /*$SQL = "UPDATE attack_user_stats SET (pvp_score, num_attack, num_defend, num_attack_win, num_defend_win)
                  VALUES (pvp_score + $PVP_SCORE, num_attack + $NUM_ATTACK, num_defend + $NUM_DEFEND, num_attack_win + $NUM_ATTACK_WIN, num_defend_win + $NUM_DEFEND_WIN) 
                  WHERE (user_id='$USER_ID')";*/

        if($QUERY = mysqli_query($this->CONNECTION, $SQL))
        {
            return true;
        }
        return false;
    }
    public function startNewAttackOnUser($TARGET_USER_ID)
    {
        /*
         * -:- LOGIC CHART -:-
         *
         * 1. Check if the current user have enough stamina to attack.      - !DONE!
         * 2. Check if the defending user can be attacked.                  - !DONE!
         * 3. Get the user-attack-info to both players.                     - !DONE!
         *
         * 4. Find a match winner.                                          - !DONE!
         * 5. Get 5% of the losing player's cash.                           - !DONE!
         * 6. Set new pvp_score to the players.
         *
         * 7. Add 5% of the losing players cash, to the winning players cash.
         * 8. Remove 5% of the losing players cash.
         *
         * 9. Insert new match to the 'match-list' - in Database.
         * 10. Update the attack_user_stats to both players.
         *      1. DEFENDER_LOST:
         *              1. num_defend       + 1
         *
         *      2. DEFENDER_WON:
         *              1. num_defend       + 1
         *              2. num_defend_win   + 1
         *
         *      3. DEFENDER_LOST:
         *              1. num_attack       + 1
         *
         *      4. DEFENDER_WON:
         *              1. num_attack       + 1
         *              2. num_attack_win   + 1
         *
         * 11. Remove 1 stamina from the attacking player. (Current user)
         * */


        if($TARGET_USER_ID == $this->USER->getUserId())
        {
            return array(
                'STATUS'    => "FAILED",
                'ERR_MSG'    => "USER_SELF"
            );
        }


        // Get both of the user's PVP info pvp_user_info(stamina, power_attack, power_defend)

        //Attacker's info
        $ATTACK_PVP_USER_INFO       = $this->getUserAttackInfoFromDatabase($this->USER->getUserId());
        $ATTACK_PVP_USER_STATS      = $this->getUserStatsInfoFromDatabase($this->USER->getUserId());

        $ATTACK_PVP_POWER_ATTACK    = $ATTACK_PVP_USER_INFO['power_attack'];            // The power of the attacker

        //Defender's info
        $DEFEND_PVP_USER_INFO       = $this->getUserAttackInfoFromDatabase($TARGET_USER_ID);
        $DEFEND_PVP_USER_STATS      = $this->getUserStatsInfoFromDatabase($TARGET_USER_ID);

        $DEFEND_PVP_POWER_DEFEND    = $DEFEND_PVP_USER_INFO['power_defend'];            // The power of the defender




        if($ATTACK_PVP_USER_INFO['stamina_current'] > 0)    //Check if the attacker have enough stamina (stamina > 0)
        {
            if($DEFEND_PVP_USER_STATS['num_attack'] > 0)    //Check if the defender have attacked someone before, by checking the N attacks of defender (num_attacks > 0)
            {
                /*      PART: 1
                 *
                 *  CHECKPOINT: Both players are eligible to attack.
                 *
                 *  Task: Find winner.
                 *
                 *  1. Update the attackers stamina.
                 *
                 * */

                if(! $this->removeStaminaFromUser())
                {
                    return array(
                        'STATUS'    => "FAILED",
                        'ERR_MSG'   => "STAMINA_UPDATE_REMOVE_FAILED"
                    );
                }

                $WINNER_USER_ID = $this->USER->getUserId();
                $LOSER_USER_ID  = $TARGET_USER_ID;

                if($ATTACK_PVP_POWER_ATTACK == $DEFEND_PVP_POWER_DEFEND) // Check if both players stand with same strength (pow_attack, pow_defend).
                {
                    $RAND_WINNER_N = rand(1, 100);

                    if($RAND_WINNER_N <= 50)
                    {
                        $WINNER_USER_ID = $TARGET_USER_ID;
                        $LOSER_USER_ID  = $this->USER->getUserId();
                    }

                }
                else if($ATTACK_PVP_POWER_ATTACK < $DEFEND_PVP_POWER_DEFEND) // Find the best of both players, by comparing strength (pow_attack, pow_defend).
                {
                    $WINNER_USER_ID = $TARGET_USER_ID;
                    $LOSER_USER_ID  = $this->USER->getUserId();
                }

                /*
                 *      PART: 2
                 *
                 *      Task: Get 5% of the loser's money
                 *
                 *  1. Query: Get the losing player's current money.
                 *  2. Find the different scores the add to the players in the match.
                 */

                $SQL = "SELECT money FROM users WHERE id='$LOSER_USER_ID' LIMIT 1";

                $MONEY_RESULT_INT = 0;                                  //Set default value to the losers money, in-case of query error.

                if($QUERY = mysqli_query($this->CONNECTION, $SQL))
                {
                    if($MONEY_RESULT = mysqli_fetch_row($QUERY))
                    {
                        $MONEY_RESULT_INT = $MONEY_RESULT[0];           // Get the current money of the user that lost.
                    }
                }

                $WINNER_MONEY_AWARD = ($MONEY_RESULT_INT / 100) * 5;     // 5% (x%) of the loser's current money.


                $WINNER_PVP_SCORE_ADD   = 3;
                $LOSER_PVP_SCORE_ADD    = -1;

                $WINNER_NUM_ATTACK      = 0;
                $WINNER_NUM_DEFEND      = 0;

                $WINNER_NUM_ATTACK_WIN  = 0;
                $WINNER_NUM_DEFEND_WIN  = 0;

                $LOSER_NUM_ATTACK       = 0;
                $LOSER_NUM_DEFEND       = 0;



                // Check if the attacker won the match.

                $ATTACKER_WON_MATCH = ($this->USER->getUserId() == $WINNER_USER_ID); // Boolean: The attacker won the match.


                if($ATTACKER_WON_MATCH)
                {
                    $WINNER_NUM_ATTACK++;
                    $WINNER_NUM_ATTACK_WIN++;

                    $LOSER_NUM_DEFEND++;
                }
                else
                {
                    $WINNER_PVP_SCORE_ADD = 2;
                    $LOSER_PVP_SCORE_ADD = -2;

                    $WINNER_NUM_DEFEND++;
                    $WINNER_NUM_DEFEND_WIN++;

                    $LOSER_NUM_ATTACK++;
                }


                $WINNER_UPDATE_STATS_ARRAY = array(
                    'user_id'           => $WINNER_USER_ID,
                    'pvp_score'         => $WINNER_PVP_SCORE_ADD,
                    'num_attack'        => $WINNER_NUM_ATTACK,
                    'num_defend'        => $WINNER_NUM_DEFEND,
                    'num_attack_win'    => $WINNER_NUM_ATTACK_WIN,
                    'num_defend_win'    => $WINNER_NUM_DEFEND_WIN

                );

                $LOSER_UPDATE_STATS_ARRAY = array(
                    'user_id'       => $LOSER_USER_ID,
                    'pvp_score'     => $LOSER_PVP_SCORE_ADD,
                    'num_attack'    => $LOSER_NUM_ATTACK,
                    'num_defend'    => $LOSER_NUM_DEFEND
                );

                // Update the winner's pvp-stats in database
                if($this->updateUserPvpStats($WINNER_UPDATE_STATS_ARRAY))
                {

                }
                else
                {

                    $A_RESULT = $this->updateUserPvpStats($WINNER_UPDATE_STATS_ARRAY);

                    return array(
                        'STATUS'    => "FAILED",
                        'ERR_MSG'   => "FAILED_TO_UPDATE_PVP_USER_STATS_A",
                        'MSG'       => $A_RESULT
                    );
                }

                if($this->updateUserPvpStats($LOSER_UPDATE_STATS_ARRAY))
                {

                }
                else
                {
                    $B_RESULT = $this->updateUserPvpStats($LOSER_UPDATE_STATS_ARRAY);

                    return array(
                        'STATUS'    => "FAILED",
                        'ERR_MSG'   => "FAILED_TO_UPDATE_PVP_USER_STATS_B",
                        'MSG'       => $B_RESULT
                    );
                }


                /*
                 *      Task: Insert new match result to database.
                 *
                 *          TO-DO: Transfer the 5% of the losing player's current money into the winners money.
                 * */

                $ATTACK_USER_ID     = $this->USER->getUserId();
                $ATTACK_TIME_START  = time();


                $MATCH_RESULT_ARRAY = array(
                    'user_id_attack'        => $ATTACK_USER_ID,
                    'user_id_defend'        => $TARGET_USER_ID,
                    'attack_time_start'     => $ATTACK_TIME_START,
                    'power_attack'          => $ATTACK_PVP_POWER_ATTACK,
                    'power_defend'          => $DEFEND_PVP_POWER_DEFEND,
                    'winner_user_id'        => $WINNER_USER_ID,
                    'winner_award_money'    => $WINNER_MONEY_AWARD,
                    'score_change_winner'   => $WINNER_PVP_SCORE_ADD,
                    'score_change_loser'    => $LOSER_PVP_SCORE_ADD
                );
                if($this->insertNewMatchResultToDatabase($MATCH_RESULT_ARRAY))
                {
                    /*
                     *                  !!---------END---------!!
                     *
                     *              Script completed with great success!
                     * */

                    require_once( __DIR__ . "/../utils/se_utils.php");                        // StaticEngine utils: Using getTimeFormatted() to convert unix timestamp to datetime.


                    $ATTACK_TIME            = time() - $ATTACK_TIME_START;

                    $ATTACK_TIME_CLEAN_TEXT = StaticUtils::getFormattedTimeToCleanText(StaticUtils::getTimeFormatted($ATTACK_TIME));

                    $WINNER_MONEY_CLEAN = round($WINNER_MONEY_AWARD, 0, PHP_ROUND_HALF_ODD);

                    $MATCH_INFO_ARRAY = array(
                        'user_id_attack'        => $ATTACK_USER_ID,
                        'user_id_defend'        => $TARGET_USER_ID,
                        'attack_time_start'     => $ATTACK_TIME_CLEAN_TEXT,
                        'power_attack'          => $ATTACK_PVP_POWER_ATTACK,
                        'winner_user_id'        => $WINNER_USER_ID,
                        'winner_award_money'    => $WINNER_MONEY_CLEAN,
                        'score_change_winner'   => $WINNER_PVP_SCORE_ADD

                    );

                    return $MATCH_INFO_ARRAY;
                }
                else
                {
                    $SQL_MSG = mysqli_error_list($this->CONNECTION);

                    return array(
                        'STATUS'    => "FAILED",
                        'ERR_MSG'   => "FAILED_TO_INSERT_NEW_MATCH_RESULT",
                        'SQL_MSG'   => "SQL: " . $SQL_MSG
                    );
                }

            }
            else
            {
                return array(
                    'STATUS'    => "FAILED",
                    'ERR_MSG'   => "PLAYER_CANNOT_BE_ATTACKED"
                );
            }
        }
        else{
            return array(
                'STATUS'    => "FAILED",
                'ERR_MSG'   => "STAMINA_LOW"
            );
        }

    }
    private function insertNewMatchResultToDatabase($ARG_ARRAY)
    {
        $columns = implode(", ",array_keys($ARG_ARRAY));
        $values  = implode(", ", array_values($ARG_ARRAY));
        
        $SQL = "INSERT INTO attack_user_list($columns) VALUES ($values)";

        if($QUERY = mysqli_query($this->CONNECTION, $SQL))
        {
            return true;
        }
        return false;

    }

    private function _connect()
    {
        require_once( __DIR__ . "/../connect/database.php");

        if($this->CONNECTION = Database::getConnection())
        {
            return true;
        }
        return false;
    }
    private function _loadUser()
    {
        require_once( __DIR__ . "/../common/session/sessioninfo.php");

        if($this->USER = new User(-1, -1))
        {
            if($this->USER->isLoggedIn())
            {
                return true;
            }
        }

        return false;
    }

}