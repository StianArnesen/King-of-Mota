<?php
    session_start();

    require("utils/shop/excerpt.php");
    require("common/page.php");
    require("layout/nav_menu/nav_menu.php");

    if(isset($_SESSION['game_username']) && ($_SESSION['game_username'] != ""))
    {
        
    }
    else
    {
        header("Location: index.php");
    }

    if(isset($_SESSION['game_username']))
    {
        $dbCon = mysqli_connect("localhost", "Stian Arnesen", "dynamicgaming", "motagamedata");
        mysqli_set_charset($dbCon,"utf8");
        if($dbCon)
        {
            $session_username = $_SESSION['game_username'];

            $sqlCommands = "SELECT id, username, password, money, level, profile_picture, current_exp, next_level_exp FROM users WHERE username='$session_username'";

            $query = mysqli_query($dbCon, $sqlCommands);
            $data_row = mysqli_fetch_row($query);

            $data_user_id = $data_row[0];
            $data_username = $data_row[1];
            $data_password = $data_row[2];
            $data_money = $data_row[3];
            $data_level = $data_row[4];
			$data_profile_picture = $data_row[5];
            $data_current_exp = $data_row[6];
            $data_next_level_exp = $data_row[7];


            $currentTime = time();

            $setLastActive_query = "UPDATE users SET last_active='$currentTime' WHERE id='$data_user_id'";
            $doLastActiveQuery = mysqli_query($dbCon,$setLastActive_query);
        }
        else
        {
            $LOGIN_ERR = 2;
        }
    }
    else
    {
        header("Location: index.php");
    }
	function secondsToTime($seconds) 
	{
		$t = round($seconds);
		$t_new = sprintf('%02d:%02d:%02d', ($t/3600),($t/60%60), $t%60);

		$str_time = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $t_new);

		sscanf($t_new, "%d:%d:%d", $hours, $minutes, $seconds);

		$h = ($hours > 0)? $hours . "h ": "";
		$m = ($minutes > 0)? $minutes . "m ": "";
		$s = ($seconds > 0)? $seconds . "s ": "";

		$time_seconds = $h . $m . $s;

	  return $time_seconds;
	}


$navBar = new navigationMenu();
$pageUtils = new PageClass();


?>


<html>
    <head>
        <link href="style/shop/style.less" rel="stylesheet/less">
        <?php echo $pageUtils->getHeaderInfo();?>

        <title>King of Mota - Shop</title>

        <script src="script/shop/shop.js" type="text/javascript"></script>

    </head>
<body>
        <?php

        $btn_file = fopen("layout/top_banner/top_banner.php", "r") or die("Unable to open file!");
        echo fread($btn_file,filesize("layout/top_banner/top_banner.php"));
        fclose($btn_file);

        if(isset($_GET['category']))
        {
            $showing_item_type = $_GET['category'];
        }
        else if(isset($_GET['show_only_item_type']))
        {
            $showing_item_type = $_GET['show_only_item_type'];
        }
        else
        {
            $showing_item_type = null;
        }



        ?>

    <div id="content">

      <div class="menu-trigger"></div>

    <div id="shop" class="shop_view">
        <div id="shop_news">
            <div id="shop_news_offer">
                <h1>Mota shop</h1>
                <div >
                    <img id="shop-news-image" src="/img/shop/Cheech_Chong.png">
                </div>
                <br>
                    <div id="shop_news_desc">Tired of growing that same old kush, huh?<br>Check out our new <a href="#" onclick="showCategoryView(7)">mushrooms!</a></div>
                <br>
            </div>
            
        </div>
            <div class="shop-navigation-bar">
                <div class="shop-navigation-item-view">
                    <div class="shop-navigation-item" id="nav-btn-kush" href="shop.php?category=0">
                      <input type="hidden" class="nav-category" name="nav-category" value="0">
                      <div class="shop-navigation-item-title">
                          Kush
                      </div>
                    </div>
                    <div class="shop-navigation-item" id="nav-btn-mushrooms" href="shop.php?category=7">
                        <input type="hidden" class="nav-category" name="nav-category" value="7">
                        <div class="shop-navigation-item-title">
                            Mushrooms
                        </div>
                    </div>
                    <div class="shop-navigation-item" id="nav-btn-lab" href="shop.php?category=10">
                        <input type="hidden" class="nav-category" name="nav-category" value="10">
                        <div class="shop-navigation-item-title">
                            Lab Ingredients
                        </div>
                    </div>
                    <div class="shop-navigation-item" id="nav-btn-all" href="shop.php?category=-1">
                        <input type="hidden" class="nav-category" name="nav-category" value="-1">
                        <div class="shop-navigation-item-title">
                            All
                        </div>
                    </div>
                </div>

                <form id="search_form" method="get" action='shop.php'>
                    <input name="searchVal" type="text" placeholder="Search">
                </form>
            </div>

        <div id="shop_items">
        <?php

        include_once("shop/PublicShop.php");

        if(isset($_GET['searchVal']))
        {
            $SEARCH_VAL = strip_tags($_GET['searchVal']);
        }
        else
        {
            $SEARCH_VAL = false;
        }

        $PUBLIC_SHOP = new PublicShop();
        $PUBLIC_SHOP->getShopList($showing_item_type, $SEARCH_VAL, true);

        ?>

        </div>

        </div>


		
      </div>
</body>
</html>

<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>

<script type="text/javascript">

$(".document").ready(main);

function main(){
	 $("#profile_info_money").load("common/userinfo.php?get_info=0");
     $("#profile_info_div").fadeIn(200);
}
</script>

<script type="text/javascript" src="script/common/priceFix.js">


</script>

<script>

$(document).ready(function(){

    $("#shop").show();
});
</script>

</script>
