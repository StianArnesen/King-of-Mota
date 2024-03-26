$(document).ready(main);

/* Constant HTML id's */

const CUSTOMER_LIST = "#customer-list";


/*
 *   function expandSection(obj: sender) ->
 *       - Function must be run manually with the sender as the 1 parameter.
 *
 *       param: object - // expandSection(this);
 *
 * */

function expandSection(obj){
    var $parent = $(obj).parent();
    $(obj).parent().find('.customer-sales').slideToggle(0);
    if(! $parent.hasClass("selected-item")){
        $parent.addClass("selected-item");
    }
    else {
        $parent.removeClass("selected-item");
    }
}

function insertNewOrder(customer_id, product_id, product_amount, total_price){

    var order       = {customer_id: customer_id, product_id: product_id, product_amount: product_amount, total_price: total_price};

    var json_order  = JSON.stringify(order);

}

function main() {

    //$("#customer-table").colResizable({liveDrag:true});

    $("#customer-table-drag").colResizable({
        liveDrag:true,
        gripInnerHtml:"<div class='grip'></div>",
        draggingClass:"dragging" });


    /*$("#nonFixedSample").colResizable({
        fixed:false,
        liveDrag:true,
        gripInnerHtml:"<div class='grip2'></div>",
        draggingClass:"dragging"
    });
    */
}