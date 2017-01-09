<?php
require_once("db.php");
if (!empty($_REQUEST["id"])) {
	$ag = NeueAgModel::loadAG($_REQUEST["id"]);
	if ($ag != null && $ag["bild"] != null) {
		header("Content-type: " . $ag["bild_mime_type"]. "; charset=ISO-8859-1" );
		header("Pragma: no-cache");
		header("Expires: 0");
		echo $ag["bild"];
		exit(0);
	} 
} 
include include_404.php
?>
