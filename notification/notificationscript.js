	
	$(document).ready(function()
	{

				  if (Notification.permission !== "granted")
				    Notification.requestPermission();
				

					function notifyMe() 
					{
					  if (!Notification) 
					  {
					    alert('Desktop notifications not available in your browser. Try Chromium.'); 
					    return;
					  }

					  	if (Notification.permission !== "granted")
					  	{
					    	Notification.requestPermission();
					  	}
					  	else 
					  	{
					    	var notification = new Notification('Notification title', {
					      	icon: 'http://kingofmota.ml/layout/top_banner/notification.png',
					      	body: "You have a new notification!",
					    });

					    notification.onclick = function () 
					    {
					      window.location.href = "http://kingofmota.ml";      
					    };
					  }
					}
				



		var notification_panel_showing = false;
		var notification_panel_moving = false;

		$("#notification_img").click(function(){
			if(! notification_panel_showing)
			{
				$("#notification_panel_items").load("notification/notification.php");
			}
			if(! notification_panel_moving)
			{
				$("#notification_panel").slideToggle(100);
				
				notification_panel_showing = (! notification_panel_showing);
				notification_panel_moving = true;
				
				setTimeout(function(){
					notification_panel_moving = false;
				}, 100);
			}
		});

		$("#notification_panel_items").load("notification/notification.php");

		$("#notifications_amount_hidden").load("notification/notification.php?type=2");
		$("#notifications_amount").load("notification/notification.php?type=1");
		

		if($("#notifications_amount_value").val() > 0)
		{
			document.getElementById("notification_img").setAttribute("style", "-webkit-filter: sepia(1);");
		}

		setInterval(function()
		{
			$("#notifications_amount_hidden").load("notification/notification.php?type=2");
			$("#notifications_amount").load("notification/notification.php?type=1");

			if($("#notifications_amount_value").val() > 0)
			{
				document.getElementById("notification_img").setAttribute("style", "-webkit-filter: drop-shadow(0px 0px 8px rgba(225, 55, 15, 1));");
			}
			else
			{
				document.getElementById("notification_img").setAttribute("style", "-webkit-filter: sepia(0);");
			}

		}, 1565);
	});

function crew_accept_user(UID)
{
	$.post("crew/crewFunction.php", {USER_JOIN_CREW: UID}, function(RESULT)
	{
		console.log("Adding user to crew... RESPONSE: " + RESULT);
	});
}


function markNotification(N, L){
	console.log("Marking notification where ID = " + N);
	$.post("notification/notification.php", {UN: N}, function(RESULT)
	{
		location.href=L;
		console.log("Notification marked as read! Result: " + RESULT);

	});
}