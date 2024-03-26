<?php

$ROOT   = $_SERVER['DOCUMENT_ROOT'];

require_once($ROOT . 'utils/se_utils.php');

class InventoryItem
{
    private $HTML;


    /**
     * InventoryItem constructor.
     * @param $ITEM_ID
     * @param $INV_ID
     * @param $NAME
     * @param $ITEM_IMG
     * @param $ITEM_AMOUNT
     */
    public function __construct($ITEM_ID, $INV_ID, $NAME, $ITEM_IMG, $ITEM_AMOUNT)
    {
        $HTML   = "<div class='inventory-item'> <input type='hidden' name='item-id-value' value='". $ITEM_ID ."'> <input type='hidden' name='inv-id-value' value='". $INV_ID ."'> ";
        $HTML  .= "<div class='inventory-item-title'> <span>" . StaticUtils::getExcerpt($NAME, 15) . "</span></div>";


        $HTML  .= "<div class='inventory-item-img-container'> <img class='inventory-item-img' src='" . $ITEM_IMG . "'> </div>" ;

        $HTML  .= "<div class='item-info-view'>";
        $HTML  .= "<div class='inventory-item-amount'>" . $ITEM_AMOUNT . "</div>" ;
        $HTML  .= "</div>";



        $HTML  .= "</div>";

        $this->HTML  = $HTML;
    }
    public function getInventoryItemHtml()
    {
        return $this->HTML;
    }
}