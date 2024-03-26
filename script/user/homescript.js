
//Control variables
var homeMenuShowing = false;

//Constants

const DIV_OVERLAY_VIEW  = "#overlay-view";
const DIV_OVERLAY_DATA  = "#overlay-data";

const DIV_DIALOG_LIST   = "#dialog-list";


//Run the initializer when document is ready.
$(document).ready(initUserScript);


//The initializer
function initUserScript()
{
    initScrollEventListener();
    homeMenuShowing = true;
}


function getUpVoteList(ID, TYPE)
{
    console.log("Getting username's of upvote list");

    $.post("feed.php", {get_upvote_list_id: ID,get_upvote_list_type: TYPE}, function(data)
    {
        USERNAME_LIST = JSON.parse(data);

        var HTML = "";

        for(var i = 0; i < USERNAME_LIST.length; i++)
        {
            var item        = USERNAME_LIST[i];

            var USERNAME    = item[0];
            var IMAGE       = item[1];

            HTML += getDialogUserLayout(USERNAME, IMAGE);
        }

        $(DIV_DIALOG_LIST).html(HTML);

        console.log(USERNAME_LIST);
        showOverlay();
    });
}

function initScrollEventListener()
{
    var profile_view        = $("#profile-view");
    var profile_username    = $("#profile-view-username");
    var profile_image       = $("#profile-view-image");
    var profile_content     = $("#profile-view-content");
    var award_list          = $("#profile-view-award-list");
    
    var top_banner_profile  = $(".profile-view-container");
    
    var viewHidden          = false;


    $(window).scroll(function(e){
        var $el = $(profile_username);
        var isPositionFixed = ($el.css('position') == 'fixed');
        var isPositionFixed_award = ($(profile_username).css('position') == 'fixed');

        if ($(this).scrollTop() > (250 - 75) && !isPositionFixed){
            $(profile_username).css({'position': 'fixed', 'top': '75px'});
            $(profile_image).css({'position': 'fixed', 'top': '125px'});
            
            if(! viewHidden){
                $(top_banner_profile).fadeOut(300);
                viewHidden = true;
            }

            //$(profile_username).css({'width': '20%'});

        }
        if ($(this).scrollTop() < (250 - 75) && isPositionFixed)
        {
            //$(profile_username).css({'width': '100%'});
            $(profile_image).css({'position': 'absolute', 'top': '300px'});
            $(profile_username).css({'position': 'absolute', 'top': '250px'});

            if(viewHidden){
                $(top_banner_profile).fadeIn(300);
                viewHidden = false;
            }
            
        }

    });
}

function getDialogUserLayout(USERNAME, IMAGE)
{
    var HTML = "";

    HTML += '<div class="dialog-list-item">';
    HTML +=     '<div class="dialog-list-item-username" >';
    HTML +=         '<a href="/' + USERNAME + '">' + USERNAME + '</a>';
    HTML +=     '</div>';
    HTML +=     '<div class="dialog-list-item-image">';
    HTML +=         '<img src="' + IMAGE + '">';
    HTML +=     '</div>';
    HTML += '</div>';

    return HTML;
}

function slideDown(commentSection)
{
    $(commentSection).slideToggle(100);
}

function showOverlay()
{
    $(DIV_OVERLAY_VIEW).fadeIn(300);
    $(DIV_OVERLAY_DATA).fadeIn(300);

    $("#overlay-view").click(function() {
        hideOverlay();
    });
}
function hideOverlay()
{
    $(DIV_OVERLAY_VIEW).fadeOut(300);
    $(DIV_OVERLAY_DATA).fadeOut(300);
}