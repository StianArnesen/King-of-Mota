<?php

$UPLOAD_STATUS  = false;

$newFileName    = uniqid();

$pre_target_dir = "../../";
$target_dir     = "img/uploads/";
$filN           = $newFileName .  basename($_FILES["crewImageFile"]["name"]);
$target_file    = $pre_target_dir . $target_dir . $filN;

$relative_path  = $target_dir . $filN;

str_replace(' ', '', $_FILES["crewImageFile"]['name']); /* Remove whitespace */

$uploadOk = 1;
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
// Check if image file is a actual image or fake image
if($_POST['item-add-submit'] != "" && ($_POST['item-add-submit'])) {
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

        $UPLOAD_STATUS = true;

        echo "The file ". basename( $_FILES["crewImageFile"]["name"]). " has been uploaded.";

    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}







if(isset($_POST['item-add-submit']) && $UPLOAD_STATUS)
{
    $newItem        = new NewItem();

    if($UPLOAD_STATUS)
    {


        $NAME           = $_POST['item-name'];
        $DESC           = $_POST['item-description'];
        $PRICE          = $_POST['item-price'];
        $G_PRICE        = $_POST['item-g-price'];
        $TYPE           = $_POST['item-type'];
        $SUB_TYPE       = $_POST['item-sub-type'];
        $IMAGE          = $relative_path;
        $POWER          = $_POST['item-power'];
        $GROW_TIME      = $_POST['item-grow-time'];
        $MIN_LEVEL      = $_POST['item-min-level'];
        $ITEM_INFO_A    = $_POST['item-info-a'];
        $ITEM_ACTIVE    = $_POST['item-active'];


        if($RESULT = $newItem->addNewItem($NAME, $DESC, $PRICE, $G_PRICE, $TYPE, $SUB_TYPE, $IMAGE, $POWER, $GROW_TIME, $MIN_LEVEL, $ITEM_INFO_A, $ITEM_ACTIVE))
        {
            if($RESULT === true)
            {
                die("Item added!");
            }
            else
            {
                die($RESULT);
            }
        }
        else
        {
            die("FAILED TO ADD ITEM!");
        }

    }
    else
    {
        echo "Failed to upload image! <br> Item was not added to database! <br>  <br> MSG: ";
    }
}
else
{
    die(" <br>Failed to upload  file. cannot create item!");
}

class NewItem
{
    private $CONNECTION;

    public function __construct()
    {
        if($this->_CONNECT())
        {

        }
        else
        {
            die("FAILED  TO CONNECT!");
        }
    }
    private function _CONNECT()
    {
        require_once(__DIR__ . "../../../connect/database.php");

        if($this->CONNECTION = Database::getConnection())
        {
            return true;
        }
        return false;
    }
    public function addNewItem($NAME, $DESC, $PRICE, $G_PRICE, $TYPE, $SUB_TYPE, $IMAGE, $POWER, $GROW_TIME, $MIN_LEVEL, $ITEM_INFO_A, $ITEM_ACTIVE)
    {
        $SQL = "INSERT INTO items (`beskrivelse`, `pris`, `type`, `sub_type`, `picture`, `name`, `item_power`, `grow_time`, `min_level`, `item_info_a`, `item_active`, `g_price`)
                                    VALUES ('$DESC', '$PRICE', '$TYPE','$SUB_TYPE', '$IMAGE', '$NAME', '$POWER', '$GROW_TIME', '$MIN_LEVEL', '$ITEM_INFO_A', '$ITEM_ACTIVE', '$G_PRICE')";

        if(mysqli_query($this->CONNECTION, $SQL))
        {
            return true;
        }
        return mysqli_error($this->CONNECTION);
    }
    public function uploadImageAndGetDirectory()
    {
        $newFileName = uniqid();

        $target_dir = "........./../../img/item/uploads/";
        $target_file = $target_dir . $newFileName;

        $MSG = "<br>";

        $uploadOk = 1;
        $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
        // Check if image file is a actual image or fake image
        if(isset($_POST['item-add-submit'])) {
            //echo "<div id='upload_info'> ";
            $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
            if($check !== false) {
                //echo "File is an image - " . $check["mime"] . ".";
                $uploadOk = 1;
            } else {
                $MSG .= "<br>File is not an image.";
                $uploadOk = 0;
            }
        }
        // Check if file already exists
        if (file_exists($target_file)) {
            $MSG .= "<br>Sorry, file already exists.";
            $uploadOk = 0;
        }
        // Check file size
        if ($_FILES["fileToUpload"]["size"] > 500000) {
            $MSG .= "<br>Sorry, your file is too large.";
            $uploadOk = 0;
        }
        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif" ) {
            $MSG .=  "<br>Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }
        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            return array(
                'STATUS'    => false,
                'MSG'       => $MSG
            );
            // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {

                $full_dir = $target_file;

                return array(
                    'STATUS'    => true,
                    'DIR'       => $full_dir
                );

            } else {
                return array(
                    'STATUS'    => false,
                    'MSG'       => "<br>Something went wrong..."
                );
            }
        }
    }
}

?>