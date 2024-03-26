<?php

class smallItem
{

	private $LAYOUT;

	public function __construct($ID, $TITLE, $IMG, $PRICE, $OLD_PRICE, $G_PRICE, $DESC, $TYPE)
	{
		$R = "";

		$R .= '<div class="rec-item">'; 
			if(isset($_GET['IT']))
			{
                    $S_IT = $_GET['IT'];
            }
            else
            {
            	$S_IT = 0;
            }
			$R .= '<div class="rec-item-title"> <a href="/item.php?item_id='. $ID .'&IT=' .$S_IT .'"><span>'. $TITLE .'</span> </a></div>';
			$R .= '<div class="rec-item-image"> <a href="/item.php?item_id='. $ID .'&IT=' .$S_IT .'"> <img class="global_item-image" src="'. $IMG .'"></a></div>';
			$R .= '<div class="rec-item-desc">'. $DESC .'</div>';
			if($OLD_PRICE != $PRICE)
			{
				$R .= '<div class="rec-item-price item_price_label old" style="text-decoration: line-through; color: darkgrey;">'. $OLD_PRICE .'$</div>';	
			}
			if($PRICE == 0){
				$R .= '<div class="rec-item-price item_price_label">FREE</div>';
			}
            else if($PRICE == -1){
				$R .= '<img src="http://kingofmota.com/img/icon/g_coin.jpg" class="green-coin-icon"><div class="rec-item-price item_price_label">'. $G_PRICE .'</div>';
			}
			else
			{
				$R .= '<div class="rec-item-price item_price_label">'. $PRICE .'$</div>';
			}
			

		$R .= ' </div>';

		$this->LAYOUT = $R;
	}
	public function getItem()
	{
		return $this->LAYOUT;
	}
}

?>