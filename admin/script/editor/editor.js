

var CURRENT_ITEM_ID;


//Item list view.
const DIV_ITEM_LIST             = "#items-list";

//Selected Item info-view
const DIV_ITEM_PRICE            = "#item-price";
const DIV_ITEM_G_PRICE          = "#item-g-price";
const DIV_ITEM_DESC             = "#item-description";
const DIV_ITEM_POWER            = "#item-power";
const DIV_ITEM_ID               = "#item-id";

const DIV_ITEM_GROW_TIME_CLEAN  = "#grow-time-final";
const DIV_ITEM_INFO_A           = "#item-info-a";
const DIV_ITEM_LEVEL            = "#item-min-level";


const DIV_ITEM_ACTIVE           = "#item-active-option";


// Revision overlay divs
const REV_ITEM_LIST_DIV         = "#rev-item-list";


//Div [no-input]
const DIV_ITEM_PRICE_FORMATTED  = "#item-price-formatted";


//Array cache with items[all info]
var CACHE_ITEMS_ARRAY = [];


var queryMax    = 5;
var queryOffset = 0;

$(document).ready(mainInit);

function mainInit()
{
    loadDefaults();
    requestAndLoadItemsFromDb(0);
    console.warn("StaticGameEditor loaded!");
}

function loadDefaults()
{
    queryMax       = 225;
    queryOffset    = 0;

    $("#item-list-view").resizable();
}

function requestAndLoadItemsFromDb(item_type)
{
    $.post("utils/itemEditorController.php", {get_item_list: 1, query_max: queryMax, query_offset: queryOffset, filter_type: item_type}, function(data)
    {
        var result      = JSON.parse(data);
        var cur_html    = "";
        var new_html    = "";
        /*
        *       Get item description
        **/

        for(var i = 0; i < result.length; i++)
        {
            var DB_ITEM = result[i];

            var item_id = DB_ITEM['id'];

            $.post("utils/itemEditorController.php", {get_item_desc: item_id}, function(data_desc) {
                DB_ITEM['desc'] = data_desc;
            });

            new_html += getItemLayoutAndSaveSingle(DB_ITEM);
        }

        $(DIV_ITEM_LIST).html(cur_html + new_html);

        initDbItemSelectorListener();

    });
}

function getItemLayoutAndSaveSingle(DB_ITEM)
{

    var item_price      = DB_ITEM['pris'];
    var item_desc       = DB_ITEM['desc'];
    var item_g_price    = DB_ITEM['g_price'];
    var item_type       = DB_ITEM['type'];
    var item_sub_type   = DB_ITEM['sub_type'];
    var item_picture    = _GLOBAL_DOMAIN_NAME + DB_ITEM['picture'];
    var item_id         = DB_ITEM['id'];
    var item_name       = DB_ITEM['name'];
    var item_discount   = DB_ITEM['discount'];  
    var item_power      = DB_ITEM['item_power'];
    var item_grow_time  = DB_ITEM['grow_time'];
    var item_min_level  = DB_ITEM['min_level'];
    var item_info_a     = DB_ITEM['item_info_a'];
    var item_shop_order = DB_ITEM['shop_order'];
    var item_active     = DB_ITEM['item_active'];

    CACHE_ITEMS_ARRAY[item_id] = DB_ITEM;


    CACHE_ITEMS_ARRAY[item_id]['picture'] = item_picture;


    var HTML = "";

    HTML += "<div class='db-row-item'>";
    HTML +=     "<input type='hidden' value='"+ item_id +"' class='db-item-id'>";
    HTML +=     "<div class='db-row-item-title'>"+ item_name +"</div>";
    HTML +=     "<img class='db-row-item-image global_item-image' src='"+ item_picture +"'>";
    HTML += "</div>";

    return HTML;

}

