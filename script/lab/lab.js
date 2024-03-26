





var DIV_LAB_SPACE_LIST 				= "#lab-space-items-list";
var DIV_LAB_PRODUCT_OVERLAY_LIST 	= "#lab-product-list";

var DIV_OVERLAY 					= "#overlay-view";
var DIV_OVERLAY_DATA 				= "#overlay-data";

var DIV_OVERLAY_ITEM_INFO 			= "#item-info-overlay";



/*					KEYBOARD CONTROLLER CONFIG						*/

const KEY_ESC 	= 27;

const KEY_LEFT 	= 37;
const KEY_RIGHT = 39;
const KEY_UP 	= 38;
const KEY_DOWN 	= 40;


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




/*			INFO STORAGE CONTAINERS			*/

var PRODUCT_LIST = [15, [3]];


$(document).ready(initLab);


function initLab()
{
	loadSpaceList();
	initOverlayListener();

	initUpgradeListener();
	getUpgradeInfo();

	initSoundEventListener();
}
function initSoundEventListener()
{
	$(".product-item").click(function(){


	});
}

function initUpgradeListener()
{
	$("#upgrade-lab-level-button").click(function(){
		$.post("lab/publiclab.php", {upgrade_lab_level: 1}, function(data)
		{
			getUpgradeInfo();
			updateUserInfo();
			console.log(data);
		});
	});
	$("#upgrade-lab-quality-button").click(function(){
		$.post("lab/publiclab.php", {upgrade_lab_quality_level: 1}, function(data)
		{
			getUpgradeInfo();
			updateUserInfo();
			console.log(data);
		});
	});
}
function initOverlayListener()
{
	$("#overlay-view").click(function()
	{
        closeOverlay();
    });

	$(window).keydown(function(e){
		if(e.keyCode == KEY_ESC)
		{
			closeOverlay();
		}
	});
}
function getUpgradeInfo()
{
	$.post("lab/publiclab.php", {get_lab_info: 1}, function(data)
	{
		var info = JSON.parse(data);

		var lab_level 			= info['lab_level'];
		var lab_quality_level 	= info['lab_quality_level'];


		$("#lab-info-level").html(lab_level);
		$("#lab-info-quality-level").html(lab_quality_level);
	});
	$.post("lab/publiclab.php", {get_lab_upgrade_price_list: 1}, function(data)
	{
		var info = JSON.parse(data);

		var lab_level_price 	= parseInt(info['lab_price']);
		var quality_level_price = parseInt(info['quality_price']);
		$("#lab-info-quality-level-price").html(quality_level_price.formatMoney(0, '.', ',') + " $");
		$("#lab-info-level-price").html(lab_level_price.formatMoney(0, '.', ',') + " $");
	});


}
function loadSpaceList()
{
	$.post("lab/PublicLab.php", {get_space_list: 1}, function(data){
		var result = JSON.parse(data);
		console.log(result);

		var finalHTML = "";

		for(var i = 0; i < result.length; i++)
		{
			var item = result[i];
			finalHTML += (getLabSpaceItemLayout(item));
		}
		$(DIV_LAB_SPACE_LIST).html(finalHTML);

		addLabSpaceTimerUpdate();


		setTimeout(function(){
			$("#lab-space-items-list").slideDown(200);
		}, 5);

	});
}
function showSpaceSetup(space_id, J_ELEMENT)
{
	CURRENT_SPACE_ID 		= space_id;
	CURRENT_SPACE_J_ELEMENT = J_ELEMENT;

	playAudio('click_lab');

	$.post("lab/PublicLab.php", {get_setup_lab: space_id}, function(data){
		var result = JSON.parse(data);
		console.log(result);

		var finalHTML = "";

		for(var i = 0; i < result.length; i++)
		{
			var item = result[i];

			var item_id = item['id'];

			PRODUCT_LIST[item_id] = item;

			finalHTML += getLabProductItemLayout(item);
		}
		$(DIV_LAB_PRODUCT_OVERLAY_LIST).html(finalHTML);

		showOverlay();
		addItemEventListeners();
	});
}

