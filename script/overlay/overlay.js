
$(document).ready(function(){


});


function showOverlay(type, data)
{
	if(type=="confirm")
	{
		$("#overlay").append("<div id='confirm_dialog'><h1>Confirm this please!</h1> </div>");
		console.log("Dialog!");
	}
}