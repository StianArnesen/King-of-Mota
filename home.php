<?php
    session_start();

    include_once("domainname.php");

    if(isset($_SESSION['game_username']))
    {
        $dbCon = mysqli_connect("localhost", "Stian Arnesen", "dynamicgaming", "motagamedata");

        if($dbCon){

            $session_username = $_SESSION['game_username'];

            $sqlCommands = "SELECT id, username, password, money, level FROM users WHERE username='$session_username'";

            $query = mysqli_query($dbCon, $sqlCommands);
            $data_row = mysqli_fetch_row($query);
            
            $data_user_id = $data_row[0];
            $data_username = $data_row[1];
            $data_password = $data_row[2];
            $data_money = $data_row[3];
            $data_level = $data_row[4];


            if(isset($_POST["upload_image"]))
            {
                $imgId = uniqid();

                $target_dir = "img/uploads/";
                $filN = ($target_dir . $imgId . ($_FILES["fileToUpload"]["name"]));
                $target_file = $filN;
                $uploadOk = 1;
                $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
                // Check if image file is a actual image or fake image
                if(isset($_POST["upload_image"])) {
                    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
                    if($check !== false) {
                        echo "File is an image - " . $check["mime"] . ".";
                        $uploadOk = 1;
                    } else {
                        die("File is not an image.");
                        $uploadOk = 0;
                    }
                }
                // Check if file already exists
                if (file_exists($target_file)) {
                    die("Sorry, file already exists.");
                    $uploadOk = 0;
                }
                // Check file size
                if ($_FILES["fileToUpload"]["size"] > 500000) {
                    die("Sorry, your file is too large.");
                    $uploadOk = 0;
                }
                // Allow certain file formats
                if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                && $imageFileType != "gif" ) {
                    die("Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
                    $uploadOk = 0;
                }
                // Check if $uploadOk is set to 0 by an error
                if ($uploadOk == 0) {
                    die("Sorry, your file was not uploaded.");
                // if everything is ok, try to upload file
                } else {
                    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                        //die("The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.");

                        $full_dir = $target_file;

                        $update_data_query = "UPDATE users SET profile_picture='$full_dir' WHERE id='$data_user_id'";
                        $run = mysqli_query($dbCon,$update_data_query);

                        if($run)
                        {

                        }
                        else
                        {
                            die("Failed to set profile picture!");
                        }

                    } else {
                        die("Sorry, there was an error uploading your file.");
                    }
                }
            }

            header("Location: ". $DOMAIN_NAME . $USER_DOMAIN . $_SESSION['game_username']);

        }
        else{
            $LOGIN_ERR = 2;
        }
    }
    else
    {
        header("Location: index.php");
    }

    if(isset($_POST['status_form_submit']))
    {

        if($dbCon)
        {
            $status_from = $_SESSION['game_user_id'];
            $status_to = $_SESSION['game_user_id'];
            $status_data = $_POST['status_update_data'];

            $submitStatusQuery = "INSERT INTO wall_status (status_from_user_id, status_to_user_id, status_data) VALUES ('". $status_from ."', '". $status_to ."', '". $status_data ."')";

            $submitQuery = mysqli_query($dbCon, $submitStatusQuery);
        }
    }

    if(isset($_SESSION['game_username']) && ($_SESSION['game_username'] != ""))
    {
    }
    else{
        header("Location: index.php");
        die("You need to be logged in to see this!");
    }

        if ($dbCon) {
            $user_id = $_SESSION['game_user_id'];
            $query1 = "SELECT * FROM users WHERE id='$user_id'"; //  WHERE type='$item_type'"

            $SQL_RUN = mysqli_query($dbCon, $query1);

            $CURRENT_USER = mysqli_fetch_array($SQL_RUN);
        }else{
            die("Failed to connect!");
        }

?>
