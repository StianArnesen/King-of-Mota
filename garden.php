<?php

    require("connect/connection.php");
    require("connect/database.php");
    require("common/page.php");
    require("garden/garden_utils.php");
    //include("common/session/sessioninfo.php");
    require("garden/gardenitem.php");
    require("farming/FarmingController.php");
    
    if(isset($_SESSION['game_user_id']) && isset($_SESSION['game_username']))
    {
        $ID = $_SESSION['game_user_id'];
        $NAME = isset($_SESSION['game_use8name']);
        
        $currentUser = new User($ID, $NAME);
    }
    else{
       
    }

    $PAGE = new PageClass();
    
    $garden_utils = new GardenUtils();
    
    $staticConnection = new StaticConnection();
    $farmingController = new FarmingController();
    
    $dbCon = Database::getConnection();
    
    $UPGRADE_LEVELS = $farmingController->getCurrentUpgradeItemsInfoArray();
    $UPGRADE_LEVEL_PRICE_LIST = $farmingController->getUpgradeItemsPriceList();

?>

<html>
    <head>
        <title>King of Mota | Garden </title>
        <link href="style/LESS/garden/garden.less" rel="stylesheet/less">
        <?php echo $PAGE->getHeaderInfo(); ?>
        
    </head>
    <body>

    <?php
        echo $PAGE->getTopBanner();
    ?>


<div id="effects-container">
    
</div>

<div class="growing-view">
    <div class="farming-view-title"><?echo $currentUser->getUsername() . "'s farm"?></div>
    <div class="growing-view-title" id="farming-view">
        
    </div>

    <div id="growing-view-loading" style="display: none;"><h1>Loading...<h1></div>
    <div id="growing-items-view">
        <div id="growing-items-grid">
            
        </div>
    </div>
</div>



<img src="img/preloader/preloader.gif" id="grow-space-preloader">

<div id="overlay-view"> </div>
<div id="overlay-data" class="overlay-data"> </div>

<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js" type="text/javascript"></script>
 <!--                        GAME-DIALOG-SRC                                -->
    <script src="utils/overlay_dialog/GameDialog.js" type="text/javascript"></script>
    <link href="utils/overlay_dialog/GameDialog.less" rel="stylesheet/less" type="text/css">
</body>
</html>



<script type="text/javascript">

    $(".document").ready(main);
    function updateUserInfo()
    {
        $("#profile_info_money").load("common/userinfo.php?get_info=0");
        
        $("#profile_info_level").load("common/userinfo.php?get_info=1");
        $("#user_level_bar_progress").load("common/userinfo.php?get_info=2");
    }
    function main(){

        var UIL = false;

        $("#profile_info_div").fadeIn(200);
        $("#profile_info_money").load("common/userinfo.php?get_info=0");

        $("#profile_info_level").load("common/userinfo.php?get_info=1");
        $("#user_level_bar_progress").load("common/userinfo.php?get_info=2");
        if(! UIL)
        {
            $("#profile_info_div").fadeIn(250);
            UIL = true;
        }
        fixPriceLabel("item_price_label");
    }
</script>

<script type="text/javascript" src="script/common/priceFix.js"> </script>
<script type="text/javascript" src="garden/grow_space/growspace.js"> </script>

</script>

<script type="text/javascript">

    $(document).ready(function(){

        $("#shop").fadeIn(400);

        var UIL = false;

        var canPlaySound = true;

        $("#overlay-view").click(function(){
            closeGrowSpace();
        });
        $(".garden-item").mouseenter(function(){
            if(canPlaySound && ! muted){
                var audio = new Audio('sound/click2.mp3');
                audio.play();
                canPlaySound = false;

                setTimeout(function(){
                    canPlaySound = true;
                }, 40);
            }

        });

        setInterval(function()
        {
            $("#profile_info_money").load("common/userinfo.php?get_info=0");
            $("#profile_info_level").load("common/userinfo.php?get_info=1");
            $("#user_level_bar_progress").load("common/userinfo.php?get_info=2");
            if(! UIL)
            {
                $("#profile_info_div").fadeIn(250);
                UIL = true;
            }
            fixPriceLabel("item_price_label");

        }, 800);
    });

fixPriceLabel("item_price_label");
</script>

</script>
