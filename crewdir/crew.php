<?php

include("connect/connection.php");
include("common/session/sessioninfo.php");
include("crew/warInfo.php");


class Crew
{
    private $CONNECTION;

    private $USER;

    private $BOOOLEAN_connected;

    private $WAR_INFO;


    public function __construct()
    {
        $this->BOOOLEAN_connected = false;

        if($this->connect())
        {
            if($this->authUser())
            {
                if($this->verifyCrewLeader())
                {
                    $this->loadWarInfo();
                }
                else
                {
                    die(".SE: User DENIED. This user is not the leader of the crew! At: C_VUCL1");
                }
            }
            else
            {die(".SE: User DENIED. This user is not authorized! At: C_VU1");}
        }
        else
        {die(".SE: Failed to connect! C_CN1");}
    }
    private function loadWarInfo()
    {
        $this->WAR_INFO = new WarInfo();
    }
    private function authUser()
    {
        if(session_status() == PHP_SESSION_NONE)
        {
            session_start();
        }

        if(isset($_SESSION['game_username']))
        {
            $SESSION_USERNAME = $_SESSION['game_username'];

            $SQL = "SELECT id, username FROM users WHERE username = '$SESSION_USERNAME'";

            $QUERY = mysqli_query($this->CONNECTION, $SQL);

            $RESULT = mysqli_fetch_row($QUERY);

            if(isset($RESULT))
            {
                if($RESULT[1] == $SESSION_USERNAME)
                {
                    $this->getUserInfo($RESULT[0], $RESULT[1]);
                    return true;
                }
            }
        }

        return false;
    }
    private function connect()
    {
        $StaticConnectionClass = new StaticConnection();

        $CON_PARAM = $StaticConnectionClass->getSecureConnectionParams();

        if($this->CONNECTION = mysqli_connect($CON_PARAM[0],$CON_PARAM[1], $CON_PARAM[2],$CON_PARAM[3]))
        {
            $this->BOOOLEAN_connected = true;
            return true;
        }
        die("CONNECTION FAILED. AT: MC1");
    }
    public function attackCrew($CREW_ATTACK_ID)
    {
        if($this->verifyCrewLeader())
        {
            /* CREW DEFENDER INFO*/
            $CREW_DEFENDER_QUERY = $this->getCrewInfo($CREW_ATTACK_ID);
            $CREW_DEFENDER_RESULT = mysqli_fetch_array($CREW_DEFENDER_QUERY);

            /* CREW ATTACKER INFO*/
            $ATTACKER_USER_CREW_ID = $this->USER->getCrewId(); // Crew ID to current user.

            $CREW_ATTACKER_QUERY = $this->getCrewInfo($ATTACKER_USER_CREW_ID);
            $CREW_ATTACKER_RESULT = mysqli_fetch_array($CREW_ATTACKER_QUERY);

            if($ATTACKER_USER_CREW_ID == $CREW_ATTACK_ID) // Check if the attacker is the same as defender. Die if case.
            {
                die(".SE: You cannot attack your own crew! AT: ACR33423");
            }

            if(isset($this->USER))
            {

            }
            else{
                die(".SE: USER INFO NOT SET! AT: ACR44");
            }

            if(isset($CREW_DEFENDER_RESULT))
            {
                if($CREW_ATTACKER_RESULT['crew_war_status'] == 0)
                {
                    die(".SE: YOUR CREW CANNOT GO TO WAR! AT ACR6");
                }
                else if($CREW_DEFENDER_RESULT['crew_war_status'] == 1)
                {
                    $SCORE_DEFENDER = $CREW_DEFENDER_RESULT['crew_score'];
                    $SCORE_ATTACKER = $CREW_ATTACKER_RESULT['crew_score'];

                    if($this->WAR_INFO->verifyRanks($SCORE_DEFENDER, $SCORE_ATTACKER))
                    {

                    }
                    else
                    {
                        die(".SE: This crew is not in the same league! AT: ACR53");
                    }
                }
                else
                {
                    die(".SE: THIS CREW CANNOT GO TO WAR! AT ACR2");
                }
            }
            else{
                die(".SE: FAILED TO GET ALL CREW INFO! AT: ACR1");
            }
        }
    }
    public function getCrewInfo()
    {
        $SQL_CREW_INFO = "SELECT * FROM crew WHERE crew_id = $this->CREW_ID";
        $QUERY_CREW_INFO = mysqli_query($this->CONNECTION, $SQL_CREW_INFO);

        return $QUERY_CREW_INFO;
    }
    public function verifyCrewLeader()
    {
        if($this->BOOOLEAN_connected)
        {
            $USER_CREW_ID = $this->USER->getCrewId();

            $SQL = "SELECT crew_leader FROM crew WHERE crew_id = '$USER_CREW_ID'";
            $QUERY = mysqli_query($this->CONNECTION, $SQL);

            $RESULT = mysqli_fetch_row($QUERY);

            if($RESULT[0] == $USER_CREW_ID) // The current user, is the leader of the crew.
            {
                return true;
            }
            return false;
        }
        die("ERROR: CONNECTION NOT SET. AT: A1");
    }
    private function getUserInfo($ID, $NAME)
    {
        $this->USER = new User($ID, $NAME);
    }

}