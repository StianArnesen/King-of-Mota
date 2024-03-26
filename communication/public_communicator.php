<?php
$ROOT = $_SERVER['DOCUMENT_ROOT'];

include("communicator.php");
include($ROOT . "/layout/bot_banner/bot_banner.php");

$COMMUNICATOR = new Communicator();

if(isset($_GET['get_data_type']))
{
    $GET_TYPE = $_GET['get_data_type'];

    if($GET_TYPE == 0) // Return chat.
    {
        $BOTTOM_BANNER = new BottomBanner();
        die($BOTTOM_BANNER->getBottomBanner());
    }
}

if(isset($_POST['msg_link']))
{
    $MSG_CHAT_GROUP_LINK_ID     =   $_POST['msg_link'];
    $MSG_CHAT_DATA              =   $_POST['msg_link_data'];


    if($COMMUNICATOR->sendNewChatMessage(1, $MSG_CHAT_GROUP_LINK_ID, $MSG_CHAT_DATA))
    {
        die(" Success!");
    }
    else{
        die(" Failed to send message!");
    }
}
else if(isset($_POST['get_chat_id']))
{
    //$USER_ID = $_POST['get_chat_id'];

    //die("The returned group: " . $COMMUNICATOR->initializeNewChat($USER_ID));
}
else if(isset($_POST['get_chat_id_username']))
{
    $USERNAME = $_POST['get_chat_id_username'];

    die($COMMUNICATOR->initializeNewChat($USERNAME));
}
else if(isset($_POST['close_inbox']))
{
    $INBOX_ID = $_POST['close_inbox'];

    die($COMMUNICATOR->setInboxGroupMemberStatus(0,$INBOX_ID));
}
else if(isset($_POST['show_inbox']))
{
    $INBOX_ID = $_POST['show_inbox'];

    die($COMMUNICATOR->setInboxGroupMemberStatus(2,$INBOX_ID));
}
else if(isset($_POST['minimize_inbox']))
{
    $INBOX_ID = $_POST['minimize_inbox'];

    die($COMMUNICATOR->setInboxGroupMemberStatus(1,$INBOX_ID));
}
else if(isset($_POST['toggle_inbox_client_status']))
{
    $INBOX_ID = $_POST['toggle_inbox_client_status'];

    die($COMMUNICATOR->setInboxGroupMemberStatusToggle($INBOX_ID));
}

else{
    die("Invalid parameter!");
}


?>
