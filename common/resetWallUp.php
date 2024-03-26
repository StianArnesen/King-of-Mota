<?php

include_once("../connect/connection.php");

die("Not allowed");

$SE_CON = new StaticConnection();

$VAL = 0;

$SQL = "UPDATE wall_status SET status_upvotes = $VAL";
$QUERY = mysqli_query($SE_CON->CONNECTION, $SQL);

die(mysqli_error($SE_CON->CONNECTION));
