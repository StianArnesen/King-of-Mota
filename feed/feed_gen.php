<?php

$ROOT = $_SERVER['DOCUMENT_ROOT'];

include($ROOT. "common/secure.php");
require($ROOT. "utils/se_utils.php");

class StaticFeed
{
    private $DEV_LOG_ENDED;

    private $CONNECTION;

    private $SECURE_CLASS;

    private $SEC_USERNAME;

    private $ROOT;

    private $CURRENT_USER;

    public function __construct()
    {
        $this->ROOT             = $_SERVER['DOCUMENT_ROOT'];

        $this->DEV_LOG_ENDED    = false;


        if ($this->initSecureClass())
        {
            if ($this->_connect())
            {
                if ($this->authenticateUser($this->SEC_USERNAME))
                {

                }
                else {
                    die("SOMETHING_WENT_WRONG");
                }
            }
            else {
                die("SOMETHING_WENT_WRONG");
            }
        }
    }
    private function initSecureClass()
    {
        return $this->SECURE_CLASS = new Secure()? 1:0;
    }
    private function authenticateUser($USERNAME)
    {
        include_once($this->ROOT . "/common/session/sessioninfo.php");
        if($this->CURRENT_USER = new User())
        {
            return true;
        }
        return false;

    }
    private function _connect()
    {
        require_once($this->ROOT. "connect/database.php");

        if($this->CONNECTION = Database::getConnection())
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    public function getFeed($USER_ID, $MAX_RESULT)
    {
        $OUTPUT_RESULT      = "";

        $max_limit          = $this->getValidParam($MAX_RESULT);
        $FEED_LIST          = $this->getFeedList($USER_ID, $max_limit);

        $IMG                = $this->CURRENT_USER->getUserImage();

        $NUMBER_OF_ELEMENTS = 0;

        while($WALL_POST = mysqli_fetch_array($FEED_LIST))
        {
            $NUMBER_OF_ELEMENTS++;

            $WALL_POST_ID       = $WALL_POST['status_id'];

            $WALL_FROM_ID       = $WALL_POST['status_from_user_id'];
            $WALL_TO_ID         = $WALL_POST['status_to_user_id'];
            $POST_DATE_TIME     = $WALL_POST['status_post_date'];

            $POST_UPVOTES       = $WALL_POST['status_upvotes'];

            $WALL_DATA          = $WALL_POST['status_data'];

            $OUTPUT_RESULT .= $this->getFeedElement($WALL_FROM_ID, $WALL_TO_ID, $WALL_DATA, $POST_DATE_TIME, $POST_UPVOTES, $NUMBER_OF_ELEMENTS, $WALL_POST_ID);

            if($this->getWallPostCommentsAmount($WALL_POST_ID) > 0)
            {
                $OUTPUT_RESULT .= $this->getCommentSection($WALL_POST_ID);
            }

            $OUTPUT_RESULT .= "<div class='wall_post_write_comment' id='comment_view_$NUMBER_OF_ELEMENTS'>";
            $OUTPUT_RESULT .=   "<img src='$IMG' class='user_comment_image'>";
            $OUTPUT_RESULT .=   "<textarea placeholder='Write a comment:' id='post-comment-input-id_$NUMBER_OF_ELEMENTS' name='wall_post_data_comment'></textarea>";
            $OUTPUT_RESULT .=   "<input type='hidden' name='wall_post_id' value='$WALL_POST_ID'></input>";
            $OUTPUT_RESULT .=   "<input id='wall_post_index_$NUMBER_OF_ELEMENTS' type='hidden' name='wall_post_index' value='$NUMBER_OF_ELEMENTS'></input>";
            $OUTPUT_RESULT .=   "<button class='comment_post_post_button button-waiting' onclick='write_comment($NUMBER_OF_ELEMENTS, $WALL_POST_ID)' name='commment_submit' value='post'>Post</button>";
            $OUTPUT_RESULT .= "</div>";
        }

        $OUTPUT_RESULT .= "<h3> - - End of feed: $NUMBER_OF_ELEMENTS Elements displayed - - </h3>";

        return $OUTPUT_RESULT;
    }
    private function getCommentSection($STATUS_LINK_ID)
    {
        $RESULT = "";
        $RESULT .= "<div class='wall_post_comment_view'>";


        $COMMENTS_LIST = $this->getWallPostComments($STATUS_LINK_ID, 5);

        if($this->getWallPostCommentsAmount($STATUS_LINK_ID) > 5)
        {
            $RESULT .= "<div class='wall_post_comment_view_show_more'> <button> Show more </button> </div>";
        }

        while ($COMMENT = mysqli_fetch_array($COMMENTS_LIST)) {
            $COMMENT_USER_ID = $COMMENT['from_user_id'];

            $COMMENT_USER_IMAGE = $this->getUserPicture($COMMENT_USER_ID);
            $COMMENT_USER_NAME = $this->getUsername($COMMENT_USER_ID);

            $COMMENT_DATA = $COMMENT['comment_data'];
            $COMMENT_DATE_TIME = $COMMENT['comment_date'];


            $RESULT .= "<div class='wall_post_comment_element'>";

            $RESULT .= "<div class='wall_post_comment_username'><a class='username_field' href='" . $COMMENT_USER_NAME . "'>" . $COMMENT_USER_NAME . "</a> </div>";

            $RESULT .= "<div class='wall_post_comment_picture'>";
            $RESULT .= "<img src='$COMMENT_USER_IMAGE'>";
            $RESULT .= "</div>";

            $RESULT .= "<div class='wall_post_comment_data'>";
            $RESULT .= htmlentities($COMMENT_DATA);
            $RESULT .= "</div>";

            $RESULT .= "<div class='wall_post_comment_date'>";
            $RESULT .= $COMMENT_DATE_TIME;
            $RESULT .= "</div>";

            $RESULT .= "</div>";
        }

        $RESULT .= "</div>";

        return $RESULT;
    }
    public function upvoteStatusPost($STATUS_LINK_ID)
    {

        $a = $this->CURRENT_USER;

        $USER_ID = $a->getUserId();

        $SQL_C_UPVOTE = "SELECT id FROM wall_status_upvotes WHERE (link_id='$STATUS_LINK_ID' AND user_id='$USER_ID') LIMIT 1";
        $QUERY_C_UPVOTE = mysqli_query($this->CONNECTION, $SQL_C_UPVOTE);

        $RESULT_C_UPVOTE = mysqli_fetch_row($QUERY_C_UPVOTE);

        if(! isset($RESULT_C_UPVOTE[0]))
        {
            $this->insertUpvote($STATUS_LINK_ID);

        }
    }
    /*
     *  getUsernameListOfUpVotes();
     *
     *  Desc:
     *      Returns an array-list over username's that have up-voted a given element.
     *
     * */
    public function getUsernameListOfUpVotes($STATUS_LINK_ID, $LINK_TYPE)
    {
        if(isset($STATUS_LINK_ID) && (! is_null($STATUS_LINK_ID)) && is_numeric($STATUS_LINK_ID))
        {
            if(isset($LINK_TYPE) && (! is_null($LINK_TYPE)) && is_numeric($LINK_TYPE))
            {
                $SQL    = "SELECT users.username, users.profile_picture FROM wall_status_upvotes LEFT JOIN users ON wall_status_upvotes.user_id =  users.id WHERE (wall_status_upvotes.link_id='$STATUS_LINK_ID' AND wall_status_upvotes.link_type='$LINK_TYPE') ";
                $QUERY  = mysqli_query($this->CONNECTION, $SQL);

                if($RESULT = mysqli_fetch_all($QUERY))
                {
                    return $RESULT;
                }
                else
                {
                    return mysqli_error_list($this->CONNECTION);
                }
            }
        }

        return null;
    }
    private function insertUpvote($STATUS_LINK_ID)
    {
        $LINK_TYPE          = 0;
        $USER_ID            = $this->CURRENT_USER->getUserId();

        $SQL_GET_USER_TO_ID     = "SELECT status_to_user_id FROM wall_status WHERE status_id = $STATUS_LINK_ID";
        $QUERY_GET_USER_TO_ID   = mysqli_query($this->CONNECTION, $SQL_GET_USER_TO_ID);
        $ROW_GET_USER_TO_ID     = mysqli_fetch_row($QUERY_GET_USER_TO_ID);
        $USER_TO_ID             = $ROW_GET_USER_TO_ID[0];

        $SQL_INSERT             = "INSERT INTO wall_status_upvotes(user_id, link_id, link_type) VALUES ($USER_ID, $STATUS_LINK_ID, $LINK_TYPE)";
        $QUERY_INSERT           = mysqli_query($this->CONNECTION, $SQL_INSERT);

        if ($QUERY_INSERT)
        {
            $SQL_UPDATE = "UPDATE wall_status SET status_upvotes = status_upvotes + 1 WHERE status_id='$STATUS_LINK_ID'";
            $QUERY_UPDATE = mysqli_query($this->CONNECTION, $SQL_UPDATE);

            $root = $_SERVER['DOCUMENT_ROOT'];

            include_once($root . "/notification/insertNotification.php");

            $NOT_CLASS = new NotificationClass();

            $NOT_CLASS->insertNotificationUpvote($USER_ID, $USER_TO_ID);
        } else {
            die("Failed to insert upvote! (IU211))");
        }
    }
    /*
     *  statusUpvotedByUser()
     *
     *  Desc:
     *      Used to check if the current user already have up-voted a given element. (Post, comment)
     *
     * */
    private function statusUpvotedByUser($STATUS_LINK_ID)
    {
        $USER_ID    = $this->CURRENT_USER->getUserId();

        $SQL        = "SELECT id FROM wall_status_upvotes WHERE link_id='$STATUS_LINK_ID' AND user_id='$USER_ID' LIMIT 1";
        $QUERY      = mysqli_query($this->CONNECTION, $SQL);

        $RESULT     = mysqli_num_rows($QUERY);

        if($RESULT != 0)
        {
            return true;
        }
        return false;
    }
    /*
     *  getStatusUpvotes()
     *
     *  Desc:
     *      Used to get the amount of up-votes on a given element.
     *
     * */
    private function getStatusUpvotes($STATUS_LINK_ID)
    {
        $SQL = "SELECT id FROM wall_status_upvotes WHERE status_id='$STATUS_LINK_ID'";
        $QUERY = mysqli_query($this->CONNECTION, $SQL);

        $RESULT = mysqli_num_rows($QUERY);

        return $RESULT;
    }
    private function getWallPostCommentsAmount($STATUS_LINK_ID)
    {
        $SQL = "SELECT comment_id FROM wall_status_comment WHERE status_link_id='$STATUS_LINK_ID'";
        $QUERY = mysqli_query($this->CONNECTION, $SQL);

        $RESULT_NUM_ROWS = mysqli_num_rows($QUERY);

        return $RESULT_NUM_ROWS;
    }
    private function getWallPostComments($STATUS_LINK_ID, $MAX_LIMIT)
    {
        $SQL = "SELECT * FROM wall_status_comment WHERE status_link_id='$STATUS_LINK_ID' LIMIT $MAX_LIMIT";
        $QUERY = mysqli_query($this->CONNECTION, $SQL);

        return $QUERY;
    }
    private function getFeedElement($FROM_ID, $TO_ID, $POST_DATA, $POST_DATE_TIME, $POST_UPVOTES, $ELEMENT_INDEX, $STATUS_ID)
    {
        $RESULT = "";

        $RESULT .= "<div class='post_item'>";
        $WALL_FROM_USERNAME = $this->getUsername($FROM_ID);
        $WALL_TO_USERNAME   = $this->getUsername($TO_ID);

        $WALL_USER_IMAGE = $this->getUserPicture($FROM_ID);

        if(($FROM_ID !== $TO_ID) && (isset($FROM_ID) && isset($TO_ID)) && (! is_null($FROM_ID) && ! is_null($TO_ID)))
        {
            $RESULT .="<div class='wall_post_from_name'>
                            <a class='username_field' href='". $WALL_FROM_USERNAME ."'>" . $WALL_FROM_USERNAME . " </a>
                            ->
                            <a class='username_field' href='". $WALL_TO_USERNAME ."'>" . $WALL_TO_USERNAME . " </a>
                       </div> <div class='debug_info'> From id: ". $FROM_ID ." <br> To id:". $TO_ID ."</div>";
        }
        else
        {
            $RESULT .= "<div class='wall_post_from_name'> <a class='username_field' href='". $WALL_FROM_USERNAME ."'>" . $WALL_FROM_USERNAME . "</a></div>";
        }
        $RESULT .= "<div class='wall_post_from_picture'> <img src='" . $WALL_USER_IMAGE . "'></div>";
        $RESULT .= "<div class='wall_post_item_comment_text'><span>" . $POST_DATA . "</span></div>";
        $RESULT .= "<div class='wall_post_item_date'> " . StaticUtils::time_elapsed_string($POST_DATE_TIME) . "</div>";
        $RESULT .= "<div class='wall_post_item_upvotes' onclick='getUpVoteList($STATUS_ID, 0)'> " .$POST_UPVOTES . " Likes</div>";

        if($this->statusUpvotedByUser($STATUS_ID))
        {
            $RESULT .= "<div class='wall_post_upvote_btn status_common_button'> <button style='background-color: rgba(100,150,100,255)' onclick='likeStatus($STATUS_ID)'>Dislike</button> </div>";
        }
        else
        {
            $RESULT .= "<div class='wall_post_upvote_btn status_common_button'> <button onclick='likeStatus($STATUS_ID)'>Like</button> </div>";
        }


        $RESULT .= "<div class='wall_post_button_comment status_common_button'> <button onclick='slideDown(". "comment_view_" . $ELEMENT_INDEX . ");'>Comment</button> </div>";

        //$RESULT .= "<div class='wall_post_button_comment'> <button onclick='slideDown(". "comment_view_" . $currentCommentViewIndex . ");'>Comment</button> </div>";

        $RESULT .= "</div>";

        return $RESULT;
    }
    private function getUserPicture($ID)
    {
        $SQL = "SELECT profile_picture FROM users WHERE id='$ID'";
        $QUERY = mysqli_query($this->CONNECTION, $SQL);

        $RESULT = mysqli_fetch_row($QUERY);

        $IMAGE = $RESULT[0];

        return $IMAGE;
    }
    private function getUsername($ID)
    {
        $SQL = "SELECT username FROM users WHERE id='$ID'";
        $QUERY = mysqli_query($this->CONNECTION, $SQL);

        $RESULT = mysqli_fetch_array($QUERY);

        return isset($RESULT['username'])?$RESULT['username']: "Deleted user";

    }
    private function getFeedList($USER_ID, $LIMIT)
    {
        $SQL = "SELECT DISTINCT wall_status.* FROM wall_status
                 INNER JOIN friend_list ON
                 (wall_status.status_from_user_id = '$USER_ID'
                  OR wall_status.status_to_user_id = '$USER_ID')
                 ORDER BY wall_status.status_post_date DESC LIMIT $LIMIT";
        $QUERY = mysqli_query($this->CONNECTION, $SQL);

        return $QUERY;
    }
    private function getFriendList()
    {
        $USER_ID = $this->CURRENT_USER->getUserId();

        $SQL = "SELECT * FROM friend_list WHERE (user_id='$USER_ID' OR friend_id='$USER_ID') AND status='1'";
        $QUERY = mysqli_query($this->CONNECTION, $SQL);

        return $QUERY;
    }
    public function getValidParam($PARAM)
    {
        include_once($this->ROOT . "common/secure.php");
        $SECURE = new Secure();

        return $SECURE->STRIP_STRING($PARAM);
    }
    public function dev_log_end()
    {
        if(! $this->DEV_LOG_ENDED)
        {
            //TRACE echo "<h4>- - End of log - - </h4></div>";
            $this->DEV_LOG_ENDED = true;
        }
    }
}
