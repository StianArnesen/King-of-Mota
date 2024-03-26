<?php
	include_once("../domainname.php");

	function calcTime($TIME){

		$TMP_SEC = (time() - $TIME);
		$TMP_MIN = 0;
		$TMP_HOUR = 0;
		$TMP_DAYS = 0;

		$MIN;
		$HOUR;
		$DAYS;

		$FINAL_TIME;


		if($TMP_SEC > 60)
		{
			while($TMP_SEC >= 60)
			{
				$TMP_SEC-=60;
				$TMP_MIN++;
			}
			$MIN = $TMP_MIN;
			if($MIN > 1)
			{
				$FINAL_TIME = $MIN . " minutes";
			}
			else
			{
				$FINAL_TIME = $MIN . " minute";
			}

			if($TMP_MIN > 60)
			{
				while($TMP_MIN >= 60)
				{
					$TMP_MIN-=60;
					$TMP_HOUR++;
				}
				$HOUR = $TMP_HOUR;
				if($HOUR > 1)
				{
					$FINAL_TIME = $HOUR . " hours";
				}
				else
				{
					$FINAL_TIME = $HOUR . " hour";
				}

				if($TMP_HOUR > 24)
				{
					while($TMP_HOUR >= 24)
					{
						$TMP_HOUR-=24;
						$TMP_DAYS++;
					}
					$DAYS = $TMP_DAYS;
					if($DAYS > 1)
					{
						$FINAL_TIME = $DAYS . " days";
					}
					else
					{
						$FINAL_TIME = $DAYS . " day";
					}
				}
			}
		}
		else
		{
			$FINAL_TIME = "Less than one minute";
		}

		$FINAL_TIME .= " ago";
		return $FINAL_TIME;
	}

    $ERROR_MSG;
    $dbCon = mysqli_connect("localhost", "Stian Arnesen", "dynamicgaming", "motagamedata");

    session_start();

    if(isset($_SESSION['game_username']))
    {
        if($dbCon)
        {
            $session_username = $_SESSION['game_username'];

            $sqlCommands = "SELECT id, username, level FROM users WHERE username='$session_username'";

            $query = mysqli_query($dbCon, $sqlCommands);
            $data_row = mysqli_fetch_row($query);

            $data_user_id = $data_row[0];
            $data_username = $data_row[1];
            $data_level = $data_row[2];

            $currentTime = time();

            $setLastActive_query = "UPDATE users SET last_active='$currentTime' WHERE id='$data_user_id'";
            $doLastActiveQuery = mysqli_query($dbCon,$setLastActive_query);

            if(isset($_POST['UN']) && is_numeric($_POST['UN']))
			{
				$notificationId = strip_tags($_POST['UN']);
				$newStatus = 1;
				$validateNotificationQ = "UPDATE notifications SET not_status='$newStatus' WHERE not_id='$notificationId' AND user_id='$data_user_id'";
				$doValidateNotificationQ = mysqli_query($dbCon, $validateNotificationQ);
				echo "Notification marked!";
				exit;
			}
			else if(isset($_GET['type']))
		    {
		    	if($_GET['type'] == 1)
		    	{
		    		$getNotificationsAmount = "SELECT not_id FROM notifications WHERE user_id='$data_user_id' AND not_status='0'";
					$doGetNotificationsAmount = mysqli_query($dbCon, $getNotificationsAmount);
					$notAmount = mysqli_num_rows($doGetNotificationsAmount);

					echo "<input type='hidden' id='notifications_amount_value' value='$notAmount'>";

					echo $notAmount;
					exit;
		    	}
		    	else if($_GET['type'] == 2)
		    	{
		    		$getNotificationsAmount2 = "SELECT not_id FROM notifications WHERE user_id='$data_user_id' AND not_status='1'";
					$doGetNotificationsAmount2 = mysqli_query($dbCon, $getNotificationsAmount2);
					$notAmount2 = mysqli_num_rows($doGetNotificationsAmount2);
					exit;
		    	}
		    }
	    	else
	    	{
			$getNotificationsCom = "SELECT * FROM notifications WHERE user_id='$data_user_id' ORDER BY not_time DESC";
			$doNotificationsCom = mysqli_query($dbCon, $getNotificationsCom);

			$notification_amount = 0;

			while($NOTIFICATION_ROW = mysqli_fetch_array($doNotificationsCom))
			{
				if($NOTIFICATION_ROW['not_status'] == 0)
				{
					$notification_amount++;
				}
				$user_id_a = $NOTIFICATION_ROW['user_id_a'];
				$user_id_b = $NOTIFICATION_ROW['user_id_b'];
				$notification_id = $NOTIFICATION_ROW['not_id'];

				$getUser_A_com = "SELECT username, profile_picture FROM users WHERE id='$user_id_a'";
				$doUserAQ = mysqli_query($dbCon, $getUser_A_com);
				$USER_A_ROW = mysqli_fetch_array($doUserAQ);

				$getUser_B_com = "SELECT username, profile_picture FROM users WHERE id='$user_id_b'";
				$doUserBQ = mysqli_query($dbCon, $getUser_B_com);
				$USER_B_ROW = mysqli_fetch_array($doUserBQ);


				$NOTIFICATION_URL = "";

				if($NOTIFICATION_ROW['not_type'] == 0)
				{
					$NOTIFICATION_URL = "'" . $DOMAIN_NAME . "/friendrequests.php" . "'";
				}
				else if($NOTIFICATION_ROW['not_type'] == 1)
				{
					$NOTIFICATION_URL = "'" . $DOMAIN_NAME . "/" . $USER_B_ROW[0] . "'";
				}
				else if($NOTIFICATION_ROW['not_type'] == 2)
				{
					$NOTIFICATION_URL = "'" . $DOMAIN_NAME . "/" . $data_username . "'";
				}
				else if($NOTIFICATION_ROW['not_type'] == 4)
				{
					$NOTIFICATION_URL = "'" . $DOMAIN_NAME . "/garden.php'";
				}
				else if($NOTIFICATION_ROW['not_type'] == 16)
				{
					$NOTIFICATION_URL = "'" . $DOMAIN_NAME . "/crew.php'";
				}
				else if($NOTIFICATION_ROW['not_type'] == 15)
				{
					$NOTIFICATION_URL = "'" . $DOMAIN_NAME . "/crew.php'";
				}

				echo '<div class="notification_item_status_'. $NOTIFICATION_ROW['not_status'] .'" onclick="markNotification('. $notification_id .', '. $NOTIFICATION_URL .');">';
				echo "<div class='notification_item_content' style='margin: 5px;'>";

				if($NOTIFICATION_ROW['not_type'] == 0) //User friend request recieved
				{
					//User profile picture
					echo "<div class='notification_img'>";
						echo "<img src='". $USER_A_ROW['profile_picture'] . "' style='width: 40px; height: 40px; border: 2px solid black;'>";
					echo "</div>";

					//User username
					echo "<div class='notification_text' style='display: inline-block;'>";
						echo  "Friend request from <a style='color: lightgreen;' href='". $DOMAIN_NAME ."/". $USER_A_ROW[0] ."'>". $USER_A_ROW[0]  ."</a>";
					echo "</div>";
				}
				else if($NOTIFICATION_ROW['not_type'] == 1) //User friend request accepted
				{
					//User profile picture
					echo "<div class='notification_img'>";
						echo "<img src='". $USER_B_ROW['profile_picture'] . "' style='width: 40px; height: 40px; border: 2px solid black;'>";
					echo "</div>";

					//User username
					echo "<div class='notification_text' style='display: inline-block;'>";
						echo  "<a style='color: lightgreen;' href='". $DOMAIN_NAME ."/". $USER_B_ROW[0] ."'>". $USER_B_ROW[0]  ."</a> accepted your friend request!";
					echo "</div>";
				}
				else if($NOTIFICATION_ROW['not_type'] == 2) //User post on your wall.
				{
					//User profile picture
					echo "<div class='notification_img'>";
						echo "<img src='". $USER_A_ROW['profile_picture'] . "' style='width: 40px; height: 40px; border: 2px solid black;'>";
					echo "</div>";

					//User username
					echo "<div class='notification_text' style='display: inline-block;'>";
						echo  "<a style='color: lightgreen;' href='". $DOMAIN_NAME ."/". $USER_A_ROW[0] ."'>". $USER_A_ROW[0]  ."</a> Wrote on your wall!";
					echo "</div>";
				}
				else if($NOTIFICATION_ROW['not_type'] == 5) //User liked your post
				{
					//User profile picture
					echo "<div class='notification_img'>";
						echo "<img src='". $USER_A_ROW['profile_picture'] . "' style='width: 40px; height: 40px; border: 2px solid black;'>";
					echo "</div>";

					//User username
					echo "<div class='notification_text' style='display: inline-block;'>";
						echo  "<a style='color: lightgreen;' href='". $DOMAIN_NAME ."/". $USER_A_ROW[0] ."'>". $USER_A_ROW[0]  ."</a> Liked your post. <br>";
					echo "</div>";
				}
				else if($NOTIFICATION_ROW['not_type'] == 15) //User joined your crew.
				{
					//User profile picture
					echo "<div class='notification_img'>";
						echo "<img src='". $USER_A_ROW['profile_picture'] . "' style='width: 40px; height: 40px; border: 2px solid black;'>";
					echo "</div>";

					//User username
					echo "<div class='notification_text' style='display: inline-block;'>";
						echo  "<a style='color: lightgreen;' href='". $DOMAIN_NAME ."/". $USER_A_ROW[0] ."'>". $USER_A_ROW[0]  ."</a> Joined your crew! <br>";
					echo "</div>";
				}
				else if($NOTIFICATION_ROW['not_type'] == 16) //User requested to join your crew.
				{
					//User profile picture
					echo "<div class='notification_img'>";
						echo "<img src='". $USER_A_ROW['profile_picture'] . "' style='width: 40px; height: 40px; border: 2px solid black;'>";
					echo "</div>";

					//User username
					echo "<div class='notification_text' style='display: inline-block;'>";
						echo  "<a style='color: lightgreen;' href='". $DOMAIN_NAME ."/". $USER_A_ROW[0] ."'>". $USER_A_ROW[0]  ."</a> Requested to join your crew! <br>";

						echo "<div class='notification_buttons'> <button class='button_accept' onclick='crew_accept_user(&quot;$user_id_a&quot;)'>Accept</button> <button class='button_remove'>Remove</button> </div>";
					echo "</div>";
				}
				else
				{
					echo "Error code 98";
				}
				echo "<div class='notification_time' style='font-size: 14px;'>". calcTime($NOTIFICATION_ROW['not_time']) . "</div>";
				echo "</div>"; //notification_item_content
				echo "</div>"; // notification_item.
			}
			if($notification_amount == 0)
			{
				echo "<div class='notification_item_status_1'>
						Notifications will be shown here.
					</div>";
			}
        }
    }
    }
