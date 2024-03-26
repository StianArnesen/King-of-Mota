<?php


require "../common/page.php";
require_once "utils/LabEditorController.php";

$PAGE                   = new PageClass();
$LAB_EDIT_CONTROLLER    = new LabEditorController();
?>


<html>
    <head>
        <link href="style/labeditor.css" type="text/css" rel="stylesheet">
            <?php echo $PAGE->getHeaderInfo(); ?>
            <script src="script/editor/labeditor.js" type="text/javascript"></script>

            <title>King of Mota | Lab Editor</title>
        </head>
    <body>
    <div class="top-banner">
        <div class="page-title">King of Mota | Editor</div>
    </div>

    <div class="page-view">
        <div class="item-list-view explorer-view" id="item-list-view">
            <div class="explorer-view-title">Lab Products:</div>

            <div class="explorer-view-content" id="items-list">

            </div>

            <div class="button-add" onclick="prepareNewLabItem()">
                <img class="button-image global_item-image" src="src/icons/plus.svg">
            </div>

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

                <div class="ingredients-view form-input-view">
                    <div class="form-input-view-title">Ingredients: </div>
                    <div class="form-input-view-content">

                        <div id="ingredient-items">
                            <div class="ingredients-item">
                                <div class="ingredients-item-title">Painkillers</div>

                                <div class="ingredients-item-content">
                                    <img class="ingredients-item-image" src="http://kingofmota.com/img/pills/Painkillers.png">
                                    <div class="ingredients-item-amount">x12</div>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>

                <div class="item-properties-view form-input-view">

                    <div class="form-input-view-title">Properties: </div>
                    <div class="form-input-view-content">
                        <div class="form-input-view-sub-content">
                            <div class="form-input-view-sub-title">Category: </div>
                            <select id="item-type-select" name="item-type">
                                <option value="0">0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="7">4</option>
                                <option value="10">5</option>
                                <option value="-1" >Misc / Unknown</option>
                            </select>
                        </div>


                        <div class="form-input-view-sub-content">
                            <div class="form-input-view-sub-title">Production time: </div>
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
