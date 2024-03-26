



/*         OVERLAY-ATTRIBUTES             */

const OVERLAY_ATTR_STATUS       = "#overlay-status";
const OVERLAY_ATTR_ITEM_IMG     = "#overlay-item-img";
const OVERLAY_ATTR_PRICE        = "#overlay-item-price";
const OVERLAY_ATTR_AMOUNT       = "#overlay-item-amount";
const OVERLAY_ATTR_TITLE        = "#overlay-item-title";
const OVERLAY_LABEL_AMOUNT      = "#dialog-amount";

const OVERLAY_ATTR_DIALOG_TITLE = "#dialog-title";






$(document).ready(itemMain);


function itemMain()
{
    initListener();
}

function initListener()
{
    $("#buy_form_submit").click(function()
    {
        buyItem();
        showOverlay();
    });

    $("#overlay-view").click(function()
    {
        hideOverlay();
    });
}


function setOverlayData(ELEMENT, ATTR, DATA)
{
    switch (ATTR)
    {
        case 0:
            $(ELEMENT).html(DATA);
            break;
        case 1:
            $(ELEMENT).css(DATA);
            break;
        default:
            break;

    }

}

function showOverlay()
{
    $("#overlay-view").fadeIn(200);
    $("#overlay-data").fadeIn(200);
    $("#overlay-data").css("transform", "scale(1)");

    setTimeout(function(){
        hideOverlay();
    }, 2000);

}
function hideOverlay()
{
    $("#overlay-data").css("transform", "scale(0.1)");
    $("#overlay-view").fadeOut(200);
    $("#overlay-data").fadeOut(200);
}

function buyItem()
{
    var ITEM_ID     = $("#current_item_id").val();
    var ITEM_AMOUNT = $("#item_buy_amount").val();

    var TOTAL_PRICE = $("#item_amount_price_label").val() * ITEM_AMOUNT;

    console.log("Buying item...");

    $.post("shop/PublicShopcart.php", {BUY_ITEM: 1, ID: ITEM_ID, AMOUNT: ITEM_AMOUNT}, function(data)
    {
        var RESULT = JSON.parse(data);

        console.log("Buy result: \n" + data);

        if(RESULT['STATUS'] == "OK")
        {
            setOverlayData(OVERLAY_ATTR_AMOUNT, 0, "Amount: " + ITEM_AMOUNT);
            setOverlayData(OVERLAY_ATTR_PRICE, 0, "Price: " + parseInt(TOTAL_PRICE).formatMoney(0, '.', ',') + " $");
            $(OVERLAY_ATTR_PRICE).css("color", "darkred");

            $("#dialog-title").css("background-color", "green");
            $(OVERLAY_LABEL_AMOUNT).html(ITEM_AMOUNT + "x");
            playAudio("buy");

        }
        else
        {
            var ERR         = RESULT['DBUG_MSG'];
            var STATUS_MSG  = "";

            switch (ERR)
            {
                case "SPACE":
                    STATUS_MSG = "Not enough space in inventory!";
                    break;
                case "MONEY":
                    STATUS_MSG = "Not enough money!";
                break;
            }


            playAudio("error");

            setOverlayData(OVERLAY_ATTR_STATUS, 0, "Status: " + STATUS_MSG);
            setOverlayData(OVERLAY_ATTR_AMOUNT, 0, "Amount: " + ITEM_AMOUNT);
            setOverlayData(OVERLAY_ATTR_PRICE, 0, "Price: " + parseInt(TOTAL_PRICE).formatMoney(0, '.', ',') + " $");
            setOverlayData(OVERLAY_ATTR_PRICE, 1, "color: red;");

            $(OVERLAY_ATTR_PRICE).css("color", "darkred");
            $("#dialog-title").css("background-color", "darkred");
        }

        updateUserInfoArray();
    });
}

