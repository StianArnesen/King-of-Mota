<?php

function get_topBanner()
{
    $btn_file = fopen("layout/top_banner/top_banner.php", "r") or die("Unable to open file!");
    $RESULT = fread($btn_file, filesize("layout/top_banner/top_banner.php"));
    fclose($btn_file);

    return $RESULT;
}