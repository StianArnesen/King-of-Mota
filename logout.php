<?php
	session_start();
    session_destroy();
	header( "Refresh:1; url=index.php", true, 303);
    echo "<h1>Logout successful!</h1>";
	
?>

<html>
	<body>
	
	<script>
	
		function clearCookie(cname){
			document.cookie = cname + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC"; 
		}
		
		clearCookie("user_money_html");
		clearCookie("user_level_html");
		clearCookie("user_profile_image_html");
		clearCookie("user_profile_username_html");
		
	</script>
	</body>
</html>