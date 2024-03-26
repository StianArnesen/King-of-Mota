<?php
/**
 * Created by PhpStorm.
 * User: StianDesktop
 * Date: 2016-01-26
 * Time: 20:28
 */

include("connect/connection.php");
include("connect/database.php");
include("common/page.php");
require("garden/garden_utils.php");
//include("common/session/sessioninfo.php");
include("garden/gardenitem.php");
include("farming/FarmingController.php");
include("market/MarketController.php");
require_once("storage/StorageController.php");
require_once("layout/inventory-item/InventoryItem.php");
require_once("layout/inventory-item/InventoryLabItem.php");


$PAGE = new PageClass();
$marketController = new MarketController();
$storageController = new StorageController();


if(isset($_SESSION['game_user_id']) && isset($_SESSION['game_username']))
{
    $ID = $_SESSION['game_user_id'];
    $NAME = isset($_SESSION['game_username']);

    $currentUser = new User($ID, $NAME);
}

$USER_INVENTORY_QUERY        = $marketController->getValidUserInventoryToSellQuery();
$USER_LAB_PRODUCTS_INVENTORY = $storageController->getLabProductsFromStorage();

function getExcerpt($str, $length)
{
    if(strlen($str) >= $length)
    {
        return substr($str, 0, $length - 3) . "...";
    }
    return $str;
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
        <link href="style/market/market.less" rel="stylesheet/less" type="text/css">
        <script src="//cdnjs.cloudflare.com/ajax/libs/less.js/2.5.3/less.min.js" type="text/javascript"  ></script>
        
        
        <?php
            echo $PAGE->getHeaderInfo();
        ?>

        <title>King of Mota | Market </title>
    </head>

    <body>
    <script src="market/script/MarketScript.js" type="text/javascript"></script>
    <?php
    echo $PAGE->getTopBanner();
    ?>
        <div id="content">
            <div class="market-view">
                <div class="inventory-view">
                    <div class="inventory-view-title"><span>Inventory</span></div>
                    <div class="inventory-view-items" id="inventory-view-items">

                        <?php

                        $total_inventory_items = 0;

                        while($INVENTORY_ITEM = mysqli_fetch_array($USER_INVENTORY_QUERY))
                        {
                            $total_inventory_items++;

                            $INVENTORY_ITEM_LAYOUT = new InventoryItem($INVENTORY_ITEM['item_id'], $INVENTORY_ITEM['inv_id'], $INVENTORY_ITEM['name'], $INVENTORY_ITEM['picture'], $INVENTORY_ITEM['item_amount']);

                            echo $INVENTORY_ITEM_LAYOUT->getInventoryItemHtml();

                        }
                        foreach($USER_LAB_PRODUCTS_INVENTORY as $INVENTORY_ITEM)
                        {
                            $total_inventory_items++;

                            $INVENTORY_ITEM_LAYOUT = new InventoryLabItem($INVENTORY_ITEM);

                            echo $INVENTORY_ITEM_LAYOUT->getInventoryItemHtml();
                        }

                        if($total_inventory_items == 0)
                        {
                            echo "<div class='inventory-view-empty'>You don't have anything to sell at this point.</div>";
                        }

                        ?>

                    </div>
                </div>
            </div>
            <div class="market-drop-view">
                <div class="market-drop-view-title">Market: </div>

                <div id="drop-view-info-text">Drop items from your inventory</div>
                <div id="market-items-drop-view" class="market-items-drop-view">
                
                </div>

                <div class="market-item-response-list" id="market-item-response-list"><span>Summary of selected items will appear here.</span></div>
            </div>
        </div>
    
    </body>
</html>

<script type="text/javascript">


    

</script>




