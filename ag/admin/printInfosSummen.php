<?php
	include '../db.php';
	require_once("../funktionen.php");

	$suffix = CfgModel::load("halbjahrFilenameSuffix");
	
	header("Content-type: text/csv; charset=ISO-8859-1");
	header("Content-Disposition: attachment; filename=Summen_$suffix.csv");
	header("Pragma: no-cache");
	header("Expires: 0");
	
	$ags = AgModel::getAgs();
	
	$lineBreak = "\n";
	
	ob_start();
	
	echo "Ag-Nummer;Ag;Beiträge Mitglieder;Beiträge Nicht-Mitglieder;Erstattungen Mitglieder;Erstattungen Nicht-Mitglieder$lineBreak";
	
	foreach ($ags as $ag) {
		$values = array();
		array_push($values, $ag['ag_nummer']);
		array_push($values, $ag['name']);
		echo join(";", $values) . ";" . str_replace(".", ",", join(";", AgModel::getSummen($ag['ag_nummer']))) . $lineBreak;
	}
	
	$csv = ob_get_clean();
	$csv = mb_convert_encoding($csv, 'ISO-8859-1',mb_detect_encoding($zeile, 'UTF-8, ISO-8859-1', true));
	echo $csv;
	?>
	
