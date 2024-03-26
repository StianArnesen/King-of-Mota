<?php

require_once ("../common/session/sessioninfo.php");
require_once ("../common/page.php");


$USER           = new User(-1, -1);
$pageUtils      = new PageClass();


if(! $USER->isUserAdmin())
{
    $USERNAME = $USER->getUsername();
    die("Access denied for user: <strong> $USERNAME </strong>  <br> <br> <strong>[Higher Access level required ]</strong>");
}

?>


<html>
    <head>
        <link href="style/newitem.css" rel="stylesheet" type="text/css">
        <?php echo $pageUtils->getHeaderInfo(); // Needed for jquery ?>

        <script src="script/newitem.js"></script>


    </head>
    <body>

        <div class="main-view">
            <form action="utils/NewItem.php" method="post" enctype="multipart/form-data" name="add-item" class="main-form">

                <div class="form-input-view item-name-view">
                    <div class="form-input-view-title">Name: </div>
                    <div class="form-input-view-content">
                        Name:
                        <input type="text" class="form-input item-name"  name="item-name" placeholder="item name">
                    </div>
                </div>

                <div class="item-image-view form-input-view">
                    <div class="form-input-view-title">Image: </div>
                    <div class="form-input-view-content">
                        <img id="item-image-preview" src="/img/icon/weed.png">
                        <input type="file" id="item-image-upload-file" accept=".png" name="crewImageFile">
                    </div>
                </div>

                <div class="item-description-view form-input-view">
                    <div class="form-input-view-title">Description: </div>
                    <div class="form-input-view-content">
                        <textarea  class="form-input item-desc" name="item-description" >Item description</textarea>
                    </div>
                </div>

                <div class="item-properties-view form-input-view">

                    <div class="form-input-view-title">Properties: </div>
                    <div class="form-input-view-content">
                        <div class="form-input-view-sub-content">
                            <div class="form-input-view-sub-title">Category: </div>
                            <select name="item-type">
                                <option value="0">Kush</option>
                                <option value="7">Mushroom</option>
                                <option value="10">Lab ingredient</option>
                            </select>
                        </div>

                        <div class="form-input-view-sub-content">
                            <div class="form-input-view-sub-title">Sub Type (growable / not growable): </div>
                            <select name="item-sub-type">
                                <option value="0">Growable</option>
                                <option value="1">Not growable</option>
                            </select>
                        </div>

                        <div class="form-input-view-sub-content">
                            <div class="form-input-view-sub-title" min="0">Price: </div>
                            <input type="number" name="item-price" value="500"> $
                            <div class="form-input-view-sub-title">G-Coin Price: </div>
                            <input type="number" name="item-g-price" min="-1" value="-1"> G
                        </div>


                        <div class="form-input-view-sub-content">
                            <div class="form-input-view-sub-title">Item power: </div>
                            <input type="number" name="item-power" value="500">
                        </div>
                        <div class="form-input-view-sub-content">
                            <div class="form-input-view-sub-title">Item info variable (item_info_a): </div>
                            EXP Gain: 
                            <input type="number" name="item-info-a" min="1" value="1">
                        </div>
                        <div class="form-input-view-sub-content">
                            <div class="form-input-view-sub-title">Item grow time: </div>
                            Hrs:
                            <input type="number" id="grow-time-hrs" class="time-input" min="0" max="24" value="0" >
                            Min:
                            <input type="number" id="grow-time-min" class="time-input" min="0" max="60" value="0" >
                            Sec:
                            <input type="number" id="grow-time-sec" class="time-input" min="0" max="60" value="0" >
                            <br>
                            <br>
                            Total seconds
                            <input type="number" name="item-grow-time" id="grow-time-final" value="0">
                        </div>
                        <div class="form-input-view-sub-content">
                            <div class="form-input-view-sub-title">Shop Item?: </div>
                            Item active:
                            <select name="item-active">
                                <option value="0">Not Active</option>
                                <option value="1">Active</option>
                            </select>
                        </div>
                        <div class="form-input-view-sub-content">
                            <div class="form-input-view-sub-title">Item unlock level (minimum level): </div>
                            Level:
                            <input type="number" name="item-min-level" min="1" value="1">
                        </div>

                    </div>
                </div>


                <div class="item-submit-view form-input-view">
                    <div class="form-input-view-title">Add item: </div>
                    <div class="form-input-view-content">
                        <input class="form-submit" name="item-add-submit" type="submit" value="Add item">
                    </div>
                </div>


            </form>
        </div>

    </body>
</html>
