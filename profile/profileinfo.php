<?php


header('Content-Type: text/html; charset=UTF-8');


require(__DIR__ . "/../utils/se_utils.php");

    echo '<head> <meta charset="UTF-8"> </head>';
	$wallConnect = mysqli_connect("localhost", "Stian Arnesen", "dynamicgaming", "motagamedata");
	
	if($wallConnect)
	{
		$user_name = $_GET['profile_username'];
        if(isset($_GET['max_post_value']))
        {
		   $load_amount = $_GET['max_post_value'];
        }
        else
        {
            $load_amount = 5;
        }
        
        $get_userinfo_query = "SELECT * FROM users WHERE username='$user_name'";
        $do_get_userinfo = mysqli_query($wallConnect, $get_userinfo_query);
        $current_user_row = mysqli_fetch_row($do_get_userinfo);

        $user_id = $current_user_row[0];
        
        $getWallPostSizeQuery = "SELECT status_id FROM  wall_status WHERE status_to_user_id='" . $user_id . "'";
        
        $doWallPostSizeQuery = mysqli_query($wallConnect, $getWallPostSizeQuery);

        $wallPostSize = mysqli_num_rows($doWallPostSizeQuery);

        echo "<input type='hidden' id='wall_total_post_max' value='".  $wallPostSize  ."'>";

		$getDataQuery = "SELECT * FROM wall_status WHERE status_to_user_id='" . $user_id . "' ORDER BY status_post_date DESC LIMIT ". $load_amount;
		
		$doDataQuery = mysqli_query($wallConnect, $getDataQuery);
        
        
        $currentCommentViewIndex = 0;

		while($CURRENT_POST = mysqli_fetch_array($doDataQuery))
		{
			echo "<div class='post_item'>";
			$userInfoQuery = "SELECT * FROM users WHERE id='" . $CURRENT_POST[1] . "'";
			
			$doInfoQuery =  mysqli_query($wallConnect, $userInfoQuery);
			
			$userRow = mysqli_fetch_array($doInfoQuery);


            $POST_DATE_TIMESTAMP = strtotime($CURRENT_POST['status_post_date']);

            $TIME_SINCE_POST = time() - $POST_DATE_TIMESTAMP;

            $TIME_SINCE_POST = StaticUtils::getTimeFormatted($TIME_SINCE_POST);

            if($TIME_SINCE_POST['years'] > 0)
            {
                $FINAL_DATE_STRING = $TIME_SINCE_POST['years'] . " year(s) ago";
            }
            else if($TIME_SINCE_POST['months'] > 0)
            {
                if($TIME_SINCE_POST['months'] > 1)
                {
                    $FINAL_DATE_STRING = $TIME_SINCE_POST['months'] . " months ago";
                }
                else
                {
                    $FINAL_DATE_STRING = $TIME_SINCE_POST['months'] . " month ago";
                }

            }
            else if($TIME_SINCE_POST['days'] > 0)
            {
                if($TIME_SINCE_POST['days'] > 1)
                {
                    $FINAL_DATE_STRING = $TIME_SINCE_POST['days'] . " days ago";
                }
                else
                {
                    $FINAL_DATE_STRING = $TIME_SINCE_POST['days'] . " day ago";
                }
            }
            else if($TIME_SINCE_POST['hours'] > 0)
            {
                if($TIME_SINCE_POST['hours'] > 1)
                {
                    $FINAL_DATE_STRING = $TIME_SINCE_POST['hours'] . " hours ago";
                }
                else
                {
                    $FINAL_DATE_STRING = $TIME_SINCE_POST['days'] . " hour ago";
                }
            }
            else if($TIME_SINCE_POST['minutes'] > 0)
            {
                if($TIME_SINCE_POST['minutes'] > 1)
                {
                    $FINAL_DATE_STRING = $TIME_SINCE_POST['minutes'] . " minutes ago";
                }
               
            } 
            else
            {
                $FINAL_DATE_STRING = "Just now";
            }

            if($CURRENT_POST[1] != $CURRENT_POST[2])
            {
                echo "<div class='wall_post_from_name'>From <a class='username_field' href='". $userRow[1] ."'>" . $userRow[1] . "</a> </div>";
            }
            else
            {
                echo "<div class='wall_post_from_name'> <a class='username_field' href='". $userRow[1] ."'>" . $userRow[1] . "</a></div>";
            }
			echo "<div class='wall_post_from_picture'> <img src='" . $userRow[9] . "'></div>";
			echo "<div class='wall_post_item_comment'>" . strip_tags($CURRENT_POST[3]) . "</div>";
			echo "<div class='wall_post_item_date'> " . $FINAL_DATE_STRING . "</div>";
            echo "<div class='wall_post_item_upvotes'> " . $CURRENT_POST[5] . " Likes</div>";
            echo "<div class='wall_post_upvote_btn'> <button> Like </button> </div>";
            echo "<div class='wall_post_button_comment'> <button onclick='slideDown(". "comment_view_" . $currentCommentViewIndex . ");'>Comment</button> </div>";
			
            echo "</div>";
            
            $commentQuery = "SELECT * FROM wall_status_comment WHERE status_link_id='" . $CURRENT_POST[0] . "'";
            
            $doCommentQuery =  mysqli_query($wallConnect,$commentQuery);
            
            $doCommentQueryTest =  mysqli_query($wallConnect,$commentQuery);
            $commentRowTest = mysqli_fetch_row($doCommentQueryTest);


            if(isset($commentRowTest[0]))
            {
                   
            }
            while($commentRow = mysqli_fetch_row($doCommentQuery))
            {

                $TIME_SINCE_COMMENT = time() - strtotime($commentRow[4]);

                $userCommentInfoQuery = "SELECT * FROM users WHERE id='" . $commentRow[2] . "'";
            
                $doUserCommentInfoQuery =  mysqli_query($wallConnect,$userCommentInfoQuery);
            
                $userCommentRow = mysqli_fetch_row($doUserCommentInfoQuery);

                if(isset($commentRow[0]))
                {
                    echo "<div class='wall_post_comment_view'>";
                        
                        echo "<div class='wall_post_comment_username'><a class='username_field' href='". $userCommentRow[1] . "'>" . $userCommentRow[1] . "</a> </div>";
                        echo "<div class='wall_post_comment_picture'>";
                            echo "<img src='$userCommentRow[9]'>";
                        echo "</div>";

                        echo "<div class='wall_post_comment_data'>";
                            echo strip_tags($commentRow[3]);
                    echo "</div>";
                        echo "<div class='wall_post_comment_date'>";
                            echo getTotalTimeSincePost($TIME_SINCE_COMMENT);
                        echo "</div>";

                    echo "</div>";
                }
            }
            echo "<div class='wall_post_write_comment' id='comment_view_". $currentCommentViewIndex ."'>";
                echo "<form name='comment_post_form' method='post' action='" . $user_name . "'>";
                    echo "<textarea placeholder='Write a comment:' name='wall_post_data_comment'></textarea>";
                    echo "<input type='hidden' name='wall_post_id' value='". $CURRENT_POST[0] . "'></input>";
                    echo "<input id='wall_post_index_". $currentCommentViewIndex . "' type='hidden' name='wall_post_index' value='". $currentCommentViewIndex . "'></input>";
                    echo "<input class='comment_post_post_button' type='submit' name='commment_submit' value='post'>";
                echo "</form>";
            echo "</div>";
            $currentCommentViewIndex++;
		}
	}
	else
	{
		die("Could not load feed!");
	}
	echo "</div>";


    function getTotalTimeSincePost($TIME_SINCE_POST)
    {

        $FINAL_DATE_STRING = "";

        $TIME_SINCE_POST = StaticUtils::getTimeFormatted($TIME_SINCE_POST);

        if($TIME_SINCE_POST['years'] > 0)
        {
            $FINAL_DATE_STRING = $TIME_SINCE_POST['years'] . " year(s) ago";
        }
        else if($TIME_SINCE_POST['months'] > 0)
        {
            if($TIME_SINCE_POST['months'] > 1)
            {
                $FINAL_DATE_STRING = $TIME_SINCE_POST['months'] . " months ago";
            }
            else
            {
                $FINAL_DATE_STRING = $TIME_SINCE_POST['months'] . " month ago";
            }

        }
        else if($TIME_SINCE_POST['days'] > 0)
        {
            if($TIME_SINCE_POST['days'] > 1)
            {
                $FINAL_DATE_STRING = $TIME_SINCE_POST['days'] . " days ago";
            }
            else
            {
                $FINAL_DATE_STRING = $TIME_SINCE_POST['days'] . " day ago";
            }
        }
        else if($TIME_SINCE_POST['hours'] > 0)
        {
            if($TIME_SINCE_POST['hours'] > 1)
            {
                $FINAL_DATE_STRING = $TIME_SINCE_POST['hours'] . " hours ago";
            }
            else
            {
                $FINAL_DATE_STRING = $TIME_SINCE_POST['days'] . " hour ago";
            }
        }
        else if($TIME_SINCE_POST['minutes'] > 0)
        {
            if($TIME_SINCE_POST['minutes'] > 1)
            {
                $FINAL_DATE_STRING = $TIME_SINCE_POST['minutes'] . " minutes ago";
            }

        }
        else
        {
            $FINAL_DATE_STRING = "Just now";
        }
        return $FINAL_DATE_STRING;
    }

?>
		
		