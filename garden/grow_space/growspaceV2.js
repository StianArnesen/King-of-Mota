const ID_FARM_VIEW  = "#farming-view";
const ID_INV_GRID   = "#growing-items-grid";

function getTime(sec){

    var h = Math.floor(sec / 3600);
    var m = Math.floor((sec - (h*3600)) / 60);
    var s = sec - (m*60) - (h*3600);

    var mm = (m > 0)? m + "m ": " ";
    var hh = (h >0)? h + "h ": " ";

    var ss = (s >0)? s + "s ": " ";
    if(h > 0)
    {
        return hh + mm;
    }
    return hh + mm + ss;
}

var $activeSetupItem;



function getGardenImage(TIME_LEFT, TIME_TOTAL){

    var img1 = "img/plant_stages/v2/10.png";
    var img2 = "img/plant_stages/v2/25.png";
    var img3 = "img/plant_stages/v2/50.png";
    var img4 = "img/plant_stages/v2/75.png";
    var img5 = "img/plant_stages/v2/100.png";

    if( ( (TIME_LEFT / TIME_TOTAL) * 100) <= 10){
        return img1;
    }
    else if( ( (TIME_LEFT / TIME_TOTAL) * 100) <= 25){
        return img2;
    }
    else if( ( (TIME_LEFT / TIME_TOTAL) * 100) <= 50){
        return img3;
    }
    else if( ( (TIME_LEFT / TIME_TOTAL) * 100) <= 75){
        return img4;
    }
    else{
        return img5;
    }
    return "ERROR";
}

function getShortString(str, len){
    if(str.length > len + 3){
        return str.substring(0, len) + "...";
    }
    else{
        return str;
    }
}

function getGardenItemLayout(NAME, TIME_LEFT, TIME_TOTAL, IMG, WID, SPACE_ID, REPLACE_HTML){

    var timeText = getTime(TIME_LEFT);

    var complete_text = "<div class='garden-item-complete-text'> <div class='garden-item-button-harvest'>Harvest !</div> </div>";

    if(TIME_LEFT <= 0){
        timeText = "Complete!";
    }
    else{
        complete_text = "<div class='garden-item-complete-text' > <span>" + getTime(TIME_LEFT) + "</span> </div>";
        complete_text += "<div class='garden-item-time-left-div'>";
        complete_text += "<div class='garden-item-time-left-bar' style='width: 100%'></div>";
        complete_text += "</div>";
    }

    var result = "";

    if(REPLACE_HTML == 0)
    {

    }
    else
    {
        result += 	"<div class='garden-item item-active'>";
    }

    /*				TITLE				*/
    result +=			"<div class='garden-item-title'>";
    result +=				getShortString(NAME, 11);
    result +=			"</div>";

    /*				IMAGE				*/
    result +=			"<div class='garden-item-img'>";
    result +=				"<img src='" + IMG + "' draggable='false'>";
    result +=			"</div>";


    /*				TIME LEFT / COMPLETE TEXT				*/
    result +=			complete_text;

    result +=  "<div class='time-left-div'>";
    result +=  "<input type='hidden' name='item-time-left' class='garden-grid-item-time-left' value=" + TIME_LEFT + ">";
    result +=  "<input type='hidden' name='item-time-total' class='garden-grid-item-time-total' value=" + TIME_TOTAL + ">";
    result +=  "<input type='hidden' name='item-wait-id' class='garden-grid-item-time-total' value=" + WID +">";
    result +=  "<input type='hidden' name='space-id' class='garden-grid-item-id' value=" + SPACE_ID +">";

    result +=  "</div>";

    result +=			"</div>";
    if(REPLACE_HTML == 0) {

    }
    else
    {
        result += 		"</div>";
    }

    return result;
}

