
<style>
.top_banner_menu #home_submenu_buttons{
	width: 80%;
	
	margin: auto;
	
	padding-bottom: 10px
}
.top_banner_menu{
    z-index: 2;

}
#top_buttons{
    background-color: grey;

    font-family: "exo", sans-serif;

    width: 180px;

    position: fixed; top: 20%; left: 10px;

    border: 2px solid transparent;

    border-radius: 10px;


}
.top_banner_menu #btn_home{

    background-color: white;
    width: 100%;
    font-family: "exo", sans-serif;

    font-size: 25px;
    color: black;

    border-radius: 5px;
 }

.top_banner_menu #btn_home:hover{
    background-color: lightblue;

    font-family: "exo", sans-serif;
    color: white;
    font-size: 25px;

    border-radius: 5px;


}
.top_banner_menu #btn_home:active{
    background-color: lightblue;

    font-family: "exo", sans-serif;

    font-size: 25px;

    color: white;
    border-radius: 5px;
}
.top_banner_menu #btn_shop{

    width: 100%;

    background-color: white;

    font-family: "exo", sans-serif;

    font-size: 25px;

    color: black;
    border-radius: 5px;
}

.top_banner_menu #btn_shop:hover{
    background-color: lightblue;

    font-family: "exo", sans-serif;
    color: white;
    font-size: 25px;

    border-radius: 2px;
}
.top_banner_menu #btn_shop:active{
    background-color: lightblue;

    color: white;

    font-family: "exo", sans-serif;

    font-size: 25px;

    border-radius: 5px;
}


.top_banner_menu #btn_logout{

    background-color: white;
    width: 100%;
    font-family: "exo", sans-serif;

    font-size: 25px;
    color: black;

    border-radius: 5px;
}

.top_banner_menu #btn_logout:hover{
    background-color: lightblue;

    font-family: "exo", sans-serif;
    color: white;
    font-size: 25px;

    border-radius: 5px;


}
.top_banner_menu #btn_logout:active{
    background-color: lightblue;

    font-family: "exo", sans-serif;

    font-size: 25px;

    color: white;
    border-radius: 5px;
}




#title_location{
     color: lightgreen;
}
.top_banner_menu #btn_crew{

    width: 100%;

    background-color: white;

    font-family: "exo", sans-serif;

    font-size: 25px;

    color: black;
    border-radius: 5px;
}
.top_banner_menu #btn_crew:hover{
    background-color: lightblue;

    font-family: "exo", sans-serif;
    color: white;
    font-size: 25px;

    border-radius: 2px;
}
.top_banner_menu #btn_crew:active{
    background-color: lightblue;

    color: white;

    font-family: "exo", sans-serif;

    font-size: 25px;

    border-radius: 5px;
}
.top_banner_menu #btn_inventory{
    background-color: white;
    width: 100%;
    font-family: "exo", sans-serif;

    font-size: 25px;
    color: black;

    border-radius: 5px;
}
.top_banner_menu #btn_inventory:hover{
    background-color: lightblue;

    font-family: "exo", sans-serif;
    color: white;
    font-size: 25px;

    border-radius: 5px;


}
.top_banner_menu #btn_inventory:active{
    background-color: lightblue;

    font-family: "exo", sans-serif;

    font-size: 25px;

    color: white;
    border-radius: 5px;
}
.top_banner_menu #crew_submenu_buttons{
	width: 80%;
	
	margin: auto;
}
.top_banner_menu #shop_submenu_buttons{
	width: 80%;
	
	margin: auto;
	
	padding-bottom: 10px;
}
.top_banner_menu #home_submenu_buttons{
	width: 80%;
	
	margin: auto;
	
	padding-bottom: 10px;
}

</style>

<head>
	<link href="layout/top_buttons/style.css" type="text/css">
</head>
<div class="top_banner_menu">
	<div id="menu_home">
		<div id="top_buttons">
			
			<button id="btn_home" onclick="toggleSlide('home_submenu_buttons')">Home</button>
				<div id="home_submenu_buttons">
						<button id="btn_home" onclick="location.href = 'home.php'">profile</button>
					<br>
				</div>
				
			
			<button id="btn_shop" onclick="toggleSlide('shop_submenu_buttons')">Shop</button>
			<br>
			
			<div id="shop_submenu_buttons">
				<button id="btn_shop" onclick="location.href = 'shop.php?show_only_item_type=-1'">All</button>
                <br>
                <button id="btn_shop" onclick="location.href = 'shop.php?show_only_item_type=0'">Kush</button>
				<br>
				<button id="btn_shop" onclick="location.href = 'shop.php?show_only_item_type=1'">Vehicles</button>
				<br>
				<button id="btn_shop" onclick="location.href = 'shop.php?show_only_item_type=2'">Weapons</button>
				<br>
			</div>
			
			<button id="btn_crew" onclick="toggleSlide('crew_submenu_buttons')">Crew</button>
			<br>
			
			<div id="crew_submenu_buttons">
				<button id="btn_crew" onclick="location.href = 'crewlist.php'">Crew list</button>
				<br>
			</div>
			<button id="btn_inventory" onclick="location.href = 'inventory.php'">Inventory</button>
			<br>
			<button id="btn_logout" onclick="location.href = 'logout.php'">Logout</button>
		</div>
	</div>
</div>

<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>

<script type="application/javascript">
    


    $('.document').ready(main);

    var slideComplete = true;

    function main(){
        console.log("Button script running!");

    }
    function toggleSlide(element){
        $("#" + element).slideToggle(300);
    }




</script>
