/*
  Slidemenu
*/
$(document).ready(function(){
	var $body = document.body;
	$menu_trigger = $(".menu-trigger");

	$menu_trigger.click(function(){
    $body.className = ( $body.className == 'menu-active' )? '' : 'menu-active';

  });


});
