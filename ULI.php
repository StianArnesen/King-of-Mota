<?php
    $newFileName = uniqid();

    $target_dir = "img/uploads/";
    $target_file = $target_dir . basename($_FILES["crewImageFile"]["name"]);

    str_replace(' ', '', $_FILES["crewImageFile"]['name']); /* Remove whitespace */

    $uploadOk = 1;
    $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
    // Check if image file is a actual image or fake image
    if($_POST['crewImageFile'] != "" && ($_POST['crewImageFile'])) {
        //echo "<div id='upload_info'> ";
        $check = getimagesize($_FILES["crewImageFile"]["tmp_name"]);
        if($check !== false) {
            //echo "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }
    }
    // Check if file already exists
    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }
    // Check file size
    if ($_FILES["crewImageFile"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }
    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" ) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
        // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["crewImageFile"]["tmp_name"], $target_file)) {

            $dbCon = mysqli_connect("localhost", "Stian Arnesen", "dynamicgaming", "motagamedata");

            mysqli_select_db("motagamedata", $dbCon);

            $full_dir = $target_file;

            $update_data_query = "UPDATE users SET profile_picture='$full_dir' WHERE id='$data_user_id'";
            $run = mysqli_query($dbCon,$update_data_query);

            echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";

        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }

    ?>