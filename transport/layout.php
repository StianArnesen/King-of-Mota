<?php

include("utils.php");

class TransportLayout
{
	private $utils;

	public function __construct()
	{
		$this->utils = new TransportUtils();
	}

	public function getTransportView()
	{
		echo '<link href="transport/style.css" rel="stylesheet">

		<div class="transport_view main-page">
			<div class="transport-view-title"><span>Transport</span></div>';
			echo $this->utils->getTransportList(5);

		echo '</div>';
	}

}
