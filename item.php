<?php
    session_start();
    include("layout/profile_banner/profileinfoClass.php");
    include("layout/item_small/small_item.php");
    include("common/secure.php");
    include("utils/se_utils.php");
    require("common/page.php"); // PageClass -> The page utils used to get top banner and header info.


    //Load the page-utils (From PageClass)
    $PAGE = new PageClass();



    $SECURE = new Secure();

    $profileBanner = new ProfileBanner();

    $dbCon = mysqli_connect("localhost", "Stian Arnesen", "dynamicgaming", "motagamedata");
    mysqli_set_charset($dbCon,"utf8");
    if(isset($_SESSION['game_username']))
    {
        if($dbCon)
        {
            $session_username = $_SESSION['game_username'];
            
            $sqlCommands = "SELECT id, username, money, level, current_exp, next_level_exp, profile_picture FROM users WHERE username='$session_username'";

            $query = mysqli_query($dbCon, $sqlCommands);
            $data_row = mysqli_fetch_array($query);

            $data_user_id = $data_row['id'];
            $data_username = $data_row['username'];
            $data_money = $data_row['money'];
            $data_level = $data_row['level'];
            $data_current_exp = $data_row['current_exp'];
            $data_next_level_exp = $data_row['next_level_exp'];
            $data_profile_picture = $data_row['profile_picture'];

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

    if(! isset($_SESSION['game_username'])){
        header("Location: index.php");
    }
    if(isset($_GET['item_id']))
    {
        if($dbCon)
        {
            $item_id = $SECURE->STRIP_STRING($_GET['item_id']);

            $query1 = "SELECT * FROM items WHERE id='$item_id'" ;

            $item = mysqli_query($dbCon,$query1);

            $item_row = mysqli_fetch_array($item);

            $ITEM_IMAGE = $item_row['picture'];

            $buy_item_unique_id = uniqid();

            $currentTime = time();

            $setLastActive_query = "UPDATE users SET last_active='$currentTime' WHERE id='$data_user_id'";
            $doLastActiveQuery = mysqli_query($dbCon,$setLastActive_query);

        }
        else
        {
            die("Connection failed!");
        }

        if(($item_row[1] == "")){
            echo "<h3>Item not found!</h3>";
        }
    }
    else{
        die ("Sorry, item not found!");
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
?>
<html>
    <head>

        <link href="style/item/style.less" rel="stylesheet/less" type="text/less">

        <title>King of Mota | <?php echo $item_row['name'];?></title>
        
        <?php echo $PAGE->getHeaderInfo();?>

        <script src="script/util/util.js"></script>
        <script src="script/item/item.js"></script>

        <script src="loader/src/jquery.percentageloader-0.1.js"></script>
        <link rel="stylesheet" href="plugin/progressbar/goalProgress.css">
        
    </head>
        <body>
        <?php echo $PAGE->getTopBanner()?>


        <!--  PURCHASE OVERLAY   -->

        <div id="overlay-view"> </div>
        <div id="overlay-data" class="overlay-data">
            <div class="dialog-title" id="dialog-title"> <?php echo $item_row['name'];?> </div>
            <div id="overlay-status"> </div>
            <div class="dialog-image">
                <img id="overlay-item-img" class="global_item-image" src="<?php echo $ITEM_IMAGE;?>">    
                <div id="dialog-amount">1x</div>
            </div>
            
            <div id="overlay-item-price"> </div>

        </div>


        <!--  |END|     PURCHASE OVERLAY      |END| -->


        <div id="item_infoview" class="main-page">
            <?php
                if(isset($_GET['IT'])){
                    $S_IT = $_GET['IT'];
                    echo '<a href ="shop.php?category='. $S_IT.'" class="back-button"><img class="icon-back" src="img/icon/back_icon.png"> <span>shop</span></a>';
                }
            ?>




            <div id="item_info">

                <div id="item_picture">
                    <div id="item_title">
                        <?php
                        echo $item_row['name'];
                        $newPrice = $item_row['pris'] - (($item_row['pris']/100) * ($item_row['discount']));
                        ?>
                    </div>
                    <img class="global_item-image" src="<?php echo $item_row['picture']?>">
                </div>
                <div id="item_desc">
                    <?php
                        echo $item_row['beskrivelse'];
                    ?>
                </div>
                    <?php
                        if($item_row['min_level'] <= $data_level)
                        {
                            $ITEM_ID = $item_row['id'];

                            $g_price = $item_row['g_price'];

                            echo '<viewport><div class="item_buy_form">
                                    <form class="buy_form" method="post" id="item-buy-form" enctype="multipart/form-data">
                                        <div class="buy-input-field">            
                                            <input  id="item_buy_amount" type="number" min="1" max="10" autocomplete="off"  onchange="updatePriceLabel(this)" name="buy_amount" editable="false" value="1">
    
                                            <input type="hidden" name="item_id" id="current_item_id" value="'. $ITEM_ID .'">
                                            <input type="hidden" id="buy_item_unique_id" name="buy_item_unique_id" value="'. $buy_item_unique_id .'">
                                            <input type="button" id="buy_form_submit" value="Buy">
                                        </div>
                                        <input type="hidden" id="item_amount_price_label" value="'. $newPrice .'">
                                        <input type="hidden" id="item_amount_g_price_label" value="'. $g_price .'">
                                        
                                        <h5 id="price_times_amount" class="item_price_label">'. StaticUtils::currencyFormat($newPrice) .'$</h5>
                                    </form>
                                </div></viewport>';
                        }
                        else
                        {
                            echo '<div class="item_buy_form">';
                            echo '    <div class="item-padlock-view">';
                            echo '        <img src="img/item/lock.png" class="image-padlock">';
                            echo '        <div class="min-level-text"> Level '. $item_row['min_level'] .'</div>';
                            echo '    </div>';
                            echo '</div>';
                        }
                    ?>
                <div id="item_price">
                    <h1>
                        <?php
                            if($item_row['discount'] > 0)
                            {
                                echo "<div id='item_price_discount' class='item_price_label'>". $item_row['pris']. " $</div>";
                                $newPrice = $item_row['pris'] - (($item_row['pris']/100) * ($item_row['discount']));
                                echo "<div id='item_discount' class='item_price_label'>" . ($newPrice) . " $</div>";
                            }
                            else
                            {
                                if($item_row['pris'] == -1)
                                {
                                    echo "<div id='item_price' class='item_price_label'>". StaticUtils::currencyFormat($item_row['g_price']). " <img src=\"/img/icon/g_coin.jpg\" title='G-Coins' style=\"
    width: 45px;
    height: auto;
    padding: 0;
    margin: 0;
    float: left;
\"></div>";
                                }
                                else
                                {
                                    echo "<div id='item_price' class='item_price_label'>". StaticUtils::currencyFormat($item_row['pris']) . " $</div>";
                                }

                            }
                        ?>
                    </h1>
                </div>


            </div>

        </div>
        <div id="item_specs">
            <div id="item_specs_title">
                <span>Info:</span>
            </div>
            <div id="item_specs_subtitle">
                <span>Specifications: </span>
            </div>
            <script src="plugin/progressbar/goalProgress.js"></script>
                <?php
					
					$item_type = $item_row['type'];
					
					$getMaxTimeSql     = "SELECT MAX(grow_time) AS time FROM items WHERE type='$item_type' limit 1";
					$getMaxPowerSql    = "SELECT MAX(item_power) AS power FROM items WHERE type='$item_type' limit 1";
					$getMaxExpSql      = "SELECT MAX(item_info_a) AS exp FROM items WHERE type='$item_type' limit 1";

					$getMaxTimeQuery   = mysqli_query($dbCon, $getMaxTimeSql);
					$getMaxPowerQuery  = mysqli_query($dbCon, $getMaxPowerSql);
                    $getMaxExpQuery    = mysqli_query($dbCon, $getMaxExpSql);
					
					$MAX_GROW_TIME = mysqli_fetch_row($getMaxTimeQuery);
					$MAX_POWER     = mysqli_fetch_row($getMaxPowerQuery);
                    $MAX_EXP       = mysqli_fetch_row($getMaxExpQuery);
					
					$MAX_GROW_TIME     = $MAX_GROW_TIME[0];
					$MAX_POWER         = $MAX_POWER[0];
                    $MAX_EXP           = $MAX_EXP[0];
					
                    if($item_row[2] == 0) //Plant
                    {
                        echo "<div class='item_specs_item' id='info-bar-power'> <span>Profit: </span></div>";
                        echo "<div class='item_specs_item' id='info-bar-time'>";
                            echo "<span>Grow time: </span>";
                        echo '</div>';
                        echo '<div class="item_specs_item">';
                            echo '<div id="info-bar-thc"><span>EXP Gain: </span></div>';

                        echo '</div>';
						
						echo "<script type='text/javascript'>
                                        $('#info-bar-thc').goalProgress({
                                            goalAmount: ". ($MAX_EXP) .",
                                            currentAmount: ". $item_row['item_info_a'] .",
                                            textBefore: '',
                                            textAfter: ' exp'
                                        });
                            </script>";
							
						 echo "<script type='text/javascript'>
									$('#info-bar-power').goalProgress({
										goalAmount: ". ($MAX_POWER + 13) .",
										currentAmount: ". $item_row['item_power'] .",
										textBefore: '',
										textAfter: ''
									});
						</script>";
						
                        echo "<script type='text/javascript'>
                                        $('#info-bar-time').goalProgress({
                                            goalAmount: ". ($MAX_GROW_TIME + 13) .",
                                            currentAmount: ". $item_row['grow_time'] .",
                                            textBefore: '',
                                            textAfter: ' s (" . secondsToTime($item_row['grow_time']). ")'
                                        });
                            </script>";
                    }
                    else if($item_row[2] == 1) // Vehicle
                    {
                      echo '<div class="item_specs_item" id="info-bar-hp"> </div>';
                      echo '<div class="item_specs_item" id="info-bar-top-speed"></div>';


                      echo "<script type='text/javascript'>
                                      $('#info-bar-hp').goalProgress({
                                          goalAmount: 500,
                                          currentAmount: ". $item_row['item_info_a'] .",
                                          textBefore: 'HP: ',
                                          textAfter: ''
                                      });
                                      $('#info-bar-top-speed').goalProgress({
                                          goalAmount: 455,
                                          currentAmount: ". $item_row['item_info_a'] .",
                                          textBefore: 'Top speed: ',
                                          textAfter: '  km/h'
                                      });
                          </script>";
                    }
                    else if($item_row['sub_type'] == 3)
                    {
                        echo '<div class="item_specs_item">';
                            echo "<span>Grow time: <span>". $item_row['grow_time'] ." seconds </span></span>";
                        echo '</div>';
                    }
                    else if($item_row['sub_type'] == 2)
                    {
                        echo '<div class="item_specs_item">';
                            echo "<span>RPM: <span>". $item_row['item_info_a'] ."</span></span>";
                        echo '</div>';
                    }
                    else
                    {
                        echo "Hmm.. this item is currently not configured correctly... Please report this at our <a href='feedback.php' class='link' target='_blank'>Feedback page</a>";
                    }
                ?>

        </div>
        <div class="rec-items-view">
            <div class="rec-items-title"><span>Related products</span></div>
            <?php

                $produktType = $item_row[2];

                $produktForslagSQL = "SELECT * FROM items WHERE (type='". $produktType . "' AND id !='". $item_row[4] ."' AND item_active=1) ORDER BY rand()  LIMIT 4";
                $hentForslagQuery = mysqli_query($dbCon, $produktForslagSQL);

                while($ITEM = mysqli_fetch_array($hentForslagQuery))
                {
                    $EXCERPT = $ITEM['beskrivelse'];

                    if(strlen($EXCERPT) >= 90)
                    {
                        $EXCERPT = substr($EXCERPT, 0, 87) . "...";
                    }

                        if($ITEM['discount'] >= 1)
                        {
                            $newPrice = ($ITEM['pris'] - (($ITEM['pris']/100) * ($ITEM['discount'])));
                        }
                        else
                        {
                            $newPrice = $ITEM['pris'];
                        }


                            // __construct($TITLE, $IMG, $PRICE, $DESC, $TYPE)
                    $S_ITEM = new smallItem($ITEM['id'], $ITEM['name'], $ITEM['picture'], $newPrice, $ITEM['pris'], $ITEM['g_price'], $EXCERPT, $ITEM['type']);
                    echo $S_ITEM->getItem();
                }
            ?>

        </div>
        



<script type="text/javascript" src="script/common/priceFix.js"></script>
    <script>
        var amount = 1;
    
        function fixPriceLabel(className)
        {
            var items_list = document.getElementsByClassName(className);
    
            for(var i = 0; i < items_list.length; i++){
                items_list[i].innerHTML = numberWithCommas(items_list[i].innerHTML);
            }
        }
        function fixPriceLabelId(id)
        {
            var item = document.getElementById(id);
            item.innerHTML = numberWithCommas(item.innerHTML);
        }
        fixPriceLabel("item_price_label");
    
        try{
            var item_price = document.getElementById("item_amount_price_label").value;
            var item_g_price = document.getElementById("item_amount_g_price_label").value;
        }
        catch(e)
        {
            
        }
        
        $(document).ready(function()
        {
            var buy_form = document.getElementById("item_buy_amount");
            
            updatePriceLabel(buy_form);
        });
    
        function updatePriceLabel(element)
        {
            if(item_price >= 0)
            {
                var newPrice    = (item_price*element.value);
                newPrice        = newPrice.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

                //x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                $("#price_times_amount").html(newPrice + " $");
            }
            else
            {
                var newPrice    = (item_g_price*element.value);
                newPrice        = newPrice.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                $("#price_times_amount").html(newPrice + " G");   
            }
            
        }
        var getAmount = function(){
            return amount;
        }
    
    
    </script>
    </body>
</html>