function addItemEventListeners()
{
	$('.product-item').click(function()
	{
		if(! $(this).hasClass("selected"))
		{
			$('.selected').each(function(){
				$(this).removeClass("selected");
			});
			$(this).addClass("selected");

			playAudio('click_drug');
		}
		else
		{

		}

		var ID 			= $(this).find("input[name='product-id']").val();
		var J_ELEMENT 	= $(this);
		
		showItemInfoView(ID, J_ELEMENT);
	});


	var canPress = true;

	$("#btn-produce-item").click(function(){
		if(canPress)
		{
			startItemProduction(CURRENT_SPACE_ID, CURRENT_PRODUCT_ID);
			canPress = false;

			setTimeout(function() {
				canPress = true;
			}, 200);
		}

	});
}

function addLabSpaceTimerUpdate()
{
	setInterval(function()
	{
		$(".lab-item").each(function(index)
		{
			var time_left 		= $(this).find("input[name='space-time-left']").val();	// time left
			var time_total 		= $(this).find("input[name='space-time-total']").val();	// time total

			var time_bar 		= $(this).find(".lab-item-time-left-bar");				// Progressbar inner
			var barDiv			= $(this).find(".lab-item-time-left-div");				// Progressbar wrapper

			var complete_text 	= $(this).find(".lab-item-complete-text");				// Timer text / status text

			var SPACE_STATUS_EMPTY	= $(this).find("input[name='space-status']").val(); // Space empty: boolean

			var SPACE_ID		= $(this).find("input[name='space-id']").val(); // Space empty: boolean


			if(SPACE_STATUS_EMPTY == 0) 												//Check if space is empty
			{
				if(time_left > 0)
				{
					barDiv.slideDown(200);
					var WIDTH = (time_left/time_total)*100 + "%";

					$(this).find("input[name='space-time-left']").val(time_left-1); // Countdown

					complete_text.html("<span>"+ getTime(time_left) +"</span>"); 	// Update timer text

					time_bar.width(WIDTH); 											// Update timebar width
				}
				else
				{
					if(! $(this).hasClass('item-complete'))
					{
						 $(this).addClass('item-complete');
						 $(this).removeClass('item-active');
					}
					barDiv.slideUp(200);
					complete_text.html("<div class='lab-item-button-collect' onclick=(collectProductFromSpace("+ SPACE_ID +"))>Compete!</div>");

				}
			}
			else
			{
				if(! $(this).hasClass('item-complete'))
				{
						 $(this).addClass('item-empty');
						 $(this).removeClass('item-active');
						 $(this).removeClass('item-complete');

				}
					barDiv.slideUp(200);
					complete_text.html("<span>Empty</span>");
			}

		});
	}, 1000);
}




