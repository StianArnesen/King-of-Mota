<?php

    require("common/page.php");

    $PAGE = new PageClass();


?>
 <script>
          less = {
            env: "development",
            async: true,
            fileAsync: true,
            poll: 1000,
            functions: {},
            dumpLineNumbers: "comments",
            relativeUrls: false,
            rootpath: ":/a.com/"
          };
 </script>
<html>
    <head>
        <script src="https://code.createjs.com/createjs-2015.11.26.min.js" type="text/javascript"/>

        <script src="//cdnjs.cloudflare.com/ajax/libs/less.js/2.5.3/less.min.js" type="text/javascript"  ></script>

		<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
		<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
		
		<script src="script/garden/garden.js"></script>

    </head>
    <body onload="init()">
		<?echo $PAGE->getTopBanner(); ?>


		<canvas id="canvas-main" width="1200" height="900" style="margin-top: 70px">
			Content loading...
		</canvas>

    </body>
    
</html>