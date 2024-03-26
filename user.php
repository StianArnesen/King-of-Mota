<?php

include("feed/feed_gen.php");
include("common/page.php");
require("common/session/sessioninfo.php");
require("user/PublicUserInfo.php");
require("user/UserController.php");


if (isset($_POST['likePostID']) && is_numeric($_POST['likePostID'])) 
{
    $STATUS_ID = ($_POST['likePostID']);

    $FEED->upvoteStatusPost($STATUS_ID);
    
    die("Done");
}


$USER               = new User();
$publicUserInfo     = new PublicUserInfo();
$FEED               = new StaticFeed();
$PAGE               = new PageClass();
$userController     = new UserController();


$FEED->dev_log_end();


$USERNAME = ($_GET['profile_username']);

if ($publicUserInfo->loadUserInfo($USERNAME)) {
    $CURRENT_USER_INFO_ARRAY = $publicUserInfo->getUserInfo();

    $CURRENT_USER_ID = $publicUserInfo->getUserId();
    $CURRENT_USER_LEVEL = $CURRENT_USER_INFO_ARRAY['level'];
} else {
    die("Failed to load user");
}

$USER_AWARDS = $userController->getUserAwardList($CURRENT_USER_ID);

$RANK_CLASS = "";
    $HIGH_RANK  = false;
    
    $USER_RANK = $publicUserInfo->getRank($CURRENT_USER_ID);
    
    if(isset($USER_RANK) && $USER_RANK <= 3 && $USER_RANK !== -1){
        $HIGH_RANK = true;
    }
    

?>


<html>
    <head>
        <link href="style/feed/style.css" rel="stylesheet" type="text/css">
        <link href="style/user/style.less" rel="stylesheet" type="text/less">
    
        <?php
            echo $PAGE->getHeaderInfo();
        
            if ($_GET['profile_username'] == $USER->getUsername()) 
            {
                echo "<title>King of Mota | Home</title>";
            } 
            else 
            {
                echo "<title>King of Mota | " . $_GET['profile_username'] . "</title>";
            }
        ?>

        <script src="script/croppic/croppic.js"  type="application/javascript"></script>
        <!-- <script src="script/cropper/js/cropper.js"  type="application/javascript"></script>-->

        <link  href="script/cropper/clean/cropper.css" rel="stylesheet">
        <script src="script/cropper/clean/cropper.js"></script>
        
        <script src="script/user/homescript.js" type="application/javascript"></script>
        <script src="script/user/imageCrop.js"  type="application/javascript"></script>
        
        
    </head>
<body>


<div id="overlay-view"> </div>
<div id="overlay-data" class="overlay-data">
    <div class="dialog-title" id="dialog-title">Players that like this</div>

    <div class="dialog-list-view" id="dialog-list">
        <div class="dialog-list-item">

            <div class="dialog-list-item-username" >
                <a href="/gulli_boy">gulli_boy</a>
            </div>

            <div class="dialog-list-item-image">
                <img src="img/0.jpg">
            </div>

        </div>
    </div>

    <div id="overlay-status"> </div>

</div>

<input type="hidden" value="<?php echo $CURRENT_USER_ID; ?>" id="current-user-id">
<?php echo $PAGE->getTopBanner(); ?>
<div class="top-image"><img src="<?php echo $CURRENT_USER_INFO_ARRAY['header_image']; ?>"></div>

<?php

    
    $name = $CURRENT_USER_INFO_ARRAY['username'];
    if($USER_RANK <= 3){
        
        $RANK_CLASS = "user-rank-" . ($USER_RANK !== -1)? $USER_RANK : "Admin";
        
        $IMG = "img/icon/ranks/gold.png";
        
        if($USER_RANK == 1){
            $IMG = "img/icon/ranks/gold.png";    
        }
        else if($USER_RANK == 2){
            $IMG = "img/icon/ranks/silver.png";
        }
        else if($USER_RANK == 3){
            $IMG = "img/icon/ranks/bronze.png";
        }
        else if($USER_RANK == -1){
            $IMG = "img/icon/ranks/admin.webp";
        }
        
        echo ' <div id="profile-view-username" class="user-rank-'. $USER_RANK .'"> <div class="user_item_rank_icon"> <img src="'. $IMG.'"> </div> '. $name. '</div>';
    }
    else
    {
        echo '<div id="profile-view-username">'. $name. '</div>';
    }