function getLabSpaceItemLayout(item, replace) // Returnerer HTML layout for lab space, evt. det aktive produktet.
{
	/*			ITEM[]: [id, link_id, img, title, time_left, time_total]			*/

	var SPACE_ID 			= item['id'];				//Space ID;
	console.log("Space ID: " + SPACE_ID);
	var TITLE 		= "Empty lab";				// Space title
	var IMAGE 		= "img/lab/icon/space_empty.png";	// Product image
	var TIME_LEFT 	= 0; 								// TIME LEFT
	var TIME_TOTAL 	= 0; 								// TIME TOTAL

	var CLASS_MAIN 	= "item-empty"; 					// CSS class wrap element.

	var EMPTY 		= item['EMPTY']; 					// Sjekk om returnert data inneholder aktive element - eller ingenting.

	var STYLE_TIME 	= "";

	if(! EMPTY)
	{
		TITLE 		= item['title'];
		IMAGE 		= item['img'];
		TIME_LEFT 	= item['time_left'];
		TIME_TOTAL 	= item['time_total'];

		if(TIME_LEFT > 0)
		{
			CLASS_MAIN 	= "item-active";
		}
		else
		{
			STYLE_TIME 	= "display: none;";
		}
	}
	else
	{
		STYLE_TIME 	= "display: none;";
	}


	var HTML_TIME = "";

	var TIME_TEXT = getTime(TIME_LEFT);

	var EMPTY_VAL = 0;

	if(EMPTY)
	{
		EMPTY_VAL = 1;
		TIME_TEXT = "Empty";
	}
	else
	{
		if(TIME_LEFT <= 0)
		{
			TIME_TEXT = "<span> Complete! </span>";
		}
	}

	HTML_TIME += "<div class='lab-item-complete-text'  > <span>" + TIME_TEXT + "</span> </div>";
		HTML_TIME += "<div class='lab-item-time-left-div' style='"+ STYLE_TIME +"'>";
		HTML_TIME += "<div class='lab-item-time-left-bar' style='width: 100%; "+ STYLE_TIME +"'></div>";
	HTML_TIME += "</div>";



	var HTML = "";

	if(! replace)
	{
		if(EMPTY)
		{
			HTML 	+= '<div class="lab-item '+ CLASS_MAIN +'" onclick="showSpaceSetup('+ SPACE_ID +', $(this))">';
		}
		else
		{
			HTML 	+= '<div class="lab-item '+ CLASS_MAIN +'" onclick="collectProductFromSpace('+ SPACE_ID +', $(this))">';
		}

	}

	HTML 	+= '<div class="lab-item-title">'+ TITLE +'</div>'
	HTML 	+= '<div class="lab-item-img"><img src="'+ IMAGE +'"></div>';
	HTML 	+= HTML_TIME;
	HTML 	+= '<input type="hidden" name="space-time-left" value="'+ TIME_LEFT +'">';
	HTML 	+= '<input type="hidden" name="space-time-total" value="'+ TIME_TOTAL +'">';
	HTML 	+= '<input type="hidden" name="space-status" value="'+ EMPTY_VAL +'">';
	HTML 	+= '<input type="hidden" name="space-id" value="'+ SPACE_ID +'">';

	if(! replace)
	{
		HTML += '</div>';
	}


	return HTML;
}
function getLabProductItemLayout(product_item)
{
	/*	[id, name, img, power, desc, type]	*/

	var id 		= product_item['id'];
	var name 	= product_item['name'];
	var img 	= product_item['img'];
	var power 	= product_item['power'];
	var desc 	= product_item['desc'];
	var type 	= product_item['type'];

	var valid 	= product_item['valid'];


	var xtra_class = "invalid";

	var valid_icon = "img/icon/invalid.png";

	if(valid)
	{
		xtra_class = "valid";
		valid_icon = "img/icon/valid.png";
	}

	var HTML = "";


	HTML += '<div class="product-item '+ xtra_class +'">';

	HTML += '	<div class="product-item-title">'+ name +'</div>';
	HTML += '	<img class="product-item-valid-icon" src="'+ valid_icon +'">'
	HTML += '	<div class="product-item-img"> <img src="'+ img +'"></div>';
	HTML += '	<input type="hidden" name="product-id" value="' + id + '">';

	HTML +=	'</div>';

	return HTML;

}

function getIngredientItemLayout(name, id, img, cur_amount, max_amount)
{
	var html = "";

	cur_amount = parseInt(cur_amount);
	max_amount = parseInt(max_amount);

	if(! cur_amount)
	{
		cur_amount = 0;
	}

	var ICON = "img/icon/valid.png";

	var xtra_class = " goal-none";



	if(cur_amount >= max_amount)
	{
		xtra_class = " goal-full";
		ICON = "img/icon/valid.png";
	}
	else if(cur_amount >= max_amount / 2)
	{
		xtra_class = " goal-half";
		ICON = "img/icon/invalid.png";
	}
	else
	{
		ICON = "img/icon/invalid.png";
	}

	html += ' <div  class="product-ingredient-item '+ xtra_class + '">';
	html += '	<div class="product-ingredient-item-amount">'+ cur_amount +'/'+ max_amount +'</div>';
	html += '	<img class="product-ingredient-item-amount-icon" src="'+ ICON +'">';
	html += '	<div class="product-ingredient-item-img-container">';
	html += '		<img class="product-ingredient-item-img" src="' + img +'">';
	html += '	</div>';
	html += '	<div class="product-ingredient-item-title">'+ name +'</div>';
	html += ' </div>';
	
	return html;
}


/*
						STRUKTUR AV PRODUKT LISTE:

		[id, name, img, desc, power, time, ingredients[name, amount, current_amount] ]
										   --Player have needed ingredients.
	*/


