<?php
    session_start();

    require("utils/shop/excerpt.php");
    require("layout/nav_menu/nav_menu.php");
    require("connect/database.php");
    require("common/session/sessioninfo.php");
    
    $dbCon = Database::getConnection();
    
    $USER = new User(-1, -1);
    if($USER->isLoggedIn())
    {
        if($dbCon = Database::getConnection())
        {

        }
        else
        {

        }
    }    
    else
    {
        echo "<h1>Not logged in!</h1>";   
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


?>

<script>
  less = {
    env: "development",
    async: false,
    fileAsync: false,
    poll: 1000,
    functions: {},
    dumpLineNumbers: "comments",
    relativeUrls: false,
    rootpath: ":/a.com/"
  };
</script>
<html>
<head>

    <title>King of Mota - Shop</title>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/less.js/2.5.3/less.min.js" type="text/javascript"  ></script>

    <link href="style/shop/style.less" rel="stylesheet/less" type="text/css">

</head>
<body>
        <?php
            $btn_file = fopen("layout/top_banner/top_banner.php", "r") or die("Unable to open file!");
            echo fread($btn_file,filesize("layout/top_banner/top_banner.php"));
            fclose($btn_file);
        ?>

    <div id="content">

      <div class="menu-trigger"></div>

    <div id="shop" class="shop_view">
        <div id="shop_news">
            <div id="shop_news_offer">                
                <h1>Mota shop</h1>
                <div >
                    <img id="shop-news-image" src="http://kingofmota.com/img/mushroom/mushrm_01.png">
                </div>
                <br>
                    <div id="shop_news_desc">Tired of growing that same old kush, huh?<br>Check out our new <a href="shop.php?show_only_item_type=7">mushrooms!</a></div>
                <br>
            </div>
        </div>
        <div class="item_filter_view">
            <form class="form_show_only" action="shop.php" enctype="application/x-www-form-urlencoded" method="get">
                <div id='show_only_title'>Show:</div>
      <select name="show_only_item_type" id="show_only" onchange="this.form.submit()">
                    <?php

                    if(isset($_GET['show_only_item_type']))
                    {
                        $showing_item_type = $_GET['show_only_item_type'];
                    }
                    else
                    {
                        $showing_item_type = 0;
                    }

                    if($showing_item_type == -1)
                    {
                        echo
                        '<option name="type" value="-1">All</option>
                        <option name="type" value="0">Kush</option>
                        <option name="type" value="7">Mushrooms</option>';

                    }
                    else if($showing_item_type == 0)
                    {
                        echo
                        '<option name="type" value="0">Kush</option>
                        <option name="type" value="-1">All</option>
                        <option name="type" value="7">Mushrooms</option>';
                    }
                    else if($showing_item_type == 7)
                    {
                        echo
                        '<option name="type" value="7">Mushrooms</option>
                        <option name="type" value="0">Kush</option>
                        <option name="type" value="-1">All</option>';

                    }
                    else if($showing_item_type == 1)
                    {
                        echo
                        '<option name="type" value="1">Vehicles</option>
                        <option name="type" value="2">Weapons</option>
                        <option name="type" value="0">Kush</option>
                        <option name="type" value="-1">All</option>
                        <option name="type" value="7">Mushrooms</option>';
                    }
                    else
                    {
                        echo
                        '<option name="type" value="2">Weapons</option>
                        <option name="type" value="1">Vehicles</option>
                        <option name="type" value="0">Kush</option>
                        <option name="type" value="-1">All</option>
                        <option name="type" value="7">Mushrooms</option>';
                    }
                    ?>

                </select>
            </form>
        </div>
            <form id="search_form" method="get" action='shop.php'>
                <input name="searchVal" type="text" placeholder="Search">
                <input type="submit" value="Search">
            </form>
        <div id="shop_items">
            <?php

            if ($dbCon) 
            {


                $item_type = strip_tags($showing_item_type);

                if(! isset($_GET['searchVal']))
                {
                    $query1 = "SELECT * FROM items"; //  WHERE type='$item_type'"
                    if (isset($item_type) && ($item_type != -1)) 
                    {
                        $query1 = "SELECT * FROM items WHERE type='$item_type' AND item_active='1' ORDER BY min_level ASC "; //  WHERE type='$item_type'"
                    }else
                    {
                        $query1 = "SELECT * FROM items WHERE item_active='1' ORDER BY min_level ASC";
                    }
                }
                else{
                    $searchValue = $_GET['searchVal'];
                    $query1 = "SELECT * FROM items WHERE( name LIKE '%". $searchValue ."%' OR beskrivelse LIKE '%". $searchValue ."%') AND item_active='1'";
                }


                $all_items = mysqli_query($dbCon, $query1);

                if($item_type != -1)
                {
                    $getMaxPriceQuery = "SELECT MAX(item_power) AS power FROM items WHERE type='$item_type' ORDER BY min_level ASC";
                    $getMaxTimeQuery = "SELECT MAX(grow_time) AS time FROM items WHERE type='$item_type'  ORDER BY min_level ASC";
                }
                else
                {
                    $getMaxPriceQuery = "SELECT MAX(item_power) AS power FROM items";
                    $getMaxTimeQuery = "SELECT MAX(grow_time) AS time FROM items";
                }


                $doGetMaxPriceQuery = mysqli_query($dbCon, $getMaxPriceQuery);

                $MAX_POWER = mysqli_fetch_row($doGetMaxPriceQuery);


                $doGetMaxTimeQuery = mysqli_query($dbCon,$getMaxTimeQuery);

                $MAX_TIME = mysqli_fetch_row($doGetMaxTimeQuery);


                $max_grow_time = $MAX_TIME[0];
                $max_power = $MAX_POWER[0];

                $varI = 2;
                $shopItemIndex = 0;
                while ($row = mysqli_fetch_array($all_items))
                {

                    $item_id = $row['id'];

                    if($USER->getLevel() >= $row['min_level'])
                    {
                        $extaClass = "item-unlocked";
                        $img = $row['picture'];
                    }
                    else
                    {
                        $extaClass = "item-locked";
                        $img = "img/item/lock.png";
                    }

                    echo "<form class='form_item' action='item.php' method='get' enctype='application/x-www-form-urlencoded'>";
                    echo "<div style='' id='item_index_id_". $shopItemIndex ."' class='shop_item $extaClass' onClick='javascript:document.forms[" . $varI . "].submit();'>";
                    echo "<div class='item_title'>" . $row['name'] . " </div>";
                    echo "<div class='item_img'>
                                <img class='item_image' src='" . $img ."'>
                    </div>";
                    $shopItemIndex++;


                    echo '<div class="item_info_view">
                    <div class="item_info_bars">
                            <div class="item_info_bar_list">';
                                
                                $ITEM_TYPE = $row['type'];
                                $ITEM_SUB_TYPE = $row['sub_type'];

                                if($ITEM_SUB_TYPE == 0)
                                {
                                    echo '<div class="item_info_text">Strength: ';
                                }
                                else if($ITEM_SUB_TYPE == 1)
                                {
                                    echo '<div class="item_info_text">Power: ';
                                }
                                else if($ITEM_SUB_TYPE == 2)
                                {
                                    echo '<div class="item_info_text">Firepower: ';
                                }
                                else if($ITEM_SUB_TYPE == 3)
                                {
                                    echo '<div class="item_info_text">Watt: ';
                                }
                                
                                else if($ITEM_SUB_TYPE == 4)
                                {
                                	echo '<div class="item_info_text">Grow time: ';
                                }
                                else if($row[2] == 5)
                                {
                                    echo '<div class="item_info_text">Power: ';
                                }
                                else if($row[2] == 6)
                                {
                                    echo '<div class="item_info_text">Power: ';
                                }
                                else if($row[2] == 7)
                                {
                                    echo '<div class="item_info_text">Power: ';
                                }
                                else
                                {
                                    echo '<div class="item_info_text">Amount:';   
                                }
                                $ITEM_POWER = $row['item_power'];
                                echo $ITEM_POWER . "</div>";

                                echo'<div class="item_info_bar_div">
                                    <div class="info_bar_filled" style="width: '. (($ITEM_POWER/$max_power)*98) .'%; background-color: rgba('. (255 - round(255 * ($row[7]/$max_power))) .','. (round(255 * ($ITEM_POWER/$max_power))) .',100,1);"></div>
                                </div>
                                ';
                                if($row[2] == 0 || $row[2] == 4 || $row[2] == 7)
                                {
                                    echo '<div class="item_info_text">Grow time: '. secondsToTime($row[8]) .'</div>
                                    <div class="item_info_bar_div">
                                        <div class="info_bar_filled" style="width: ' . ($row[8]/$max_grow_time)*98 . '%; background-color: rgba('. (round(255*(($row[8]/$max_grow_time)))) .','. (200 - (round(255*(($row[8]/$max_grow_time)))))  .',1,1.0);"></div>
                                    </div>
                                </div> </div> </div>';
                                }
                                else{
                                    echo '</div> </div> </div>';
                                }

                                $excrpt = Excerpt::getExcerpt($row['beskrivelse'], 85);


                    echo "<div id='item_desc' class='item_desc'>" . $excrpt . "</div> ";
                    $number = 1234.56;
                    $english_format_number = number_format($number);
                    
                    if($row['discount'] > 0){

                        echo "<div class='item_price_label_discount item_price_label' id='item_price_discount'> <span>" . number_format($row['pris'], 0, '.', ',') . "$</div>";
                        $newPrice = $row['pris'] - (($row['pris']/100) * ($row['discount']));
                        echo "<div id='item_discount'><span>New price: <span class='item_price_label'>" . ($newPrice) . "</span></span>$</span></div>";
                        if($row['type'] == 0)
                        {
                        	echo "<div id='item_discount_percent_0' class='item_discount_class'>" . $row['discount'] . "% off! </div>";
                        }
                        else if($row['type'] == 1)
                        {
                        	echo "<div id='item_discount_percent_1' class='item_discount_class'> " . $row['discount'] . "% off! </div>";
                        }
                        else if($row['type'] == 2)
                        {
                        	echo "<div id='item_discount_percent_2' class='item_discount_class'>" . $row['discount'] . "% off! </div>";
                        }
                        else if($row['type'] == 3)
                        {
                        	echo "<div id='item_discount_percent_3' class='item_discount_class'>" . $row['discount'] . "% off! </div>";
                        }
                        else if($row['type'] == 4)
                        {
                        	echo "<div id='item_discount_percent_4' class='item_discount_class'>" . $row['discount'] . "% off! </div>";
                        }

                    }
                    else{
                    	if($row['pris'] == 0){
                    		echo "<div class='item_price_label' id='item_price'> <span>FREE</span></div>";	
                    	}
                    	else{
                    		echo "<div class='item_price_label' id='item_price'> <span>" . number_format($row['pris'], 0, '.', ',') . "$</span></div>";	
                    	}
                        
                    }

                    if($USER->getLevel() >= $row['min_level'])
                    {
                        echo "<div class='item_min_level' style='background-color: green; color: black;'>Lvl: ". $row['min_level'] ."</div>";
                    }
                    else
                    {
                        echo "<div class='item_min_level' style='background-color: orange; color: black;'>Lvl: ". $row['min_level'] ."</div>";
                    }
                    echo "<input id='item_id_hidden' name='item_id' value='" . $row['id'] . "'>";
                    echo "<input id='item_type_hidden' type='hidden' name='IT' value='" . $showing_item_type . "'>";
                        echo "</form>";

                    echo "</div>";
                    $varI++;
                }
                if($shopItemIndex == 0)
                {
                  echo "<div class='warning-not-found'> <span>No matching items found.</span> </div>";
                }
            }
            else
            {
                die("Failed to fetch item list!");
            }
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
