$(document).ready(main);

const CLASS_MONEY_VALID 	= "money-valid";
const CLASS_MONEY_INVALID 	= "money-invalid";

const DIV_UPGRADE_BUTTON 	= "#upgrade-button";

const DIV_UPGRADE_PRICE 	= "#upgrade-price";
const DIV_STORAGE_LEVEL 	= "#storage-level";

const DIV_STORAGE_USED 		= "#storage-space-used";
const DIV_STORAGE_TOTAL 	= "#storage-space-total";


function main()
{
	setTimeout(function() {
		getStorageInfo();	
	}, 150);

	$(DIV_UPGRADE_BUTTON).click(function(){
		
		$.post("storage/StorageController.php", {upgrade_backpack: 1}, function(data)
		{
			var RESULT = JSON.parse(data);

			if(RESULT['ERR'])
			{
				console.warn("Failed to load inventory info!");
			}
			else {
				getStorageInfo();
				updateUserInfo();
				playAudio('buy');
			}

		});
	});
	
}

function getStorageInfo()
{
	$.post("storage/StorageController.php", {get_backpack_info: 1}, function(data)
	{
		var RESULT = JSON.parse(data);

		if(RESULT['ERR'])
		{
			console.warn("Failed to load inventory info!");
		}
		else
		{
			var UPGRADE_PRICE 		= parseInt(RESULT['upgrade_price']);

			CURRENT_PLAYER_MONEY 	= parseInt(ARRAY_USER_INFO['money']);

			console.log("Money: " + CURRENT_PLAYER_MONEY + "\n Price: " + UPGRADE_PRICE);

			var STORAGE_LEVEL = RESULT['storage_level'];
			var STORAGE_PRICE = RESULT['upgrade_price'];
			var STORAGE_SPACE_USED 	= RESULT['storage_used'];
			var STORAGE_SPACE_TOTAL = RESULT['storage_total'];


			$(DIV_STORAGE_LEVEL).html("<span>Storage level: "+ STORAGE_LEVEL +"</span>");
			$(DIV_UPGRADE_PRICE).html(STORAGE_PRICE.formatMoney(0, '.', ',') + " $");
			$(DIV_STORAGE_USED).html(STORAGE_SPACE_USED);
			$(DIV_STORAGE_TOTAL).html(STORAGE_SPACE_TOTAL);

			console.log("Money: " + CURRENT_PLAYER_MONEY);

			if(UPGRADE_PRICE <= CURRENT_PLAYER_MONEY)
			{
				$(DIV_UPGRADE_BUTTON).css("background-color", "green");
			}
			else
			{
				$(DIV_UPGRADE_BUTTON).css("background-color", "red");
			}
		}
	});
}



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