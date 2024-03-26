var newStatus = function(GSPUDIN, GPUUIDIN){
	console.log("Sending!");
	
	$.post("profile/status/writestatus.php", {GSPUD: GSPUDIN, GPUUID: GPUUIDIN}, function(RESP)
	{
		console.log("Status posted! Response: " + RESP);
	});
	
}

console.log("Post status script loaded!");