function testSaveCurrentItem()
{
    var array_result = {};

    /*
    var item_id         = CURRENT_ITEM_ID;
    var item_price      = $(DIV_ITEM_PRICE).val();;

    var item_type       = $('#item-type-select').val();
    var item_sub_type   = $('#item-sub-type-select').val();

    var item_power      = $(DIV_ITEM_POWER).val();
    var item_grow_time  = $(DIV_ITEM_GROW_TIME_CLEAN).val();
    var item_info_a     = $(DIV_ITEM_INFO_A).val();

    var item_name       = $("#item-name").val();
    var item_level      = $(DIV_ITEM_LEVEL).val();

    var item_active     = $(DIV_ITEM_ACTIVE).val();*/

    array_result['item_id']             = CURRENT_ITEM_ID;
    array_result['item_price']          = $(DIV_ITEM_PRICE).val();
    array_result['item_g_price']        = $(DIV_ITEM_G_PRICE).val();
    array_result['item_description']    = $(DIV_ITEM_DESC).val();

    array_result['item_type']           = $('#item-type-select').val();
    array_result['item_sub_type']       = $('#item-sub-type-select').val();

    array_result['item_power']          = $(DIV_ITEM_POWER).val();
    array_result['item_grow_time']      = $(DIV_ITEM_GROW_TIME_CLEAN).val();
    array_result['item_info_a']         = $(DIV_ITEM_INFO_A).val();

    array_result['item_name']           = $("#item-name").val();
    array_result['item_level']          = $(DIV_ITEM_LEVEL).val();

    array_result['item_active']         = $(DIV_ITEM_ACTIVE).val();

    var array_result_str                = JSON.stringify(array_result);

    var confirm_update                  = confirm(array_result_str);

    
    if(confirm_update)
    {
        $.post("utils/itemEditorController.php", {update_item_array: array_result_str}, function(json_data){
            var array_data = JSON.parse(json_data);
            var SQL_MSG = array_data['SQL_MSG'];

            confirm("Request sent! \n Result: " + array_data['STATUS'] + " \n SQL Error: " + SQL_MSG);
        });

    }

}




function initDbItemSelectorListener()
{
    $(".db-row-item").click(function()
    {

        // Find and get item info (item_id, item_type, item_active, Etc...)
        var item_id         = $(this).find(".db-item-id").val();

        if(CURRENT_ITEM_ID == item_id)
            return;
           
        CURRENT_ITEM_ID = item_id;

        var ITEM = CACHE_ITEMS_ARRAY[item_id];


        var item_type       = ITEM['type'];
        var item_sub_type   = parseInt(ITEM['sub_type']);

        var item_name       = ITEM['name'];
        var item_desc       = ITEM['desc'];
        var item_picture    = ITEM['picture'];

        var item_price      = parseInt(ITEM['pris']);
        var item_g_price    = parseInt(ITEM['g_price']);
        var item_power      = parseInt(ITEM['item_power']);
        var item_grow_time  = parseInt(ITEM['grow_time']);
        var item_min_level  = parseInt(ITEM['min_level']);

        var item_info_a     = parseInt(ITEM['item_info_a']);

        var item_active     = parseInt(ITEM['item_active']);



        console.warn("Loading item with item ID: [" + item_id + "] Item type: " + item_type);


        //Update Item ID - view
        $(DIV_ITEM_ID).html(item_id);


        // Select category if(true){category valid and found.}
        // else if(false){invalid category / not found}
        if($('#item-type-select option[value='+item_type+']').length > 0)
        {
            $('#item-type-select option[value='+item_type+']').prop('selected', true)
        }
        else
        {
            $('#item-type-select option:eq(-1)').prop('selected', true)
        }

        // Select Subtype [growable / not growable]
        $('#item-sub-type-select option[value='+item_sub_type+']').prop('selected', true)

        //Update price view.

        $(DIV_ITEM_PRICE).val(item_price);
        $(DIV_ITEM_G_PRICE).val(item_g_price);
        $(DIV_ITEM_PRICE_FORMATTED).html(item_price.formatMoney(0, '.', ',') + " $");
        $(DIV_ITEM_DESC).val(item_desc);

        //Update the Item Power view
        $(DIV_ITEM_POWER).val(item_power);
        $(DIV_ITEM_GROW_TIME_CLEAN).val(item_grow_time);



        //Update name view
        $("#item-name").val(item_name);
        //Update image preview
        $("#item-image-preview").attr("src", item_picture);


        $(DIV_ITEM_POWER).val(item_power);
        $(DIV_ITEM_INFO_A).val(item_info_a);
        $(DIV_ITEM_LEVEL).val(item_min_level);
        $(DIV_ITEM_ACTIVE).val(item_active);



        $(".db-row-item").removeClass("selected");

        if(! $(this).hasClass("selected") )
        {
            $(this).addClass("selected");
        }
    });
}


function showCurrentItemRevisionList()
{
    $.post("utils/public.php", {GET_REV_LIST: CURRENT_ITEM_ID}, function(data)
    {
        var RESULT      = JSON.parse(data);

        var HTML_FINAL  = "";

        for(var i = 0; i < RESULT.length; i++)
        {
            rev_item    = RESULT[i];
            HTML_FINAL += getRevisionItemLayout(rev_item);
        }

        if(RESULT.length == 0)
        {
            HTML_FINAL = "<div class='rev-message'>No revisions found for this item.</div>";
        }

        var ITEM = CACHE_ITEMS_ARRAY[CURRENT_ITEM_ID];

        $("#rev-item-name").html(ITEM['name']);
        $(REV_ITEM_LIST_DIV).html(HTML_FINAL);8

        showOverlay();
    });

}


function updateItemList(selector)
{
    var type = $(selector).val();
    requestAndLoadItemsFromDb(type);

    console.log("Item type changed!");
}