?>


<div id="profile-view-image" class="<?php echo $RANK_CLASS;?>">
    <div class="container">
        <div class="img-container">
            <img id="user_image" src="<?php echo $CURRENT_USER_INFO_ARRAY['profile_picture']; ?>">
        </div>
        <?php
            
        if($CURRENT_USER_ID == $USER->getUserId())
        {
            $CHANGE_IMAGE_SETTINGS_HTML = '<button id="change_profile_picture" > Change profile picture </button>
                                <button id="btn_save_cropping" onclick="saveCropping();" style="display: none;" class="button">Save crop</button>';
            echo $CHANGE_IMAGE_SETTINGS_HTML;
        }
        else{
            if(! $userController->friendWith($CURRENT_USER_ID))
            {
?>
            <button name="add_friend" class="btn-add-friend" value="<?php echo $CURRENT_USER_ID;?>" onclick="sendFriendRequest(this)">Add friend</button>
<?php
            }
        }
        
        ?>
    </div>
    
    
    
    <?php
    $CHANGE_PROFILE_IMG_HTML ='<div class="upload_form">
                                    <form action="home.php" method="post" enctype="multipart/form-data">
                                        Select image to upload:
                                        <input type="file" name="fileToUpload" id="fileToUpload">
                                        <input type="submit" value="Upload Image" name="upload_image">
                                    </form> 
                                    <button onclick="initImageCrop()" value="Crop image">Crop current image</button>
                                </div>';
    
    if ($CURRENT_USER_ID == $USER->getUserId()) {
        echo $CHANGE_PROFILE_IMG_HTML;
    }
    ?>

</div>
<div id="profile-view">
    <div id="profile-view-content" class="<?php echo $RANK_CLASS;?>">
        
        <?php 
            $rank_text = ($USER_RANK !== -1)? $USER_RANK : "Admin";
                echo '<div class="info-text '.$RANK_CLASS .'">
                        Rank: '. $rank_text .'
                      </div>';
        ?>
        
        <div id="profile-view-level" class="info-text <?php echo $RANK_CLASS;?>">
            Level: <?php echo $CURRENT_USER_LEVEL; ?>
        </div>
        <div id="profile-view-award-list">

            <?php

            if ($USER_AWARDS) {            
                while ($AWARD = mysqli_fetch_array($USER_AWARDS)) {

                    $AWARD_IMAGE = $AWARD['image'];          // Award image
                    $DESC = $AWARD['description'];    // Award desc
                    $TITLE = $AWARD['title'];          // Award title

                    echo "<div class='award-item'>";
                    echo "<img title='$DESC' class='award-icon' src='$AWARD_IMAGE'>";
                    echo "<div class='title'>$TITLE</div>";
                    echo "</div>";
                }
            }
            ?>
        </div>
    </div>

</div>

<?php echo "<div id='feed'>" . $FEED->getFeed($CURRENT_USER_ID, 5) . "</div>"; ?>

</body>

</html>


<script type="text/javascript" src="script/common/priceFix.js"></script>
<script type="text/javascript" src="profile/status/writestatus.js"></script>
<script type="text/javascript" src="profile/comment/writecomment.js"></script>

<script>

    var update = false;

    $(document).ready(function () {


        $("#change_profile_picture").click(function () {
            $(".upload_form").slideToggle(300);
        });

        var USER_ID;

        var wall_post_show_amount = 5;

        $(window).scroll(function () {


            var scrollposY = $(window).scrollTop() + 1100;

            $(".top-image").offset({top: (scrollposY / 2) - 600});


            if (scrollposY >= ($(document).height())) {
                wall_post_show_amount += 1;

                update = true;
            }
        });

        setInterval(function () {
            if (update) {
                USER_ID = $("#current-user-id").val();
                $("#feed").load("feed/getFeed.php?feed_amount=" + wall_post_show_amount + "&user_id=" + USER_ID);
                update = false;
            }
            else {

            }
        }, 1300);

    });


    function likeStatus(id) {
        $.post("feed.php", {likePostID: id}, function (data) {
            update = true;
            console.log("Status Liked: Result = " + data);
        });
    }

    function slideDown(commentSection) {
        $(commentSection).slideToggle(100);
    }

</script>
