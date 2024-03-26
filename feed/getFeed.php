<?php

$ROOT = $_SERVER['DOCUMENT_ROOT'];
include($ROOT. "/feed/feed_gen.php");

$SE_FEED = new StaticFeed();

if(isset($_GET['feed_amount']) && isset($_GET['user_id']))
{
    $MAX_AMOUNT = $_GET['feed_amount'];
    $USER_ID    = $_GET['user_id'];

    echo $SE_FEED->getFeed($USER_ID, $MAX_AMOUNT);
}
else
{
    die("invalid request");
}