function getRevisionItemLayout(REV_ITEM)
{
    var C_ITEM = CACHE_ITEMS_ARRAY[CURRENT_ITEM_ID];

    var NAME            = REV_ITEM['name'];
    var IMAGE           = C_ITEM['picture'];

    var POWER           = REV_ITEM['item_power'];
    var GROW_TIME       = REV_ITEM['grow_time'];
    var EXP_GAIN        = REV_ITEM['item_info_a'];

    var PRICE           = REV_ITEM['pris'];
    var MIN_LEVEL       = REV_ITEM['min_level'];
    var ITEM_ACTIVE     = REV_ITEM['item_active'];



    var UNIX_TIMESTAMP  = REV_ITEM['rev_timestamp'];
    var DATE_TIMESTAMP  = new Date(UNIX_TIMESTAMP*1000);

    /*                  Convert unix-timestamp to date                              */

    var REV_YEAR        = (DATE_TIMESTAMP.getUTCFullYear());
    var REV_MONTH       = (DATE_TIMESTAMP.getUTCMonth() + 1);
    var REV_DAY         = (DATE_TIMESTAMP.getUTCDate());


    /*                  Calculate the what the plant gives you an hour              */


    var HOUR_MONEY      = POWER     / (GROW_TIME/60/60); // Convert the grow time from seconds to hours.
        HOUR_MONEY      = Math.round(HOUR_MONEY * 100) / 100;

    var HOUR_EXP        = EXP_GAIN  / (GROW_TIME/60/60); // Convert the grow time from seconds to hours.
        HOUR_EXP        = Math.round(HOUR_EXP * 100) / 100;


    /*                  The username of the editor                                  */

    var REV_USERNAME    = REV_ITEM['rev_username'];


    var HTML;

    HTML =  '<div class="rev-item">';
    HTML +=     '<div class="rev-item-title">' + NAME +'</div>';
    HTML +=     '<div class="rev-item-container">';

    HTML += '<div class="rev-item-inline-container">';
    HTML +=     '<img class="rev-item-picture global_item-image" src="'+ IMAGE +'">';
    HTML += '</div>';

    HTML += '<div class="rev-item-inline-container">';

    HTML +=     '<div class="rev-stat">Power:';
    HTML +=         '<div class="rev-stat-var">'+ POWER +' $</div>'
    HTML +=     '</div>';

    HTML +=     '<div class="rev-stat">EXP Gain:';
    HTML +=         '<div class="rev-stat-var">'+ EXP_GAIN +'</div>'
    HTML +=     '</div>';

    HTML +=     '<div class="rev-stat">Grow time:';
    HTML +=         '<div class="rev-stat-var">'+ GROW_TIME +' s</div>'
    HTML +=     '</div>';

    HTML += '</div>';

    HTML += '<div class="rev-item-inline-container">';

    HTML +=     '<div class="rev-stat">Price: ';
    HTML +=         '<div class="rev-stat-var">'+ PRICE +' $</div>'
    HTML +=     '</div>';

    HTML +=     '<div class="rev-stat">Level unlock:';
    HTML +=         '<div class="rev-stat-var">'+ MIN_LEVEL +'</div>'
    HTML +=     '</div>';

    HTML +=     '<div class="rev-stat">Item active:';
    HTML +=         '<div class="rev-stat-var">'+ ITEM_ACTIVE +'</div>'
    HTML +=     '</div>';

    HTML += '</div>';


    HTML += '<div class="rev-item-inline-container">';

    HTML +=     '<div class="rev-stat">Money /hr: ';
    HTML +=         '<div class="rev-stat-var">'+ HOUR_MONEY +' $</div>'
    HTML +=     '</div>';

    HTML +=     '<div class="rev-stat">EXP /hr:';
    HTML +=         '<div class="rev-stat-var">'+ HOUR_EXP +'</div>'
    HTML +=     '</div>';

    HTML += '</div>';


    HTML += '<div class="rev-item-time">'+ DATE_TIMESTAMP.getUTCDate() + "." + REV_MONTH + "." + DATE_TIMESTAMP.getUTCFullYear() +' </div>';

    HTML += '<div class="rev-item-username">'+ REV_USERNAME +'  --> @</div>';




    HTML +=     '</div>';

    HTML += '</div>';


    return HTML;



}












const OVERLAY_ANIM_SPEED = 150;


function showOverlay()
{
    $("#overlay-view").fadeIn(OVERLAY_ANIM_SPEED);
    $("#overlay-data").fadeIn(OVERLAY_ANIM_SPEED);

}
function hideOverlay()
{
    $("#overlay-view").fadeOut(OVERLAY_ANIM_SPEED);
    $("#overlay-data").fadeOut(OVERLAY_ANIM_SPEED);

}

















