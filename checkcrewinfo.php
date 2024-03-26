<?php

	if(isset($_POST['ICN']))
	{
		$ICN = strtolower(strip_tags(($_POST['ICN'])));

		$dbCon = mysqli_connect("localhost", "Stian Arnesen", "dynamicgaming", "motagamedata");
		$checkCrewName = "SELECT * FROM crew WHERE crew_name='$ICN'";
		$doQuery = mysqli_query($dbCon, $checkCrewName);

		$crew_row = mysqli_fetch_row($doQuery);

		if(isset($crew_row[0]))
		{
			echo "<div style='color:rgba(250,110,110,1);'>Sorry, this name is already taken!</div>";
		}
		else
		{
			echo "<div style='color:green;'>Name valid</div>";
		}
	}
?>