


/*------------------------------------
				GLOBAL USER INFO 	
------------------------------------*/

var CURRENT_PLAYER_MONEY	= 0;
var CURRENT_PLAYER_LEVEL	= 0;

var CURRENT_PLAYER_G_COINS 	= 0;


/*------------------------------------
 				GLOBAL CONTROL 	
 ------------------------------------*/

var SOUND_MUTED;


/*------------------------------------
				USER INFO ARRAY	
------------------------------------*/

var ARRAY_USER_INFO;

var ARRAY_USER_SETTINGS;

/*------------------------------------
				CONTROL TMP VARS	
------------------------------------*/

var LAST_CUR_EXP 			= "";
var LAST_TARGET_EXP 		= "";

var LAST_EXP_BAR_WIDTH		= "";

/*------------------------------------
				COOKIES	
------------------------------------*/


// User info: 
const _cookie_money 		= "user_money_html";
const _cookie_level 		= "user_level_html";
const _cookie_username 		= "user_profile_username_html";

const _cookie_user_image 	= "user_profile_image_html";

const _cookie_exp_cur 		= "user_exp_current_int";
const _cookie_exp_target 	= "user_exp_target_int";
const _cookie_exp_view 		= "user_exp_view_html";

// User preference

const _cookie_pref_userinfo = "pref_show_user_info";		//	- Toggle user-info-view visibility.

const _cookie_sound_mute	= "game-sound-mute";

/*------------------------------------
				HTML ID'S
------------------------------------*/

const moneyDiv 			= "#profile-money";
const coinsDiv 			= "#profile-g-coins";
const levelDiv 			= "#profile-level";
const usernameDiv 		= "#profile-username";

const profileImageDiv 	= "#profile-image";

const expBarDiv 		= "#profile-exp-bar";
const expBarInner 		= "#profile-exp-bar-inner";


const expCurrentDiv    	= "#profile-cur-exp";
const expTargetDiv 		= "#profile-target-exp";

const inventoryDiv 		= "#top-banner-inventory-space";

/*  Mute button ID  */
const DIV_MUTE_BUTTON	= "#global-mute-button";



/*				!DOCUMENT-READY!					*/


$(document).ready(topBannerInit);


function topBannerInit()
{
    loadSoundMuteEvent();
    updateUserInfoArray();

    $(document).mouseenter(function()
    {
        updateUserInfoArray();
    });

    loadProfileInfo();
    updateUserPreference();
	
}
function updateUserPreference(){
    var userInfoViewVisible = getCookie(_cookie_pref_userinfo);
	
	if(userInfoViewVisible == "false"){
        hideProfileInfo(0);
	}
}


function loadUsername(){
	var USERNAME = ARRAY_USER_INFO['username'];

	$(usernameDiv).html(USERNAME);
}

