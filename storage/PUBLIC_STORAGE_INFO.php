<?php

if(isset($_POST['backpack']))
{
	require("StorageController.php");

	$STORAGE = new StorageController();

	$BACKPACK_ID = $STORAGE->getBackpackStorageID();

	$R = array(
		"used" 	=> $STORAGE->getSpaceUsedInStorage($BACKPACK_ID),
		"cap" 	=> $STORAGE->getStorageCapacity($BACKPACK_ID)
		);
	/*$R = array(
	"used" 	=> "Server restart < 1min!",
	"cap" 	=> $STORAGE->getStorageCapacity($BACKPACK_ID)
	);*/

	$RESULT = json_encode($R, JSON_PRETTY_PRINT);

	die($RESULT);
}