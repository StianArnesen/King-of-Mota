<?php

$ROOT   = $_SERVER['DOCUMENT_ROOT'];

require_once($ROOT . 'utils/se_utils.php');

class InventoryLabItem
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
    public function __construct($LAB_ITEM)
    {
        $ID         = $LAB_ITEM['id'];
        $INV_ID     = $LAB_ITEM['inv_id'];
        $NAME       = $LAB_ITEM['name'];
        $IMG        = $LAB_ITEM['img'];
        $AMOUNT     = $LAB_ITEM['amount'];
        $QUALITY    = $LAB_ITEM['quality'];

        $HTML   = "<div class='inventory-item'> <input type='hidden' name='item-id-value' value='". $ID ."'> <input type='hidden' name='inv-id-value' value='". $INV_ID ."'> ";
        $HTML  .= "<div class='inventory-item-title'> <span>" . StaticUtils::getExcerpt($NAME, 15) . "</span></div>";


        $HTML  .= "<div class='inventory-item-img-container'> <img class='inventory-item-img' src='" . $IMG . "'> </div>" ;

        $HTML  .= "<div class='item-info-view'>";
        $HTML  .= "<div class='inventory-item-amount'>" . $AMOUNT . "</div>" ;
        $HTML .= "<div class='inv_item_quality'>" . number_format((float)$QUALITY, 2, '.', '') . "%</div>" ;
        $HTML  .= "</div>";



        $HTML  .= "</div>";

        $this->HTML  = $HTML;
    }
    public function getInventoryItemHtml()
    {
        return $this->HTML;
    }
}