<?php

include("common/page.php");
include("utils/se_utils.php");

require_once("common/session/sessioninfo.php"); // User(int id, int username);

include("storage/StorageUnitController.php");


if($USER = new User(-1, -1))
{
    if(! $USER->isLoggedIn())
    {
        die("Failed to load user!");
    }   
}
else
{
    die("Failed to load user!");
}

$PAGE = new PageClass();


$STORAGE = new StorageUnitController();
$storage_units = $STORAGE->getAllStorageUnits();


?>


<script xmlns="http://www.w3.org/1999/html">
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
        <title>King of Mota | Storage</title>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/less.js/2.5.3/less.min.js" type="text/javascript"  ></script>

        <?php echo $PAGE->getHeaderInfo();?>
        <script src="script/storage/storage.js" type="text/javascript"> </script>

        <link href="style/storage/style.less" rel="stylesheet/less" type="text/css">
        <link href="style/storage/storage.less" rel="stylesheet/less" type="text/css">
        <link href="style/inventory/style.less" rel="stylesheet/less" type="text/css">

    </head>
    <body>
        <?echo $PAGE->getTopBanner();?>

        <div class="storage-view" id="body">

                <div class="storage-info-view">

                    <div class="storage-view-title">
                       Storage
                    </div>
                    <div class="storage-info-title" id="storage-info-name">
                        Storage Name
                    </div>

                    <div class="storage-info-img">
                        <img src="img/storage/storage_unit_medium.jpg" id="storage-info-img">
                        <div class="button" id="open-storage" onclick="showStorageUnit">Open</div>
                    </div>

                    <div class="storage-info-div">

                        <div class="storage-info-level">
                            <span>Storage level: </span>
                            <div id="storage-level">1</div>
                        </div>

                        <div class="storage-info-space" id="storage-space-info">
                            Space:
                            <div class="storage-info-space space-used" id="storage-space-used">NAN</div>
                            <span>/</span>
                            <div class="storage-info-space space-total" id="storage-space-total">NAN</div>

                        </div>

                        <div class="storage-upgrade-view">
                            <div class="storage-upgrade-button button money-valid" id="upgrade-button">Upgrade! (+3)</div>
                            <div class="storage-upgrade-button-price">
                                <div id="upgrade-price">90000</div>

                            </div>
                        </div>



                        

                    </div>

                </div>


                <!-- -->

            <div class="storage-view-items">
        <?

        require_once("storage/StorageController.php");

        $STORAGE_CONTROLLER = new StorageController();

        while($UNIT = mysqli_fetch_array($storage_units))
        {
            $STORAGE_ID = $UNIT['id'];

            $USED_SPACE     = $STORAGE_CONTROLLER->getSpaceUsedInStorage($STORAGE_ID);
            $TOTAL_SPACE    = $STORAGE_CONTROLLER->getStorageCapacity($STORAGE_ID);

            echo "<div class='storage-unit' onclick='showStorageUnitInfo($STORAGE_ID, this)'>";
                echo "<div class='storage-unit-title'>";
                    echo "<div class='unit-title'>". $UNIT['storage_title'] ."</div>";
                echo "</div>";

                echo "<img class='storage-unit-image' src='img/storage/storage_unit_medium.jpg'>";

                $STR_COLOR = 255 * ($USED_SPACE/$TOTAL_SPACE);

                if($USED_SPACE >= $TOTAL_SPACE - 1){
                    $STR_COLOR = "color: rgba(220, 40, 40,1)";
                }
                else if($USED_SPACE < $TOTAL_SPACE / 2){
                    $STR_COLOR = "color: rgba(100, 170, 40,1)";
                }
                else if($USED_SPACE >= $TOTAL_SPACE / 2){
                    $STR_COLOR = "color: rgba(170, 170, 40,1)";
                }
                echo "<div class='storage-unit-space-size' style='$STR_COLOR'> ";
                    echo "<div class='space-size-used'>". $USED_SPACE ." / </div>";
                    echo "<div class='space-size-total'> ". " ". $TOTAL_SPACE ."</div>";
                echo "</div>";
            echo "</div>";
        }

        echo "<div class='storage-unit' onclick='buyStorage()'>";
                echo "<div class='storage-unit-title'>";
                    echo "<div class='unit-title'>Buy more storage</div>";
                echo "</div>";

                echo "<img class='storage-unit-image' src='img/garden/add_space.png'>";
                echo "<div class='storage-unit-price'>". StaticUtils::currencyFormat($STORAGE->getStorageUnitPrice()) ."$</div>";

            echo "</div>";

        ?>
            </div>
        </div>
        <div class="inventory-action-view" id="inv-action-view">
            <div class="inv-action-title">Selected: </div>
            <button id="inventory-action-button-trash" onclick="trashItems()">Trash</button>
            <button id="inventory-action-button-move" onclick="showStorageList()">Move</button>
            <div id="inv-selected-amount"></div>
        </div>
        <div id="overlay-view"> </div>
        <div id="overlay-data" class="overlay-data"> </div>

    </body>
</html>

<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
