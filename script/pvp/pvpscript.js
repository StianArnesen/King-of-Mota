








const ANIMATION_SPEED_SCALE     = 1;
const ANIMATION_SPEED_SLIDER    = 55 * ANIMATION_SPEED_SCALE;

const ID_UPGRADE_BUTTON_OFFENSE = "#button-upgrade-offense";
const ID_UPGRADE_BUTTON_DEFENSE = "#button-upgrade-defense";
const ID_UPGRADE_BUTTON_LOOT    = "#button-upgrade-loot";

const ID_UPGRADE_LEVEL_OFFENSE  = "#upgrade_value_offense_level";
const ID_UPGRADE_LEVEL_DEFENSE  = "#upgrade_value_defense_level";
const ID_UPGRADE_LEVEL_LOOT     = "#upgrade_value_loot_level";


const ID_UPGRADE_PRICE_OFFENSE  = "#upgrade_value_price_offense";
const ID_UPGRADE_PRICE_DEFENSE  = "#upgrade_value_price_defense";
const ID_UPGRADE_PRICE_LOOT     = "#upgrade_value_price_loot";






$(document).ready(initPvpEvents);



function initPvpEvents()
{
    initUpgradeButtonListeners();
    initStaticEngineTabListeners();

    $(".stats-progressbar-container").each(function()
    {
        var width = $(this).find("input").val();

        $(this).width(width + '%');
    });
}

function initUpgradeButtonListeners()
{
    $(ID_UPGRADE_BUTTON_OFFENSE).click(function() {
        upgradePvpLevel("UPGRADE_LEVEL_OFFENSE");
    });
    $(ID_UPGRADE_BUTTON_DEFENSE).click(function() {
        upgradePvpLevel("UPGRADE_LEVEL_DEFENSE");
    });
    $(ID_UPGRADE_BUTTON_LOOT).click(function() {
        upgradePvpLevel("UPGRADE_LEVEL_LOOT");
    });
}

function initStaticEngineTabListeners()
{
    $('.tab-item-container').slideUp(0);

    $('.view-title').click(function(){
        var view = $(this).next(".page-view-container");

        $(view).slideToggle(ANIMATION_SPEED_SLIDER);

        playAudio('open');
    });
    $('.tab-item-title').click(function(){
        var item = $(this).find('.tab-item-container');

        var item_title_view = $(item).find('.match-item-title-view');

        var bool_is_visible = $(item).is(':visible');

        $('.tab-item-container').slideUp(ANIMATION_SPEED_SLIDER);
        $('.match-item-title-view').removeClass("tab-container-visible");
        $('.match-item-title-view').addClass("tab-container-invisible");

        if(! bool_is_visible){
            $(item_title_view).addClass("tab-container-visible");
            $(item).slideDown(ANIMATION_SPEED_SLIDER);
        }
        playAudio('open');
    });
}

function getMatchHistoryItemLayout(USERNAME_DEFENDER, MATCH_INFO)      //Return pure html of the match given in arg. Argument: array; 'attack_user_list' / match_item
{

    var match_time              = MATCH_INFO['attack_time_start'];

    var money_change            = - MATCH_INFO['winner_award_money'];

    var player_score_change     = - MATCH_INFO['score_change_winner'];

    var css_profit_extra_class  = "money-lost";
    var css_match_extra_class   = "match-lost";

    var user_id_attacker        = MATCH_INFO['user_id_attack'];
    var user_id_defender        = MATCH_INFO['user_id_defend'];

    if(MATCH_INFO['winner_user_id'] == user_id_attacker)
    {
        css_profit_extra_class  = "money-won";
        css_match_extra_class   = "match-won";
        player_score_change     = MATCH_INFO['score_change_winner'];
        money_change            = MATCH_INFO['winner_award_money'];

    }

    var HTML = "";

    var HTML_MATCH_RESULT  = "<div class='match-result-info'>";

    HTML_MATCH_RESULT +=        "<div class='match-result-profit $css_profit_extra_class'>Money: "+ money_change  +"$</div>";
    HTML_MATCH_RESULT +=        "<div class='attack-button' onclick='attackUser("+ user_id_defender +")'>Attack!</div>";
    HTML_MATCH_RESULT +=      "</div>";




    //Generate prepared HTML ready for output
    HTML    += '<div class="match-item tab-item-title '+ css_match_extra_class +'">';
    HTML    +=     '<div class="match-item-title-view tab-container-invisible">';
    HTML    +=         '<div class="match-item-title list-div-25"><div class="match-item-username">You</div> attacked <div class="match-item-username">'+ USERNAME_DEFENDER +' </div></div>';
    HTML    +=         '<div class="match-item-result-text list-div-25 '+ css_profit_extra_class  +'">'+    money_change           +' $</div>';
    HTML    +=         '<div class="match-item-result-text list-div-25 '+ css_profit_extra_class  +'">'+    player_score_change    +' p</div>';
    HTML    +=         '<div class="match-item-time">'+ match_time +'</div>';
    HTML    +=     '</div>';
    HTML    +=     '<div class="tab-item-container">'+ HTML_MATCH_RESULT +'</div>';

    HTML    += "</div>";

    return HTML;
}

