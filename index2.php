<?php
    session_start();

    include("admin/admin_log.php");
    require("prepared/Database.php");

    $n_dbCon = PreparedDatabase::getConnection();

    if($n_dbCon->connect_error)
    {
        die("<h1>Connection failed!: " . $n_dbCon->connect_error . "</h1>");
    }
    else
    {

    }

    $LOGIN_ERR = -1;
    if(isset($_SESSION['game_username']))
    {
        header("Location: home.php");
    }
    else if(isset($_POST['username']))
    {


        if(! $n_dbCon->connect_error)
        {
            $input_username = ($_POST['username']);
            $input_password = ($_POST['password']);



            $get_user_query = "SELECT id, username, password, money, level, salt, activated FROM users WHERE username=?";

            $stmt = $n_dbCon->prepare($get_user_query);


            if( !$stmt->bind_param('s', $input_username) || !$stmt->execute()){
                die("Failed to bind parameters!");
            }

            $stmt->bind_result($data_user_id, $data_username, $data_password, $data_money, $data_level, $user_salt, $user_activated);

            $stmt->fetch();

            if($input_username == $data_username && ($data_password == crypt($input_password, $user_salt)))
            {
                if($user_activated == 1)
                {
                    $_SESSION['game_username'] = $data_username;
                    $_SESSION['game_money'] = $data_money;
                    $_SESSION['game_level'] = $data_level;
                    $_SESSION['game_user_id'] = $data_user_id;

                    $currentTime = time();

                    $setLastActive_query = "UPDATE users SET last_active='$currentTime' WHERE id='$data_user_id'";
                    $doLastActiveQuery = mysqli_query($dbCon,$setLastActive_query);

                    $SENGINE_LOG->staticEvent("User login: <br> Username: $data_username");

                    $LOGIN_ERR = 0;
                    header("Location: home.php");
                }
                else
                {
                    $LOGIN_ERR = 4;
                }
            }
            else
            {
            	if($input_password == $data_password)
            	{
            		$cost = 10;
					$salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');
					$salt = sprintf("$2a$%02d$", $cost) . $salt;
					$hash = crypt($data_password, $salt);

					$updateUserHash = "UPDATE users SET password='$hash' WHERE username='$input_username'";
					$updateUserSalt = "UPDATE users SET salt='$salt' WHERE username='$input_username'";
					$doUpdateUserHash = mysqli_query($dbCon,$updateUserHash);
					$doUpdateUserSalt = mysqli_query($dbCon,$updateUserSalt);

					header("Location: index.php?msg_enc_conf=0");
            	}
            	else
            	{
                    if(isset($_SESSION['login_attempt']))
                    {
                        $login_attempt = ($_SESSION['login_attempt'] + 1);
                        $_SESSION['login_attempt'] = $login_attempt;
                        if($login_attempt >= 3)
                        {
                            $LOGIN_ERR = 3;
                        }
                        else
                        {
                            $LOGIN_ERR = 1;
                        }
                    }
                    else
                    {
                        $_SESSION['login_attempt'] = 1;
                    }
            	}
            }
        }
    }
?>

<html>
    <head>
        <link href="style/index/style.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <div id="title">
            <h1>King of Mota</h1>
        </div>

		<div id="login_box">
        <div id="title_div">Login: </div>
            <div id="login_form">
                <form id="login" method="post" enctype="multipart/form-data" action="index.php">
                    <input id="username" type="text" placeholder="username" name="username">
                    <input id="password" type="password" placeholder="password" name="password">
                    <input id="submit" type="submit" value="Login" placeholder="Login">
                    <div id="login_error">
                        <?php
							if($LOGIN_ERR == 0){
								echo "Login successful!";
							}
							else if($LOGIN_ERR == 1){
								echo "Failed to login!";
							}
							else if($LOGIN_ERR == 2){
								echo "Could not connect to database!";
							}
                            else if($LOGIN_ERR == 3){
                                echo 'Failed to login <br> Wrong username or password! <br><div class="g-recaptcha" data-sitekey="6LfFLwsTAAAAAElpuHQydvWaxSrTCl70obeIOReB"></div>';
                            }
                            else if($LOGIN_ERR == 4){
                                echo "You need to activate your account first! <br>";
                            }
                        ?>
                    </div>
                    <input id="register" type="button" value="Register" onclick="location.href='register.php'">
                    <?php
                        if(isset($_GET["msg_enc_conf"]))
                        {
                            $msgType = $_GET["msg_enc_conf"];
                            if($msgType == 0)
                            {
                                echo "<div style='color: green;'> <h2> User info updated automatically. <br> Please login in again (This time only!). </div>";
                            }
                            else if($msgType == 1)
                            {
                                echo "<div style='color: darkorange;'> <h2> Activation link sent! <br> Please confirm your account by clicking the link sent to your email. </div>";
                            }
                            else if($msgType == 2)
                            {
                                echo "<div style='color: green;'> <h2>Your account has been activated! </h2> </div>";
                            }
                        }
                    ?>
                </form>

            </div>

        </div>
    </body>
</html>
