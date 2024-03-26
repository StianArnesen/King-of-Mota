




/*
		DIV-CONTROL
*/




const _DIV_STORAGE_INFO_IMG 			= "#storage-info-img";
const _DIV_STORAGE_INFO_NAME 			= "#storage-info-name";
const _DIV_STORAGE_INFO_LEVEL 			= "#storage-level";
const _DIV_STORAGE_INFO_SPACE_USED 		= "#storage-space-used";
const _DIV_STORAGE_INFO_SPACE_TOTAL 	= "#storage-space-total";

const _DIV_STORAGE_INFO_UPGRADE_PRICE 	= "#upgrade-price";
const _DIV_STORAGE_INFO_BUTTON_OPEN		= "#open-storage";


const _DIV_STORAGE_LIST = "#storage-list";






$(document).ready(storageInit);

function storageInit()
{
	loadStorageUnits();

	$("#overlay-view").click(function(){
		closeOverlay();
	});
}


var selectedItems = new Array();

function closeOverlay(){
	$("#overlay-view").fadeOut(100);
	$("#overlay-data").fadeOut(100);
	$("#body").removeClass('effect_blurred-div');

	selectedItems = new Array();
	$("#inv-selected-amount").html(selectedItems.length + " items selected");
	$("#inv-action-view").fadeOut(300);

}



function moveItemsToBackpack()
{
	console.log("Moving items to backpack");
	$.post("storage/StorageUnitController.php", {move_inv_items_id_to_backpack: selectedItems}, function(result){
		console.log("RESULT   -> " + result);
		if(result == "success")
		{
			closeOverlay();
			updateUserInfoArray();
		}
	});
}

function loadSelector() {
	$('.inventory_item').click(function()
	{
		if($(this).hasClass("inv-item-selected")){
			$(this).removeClass("inv-item-selected");

			var inv_val = $(this).find("input[name='inv-id-value']").val();
			for(var i = 0; i < selectedItems.length; i++){
				if(selectedItems[i] == inv_val){
					selectedItems.splice(i, 1);
					break;
				}
			}
		}
		else {
			$(this).addClass("inv-item-selected");

			var inv_val1 = $(this).find("input[name='inv-id-value']").val();

			selectedItems.push(inv_val1);
		}

		for(var i = 0; i < selectedItems.length; i++){
			//console.log(selectedItems + "\n");
		}
		if(selectedItems.length > 0){
			$("#inv-action-view").fadeIn(100);
		}
		else {
			$("#inv-action-view").fadeOut(100);
		}

		$("#inv-selected-amount").html(selectedItems.length + " items selected");
	});
}

function showStorageUnitInfo(storage_id, object)
{
	$.post("storage/StorageUnitController.php", {GET_STORAGE_UNIT_INFO: storage_id}, function(data)
	{

		/*       Update storage-info-view       */

		var storage = JSON.parse(data);

		var STORAGE_TITLE           = storage['STORAGE_TITLE'];
		var STORAGE_IMAGE           = storage['STORAGE_IMAGE'];
		var STORAGE_LEVEL           = storage['STORAGE_LEVEL'];
		var STORAGE_SPACE_TOTAL     = storage['STORAGE_SPACE_TOTAL'];
		var STORAGE_SPACE_USED      = storage['STORAGE_SPACE_USED'];
		var STORAGE_UPGRADE_PRICE   = storage['STORAGE_UPGRADE_PRICE'];


		$(_DIV_STORAGE_INFO_IMG).attr('src', STORAGE_IMAGE);
		$(_DIV_STORAGE_INFO_BUTTON_OPEN).attr('onclick', "showStorageUnit("+ storage_id +")");
		$(_DIV_STORAGE_INFO_NAME).html(STORAGE_TITLE);
		$(_DIV_STORAGE_INFO_LEVEL).html(STORAGE_LEVEL);
		$(_DIV_STORAGE_INFO_SPACE_USED).html(STORAGE_SPACE_USED);
		$(_DIV_STORAGE_INFO_SPACE_TOTAL).html(STORAGE_SPACE_TOTAL);
		$(_DIV_STORAGE_INFO_UPGRADE_PRICE).html(STORAGE_UPGRADE_PRICE);






		/*       ADD - AND REMOVE STYLE CLASSES FROM THE ELEMENTS       */


		$('.storage-unit').removeClass('storage-active');

		if(! $(object).hasClass('storage-active'))
		{
			playAudio('click');
			$(object).addClass('storage-active');
		}
	});
}

