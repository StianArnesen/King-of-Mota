<?php

include("crewdir/crew.php");
include("crewpanel/panel.php");
include("common/page.php");

$PAGE = new PageClass();
$CREW = new Crew();
$CREW_PANEL = new CrewPanel(2);

?>
<html>
    <head>
        <link href="style/crewpanel/style.css" rel="stylesheet">
    </head>
    <body>
        <?php echo $PAGE->getTopBanner();

        if($CREW->verifyCrewLeader())
        {
            echo $CREW_PANEL->getCrewPanel();
        }
        else
        {
            echo "You do not have access to this!";
        }
        ?>
    </body>
</html>