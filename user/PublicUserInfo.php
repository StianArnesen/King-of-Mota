<?php


/**
 * Class PublicUserInfo
 *
 * 1. Description:
 *      The PublicUserInfo class is used to obtain non-private user info of the given Username/User ID
 *
 * 2. Usage:
 *
 *      1.  - Use public: 'loadUserInfo(string: username)'  to load info by username.
 *          - Or          'loadUserInfoById(int: user_id)'  to load info by user_id.  [RECOMMENDED]
 *
 *      2.  - Use public: 'getUserInfo(void)'               to return the currently loaded user info.
 *
 */


class PublicUserInfo
{

    /**
     * @var
     */
    private $USER;

    /**
     * @var
     */
    private $CONNECTION;

    /**
     * @var bool
     */
    private $CONNECTED;


    /**
     * PublicUserInfo constructor.
     */
    public function __construct()
    {
        if($this->_connect())
        {
            $this->CONNECTED = true;
        }
        else
        {
            $this->CONNECTED = false;
        }
    }

    /**
     * @param null $LIMIT
     * @param null $QUERY
     * @return array|null
     */
    public function getUserListArray($LIMIT = null, $QUERY = null)
    {
        if(is_null($LIMIT)) 
        {
            $SQL = "SELECT username, profile_picture AS image, level FROM users";
            
            if($QUERY = mysqli_query($this->CONNECTION, $SQL))
            {
                $RESULT = array();
                
                while($USER = mysqli_fetch_array($QUERY))
                {
                    $username   = $USER['username'];
                    $image      = $USER['image'];
                    $level      = $USER['level'];
                    
                    $USER_ARRAY = array(
                        'username'  => $username,
                        'image'     => $image,
                        'level'     => $level
                    );
                    
                    array_push($RESULT, $USER_ARRAY);
                }
                return $RESULT;
            }
        }
        return null;
    }

    /**
     * @param null $USERNAME
     * @return bool
     */
    public function loadUserInfo($USERNAME = null)
    {
        if($this->CONNECTED && $USERNAME != null)
        {
            $SQL = "SELECT id, username, profile_picture, header_image, level FROM users WHERE username='$USERNAME' LIMIT 1";

            if($QUERY = mysqli_query($this->CONNECTION, $SQL))
            {
                if($RESULT = mysqli_fetch_array($QUERY))
                {
                    $IMG = $RESULT['profile_picture'];
                    if(file_exists($_SERVER['DOCUMENT_ROOT'] . $IMG))
                    {
                       
                    }
                    else
                    {
                       $IMG = "img/0.jpg";                    
                    }
                    $RESULT['profile_picture'] = $IMG;
                    $this->USER = $RESULT;
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param null $USER_ID
     * @return bool
     */
    public function loadUserInfoById($USER_ID = null)
    {
        if($this->CONNECTED && $USER_ID != null)
        {
            $SQL = "SELECT id, username, profile_picture, header_image, level FROM users WHERE id='$USER_ID' LIMIT 1";

            if($QUERY = mysqli_query($this->CONNECTION, $SQL))
            {
                if($RESULT = mysqli_fetch_array($QUERY))
                {
                    $this->USER = $RESULT;
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getUserInfo()
    {
        return $this->USER;
    }
    public function getUserId(){
        return $this->USER['id'];
    }
    public function getRank($USER_ID_PARAM = null){

        $USER_ID = ($USER_ID_PARAM !== null)? $USER_ID_PARAM : $this->USER['id'];

        $SQL = "SELECT 
        id,
        level,
        current_exp,
        ROW_NUMBER() OVER(ORDER BY level desc) user_rank
        FROM users WHERE user_access_level < 2  ORDER BY user_rank ";

        if($QUERY = mysqli_query($this->CONNECTION, $SQL)){
            $RESULT = array();
            while($ROW = mysqli_fetch_assoc($QUERY))
            {
                if ($ROW['id'] === $USER_ID)
                    $RANK = $ROW['user_rank'];
            }
            return isset($RANK)? $RANK : -1;
        }
        return array(
            'STATUS' => "FAILED",
            'MSG'    => mysqli_error($this->CONNECTION),
        );
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