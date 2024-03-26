$('.document').ready(main);

    let slideComplete = true;

    function main(){
        console.log("Button script running!");

        $('#btn_home').mouseover(function()
        {
        	if(slideComplete){
	        	slideComplete = false;
	            $("#home_submenu_buttons").slideDown(300, function()
	            {
	            	slideComplete = true;
	            });	
        	}
        });
        $('#btn_home').mouseleave(function()
        {
            $("#home_submenu_buttons").slideUp(300);
        });
    }
    function toggleSlide(element){
        $("#" + element).slideToggle(300);
    }