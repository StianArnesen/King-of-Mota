<?php


/**
 * Class UserEditorController
 */
class UserEditorController
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
     * UserEditorController constructor.
     */
    public function __construct()
    {
        if($this->_connect()){
            if($this->_authUser()){
                
            }
            else
            {
                http_response_code(404);
                die("Failed to load user");
            }
        }
    }
    public function getUserListJson(int $limit): string
    {
        return json_encode($this->getUserListArray($limit), JSON_PRETTY_PRINT);
    }
    private function getUserListArray(int $limit)
    {
        
        $QUERY = $this->getUserListQuery($limit);

        $RESULT = array();

        while($USER_ARRAY = mysqli_fetch_array($QUERY))
        {
            $USER = $this->getUserInfoArray($USER_ARRAY);

            array_push($RESULT, $USER);
        }
        return $RESULT;
    }
    private function getUserInfoArray(array $USER): array 
    {
        return array(
            'user_id'           => $USER['id'],
            'username'          => $USER['username'],
            'profile_picture'   => $USER['profile_picture'],
            'last_active'       => $USER['last_active'],
        );
    }
    private function getUserListQuery(int $limit): array
    {

        $SQL = "SELECT id, username, profile_picture, last_active, level, activated, money, g_coins FROM users ORDER BY last_active DESC LIMIT $limit";

        if($QUERY = mysqli_query($this->CONNECTION, $SQL))
        {
            return $QUERY;
        }
        return null;
    }
    
    
    
    
    
    
    /*
     * Authentication and connection -> don't touch.
     * 
     * */
    
    
    /**
     * @return bool
     */
    private function _authUser(): bool{
        require_once (__DIR__ . "/../../common/session/sessioninfo.php");
        
        if($this->USER = new User()){
            if($this->USER->isUserAdmin()){
                return true;
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    private function _connect(): bool {
        require_once(__DIR__ . "/../../connect/database.php");
        
        if($this->CONNECTION = Database::getConnection()){
            if(! is_null($this->CONNECTION)){
                return true;
            }
        }
        return false;
        
    }
}