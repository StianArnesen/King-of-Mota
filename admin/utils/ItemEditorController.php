<?php
if(isset($_POST['get_item_list']))
{
    $EDITOR = new DatabaseEditor();

    $limit      = $_POST['query_max'];
    $offset     = $_POST['query_offset'];
    $item_type  = $_POST['filter_type'];

    if(is_numeric($limit) && is_numeric($offset))
    {
        if($limit > 0 && $offset >= 0)
        {
            die($EDITOR->getItemArrayList($item_type, $limit));
        }
        else
        {
            die("Invalid request. limit or offset param invalid!");
        }
    }
    else
    {
        die("Invalid request. param not numeric!");
    }
}
else if(isset($_POST['get_item_desc']))
{
    $EDITOR = new DatabaseEditor();

    $index  = $_POST['get_item_desc'];

    if(is_numeric($index)){
        die($EDITOR->getItemDescription($index));
    }
    else{
        die(array(
            'STATUS' => "FAILED",
            'MSG'    => "NON_NUMERIC"
        ));
    }

}
else if (isset($_POST['update_item_array']))
{
    $ITEM_ARRAY = json_decode($_POST['update_item_array'], true);

    $EDITOR     = new DatabaseEditor();

    die(json_encode($EDITOR->updateItemInDatabase($ITEM_ARRAY), JSON_PRETTY_PRINT));
}

class DatabaseEditor {

    private $CONNECTION;

    private $USER;

