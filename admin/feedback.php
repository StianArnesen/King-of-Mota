<?php


require "../common/page.php";
require "utils/FeedbackController.php";

$PAGE                   = new PageClass();
$feedbackController     = new FeedbackController();
?>


<html>
<head>
    <link href="style/feedback.css" type="text/css" rel="stylesheet">
    <?php echo $PAGE->getHeaderInfo(); ?>
   <script src="script/feedback.js" type="text/javascript"></script>

    <title>King of Mota | Feedback</title>
</head>
<body>
<div class="top-banner">
    <div class="page-title">King of Mota | Feedback</div>
</div>

<div class="page-view">
    <div class="item-list-view explorer-view" id="item-list-view">
        <div class="explorer-view-title">Feedback issues: </div>

        <div class="explorer-view-content" id="items-list">

        </div>
        
    </div>

    <div class="main-view-pre">
        <div class="main-view">
            <div class="main-form">

                <div class="form-input-view item-name-view">
                    <div class="form-input-view-title">Feedback issue</div>
                    <div class="form-input-view-content">
                        Title:
                        <input type="text" class="form-input item-name" id="item-name"  name="item-name" placeholder="Feedback issue title">
                    </div>
                    <div class="form-input " id="item-id">Issue ID</div>



                </div>

                <div class="user-info-view form-input-view">
                    <div class="form-input-view-title">User info: </div>
                    <div class="form-input-view-content">
                        <img id="item-image-preview" class="global_item-image" src="/img/icon/weed.png">
                    </div>
                </div>
                

                <div class="item-properties-view form-input-view">

                    <div class="form-input-view-title">Feedback: </div>
                    <div class="form-input-view-content">
                        <div class="form-input-view-sub-content">
                            <div class="form-input-view-sub-title">Category: </div>
                            <select id="item-type-select" name="item-type">
                                <option value="0">Bugs</option>
                                <option value="1">General</option>
                                <option value="2">Ideas</option>
                                <option value="3">UI & UX</option>
                                <option value="4">Security</option>
                                <option value="5">Misc</option>
                                <option value="-1" >Unknown</option>
                            </select>
                        </div>
                        
                        <div class="form-input-view-sub-content">
                            <div class="form-input-view-sub-title">Status</div>
                            <select id="item-status-select" class="bg-red" name="item-status">
                                <option value="0">Sent</option>
                                <option value="1">Seen</option>
                                <option value="2">Solved</option>
                            </select>
                        </div>

                        <div class="form-input-view-sub-content">
                            <div class="form-input-view-sub-title">Feedback data</div>
                            <textarea id="item-data" class=""></textarea>
                        </div>


                    </div>
                </div>


            </div>
        </div>
    </div>

</div>
</body>
</html>
