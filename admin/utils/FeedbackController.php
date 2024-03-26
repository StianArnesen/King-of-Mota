<?php

/**
 * Created by PhpStorm.
 * User: Stiarn
 * Date: 04.02.2017
 * Time: 16:27
 */

define("ROOT_DIR", $_SERVER['DOCUMENT_ROOT']);

class FeedbackController
{
    private $CONNECTION;
    
    private $USER;
    
    
    public function __construct()
    {
        if($this->_connect())
        {
            if($this->_loadAndAuthUser())
            {
                
            }
            else{ exit; }
        }
        else{ exit; }
    }
    public function getFeedbackListArray(int $limit)
    {
        $query  = $this->getFeedbackListQuery($limit);
        
        $result = array(); 
        
        while($post = mysqli_fetch_array($query))
        {
            $post_id        = $post['id'];
            $post_user_id   = $post['user_id'];
            $post_title     = $post['title'];
            $post_data      = $post['data'];
            $post_time      = $post['post_time'];
            $post_status    = $post['status'];
            $post_category  = (int) $post['post_category'];
            
            $user_username  = $post['username'];
            $user_image     = $post['profile_picture'];
            
            $post_array = array(
                'id'            => $post_id,
                'user_id'       => $post_user_id,
                'title'         => $post_title,
                'data'          => $post_data,
                'post_time'     => $post_time,
                'status'        => $post_status,
                'post_category' => $post_category,
                
                'username'      => $user_username,
                'user_image'    => $user_image
            );
            
            array_push($result, $post_array);
        }
        return $result;
    }
    public function updatePostStatus(int $id, int $status){
        $sql = "UPDATE feedback_list SET status=$status WHERE id=$id LIMIT 1"; 
        
        if(mysqli_query($this->CONNECTION, $sql)){
            return array(
                'STAUTS'    => "OK",
                'ERR_MSG'   => mysqli_error_list($this->CONNECTION)
            );
        }
        return array(
            'STAUTS'    => "FAILED",
            'ERR_MSG'   => mysqli_error_list($this->CONNECTION)
        );
    }
    private function getFeedbackListQuery(int $limit)
    {
        $sql = "SELECT feedback_list.*, users.username, users.profile_picture FROM feedback_list INNER JOIN users ON users.id = feedback_list.user_id ORDER BY feedback_list.post_time DESC LIMIT $limit";
        
        if($query = mysqli_query($this->CONNECTION, $sql)){
            return $query;
        }
        return null;
    }

    /*
     *      |-------------------------------------------------------|
     *      | AUTHENTICATION -> DON'T CHANGE SHIT BELOW THIS POINT! |
     *      |                                                       |
     *      |-------------------------------------------------------|
     * */

    private function _loadAndAuthUser()
    {
        require_once ROOT_DIR ."common/session/sessioninfo.php";
        if($USER_TMP = new User())
        {
            if($USER_TMP->isLoggedIn())
            {
                if($USER_TMP->isUserAdmin())
                {
                    if($this->USER = $USER_TMP)
                    {
                        return true;
                    }
                }
            }
        }
        return false;
    }
    private function _connect()
    {
        require_once(ROOT_DIR . "connect/database.php");

        if($this->CONNECTION = Database::getConnection())
        {
            return true;
        }
        return false;
    }
}

/*
 *  END OF CLASS
 * */


// Public input-output
if(isset($_POST['get_feedback_list'])){

    $feedbackController = new FeedbackController();
    
    $post_limit = $_POST['get_feedback_list'];
    
    $feedback_list  = $feedbackController->getFeedbackListArray($post_limit);
    $json_list      = json_encode($feedback_list, JSON_PRETTY_PRINT);
    
    die($json_list);
    
}
else if(isset($_POST['set_single_status'])){

    $feedbackController     = new FeedbackController();
    
    $id     = $_POST['issue_id'];
    $status = $_POST['issue_status'];
    
    $status_update_result   = $feedbackController->updatePostStatus($id, $status);

    $json_list = json_encode($status_update_result, JSON_PRETTY_PRINT);
    
    die($json_list);

}