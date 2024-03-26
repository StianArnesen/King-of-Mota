<?php

    require("common/page.php");

    $PAGE = new PageClass();


?>
<html>
    <head>
        

		<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
		<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>

        <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.3.5/angular.min.js"></script>

        <link href="style/garden/garden.less" rel="stylesheet">

    </head>
    <body ng-app="garden-app">
		<?echo $PAGE->getTopBanner(); ?>

        
        
    </body>
    
</html>