<?php
/**
 * Created by Stian Arnesen.
 * User: Stiarn
 * Date: 14.10.2017
 * Time: 18:09
 */

require "common/page.php";

$page = new PageClass();

?>


<html>
	<head>
		<link href="style/jobs/jobs.less" type="text/less" rel="stylesheet">
		<?php echo $page->getHeaderInfo();?>
		<title>King of Mota | Jobs</title>
		
	</head>
	<body>
		<?php echo $page->getTopBanner();?>
		
		<div class="main">
			<div class="page-title">
				World Map
			</div>
			<div class="main-view">
				<img src="img/map/map-v2.png" id="map-image">
			</div>
		</div>
	
	</body>
</html>
