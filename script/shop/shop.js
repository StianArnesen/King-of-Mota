



/*          SHOP NAVIGATION BUTTONS            */

var NAV_KUSH    = "#nav-btn-kush";
var NAV_SHROOM  = "#nav-btn-mushrooms";
var NAV_ALL     = "#nav-btn-all";
var NAV_LAB     = "#nav-btn-lab";

var SHOP_PRODUCT_VIEW = "#shop_items";



/*          SHOP ITEMS CACHE STORAGE                 */

var SHOP_CACHE =[];


$(document).ready(function()
{
    updateActiveNavigationElement();
    initNavigationListener();

});


function initNavigationListener()
{

    $(".shop-navigation-item").click(function()
    {
        $(".shop-navigation-item").removeClass("nav-active");

        $(this).addClass("nav-active");
        $(SHOP_PRODUCT_VIEW).animate("slide", { direction: "left" }, 300);

        var type = parseInt($(this).find("input[name='nav-category']").val());

        if(SHOP_CACHE[type])
        {
            $(SHOP_PRODUCT_VIEW).html(SHOP_CACHE[type]);
            setTimeout(function(){
                $(SHOP_PRODUCT_VIEW).show("slide", { direction: "right" }, 300);
            },300);
        }
        else
        {
            $.get("shop/PublicShop.php", {get_product_list: 1, show_only_item_type: type, searchVal: ""}, function(data)
            {
                SHOP_CACHE[type] = data;
                $(SHOP_PRODUCT_VIEW).html(data);
                setTimeout(function(){
                    $(SHOP_PRODUCT_VIEW).show("slide", { direction: "right" }, 300);
                },300);
            });
        }

    });
}

function showCategoryView(category)
{
    switch (category)
    {
        case 7:
            $(".shop-navigation-item").removeClass("nav-active");
            $(NAV_SHROOM).addClass("nav-active");
            break;
    }
    if(SHOP_CACHE[category])
    {
        $(SHOP_PRODUCT_VIEW).html(SHOP_CACHE[category]);
    }
    else
    {
        $.get("shop/PublicShop.php", {get_product_list: 1, show_only_item_type: category, searchVal: ""}, function(data)
        {
            SHOP_CACHE[category] = data;
            $(SHOP_PRODUCT_VIEW).html(data);
        });
    }
}


function updateActiveNavigationElement()
{
    var activeCategory = getQueryVariable("category");

    if(activeCategory)
    {
        if(activeCategory == 0)
        {
            $(NAV_KUSH).addClass("nav-active");
        }
        else if(activeCategory == 7)
        {
            $(NAV_SHROOM).addClass("nav-active");
        }
        else if(activeCategory == 10)
        {
            $(NAV_LAB).addClass("nav-active");
        }
        else if(activeCategory == -1)
        {
            $(NAV_ALL).addClass("nav-active");
        }
    }
}

















function getQueryVariable(variable)
{
    var query = window.location.search.substring(1);
    var vars = query.split("&");
    for (var i=0;i<vars.length;i++) {
        var pair = vars[i].split("=");
        if(pair[0] == variable){return pair[1];}
    }
    return(false);
}



