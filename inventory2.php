<?php

require("common/page.php");

$page = new PageClass();

?>

<html>
	<head>
		<link href="style/inventory/style2.less" rel="stylesheet/less">
	</head>
	<body>
		<?echo $page->getTopBanner();?>


		<div id="main">
			<div id="inventory-view">
				<div class="page-view-title">
					<span>Inventory: </span>
				</div>

				<div class="inventory-items-grid">
					<div class="inventory-item">
						<div class="inventory-item-title">
							<span>OG Kush</span>
						</div>
						<div class="inventory-item-image">
							<img src="img/weed/weed_leaf_1.png">
						</div>
					</div>

					<div class="inventory-item">
						<div class="inventory-item-title">
							<span>OG Kush</span>
						</div>
						<div class="inventory-item-image">
							<img src="img/weed/weed_leaf_1.png">
						</div>
					</div>

				</div>

			</div>
		</div>

	</body>
</html>