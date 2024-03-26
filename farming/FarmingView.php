<?php
/**
 * Created by PhpStorm.
 * User: StianDesktop
 * Date: 2016-01-30
 * Time: 01:01
 */
include("FarmingController.php");

$farmingController = new FarmingController();


$UPGRADE_LEVELS = $farmingController->getCurrentUpgradeItemsInfoArray();
$UPGRADE_LEVEL_PRICE_LIST = $farmingController->getUpgradeItemsPriceList();

if(isset($_GET['farm_info'])){

    $farm = array(
        "title" => $farmingController->getCurrentFarmName(),
        
        "farm_image" => $farmingController->getCurrentFarmImage(),
        "farm_level" => $UPGRADE_LEVELS['farm_id'],
        "farm_price" => $UPGRADE_LEVEL_PRICE_LIST['farm_upgrade_price'],
        
        "light_level" => $UPGRADE_LEVELS['light_level'],
        "light_price" => $UPGRADE_LEVEL_PRICE_LIST['light_upgrade_price'],
        
        "air_level" => $UPGRADE_LEVELS['air_level'],
        "air_price" => $UPGRADE_LEVEL_PRICE_LIST['air_upgrade_price'],
        
        "soil_level" => $UPGRADE_LEVELS['soil_level'],
        "soil_price" => $UPGRADE_LEVEL_PRICE_LIST['soil_upgrade_price']
       );

    die(json_encode($farm, JSON_PRETTY_PRINT));
}



























/*'
        <div class="growing-view-farm-title">'. $farmingController->getCurrentFarmName() .'</div>

<div class="farm-image-wrapper">
    <img id="farm-image" src="'. $farmingController->getCurrentFarmImage().'">
</div>

<div class="farm-upgrade-view">
    <div class="farm-upgrade-view-title">
        <span>Upgrades</span>
    </div>

    <div class="farm-upgrade-view-level">
        <div class="farm-upgrade-button" id="air_upgrade_button">Farm level: ' . ($UPGRADE_LEVELS['farm_id']) .'
            <div class="farm-upgrade-button-desc">
                Add more space for your plants to grow!
            </div>
            
            <div class="farm-upgrade-button-price" id="air-upgrade-price">
                $ '. $UPGRADE_LEVEL_PRICE_LIST[0] .'
            </div>
        </div>

    </div>
    <div class="farm-upgrade-view-level">
        <div class="farm-upgrade-button" id="light_upgrade_button">Light level: '. ($UPGRADE_LEVELS['light_level']).'
            <div class="farm-upgrade-button-desc">
                Increase growing speed!
            </div>

            <div class="farm-upgrade-button-price" id="light-upgrade-price">
                $ '. $UPGRADE_LEVEL_PRICE_LIST[1] .'
            </div>

        </div>


        

    </div>
    <div class="farm-upgrade-view-level">
        <div class="farm-upgrade-button" id="air_upgrade_button">Air vent level: ' . ($UPGRADE_LEVELS['air_level']) .'
            <div class="farm-upgrade-button-desc">
                Increase the quality of your weed!
            </div>

            <div class="farm-upgrade-button-price" id="air-upgrade-price">
                $ '. $UPGRADE_LEVEL_PRICE_LIST[2] .'
            </div>

        </div>

        

    </div>
    <div class="farm-upgrade-view-level">
        <div class="farm-upgrade-button" id="soil_upgrade_button">Soil level: ' . ($UPGRADE_LEVELS['soil_level']) .'
            <div class="farm-upgrade-button-desc">
                Increase the EXP earned when harvesting a plant!
            </div>

            <div class="farm-upgrade-button-price" id="air-upgrade-price">
                $'. $UPGRADE_LEVEL_PRICE_LIST[3] .'
            </div>


        </div>

        
    </div>
';*/



?>