function showStorageUnit(storage_id) {
	console.log("Loading storage unit ID; " + storage_id);

	$("#overlay-data").load("storage/StorageUnitOverlay.php?storage_id=" + storage_id);
	$("#overlay-view").fadeIn(100);
	$("#overlay-data").fadeIn(100);
	$("#body").addClass('effect_blurred-div');
	setTimeout(function(){
		loadSelector();
	},400);

	playAudio('open');

}

$(".inventory_item").click(function() {
	if(canPlaySound && !muted)
	{
		var audio = new Audio('sound/click.mp3');
		audio.play();
		canPlaySound = false;

		setTimeout(function(){
			canPlaySound = true;
		}, 40);
	}
	if($(this).hasClass("inv-item-selected")){
		$(this).removeClass("inv-item-selected");

		var inv_val = $(this).find("input[name='inv-id-value']").val();
		for(var i = 0; i < selectedItems.length; i++){
			if(selectedItems[i] == inv_val){
				selectedItems.splice(i, 1);
				break;
			}
		}

	}
	else {
		$(this).addClass("inv-item-selected");

		var inv_val = $(this).find("input[name='inv-id-value']").val();

		selectedItems.push(inv_val);
	}

	for(var i = 0; i < selectedItems.length; i++){
		//console.log(selectedItems + "\n");
	}
	if(selectedItems.length > 0){
		$("#inv-action-view").fadeIn(100);
	}
	else
	{
		$("#inv-action-view").fadeOut(100);
	}

	$("#inv-selected-amount").html(selectedItems.length + " items selected");


	var loaded = false;

	var queries = 0;

	var lastId = 0;

});
function trashItems(){
	console.log("Trashing items...");
	if(selectedItems.length > 0){

		var confirmDelete = confirm("Delete selected items?");
		if(confirmDelete){
			$.post("storage/StorageUnitController.php", {trash_items: selectedItems}, function(result){
				console.log("Item trash result: " + result);
			});

			$(".inv-item-selected").hide(200);
			selectedItems = [];
			$("#inv-action-view").fadeOut(100);
		}
	}
}
function showStorageList(){

	var SID = 2;

	$.post("storage/StorageUnitController.php", {get_storage_list: SID}, function(result){
		$("#overlay-data").html(result);
	});

	$("#overlay-view").fadeIn(100);
	$("#overlay-data").fadeIn(100);
}
function buyStorage(){
	$.post("storage/StorageUnitController.php", {BUY_STORAGE: 1}, function(result){
		console.log(result);
	});
}
function moveItemsToStorage(storage_id)
{

	console.log("Moving items to storage [" + storage_id + "]");

	$.post("storage/StorageUnitController.php", {
		move_inv_items_id: selectedItems,
		move_to_storage_id: storage_id
	}, function (result) {
		console.log("Result: " + result);
		$(".inv-item-selected").hide(200);
		$("#overlay-view").fadeOut(100);
		$("#overlay-data").fadeOut(100);

		var audio = new Audio('sound/click.mp3');
		audio.play();
		canPlaySound = false;

		selectedItems = [];
		$("#inv-action-view").fadeOut(100);

		closeOverlay();
		updateUserInfo();

	});
}







function loadStorageUnits()
{
	$.post("storage/StorageController.php", {GET_STORAGE_LIST: 1}, function(data)
	{
		var result = JSON.parse(data);

		var HTML = "";

		for(var i = 0; i < result.length; i++)
		{
			var UNIT = result[i];
			HTML += getStorageUnitLayout(UNIT);
		}

		$(_DIV_STORAGE_LIST).html(HTML);

		console.log(result);
	});
}







function getStorageUnitLayout(UNIT)
{
	var TITLE	= UNIT['storage_title'];
	var LEVEL	= UNIT['storage_level'];
	var IMAGE	= UNIT['storage_img'];
	var HEALTH	= UNIT['storage_health'];

	var SPACE_USED	= UNIT['space_used'];
	var SPACE_TOTAL	= UNIT['space_total'];


	var HTML = "";


	HTML += "<div class='storage-unit'>";
		HTML += "<div class='storage-unit-title'>" + TITLE + "</div>";
		HTML += "<div class='storage-unit-image'> <img src='"+ IMAGE +"'> </div>";
		HTML += "<div class='storage-info-container'>";
			HTML += "<div class='storage-unit-level'>Level: " + LEVEL + "</div>";
			HTML += "<div class='storage-unit-health'>Health: " + HEALTH + "</div>";
			HTML += "<div class='storage-unit-space'>" + SPACE_USED + "/"+ SPACE_TOTAL + "</div>";
		HTML += "</div>";
	HTML += "</div>";

	return HTML;
}