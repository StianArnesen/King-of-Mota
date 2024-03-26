<?php

require("common/page.php");
require("lab/PublicLab.php");

$PAGE = new PageClass();

$LAB = new PublicLab();

$LAB_INFO = $LAB->getLabInfo();

$USER_HAS_LAB = true;

if(! isset($LAB_INFO) || $LAB_INFO == null )
{
    $LOCKED_HTML_FILE 	= fopen("layout/lab_locked/lab_locked.html", "r") or die("Unable to open file!");
    $LOCKED_HTML 		= fread($LOCKED_HTML_FILE,filesize("layout/lab_locked/lab_locked.html"));
    
    fclose($LOCKED_HTML_FILE);
	$USER_HAS_LAB = false;
}

$LAB_LEVEL 		= isset($LAB_INFO['lab_level'])?$LAB_INFO['lab_level'] : 0;
$QUALITY_LEVEL 	= isset($LAB_INFO['lab_quality_level'])? $LAB_INFO['lab_quality_level'] : 0;

$LAB_IMAGE = $LAB->getLabImage($LAB_LEVEL);

$LAB_UPGRADE_PRICE = $LAB->getLabLevelUpgradePrice();


?>

<script>
    less = {
        env: "development",
        async: true,
        fileAsync: false,
        poll: 1000,
        functions: {},
        dumpLineNumbers: "comments",
        relativeUrls: false,
        rootpath: ":/a.com/"
    };
</script>
<html>
	<head>

		<title>King of Mota - Lab</title>

	    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
	    
	    <script src="//cdnjs.cloudflare.com/ajax/libs/less.js/2.5.3/less.min.js" type="text/javascript"  ></script>

	    <script src="script/lab/lab_core.js" type="text/javascript"></script>
	    <script src="script/lab/lab.js" type="text/javascript"></script>

		<link href="style/lab/lab.less" rel="stylesheet/less">

		<?php echo $PAGE->getHeaderInfo();  ?>

	</head>
	<body>
		<?php echo $PAGE->getTopBanner();?>


		<?php
			if(! $USER_HAS_LAB)
			{
				die($LOCKED_HTML);
			}

		?>

		<div class="main">
			<div id="lab-view">
				<div class="lab-title">
					<span><?php echo $LAB_INFO['lab_title'] . " Level: " . $LAB_LEVEL;?></span>
				</div>

				<div class="lab-image-view">
					<img id="lab-image" src="img/lab/lab/lab_01.png">
				</div>
				<div class="upgrade-view">
					<div id="upgrade-lab-level-button" class="upgrade-button">
						<div class="upgrade-item-image">
							<img src="img/lab/icon/space_empty.png">
						</div>
						<span>Lab level:</span>
						<div id="lab-info-level" class="lab-upgrade-level"><?php echo $LAB_LEVEL ?></div>
						<div id="lab-info-level-price" class="upgrade-price"></div>
					</div>
					<div id="upgrade-lab-quality-button" class="upgrade-button">


						<span>Quality level:</span>
						<div id="lab-info-quality-level" class="lab-upgrade-level"><?php echo $QUALITY_LEVEL ?></div>
						<div id="lab-info-quality-level-price" class="upgrade-price"></div>
					</div>
				</div>
			</div>
			<div class="lab-space-view">
				<div class="lab-space-title">
					Cooking space: 
				</div>
				<div id="lab-space-items-list"></div>

			</div>
		</div>



		




		<div id="overlay-view"> </div>
		<div id="overlay-data" class="overlay-data">
			


				<div id="item-info-overlay">

					<div class="info-container">

						<div class="product-info-name">
							<div id="p_info_name">Drug name </div>
						</div>
						
						<div class="product-info-image">
							<img id="p_info_image" src="#">
						</div>

						<div class="product-info-bar-view">

							<div class="info-bar-title">Power:</div>
							<div class="product-info-text" id="p_info_power"></div>
							
							<div class="product-infobar">
								<div id="p_info_power_bar" class="info-bar-inner"></div>
							</div>

							<div class="info-bar-title">Production time:</div>
							<div class="product-info-text" id="p_info_time"></div>
							
							<div  class="product-infobar">
								<div id="p_info_time_bar" class="info-bar-inner"></div>
							</div>
							<div id="btn-produce-item"><span>Produce</span></div>
						</div>

						<div class="product-info-bar-view">
							
						</div>

						<div class="product-info-description">
							<div class="product-info-div-title">Description: </div>
							<div id="p_info_description" class="product-description"></div>
						</div>

						<div class="product-info-ingredients">
							<div class="product-info-div-title">Ingredients</div>
							<div id="p_info_ingredients_list">
								
							<div class="product-ingredient-item">
								<div class="product-ingredient-item-title">Car battery</div>
								<div class="product-ingredient-item-amount">1/5</div>
							</div>


							</div>
						</div>
							
					</div>


					</div>
					<div class='lab-space-setup-dialog-title'> <span> Lab setup </span> </div>
					<div id="lab-product-list"></div>
			</div>
		</div>
	</body>
</html>