<?php

require("common/page.php");
require("crafting/CraftingController.php");

$craftingController = new CraftingController();



$PAGE = new PageClass();

?>


<html>
    <head>
        <?php echo $PAGE->getHeaderInfo(); ?>

        <link href="style/crafting/crafting.less" rel="stylesheet" type="text/css">

        <title>King of Mota | Crafting</title>

    </head>
    <body>
    <?php echo $PAGE->getTopBanner(); ?>
        <div class="main crafting-view">
            <h1>Crafting!</h1>

            <div class="crafting-items-view">
                <div class="crafting-list">


                    <?php

                    $ITEMS_QUERY = $craftingController->getQueryAllCraftingItems();
                    if(is_null($ITEMS_QUERY))
                    {
                        echo "Function return is NULL, closing application";
                        exit;
                    }

                    $LAST_ITEM_TIRE = null;

                    while($ITEM = mysqli_fetch_array($ITEMS_QUERY))
                    {

                        $ITEM_ID    = $ITEM['item_id'];
                        $ITEM_TIRE  = $ITEM['item_tire'];

                        $ITEM_TITLE = $ITEM['base_name'];
                        $ITEM_IMAGE = $ITEM['base_image'];


                        $REQ_ITEMS_TO_CRAFT = $craftingController->getQueryItemsRequiredToCraftItem($ITEM_ID, $ITEM_TIRE - 1);

                        if($LAST_ITEM_TIRE != $ITEM_TIRE || is_null($LAST_ITEM_TIRE))
                        {
                            if(! is_null($LAST_ITEM_TIRE))
                            {
                                echo "</div> </div>";
                            }
                            echo "<div class='group-divider'>";
                            echo "<div class='group-divider-title'>Group tire $ITEM_TIRE</div>";
                            echo "<div class='group-divider-content'>";
                            $LAST_ITEM_TIRE = $ITEM_TIRE;
                        }


                        /*
                         *  Show required items to craft item. (If any)
                         * */


                        if(mysqli_num_rows($REQ_ITEMS_TO_CRAFT) > 0)
                        {
                            echo "<div class='req-items-view'>";
                            echo "<div class='req-items-view-title'>Required items to craft '$ITEM_TITLE'</div>";
                        }
                        else
                        {

                        }

                        while($REQ_ITEM = mysqli_fetch_array($REQ_ITEMS_TO_CRAFT))
                        {
                            $R_ITEM_TITLE   = $REQ_ITEM['base_name'];
                            $R_ITEM_IMAGE   = $REQ_ITEM['base_image'];
                            $R_ITEM_AMOUNT  = $REQ_ITEM['req_item_amount'];


                            echo "<div class='crafting-item-req'>";
                            echo "  <div class='crafting-item-req-title'>". $R_ITEM_TITLE. "</div>";
                            echo "  <div class='crafting-item-req-image'>";
                            echo "    <img class='global_item-image' src=' $R_ITEM_IMAGE '>";
                            echo "  </div>";
                            echo "  <div class='req-item-amount'>x $R_ITEM_AMOUNT</div>";
                            echo "</div>";
                        }
                        if(mysqli_num_rows($REQ_ITEMS_TO_CRAFT) > 0)
                        {
                            echo "</div>";
                        }



                        /*
                        *  Show crafting-item.
                        * */
                        echo "<div class='crafting-item'>";
                        echo "  <div class='crafting-item-title'>". $ITEM_TITLE. "</div>";
                        echo "  <div class='crafting-item-image'>";
                        echo "    <img class='global_item-image' src=' $ITEM_IMAGE '>";
                        echo "  </div>";
                        echo "</div>";
                    }


                    ?>

                </div>
            </div>

        </div>

    </body>
</html>



