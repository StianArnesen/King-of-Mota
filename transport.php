<?php

	include("common/page.php");
	include("transport/layout.php");

	$PAGE = new PageClass();

	$TRANSPORT = new TransportLayout();

	echo $PAGE->getTopBanner();

	$TRANSPORT->getTransportView();

?>




