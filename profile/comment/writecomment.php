<?php

    $ROOT = $_SERVER['DOCUMENT_ROOT'];

    require_once($ROOT. "common/session/sessioninfo.php");
    require_once($ROOT. "connect/database.php");

    $dbCon  = Database::getConnection();
    $USER   = new User();

    if(! $USER->isLoggedIn())
    {
        $RESULT = array(
            'STATUS' => "FAILED",
            'MSG'    => "USER_AUTH_FAILED"
        );

        die(json_encode($RESULT, JSON_PRETTY_PRINT));
    }

    if(isset($_POST['GSPUD']) && isset($_POST['GPUUID'])) // Get status update data, Get original POST_ID to where the comment should go.
    {
        if($dbCon)
        {
            $post_text_data_wall = ($_POST['GSPUD']); // Get status post update data

            if(strlen($post_text_data_wall) >= 3)
            {
                $comment_from       = $USER->getUserId();
                $comment_to_post    = $_POST['GPUUID'];

                $submitStatusQuery  = "INSERT INTO wall_status_comment(status_link_id, from_user_id, comment_data) VALUES('$comment_to_post', '$comment_from', '$post_text_data_wall')";
                //$submitStatusQuery = "INSERT INTO wall_status (status_from_user_id, status_to_user_id, status_data) VALUES ('$status_from', '$status_to', '$post_text_data_wall')";
                
                if($submitQuery = mysqli_query($dbCon, $submitStatusQuery))
                {
                    $RESULT = array(
                        'STATUS' => "OK",
                        'MSG'    => "NAN"
                    );

                    die(json_encode($RESULT, JSON_PRETTY_PRINT));
                }
                else
                {
                    $RESULT = array(
                        'STATUS' => "FAILED",
                        'MSG'    => "COMMENT_INSERT_FAILED"
                    );

                    die(json_encode($RESULT, JSON_PRETTY_PRINT));
                }

                /*
                 * @FUNCTION_REMOVED_ID = 4U650435435345
                 * INSERT NOTIFICATION TO OWNER OF POST
                */
            }
            else
            {
                $RESULT = array(
                    'STATUS' => "FAILED",
                    'MSG'    => "COMMENT_LENGTH_MISMATCH"
                );

                die(json_encode($RESULT, JSON_PRETTY_PRINT));
            }
        }
        else
        {
            $RESULT = array(
                'STATUS' => "FAILED",
                'MSG'    => "CONNECTION_FAILED"
            );

            die(json_encode($RESULT, JSON_PRETTY_PRINT));
        }
    }
    else
    {
        $RESULT = array(
            'STATUS' => "FAILED",
            'MSG'    => "ARG_MISMATCH"
        );

        die(json_encode($RESULT, JSON_PRETTY_PRINT));
    }



/*
 *  @FUNCTION_REMOVED_ID = 4U650435435345
 *
 *
$to_user_id         = $_POST['GPUUID'];
$notification_type  = 2;
$notification_time  = time();

$notification_query = "INSERT INTO notifications (user_id, not_type, user_id_a, user_id_b, not_time) VALUES ($to_user_id, $notification_type, )";



if($insert_notification_query = mysqli_query($dbCon, $notification_query))
{
$RESULT = array(
'STATUS' => "OK",
'MSG'    => "NAN"
);

die(json_encode($RESULT, JSON_PRETTY_PRINT));
}
else
{
$RESULT = array(
'STATUS' => "FAILED",
'MSG'    => "NOTIFICATION_INSERT_FAILED"
);

die(json_encode($RESULT, JSON_PRETTY_PRINT));
}*/

?>




