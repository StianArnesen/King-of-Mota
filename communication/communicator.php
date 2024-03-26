<?php

$ROOT = $_SERVER['DOCUMENT_ROOT'];

include_once($ROOT . "/connect/connection.php");

class Communicator
{
    private $CONNECTION;

    private $USER;

    public function __construct()
    {
        if($this->connect())
        {
            $this->getUserInfo();
        }
        else{
            die("Connection failed!");
        }
    }
    public function getChatItems()
    {
        $RESULT = "";

        if(isset($this->USER))
        {
            $CHAT_ITEMS_QUERY = $this->getChatItemsQuery();

            while($CHAT_ITEM = mysqli_fetch_array($CHAT_ITEMS_QUERY))
            {
                $CHAT_USER_ID = $CHAT_ITEM['user_id'];
                $CHAT_ITEM_ID = $CHAT_ITEM['inbox_link_id'];

                $CHAT_ITEM_CLIENT_STATUS = $CHAT_ITEM['client_status'];

                $MEMBERS_OF_CHAT = $this->getMembersOfChatGroup($CHAT_ITEM_ID);

                while($CHAT_MEMBER = mysqli_fetch_array($MEMBERS_OF_CHAT))
                {
                    //$RESULT .= "<div class='chat_item_view_box' id='chat_item_view_box_w_id_$CHAT_ITEM_ID'> <div class='show_chat_data_button'><button class='btn_open_chat' onclick='showChat($CHAT_ITEM_ID)'></button> <button class='btn_close_chat' onclick='closeChat($CHAT_ITEM_ID)'>X</button> </div>";
                    $RESULT .= "<div class='chat_item_view_box' id='chat_item_view_box_w_id_$CHAT_ITEM_ID'> <div class='show_chat_data_button'><button class='btn_open_chat' onclick='showChat($CHAT_ITEM_ID)'></button> <button class='btn_close_chat' onclick='closeChat($CHAT_ITEM_ID)'>X</button> </div>";

                    $CHAT_PARTNER_ID = $CHAT_MEMBER['user_id'];

                    $CHAT_USER_QUERY = $this->getUserInfoID($CHAT_PARTNER_ID);
                    $CHAT_USER = mysqli_fetch_array($CHAT_USER_QUERY);

                    $USERNAME = $CHAT_USER['username'];

                    $CHAT_DATA = $this->getChatData($CHAT_ITEM_ID);


                    $RESULT .= "<div class='bottom_banner_chat_item'>$USERNAME</div>";
                    if($CHAT_ITEM_CLIENT_STATUS == 2)
                    {
                        $RESULT .= "<div class='chat_view' style='display: block;' id='chat_view_item_id_$CHAT_ITEM_ID'>$CHAT_DATA</div>";
                    }
                    else{
                        $RESULT .= "<div class='chat_view' style='display: none;' id='chat_view_item_id_$CHAT_ITEM_ID'>$CHAT_DATA</div>";
                    }

                    $RESULT .= "</div>";
                }
            }
        }

        return $RESULT;
    }
    private function getUserInfoID($ID)
    {
        $SQL = "SELECT id, username, profile_picture, level, last_active FROM users WHERE id='$ID'";
        $QUERY = mysqli_query($this->CONNECTION, $SQL);

        return $QUERY;
    }
    private function getChatItemsQuery()
    {
        $USER_ID = $this->USER->getUserId();

        $STATUS_MINIMIZED = 1;
        $STATUS_OPEN = 2;

        $SQL = "SELECT * FROM inbox_group_members WHERE (user_id = $USER_ID) AND (client_status = $STATUS_MINIMIZED OR client_status = $STATUS_OPEN)";
        $QUERY = mysqli_query($this->CONNECTION, $SQL);

        return $QUERY;
    }
    private function getMembersOfChatGroup($INBOX_LINK_ID)
    {
        $CURRENT_USER_ID = $this->USER->getUserId();

        $SQL = "SELECT * FROM inbox_group_members WHERE inbox_link_id = $INBOX_LINK_ID AND user_id != $CURRENT_USER_ID LIMIT 1";
        $QUERY = mysqli_query($this->CONNECTION, $SQL);

        return $QUERY;
    }
    public function initializeNewChat($USER)
    {
        $USER_ID = $this->getUserIdByName($USER);

        if($CHAT_GROUP_LINK_ID = $this->getChatGroupIdIfPresent($USER_ID))
        {
            if($this->setInboxGroupMemberStatus(2, $CHAT_GROUP_LINK_ID))
            {
                return "Success! Group found.";
            }
            return "Failed to set client chat status!";
        }
        else{
            if($GROUP_ID = $this->createNewInboxGroupAndGetGroupId($USER_ID))
            {
                if($this->setInboxGroupMemberStatus(2, $GROUP_ID))
                {

                }
                return "New group for inbox created!";
            }
            return "Failed to create new group!";
        }
        return "Failed to get group!";
    }
    private function createNewInboxGroupAndGetGroupId($USER_ID)
    {
        $CURRENT_USER_ID = $this->USER->getUserId();

        $SQL = "INSERT INTO inbox_groups (group_leader_id) VALUES ('$CURRENT_USER_ID')";
        $QUERY = mysqli_query($this->CONNECTION, $SQL);

        if($QUERY)
        {
            $SQL_GET_GROUP_ID = "SELECT id FROM inbox_groups ORDER BY id DESC LIMIT 1";
            $QUERY_GROUP_ID = mysqli_query($this->CONNECTION, $SQL_GET_GROUP_ID);

            $GROUP_ID_ROW = mysqli_fetch_row($QUERY_GROUP_ID);

            $GROUP_ID = $GROUP_ID_ROW[0];

            if($this->addMemberToInboxGroup($GROUP_ID, $CURRENT_USER_ID))
            {
                if($this->addMemberToInboxGroup($GROUP_ID, $USER_ID))
                {

                }
            }
            else{
                die("Failed to add members to inbox group!");
            }

            return $GROUP_ID;
        }
    }
    private function addMemberToInboxGroup($GROUP_LINK_ID, $USER_ID)
    {
        $SQL = "INSERT INTO inbox_group_members (inbox_link_id, user_id) VALUES ($GROUP_LINK_ID, $USER_ID)";
        $QUERY = mysqli_query($this->CONNECTION, $SQL);

        if($QUERY)
        {
            return true;
        }
        return false;
    }
    public function setInboxGroupMemberStatus($STATUS, $CHAT_GROUP_LINK_ID)
    {
        $CURRENT_USER_ID = $this->USER->getUserId();

        $SQL = "UPDATE inbox_group_members SET client_status = '$STATUS' WHERE (user_id = '$CURRENT_USER_ID' AND inbox_link_id = '$CHAT_GROUP_LINK_ID')";
        $QUERY = mysqli_query($this->CONNECTION, $SQL);

        if($QUERY)
        {
            return true;
        }
        return false;
    }
    public function setInboxGroupMemberStatusToggle($CHAT_GROUP_LINK_ID)
    {
        $CURRENT_USER_ID = $this->USER->getUserId();

        if($this->getInboxGroupMemberStatus($CHAT_GROUP_LINK_ID) == 1)
        {
            $STATUS = 2;
        }
        else if($this->getInboxGroupMemberStatus($CHAT_GROUP_LINK_ID) == 2)
        {
            $STATUS = 1;
        }

        $SQL = "UPDATE inbox_group_members SET client_status = '$STATUS' WHERE (user_id = '$CURRENT_USER_ID' AND inbox_link_id = '$CHAT_GROUP_LINK_ID')";
        $QUERY = mysqli_query($this->CONNECTION, $SQL);

        if($QUERY)
        {
            return true;
        }
        return false;
    }
    private function getInboxGroupMemberStatus($CHAT_GROUP_LINK_ID)
    {
        $CURRENT_USER_ID = $this->USER->getUserId();

        $SQL = "SELECT client_status FROM inbox_group_members WHERE (user_id = '$CURRENT_USER_ID' AND inbox_link_id = '$CHAT_GROUP_LINK_ID')";
        $QUERY = mysqli_query($this->CONNECTION, $SQL);

        $RESULT = mysqli_fetch_row($QUERY);

        return $RESULT[0];
    }
    private function getUserIdByName($USERNAME)
    {
        $G_USERNAME = $USERNAME;

        $SQL = "SELECT id FROM users WHERE username = '$G_USERNAME'";
        $QUERY = mysqli_query($this->CONNECTION, $SQL);

        $RESULT = mysqli_fetch_row($QUERY);

        if(! isset($RESULT))
        {
            die("User not found! At: GUIDBN_0242323");
        }

        return $RESULT[0];
    }
    private function getChatGroupIdIfPresent($USER_ID) // Returns the link_id for the chat_group betweed the current - and selected user.
    {
        $CURRENT_USER_ID = $this->USER->getUserId();

        $SQL = "SELECT a.inbox_link_id FROM inbox_group_members a JOIN inbox_group_members b ON (a.user_id = $USER_ID AND b.user_id = $CURRENT_USER_ID AND a.inbox_link_id = b.inbox_link_id)";
        $QUERY = mysqli_query($this->CONNECTION, $SQL);

        $RESULT = mysqli_fetch_row($QUERY);

        if(! isset($RESULT))
        {
            return false;
        }

        return $RESULT[0];

    }
    private function getChatData($CHAT_LINK_ID)
    {
        $RESULT = "";

        $QUERY = $this->getChatDataQuery($CHAT_LINK_ID);
        $RESULT .= "<div class='chat-item-data-view'>";
        while($CHAT_DATA = mysqli_fetch_array($QUERY))
        {
            $USER_INFO_QUERY = $this->getUserInfoID($CHAT_DATA['from_user_id']);
            $USER = mysqli_fetch_array($USER_INFO_QUERY);

            $IMAGE = $USER['profile_picture'];

            $DATA = $CHAT_DATA['data'];
            $SENDER = $CHAT_DATA['from_user_id'];

            $THIS_USER_OR_ANOTHER = "to_current";

            if($SENDER == $this->USER->getUserId())
            {
                $THIS_USER_OR_ANOTHER = "from_current";
            }

            $RESULT .= "<div class='inbox_chat_item_$THIS_USER_OR_ANOTHER'>
                        <div class='inbox_chat_item_user_picture'>
                            <img src='$IMAGE'>
                        </div>
                        <div class='inbox_chat_item_data'>
                            $DATA
                        </div>

                        </div>";
        }
        $RESULT .= "</div>";
        $RESULT .= "<div class='msg-input-field-view'><input type='text' placeholder='write' class='msg_input_text'> <button class='btn_send_message_chat_button'>Send</button> <input type='hidden' class='message_input_group' value='$CHAT_LINK_ID'></div>";
        return $RESULT;
    }
    private function getChatDataQuery($CHAT_LINK_ID)
    {
        $USER_ID = $this->USER->getUserId();

        $SQL = "SELECT * FROM inbox WHERE inbox_link_id = $CHAT_LINK_ID ORDER BY send_time ASC";
        $QUERY = mysqli_query($this->CONNECTION, $SQL);

        return $QUERY;
    }
    private function getUserInfo()
    {
        $ROOT = $_SERVER['DOCUMENT_ROOT'];

        include_once($ROOT . "/common/session/sessioninfo.php");
        if(session_status() == PHP_SESSION_NONE)
        {
            session_start();
        }

        $ID = $_SESSION['game_user_id'];
        $USERNAME = $_SESSION['game_username'];

        if($this->USER = new User($ID, $USERNAME))
        {
            return true;
        }
        return false;
    }
    private function connect()
    {
        if($SE_CONNECTION = new StaticConnection())
        {
            $DB_PARAM = $SE_CONNECTION->getSecureConnectionParams();

            if($this->CONNECTION = mysqli_connect($DB_PARAM[0], $DB_PARAM[1], $DB_PARAM[2], $DB_PARAM[3]))
            {
                return true;
            }
        }
        return false;
    }
    public function sendNewChatMessage($TO_USER_ID,$CHAT_LINK_GROUP_ID, $DATA)
    {
        if($this->checkInboxGroupMembership($CHAT_LINK_GROUP_ID))
        {
            if($this->finalizeSendChatMessage($TO_USER_ID,$CHAT_LINK_GROUP_ID, $DATA))
            {
                return true;
            }
            else{
                die("Failed to finalize insertion of chat data! At: SNC_MSG_01");
            }
        }
        else{
            die("User chat-group membership authorization failed! At: CIGMS_A_0552");
        }
        return false;
    }
    private function finalizeSendChatMessage($TO_USER_ID, $CHAT_LINK_GROUP_ID, $DATA)
    {
        $MSG_LINK_ID = $CHAT_LINK_GROUP_ID;

        $MSG_FROM_ID = $this->USER->getUserId();
        $MSG_TO_ID = $TO_USER_ID;

        $MSG_DATA = $DATA;

        $MSG_TYPE = 0;
        $MSG_SEND_TIME = time();

        if(! isset($this->USER))
        {
            die("UserClass not set! At: FNLZ_N_MSG_02");
        }

        if(! isset($this->CONNECTION))
        {
            die("Failed to finalize insertion of chat data! [CONNECTION NOT SET] -- At: FNLZ_N_MSG_05");
        }

        $SQL = "INSERT INTO inbox(inbox_link_id, from_user_id, to_user_id, data, type, send_time) VALUES ('$MSG_LINK_ID','$MSG_FROM_ID', '$MSG_TO_ID', '$MSG_DATA', '$MSG_TYPE', '$MSG_SEND_TIME')";
        $QUERY = mysqli_query($this->CONNECTION, $SQL);

        if($QUERY)
        {
            return true;
        }
        else{
            die("Query failed! At: FNLZ_N_MSG_QUERY_01");
        }
        return false;

    }
    private function checkInboxGroupMembership($CHAT_LINK_GROUP_ID)
    {
        $GROUP_ID = $CHAT_LINK_GROUP_ID;
        $USER_ID = $this->USER->getUserId();

        $SQL = "SELECT * FROM inbox_group_members WHERE inbox_link_id = $GROUP_ID AND user_id = $USER_ID";
        $QUERY = mysqli_query($this->CONNECTION, $SQL);

        $RESULT = mysqli_fetch_array($QUERY);

        if($RESULT['inbox_link_id'] == $CHAT_LINK_GROUP_ID && $RESULT['user_id'] == $USER_ID)
        {
            return true;
        }

        return false;
    }
}
