<?php

class LabEditorController
{
    private $CONNECTION;

    private $USER;

    private $LAB_CONTROLLER;

    public function __construct()
    {
        $ERR = array(
            'err_num' => 0
        );
        
        if($this->_connect())
        {
            if($this->_loadAndAuthUser())
            {
                if($this->loadLabController())
                {
                    
                }
                else{
                    $ERR['err_num']++;
                    $ERR['lab_controller_loaded'] = "failed";
                }
            }
            else{
                $ERR['err_num']++;
                $ERR['authentication'] = "failed";
            }
        }
        else{
            $ERR['err_num']++;
            $ERR['db_connection'] = "failed";
        }
        
        if($ERR['err_num'] > 0){
            die(json_encode($ERR, JSON_PRETTY_PRINT));
        }
    }
    public function getLabItem($LAB_ITEM_ID)
    {
        $SQL = "SELECT lab_ingredients.*, lab_products.* FROM lab_products 
                INNER JOIN lab_ingredients ON motagamedata.lab_products.ingredients_link_id=motagamedata.lab_ingredients.lab_product_id 
                  WHERE motagamedata.lab_products.id='$LAB_ITEM_ID'";

        if($QUERY = mysqli_query($this->CONNECTION, $SQL))
        {
            $ARRAY_RESULT = array();

            while($RESULT = mysqli_fetch_array($QUERY))
            {
                $ARRAY_ITEM = array(

                );
            }
        }
        return null;
    }
    public function getAllLabItems()
    {
        return $this->LAB_CONTROLLER->getValidLabProductsToUse();
    }



    /*
     *      |-------------------------------------------------------|
     *      | AUTHENTICATION -> DON'T CHANGE SHIT BELOW THIS POINT! |
     *      |                                                       |
     *      |-------------------------------------------------------|
     * */

    private function loadLabController()
    {
        require_once __DIR__ ."/../../lab/PublicLab.php";

        if($this->LAB_CONTROLLER = new PublicLab())
        {
            return true;
        }
        return false;

    }

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


$GET_REQUEST_LAB_ITEM_ID    = isset($_POST['get_lab_products_list'])? $_POST['get_lab_products_list']: null;

if(isset($GET_REQUEST_LAB_ITEM_ID) && ! is_null($GET_REQUEST_LAB_ITEM_ID))
{
    if(is_numeric($GET_REQUEST_LAB_ITEM_ID))
    {
        $LAB_EDIT_CONTROLLER    = new LabEditorController();
        $RESULT                 = $LAB_EDIT_CONTROLLER->getAllLabItems();

        die(
            json_encode($RESULT,JSON_PRETTY_PRINT)
        );
    }
    else
    {
        die("Not integer");
    }
}
else{
    
}

