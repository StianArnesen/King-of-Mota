<?php


/**
 * Class UserController
 */
class UserController
{
    /**
     * @var mysqli
     */
    private $CONNECTION;

    /**
     * @var User
     */
    private $USER;

    /**
     * @var array
     */
    private $USER_SETTINGS;

    /**
     * UserController constructor.
     */
    public function __construct()
	{
		if($this->_connect())
		{
			if($this->_loadUser())
			{
				if($this->_loadUserSettings())
				{

				}
				else
				{
					return array(
					"STATUS" => "FAILED",
					"debug_msg" => "USER_SETTINGS_FAIL"
					);
				}
			}
			return array(
				"STATUS" => "FAILED",
				"debug_msg" => "USER_FAIL"
				);
		}
		else
		{
			return array(
				"STATUS" => "FAILED",
				"debug_msg" => "DBCON_FAIL"
				);
		}
	}

    /**
     * @param int $USER_ID
     *
     * @return bool|mysqli_result
     */
    public function getUserAwardList(int $USER_ID)
	{

		$SQL = "SELECT * FROM award_list INNER JOIN user_award_list ON user_award_list.award_link_id = award_list.id WHERE user_award_list.user_id = $USER_ID";
		if($QUERY = mysqli_query($this->CONNECTION, $SQL))
		{
			return $QUERY;
		}
		return false;
	}

    /**
     * @param $field
     * @param $data
     *
     * @return bool
     */
    public function setUserSettingByShortField($field, $data){
	    
	    switch ($field)
        {
            case "image":
                return $this->setUserSettingsField('profile_image_crop', $data);
        }

        return false;
    }

    /**
     * @param $field
     * @param $data
     *
     * @return bool
     */
    private function setUserSettingsField($field, $data){
	    $clean_data     = mysqli_real_escape_string($this->CONNECTION, $data);
        $SETTINGS_ID    = $this->USER_SETTINGS['id'];
            
	    $SQL            = "UPDATE user_settings SET '$field'='$clean_data' WHERE id='$SETTINGS_ID' LIMIT 1";
	    
	    if($QUERY = mysqli_query($this->CONNECTION, $SQL)){
	        return true;
        }
        return false;
    }

    /**
     * @return array
     */
    public function getPublicUserSettings(){
        
        $RESULT = array(
            'image_crop' => $this->USER_SETTINGS['profile_image_crop']
        );
        
        return $RESULT;
    }

    /**
     * @return bool
     */
    private function _loadUserSettings()
	{
		$USER_ID = $this->USER->getUserId();
		$SQL = "SELECT * FROM user_settings WHERE user_id = '$USER_ID' LIMIT 1";

		if($QUERY = mysqli_query($this->CONNECTION, $SQL))
		{
			if($RESULT = mysqli_fetch_array($QUERY))
			{
                $ID         = $RESULT['id'];
				$MUTE 		= $RESULT['sound_mute'];
				$SHOW_RANK 	= $RESULT['rank_hidden'];
				$GFX_LEVEL 	= $RESULT['graphics_stage'];
				$IMG_CROP   = $RESULT['profile_image_crop'];

				$this->USER_SETTINGS = array(
				    "id"            => $ID,
					"sound_mute" 	=> $MUTE,
					"rank_hidden" 	=> $SHOW_RANK,
					"gfx_level" 	=> $GFX_LEVEL,
                    "profile_image_crop" => $IMG_CROP,
					);
                return true;
			}
		}
        return false;
	}

    /**
     * @param int $user_id
     *
     * @return int
     */
    private function getFriendshipStatus(int $user_id) : int
    {
        $c_user_id = $this->USER->getUserId();

        $checkFriendStatusQuery = "SELECT status FROM friend_list WHERE (friend_id='$user_id' AND user_id='$c_user_id') OR (friend_id='$c_user_id' AND user_id='$user_id')";
        $doCheckFriendStatus    = mysqli_query($this->CONNECTION, $checkFriendStatusQuery);

        $FRIEND_STATUS          = mysqli_fetch_row($doCheckFriendStatus);

        $STATUS                 = $FRIEND_STATUS[0];
        
        return ($STATUS == null)? -1: $STATUS;
    }