function loadProfileImage(){

	var image = ARRAY_USER_INFO['img'];

	var txt = "<img src='" + image + "'>";

	$(profileImageDiv).html(txt);
}
function loadLevel()
{
	var LEVEL = ARRAY_USER_INFO['level'];
			
	$(levelDiv).html(LEVEL);

	CURRENT_PLAYER_LEVEL = parseInt(LEVEL);
	
}
function loadCoinsDiv() {
	var G_COINS = parseInt(ARRAY_USER_INFO['g_coins']);
	var txt 	=  (G_COINS.formatMoney(0, '.', ','))+ " G";

	$(coinsDiv).html(txt);

	CURRENT_PLAYER_G_COINS = ARRAY_USER_INFO['g_coins'];

}
function loadMoney(){
	var MONEY 	= parseInt(ARRAY_USER_INFO['money']);
	var txt 	=  (MONEY.formatMoney(0, '.', ','));
	
    if($(moneyDiv).html() == "" || $(moneyDiv).html() == txt){
        $(moneyDiv).html(txt);
    }
    else
	{
        var comma_separator_number_step = $.animateNumber.numberStepFactories.separator(',');

        $(moneyDiv).animateNumber(
            {
                number: MONEY,
                numberStep: comma_separator_number_step
            }
        );
	}
    
	

	CURRENT_PLAYER_MONEY = ARRAY_USER_INFO['money'];
}
function loadExpDiv(){

    console.info("Loading exp bar");

    var cookieTxt 			= getCookie(_cookie_exp_cur);

    var cookieCurrentExp 	= getCookie(_cookie_exp_cur);
    var cookieTargetExp 	= getCookie(_cookie_exp_target);
	
    var width = ((cookieCurrentExp/cookieTargetExp)*100) + "%";


    if( (cookieTargetExp != LAST_TARGET_EXP && cookieTargetExp != "") && cookieTxt != "" || (cookieCurrentExp == LAST_CUR_EXP && cookieTxt != "undefined" && cookieCurrentExp != null && cookieCurrentExp != "") )
    {
        LAST_TARGET_EXP  	= cookieTargetExp;
        LAST_CUR_EXP  		= cookieCurrentExp;
        console.log("loadExpDiv(): Cookie found and contains data! [" + LAST_CUR_EXP + "]");

        if(LAST_EXP_BAR_WIDTH != width)
        {
            LAST_EXP_BAR_WIDTH = width;

            $(expBarInner).css("background-color", "rgba(100,170,100,1)");
            if($(expBarInner).hasClass('progressbar-exp-animation'))
            {
                $(expBarInner).removeClass('progressbar-exp-animation');
            }
            $(expBarInner).css("width", width);


            setTimeout(function()
            {
                $(expBarInner).css("background-color", "#39a25f");
                if(! $(expBarInner).hasClass('progressbar-exp-animation'))
                {
                    $(expBarInner).addClass('progressbar-exp-animation');
                }


            }, 255);
        }
    }
    else
    {
        console.warn("loadExpDiv(): Cookie is not set correctly! Updating cookie!");
        var currentExp 	= ARRAY_USER_INFO['exp_cur'];
        var targetExp 	= ARRAY_USER_INFO['exp_target'];

        $(expCurrentDiv).html("EXP: " + currentExp + " / ");

        width = ((currentExp/targetExp)*100) + "%";

        LAST_TARGET_EXP  	= targetExp;
        LAST_CUR_EXP  		= currentExp;


        if(LAST_EXP_BAR_WIDTH != width)
        {
            LAST_EXP_BAR_WIDTH = width;

            $(expBarInner).css("background-color", "rgba(100,170,100,1)");
            if($(expBarInner).hasClass('progressbar-exp-animation'))
            {
                $(expBarInner).removeClass('progressbar-exp-animation');
            }
            $(expBarInner).css("width", width);


            setTimeout(function()
            {
                $(expBarInner).css("background-color", "#39a25f");
                if(! $(expBarInner).hasClass('progressbar-exp-animation'))
                {
                    $(expBarInner).addClass('progressbar-exp-animation');
                }


            }, 255);
        }


        $(expTargetDiv).html("" + targetExp);

        var txt = $(expBarInner).html();
        setCookie(_cookie_exp_target, targetExp);
        setCookie(_cookie_exp_view, txt);
    }
	//$(expCurrentDiv).html(cookieTxt);
}
function loadInventorySpaceDiv()
{
	$.post("storage/PUBLIC_STORAGE_INFO.php", {backpack: 1}, function(data){
			
		var result = JSON.parse(data);

		var used = result['used'];
		var max = result['cap'];

		$(inventoryDiv).html(used + " / " + max);
		if(used < (max/2))
		{
			$(inventoryDiv).css('color',  "lightgreen");	
		}
		else if(used < (max))
		{
			$(inventoryDiv).css('color',  "orange");
		} 
		else
		{
			$(inventoryDiv).css('color',  "red");
			$(inventoryDiv).css('transform',  "scale(1.05)");
			$(inventoryDiv).html(used + " / " + max + " [FULL]");

			setTimeout(function() {
				$(inventoryDiv).css('transform',  "scale(1.0)");
			}, 1150);

		}
		
	});
}

