<?php
    session_start();

    $dbCon = mysqli_connect("localhost", "Stian Arnesen", "dynamicgaming", "motagamedata");

    if(isset($_SESSION['game_username']))
    {
        if($dbCon)
        {
            $session_username = $_SESSION['game_username'];

            $sqlCommands = "SELECT id, username, money, level, current_exp, next_level_exp, profile_picture FROM users WHERE username='$session_username'";

            $query = mysqli_query($dbCon, $sqlCommands);
            $data_row = mysqli_fetch_row($query);
            
            $data_user_id = $data_row[0];
            $data_username = $data_row[1];
            $data_money = $data_row[2];
            $data_level = $data_row[3];
            $data_current_exp = $data_row[4];
            $data_next_level_exp = $data_row[5];
            $data_profile_picture = $data_row[6];

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
        $inventoryQuery = "SELECT * FROM inventory WHERE user_id='$data_user_id'";
        
        $doInventoryQuery = mysqli_query($dbCon,$inventoryQuery);
        
        $total_items_in_inventory = 0;
        while($INV_ITEM = mysqli_fetch_array($doInventoryQuery))
        {
            $total_items_in_inventory++;
            $item_id = $INV_ITEM[1];
            $invItemQuery = "SELECT * FROM items WHERE (id='$item_id' AND (type='0' OR type='4'))";
            
            $ITEM = mysqli_query($dbCon,$invItemQuery);

            $ITEM_ROW = mysqli_fetch_row($ITEM);
            
            if(isset($ITEM_ROW[0]))
            {
                echo "<div class='inv_item' draggable='true'>";
                echo "<div class='inv_item_amount'> <a>" . $INV_ITEM[2] . "</a></div>";
                    echo "<span>" . $ITEM_ROW[5] . "</span><br>";
                    echo "<img src='" . $ITEM_ROW[3] . "'>";
                    echo "<div class='item_info'>";
                        echo "<a>Price: ". $ITEM_ROW[1] ." $ </a><br>";
                        echo "Grow time: ". $ITEM_ROW[8] ." seconds <br>";
                    echo "</div>";
                        echo "<input type='submit' value='Grow' class='start_growing_item' onclick='growItem(". $INV_ITEM[3] .")'>";
                        echo "<input type='hidden' id='grow_item_id' name='grow_item_id' value='$ITEM_ROW[4]'>";
                        echo "<input type='hidden' id='grow_item_inv_id' value='$INV_ITEM[0]'>";
                echo "</div>";    
            }
            
        }
        if($total_items_in_inventory == 0)
        {
            echo '<h1>No plants in inventory</h1>';
        }
    }
?>