    public function __construct()
    {
        if($this->_connect())
        {
            if($this->_loadAndAuthUser())
            {

            }
            else
            {
                die("Access denied");
            }
        }
    }
    private function checkForNewImageAndUpload()
    {
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

        $check = getimagesize($_FILES["crewImageFile"]["tmp_name"]);
        if($check !== false)
        {
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }
        if (file_exists($target_file))
        {
            echo "Sorry, file already exists.";
            $uploadOk = 0;
        }

        if ($_FILES["crewImageFile"]["size"] > 500000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" )
        {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        if ($uploadOk == 0)
        {
            echo "Sorry, your file was not uploaded.";
        }
        else
        {
            if (move_uploaded_file($_FILES["crewImageFile"]["tmp_name"], $target_file))
            {
                $UPLOAD_STATUS = true;

                echo "The file ". basename( $_FILES["crewImageFile"]["name"]). " has been uploaded.";
            }
            else
            {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    }
    /**
     * @param $limit
     * @return bool|mysqli_result
     */
    private function getUsernameById($USER_ID)
    {
        $SQL = "SELECT username FROM users WHERE id='$USER_ID' LIMIT 1";

        if($QUERY = mysqli_query($this->CONNECTION, $SQL))
        {
            $USERNAME = mysqli_fetch_row($QUERY);

            return $USERNAME[0];
        }
        return "NO_NAME";
    }
    private function getItemListQuery($item_type ,$limit)
    {
        $SQL = "SELECT * FROM items WHERE type=$item_type ORDER BY min_level LIMIT $limit";
        
        if($QUERY = mysqli_query($this->CONNECTION, $SQL))
        {
            return $QUERY;
        }
        return false;
    }
    public function getItemDescription($item_id){
        $SQL = "SELECT beskrivelse FROM items WHERE items.id=$item_id LIMIT 1";

        if($QUERY = mysqli_query($this->CONNECTION, $SQL)){
            $RESULT = mysqli_fetch_row($QUERY);

            return $RESULT[0];
        }
        $RESULT = array(
            'STATUS' => "FAILED"
        );

        return json_encode($RESULT, JSON_PRETTY_PRINT);
    }
    public function getItemArrayList($item_type, $limit)
    {
        if($itemSql = $this->getItemListQuery($item_type, $limit))
        {
            $ARRAY = array();

            while($ITEM = mysqli_fetch_array($itemSql))
            {
                $ITEM_ARRAY = array();

                $PRICE  = $ITEM['pris'];


                $ITEM_ARRAY['pris']        = $PRICE;
                $ITEM_ARRAY['g_price']     = $ITEM['g_price'];
                $ITEM_ARRAY['type']        = $ITEM['type'];
                $ITEM_ARRAY['sub_type']    = $ITEM['sub_type'];
                $ITEM_ARRAY['picture']     = $ITEM['picture'];
                $ITEM_ARRAY['id']          = $ITEM['id'];
                $ITEM_ARRAY['name']        = $ITEM['name'];
                $ITEM_ARRAY['discount']    = $ITEM['discount'];
                $ITEM_ARRAY['item_power']  = $ITEM['item_power'];
                $ITEM_ARRAY['grow_time']   = $ITEM['grow_time'];
                $ITEM_ARRAY['min_level']   = $ITEM['min_level'];
                $ITEM_ARRAY['item_info_a'] = $ITEM['item_info_a'];
                $ITEM_ARRAY['shop_order']  = $ITEM['shop_order'];
                $ITEM_ARRAY['item_active'] = $ITEM['item_active'];
                $ITEM_ARRAY['desc']        = $ITEM['beskrivelse'];

                $SQL_ERR = mysqli_error($this->CONNECTION);

                if(! is_null($SQL_ERR) && $SQL_ERR != ""){

                    $ARRAY_ERR = array(
                        'STATUS'    => "FAILED",
                        'SQL'       => $SQL_ERR
                    );
                    return (json_encode($ARRAY_ERR, JSON_PRETTY_PRINT));
                }

                array_push($ARRAY, $ITEM_ARRAY);
            }
            return (json_encode($ARRAY, JSON_PRETTY_PRINT));
        }
        $ARRAY_ERR = array(
            'STATUS' => "FAILED"
        );
        return (json_encode($ARRAY_ERR, JSON_PRETTY_PRINT));
    }
    public function updateItemInDatabase($ITEM_ARRAY)
    {
        if(! $this->USER->isUserAdmin())
        {
            die("ACC_DENY");
        }

        if($this->insertItemToRevision($ITEM_ARRAY))
        {

        }
        else
        {
            $SQL_ERR = mysqli_error($this->CONNECTION);

            return array(
                'STATUS'    => "FAILED",
                'MSG'       => "REV_ITEMS_INSERT_ERR",
                'SQL_MSG'   => $SQL_ERR
            );
        }


        $ID             = $ITEM_ARRAY['item_id'];
        $PRICE          = $ITEM_ARRAY['item_price'];
        $G_PRICE        = $ITEM_ARRAY['item_g_price'];

        $TYPE           = $ITEM_ARRAY['item_type'];
        $SUB_TYPE       = $ITEM_ARRAY['item_sub_type'];

        $POWER          = $ITEM_ARRAY['item_power'];
        $GROW_TIME      = $ITEM_ARRAY['item_grow_time'];
        $ITEM_INFO_A    = $ITEM_ARRAY['item_info_a'];

        $NAME           = $ITEM_ARRAY['item_name'];
        $MIN_LEVEL      = $ITEM_ARRAY['item_level'];

        $ITEM_ACTIVE    = $ITEM_ARRAY['item_active'];



        $SQL = "UPDATE items SET pris='$PRICE', type='$TYPE', sub_type='$SUB_TYPE', item_power='$POWER', grow_time='$GROW_TIME', min_level='$MIN_LEVEL', name='$NAME', item_active='$ITEM_ACTIVE', item_info_a='$ITEM_INFO_A', g_price='$G_PRICE' WHERE id='$ID'";

        if($QUERY = mysqli_query($this->CONNECTION, $SQL))
        {
            return array(
                'STATUS' => "OK"
            );
        }

        $SQL_ERR = mysqli_error($this->CONNECTION);

        return array(
            'STATUS'    => "FAILED",
            'MSG'       => "SQL_QUERY_ERR",
            'SQL_MSG'   => $SQL_ERR
        );
    }
    private function insertItemToRevision($ITEM_ARRAY)
    {
        if(! $this->USER->isUserAdmin())
        {
            die("ACC_DENY");
        }

        $ID             = $ITEM_ARRAY['item_id'];
        $PRICE          = $ITEM_ARRAY['item_price'];

        $TYPE           = $ITEM_ARRAY['item_type'];
        $SUB_TYPE       = $ITEM_ARRAY['item_sub_type'];

        $POWER          = $ITEM_ARRAY['item_power'];
        $GROW_TIME      = $ITEM_ARRAY['item_grow_time'];
        $ITEM_INFO_A    = $ITEM_ARRAY['item_info_a'];

        $NAME           = $ITEM_ARRAY['item_name'];
        $MIN_LEVEL      = $ITEM_ARRAY['item_level'];

        $ITEM_ACTIVE    = $ITEM_ARRAY['item_active'];

        /*
         *      |-------------------------------------------------------|
         *      |      Database structure for table 'rev_items'         |
         *      |                                                       |
         *      |-------------------------------------------------------|
         *
         *
         *      Table [rev_items]     -->   (pris, type, sub_type, name, item_power, grow_time, min_level, item_info_a, item_active, rev_version, rev_note, rev_user_id, rev_timestamp)
         *
         * */


        $REV_VERSION    = rand(1, 1257) + rand(1, 1257);
        $REV_NOTE       = "This is a test note for the King of Mota version control";
        $REV_USER_ID    = $this->USER->getUserId();
        $REV_TIMESTAMP  = time();

        $SQL = "INSERT INTO rev_items (pris, type, sub_type, id, name, item_power, grow_time, min_level, item_info_a, item_active, rev_version, rev_user_id, rev_timestamp) VALUES ('$PRICE', '$TYPE', '$SUB_TYPE', '$ID', '$NAME', '$POWER', '$GROW_TIME', '$MIN_LEVEL', '$ITEM_INFO_A', '$ITEM_ACTIVE', '$REV_VERSION', '$REV_USER_ID', '$REV_TIMESTAMP') ";

        if($QUERY = mysqli_query($this->CONNECTION, $SQL))
        {
            return true;
        }

        return false;
    }

    public function getRevisionListForItem($ITEM_ID)
    {
        $SQL = "SELECT * FROM rev_items WHERE id='$ITEM_ID' ORDER BY rev_id DESC ";

        if($QUERY = mysqli_query($this->CONNECTION, $SQL))
        {
            $ARRAY = array();

            while($ITEM = mysqli_fetch_array($QUERY))
            {
                $REV_USERNAME   = $this->getUsernameById($ITEM['rev_user_id']);

                $ITEM_ARRAY = array();

                $ITEM_ARRAY['pris']        = $ITEM['pris'];
                $ITEM_ARRAY['type']        = $ITEM['type'];
                $ITEM_ARRAY['sub_type']    = $ITEM['sub_type'];
                $ITEM_ARRAY['picture']     = $ITEM['picture'];
                $ITEM_ARRAY['id']          = $ITEM['id'];
                $ITEM_ARRAY['name']        = $ITEM['name'];
                $ITEM_ARRAY['item_power']  = $ITEM['item_power'];
                $ITEM_ARRAY['grow_time']   = $ITEM['grow_time'];
                $ITEM_ARRAY['min_level']   = $ITEM['min_level'];
                $ITEM_ARRAY['item_info_a'] = $ITEM['item_info_a'];
                $ITEM_ARRAY['shop_order']  = $ITEM['shop_order'];
                $ITEM_ARRAY['item_active'] = $ITEM['item_active'];

                $ITEM_ARRAY['rev_version']      = $ITEM['rev_version'];
                $ITEM_ARRAY['rev_note']         = $ITEM['rev_note'];
                $ITEM_ARRAY['rev_user_id']      = $ITEM['rev_user_id'];
                $ITEM_ARRAY['rev_username']     = $REV_USERNAME;

                $ITEM_ARRAY['rev_timestamp']    = $ITEM['rev_timestamp'];
                $ITEM_ARRAY['rev_id']           = $ITEM['rev_id'];


                array_push($ARRAY, $ITEM_ARRAY);
            }
            return json_encode($ARRAY, JSON_PRETTY_PRINT);
        }

        $mysqli_error = mysqli_error($this->CONNECTION);

        $ARRAY_ERR = array(
            'STATUS'    => "FAILED",
            'MSG'       => "FETCH_REV_ITEMS_FAILED",
            'SQL_MSG'   => $mysqli_error

        );

        return json_encode($ARRAY_ERR, JSON_PRETTY_PRINT);
    }












    /*
     *      |-------------------------------------------------------|
     *      | AUTHENTICATION -> DON'T CHANGE SHIT BELOW THIS POINT! |
     *      |                                                       |
     *      |-------------------------------------------------------|
     * */

    private function _loadAndAuthUser()
    {
        require_once __DIR__ ."/../../common/session/sessioninfo.php";
        if($USER_TMP = new User())
        {
            if($USER_TMP->isLoggedIn())
            {
                if($USER_TMP->isUserAdmin())
                {
                    if($this->USER = $USER_TMP)
                    {
                        return true;
                    }
                }
            }
        }
        return false;
    }
    private function _connect()
    {
        require_once(__DIR__ . "/../../connect/database.php");

        if($this->CONNECTION = Database::getConnection())
        {
            return true;
        }
        return false;
    }
}