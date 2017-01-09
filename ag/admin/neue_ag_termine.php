<?php
	include '../db.php';
	require_once("../funktionen.php");
	
	$tableId = "";
	$title = "Terminübersicht Neue Eltern-AGs";
	include 'header.php';
	include 'include_neue_ag_termine.php';
?>

<script>
$(document).ready(function() {
	setContentTableVisible();
});
</script>

