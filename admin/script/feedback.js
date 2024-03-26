
var CURRENT_POST_ID;


//Item list view.
const DIV_ITEM_LIST     = "#items-list";
const DIV_ITEM_ID       = "#item-id";
const DIV_ITEM_TITLE    = "#item-name";
const DIV_ITEM_IMAGE    = "#item-image-preview";
const DIV_ITEM_DATA     = "#item-data";
const DIV_ITEM_STATUS   = "#item-status-select";

var CACHE_ITEMS_ARRAY = [];


$(document).ready(feedbackInit);



function feedbackInit()
{
    loadFeedbackList(15);
    initStatusChangeListener();
}
function initStatusChangeListener()
{
    $(DIV_ITEM_STATUS).change(function () 
    {
        var val = $(this).val();
        updateStatusColor(val);
       
    });
}

function updateStatusColor(val){
    
    
    $(DIV_ITEM_STATUS).removeClass("bg-red");
    $(DIV_ITEM_STATUS).removeClass("bg-orange");
    $(DIV_ITEM_STATUS).removeClass("bg-green");

    switch (val)
    {
        case "0":
            if(! $(DIV_ITEM_STATUS).hasClass("bg-red")){
                $(DIV_ITEM_STATUS).addClass("bg-red");
            }
            break;
        case "1":
            if(! $(DIV_ITEM_STATUS).hasClass("bg-orange")){
                $(DIV_ITEM_STATUS).addClass("bg-orange");
            }
            break;
        case "2":
            if(! $(DIV_ITEM_STATUS).hasClass("bg-green")){
                $(DIV_ITEM_STATUS).addClass("bg-green");
            }
            break;
    }
}

function loadFeedbackList(limit) {
    $.post("utils/FeedbackController.php", {get_feedback_list: limit}, function (data) {
        var result = JSON.parse(data);

        var feedbackListHtml = "";

        for (var i = 0; i < result.length; i++) {
            var postItem = result[i];
            feedbackListHtml += getFeedbackItemAndSaveSingle(postItem);
        }
        $(DIV_ITEM_LIST).html(feedbackListHtml);
        initDbItemSelectorListener();
    });
}

function getFeedbackItemAndSaveSingle(post)
{
    var post_id         = post['id'];
    var post_user_id    = post['user_id'];
    var post_title      = post['title'];
    var post_data       = post['data'];
    var post_time       = post['post_time'];
    var post_status     = post['status'];
    var post_category   = post['post_category'];
    var post_category_s = getIssueCategoryAsString(post_category);
    
    var username        = post['username'];
    var image           = post['user_image'];

    /*     Update/add to the cached list     */
    CACHE_ITEMS_ARRAY[post_id]  =   post;
    
    var tmp_str = image;
    
    image = "//kingofmota.com/" + tmp_str;
    CACHE_ITEMS_ARRAY[post_id]['user_image'] = image;


    var HTML = "";

    HTML += "<div class='db-row-item'>";
    HTML +=     "<input type='hidden' value='"+ post_id +"' class='db-item-id'>";
    HTML +=     "<div class='db-row-item-title'>"+ post_title +"</div>";
    HTML +=     "<div class='db-row-item-category'>"+ post_category_s +"</div>";
    
    HTML +=     "<img class='db-row-item-image global_item-image' src='"+ image +"'>";
    
    HTML +=     "<div class='db-row-item-data'>"+ getExcerpt(post_data,25) +"</div>";
    HTML +=     "<div class='db-row-item-username'>" + username +"</div>";
    HTML += "</div>";

    return HTML;
}



function getIssueCategoryAsString(category){
    switch (category)
    {
        case 0:
            return "Bug";
        case 1:
            return "Gameplay | General";
        case 2:
            return "Idea";
        case 3:
            return "User interface | UX";
        case 4:
            return "Security";
        case 5:
            return "Misc";
        default:
            return "UNKNOWN CATEGORY";
    }
}
function updatePostStatus(id, status){
    
    CACHE_ITEMS_ARRAY[id]['status'] = $(DIV_ITEM_STATUS).val();
    
    $.post("utils/FeedbackController.php", {set_single_status: 1, issue_id: id, issue_status: status}, function(data){
        var result = JSON.parse(data);
        console.log(result);
    });
}


function initDbItemSelectorListener()
{
    $(DIV_ITEM_STATUS).change(function(){
        
        var status = $(this).val();
        updatePostStatus(CURRENT_POST_ID, status);
    });
    
    $(".db-row-item").click(function()
    {
        // Find and get item info (item_id, item_type, item_active, Etc...)
        var post_id         = $(this).find(".db-item-id").val();

        if(CURRENT_POST_ID == post_id) { return; }
        else { CURRENT_POST_ID = post_id; }

        var ITEM = CACHE_ITEMS_ARRAY[post_id];

        var item_type       = ITEM['post_category'];
        var item_title      = ITEM['title'];
        var item_picture    = ITEM['user_image'];
        var item_data       = ITEM['data'];
        var item_status     = ITEM['status'];
        
        $(DIV_ITEM_ID).html(post_id);
        
        var category_selector   = '#item-type-select option[value='+item_type+']';
        var status_selector     = '#item-status-select option[value='+item_status+']';

        $(status_selector).prop('selected', true);
        
        if( $(category_selector).length > 0)
        {
            $(category_selector).prop('selected', true)
        }
        else
        {
            $('#item-type-select option:eq(-1)').prop('selected', true)
        }
                
        //Update title and user image
        $(DIV_ITEM_TITLE).val(item_title);
        $(DIV_ITEM_IMAGE).attr("src", item_picture);
        $(DIV_ITEM_DATA).val(unicodeToChar(item_data));
        updateStatusColor(item_status);

        $(".db-row-item").removeClass("selected");

        if(! $(this).hasClass("selected") )
        {
            $(this).addClass("selected");
        }
    });
}

function unicodeToChar(text) {
    return text.replace(/\\u[\dA-F]{4}/gi, 
           function (match) {
                return String.fromCharCode(parseInt(match.replace(/\\u/g, ''), 16));
           });
 }