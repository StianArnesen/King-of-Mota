<?php
	$dbCon = mysql_connect("localhost", "Stian Arnesen", "dynamicgaming", "motagamedata");

	if($dbCon)
	{
		$requestId = $_GET['UID'];

		mysql_select_db("motagamedata");
		$commandQuery = "SELECT last_active FROM users WHERE id='$requestId'";

		$doQuery = mysql_query($commandQuery);

		while($ITEM = mysql_fetch_array($doQuery))
		{
			if($ITEM[0] == 0)
			{
				continue;
			}
			else
			{
				$lastActiveTMP_seconds = ((time()-$ITEM[0]));
				$lastActiveTMP_minutes = 0;

				$lastActive_seconds = ((time()-$ITEM[0]))%60;
				$lastActive_minutes = 0;
				$lastActive_hour = 0;
				if($lastActiveTMP_seconds > 59)
				{
					while($lastActiveTMP_seconds > 59)
					{
						$lastActiveTMP_seconds-=60;
						$lastActive_minutes++;
						$lastActiveTMP_minutes++;
					}
					if($lastActiveTMP_minutes < 60)
					{
						//echo "User: '" . $ITEM[1] . "' was last online " . $lastActive_minutes . " minute(s) and " . $lastActive_seconds . " seconds ago. <br>";	
					}
					else
					{
						while($lastActiveTMP_minutes >= (60))
						{
							$lastActiveTMP_minutes-=(60);
							$lastActive_hour++;
						}
						$lastActive_minutes = ($lastActive_minutes%60);
						//echo "User: '" . $ITEM[1] . "' was last online " . $lastActive_hour . " hour(s) and " . $lastActiveTMP_minutes . " minute(s) ago. <br>";
					}
					
				}
				else
				{
					echo "Online";
					//echo "User: '" . $ITEM[1] . "' was last online '" . $lastActive_seconds . "' seconds ago. <br>";
				}
				//echo "<br> <br>";
			}
			
		}
	}
?>