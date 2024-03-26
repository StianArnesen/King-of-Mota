<?php

class FeedbackController
{
    /**
     * @var mysqli
     */
    public $CONNECTION;

    /**
     * @var User
     */
    private $USER;

    /**
     * FeedbackController constructor.
     */
    public function __construct()
    {
        if($this->_connect()) {
            if ($this->_loadUser()) {
                
            }
            else{
                exit(2);
            }
        }
        else{
            exit(1);
        }
    }

    /**
     *  Desc: The first and only public function to run when inserting new feedback row to DB.
     * 
     * @param $title:           Title for the content. 
     * @param $data:            The 'feedback text' that the user have written
     * @param $post_category:   Category for feedback item.
     * 
     * @return bool: Returns true if the insertion was successful.
     */
    public function insertNewFeedbackItem($title, $data, $post_category)
    {
        $post_time  = time();                   // The date of row insertion (Unix time).
        $user_id    = $this->USER->getUserId();
        
        $data       = mysqli_real_escape_string($this->CONNECTION, $data);
        $title      = mysqli_real_escape_string($this->CONNECTION, $title);
        
        
        $SQL = "INSERT INTO feedback_list (data, post_category, title, user_id) VALUES ('$data', '$post_category', '$title', '$user_id')";
        
        if($QUERY = mysqli_query($this->CONNECTION, $SQL)){
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    private function _loadUser()
    {
        $USER = null;
        require_once(__DIR__ . "/../../common/session/sessioninfo.php");
        if($USER = new User(-1,-1))
        {
            $this->USER = $USER;

            if($USER->isLoggedIn())
            {
                $this->USER_ID = $USER->getUserId();
                return true;
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    private function _connect()
    {
        require_once( __DIR__ . "/../../connect/Database.php");

        if($this->CONNECTION = Database::getConnection())
        {
            return true;
        }
        return false;
    }
}

if(isset($_POST['insert_feedback']))
{
    $title          = $_POST['feedback_title'];
    $data           = $_POST['feedback_data'];
    $post_category  = $_POST['feedback_category'];
    
    if(isset($title) && isset($data) && isset($post_category))
    {
        if(! (is_null($title) || is_null($data) || is_null($post_category))) 
        {
            $feedbackController = new FeedbackController();

            if($feedbackController->insertNewFeedbackItem($title, $data, $post_category))
            {
                $RESULT = array(
                    'RESPONSE_CODE' => 0,
                    'STATUS'        => "OK"
                );
            }
            else
            {
                $SQL_ERR = mysqli_error($feedbackController->CONNECTION);
                
                $RESULT = array(
                    'RESPONSE_CODE' => 1,
                    'STATUS'        => "FAILED",
                    'DBUG_MSG'      => $SQL_ERR
                );
            }
        }
        else
        {
            $RESULT = array(
                'RESPONSE_CODE' => 2,
                'STATUS'        => "FAILED"
            );
        }
    }
    else
    {
        $RESULT = array(
            'RESPONSE_CODE' => 3,
            'STATUS'        => "FAILED"
        );
    }
    
    die(json_encode($RESULT, JSON_PRETTY_PRINT));
    
}