<?php
    session_start();
    require_once("common/secure.php");
    require_once("utils/new_user/InitUser.php");
    
    define("LOGIN_ERROR_INVALID_CREDENTIALS", 3);
    define("LOGIN_ERROR_NOT_ACTIVATED", 4);
	
    $SECURE = new Secure();

    $LOGIN_ERR = -1;
    
    if(isset($_SESSION['game_username']))
    {
        header("Location: home.php");
    }
    else if(isset($_POST['username']))
    {
        $dbCon = mysqli_connect("localhost", "Stian Arnesen", "dynamicgaming", "motagamedata");

        if($dbCon)
        {
            $input_username = $_POST['username'];
            $input_password = $_POST['password'];

            $sqlCommands = 'SELECT id, username, password, money, level, salt, activated FROM users WHERE username="'.$input_username.'"';

            $query      = mysqli_query($dbCon, $sqlCommands);
            $data_row   = mysqli_fetch_array($query);
            if(! $query){
                die("Failed to get userdata from DB! MySQL error: ". mysqli_error($dbCon));
            }
            
            if(! $data_row){
                $LOGIN_ERR = LOGIN_ERROR_INVALID_CREDENTIALS;
            }
            else
            {
                $data_user_id   = $data_row['id'];
                $data_username  = $data_row['username'];
                $data_password  = $data_row['password'];
                $data_money     = $data_row['money'];
                $data_level     = $data_row['level'];
                $user_salt      = $data_row['salt'];
                $user_activated = $data_row['activated'];


                require_once(__DIR__ . "/utils/database/RemoteAddress.php");
                $remoteAddressLog = new RemoteAddressLog();

                $_IP = $_SERVER['REMOTE_ADDR'];

                if($input_username == $data_username && ($data_password == crypt($input_password, $user_salt)))
                {
                    if($user_activated == 1 && $remoteAddressLog->insertAddressToDatabaseLog($_IP, $data_user_id))
                    {
                        $_SESSION['game_username']  = $data_username;
                        $_SESSION['game_money']     = $data_money;
                        $_SESSION['game_level']     = $data_level;
                        $_SESSION['game_user_id']   = $data_user_id;

                        $currentTime = time();

                        $setLastActive_query = "UPDATE users SET last_active='$currentTime' WHERE id='$data_user_id'";
                        $doLastActiveQuery = mysqli_query($dbCon,$setLastActive_query);

                        $firstTimeSetupForDatabase = new InitNewUser();
                        $firstTimeSetupForDatabase->_INIT_USER($data_user_id);
                        
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
                        $salt = strtr(base64_encode(random_bytes(16)), '+', '.');
                        $salt = sprintf("$2a$%02d$", $cost) . $salt;
                        $hash = crypt($data_password, $salt);

                        $updateUserHash = "UPDATE users SET password='$hash' WHERE username='$input_username'";
                        $updateUserSalt = "UPDATE users SET salt='$salt' WHERE username='$input_username'";
                        $doUpdateUserHash = mysqli_query($dbCon,$updateUserHash);
                        $doUpdateUserSalt = mysqli_query($dbCon,$updateUserSalt);;
                    }
                    else
                    {
                        $LOGIN_ERR = LOGIN_ERROR_INVALID_CREDENTIALS;
                    }
                }
            } // If $data_row is NOT NULL
        }
    }
?>
<script>
    less = {
        env: "development",
        async: false,
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
        <link href="style/index/style.less" rel="stylesheet/less" type="text/css">

        <meta charset="UTF-8">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/less.js/2.5.3/less.min.js" type="text/javascript"  ></script>

        <title>King of Mota | Login</title>

    </head>
    <body>
        <div id="top-banner">
            <div class="top-banner-title">
                King of Mota > Login
            </div>
        </div>
        <div class="info-view" style="display: none">Reset 12 Aug<br>
        All users have been reset!</div> <br>
		<div id="login_box">
        <div id="title_div">
        <span>Login</span></div>
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
                            else if($LOGIN_ERR == LOGIN_ERROR_INVALID_CREDENTIALS){
                                echo 'Failed to login <br> Wrong username or password! <br><div class="g-recaptcha" data-sitekey="6LfFLwsTAAAAAElpuHQydvWaxSrTCl70obeIOReB"></div>';
                            }
                            else if($LOGIN_ERR == LOGIN_ERROR_NOT_ACTIVATED){
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
