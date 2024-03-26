function write_comment(COMMENT_IDENTIFIER, POST_ID) {
    console.log("Sending!");

    var DATA = $("#post-comment-input-id_" + COMMENT_IDENTIFIER).val();

    $.post("profile/comment/writecomment.php", {GSPUD: DATA, GPUUID: POST_ID}, function () {
        console.log("Status posted!");
    });
}
console.log("Post comment script loaded!");