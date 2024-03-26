<?php


if(isset($_GET['get_product_list']))
{
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

    if(isset($_GET['searchVal']))
    {
        $SEARCH_VAL = strip_tags($_GET['searchVal']);
    }
    else
    {
        $SEARCH_VAL = false;
    }

    $PUBLIC_SHOP = new PublicShop();
    $PUBLIC_SHOP->getShopList($showing_item_type, $SEARCH_VAL, false);
}



class PublicShop
{

        public function __construct()
        {

        }
        private function secondsToTime($seconds)
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
        public function getShopList($TYPE, $SEARCH_VAL, $INTERNAL)
        {
            if($INTERNAL)
            {
                require_once("utils/shop/excerpt.php");
                include_once("connect/database.php");
                include_once("common/session/sessioninfo.php");
            }
            else
            {
                require_once("../utils/shop/excerpt.php");
                include_once("../connect/database.php");
                include_once("../common/session/sessioninfo.php");
            }

            $USER = new User(-1, -1);

            if(! $USER->isLoggedIn())
            {
                die("You need to login to see this.");
            }

            $data_level             = $USER->getLevel();

            $dbCon                  = Database::getConnection();
            mysqli_set_charset($dbCon, "UTF8");

            $searchVal              = $SEARCH_VAL;

            $showing_item_type      = $TYPE;
            $show_only_item_type    = $TYPE;
            $item_type = $TYPE;


            if ($SEARCH_VAL == "" || ! isset($SEARCH_VAL))
            {
                if (isset($item_type) && ($item_type != -1))
                {
                    $query1 = "SELECT * FROM items WHERE type='$item_type' AND item_active='1' ORDER BY min_level ASC "; //  WHERE type='$item_type'"
                }
                else
                {
                    $query1 = "SELECT * FROM items WHERE item_active='1' ORDER BY min_level ASC";
                }
            }
            else
            {
                $searchValue = $SEARCH_VAL;
                $query1 = "SELECT * FROM items WHERE( name LIKE '%" . $searchValue . "%' OR beskrivelse LIKE '%" . $searchValue . "%') AND item_active='1'";
            }


            $all_items = mysqli_query($dbCon, $query1);

            if ($item_type != -1 &&  ($SEARCH_VAL == "" || !isset($searchVal)))
            {
                $getMaxPriceQuery = "SELECT MAX(item_power) AS power FROM items WHERE type='$item_type' AND item_active=1";
                $getMaxTimeQuery = "SELECT MAX(grow_time) AS time FROM items WHERE type='$item_type' AND item_active=1";
            }
            else
            {
                $getMaxPriceQuery = "SELECT MAX(item_power) AS power FROM items WHERE item_active=1";
                $getMaxTimeQuery = "SELECT MAX(grow_time) AS time FROM items WHERE item_active=1";
            }

            if ($item_type != -1) {
                $MAX_EXP_QUERY = "SELECT MAX(item_info_a) FROM items WHERE type='$item_type' AND item_active=1";
            }
            else
            {
                $MAX_EXP_QUERY = "SELECT MAX(item_info_a) FROM items WHERE item_active=1";
            }


            $EXP_QUERY = mysqli_query($dbCon, $MAX_EXP_QUERY);

            $MAX_EXP_GAIN_RESULT = mysqli_fetch_array($EXP_QUERY);

            $doGetMaxPriceQuery = mysqli_query($dbCon, $getMaxPriceQuery);

            $MAX_POWER = mysqli_fetch_row($doGetMaxPriceQuery);

            $doGetMaxTimeQuery = mysqli_query($dbCon, $getMaxTimeQuery);

            $MAX_TIME = mysqli_fetch_row($doGetMaxTimeQuery);


            $max_grow_time = $MAX_TIME[0];
            $max_power = $MAX_POWER[0];
            $MAX_EXP_GAIN = $MAX_EXP_GAIN_RESULT[0];

            $EXP_LOG_MAX = 100 / (log($MAX_EXP_GAIN, 10));


            $varI = 2;
            $shopItemIndex = 0;
            while ($row = mysqli_fetch_array($all_items)) {

                $item_id = $row['id'];

                $G_PRICE    = $row['g_price'];

                if ($data_level >= $row['min_level']) {
                    $extaClass = "item-unlocked";
                    $img = $row['picture'];
                } else {
                    $extaClass = "item-locked";
                    $img = "img/item/lock.png";
                }

                echo "<form class='form_item' action='item.php' method='get' enctype='application/x-www-form-urlencoded'>";
                if($row['min_level'] <= $USER->getLevel())
                {
                    echo "<div style='' id='item_index_id_" . $shopItemIndex . "' class='shop_item $extaClass' onClick='javascript:document.forms[" . $varI . "].submit();'>";
                }
                else
                {
                    echo "<div style='' id='item_index_id_" . $shopItemIndex . "' class='shop_item $extaClass'>";
                }

                echo "<div class='item_title'>" . $row['name'] . " </div>";
                echo "<div class='item_img global_item-image'>
                                        <img class='item_image' src='" . $img . "'>
                            </div>";
                $shopItemIndex++;


                echo '<div class="item_info_view">
                            <div class="item_info_bars">
                                    <div class="item_info_bar_list">';

                $ITEM_TYPE = $row['type'];
                $ITEM_SUB_TYPE = $row['sub_type'];

                if ($ITEM_SUB_TYPE == 0) {
                    echo '<div class="item_info_text">Profit: ';
                } else if ($ITEM_SUB_TYPE == 1) {
                    echo '<div class="item_info_text">Power: ';
                } else if ($ITEM_SUB_TYPE == 2) {
                    echo '<div class="item_info_text">Firepower: ';
                } else if ($ITEM_SUB_TYPE == 3) {
                    echo '<div class="item_info_text">Watt: ';
                } else if ($ITEM_SUB_TYPE == 4) {
                    echo '<div class="item_info_text">Grow time: ';
                } else if ($row[2] == 5) {
                    echo '<div class="item_info_text">Power: ';
                } else if ($row[2] == 6) {
                    echo '<div class="item_info_text">Power: ';
                } else if ($row[2] == 7) {
                    echo '<div class="item_info_text">Power: ';
                } else {
                    echo '<div class="item_info_text">Amount:';
                }
                $ITEM_POWER = $row['item_power'];
                $ITEM_TIME  = $row['grow_time'];
                echo '<div class="item-info-number">' . $ITEM_POWER . "</div></div>";

                echo '<div class="item_info_bar_div">
                                            <div class="info_bar_filled" style="width: ' . (($ITEM_POWER / $max_power) * 98) . '%; background-color: rgba(' . (255 - round(255 * ($ITEM_POWER / $max_power))) . ',' . (round(255 * ($ITEM_POWER / $max_power))) . ',100,1);"></div>
                                        </div>
                                        ';


                $ITEM_EXP = $row['item_info_a'];

                if ($row[2] == 0 || $row[2] == 4 || $row[2] == 7) {
                    echo '<div class="item_info_text">Grow time: <div class="item-info-number">' . $this->secondsToTime($ITEM_TIME) . '</div></div>
                                            <div class="item_info_bar_div">
                                                <div class="info_bar_filled" style="width: ' . ($ITEM_TIME / $max_grow_time) * 98 . '%; background-color: rgba(' . (round(255 * (($ITEM_TIME / $max_grow_time)))) . ',' . (200 - (round(255 * (($row[8] / $max_grow_time))))) . ',1,1.0);"></div>
                                            </div>
                                        ';

                    echo '<div class="item_info_text">Exp gain: <div class="item-info-number">' . $ITEM_EXP . '</div></div>
                                            <div class="item_info_bar_div">
                                                <div class="info_bar_filled" style="width: ' . ($ITEM_EXP / $MAX_EXP_GAIN) * 98 . '%; background-color: rgba(' . (round(255 * (($ITEM_EXP / $MAX_EXP_GAIN)))) . ',' . (200 - (round(255 * (($ITEM_EXP / $MAX_EXP_GAIN))))) . ',1,1.0);"></div>
                                            </div>
                                        </div> </div> </div>';
                } else {
                    echo '</div> </div> </div>';
                }

                $excrpt = Excerpt::getExcerpt($row['beskrivelse'], 85);


                echo "<div id='item_desc' class='item_desc'>" . $excrpt . "</div> ";
                
                if ($row['discount'] > 0) {

                    echo "<div class='item_price_label_discount item_price_label' id='item_price_discount'> <span>" . number_format($row['pris'], 0, '.', ',') . "$</div>";
                    $newPrice = $row['pris'] - (($row['pris'] / 100) * ($row['discount']));
                    echo "<div id='item_discount'><span>New price: <span class='item_price_label'>" . ($newPrice) . "</span></span>$</span></div>";
                    if ($row['type'] == 0) {
                        echo "<div id='item_discount_percent_0' class='item_discount_class'>" . $row['discount'] . "% off! </div>";
                    } else if ($row['type'] == 1) {
                        echo "<div id='item_discount_percent_1' class='item_discount_class'> " . $row['discount'] . "% off! </div>";
                    } else if ($row['type'] == 2) {
                        echo "<div id='item_discount_percent_2' class='item_discount_class'>" . $row['discount'] . "% off! </div>";
                    } else if ($row['type'] == 3) {
                        echo "<div id='item_discount_percent_3' class='item_discount_class'>" . $row['discount'] . "% off! </div>";
                    } else if ($row['type'] == 4) {
                        echo "<div id='item_discount_percent_4' class='item_discount_class'>" . $row['discount'] . "% off! </div>";
                    }

                } else {
                    if ($row['pris'] == 0) {
                        echo "<div class='item_price_label item_price color-green'> <span>FREE</span></div>";
                    }
                    else if ($row['pris'] == -1) {
                        $COLOR_STYLE = "color-red";
                        if($USER->getCoins() >= $G_PRICE){
                            $COLOR_STYLE = "color-green";
                        }
                        echo "<div class='item_price_label item_price ". $COLOR_STYLE ."'> <span>". $G_PRICE ." <img src='/img/icon/g_coin.png' style='width: 30px;
                            height: auto;
                            padding: 0;
                            margin: 0;
                            float: left;
                            title='G-Coin'> </span></div>";
                    }
                    else
                    {

                        if($USER->getMoney() >= $row['pris'])
                        {
                            echo "<div class='item_price_label item_price color-green'> <span>" . number_format($row['pris'], 0, '.', ',') . " $</span></div>";
                        }
                        else
                        {
                            echo "<div class='item_price_label item_price color-red'> <span>" . number_format($row['pris'], 0, '.', ',') . " $</span></div>";
                        }
                    }

                }

                if ($data_level >= $row['min_level']) {
                    echo "<div class='item_min_level' style='background-color: green; color: black;'>Lvl: " . $row['min_level'] . "</div>";
                } else {
                    echo "<div class='item_min_level' style='background-color: orange; color: black;'>Lvl: " . $row['min_level'] . "</div>";
                }
                echo "<input id='item_id_hidden' name='item_id' value='" . $row['id'] . "'>";
                echo "<input id='item_type_hidden' type='hidden' name='IT' value='" . $showing_item_type . "'>";
                echo "</form>";

                echo "</div>";
                $varI++;
            }
            if ($shopItemIndex == 0) {
                echo "<div class='warning-not-found'> <span>No matching items found.</span> </div>";
            }
        }
}
