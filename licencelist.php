<?php
    session_start();

    if(isset($_SESSION['game_username']) && ($_SESSION['game_username'] != ""))
    {

    }
    else{
        header("Location: index.php");
    }

	
    if(isset($_SESSION['game_username'])){

       $dbCon = mysqli_connect("localhost", "Stian Arnesen", "dynamicgaming", "motagamedata");

        if($dbCon){

            $session_username = $_SESSION['game_username'];

            $sqlCommands = "SELECT id, username, password, money, level, profile_picture FROM users WHERE username='$session_username'";

            $query = mysqli_query($dbCon, $sqlCommands);
            $data_row = mysqli_fetch_row($query);

            $data_user_id = $data_row[0];
            $data_username = $data_row[1];
            $data_password = $data_row[2];
            $data_money = $data_row[3];
            $data_level = $data_row[4];
			$data_profile_picture = $data_row[5];
        }
        else{
            die("Error");
            $LOGIN_ERR = 2;
        }
}
else{
    header("Location: index.php");
}
?>

<html>
<head>
    <link href="style/shop/style.css" rel="stylesheet" type="text/css">
    <link href="style/licencelist/style.css" rel="stylesheet" type="text/css">

    <title>King of Mota - Shop</title>


</head>
	<body>
	    <?php
	            $btn_file = fopen("layout/top_banner/top_banner.php", "r") or die("Unable to open file!");
	                echo fread($btn_file,filesize("layout/top_banner/top_banner.php"));
	                fclose($btn_file);
	        ?>
	        <div id="licence_list">
                <div id='page_info'>
                    Here is a list of all licences that is available for purchase.<br>
                    A licence gives you the rights to produce and sell items.
                </div>
    	        <?php
                	if ($dbCon) 
                    {
                    	$getLicenceListQuery = "SELECT * FROM licence"; //  WHERE type='$item_type'"
                    	$doLicenceListQuery = mysqli_query($dbCon,$getLicenceListQuery);
                        
                    	echo "<div id='licence_view'>";
                    	while($CURRENT_LICENCE = mysqli_fetch_array($doLicenceListQuery))
                    	{
                    		echo "<div class='licence_item'>";
                    			echo "<div class='licence_title'>";
                    				echo "<h1>". $CURRENT_LICENCE[1] ."</h1>";
                                    echo "<h3>". $CURRENT_LICENCE[2] ."</h3>";
                    			echo "</div>";
                    		echo "</div>";
                    	}
                    	echo "</div>";
                    }
                    else{
                        echo "<h1>Failed to load database!</h1>";
                    	die("Failed to load database!");
                    }

    	        ?>
	        </div>
	</body>


</html>