function getGardenEmptyItemLayout(SPACE_ID, LAYOUT_EMPTY)
{
    var result = "";

    if(LAYOUT_EMPTY == 1)
    {

    }
    else
    {
        result += 	"<div class='garden-item item-empty'>";
    }

    /*				TITLE				*/
    result +=			"<div class='garden-item-title'>";
    result +=				"Empty space";
    result +=			"</div>";


    /*				IMAGE				*/
    result +=			"<div class='garden-item-img'>";
    result +=				"<img src='img/garden/drop_weed.png' draggable='false'>";
    result +=			"</div>";
    result +=  "<input type='hidden' name='space-id' class='garden-grid-item-id' value=" + SPACE_ID +">";


    /*				TIME LEFT / COMPLETE TEXT				*/

    if(LAYOUT_EMPTY == 1)
    {

    }
    else
    {
        result += 		"</div>";
    }

    return result;
}

function loadGardenItems(){
    $.post("garden/garden_utils.php", {grow_list: 1}, function(result){

        $().html("");
        var items = JSON.parse(result);

        var HTML = "";

        try{
            if(items['error'])
            {
                console.log("Query returned error. -<loading garden items>-");
            }
        }catch(e){

        }

        for(var i = 0; i < items.length; i++)
        {
            if(items[i]['empty'] != 1)
            {
                var item = items[i];

                console.log(item['item_name']);
                //HTML += getGardenItemLayoutVersionTwo(item['item_name'], item['time_left'], item['time_total']);
                HTML += getGardenItemLayout(item['item_name'], item['time_left'], item['time_total'], item['item_img'], item['item_wid'], item['space_id'], 1);
            }
            else
            {
                var item = items[i];
                HTML += getGardenEmptyItemLayout(item['space_id'], 0);
            }
        }

        $(ID_INV_GRID).html(HTML);
        $(ID_INV_GRID).show(300);

        initGardenItemHarvestMethod();



    });
}

function loadTimerMethod(){
    setInterval(function()
    {
        $(".garden-item").each(function(index)
        {
            var timeLeft = $(this).find("input[name='item-time-left']").val();
            var timeTotal = $(this).find("input[name='item-time-total']").val();

            var wait_id = $(this).find("input[name='item-wait-id']").val();

            if(timeLeft)
            {
                var bar = $(this).find(".garden-item-time-left-bar");
                var barDiv = $(this).find(".garden-item-time-left-div");

                var complete_text = $(this).find(".garden-item-complete-text");

                if(timeLeft > 0)
                {
                    $(this).find("input[name='item-time-left']").val(timeLeft -1);
                    var width = (timeLeft / timeTotal) * 100 + "%";

                    complete_text.html("<span>" + getTime(timeLeft) + "</span>");
                    bar.width(width);
                }
                else
                {
                    if(! $(this).hasClass('item-complete')){
                        $(this).addClass('item-complete');
                        $(this).removeClass('item-active');
                    }

                    barDiv.slideUp(200);
                    complete_text.html("<div class='garden-item-button-harvest' onclick='harvestItem(" + wait_id + ")'>Harvest !</div>");
                }
            }
        });
    }, 1000);
}

$(document).ready(function(){

    $(ID_INV_GRID).hide(15);
    loadGardenItems();

    $(ID_INV_GRID).show(315);
    loadTimerMethod();

});

function addTimerMethodToObject($obj){

}


var gfx_item_id = 0;

function addGfx(value, prefix, position)
{
    var posX = position.left;
    var posY = position.top;

    var HTML = $('#effects-container').html() + '<div id="gfx-item-id-'+ gfx_item_id +'" class="effect-item">+'+ value + prefix +'</div>';

    if(prefix == " G")
    {
        HTML = $('#effects-container').html() + '<div id="gfx-item-id-'+ gfx_item_id +'" class="effect-item-green">+'+ value + prefix +'</div>';
        posY += 55;
    }




    $('#effects-container').html(HTML);

    console.log("Effect added at posX: " + posX + ", posY: " + posY);

    var object = '#gfx-item-id-' + gfx_item_id;

    setTimeout(function(){
        $(object).fadeOut(300);
    }, 15500);

    $(object).css({top: posY, left: posX + 35, position:'fixed'})
    $(object).animate({
        top: "-=250px",
    }, 800);

    gfx_item_id++;
}






