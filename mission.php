<?php

require("mission/MissionController.php");
require("common/page.php");
require("common/itemUtils.php");


$missionController = new MissionController();
$itemUtils = new ItemUtils();


$MISSION = null;
$REQUIREMENTS    = null;

if($MISSION = $missionController->getMissionInfo(1))
{
    if($REQUIREMENTS = $missionController->getMissionRequirements(1))
    {
        
    }
    else
    {
        die("Failed to load mission requirements!");
    }
}
else
{
    die("Failed to load mission");
}

$pageUtils = new PageClass();



?>

<html>
    <head>
        <? echo $pageUtils->getHeaderInfo(); ?>

        <link href="style/mission/mission.less" rel="stylesheet" type="text/css">

        <title>King of Mota | Missions</title>

    </head>
    <body>

    <? echo $pageUtils->getTopBanner(); ?>

        <div id="mission-view">
            <div class="mission-title">
                <?php echo $MISSION['name']?>
            </div>

            <div class="mission-requirements">
                <?php

                foreach ($REQUIREMENTS AS $REQ)
                {
                    $ITEM_ID = $REQ['ITEM_ID'];

                    $ITEM_AMOUNT = $REQ['ITEM_AMOUNT'];

                    $ITEM = $itemUtils->getItemInfo($ITEM_ID);

                    $ITEM_TITLE = $ITEM['name'];
                    $ITEM_IMG   = $ITEM['picture'];

                    echo    "<div class='requirement-item global_inventory-item'>
                                <div class='requirement-item-amount'>
                                    <div class='amount-current'>
                                                               
                                    </div>
                                    <div class='amount-total'>
                                        $ITEM_AMOUNT <span>x</span>
                                    </div>                    
                                </div>
                                
                                <div class='requirement-item-image-div'>
                                    <img class='requirement-item-image global_item-image global_inventory-item-img' src='$ITEM_IMG'>                           
                                </div>
                                
                                <div class='requirement-item-title'>
                                    $ITEM_TITLE                                    
                                </div>

                            </div>";
                }

                ?>
            </div>

            <div class="mission-description">
                <span>
                    <?php echo $MISSION['description']?>
                </span>
            </div>
        </div>
        <!--            MISSION-VIEW                -->


    </body>

</html>









