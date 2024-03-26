<!--Profile info menu-->
<?php


class ProfileBanner
{
    private $BANNER;

    public function __construct()
    {
        $this->loadBanner();
    }
    private function loadBanner()
    {
        $profile_banner_file = fopen("layout/profile_banner/profile_banner_style.css", "r") or die("Unable to open file!");
        $this->BANNER = fread($profile_banner_file,filesize("layout/profile_banner/profile_banner_style.css"));
        fclose($profile_banner_file);
    }
    public function getProfileBanner()
    {
        $USERNAME = $_SESSION["game_username"];

        return '
    <style>
        '.$this->BANNER .'
    </style>
    <div id="profile_info_div">
        <div id="profile_info_username">
            '.$USERNAME.'
        </div>
        <div id="profile_info_picture">
           <img id="profile_pic" src="$data_profile_picture">
        </div>
        <div id="profile_info_money">
            Money: <div class="item_price_label" id="profile_money">$data_money</div>$
        </div>
        <div id="profile_info_level">
            Level: '. $data_level . '
        </div>
        <div class="progress" id="user_level_bar_progress">
            <div id="progressbar_level">
                <div style="width: <?php echo ($data_current_exp/$data_next_level_exp)*100 . "%";?>;"></div>
            </div>
            <div id="exp_status_text">EXP:'.  $data_current_exp .' / '. $data_next_level_exp. '</div>
        </div>
    </div>';
    }
}
