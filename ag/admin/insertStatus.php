<?php 
	include '../db.php';
	require_once("../funktionen.php");
	
	$content = file_get_contents('C:/temp/status.csv');
	$zeilen = explode("\n", $content);
	
	$count = 0;
	
 	StatusModel::wipe();
	
 	$line = 0;
 	
	foreach ($zeilen as $zeile) {
		$id = StatusModel::insertStatus($zeile);
		if ($id > -1) {
			$count++;
		}
		$line++;
	}

	$title =  "Mitglieder-Status importieren";
	include 'listStatus.php';
	
?>