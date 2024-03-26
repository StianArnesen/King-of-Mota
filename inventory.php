<?php
  define("INVENTORY_TYPE_SHOP_ITEM", 0);
  define("INVENTORY_TYPE_LAB_ITEM", 1);

  session_start();

  require("storage/StorageController.php");
  require("layout/inventory-item/InventoryItem.php");
  require("layout/inventory-item/InventoryLabItem.php");

  require("common/page.php");

  $PAGE = new PageClass();

  $STORAGE = new StorageController();

  $BACKPACK_ID = $STORAGE->getBackpackStorageID();

  $INVENTORY_SPACE_LEFT = $STORAGE->getStorageCapacity($BACKPACK_ID) - $STORAGE->getSpaceUsedInStorage($BACKPACK_ID);

  if($BACKPACK_INFO_ARRAY  = $STORAGE->getStorageInfo($BACKPACK_ID))
  {
    if(isset($BACKPACK_INFO_ARRAY['ERR']))
    {
      echo("failed to fetch storage info");
    }
    else
    {
      $STORAGE_UPGRADE_PRICE = $BACKPACK_INFO_ARRAY['upgrade_price'];
    }
  }
  else
  {
    
    echo("failed to fetch storage info");
  }

  $dbCon = mysqli_connect("localhost", "Stian Arnesen", "dynamicgaming", "motagamedata");
  header('Content-Type: text/html; charset=utf8');
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
  function getExcerpt($str, $limit)
  {
    if(strlen($str) > $limit)
    {
      return substr($str, 0, $limit) . "...";
    }
    return $str;
  }

  function getItemInfo($ITEM_ID)
  {
      $dbCon = mysqli_connect("localhost", "Stian Arnesen", "dynamicgaming", "motagamedata");

      $SQL_21 = "SELECT * FROM items WHERE id='$ITEM_ID'";
      $QUERY_21 = mysqli_query($dbCon, $SQL_21);

      return mysqli_fetch_array($QUERY_21);
  }

  $session_user_id= $_SESSION['game_user_id'];

  $sqlCommands = "SELECT * FROM inventory WHERE user_id='$session_user_id'";

  $query = mysqli_query($dbCon, $sqlCommands);

  $currentTime = time();

  $setLastActive_query = "UPDATE users SET last_active='$currentTime' WHERE id='$data_user_id'";
  $doLastActiveQuery = mysqli_query($dbCon,$setLastActive_query);

?>

<script>
less = {
  env: "development",
  async: true,
  fileAsync: true,
  poll: 1000,
  functions: {},
  dumpLineNumbers: "comments",
  relativeUrls: false,
  rootpath: ":/a.com/"
};
</script>
<!DOCTYPE html>
<html>
<head>
  <link href="style/inventory/style.less" rel="stylesheet/less" type="text/css">
  <link href="/style/storage/storage.less" rel="stylesheet/less" type="text/css">

  <link href="style/global/global.css" rel="stylesheet" type="text/css">

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/less.js/2.5.3/less.min.js" type="text/javascript"  ></script>

  <script src="script/inventory/inventory.js" type="text/javascript"></script>
  <script src="script/inventory/inv.js" type="text/javascript"></script>

  


  <?php echo $PAGE->getHeaderInfo(); ?>

  <title>King of Mota | Inventory</title>
