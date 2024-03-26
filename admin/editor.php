<?php


require_once $_SERVER['DOCUMENT_ROOT'] . "common/page.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "admin/utils/ItemEditorController.php";

$PAGE               = new PageClass();
$DatabaseController = new DatabaseEditor();
?>


<html>
    <head>
        <?php echo $PAGE->getHeaderInfo(); ?>
        <link href="style/editor.css" rel="stylesheet" type="text/css">
        <script src="script/editor/editor.js" type="text/javascript"></script>

        <title>King of Mota | Editor</title>
    </head>

    <body>



    <!--    |---------------------------|
            |         OVERLAY           |
            |            |              |-->
    <div id="overlay-view" onclick="hideOverlay()" ></div>
    <div id="overlay-data">
        <div class="overlay-title">Revisions for <div id="rev-item-name"></div></div>
        <div id="rev-item-list">

            <!--       REV-ITEM-EXAMPLE-START

            <div class="rev-item">
                <div class="rev-item-title">The albanian</div>
                <div class="rev-item-container">
                    <div class="rev-item-inline-container">
                        <img class="rev-item-picture global_item-image" src="http://kingofmota.com/img/weed/weed_leaf_8.png">
                    </div>
                    <div class="rev-item-inline-container">
                        <div class="rev-stat">Power:
                            <div class="rev-stat-var">11</div>
                        </div>
                        <div class="rev-stat">Grow time:
                            <div class="rev-stat-var">35s</div>
                        </div>
                        <div class="rev-stat">EXP Gain:
                            <div class="rev-stat-var">3</div>
                        </div>
                    </div>
                    <div class="rev-item-time">3 days ago</div>

                </div>
            </div>

                   REV-ITEM-EXAMPLE-END   -->


        </div>
    </div>

    <!--    |---------------------------|
            |         Top banner        |
            |            |              |-->
    <div class="top-banner">
        <div class="page-title">King of Mota | Editor</div>
    </div>

    <div class="page-view">
        <div class="item-list-view explorer-view" id="item-list-view">
            <div class="explorer-view-title">Items:</div>
            <div class="explorer-view-filters">
                <div class="filter-select">
                    <div class="filter-select-title">Type: </div>
                    <select id="filter-type" onchange="updateItemList(this)">
                        <option value="0">Kush</option>
                        <option value="1">Vehicle</option>
                        <option value="2">Weapon</option>
                        <option value="3">Light</option>
                        <option value="7">Mushroom</option>
                        <option value="10">Lab ingredient</option>
                        <option value="-1" >All</option>
                    </select>
                </div>
            </div>
        
            <div class="explorer-view-content" id="items-list"></div>

        </div>
        <div class="main-view-pre">
            <div class="main-view">
                <div class="main-form">

                    <div class="form-input-view item-name-view">
                        <div class="form-input-view-title">Name: </div>
                        <div class="form-input-view-content">
                            Name:
                            <input type="text" class="form-input item-name" id="item-name"  name="item-name" placeholder="item name">
                        </div>
                        <div class="form-input " id="item-id">ID</div>



                    </div>

                    <div class="item-image-view form-input-view">
                        <div class="form-input-view-title">Image: </div>
                        <div class="form-input-view-content">
                            <img id="item-image-preview" class="global_item-image" src="/img/icon/weed.png">
                            <input type="file" id="item-image-upload-file" accept=".png" name="crewImageFile">
                        </div>
                    </div>

                    <div class="item-description-view form-input-view">
                        <div class="form-input-view-title">Description: </div>
                        <div class="form-input-view-content">
                            <textarea  class="form-input item-desc" id="item-description" name="item-description" >Item description</textarea>
                        </div>
                    </div>

                    <div class="item-properties-view form-input-view">

                        <div class="form-input-view-title">Properties: </div>
                        <div class="form-input-view-content">
                            <div class="form-input-view-sub-content">
                                <div class="form-input-view-sub-title">Category: </div>
                                <select id="item-type-select" name="item-type">
                                    <option value="0">Kush</option>
                                    <option value="1">Vehicle</option>
                                    <option value="2">Weapon</option>
                                    <option value="3">Light</option>
                                    <option value="7">Mushroom</option>
                                    <option value="10">Lab ingredient</option>
                                    <option value="-1" >Misc / Unknown</option>
                                </select>
                            </div>

                            <div class="form-input-view-sub-content">
                                <div class="form-input-view-sub-title">Sub Type (growable / not growable): </div>
                                <select id="item-sub-type-select" name="item-sub-type">
                                    <option value="0">Growable</option>
                                    <option value="1">Not growable</option>
                                </select>
                            </div>
                            <div class="form-input-view-sub-content">
                                <div class="form-input-view-sub-title">Price: </div>
                                <input type="number" name="item-price" id="item-price" value="500"> $
                                <div id="item-price-formatted" class="green" >500$</div>
                                <div class="form-input-view-sub-title">G-Coin price: </div>
                                <input type="number" name="item-g-price" id="item-g-price" value="-1"> G
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
                                <div class="form-input-view-sub-title">Item power: </div>
                                <input type="number" name="item-power" id="item-power" value="500">
                            </div>
                            <div class="form-input-view-sub-content">
                                <div class="form-input-view-sub-title">item_info_a (Exp Gain): </div>
                                EXP Gain:
                                <input type="number" name="item-info-a" id="item-info-a" min="1" value="1">
                            </div>

                            <div class="form-input-view-sub-content">
                                <div class="form-input-view-sub-title">Item unlock level (minimum level): </div>
                                Level:
                                <input type="number" name="item-min-level" id="item-min-level" min="1" value="1">
                            </div>

                            <div class="form-input-view-sub-content">
                                <div class="form-input-view-sub-title">Item visible in shop?: </div>
                                Item active:
                                <select name="item-active" id="item-active-option">
                                    <option value="0">Not Active</option>
                                    <option value="1">Active</option>
                                </select>
                            </div>


                        </div>
                    </div>


                    <div class="item-submit-view form-input-view">
                        <div class="form-input-view-title">Save changes</div>
                        <div class="form-input-view-content">
                            <input class="form-submit" onclick="testSaveCurrentItem()" type="submit" value="Save changes">
                            <div class="form-submit" onclick="showCurrentItemRevisionList()">Browse revisions</div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
    </body>
</html>