    /**
     * @param int $user_id
     *
     * @return bool
     */private function sendFriendRequestQuery(int $user_id) : bool{
        
        $currentUserId = $this->USER->getUserId();
        
        /*
         * Check for existing friendship relation in db. 
         *  -> confirm status if existing found.
         * */
        if( ($friend_status = $this->getFriendshipStatus($user_id)) != null )
        {
            /*
             * status == 2 -    "Already sent and awaiting"
             * status == x -    "???"
             * */
            
            if($friend_status == 2)
            {
                // existing found
                //  -> Status not valid. (already awaiting request)
                return false;
            }
            
            // Send the friend request.
            $addFriendQuery         = "INSERT INTO friend_list (user_id, friend_id) VALUES ('$currentUserId', '$user_id')";
            if($doAddFriendQuery    = mysqli_query($this->CONNECTION, $addFriendQuery))
            {
                //  The friend request was added to db.
                //   ->Insert notification to the receiving part of the friend request.
                
                $notificationType   = 0;
                $notificationTime   = time();
                $addNotificationQ   = "INSERT INTO notifications (user_id, not_type, user_id_a, not_time) VALUES ('$user_id', '$notificationType', '$currentUserId', '$notificationTime')";
                
                if(mysqli_query($this->CONNECTION, $addNotificationQ)){
                    return true;
                }        
            }
        }
        return false;
    }

    /**
     * @param int $user_id
     *
     * @return bool
     */
    public function sendFriendRequestToUserId(int $user_id) : bool
    {
        
        return $this->sendFriendRequestQuery($user_id);
    }
    
    /**
     * @param int $friend_user_id
     *
     * @return bool
     */
    public function friendWith(int $friend_user_id) : bool 
    {
        $user_id = $this->USER->getUserId();
        
        $sql = "SELECT status FROM friend_list WHERE (user_id='$friend_user_id' AND friend_id='$user_id') OR (user_id='$user_id' AND friend_id='$friend_user_id')";
        
        if($query = mysqli_query($this->CONNECTION, $sql)) {
            if($status = mysqli_fetch_row($query)){
                if(! is_null($status[0]) && isset($status)){
                    return true;
                }
            }
        }
        return false;
    }
    /**
     * @param $FROM_USER_ID
     *
     * @return array
     */
    public function acceptFriendRequestFromUserId($FROM_USER_ID)
    {
        $USER_ID = $this->USER->getUserId();
        
            $SQL = "SELECT id, status FROM friend_list WHERE (friend_id='$FROM_USER_ID' AND user_id='$USER_ID')  OR (friend_id='$USER_ID' AND user_id='$FROM_USER_ID')LIMIT 2";

            if($QUERY = mysqli_query($this->CONNECTION, $SQL))
            {
                if($RESULT = mysqli_fetch_array($QUERY))
                {
                    $ID     = $RESULT['id'];
                    $STATUS = $RESULT['status'];

                    if($STATUS == 2)
                    {
                        if($this->updateFriendStatus($ID, 1))
                        {
                            return array(
                                'STATUS' => "OK"
                            );
                        }
                        return array(
                            'STATUS'    => "FAILED",
                            'MSG'       => "STATUS_UPDATE_FAILED"
                        );
                    }
                    return array(
                        'STATUS'    => "FAILED",
                        'MSG'       => "STATUS_PROPERTIES_INVALID"
                    );
                }
                return array(
                    'STATUS'    => "FAILED",
                    'MSG'       => "QUERY_FETCH_FAILED"
                );
            }
            return array(
                'STATUS'    => "FAILED",
                'MSG'       => "QUERY_FAILED"
            );
    }

    /**
     * @param $ID
     * @param $STATUS
     *
     * @return bool
     */
    private function updateFriendStatus($ID, $STATUS)
    {

        $SQL = "UPDATE friend_list SET status = '$STATUS' WHERE id='$ID' LIMIT 1";

        if($QUERY = mysqli_query($this->CONNECTION, $SQL))
        {
            return true;
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getUserSettings()
	{
		return $this->USER_SETTINGS;
	}

    /**
     * @return bool
     */
    private function _loadUser()
	{
		$ROOT = $_SERVER['DOCUMENT_ROOT'];

		include_once($ROOT . "/common/session/sessioninfo.php");

		if($this->USER = new User(-1, -1))
		{
			return true;
		}
		return false;
	}

    /**
     * @return bool
     */
    private function _connect()
	{
		$ROOT = $_SERVER['DOCUMENT_ROOT'];

		require_once($ROOT . "/connect/database.php");
		if($this->CONNECTION = Database::getConnection())
		{
			return true;
		}

		return false;
	}
}
