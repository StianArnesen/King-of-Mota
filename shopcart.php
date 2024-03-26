<?php

    session_start();

    require("storage/StorageController.php");

    $STORAGE = new StorageController();

    $BACKPACK_ID = $STORAGE->getBackpackStorageID();

    $INVENTORY_SPACE_LEFT = $STORAGE->getStorageCapacity($BACKPACK_ID) - $STORAGE->getSpaceUsedInStorage($BACKPACK_ID);

    $dbCon = mysqli_connect("localhost", "Stian Arnesen", "dynamicgaming", "motagamedata");


    $pay_failed = -1;

    if(isset($_SESSION['game_username']) && ($_SESSION['game_username'] != ""))
    {

    }
    else
    {
        header("Location: index.php");
    }
    
    if(isset($_SESSION['game_username']))
    {
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

    if($dbCon)
	{
        $input_username = strip_tags($_SESSION['game_username']);

        $sqlCommands = "SELECT id, money, inventory FROM users WHERE username='$input_username'";

        $query = mysqli_query($dbCon, $sqlCommands);
        $data_row = mysqli_fetch_row($query);

        $data_user_id = $data_row[0];
        $data_money = $data_row[1];
        $data_inventory = $data_row[2];
        
        if(isset($_POST['item_id_buy'])){
            $item_id = $_POST['item_id_buy'];    
        }
        
        if(isset($_POST['buy_amount']))
        {
            $item_amount = $_POST['buy_amount'];
            
            if($item_amount >= 1)
            {
                if($item_amount <= $INVENTORY_SPACE_LEFT)
                {
                    //$update_inventory_data_query = "UPDATE inventory SET item_amount='$new_amount' WHERE user_id='$data_user_id' AND storage_id='$BACKPACK_ID' AND inv_item_status_type='$INV_ITEM_STATUS_TYPE' AND item_id='$item_id'";    
                }
                else
                {
                    $pay_failed = 62;
                }
            }
            else
            {
                die("Failed to buy item. ");
            }
        }



        

        if(isset($_POST['item_id_buy']) && $dbCon)
        {
            $item_id = $_POST['item_id_buy'];

            $getItem = "SELECT * FROM items WHERE id='$item_id'";

            $doGetItem = mysqli_query($dbCon, $getItem);

            $get_item_row = mysqli_fetch_array($doGetItem);

            if($get_item_row['min_level'] > $data_level){
                die("ERROR: LEVEL");
            }

            $TOTAL_PRICE_FOR_BUY = ($get_item_row['pris'] - (($get_item_row['pris']/100) * ($get_item_row['discount'])))*$item_amount;
        }
        else
        {
            
        }
        if(false)
        {
            $pay_failed = 5;
        }
        else
        {
            if(isset($_POST['buy_item']) && $pay_failed === -1)
            {
                if($dbCon)
                {
                    if($data_money >= $TOTAL_PRICE_FOR_BUY)
                    {

                        $old_money = $data_money;
                        $new_money = $old_money - $TOTAL_PRICE_FOR_BUY;
                        
                        $update_data_query = "UPDATE users SET money='$new_money' WHERE id='$data_user_id'";
                        $run = mysqli_query($dbCon, $update_data_query);

                        

                        $invItemQuery = "SELECT * FROM inventory WHERE (user_id='$data_user_id' AND item_id='$item_id') AND storage_id='$BACKPACK_ID' AND inv_item_status_type='0'";

                        $doInvItemQuery = mysqli_query($dbCon,$invItemQuery);

                        $INV_ITEM = mysqli_fetch_array($doInvItemQuery);

                        if(isset ($INV_ITEM))
                        {
                            $INV_ITEM_STATUS_TYPE = $INV_ITEM['inv_item_status_type'];
                            $new_amount = ($INV_ITEM['item_amount']+$item_amount);

                            if($item_amount <= $INVENTORY_SPACE_LEFT)
                            {
                                $update_inventory_data_query = "UPDATE inventory SET item_amount='$new_amount' WHERE user_id='$data_user_id' AND storage_id='$BACKPACK_ID' AND inv_item_status_type='$INV_ITEM_STATUS_TYPE' AND item_id='$item_id'";    
                            }
                            else
                            {
                                $pay_failed = 62;
                            }
                        }
                        else
                        {
                            $update_inventory_data_query = "INSERT INTO inventory (user_id, item_id, item_amount, storage_id) VALUES('$data_user_id','$item_id','$item_amount', '$BACKPACK_ID')";
                        }
                        
                        mysqli_query($dbCon, $update_inventory_data_query);

                        $pay_failed = 0;
                    }
                    else
                    {
                        $pay_failed = 1;
                    }
                }
                else
                {
                    $pay_failed = 7;
                }
            }
            else
            {
                
            }
        }
    }
	else
	{
		die("Connection failed!");
	}
?>

<html>
    <head>
        <link href="style/shopcart/style.css" rel="stylesheet" type="text/css">

        <title>King of Mota - My cart</title>

    </head>
    <body>
    <?php
        $btn_file = fopen("layout/top_banner/top_banner.php", "r") or die("Unable to open file!");
        echo fread($btn_file,filesize("layout/top_banner/top_banner.php"));
        fclose($btn_file);
    ?>

    <div id="cart">

        <div id="cart_view">
            <div id="cart_item">
                <?php

                    if(isset($_POST['item_id_buy']))
                    {
                        $item_id = $_POST['item_id_buy'];    
                    }
                    else
                    {
                        $item_id = $_POST['item_id'];    
                    }

                    $query1 = "SELECT * FROM items WHERE id='$item_id'";

                    $item = mysqli_query($dbCon,$query1);

                    $item_row = mysqli_fetch_array($item);

                    if(! isset($item_row[0]))
                    {
                        die("Something went wrong:     @5");
                    }

                    echo "<div id='item_title'> <a href='item.php?item_id=$item_id'>" . $item_row['name'] . "</a></div>";
                    echo "<div id='item_img'> <img src='" . $item_row['picture'] . "'>";
                    echo "<div id='item_desc'>" . $item_row['beskrivelse'] . "</div></div>";

                    $price = $item_row['pris'];
                    $TOTAL_ITEM_PRICE = ($item_row['pris'] - (($item_row['pris']/100) * ($item_row['discount'])))*$item_amount;



                ?>
                </div>
            <div id="cart_checkout">
                <?php
                if($item_row['discount'] > 0)
                {
                    echo "<div class='item_price_label' id='item_price'> Price: " .$price  . "$</div>";
                }
                else
                {
                    echo "<div class='item_price_label' id='item_price'> Price: " . $price . "$</div>";
                }
                    echo "<div id='item_amount'> Amount: " . $_POST['buy_amount'] . "</div>";
                    echo "<div class='item_price_label' id='checkout_price'>Total: " . $TOTAL_ITEM_PRICE . "$</div>";
                ?>
                </div>

                <form class="checkout_form" id="buy_form"  action="shopcart.php" method="post" enctype="application/x-www-form-urlencoded">
                    <input type="submit" id="btn_buy" value="Buy" name="buy_item">
                    <div id="buy_status">
                        <?php
                            if($pay_failed == 1)
							{
                                echo "<h1>You need more money</h1>";
                            }
                            else if($pay_failed == 0)
							{
                                echo "Buy success!";
                            }
                            else if($pay_failed == 2)
							{
                                echo "Error 2";
                            }
                            else if($pay_failed == 5)
                            {
                                echo "Error 5";
                            }
                            else if($pay_failed == 7)
                            {
                                echo "Connection failed!";
                            }
                            else if($pay_failed == 62)
                            {
                                echo "Not enough space in inventory!";
                            }
                        ?>
                    </div>
					
                    <input hidden="hidden" value="<?php echo $TOTAL_ITEM_PRICE?>" name="price">
                    <input type="hidden" value="<?php echo $item_amount?>" name="buy_amount">
                    <input type="hidden" value="<?php echo $item_id?>" name="item_id_buy">
                </form>

            </div>
    </div>
    </div>


</body>
</html>

<script type="text/javascript" src="script/common/priceFix.js">
    fixPriceLabel("item_price_label");
</script>

<?php 
    mysqli_close($dbCon);
?>

<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<script>
    $("#cart_view").fadeIn(300);
    
</script>