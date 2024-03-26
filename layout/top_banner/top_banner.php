<!--  			Game overlay		 -->
<div id="top-banner-overlay-view"></div>
<div id="top-banner-overlay-data" class="top-banner-overlay-data">


    <!-- OVERLAY DIALOG (GameDialog) -->
    <div id="overlay-msg-dialog">
        <div class="overlay-msg-title">
            <div id="overlay-msg-title">Game dialog (TEST)</div>
        </div>
        <div class="overlay-msg-text">
            <div id="overlay-msg-text">Error message should have been displayed here... But seems like the script is broken :(
            </div>
        </div>
        <div class="error-code"> 
            <div id="overlay-msg-error-code"></div>
        </div>

        <div class="overlay-action-button-container">
            <button onclick="hideGameOverlay()" class="overlay-action-button" id="overlay-action-button">Okay</button>
            <button onclick="hideGameOverlay()" class="overlay-action-button" style="display: none;" id="overlay-button-cancel">No</button>
        </div>
        

    </div>


    <!-- Level up dialog  -->
    <div id="overlay-unlocked-items-view" style="display: none;">
        <div class="overlay-pre-text">
            <div class="overlay-unlocked-items-title"> Level up!</div>
            <div id="overlay-new-level"></div>

            <div id="overlay-unlocked-desc">Items unlocked:</div>
        </div>


        <div id="overlay-unlocked-items-list">

        </div>

        <button onclick="hideGameOverlay()" id="overlay-close-button">CLOSE</button>

    </div>


</div>


<!--    Navigation bar | Top banner  -->
<div id="nav_toggle" class="navigation_bar_toggle">
    <div class="game-title">King of Mota</div>
    <div class="toggle-menu-title">Menu</div>

</div>
<div id="nav" class="navigation_bar">

    <div id="nav_wrapper">
        <img id="notification_img" src="layout/top_banner/notification.png">
        <div id="notifications_amount"></div>
        <input type='hidden' id='notifications_amount_hidden' value='0'>

        <div class="navbar-items">
            <ul>
                <li><a href="garden.php">Home</a>
                    <ul>
                        <li><a href="home.php">My profile <img src="img/icon/menu/home.svg" class="menu-item-image">
                            </a></li>
                        <li><a href="garden.php">Garden <img src="img/icon/menu/garden.svg" class="menu-item-image"></a>
                        </li>
                        <li><a href="lab.php">Lab <img src="img/icon/menu/lab.svg" class="menu-item-image"></a></li>
                        <li><a href="stats.php">My Stats<img src="img/icon/ranks/gold.png" class="menu-item-image"></a>
                        </li>
                    </ul>
                </li>


                <li>
                    <a href="inventory.php">Inventory</a>
                    <div id="top-banner-inventory-space"></div>
                    <ul class="hidden">
                        <li><a href="inventory.php">My items <img src="img/icon/menu/icon_pack/png/suitcase.png"
                                                                  class="menu-item-image"></a></li>
                        <li><a href="storage.php" style="background-color: rgba(114, 10, 7, 0.68)">Storage<img
                                        src="img/icon/menu/icon_pack/png/suitcase.png" class="menu-item-image"></a></li>
                    </ul>
                </li>
                <li>
                    <a href="shop.php?category=0">Market</a>
                    <ul>
                        <li><a href="market.php">Sell <img src="img/icon/menu/icon_pack/png/coin.png"
                                                           class="menu-item-image"></a></li>
                        <li>
                            <a href="shop.php?category=0">Buy <img src="img/icon/menu/icon_pack/png/delivery-1.png"
                                                                   class="menu-item-image"></a>
                        </li>
                    </ul>
                </li>

                <li class="hidden"><a href="crew.php">Crew</a>
                    <ul>

                        <li><a href="crew.php">My crew <img src="img/icon/menu/crew.svg"
                                                            class="menu-item-image"></a> </a> </li>
                        <li><a href="crewlist.php">Scoreboard<img src="img/icon/menu/findcrew.svg"
                                                                  class="menu-item-image"></a></li>
                        <li><a href="newcrew.php">Start crew <img src="img/icon/menu/add_crew.svg"
                                                                  class="menu-item-image"> </a></li>
                    </ul>
                </li>

                <li><a href="scoreboard.php">Rank</a>
                    <ul class="hidden">
                        <li>
                            <a href="scoreboard.php">Global ranks <img src="img/icon/menu/rank.svg"
                                                                       class="menu-item-image"></a>
                        </li>
                    </ul>
                </li>

                <li><a href="friends.php">Friends</a>
                    <ul>
                        <li class="hidden"><a href="friends.php">My friends <img src="img/icon/menu/friends.svg"
                                                                                 class="menu-item-image"></a></li>

                        <li class="hidden"><a href="addfriend.php">Add friend <img src="img/icon/menu/addfriend.svg"
                                                                                   class="menu-item-image"></a></li>
                        <li class="hidden"><a href="friendrequests.php">Added me<img src="img/icon/menu/friend_req.svg"
                                                                                     class="menu-item-image"> </a></li>
                    </ul>
                </li>
                <li><a href="#" style="background-color: rgba(8, 98, 8, 0.16);">Game</a>
                    <ul>
                        <li>
                            <a href="credits.php">Credits</a>
                        </li>
                        <li>
                            <a href="feedback.php" >Feedback</a>
                        </li>

                    </ul>
                </li>
                <li><a href="#" style="background-color: rgba(93, 8, 8, 0.16);">Logout</a>
                    <ul>
                        <li>
                            <a href="logout.php" style="background-color: rgba(250,10,10,0.4);">Confirm logout</a>
                        </li>
                    </ul>
                </li>

                

                <form class="top-banner-search-bar">
                    <div class="form-input">
                        <input type="text" class="top-banner-query" ng-model="query" placeholder="Search for players">
                    </div>
                    <div class="form-result" ng-show="query.length">
                        <div ng-repeat="user in userList | filter: query | limitTo: 20">
                            <div class="result-item">
                                <div class="user-image">
                                    <img src="{{user.image}}">
                                </div>
                                <div class="username">
                                    <a href="/{{user.username}}">{{user.username}}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <!--<input class="top-banner-query" type="text" placeholder="search">-->
            </ul>
        </div>

        <div> </div>
        <div id="global-mute-button"></div>
    </div>
</div>

<div id="notification_view">

    <div id="notification_panel">
        <div id="notifications_title">
            Notifications
        </div>

        <div id="notification_panel_items">

        </div>
    </div>
</div>

<div class="profile-view-container">

    <div class="profile-view-content">
        <div class="profile-view-info-section">
            <div class="profile-view-info-item profile-username-section">
                <div id="profile-username"></div>
                <div class="profile-level-view"><span>lvl </span>
                    <div id="profile-level"></div>
                </div>
            </div>
            <div class="profile-view-info-item" id="profile-view-info-item-money">
                <img class="profile-view-info-icon" src="img/icon/cash.png">
                <div id="profile-money"></div>
            </div>
            <div class="profile-view-info-item">
                <img class="profile-view-info-icon" src="img/icon/g_coin.jpg">
                <div id="profile-g-coins"></div>
            </div>
        </div>

        <div class="profile-view-image-section">
            <div id="profile-image"></div>
        </div>

        <div id="profile-exp-bar-div">
            <div id="exp-text-div">
                <div id="profile-cur-exp"></div>
                <div id="profile-target-exp"></div>
            </div>
            <div id="profile-exp-bar">
                <div id="profile-exp-bar-inner"></div>
            </div>
            <div class="profile-btn-view-toggle" onclick="toggleProfileView()">Show / hide</div>
        </div>
        

    </div>

</div>


<script type="text/javascript" src="script/top_banner/notification.js"></script>

<script type="text/javascript">

    var muted;


    $(document).ready(function () {


        $("#notification_img").click(function () {
            $("#notification_panel").slideToggle(100);
        });
        $("#content").click(function () {
            $("#notification_panel").slideUp(100);
        });

        $("#nav_toggle").click(function () {
            $("#nav").slideToggle(100);
        });

    });

</script>
