<?php

require("common/page.php");
require("stats/StatsController.php");

$STATS_CONTROL  = new StatsController();
$PAGE           = new PageClass();

$STATS = $STATS_CONTROL->getAllStatsFromUser();

$TOTAL_HARVESTS     = $STATS['plant_harvest_amount'];
$TOTAL_MONEY        = $STATS['money_total'];
$TOTAL_MONEY_USED   = $STATS['money_used'];

?>





<html>
    <head>
        <title>King of Mota | Stats</title>
        <link href="style/stats/stats.less" rel="stylesheet/less">
        <? echo $PAGE->getHeaderInfo() ?>
    </head>
    
    <? echo $PAGE->getTopBanner() ?>

    <body>
        <div class="main">
            <div class="stats-view">
                <div class="title">
                    Game stats
                </div>

                <div class="stats-item-view">
                    <div class="stats-item">
                        <div class="stats-item-text">Total plants harvested:</div>
                        <div class="stats-item-value">
                            <?php echo $TOTAL_HARVESTS?>
                        </div>
                    </div>
                    <div class="stats-item">
                        <div class="stats-item-text">Total money earned:</div>
                        <div class="stats-item-value">
                            <?php echo $TOTAL_MONEY?> $
                        </div>
                    </div>
                    <div class="stats-item">
                        <div class="stats-item-text">Total money spent:</div>
                        <div class="stats-item-value">
                            <?php echo $TOTAL_MONEY_USED?> $
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </body>

</html>