<?php

	require("utils/new_user/InitUser.php");
	require("utils/new_user/RegisterController.php");
	require("connect/database.php");

	$dbCon = Database::getConnection();		// Connection;

	$regCtr = new RegisterController(); 	// RegisterController;

	if($regCtr){

	}
	else{
		echo "RegisterController failed to load.";
	}

	if(isset($_POST['g-recaptcha-response']))
	{
      	$captcha=$_POST['g-recaptcha-response'];
		if(!$captcha)
		{

		}
		else
		{
			$UNAME 		= $_POST['username'];
			$PASS 		= $_POST['password'];
			$PASS_RE 	= $_POST['password_retype'];
			$MAIL 		= $_POST['email'];
 
			$VALID = $regCtr->validatePostResponse($UNAME, $PASS, $PASS_RE, $MAIL);
			if($VALID)
			{
				$regCtr->addNewUser($UNAME, $PASS, $PASS_RE, $MAIL);
				echo "<h2>VALID input</h2>";
			}
			else
			{
				echo "<h2>Invalid input</h2>";
			}
			
		}
    }
   


?>

<script>
    less = {
        env: "development",
        async: true,
        fileAsync: false,
        poll: 1000,
        functions: {},
        dumpLineNumbers: "comments",
        relativeUrls: false,
        rootpath: ":/a.com/"
    };
</script>
<html>
	<head>
	    <link href="style/register/style.less" rel="stylesheet" type="text/less">

	    <title>King of Mota | Register</title>

	    <script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
	    <script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
		<script src='https://www.google.com/recaptcha/api.js'></script>

		<script src='script/register/register.js'></script>

	</head>
<html>
    <body>
        <div id="top-banner">
        	<div class="top-banner-title">
				King of Mota > Register
        	</div>
        </div>

    	<div class="main">

    		<div class="register-view">
    			
    			<div class="register-title">
					<div class="title">King of Mota | Register</div>
    			</div>

    			<div class="content">
	    			<form method="post" class="register-form">
	    				
	    				<div class="input-view">
	    				<span>Username: </span>
    						<input type="text" id="input-username" name="username" placeholder="">
    						<div class"input-error" id="err-name"></div>
	    				</div>
	    				
	    				<div class="input-view">
    					<span>Password: </span>
							<input type="password" name="password" id="input-pass">
							<div class"input-error" id="err-pass"></div>
	    				</div>

    					<div class="input-view">
    					<span>Retype password: </span>
    						<input type="password" name="password_retype"  id="input-pass-retype">
    						<div class"input-error" id="err-pass-retype"></div>
    					</div>
    					
    					<div class="input-view">
    					<span>E-mail: </span>
    						<input type="email" name="email" placeholder="yep@example.com" id="input-mail">
    						<div class"input-error" id="err-mail"></div>
    					</div>

    					<div class="submit-input-view input-view">
    						<div class="g-recaptcha" data-sitekey="6LcrXAgTAAAAAKFdDvnCQe0JkMkQH10KU4hz_q9l"></div>
						</div>
						<div class="submit-input-view input-view">
    						<input type="submit" name="register_submit" value="Register" name="register" id="input-submit">
						</div>
						
	    			</form>

    			</div>
    		</div>

    	</div>
	</body>

</html>
