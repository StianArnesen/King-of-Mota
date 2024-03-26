var selectedItems = new Array();

function closeOverlay(){
    $("#overlay-view").fadeOut(100);
    $("#overlay-data").fadeOut(100);
}

var canPlaySound = true;

$('.inventory_item').mouseenter(function()
{


});
$(document).ready(function(){


    var mouseX;
    var mouseY;
    $(document).mousemove(function (e) {
        mouseX = e.pageX;
        mouseY = e.pageY;
        $("#item-info-overlay-view").position = "fixed";
        $("#item-info-overlay-view").css({'top': mouseY, 'left': mouseX});
    });

    $('#overlay-view').click(function () {
        closeOverlay();
    });

    document.addEventListener("onkeydown", inputKeyDown);
    document.addEventListener("onkeyup", inputKeyUp);
    window.onkeydown = inputKeyDown;
    window.onkeyup = inputKeyUp;

    var evtobj = window.event? event : e;

    var multiSelection = false;
    function inputKeyDown(e)
    {
        evtobj = window.event? event : e;
        if(evtobj.ctrlKey)
        {
            if(! multiSelection)
            {
                multiSelection = true;
                console.log("Multiselection enabled!");
            }
        }
    }

    function inputKeyUp(e)
    {
        multiSelection = false;
    }

    $(".inventory-item").click(function() 
    {
        playAudio('click');

        var inv_val = $(this).find("input[name='inv-id-value']").val();
        
        if($(this).hasClass("inventory-item-selected"))
        {
            $(this).removeClass("inventory-item-selected");
            
            
            
            if(! multiSelection)
            {
                $('.inventory-item-selected').each(function(){
                    $(this).removeClass("inventory-item-selected");
                });
                selectedItems.splice(0, selectedItems.length);
            }
            else
            {
                for(var i = 0; i < selectedItems.length; i++) 
                {
                    if(selectedItems[i] == inv_val)
                    {
                        selectedItems.splice(i, 1);
                        break;
                    }
                }
            }
        }
        else if(multiSelection)
        {
            $(this).addClass("inventory-item-selected");
            
            selectedItems.push(inv_val);
        }
        else
        {
            $('.inventory-item-selected').each(function(){
                $(this).removeClass("inventory-item-selected");
            });
            selectedItems.splice(0, selectedItems.length);
            selectedItems.push(inv_val);
            $(this).addClass("inventory-item-selected");
        }

        for(var i = 0; i < selectedItems.length; i++){
            console.log(selectedItems + "\n");
        }
        if(selectedItems.length > 0){
            $("#inv-action-view").fadeIn(100);
        }
        else
        {
            $("#inv-action-view").fadeOut(100);
        }

        $("#inv-selected-amount").html(selectedItems.length + " items selected");
    });

});
function trashItems(){
    console.log("Trashing items...");
    if(selectedItems.length > 0){

        var value = 0;

        $.post("storage/StorageUnitController.php", {items_price: selectedItems}, function(result1){
            value = result1;

            console.log("Value: " + value);

            var confirmDelete = confirm("Delete selected items? \n Value: " + value);
            if(confirmDelete){
                $.post("storage/StorageUnitController.php", {trash_items: selectedItems}, function(result){
                    console.log("Item trash result: " + result);
                });

                $(".inventory-item-selected").fadeOut(200);
                selectedItems = [];
                $("#inv-action-view").fadeOut(100);
            }
        });



    }
}
function showStorageList(){

    var SID = 2;

    $.post("storage/StorageUnitController.php", {get_storage_list: SID}, function(result){
        $("#overlay-data").html(result);
    });

    $("#overlay-view").fadeIn(100);
    $("#overlay-data").fadeIn(100);
}
function moveItemsToStorage(storage_id){

    console.log("Moving items to storage [" + storage_id + "]");

    $.post("storage/StorageUnitController.php", {move_inv_items_id: selectedItems, move_to_storage_id: storage_id}, function(result){
        console.log("Result: " + result);
        $(".inventory-item-selected").fadeOut(200);
        $("#overlay-view").fadeOut(100);
        $("#overlay-data").fadeOut(100);

        selectedItems = [];
        $("#inv-action-view").fadeOut(100);

    });
}




