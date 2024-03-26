
function showChat(id) {
    console.log("Showing chat: " + id);
    $("#chat_view_item_id_" + id).toggle(100);
    toggleChatStatus(id);
}

$(document).ready(function(){

    $(".btn_send_message_chat_button").click(function(){

        var textFieldData = $(this).prev('.msg_input_text').val();
        var messageGroupLink = $(this).next('.message_input_group').val();

        console.log("Sending message...   Data: " + textFieldData);

        completeMsg(messageGroupLink,textFieldData);
    });

    $(".bottom_banner_friend_item").click(function(){

        var username = $(this).find('.bottom_banner_friend_username_value').val();
        console.log("Opening chat: username = '" + username + "'");
        openChat(username);
    });

    function openChat(username)
    {
        $.post("communication/public_communicator.php", {get_chat_id_username: username}, function(RESPONSE){
            console.log("Response from server: " + RESPONSE);
        });
        setTimeout(function(){
            $("#bottom_banner_full_view").load("communication/public_communicator.php?get_data_type=0");
        },500);
    }

    function completeMsg(link, data)
    {
        $.post("communication/public_communicator.php", {msg_link: link, msg_link_data: data}, function(RESPONSE){
           console.log("Response from server: " + RESPONSE);
        });
        setTimeout(function(){
            $("#bottom_banner_full_view").load("communication/public_communicator.php?get_data_type=0");
        },500);
    }

    function setScroll()
    {
        $('.chat_view').animate({
                scrollTop: $(this).height()/2},
            155,
            "linear"
        );
    }

    setScroll();


});

function closeChat(chat_link)
{
    console.log("Closing chat...");
    $.post("communication/public_communicator.php", {close_inbox: chat_link}, function(RESPONSE){
        console.log("Response from server: " + RESPONSE);
    });
    $("#chat_item_view_box_w_id_" + chat_link).remove();
}
function toggleChatStatus(chat_link)
{
    console.log("Chat toggle...");
    $.post("communication/public_communicator.php", {toggle_inbox_client_status: chat_link}, function(RESPONSE){
        console.log("Response from server: " + RESPONSE);
    });
}