</head>
  <body>
      <?php
        $btn_file = fopen("layout/top_banner/top_banner.php", "r") or die("Unable to open file!");
      echo fread($btn_file,filesize("layout/top_banner/top_banner.php"));
      fclose($btn_file);
      ?>
      </div>

      <div id="inventory">
          <div id="inventory_view">
            <div id="inventory_title">
              <span>Inventory</span>
            </div>
          <div class="storage-info-view">
            
            <div class="storage-info-title">
              <?echo $BACKPACK_INFO_ARRAY['title'] ?>
            </div>
            
            <div class="storage-info-img">
              <img src="img/storage_inventory/inventory.png">
            </div>

            <div class="storage-info-div">
              
              <div class="storage-info-level" id="storage-level">
                <span>Storage level:  <?echo $BACKPACK_INFO_ARRAY['storage_level'] ?></span>
              </div>

              <div class="storage-info-space" id="storage-space-info">
                  Space: 
                <div class="storage-info-space space-used" id="storage-space-used">

                  <?echo $BACKPACK_INFO_ARRAY['storage_used'] ?>
                </div>
                <span>/</span>
                <div class="storage-info-space space-total" id="storage-space-total">
                  <?echo $BACKPACK_INFO_ARRAY['storage_total'] ?>
                </div>

              </div>

              <div class="storage-upgrade-view">
                  <div class="storage-upgrade-button money-valid" id="upgrade-button">Upgrade! (+3)</div>
                  <div class="storage-upgrade-button-price">
                    <div id="upgrade-price"> <?echo $STORAGE_UPGRADE_PRICE ?> $ </div>
                    
                  </div>

              </div>

            </div>

          </div>
          
          <div class="inventory-items-view-title">
            <div class="title">
              
            </div>
          </div>
      <?php

        $dbCon = mysqli_connect("localhost", "Stian Arnesen", "dynamicgaming", "motagamedata");

        $session_user_id= $_SESSION['game_user_id'];
  

        $valid_storage_id = $BACKPACK_ID;
  
        $sqlCommands = "SELECT inventory.*, items.type FROM inventory, items WHERE inventory.storage_id='$valid_storage_id' AND (inventory.user_id='$data_user_id' AND inventory.item_id=items.id) AND  inventory.inventory_type=". INVENTORY_TYPE_SHOP_ITEM ."  ORDER BY items.type DESC,inventory.inv_item_status_type DESC";

        if($query = mysqli_query($dbCon, $sqlCommands))
        {

        }
        else
        {
            die("Sorry! There seems to be a problem here :|");
        }

        $LAST_STATUS_TYPE = -1;
        $LAST_TYPE = -1;

        $TYPE_CHANGE = false;
        $STATUS_TYPE_CHANGE = false;


        echo "<div id='inventory_items'>";

        $TOTAL_ITEMS = 0;

        $LAB_PRODUCTS = $STORAGE->getLabProductsFromStorage($BACKPACK_ID);
        
        if($LAB_PRODUCTS)
        {
          if($LAB_PRODUCTS == null)
          {
            die("<strong><h1>FAILED TO FETCH LAB_PRODUCTS!<h1></strong>");
          }
          else
          {
            for($I = 0; $I < sizeof($LAB_PRODUCTS); $I++)
            {
                $PRODUCT= $LAB_PRODUCTS[$I];
                $ID     = $PRODUCT['id'];
                $INV_ID = $PRODUCT['inv_id'];
                $NAME   = $PRODUCT['name'];
                $IMG    = $PRODUCT['img'];
                $AMOUNT = $PRODUCT['amount'];
                $QUALITY = $PRODUCT['quality'];

                $LAB_ITEM_LAYOUT = new InventoryLabItem($LAB_PRODUCTS[$I]);

                echo $LAB_ITEM_LAYOUT->getInventoryItemHtml();
            }
          }
        }
        
        while($data_row = mysqli_fetch_array($query))
        {
          $TOTAL_ITEMS++;
            if($data_row['type'] != $LAST_TYPE)
            {
              
                $TYPE_CHANGE = true;
                $LAST_TYPE = $data_row['type'];
                if($LAST_TYPE == 0 || $LAST_TYPE == 7){
                    echo "<div class='inv-item-view-divider-group'>Drugs: </div>";
                }
                else if($LAST_TYPE == 1){
                    echo "<div class='inv-item-view-divider-group'>Vehicles: </div>";
                }
                else if($LAST_TYPE == 2){
                    echo "<div class='inv-item-view-divider-group'>Weapons: </div>";
                }
                else if($LAST_TYPE == 3){
                    echo "<div class='inv-item-view-divider-group'>Growing lights: </div>";
                }
                else if($LAST_TYPE == 6){
                    echo "<div class='inv-item-view-divider-group'>Air vent: </div>";
                }
                else if($LAST_TYPE == 5){
                    echo "<div class='inv-item-view-divider-group'>Soil: </div>";
                }
            }
            if($data_row['inv_item_status_type'] != $LAST_STATUS_TYPE)
            {
                $STATUS_TYPE_CHANGE = true;
                //echo "<div class='inv-items-view-group-status-type'>";
                if($LAST_TYPE == 0)
                {
                    $LAST_STATUS_TYPE = $data_row['inv_item_status_type'];
                    if($LAST_STATUS_TYPE == 0){
                        echo "<div class='inv-item-view-sub-divider-group'>Seeds: </div>";
                    }
                    else if($LAST_STATUS_TYPE == 1){
                      echo "<div class='inv-item-view-sub-divider-group'>Consumer ready: </div>";
                    }
                }
            }
            $search = 0;

            $data_item_id = $data_row['item_id'];
            $data_item_amount = $data_row['item_amount'];
                        $data_item_status_type = $data_row['inv_item_status_type'];

            if(! isset($_GET['searchVal']))
            {
              $query1 = "SELECT * FROM items WHERE (id='$data_item_id')";
            }
            else
            {
              $searchValue = $_GET['searchVal'];
              $query1 = "SELECT * FROM items WHERE
              (
                name LIKE '%". $searchValue ."%'
              )";
              $search = 1;
            }

            $item = mysqli_query($dbCon,$query1);
            $item_row = mysqli_fetch_array($item);

                      $INVENTORY_ITEM_LAYOUT = new InventoryItem($data_row['item_id'], $data_row['inv_id'], $item_row['name'], $item_row['picture'], $data_row['item_amount']);

                      echo $INVENTORY_ITEM_LAYOUT->getInventoryItemHtml();

          if($search == 1)
          {
            break;
          }
        }
        


          



        if($TOTAL_ITEMS == 0)
        {
            echo "<h2 style='color: white; margin: 5px'>No items in inventory</h2>";
        }
        echo "</div>";
      ?>
			</div>
            <div class="inventory-action-view" id="inv-action-view">
                <div class="inv-action-title">Selected: </div>
                <button id="inventory-action-button-trash" onclick="trashItems()">Trash</button>
                <button id="inventory-action-button-move" onclick="showStorageList()">Move</button>
                <div id="inv-selected-amount"></div>
            </div>
    </div>
    </body>

    <div id="overlay-view"> </div>
    <div id="overlay-data" class="overlay-data"> </div>

</html>