function addPlayerMoney(amount){
	var curMoney = getCookie(_cookie_money);

	if(curMoney != ""){
		curMoney += amount;
		setCookie(_cookie_money, curMoney);
		loadMoney();
	}
}

function addExp(amount){

	var cookieCurrentExp = getCookie(_cookie_exp_cur);
	//var cookieTargetExp = getCookie(_cookie_exp_target);

	if(cookieCurrentExp != "")
	{
		cookieCurrentExp += amount;
		setCookie(_cookie_exp_cur, cookieCurrentExp);
		loadExpDiv();
	}

	loadExpDiv();

	loadLevel();
}
function updateUserInfoArray()
{
	$.get("userinfo/userinfo.php", {get_info: "info_array"}, function(result)
	{
		ARRAY_USER_INFO = JSON.parse(result);
		
		loadProfileImage();
		loadUsername();
		loadMoney();
		loadLevel();
		loadExpDiv();
		loadInventorySpaceDiv();
		loadCoinsDiv();

		checkForLevelChange();
	});
}
function updateUserInfo()
{
	updateUserInfoArray();
}

function loadProfileInfo()
{
	updateUserInfoArray();
}

/*
* Hide/show profile info (.profile-view-info-section) 
* */

var profileViewVisible = (getCookie(_cookie_pref_userinfo) == "")? true: getCookie(_cookie_pref_userinfo);

function hideProfileInfo(){
	$('.profile-view-info-section').slideUp(300);
    $('.profile-view-image-section').slideUp(300);
    profileViewVisible = false;
    
    setCookie(_cookie_pref_userinfo, profileViewVisible);
}
function hideProfileInfo(speed){
    $('.profile-view-info-section').slideUp(speed);
    $('.profile-view-image-section').slideUp(speed);
    profileViewVisible = false;

    setCookie(_cookie_pref_userinfo, profileViewVisible);
}

function showProfileInfo(){
    $('.profile-view-info-section').slideDown(300);
    $('.profile-view-image-section').slideDown(300);
    profileViewVisible = true;

    setCookie(_cookie_pref_userinfo, profileViewVisible);
}

function toggleProfileView(){
	if(profileViewVisible){
		hideProfileInfo();
		return;
	}
	showProfileInfo();
	
}
function loadSoundMuteEvent()
{
	// Add mute-button click listener.
	$(DIV_MUTE_BUTTON).click(function(){
		setSoundMute(! SOUND_MUTED);
	});

	//Check if the cookie variable is set.

	var cookie_mute = getCookie(_cookie_sound_mute);

	if(cookie_mute != "")
	{
		if(cookie_mute == '1')
		{
			SOUND_MUTED = true;
		}
		else
		{
			SOUND_MUTED = false;
		}

	}
	else
	{
		setCookie(_cookie_sound_mute, '0');
	}

	// Set the mute button image based on mute-boolean.
	$(DIV_MUTE_BUTTON).each(function()
	{
        setSoundMute(SOUND_MUTED);
	});
}

function setSoundMute(mute)
{
	SOUND_MUTED = mute;
	
	console.log("SOUND_MUTE: " + SOUND_MUTED);
	
    if(SOUND_MUTED)
    {
        setCookie(_cookie_sound_mute, '1');
        $("#global-mute-button").css('background', 'url("'+ _GLOBAL_DOMAIN_NAME +'img/icon/menu/sound/sound_off.svg")');
    }
    else
    {
        setCookie(_cookie_sound_mute, '0');
        $("#global-mute-button").css('background', 'url("'+ _GLOBAL_DOMAIN_NAME +'img/icon/menu/sound/sound_on.svg")');
    }
}

Number.prototype.formatMoney = function(c, d, t){
var n = this, 
    c = isNaN(c = Math.abs(c)) ? 2 : c, 
    d = d == undefined ? "." : d, 
    t = t == undefined ? "," : t, 
    s = n < 0 ? "-" : "", 
    i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", 
    j = (j = i.length) > 3 ? j % 3 : 0;
   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
 };