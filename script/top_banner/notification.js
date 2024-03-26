
var notificationPanel = "#notification_panel_items";



/*				NOTIFICATION VAR				*/
var NOT_OFFSET = 0;

var NOT_UNMARKED = 0;



$(document).ready(main());

function main()
{
	getNotifications(NOT_OFFSET);
	loadScrollCheck();

	initKeylistener();
}
function initKeylistener()
{
	$(window).keydown(function(e){
		if(e.keyCode == 27)
		{
			$("#notification_panel").slideUp(100);
		}
	});
}
function getNotifications(offset)
{
	$.post("utils/notification/notification_controller.php", {get_notifications: 1, not_offset: offset}, function(data){
		console.log("Notifications loaded!");

		var list = JSON.parse(data);

		for(var i = 0; i < list.length; i++)
		{
			var item = list[i];
			if(item['status'] == 0)
			{
				NOT_UNMARKED++;
			}
			insertToNotificationBoard(item);
		}

		if(list.length == 0)
		{
			$(notificationPanel).append("<div class='notification-item'>If there is something up, i'll let you know right here!</div>");
		}

		setNotificationAmountView(NOT_UNMARKED);
		initNotificationMarker();
		initAllButtonListeners();
	});
}
function setNotificationAmountView(N)
{
	if(N > 0)
	{
		$("#notifications_amount").html(N);
		$("#notification_img").addClass("green-filter");
	}
}

function initNotificationMarker()
{
	$(".notification-unmarked").click(function(){
		var obj = $(this); 

		$(obj).removeClass("notification-unmarked");
		$(obj).addClass("notification-marked");
	});
}

function insertToNotificationBoard(data)
{
	var db_id 			= data['id'];
	var db_status 		= data['status'];
	var db_type 		= data['type'];

	var db_img 			= data['img'];

	var db_time 		= data['time'];

	var db_user_a 		= data['username_a'];
	var db_user_b 		= data['username_b'];

	var db_user_id_a 	= data['user_id_a'];
	var db_user_id_b	= data['user_id_b'];

	/*		HTML VAR	*/
	var html_status_div;
	
	if(db_status == 0)
	{
		html_status_div = "<div class='notification-item notification-unmarked' onclick='markNotification("+ db_id +")'>";
	}
	else
	{
		html_status_div = "<div class='notification-item notification-marked'>";
	}

	var html_time_div = "<div class='notification_time'>" + secondsToTime(db_time) + "</div>";

	var html_img_div = "<div class='notification_img'> <img class='notification-image' src='" + db_img + "'> </div>";

	var html_not_text = "";
	
	var html_id_input = "<input type='hidden' value='" + db_id + "' name='not-id'>";

	if(db_type == 0)
	{
		
		var username_html 	= "Friend request from <a class='notification-link username' href='"+ db_user_a + "'>" + db_user_a + "</a>";

		var buttons_layout 	= "<button class='button_accept friend_request_action_button' value='"+ db_user_id_a +"'>Accept</button>";

		html_not_text = "<div class='notification_text notification_buttons '>" + username_html + buttons_layout + "</div>";
	}
	else if(db_type == 1)
	{
		var username_html = "<a class='notification-link username' href='"+ db_user_b + "'>" + db_user_b + "</a>";
		html_not_text = username_html + " accepted your friend request!";
	}
	else if(db_type == 2)
	{
		var username_html = "<a class='notification-link username' href='"+ db_user_b + "'>" + db_user_b + "</a>";
		html_not_text = username_html + " wrote on your wall!";
	}
	else if(db_type == 5)
	{
		var username_html = "<a class='notification-link username' href='"+ db_user_b + "'>" + db_user_b + "(You)</a>";
		html_not_text = username_html + " commented on a post on your own wall!";
	}

	var FINAL_HTML = html_status_div + html_img_div + html_not_text + html_time_div+  "</div>";

	$(notificationPanel).append(FINAL_HTML);
}

function markNotification(NOT_ID)
{
	console.log("Marking notification...");
		
	$.post("notification/notification.php", {UN: NOT_ID}, function(result)
	{
		console.log("Mark not-Result: " + result);
	});
}




function loadScrollCheck()
{
	/* TO-DO:
	
		Lag funksjon for innlastning av nye elementer fra DB.

	*/
}