function initGardenItemHarvestMethod() {

    $(".garden-item").click(function()
    {
        var $gardenItem = $(this);

        var timeLeft = $(this).find("input[name='item-time-left']").val();
        var timeTotal = $(this).find("input[name='item-time-total']").val();

        var wait_id = $(this).find("input[name='item-wait-id']").val();

        if(timeLeft)
        {
            if(timeLeft <= 0)
            {
                $.post("garden/grow_space/gardenController.php", {harvest_plant_wait_id: wait_id}, function(data)
                {
                    var RESULT 			= JSON.parse(data);

                    var itemResult 		= RESULT['SPACE_INFO'];

                    var G_COINS			= parseInt(RESULT['G_COINS']);

                    var RESPONSE_STATUS = RESULT['STATUS'];
                    var RESPONSE_ERR 	= RESULT['ERROR'];

                    if(RESPONSE_STATUS == "OK")
                    {
                        $gardenItem.html(getGardenEmptyItemLayout(itemResult['space_id'], 1));

                        if($gardenItem.hasClass('item-complete'))
                        {
                            $gardenItem.removeClass('item-active');
                        }

                        if(! $gardenItem.hasClass('item-empty'))
                        {
                            $gardenItem.addClass('item-empty')
                        }
                        if($gardenItem.hasClass('item-complete'))
                        {
                            $gardenItem.removeClass('item-complete');
                        }


                        var EXP_EARNED = RESULT['EXP'];
                        addGfx(EXP_EARNED, " EXP", $gardenItem.position());
                        setTimeout(function(){
                            if(G_COINS > 0)
                            {
                                addGfx(G_COINS, " G", $gardenItem.position());
                            }

                        }, 200);


                        updateUserInfoArray();
                        playAudio("harvest");
                    }
                    else if(RESPONSE_STATUS.length > 2 && RESPONSE_ERR.length > 0)
                    {
                        showGameDialogByServerResponse(RESPONSE_ERR);
                    }
                });
            }
        }
        else
        {
            if(wait_id)
            {
                $.post("garden/grow_space/gardenController.php", {harvest_plant_wait_id: wait_id}, function(data)
                {
                    var itemResult = JSON.parse(data);

                    if(itemResult['empty'] == 1)
                    {
                        if(	$gardenItem.hasClass('item-complete'))
                        {
                            $gardenItem.removeClass('item-complete');
                        }
                        $gardenItem.html(getGardenEmptyItemLayout(itemResult['space_id'],1));

                        if(	$gardenItem.hasClass('item-active'))
                        {
                            $gardenItem.removeClass('item-active');

                            if(! $gardenItem.hasClass('item-empty'))
                            {
                                $gardenItem.addClass('item-empty')
                            }
                        }
                        playAudio("harvest");
                        updateUserInfoArray();
                    }
                });
            }
            else
            {
                var spaceID = $(this).find("input[name='space-id']").val();

                showGrowSpaceSetup(spaceID);
                $activeSetupItem = $gardenItem;

                playAudio('click');
            }
        }

    });

}

function showGrowSpace(id)
{

}
function showGrowSpaceSetup(id)
{

    $("#grow-space-preloader").show();

    $.post("garden/garden_utils.php", {get_space_setup: id}, function(data)
    {
        $("#overlay-data").html(data);

        $("#overlay-view").fadeIn(1);
        $("#overlay-data").fadeIn(1);

        $("#grow-space-preloader").fadeOut(100);

        $("#setup-dialog").fadeIn(2);
        initSpaceSetupListeners(id);

    });

}


/*
 * 		void initSpaceSetupListeners()
 *
 * 			Add listeners to each inventory item - shown in the space setup overlay-view.
 *
 * */
