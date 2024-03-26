<?php

require ("common/session/sessioninfo.php");
require ("common/page.php"); // PageClass -> The page utils used to get top banner and header info.


//Load the page-utils (From PageClass)
$PAGE = new PageClass();

$user = new User();

if(! $user->isLoggedIn())
{
    echo "You need to login first!";
}

?>


<html>
    <head>
        <title>King of Mota | Feedback</title>

        <link href="style/feedback/style.less" rel="stylesheet" type="text/less">
        
        <?php echo $PAGE->getHeaderInfo(); ?>

        <script src="script/feedback/feedback.js" type="text/javascript"> </script>
            
    </head>

    <body>
        <?php echo $PAGE->getTopBanner(); ?>

        <div class="main">
            <div class="page-title ">King of Mota | Feedback</div>
            
            <div class="page-desc">
                <div class="title">Feedback: </div>
                <span>Here you can submit feedback for King of Mota.</span>
                <span>I will appreciate any feedback at all, and nothing is either too little or too much .</span>
                <span></span>
                <span style="font-weight: 600;">Your feedback will only be visible to me (Stian Arnesen) <br> Your username will be sent with your feedback.</span>
                
            </div>

            <form class="form" id="feedback-form" action="feedback.php" method="post" >

                <div class="form-subview">
                    <div class="form-input-title">Title: </div>
                    <input class="form-input" id="val-title" placeholder="Title" type="text">
                </div>

                <div class="form-subview">
                    <div class="form-input-title">Category: </div>

                    <select class="form-input" id="val-category">
                        <option value="0">Bugs / Glitches</option>
                        <option value="1">Gameplay / General feedback</option>
                        <option value="2">Ideas</option>
                        <option value="3">User interface / User experience</option>
                        <option value="4">Security</option>
                        <option value="5">Misc / Other</option>
                    </select>
                </div>
                <div class="form-subview">
                    <div class="form-input-title">Feedback: </div>
                    <textarea class="form-input" id="val-data" placeholder="Your feedback"></textarea>
                </div>
                
            </form>
            <button id='submit-feedback' onclick="insertNewFeedback()" class="fb-button">Send feedback!</button>

        </div>

    </body>
</html>
