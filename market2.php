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


$PAGE = new PageClass();

$marketController = new MarketController();


if(isset($_SESSION['game_user_id']) && isset($_SESSION['game_username']))
{
    $ID = $_SESSION['game_user_id'];
    $NAME = isset($_SESSION['game_username']);

    $currentUser = new User($ID, $NAME);
}

$USER_INVENTORY_QUERY = $marketController->getValidUserInventoryToSellQuery();

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
        <script src="http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js" type="text/javascript"  ></script>


        <title>King of Mota | Market </title>
    </head>

    <body>
    <?php
    echo $PAGE->getTopBanner();
    ?>
        <div id="content">
            <div class="market-view">
                <div class="inventory-view">
                    <div class="inventory-view-title"><span>Inventory</span></div>
                    <div class="inventory-view-items" id="inventory-view-items">

                        <?php

                        while($INVENTORY_ITEM = mysqli_fetch_array($USER_INVENTORY_QUERY))
                        {
                            echo "<div class='inventory_item'> <input type='hidden' name='item-id-value' value='". $INVENTORY_ITEM['item_id'] ."'> <input type='hidden' name='inv-id-value' value='". $INVENTORY_ITEM['inv_id'] ."'> ";
                            echo "<div class='inv_item_title'> <span>" . getExcerpt($INVENTORY_ITEM['name'], 15) . "</span></div>";

                            echo "<div class='item-info-view'>";
                                echo "<div class='inv_item_amount'>" . $INVENTORY_ITEM['item_amount'] . "</div>" ;
                            echo "</div>";


                            echo "<img class='item_final_img' src='" . $INVENTORY_ITEM['picture'] . "'>" ;
                            echo "</div>";
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

<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js" type="text/javascript"></script>

<script type="text/javascript">


    
    function playAudio(type)
    {
        switch(type)
        {
            case "buy":
                var audio = new Audio("sound/ca_ching.mp3");
                audio.volume = 0.2;
                audio.play();
        }
    }

    var selectedItems = new Array();

    function updateUserInfo()
    {
        $("#profile_info_money").load("common/userinfo.php?get_info=0");

        $("#profile_info_level").load("common/userinfo.php?get_info=1");
        $("#user_level_bar_progress").load("common/userinfo.php?get_info=2");
    }

    function sellSelectedItems() {

        console.log("Selling selected items!");
        $.post("market/MarketController.php", {sell_items_list: selectedItems}, function(result){
            console.log("Result: " + result);
            setTimeout(function(){
                updateUserInfo();
                selectedItems = [];

                updateUserInfo();
                playAudio("buy");

                $("#market-items-drop-view").html("");

                $("#market-item-response-list").html("<span>Summary of selected items will appear here.</span>");

            },200);

        });
    }

    $(document).ready(function(){
        console.warn("DOCUMENT READY");

        $(function()
        {
            counts = [ 0, 0, 0 ];

            $(".inventory_item").draggable(
            {
                containment: "#content", scroll: false, snap: false, revert: "invalid",
                start: function()
                {
                    if(! $(this).hasClass("ui-state-hover"))
                    {
                        $(this).addClass( "ui-state-hover" );
                    }

                },
                drag: function()
                {

                },
                stop: function()
                {
                    console.log("-------------");
                    for(var i = 0; i < selectedItems.length; i++)
                    {
                        console.log(selectedItems[i]);
                    }
                    $.post("market/MarketController2.php", {get_inv_item_val: selectedItems}, function(result){
                        $("#market-item-response-list").html(result);
                    });
                    $(this).removeClass( "ui-state-hover" );
                },

            });
        });
        $( "#market-items-drop-view" ).droppable(
        {
            activeClass: "ui-state-default",
            hoverClass: "ui-state-target-hover",

            drop: function( event, ui )
            {
                var dropped = ui.draggable;
                var droppedOn = $(this);


                var newValue = dropped.find("input[name='inv-id-value']").val();


                var index;

                index = selectedItems.indexOf(newValue)

                if(index == - 1){
                    selectedItems.push(newValue);
                }




                $(dropped).detach().css({top: 0,left: 0}).appendTo(droppedOn);

                if( $( this ).hasClass( "ui-state-target-hover" )){
                    $( this ).removeClass( "ui-state-target-hover" );
                }

                $( this ).addClass( "ui-state-target-dropped" );
               if(! muted)
                    {
                        var audio = new Audio('sound/drop.mp3');
                        audio.volume = 0.3;
                        audio.play();    
                    }

            },
            over: function(event, ui)
            {
                if($(this).hasClass('market-items-drop-view'))
                {
                    if(! $( this ).hasClass( "ui-state-target-hover" )){
                        $( this ).addClass( "ui-state-target-hover" );
                    }
                }
            }
        });
        $( "#inventory-view-items" ).droppable(
        {
            activeClass: "ui-state-default",


            drop: function( event, ui )
            {
                var dropped = ui.draggable;
                var droppedOn = $(this);

                var newValue = dropped.find("input[name='inv-id-value']").val();


                var index = selectedItems.indexOf(newValue);
                selectedItems.splice(index, 1);

                $(dropped).detach().css({top: 0,left: 0}).appendTo(droppedOn);

                $( this ).addClass( "ui-state-target-dropped" );
                if(! muted)
                    {
                        var audio = new Audio('sound/drop.mp3');
                        audio.volume = 0.3;
                        audio.play();    
                    }

            },
            over: function(event, ui)
            {

            }
        });



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
    });

</script>