function initSpaceSetupListeners(id)
{
    $(".inventory-item").click(function()
    {

        var plant = $(this).find("input[name='inv-id-value']").val();

        $.post("garden/grow_space/gardenController.php", {SPACE_ID: id, PLANT_ID: plant}, function(data)
        {
            var item = JSON.parse(data);

            if(! item['error'])
            {
                $activeSetupItem.html(getGardenItemLayout(item['item_name'], item['time_left'], item['time_total'], item['item_img'], item['item_wid'], item['space_id'], 0));

                if(! $activeSetupItem.hasClass('item-active'))
                {
                    $activeSetupItem.addClass('item-active');

                    if($activeSetupItem.hasClass('item-empty'))
                    {
                        $activeSetupItem.removeClass('item-empty')
                    }
                }

                playAudio("grow");
                closeGrowSpace();
                updateUserInfoArray();
            }
            else{
                console.log("Failed to start growing! Query returned error: \n " + item['error']);
            }
        });

    });
}


function setInventoryPage(page, dir)
{
    $.post("garden/garden_utils.php", {getInventoryPage: page}, function(data)
    {
        $("#grow-space-inv-view").html(data);

        setTimeout(function()
        {
            dragAndDropInit();
        }, pageRotationSpeed);
    });
}

function closeGrowSpace()
{

    $("#grow-space-info-view").slideUp(100);

    $("#overlay-view").fadeOut(100);
    $("#overlay-data").fadeOut(100);

    animateObject(".grow-space-progress-view");
    $("#confirm-dialog").hide();
}
function animateObject(elementSelector)
{
    $(elementSelector).animate({
        opacity: 1,
        top: "+=365",
        height: "toggle"
    }, 55, function() {

    });
}

function updateGardenItems()
{
    loadGardenItems();
}
function confirmBuySpace()
{
    $.post("garden/garden_utils.php", {buy_more_space: 1}, function(data)
    {
        console.log(data);
        $(document).html(data);
        if(data)
        {

        }
        else
        {
            $("#overlay-view").fadeOut(100);
            $("#overlay-data").fadeOut(100);

        }
    });
    updateGardenItems();
}

function updateFarmingView(){

    var infoArray =  $.getJSON("farming/FarmingView.php?farm_info", function(result)
    {
        $("#farm_name").html(result.title);
        $("#farm-image").attr('src', result.farm_image);

        $("#farm_level").html(result.farm_level);
        $("#farm-upgrade-price").html("$ " + result.farm_price.formatMoney(0, '.', ','));
        if(CURRENT_PLAYER_MONEY >= result.farm_price)
        {
            if(! $("#farm-upgrade-price").hasClass("affordable"))
            {
                $("#farm-upgrade-price").addClass("affordable");
            }
        }
        else
        {
            if($("#farm-upgrade-price").hasClass("affordable"))
            {
                $("#farm-upgrade-price").removeClass("affordable");
            }
        }

        $("#light_level").html(result.light_level);
        $("#light-upgrade-price").html("$ " + result.light_price.formatMoney(0, '.', ','));
        if(CURRENT_PLAYER_MONEY >= result.light_price)
        {
            if(! $("#light-upgrade-price").hasClass("affordable"))
            {
                $("#light-upgrade-price").addClass("affordable");
            }
        }
        else
        {
            if($("#light-upgrade-price").hasClass("affordable"))
            {
                $("#light-upgrade-price").removeClass("affordable");
            }
        }

        $("#air_level").html(result.air_level);
        $("#air-upgrade-price").html("$ " + result.air_price.formatMoney(0, '.', ','));
        if(CURRENT_PLAYER_MONEY >= result.air_price)
        {
            if(! $("#air-upgrade-price").hasClass("affordable"))
            {
                $("#air-upgrade-price").addClass("affordable");
            }
        }
        else
        {
            if($("#air-upgrade-price").hasClass("affordable"))
            {
                $("#air-upgrade-price").removeClass("affordable");
            }
        }


        $("#soil_level").html(result.soil_level);
        $("#soil-upgrade-price").html("$ " + result.soil_price.formatMoney(0, '.', ','));

        if(CURRENT_PLAYER_MONEY >= result.soil_price)
        {
            if(! $("#soil-upgrade-price").hasClass("affordable"))
            {
                $("#soil-upgrade-price").addClass("affordable");
            }
        }
        else
        {
            if($("#soil-upgrade-price").hasClass("affordable"))
            {
                $("#soil-upgrade-price").removeClass("affordable");
            }
        }
    });

    $.post("market/MarketController.php", {get_market_boost: 1}, function(data)
    {
        $("#air_level_boost").html("Boost: " + Math.round((data*100)-100) + "%");
    });
    $.post("garden/grow_space/gardenController.php", {get_light_boost: 1}, function(data)
    {
        $("#light_level_boost").html("boost: " + (100 - Math.round(data*100)) + "%");
    });
}




