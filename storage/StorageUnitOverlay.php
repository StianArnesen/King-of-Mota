<?

if(! isset($_SESSION))
{
    session_start();
}

function getExcerpt($str, $limit)
{
    if(strlen($str) > $limit)
    {
        return substr($str, 0, $limit) . "...";
    }
    return $str;
}

function getItemInfo($ITEM_ID)
{
    $dbCon = mysqli_connect("localhost", "Stian Arnesen", "dynamicgaming", "motagamedata");

    $SQL_21 = "SELECT * FROM items WHERE id='$ITEM_ID'";
    $QUERY_21 = mysqli_query($dbCon, $SQL_21);

    return mysqli_fetch_array($QUERY_21);
}


$DIE_RESULT = "";

$dbCon = mysqli_connect("localhost", "Stian Arnesen", "dynamicgaming", "motagamedata");

$session_user_id    = $_SESSION['game_user_id'];

$valid_storage_id   = $dbCon->real_escape_string($_GET['storage_id']);

$data_user_id       = $session_user_id;

$sqlCommands = "SELECT inventory.*, items.type FROM inventory, items WHERE inventory.storage_id='$valid_storage_id' AND (inventory.user_id='$data_user_id' AND inventory.item_id=items.id)  ORDER BY items.type DESC,inventory.inv_item_status_type DESC";

$query = mysqli_query($dbCon, $sqlCommands);

$LAST_STATUS_TYPE = -1;
$LAST_TYPE = -1;

$TYPE_CHANGE = false;
$STATUS_TYPE_CHANGE = false;

$storage_title_sql = "SELECT storage_title FROM storage_units WHERE id='$valid_storage_id' AND user_id='$session_user_id'";
$TITLE_QUERY = mysqli_query($dbCon, $storage_title_sql);

$title_q = mysqli_fetch_array($TITLE_QUERY);

$storage_title = $title_q['storage_title'];



$ITEMS_TOTAL = 0;

$DIE_RESULT .= "<div class='storage-title-overlay'> $storage_title</div> <div id='inventory_items'> ";
    while($data_row = mysqli_fetch_array($query))
    {
        $ITEMS_TOTAL++;
        if($data_row['type'] != $LAST_TYPE)
        {
            $TYPE_CHANGE = true;
            $LAST_TYPE = $data_row['type'];
            if($LAST_TYPE == 0){
                $DIE_RESULT .= "<div class='inv-item-view-divider-group'>Drugs: </div>";
            }
            else if($LAST_TYPE == 1){
                $DIE_RESULT .= "<div class='inv-item-view-divider-group'>Vehicles: </div>";
            }
            else if($LAST_TYPE == 2){
                $DIE_RESULT .= "<div class='inv-item-view-divider-group'>Weapons: </div>";
            }
            else if($LAST_TYPE == 3){
                $DIE_RESULT .= "<div class='inv-item-view-divider-group'>Growing lights: </div>";
            }
            else if($LAST_TYPE == 6){
                $DIE_RESULT .= "<div class='inv-item-view-divider-group'>Air vent: </div>";
            }
            else if($LAST_TYPE == 5){
                $DIE_RESULT .= "<div class='inv-item-view-divider-group'>Soil: </div>";
            }
        }
        if($data_row['inv_item_status_type'] != $LAST_STATUS_TYPE)
        {
            $STATUS_TYPE_CHANGE = true;
            //$DIE_RESULT .= "<div class='inv-items-view-group-status-type'>";
            if($LAST_TYPE == 0)
            {
                $LAST_STATUS_TYPE = $data_row['inv_item_status_type'];
                if($LAST_STATUS_TYPE == 0){
                    $DIE_RESULT .= "<div class='inv-item-view-sub-divider-group'>Seeds: </div>";
                }
                else if($LAST_STATUS_TYPE == 1){
                    $DIE_RESULT .= "<div class='inv-item-view-sub-divider-group'>Kush: </div>";
                }
            }
        }
        $search = 0;

        $data_item_id = $data_row['item_id'];
        $data_item_amount = $data_row['item_amount'];
        $data_item_status_type = $data_row['inv_item_status_type'];

        if(! isset($_GET['searchVal']))
        {
            $query1 = "SELECT * FROM items WHERE (id='$data_item_id')";
        }
        else
        {
            $searchValue = $_GET['searchVal'];
            $query1 = "SELECT * FROM items WHERE
                                (
                                    name LIKE '%". $searchValue ."%'
                                )";
            $search = 1;
        }

        $item = mysqli_query($dbCon,$query1);
        $item_row = mysqli_fetch_array($item);


        $DIE_RESULT .= "<div class='inventory_item'> <input type='hidden' name='item-id-value' value='". $data_row['item_id'] ."'> <input type='hidden' name='inv-id-value' value='". $data_row['inv_id'] ."'> ";
        $DIE_RESULT .= "<div class='inv_item_title'> <span>" . getExcerpt($item_row['name'], 15) . "</span></div>";
        if($data_item_status_type == 0 && $LAST_TYPE == 0)
        {
            $DIE_RESULT .= "<img class='inventory_item_seed_img' src='img/seed/seed_icon.png'>";
        }
        $DIE_RESULT .= "<div class='item-info-view'>";
        $DIE_RESULT .= "<div class='inv_item_amount'>" . $data_row['item_amount'] . "</div>" ;
        $DIE_RESULT .= "</div>";


        $DIE_RESULT .= "<img class='item_final_img' src='" . $item_row['picture'] . "'>" ;
        $DIE_RESULT .= "</div>";

        if($search == 1)
        {
            break;
        }
    }

    if($ITEMS_TOTAL == 0)
    {
        $DIE_RESULT .= "<h3>No items in storage</h3>";
    }


    $DIE_RESULT .= "</div>";

    die($DIE_RESULT);

?>
