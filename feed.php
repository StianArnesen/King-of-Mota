<?php
include("feed/feed_gen.php");
include("common/page.php");
include("layout/bot_banner/bot_banner.php");

$FEED = new StaticFeed();

if(isset($_POST['likePostID']))
{
    $STATUS_ID = ($_POST['likePostID']);

    $FEED->upvoteStatusPost($STATUS_ID);
    die("Done");
}
else if(isset($_POST['get_upvote_list_id']))
{
    $ELEMENT_ID     = $_POST['get_upvote_list_id'];
    $LINK_TYPE      = $_POST['get_upvote_list_type'];

    $USERNAME_LIST  = $FEED->getUsernameListOfUpVotes($ELEMENT_ID, $LINK_TYPE);

    die(json_encode($USERNAME_LIST, JSON_PRETTY_PRINT));
}
else
{

}

$PAGE = new PageClass();

echo $PAGE->getTopBanner();


$BOTTOM_BANNER = new BottomBanner();

$FEED->dev_log_end();

?>


<html>
    <head>
        <link href="/style/feed/style.css" rel="stylesheet">
    </head>
    <div class="top-image"> <img src="img/profile/bga.jpg"></div>
    <?php
        $FEED_AMOUNT = 5;

        echo "<div id='feed'>". $FEED->getFeed($FEED_AMOUNT) . "</div>";

    ?>
    <div id="bottom_banner_full_view">
        <? echo $BOTTOM_BANNER->getBottomBanner();?>
    </div>

</html>



<script type="text/javascript" src="script/common/priceFix.js"></script>
<script type="text/javascript" src="profile/status/writestatus.js"></script>

<script>

    var update = false;

    $(document).ready(function(){



        var wall_post_show_amount = 5;

        $(window).scroll(function(){



            var scrollposY = $(window).scrollTop() + 1100;

            $(".top-image").offset({top: (scrollposY / 2)-600});


            if(scrollposY >= ($(document).height()))
            {
                wall_post_show_amount += 1;

                update = true;
            }
        });

        setInterval(function()
        {
            if(update)
            {
                $("#feed").load("feed/getFeed.php?feed_amount=" +  wall_post_show_amount);
                update = false;
            }
            else
            {

            }
        }, 300);

    });


    function likeStatus(id)
    {
        $.post("feed.php", {likePostID: id}, function(data)
        {
            update = true;
            console.log("Status Liked: Result = " + data);
        });
    }

    function slideDown(commentSection)
    {
        $(commentSection).slideToggle(100);
    }

</script>