function upgradePvpLevel(UPGRADE_ARG)
{
    console.warn("Requesting upgrade for offence level...");
    $.post("pvp/pvpController.php", {UPGRADE_OPTION: UPGRADE_ARG}, function(data)
    {
        var result = JSON.parse(data);

        console.warn(result['STATUS']);

        if(result['STATUS'] == "OK") {

            switch (UPGRADE_ARG)
            {
                case "UPGRADE_LEVEL_OFFENSE":
                    setUpgradeInfoOffense(result);
                    break;
                case "UPGRADE_LEVEL_DEFENSE":
                    setUpgradeInfoDefense(result);
                    break;
                case "UPGRADE_LEVEL_LOOT":
                    setUpgradeInfoLoot(result);
                    break;
            }

        }
        else {
            playAudio('open');
        }
    });
}

function setUpgradeInfoOffense(RESULT)
{
    var new_upgrade_price = RESULT['UPGRADE_PRICE'];
    var new_upgrade_level = parseInt($(ID_UPGRADE_LEVEL_OFFENSE).html()) + 1;

    var user_money = parseInt(ARRAY_USER_INFO['money']);

    console.warn("User money: " + user_money);

    $(ID_UPGRADE_PRICE_OFFENSE).html(new_upgrade_price);
    $(ID_UPGRADE_LEVEL_OFFENSE).html(new_upgrade_level);

    playAudio('buy');
}

function setUpgradeInfoDefense(RESULT)
{
    var new_upgrade_price = RESULT['UPGRADE_PRICE'];
    var new_upgrade_level = parseInt($(ID_UPGRADE_LEVEL_DEFENSE).html()) + 1;

    $(ID_UPGRADE_PRICE_DEFENSE).html(new_upgrade_price);
    $(ID_UPGRADE_LEVEL_DEFENSE).html(new_upgrade_level);

    playAudio('buy');
}
function setUpgradeInfoLoot(RESULT)
{
    var new_upgrade_price = RESULT['UPGRADE_PRICE'];
    var new_upgrade_level = parseInt($(ID_UPGRADE_LEVEL_LOOT).html()) + 1;

    $(ID_UPGRADE_PRICE_LOOT).html(new_upgrade_price);
    $(ID_UPGRADE_LEVEL_LOOT).html(new_upgrade_level);

    playAudio('buy');
}



function attackUser(TARGET_USER_ID, TARGET_USERNAME)
{
    console.warn("New attack started on user ID: " + TARGET_USER_ID);
    $.post("pvp/PvpController.php", {ATTACK_USER_ID: TARGET_USER_ID}, function(data)
    {
        var result = JSON.parse(data);

        if(result['STATUS'] == "FAILED")
        {
            console.warn(result['ERR_MSG']);
        }
        //Everything was completed without error.

        var html        = $("#match-history-list").html();

        var final_html  = getMatchHistoryItemLayout(TARGET_USERNAME, result) + html;

        $("#match-history-list").prepend(getMatchHistoryItemLayout(TARGET_USERNAME, result));


    });
}