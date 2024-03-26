<?php


/*        PublicShopcart      */


if(isset($_POST['BUY_ITEM']))
{
    $ID     = null;
    $AMOUNT = null;

    if(isset($_POST['ID']) && isset($_POST['AMOUNT']))
    {
        $ID     = ($_POST['ID']);
        $AMOUNT = ($_POST['AMOUNT']);

        if(! is_numeric($ID) || ! is_numeric($AMOUNT)) { die("FORMAT_UNKNOWN_A_B");}
    }
    else
    {
        die("FORMAT_UNKNOWN_A_A");
    }

    $S = new PublicShopCart();

    $BUY_RESULT = $S->buyItem($ID, $AMOUNT);
    
    die(json_encode($BUY_RESULT, JSON_PRETTY_PRINT));
}
else
{
    die("INVALID");
}

class PublicShopCart
{
    private $CONNECTION;

    private $USER;

    private $STORAGE;

    public function __construct()
    {
        if($this->_connect())
        {
            if($this->_loadUser())
            {
                if($this->loadStorage())
                {

                }
                else
                {
                    $R = array(
                        "STATUS"    => "FAILED",
                        "DBUG_MSG"  => "STORAGE_INIT_FAILED"
                    );
                    array_push($ERR_ARRAY, $R);
                }
            }
            else
            {
                $R = array(
                    "STATUS"    => "FAILED",
                    "DBUG_MSG"  => "USER_AUTH_FAILED"
                );
                array_push($ERR_ARRAY, $R);
            }
        }
        else
        {
            $R = array(
                "STATUS"    => "FAILED",
                "DBUG_MSG"  => "CONN_FAILED"
            );
            array_push($ERR_ARRAY, $R);
        }
    }
    private function loadStorage()
    {
        require_once(__DIR__ . "/../storage/StorageController.php");

        if($this->STORAGE = new StorageController())
        {
            return true;
        }
        return false;
    }
    private function getItemInfo($ITEM_ID)
    {
        $SQL = "SELECT * FROM items WHERE id=$ITEM_ID LIMIT 1";

        if($QUERY = mysqli_query($this->CONNECTION, $SQL))
        {
            if($RESULT = mysqli_fetch_array($QUERY))
            {
                return $RESULT;
            }
        }
        return null;
    }
    public function buyItem($ITEM_ID, $ITEM_AMOUNT)
    {
        $ITEM_INFO          = $this->getItemInfo($ITEM_ID);

        $ITEM_PRICE         = (int)$ITEM_INFO['pris'];
        $ITEM_G_PRICE       = (int)$ITEM_INFO['g_price'];
        $ITEM_LEVEL         = (int)$ITEM_INFO['min_level'];

        $CURRENT_G_COINS    = $this->USER->getCoins();

        $BACKPACK_ID            =  (int)$this->STORAGE->getBackpackStorageID();
        $STORAGE_SPACE_USED     =  (int)$this->STORAGE->getSpaceUsedInStorage($BACKPACK_ID);
        $STORAGE_SPACE_TOTAL    =  (int)$this->STORAGE->getStorageCapacity($BACKPACK_ID);

        $ITEM_PRICE_TOTAL       =  ($ITEM_PRICE   * $ITEM_AMOUNT);
        $ITEM_G_PRICE_TOTAL     =  ($ITEM_G_PRICE * $ITEM_AMOUNT);

        if($ITEM_LEVEL > $this->USER->getLevel())
        {
            return array(
                "STATUS"    => "FAILED",
                "DBUG_MSG"  => "LEVEL"
            );
        }

        $LOG = "";

        if($this->USER->getMoney() >= $ITEM_PRICE_TOTAL && $ITEM_PRICE >= 0)
        {
            $LOG .= "MONEY: 1 \n";
        }
        else if($ITEM_PRICE == -1 && $this->USER->getCoins() >= $ITEM_G_PRICE_TOTAL)
        {
            $LOG .= "G_COINS: 1 \n";
        }
        else
        {
            $LOG .= "NO_COINS_AND_NO_MONEY: 1 \n";
            return array(
                "STATUS"    => "FAILED",
                "DBUG_MSG"  => "MONEY",
                "G_PRICE"   => $ITEM_G_PRICE_TOTAL,
                "G_COINS"   => $CURRENT_G_COINS,
                "G_SUM"     => $CURRENT_G_COINS - $ITEM_G_PRICE_TOTAL,
                "LOG"       => $LOG
            );
        }



        if($STORAGE_SPACE_TOTAL >= $ITEM_AMOUNT + $STORAGE_SPACE_USED)
        {
            if($this->STORAGE->insertItemToInventory($ITEM_INFO, $ITEM_AMOUNT))
            {
                if($ITEM_PRICE_TOTAL >= 0)
                {
                    if($this->USER->subtractMoney($ITEM_PRICE_TOTAL)) {
                        return array(
                            "STATUS"    => "OK",
                            "MSG"       => "MONEY_VALID",
                            "PRICE"     => $ITEM_PRICE_TOTAL,
                            "LOG"       => $LOG
                        );
                    }
                }
                else if($ITEM_PRICE == -1 && $this->USER->subtractCoins($ITEM_G_PRICE_TOTAL))
                {
                    return array(
                        "STATUS" => "OK",
                        "MSG"    => "G_COIN_VALID"
                    );
                }
                else
                {
                    return array(
                        "STATUS"    => "FAILED",
                        "DBUG_MSG"  => "MONEY_SUBTRACT_FAIL"
                    );
                }

            }
            else
            {
                return array(
                    "STATUS"    => "FAILED",
                    "DBUG_MSG"  => "SPACE"
                );
            }
        }
        else
        {
            return array(
                "STATUS"    => "FAILED",
                "DBUG_MSG"  => "SPACE"
            );
        }
        return array(
            "STATUS" => "FAILED"
        );
    }
    private function _loadUser()
    {
        require_once(__DIR__ . "/../common/session/sessioninfo.php");

        if($this->USER = new User(-1, -1))
        {
            return true;
        }
        return false;
    }
    private function _connect()
    {
        require_once(__DIR__ . "/../connect/database.php");

        if($this->CONNECTION = Database::getConnection())
        {
            return true;
        }
        return false;
    }
}