function showIngredientItemInfo(id)
{

}
function loadProductInfoHTML(ITEM) // Array med info.
{
	var div_name 		= "#p_info_name";
	var div_image 		= "#p_info_image";
	var div_desc 		= "#p_info_description";
	var div_ingredients = "#p_info_ingredients_list";


	var DIV_INFO_BAR_POWER_TEXT			= "#p_info_power";
	var DIV_INFO_BAR_POWER				= "#p_info_power_bar";

	var DIV_INFO_BAR_TIME_TEXT			= "#p_info_time";
	var DIV_INFO_BAR_TIME				= "#p_info_time_bar";


	var name 	= ITEM['name'];
	var image 	= ITEM['img'];
	var desc 	= ITEM['desc'];

	var valid 	= ITEM['valid'];

	var power 	= parseInt(ITEM['power']);
	var time 	= parseInt(ITEM['time']);

	var power_max 	= parseInt(ITEM['max_power']);
	var time_max 	= parseInt(ITEM['max_time']);

	var ingredients = ITEM['ingredients'];

	if(! valid)
	{
		$("#btn-produce-item").hide(0);
	}
	else
	{
		$("#btn-produce-item").show(0);
	}

	var ingredients_html = "";

	for(var i = 0; i < ingredients.length - 1; i++)
	{
		ingredients_html += getIngredientItemLayout(ingredients[i]['name'],ingredients[i]['item_id'], ingredients[i]['img'], ingredients[i]['cur_amount'],ingredients[i]['amount']);
	}

	$(DIV_INFO_BAR_POWER_TEXT).html(power);
	$(DIV_INFO_BAR_TIME_TEXT).html(getTime(time));

	$(DIV_INFO_BAR_TIME).width( 	((time/time_max)*100) + "%");
	$(DIV_INFO_BAR_POWER).width( 	((power/power_max)*100) + "%");

	$(DIV_INFO_BAR_TIME).css("background-color",  "rgba(" + Math.round(time/time_max)*255  + ", " + Math.round(time/time_max)*255  + ", " + Math.round(time/time_max)*255  + ", 1)");

	$(div_desc).html(desc);
	$(div_ingredients).html(ingredients_html);
	$(div_name).html(name);
	$(div_image).attr("src", image);

}

function showItemInfoView(ITEM_ID)
{
	if(CURRENT_PRODUCT_ID != ITEM_ID)
	{
		loadProductInfoHTML(PRODUCT_LIST[ITEM_ID]); // Load viewport for product info.

		CURRENT_PRODUCT_ID = ITEM_ID;

		$(DIV_OVERLAY_ITEM_INFO).fadeIn(300);
	}
}
function closeItemInfoView()
{
	$(DIV_OVERLAY_ITEM_INFO).fadeOut(300);
}

function showOverlay()
{
	$("#overlay-view").fadeIn(100);
	$("#overlay-data").fadeIn(100);
}
function closeOverlay()
{
	$("#overlay-view").fadeOut(100);
	$("#overlay-data").fadeOut(100);
}





function showLabItemCollectResult(INFO)
{
	var NAME 	= INFO['product_name'];
	var IMAGE 	= INFO['product_img'];

	var QUALITY = INFO['product_quality'];

	var HTML = "";

	HTML += "<div class='collect-info-view'>";
	HTML += "	<div class='collect-info-title'>Drug complete!</div>";
	HTML += "	<div class='collect-info-content-title'>"+ NAME +"</div>";
	HTML += "	<img class='collect-info-content-image' src='"+ IMG +"'>";
	HTML += "	<div class='collect-info-content-quality'>"+ QUALITY +"%</div>";
	HTML += "</div>";

}






function getTime(sec){

	var d = Math.floor(sec / 3600*24);
	var h = Math.floor(sec / 3600);
	var m = Math.floor((sec - (h*3600)) / 60);
	var s = sec - (m*60) - (h*3600);

	var mm = (m > 0)? m + "m ": " ";
	var hh = (h >0)? h + "h ": " ";
	var dd = (d >24)? d + "d ": " ";

	var ss = (s >0)? s + "s ": " ";

	if(dd > 0)
	{
		return dd + hh;
	}
	else if(h > 0)
	{
		return hh + mm;	
	}
	return hh + mm + ss;
}