<?php

if(isset($_POST['get_user_settings']))
{
	require("UserController.php");
	
	$USER_CTRL 	= new UserController();

	$VAL 		=  $USER_CTRL->getUserSettings();
	die(json_encode($VAL, JSON_PRETTY_PRINT));
}
else if(isset($_POST['get_image_crop'])){
    require("UserController.php");

    $USER_CTRL 	= new UserController();

    $VAL 		=  $USER_CTRL->getPublicUserSettings();
    die(json_encode($VAL, JSON_PRETTY_PRINT));
}
else if(isset($_POST['set_image_crop'])){
    require("UserController.php");

    $USER_CTRL 	= new UserController();
    
    $data       = $_POST['set_image_crop'];
    
    $RESULT = array(
        'status' => "FAILED"
    );
    
    if($USER_CTRL->setUserSettingByShortField('image', $data)){
        $RESULT['status'] = "OK";
    }
    die(json_encode($RESULT, JSON_PRETTY_PRINT));
}
else if(isset($_POST['add_friend']))
{
    require("UserController.php");

    $userController = new UserController();
    
    $f_user_id      = (int) ($_POST['add_friend']);     // The receiving part of the friend request.
    
    if($userController->sendFriendRequestToUserId($f_user_id)){
        die("Friend request sent!");
    }
    die("Failed to send friend request!");

}
    
else if(isset($_POST['accept_friend_user_id']))
{
    require("UserController.php");

    $USER_CTRL 	= new UserController();

    // User ID of the user that sent the request.
    $REQUEST_UID    = $_POST['accept_friend_user_id'];

    $VAL 		    = $USER_CTRL->acceptFriendRequestFromUserId($REQUEST_UID);

    die(json_encode($VAL, JSON_PRETTY_PRINT));

}
else if(isset($_GET['GET_USER_LIST']))     // Request a list of basic information about all users in DB. TODO: Adaptive query - based on search query
{
    require("PublicUserInfo.php");

    $USER_INFO 	= new PublicUserInfo();
    
    $RESULT     = $USER_INFO->getUserListArray();
    
    die(json_encode($RESULT, JSON_PRETTY_PRINT));
    
}
else if(isset($_POST['SESSION_TIME']))
{
	$TIME = intval($_POST['SESSION_TIME']);
	if (!file_exists('data.txt')) {
		file_put_contents('data.txt', $TIME . "\n");
	} else {
		file_put_contents('data.txt', $TIME . "\n", FILE_APPEND);
	}
}
else
{
    die("Invalid request.");
}