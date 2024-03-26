<html>
	<head>
	    <link href="style/shop/style.css" rel="stylesheet" type="text/css">

	    <title>King of Mota - Shop</title>
	</head>
	
	<body>
	    <?php
	            $btn_file = fopen("layout/top_banner/top_banner.php", "r") or die("Unable to open file!");
	                echo fread($btn_file,filesize("layout/top_banner/top_banner.php"));
	                fclose($btn_file);
	        ?>
	</body>
</html>