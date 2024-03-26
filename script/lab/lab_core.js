var CURRENT_PRODUCT_ID;
var CURRENT_SPACE_J_ELEMENT; // Jquery element for lab-space-item
var CURRENT_SPACE_ID;


function startItemProduction(SID, PID)
{
	$.post("lab/PublicLab.php", {set_lab_space: SID, product_id: PID}, function(data)
	{
		var result = JSON.parse(data);

		if(! result['ERR'] && ! result['WARN_DIALOG'])
		{
			var HTML = getLabSpaceItemLayout(result, true);
			$(CURRENT_SPACE_J_ELEMENT).html(HTML);

			if(! $(CURRENT_SPACE_J_ELEMENT).hasClass("item-active"))
			{
				$(CURRENT_SPACE_J_ELEMENT).addClass("item-active");
			}

			playAudio("grow");

			closeOverlay();
		}
	});
}
function collectProductFromSpace(SID, J_ELEMENT)
{
	$.post("lab/PublicLab.php", {collect_lab_space: SID}, function(data)
	{
		var result = JSON.parse(data);
		
		if(! result['ERR'] && ! result['WARN_DIALOG'])
		{
			var HTML = getLabSpaceItemLayout(result, true);
			if(! J_ELEMENT)
			{
				if($(CURRENT_SPACE_J_ELEMENT).hasClass('item-complete'))
				{
					$(CURRENT_SPACE_J_ELEMENT).removeClass('item-complete');
				}
				$(CURRENT_SPACE_J_ELEMENT).html(HTML);
			}
			else
			{
				if($(J_ELEMENT).hasClass('item-complete'))
				{
					$(J_ELEMENT).removeClass('item-complete');
				}
				$(J_ELEMENT).html(HTML);
			}

			var sound = new Audio("sound/garden/harvest.wav");
			sound.play();
			
			closeOverlay();
		}
	});
}