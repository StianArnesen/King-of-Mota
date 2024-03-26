<?php


class MissionController
{
    private $CONNECTION;

    private $USER;

    public function __construct()
    {
        if($this->_connect())
        {
            if($this->_loadUser())
            {

            }
        }
    }
    public function getMissionInfo($MISSION_ID)
    {
        $SQL = "SELECT * FROM mission_list WHERE id='$MISSION_ID'";

        if($QUERY = mysqli_query($this->CONNECTION, $SQL))
        {
            if($RESULT = mysqli_fetch_array($QUERY))
            {
                return $RESULT;
            }
        }
        
        return false;
    }
    public function getRequirementPreText($REQ_TYPE, $REQ_STATUS_TYPE) // Ref: Google Docs -> King of Mota -> Mission -> mission spreadsheet
    {
        if($REQ_TYPE == 0)
        {
            if($REQ_STATUS_TYPE == 0)
            {
                return "";
            }
        }
    }
    public function getMissionRequirements($MISSION_ID)
    {
        $SQL = "SELECT * FROM mission_requirement_list WHERE mission_id='$MISSION_ID'";

        if($QUERY = mysqli_query($this->CONNECTION, $SQL))
        {
            $REQ_LIST = array();

            while($RESULT = mysqli_fetch_array($QUERY))
            {
                $TYPE           = $RESULT['req_type'];
                $STATUS_TYPE    = $RESULT['req_status_type'];
                $ITEM_ID        = $RESULT['item_id'];
                $ITEM_AMOUNT    = $RESULT['item_amount'];

                $R = array(
                    "TYPE"          => $TYPE,
                    "STATUS_TYPE"   => $STATUS_TYPE,
                    "ITEM_ID"       => $ITEM_ID,
                    "ITEM_AMOUNT"   => $ITEM_AMOUNT

                );

                array_push($REQ_LIST, $R);

            }
            return $REQ_LIST;
        }

        return false;
    }

    private function _loadUser()
    {
        require_once(__DIR__ . "/../common/session/sessioninfo.php");

        if($this->USER = new User(-1, -1))
        {
            return true;
        }

        return false;
    }
    private function _connect()
    {
        require_once(__DIR__ . "/../connect/database.php");

        if($this->CONNECTION = Database::getConnection())
        {
            return true;
        }

        return false;
    }
}
