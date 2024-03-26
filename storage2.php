<?php

require("storage/StorageController.php");
require("common/Page.php");

$STORAGE    = new StorageController();
$PAGE       = new PageClass();

?>

<html>
    <head>
        <?php echo $PAGE->getHeaderInfo(); ?>
        <script type="text/javascript" src="script/storage/storage.js"></script>
        <link href="style/storage/storage.less" rel="stylesheet/less">
    </head>

    <body>
        <?php echo $PAGE->getTopBanner(); ?>

        <div class="main">
            <div id="storage-view">
                <div id="storage-title">
                    Storage Units    
                </div>
                
                <div class="storage-list-view">
                    <div id="storage-list">
                        
                    </div>
                </div>

            </div>
        </div>


    </body>

</html>