function initUpgrades(){
    $("#light_upgrade_button").click(function(){
        $.post("farming/FarmingController.php", {upgrade_light: 1}, function(RESULT){
            if(RESULT == 1){
                playAudio("buy");
                updateFarmingView();
                loadProfileInfo();
            }
        });
    });

    $("#farm_upgrade_button").click(function(){
        $.post("farming/FarmingController.php", {upgrade_farm: 1}, function(RESULT){

            if(RESULT == 1)
            {
                updateFarmingView();
                updateGardenItems();
                playAudio("buy");
                loadProfileInfo();
            }

        });

    });
    $("#air_upgrade_button").click(function(){
        $.post("farming/FarmingController.php", {upgrade_air: 1}, function(RESULT){

            if(RESULT == 1)
            {
                playAudio("buy");
                updateFarmingView();
                loadProfileInfo();
            }
        });

    });
    $("#soil_upgrade_button").click(function(){
        $.post("farming/FarmingController.php", {upgrade_soil: 1}, function(RESULT){

            if(RESULT == 1)
            {
                playAudio("buy");
                updateFarmingView();
                loadProfileInfo();
            }
        });

    });
}
$(document).ready(function(){

    
    $(ID_FARM_VIEW).load("layout/farm_view/farm_view.html");
    updateFarmingView();
    setInterval(function(){
        var totalTime = document.getElementById("hidden-time-total");

        var timeLeft;
        try{
            timeLeft = document.getElementById("hidden-time-left");



            var visibleTimeLeft = document.getElementById("time-left-text");

            var bar = document.getElementById("grow-space-bar");

            var barWidth;

            if(timeLeft.value > 0)
            {
                barWidth = ((timeLeft.value/totalTime.value)*100);
            }
            else
            {
                if(timeLeft.value == 0){
                    $.post( "garden/grow_space/GrowSpaceInfo.php",{space_id: id}, function( data ) {
                        $("#overlay-data").html(data);
                    });
                }

                barWidth = 100;
            }



            bar.style.width = barWidth + "%";

            timeLeft.value--;
            if(timeLeft.value > 0)
            {
                visibleTimeLeft.innerHTML = "Time left: <div class='status-time'>  " + getTime(timeLeft.value) + " seconds </div>";
            }
            else
            {
                visibleTimeLeft.innerHTML = "<div class='status-complete'> Complete! </div>";
            }
        }catch( e )
        {
            return;
        }
        initGardenProgressBars();

    }, 1000);

    setTimeout(function(){
        initUpgrades();
    },500);

});



Number.prototype.formatMoney = function(c, d, t){
    var n = this,
        c = isNaN(c = Math.abs(c)) ? 2 : c,
        d = d == undefined ? "." : d,
        t = t == undefined ? "," : t,
        s = n < 0 ? "-" : "",
        i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
        j = (j = i.length) > 3 ? j % 3 : 0;
    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};




