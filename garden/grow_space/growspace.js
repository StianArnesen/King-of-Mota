const ID_PRICE_LABEL_LIGHT 	= "#light-upgrade-price";
const ID_PRICE_LABEL_AIR  	= "#air-upgrade-price";
const ID_PRICE_LABEL_FARM 	= "#farm-upgrade-price";
const ID_PRICE_LABEL_SOIL  	= "#soil-upgrade-price";


/*
* 	HTML ITEM SELECTORS
*
* 
* */

const DIV_ID_GROWING_ITEMS_GRID = '#growing-items-grid';
const DIV_ID_GFX_CONTAINER 		= '#effects-container';

const ResponseStatus = Object.freeze({
	OK 		: "OK",
	ERROR 	: "FAILED"
});

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

function getShortString(str, len){
	if(str.length > len + 3){
		return str.substring(0, len) + "...";
	}
	else{
		return str;
	}
}

function getGardenItemLayout(NAME, TIME_LEFT, TIME_TOTAL, IMG, WID, SPACE_ID, REPLACE_HTML){
	
	var complete_text = "<div class='garden-item-complete-text'> <div class='garden-item-button-harvest'>Harvest !</div> </div>";

	if(TIME_LEFT <= 0){
		
	}
	else{
		complete_text = "<div class='garden-item-complete-text' > <span>" + getTime(TIME_LEFT) + "</span> </div>";
			complete_text += "<div class='garden-item-time-left-div'>";
			complete_text += "<div class='garden-item-time-left-bar' style='width: 100%'></div>";
		complete_text += "</div>";
	}

	var result = "";

	if(REPLACE_HTML !== 0) {
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
	
	if(REPLACE_HTML !== 0) {
        result += 		"</div>";
	}
	
	return result;
}

function getGardenEmptyItemLayout(SPACE_ID, LAYOUT_EMPTY)
{
	var result = "";

	if(LAYOUT_EMPTY !== 1) {
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

	if(LAYOUT_EMPTY !== 1) {
        result += 		"</div>";
	}
	
	return result;
}


/*
* 	void loadGardenItems();
* 
* 	Description: Loads all currently growing plants with info about space and potential growing item. 
* 	
* */



function loadGardenItems(){
	$.post("garden/garden_utils.php", {grow_list: 1}, function(result){
		
		$(DIV_ID_GROWING_ITEMS_GRID).html("");
		var items = JSON.parse(result);

		var HTML = "";

		try{
			if(items['error']) {
				console.log("An error occurred while loading garden items from server.");
			}
		}catch(e){
            console.log("Could not load garden items! An error occurred while reading data from server response.");
            console.log("An error was thrown: " + e.message);
		}

		for(var i = 0; i < items.length; i++)
		{
            var item = items[i];
			
			if(item['empty'] != 1)
			{
				console.log(item['item_name']);
				HTML += getGardenItemLayout(item['item_name'], item['time_left'], item['time_total'], item['item_img'], item['item_wid'], item['space_id'], 1);
			}
			else
			{
				HTML += getGardenEmptyItemLayout(item['space_id'], 0);
			}
		}

		$(DIV_ID_GROWING_ITEMS_GRID).html(HTML);
		$(DIV_ID_GROWING_ITEMS_GRID).show(300);
		
		initGardenItemHarvestMethod();  

		

	});
}

function loadTimerMethod(){
	setInterval(function()
	{
		$(".garden-item").each(function()
		{
			var timeLeft = $(this).find("input[name='item-time-left']").val();
			var timeTotal = $(this).find("input[name='item-time-total']").val();
			
			if(timeLeft)
			{
				var bar 			= $(this).find(".garden-item-time-left-bar");
				var barDiv 			= $(this).find(".garden-item-time-left-div");
				
				var complete_text 	= $(this).find(".garden-item-complete-text");
				
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
					complete_text.html("<div class='garden-item-button-harvest'>Harvest !</div>");
				}
			}
		});
	}, 1000);
}

$(document).ready(function(){

	$(DIV_ID_GROWING_ITEMS_GRID).hide(15);
	loadGardenItems();

	$(DIV_ID_GROWING_ITEMS_GRID).show(315);
	loadTimerMethod();
	
});


var gfx_item_id = 0;

function addGfx(value, prefix, position)
{
	var posX = position.left;
	var posY = position.top;

	var HTML = $(DIV_ID_GFX_CONTAINER).html() + '<div id="gfx-item-id-'+ gfx_item_id +'" class="effect-item">+'+ value + prefix +'</div>';

	if(prefix === " G"){
		HTML = $(DIV_ID_GFX_CONTAINER).html() + '<div id="gfx-item-id-'+ gfx_item_id +'" class="effect-item-green">+'+ value + prefix +'</div>';
		posY += 55;
	}




	$(DIV_ID_GFX_CONTAINER).html(HTML);

	console.log("Effect added at posX: " + posX + ", posY: " + posY);

	var object = '#gfx-item-id-' + gfx_item_id;

	setTimeout(function(){
		$(object).fadeOut(300);
	}, 2400);

	$(object).css({top: posY, left: posX + 35, position:'fixed'})
	$(object).animate({
		top: "-=250px",
	}, { duration: 500, queue: false });

	gfx_item_id++;
}





/*
	|------------------------------------------------------------------------------------------------------------------------
	|void initGardenItemHarvestMethod();
	|------------------------------------------------------------------------------------------------------------------------
	|Description: 
	|
	|Creates mouse-click listeners for all garden items in document.
	|When plant is ready, a click will send a post form to server with "harvest" query code.
	|harvest item from grow-space.
	|------------------------------------------------------------------------------------------------------------------------
	|OBS!:
	|
	|function will only be called once. function call will occur when document is loaded 	=> 	$(document).ready(); 
	|------------------------------------------------------------------------------------------------------------------------

 */


function initGardenItemHarvestMethod() {

	$(".garden-item").click(function()
	{
		var $gardenItem = $(this);

		var timeLeft 	= $(this).find("input[name='item-time-left']").val();
		var wait_id 	= $(this).find("input[name='item-wait-id']").val();

		if(timeLeft)
		{
			if(timeLeft <= 0)
			{
				$.post("garden/grow_space/gardenController.php", {harvest_plant_wait_id: wait_id}, function(data)
				{
					let RESULT 			= JSON.parse(data);

					let itemResult 		= RESULT['SPACE_INFO'];

					let G_COINS			= parseInt(RESULT['G_COINS']);

					let RESPONSE_STATUS = RESULT['STATUS'];
					let ERROR_CODE 		= RESULT['ERROR'];
					let DIALOG_MSG 		= RESULT['DIALOG_MSG'];

					if(RESPONSE_STATUS === ResponseStatus.OK)
					{
						$gardenItem.html(getGardenEmptyItemLayout(itemResult['space_id'], 1));

						if($gardenItem.hasClass('item-complete')){
							$gardenItem.removeClass('item-active');
						}

						if(! $gardenItem.hasClass('item-empty')){
							$gardenItem.addClass('item-empty')
						}
						if($gardenItem.hasClass('item-complete')){
							$gardenItem.removeClass('item-complete');
						}


						let EXP_EARNED = RESULT['EXP'];
						
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
					else if(RESPONSE_STATUS === ResponseStatus.ERROR)
					{
						new GameDialog(DialogType.DIALOG_TYPE_ERROR, "Failed to harvest plant!", DIALOG_MSG, ERROR_CODE);
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

					if(itemResult['empty'] === 1)
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
		let result;
		try {
			result = JSON.parse(data);
			
			if(result['STATUS'] === ResponseStatus.ERROR){
				$("#grow-space-preloader").hide();
				let error_msg 	= result['DIALOG_MSG'];
				let error_code 	= result['ERROR'];
				new GameDialog(DialogType.DIALOG_TYPE_ERROR, "Already in use!", error_msg, error_code);
				return;
			}
		} catch (e) {
			$("#overlay-data").html(data);
		
			$("#overlay-view").fadeIn(1);
			$("#overlay-data").fadeIn(1);
			
			$("#grow-space-preloader").fadeOut(100);
				
			$("#setup-dialog").fadeIn(2);
			initSpaceSetupListeners(id);
		}
		
			
		
		
		

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
		if(! data)
		{
			$("#overlay-view").fadeOut(100);
			$("#overlay-data").fadeOut(100);
		}
	});
	updateGardenItems();
}

function updateFarmingView(){

	$.getJSON("farming/FarmingView.php?farm_info", function(result) 
	{
		$("#farm_name").html(result.title);
		$("#farm-image").attr('src', result.farm_image);
		
		$("#farm_level").html(result.farm_level);
		$(ID_PRICE_LABEL_FARM).html("$ " + result.farm_price.formatMoney(0, '.', ','));
		
		if(CURRENT_PLAYER_MONEY >= result.farm_price)
		{
			if(! $(ID_PRICE_LABEL_FARM).hasClass("affordable"))
			{
				var parentDiv = $(ID_PRICE_LABEL_FARM).closest(".farm-upgrade-view-level");
				$(ID_PRICE_LABEL_FARM).addClass("affordable");
				if(! $(parentDiv).hasClass("bg-green"))
				{
                    $(parentDiv).addClass("bg-green");
                    $(parentDiv).removeClass("bg-red");
				}
                
			}
		}
		else
		{
			if($(ID_PRICE_LABEL_FARM).hasClass("affordable"))
			{
				$(ID_PRICE_LABEL_FARM).removeClass("affordable");
			}
		}

		$("#light_level").html(result.light_level);
		$(ID_PRICE_LABEL_LIGHT).html("$ " + result.light_price.formatMoney(0, '.', ','));
		
		if(CURRENT_PLAYER_MONEY >= result.light_price)
		{
            var parentDiv = $(ID_PRICE_LABEL_LIGHT).closest(".farm-upgrade-view-level");
            if(! $(parentDiv).hasClass("bg-green"))
            {
                $(parentDiv).addClass("bg-green");
                $(parentDiv).removeClass("bg-red");
            }
			
			if(! $(ID_PRICE_LABEL_LIGHT).hasClass("affordable"))
			{
                $(ID_PRICE_LABEL_LIGHT).addClass("affordable");
			}
		}
		else
		{
            var parentDiv = $(ID_PRICE_LABEL_LIGHT).closest(".farm-upgrade-view-level");
            if(! $(parentDiv).hasClass("bg-red"))
            {
                $(parentDiv).addClass("bg-red");
                $(parentDiv).removeClass("bg-green");
            }
            
			if($(ID_PRICE_LABEL_LIGHT).hasClass("affordable"))
			{
				$(ID_PRICE_LABEL_LIGHT).removeClass("affordable");
			}
		}

		$("#air_level").html(result.air_level);
		$(ID_PRICE_LABEL_AIR).html("$ " + result.air_price.formatMoney(0, '.', ','));
		
		if(CURRENT_PLAYER_MONEY >= result.air_price)
		{
            var parentDiv = $(ID_PRICE_LABEL_AIR).closest(".farm-upgrade-view-level");
            if(! $(parentDiv).hasClass("bg-green"))
            {
                $(parentDiv).addClass("bg-green");
                $(parentDiv).removeClass("bg-red");
            }
			
			if(! $(ID_PRICE_LABEL_AIR).hasClass("affordable"))
			{
				$(ID_PRICE_LABEL_AIR).addClass("affordable");
			}
		}
		else
		{
            var parentDiv = $(ID_PRICE_LABEL_AIR).closest(".farm-upgrade-view-level");
            if(! $(parentDiv).hasClass("bg-red"))
            {
                $(parentDiv).addClass("bg-red");
                $(parentDiv).removeClass("bg-green");
            }
			if($(ID_PRICE_LABEL_AIR).hasClass("affordable"))
			{
				$(ID_PRICE_LABEL_AIR).removeClass("affordable");
			}
		}


		$("#soil_level").html(result.soil_level);
		$(ID_PRICE_LABEL_SOIL).html("$ " + result.soil_price.formatMoney(0, '.', ','));

		if(CURRENT_PLAYER_MONEY >= result.soil_price)
		{
            var parentDiv = $(ID_PRICE_LABEL_SOIL).closest(".farm-upgrade-view-level");
            if(! $(parentDiv).hasClass("bg-green"))
            {
                $(parentDiv).addClass("bg-green");
                $(parentDiv).removeClass("bg-red");
            }

            
			if(! $(ID_PRICE_LABEL_SOIL).hasClass("affordable"))
			{
				$(ID_PRICE_LABEL_SOIL).addClass("affordable");
			}
		}
		else
		{
            var parentDiv = $(ID_PRICE_LABEL_SOIL).closest(".farm-upgrade-view-level");
            if(! $(parentDiv).hasClass("bg-red"))
            {
                $(parentDiv).addClass("bg-red");
                $(parentDiv).removeClass("bg-green");
            }
			if($(ID_PRICE_LABEL_SOIL).hasClass("affordable"))
			{
				$(ID_PRICE_LABEL_SOIL).removeClass("affordable");
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


function upgradeLightLevel(){
	$.post("farming/FarmingController.php", {upgrade_light: 1}, function(RESULT){
		RESULT = JSON.parse(RESULT);
		if(RESULT['STATUS'] == ResponseStatus.OK){
			playAudio("buy");
			updateFarmingView();
			loadProfileInfo();
		}
		else{
			new GameDialog(DialogType.DIALOG_TYPE_ERROR, "ERROR", "Failed to upgrade light level! Server response: <br><br>" + RESULT['DIALOG_MSG']);
		}
	});
}
function upgradeFarmLevel(){
	$.post("farming/FarmingController.php", {upgrade_farm: 1}, function(RESULT){		
		RESULT = JSON.parse(RESULT);
		if(RESULT['STATUS'] == ResponseStatus.OK)
		{
			updateFarmingView();
			updateGardenItems();
			playAudio("buy");
			loadProfileInfo();
		}
		else{
			new GameDialog(DialogType.DIALOG_TYPE_ERROR, "ERROR", "Failed to upgrade farm level! Server response: <br><br>" + RESULT['DIALOG_MSG']);
		}
	});
}

function upgradeAirLevel(){
	$.post("farming/FarmingController.php", {upgrade_air: 1}, function(RESULT){
		
		RESULT = JSON.parse(RESULT);

		if(RESULT['STATUS'] == ResponseStatus.OK)
		{
			playAudio("buy");
			updateFarmingView();
			loadProfileInfo();
		}
		else{
			new GameDialog(DialogType.DIALOG_TYPE_ERROR, "ERROR", "Failed to upgrade air level! Server response: <br><br>" + RESULT['DIALOG_MSG']);
		}
	});
}
function upgradeSoilLevel(){
	$.post("farming/FarmingController.php", {upgrade_soil: 1}, function(RESULT){
		
		RESULT = JSON.parse(RESULT);
		
		if(RESULT['STATUS'] == ResponseStatus.OK)
		{
			playAudio("buy");
			updateFarmingView();
			loadProfileInfo();
		}
		else{
			new GameDialog(DialogType.DIALOG_TYPE_ERROR, "ERROR", "Failed to upgrade soil level! Server response: <br><br>" + RESULT['DIALOG_MSG']);
		}
	});
}

function initUpgrades(){

	$("#light_upgrade_button").click(function(){
			new GameDialog(DialogType.DIALOG_TYPE_CONFIRM, "Upgrade Light", "Are you sure you want to upgrade your light level?", null,  upgradeLightLevel);
		}
	);		

	$("#farm_upgrade_button").click(function(){
			new GameDialog(DialogType.DIALOG_TYPE_CONFIRM, "Upgrade Farm", "Are you sure you want to upgrade your farm level?", null,  upgradeFarmLevel);
		}
	);
	$("#air_upgrade_button").click(function(){
			new GameDialog(DialogType.DIALOG_TYPE_CONFIRM, "Upgrade Air", "Are you sure you want to upgrade your air level?", null,  upgradeAirLevel);
		}
	);

	$("#soil_upgrade_button").click(function(){
			new GameDialog(DialogType.DIALOG_TYPE_CONFIRM, "Upgrade Soil", "Are you sure you want to upgrade your soil level?", null,  upgradeSoilLevel);
		}
	);
}
$(document).ready(function(){

	
	$("#farming-view").load("layout/farm_view/farm_view.html");
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




