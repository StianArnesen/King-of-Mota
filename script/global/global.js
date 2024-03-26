
// Global constants

const _GLOBAL_DOMAIN_NAME = "/";
//const _GLOBAL_DOMAIN_NAME = "localhost";




/*
*   INPUT KEYS
*       KEY WITH KEYCODES
*
* */

const INPUT_KEY_CTRL      = 58;


/*
*   |   Cookie-name Constants   |
*
* */

const COOKIE_USER_LAST_LEVEL    = "USER_LAST_LEVEL_C";


/*
*   |    HTML id constants   |
*
* */

const DIV_GAME_OVERLAY_BG               = "#top-banner-overlay-view";
const DIV_GAME_OVERLAY_DATA             = "#top-banner-overlay-data";

const DIV_GAME_OVERLAY_UNLOCK_VIEW      = "#overlay-unlocked-items-view";
const DIV_GAME_OVERLAY_UNLOCK_LIST      = "#overlay-unlocked-items-list";

const DIV_GAME_OVERLAY_USER_LEVEL       = "#overlay-new-level";

/*
*
*   |    ---The game dialog: (With overlay to game)---  |
*   |       Used to show warnings and info to player    |
*   |                                                   |
*   |                                                   |
*
* */
const DIV_GAME_OVERLAY_MSG_DIALOG       = "#overlay-msg-dialog";
const DIV_GAME_OVERLAY_MSG_DIALOG_TITLE = "#overlay-msg-title";
const DIV_GAME_OVERLAY_MSG_DIALOG_TEXT  = "#overlay-msg-text";



const TEXT_MSG_DIALOG_TEXT_INV_FULL     = "<div class='center'>Your inventory seems to be full!</div>  You need to upgrade your inventory, or you could clear some space by selling some stuff at the <a class='msg-link' href='market.php'>Market</a> .";


/*
*   Animation speed
*
* */


const ANIMATION_SPEED_OVERLAY   = 300;



$(document).ready(globalMain);


/*
*   Initializer to the global script
*
*
* */
function globalMain()
{
    initLevelChangeListener();
    initAllButtonListeners();
}

function initLevelChangeListener()
{

}

function checkForLevelChange()
{
    var level_in_cookie     = parseInt(getCookie(COOKIE_USER_LAST_LEVEL));

    if(getCookie(COOKIE_USER_LAST_LEVEL) == "")
    {
        setCookie(COOKIE_USER_LAST_LEVEL, CURRENT_PLAYER_LEVEL);
    }
    else if(CURRENT_PLAYER_LEVEL > level_in_cookie)                         /*     LEVEL-UP     */
    {
        setCookie(COOKIE_USER_LAST_LEVEL, CURRENT_PLAYER_LEVEL);
        showListOfUnlockedItems();
    }
    else if(CURRENT_PLAYER_LEVEL < level_in_cookie)
    {
        setCookie(COOKIE_USER_LAST_LEVEL, CURRENT_PLAYER_LEVEL);
    }
}
function showListOfUnlockedItems()
{
    $(DIV_GAME_OVERLAY_USER_LEVEL).html(CURRENT_PLAYER_LEVEL);

    $.post("items/PublicItems.php", {GET_UNLOCKED_ITEMS: 1}, function(data)
    {
        var RESULT = JSON.parse(data);

        var HTML_FINAL = "";

        for(var i = 0; i < RESULT.length; i++)
        {
            var UNLOCKED_ITEM = RESULT[i];

            HTML_FINAL += getUnlockedItemSingleLayout(UNLOCKED_ITEM);
        }

        $(DIV_GAME_OVERLAY_UNLOCK_LIST).html(HTML_FINAL);
        $(DIV_GAME_OVERLAY_UNLOCK_VIEW).show();
        showGameOverlay();
    });
}
/*
*   Function that returns the HTML for each unlocked item.
* */

function getUnlockedItemSingleLayout(ITEM)
{
    var name    = ITEM['NAME'];
    var image   = ITEM['IMG'];

    var HTML = "";

    HTML += "<div class='unlocked-item'>";
    HTML += "   <div class='unlocked-item-image-view'>";
    HTML += "   <div class='unlocked-item-name'>";
    HTML +=         name;
    HTML += "   </div>";
    HTML += "       <img class='unlocked-item-image' src='"+ image +"'>";
    HTML += "   </div>";
    HTML += "</div>";

    return HTML;
}
function initAllButtonListeners()
{
    console.log("Button initializers started");

    $('.friend_request_action_button').click(function(){

        var obj = $(this);
        var UID = $(obj).val();

        acceptFriendRequestFromUser(UID);
    });
}
function sendFriendRequest(button){
    var user_id = $(button).val();

    console.log("Sending friend request...");
    
    $.post("user/PublicUser.php", {add_friend: user_id}, function(data)
    {
        console.log("Friend request result: " + data);
    });
}
function acceptFriendRequestFromUser(UID)
{
    $.post("user/PublicUser.php", {accept_friend_user_id: UID}, function(data)
    {
        var result = JSON.parse(data);

        if(result['STATUS'] == "FAILED")
        {
            console.log("Accept failed!");
        }

    });
}

function showGameOverlay()
{
    $(DIV_GAME_OVERLAY_BG).fadeIn(ANIMATION_SPEED_OVERLAY);
    $(DIV_GAME_OVERLAY_DATA).fadeIn(ANIMATION_SPEED_OVERLAY);
}


function hideGameOverlay()
{
    $(DIV_GAME_OVERLAY_BG).fadeOut(ANIMATION_SPEED_OVERLAY);
    $(DIV_GAME_OVERLAY_DATA).fadeOut(ANIMATION_SPEED_OVERLAY);
}
function showGameDialogByServerResponse(SERVER_RESPONSE)
{
    switch (SERVER_RESPONSE)
    {
        case "SPACE_FULL":
            $(DIV_GAME_OVERLAY_UNLOCK_VIEW).hide();
            $(DIV_GAME_OVERLAY_MSG_DIALOG).show();

            $(DIV_GAME_OVERLAY_MSG_DIALOG_TITLE).html("Inventory full");
            $(DIV_GAME_OVERLAY_MSG_DIALOG_TEXT).html(TEXT_MSG_DIALOG_TEXT_INV_FULL);

            showGameOverlay();
            break;
        default:
            $(DIV_GAME_OVERLAY_UNLOCK_VIEW).hide();
            $(DIV_GAME_OVERLAY_MSG_DIALOG).show();

            $(DIV_GAME_OVERLAY_MSG_DIALOG_TITLE).html("SERVER ERROR");
            $(DIV_GAME_OVERLAY_MSG_DIALOG_TEXT).html(SERVER_RESPONSE);
            showGameOverlay();
            break;
    }
}




// COOKIE CONTROLS

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
    }
    return "";
}


function setCookie(cname, cvalue) {

    var exdays = 1555555;

    var d = new Date();
    d.setTime(d.getTime() + (exdays*60*60*60000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}



function clearCookie(cname){
    document.cookie = cname + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC";
}



/*  String prototypes   */

// String excerpt

String.prototype.getExcerpt = function (str_data, limit){
    return (str_data.length > limit)? str_data.substring(0, limit) + "...": str_data;
};

function getExcerpt(str_data, limit){
    return (str_data.length > limit)? str_data.substring(0, limit) + "...": str_data;
}












