<?php

if(isset($_POST['get_item_list']))
{
    require "ItemEditorController.php";

    $EDITOR = new DatabaseEditor();

    $limit  = $_POST['query_max'];
    $offset = $_POST['query_offset'];

    if(is_numeric($limit) && is_numeric($offset))
    {
        if($limit > 0 && $offset >= 0)
        {
            die($EDITOR->getItemArrayList($limit));
        }
        else
        {
            die("Invalid request. limit or offset invalid!");
        }
    }
    else
    {
        die("Invalid request. not numeric");
    }
}
else if(isset($_POST['GET_REV_LIST']))
{
    require "ItemEditorController.php";

    $EDITOR = new DatabaseEditor();

    $ITEM_ID = $_POST['GET_REV_LIST'];

    die($EDITOR->getRevisionListForItem($ITEM_ID));

}
die("Invalid request");