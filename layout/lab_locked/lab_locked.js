
//Server response definers

const BUY_SUCCESS = "TRUE";

$(document).ready(lockedMain);

var UNLOCK_LEVEL 	= 22;
var UNLOCK_PRICE	= 5000000;

function lockedMain()
{
    updateLabUnlockRequireMents();
    
	setTimeout(function() {

		var valid = true;

		if(CURRENT_PLAYER_LEVEL < UNLOCK_LEVEL)
		{
			valid = false;

			$("#btn-lab-unlock").css("background-color", "grey");
			$("#goal-level").css("color", "red");
		}
		else
		{
			$("#goal-level").css("color", "lightgreen");	
		}
		if(CURRENT_PLAYER_MONEY < UNLOCK_PRICE)
		{
			valid = false;
			
			$("#btn-lab-unlock").css("background-color", "grey");
			$("#goal-money").css("color", "red");
		}
		else
		{
			$("#goal-money").css("color", "lightgreen");
		}

		if(valid)
		{
			$("#btn-lab-unlock").click(function(){
				buyLabUnlock();
			});
		}

	}, 210);
}

function updateLabUnlockRequireMents(){
    $.post("lab/PublicLab.php", {get_unlock_req: 1}, function(data){
        var result = JSON.parse(data);
        console.log(result);

        UNLOCK_LEVEL = result['min_level'];
        UNLOCK_PRICE = result['min_money'];
        $("#goal-money").html("Price: " + UNLOCK_PRICE.formatMoney(0, '.', ',') + " $");
        $("#goal-level").html("Level: " + UNLOCK_LEVEL);
        
        return result;
    });
}

function buyLabUnlock()
{
	$.post("lab/PublicLab.php", {buy_lab_unlock: 1}, function(data){
		var result = JSON.parse(data);

		if(result['RESULT'] == BUY_SUCCESS){
            location.reload();
		}
		else{
			
		}
		
		console.log(result);
	});
}