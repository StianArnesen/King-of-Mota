<?php 

	$dbCon = mysqli_connect("localhost", "Stian Arnesen", "dynamicgaming", "motagamedata");

	if(isset($_GET["AUAC"]) && isset($_GET["AUID"]))
	{
		$activate_code = $_GET["AUAC"];
		$activate_userid = $_GET["AUID"];

		$getActivationCode = "SELECT * FROM activation WHERE user_id='$activate_userid' AND unique_id='$activate_code'";
		$doGetActivationCode = mysqli_query($dbCon,$getActivationCode);

		$activationRow = mysqli_fetch_row($doGetActivationCode);

		if(isset($activationRow[0]))
		{
			$user_id = $activationRow[1];
			$updateUserActivated = "UPDATE users SET activated='1' WHERE id='$user_id'";
			$doUpdateActivated = mysqli_query($dbCon, $updateUserActivated);

			echo "<div style='color: green;'> <h1>Your account has been activated! </h1>";
			header( "Refresh:2; url=index.php?msg_enc_conf=2", true, 303);
		}
		else
		{
			echo "<div style='color: red;'> <h1>Failed to activate account!</h1>";
		}

	}

?>