/**
 * Created by StianDesktop on 2016-01-27.
 */

/*
$(document).ready(function(){

    dragAndDropInit();

    function dragAndDropInit()
    {
        $(function()
        {
            counts = [ 0, 0, 0 ];

            $('.inventory-item').draggable(
                {
                    containment: ".inventory-view-items", scroll: false, revert: true, snap: false, revert: "invalid", hold: "false"
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
                        $(this).removeClass( "ui-state-hover" );
                    },

                });

            function updateCounterStatus()
            {

            }
        });
        $( ".drop-target" ).droppable(
            {
                activeClass: "ui-state-default",
                hoverClass: "ui-state-target-hover",

                drop: function( event, ui )
                {
                    var dropped = ui.draggable;
                    var droppedOn = $(this);


                    $(this).droppable( 'disable' );

                    var newValue = dropped.find("input[name='item-inv-id-name']").val();

                    $(this).find("input[name='inv-id-input']").val(newValue);

                    $(dropped).detach().css({top: 0,left: 0}).appendTo(droppedOn);

                    playAudio("drop");

                },
                over: function(event, ui)
                {
                    var hovering = ui.draggable;

                     if(hovering.hasClass("inv-item-type-3") && $(this).hasClass("drop-target-light'"))
                     {
                     $(this).droppable( 'enable' );
                     }
                     else
                     {
                     $(this).droppable( 'disable' );
                     }
                }
            });$(function()
    {

        counts = [ 0, 0, 0 ];

        $('.inventory-item').draggable(
            {
                containment: ".inventory-view-items", scroll: false, revert: true, snap: false, revert: "invalid",
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
                    $(this).removeClass( "ui-state-hover" );
                },

            });

        function updateCounterStatus()
        {

        }
    });
        $( ".drop-target" ).droppable(
            {
                activeClass: "ui-state-default",
                hoverClass: "ui-state-target-hover",

                drop: function( event, ui )
                {
                    var dropped = ui.draggable;
                    var droppedOn = $(this);


                    $(this).droppable( 'disable' );

                    var newValue = dropped.find("input[name='item-inv-id-name']").val();

                    $(this).find("input[name='inv-id-input']").val(newValue);

                    $(dropped).detach().css({top: 0,left: 0}).appendTo(droppedOn);

                    $( this ).addClass( "ui-state-target-dropped" );
                    playAudio('drop');
                    

                },
                over: function(event, ui)
                {
                    var hovering = ui.draggable;

                     if(hovering.hasClass("inv-item-type-3") && $(this).hasClass("drop-target-light'"))
                     {
                     $(this).droppable( 'enable' );
                     }
                     else
                     {
                     $(this).droppable( 'disable' );
                     }
                }
            });
    }
});

*/
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

            updateUserInfoArray();

            playAudio("buy");

            $("#market-items-drop-view").html("");

            $("#market-item-response-list").html("<span>Summary of selected items will appear here.</span>");

        },200);

    });
}

function sendSelectedItemsForPriceList(inventoryItemObject)
{
    console.log("-------------");
    for(var i = 0; i < selectedItems.length; i++)
    {
        console.log(selectedItems[i]);
    }
    $.post("market/MarketController.php", {get_inv_item_val: selectedItems}, function(result){
        $("#market-item-response-list").html(result);
    });
    $(inventoryItemObject).removeClass( "ui-state-hover" );
}
$(document).ready(function(){
    console.warn("DOCUMENT READY");

    $(function()
    {
        $(".inventory-item").dblclick( function(){
                let newValue = $(this).find("input[name='inv-id-value']").val();
                
                let index = selectedItems.indexOf(newValue)

                if(index == - 1){
                    selectedItems.push(newValue);
                }
                selectedItems.push(index);
                $(this).detach().css({top: 0,left: 0}).appendTo($("#market-items-drop-view"));

                if( $( this ).hasClass( "ui-state-target-hover" )){
                    $( this ).removeClass( "ui-state-target-hover" );
                }

                $( this ).addClass( "ui-state-target-dropped" );
                playAudio('drop');
                sendSelectedItemsForPriceList($(this));
        });
        counts = [ 0, 0, 0 ];

        $(".inventory-item").draggable(
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
                    sendSelectedItemsForPriceList($(this));
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
                playAudio('drop');

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
                playAudio('drop');

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