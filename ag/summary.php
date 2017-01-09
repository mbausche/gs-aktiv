<?php
	session_start();
// 	ini_set("log_errors", 1);     /* Logging "an" schalten */
// 	ini_set("error_log", "errorlog.txt");     /* Log-Datei angeben */

	require_once("funktionen.php");
	require_once("db.php");
	copyRequestValuesToSession(array("AnmeldungsId","direkt_eingabe","geprueft","klasseFuerFilter","filterKlassen","fingerprint"));
	$ags = AgModel::getAgs();

	$pages = array();
	$pages[0] = false;
	$pages[1] = false;

	if (empty($_REQUEST["html"])) {
		$html = true;
	} else {
		$html = $_REQUEST["html"] == "true";
	}
	$_SESSION["html"] = $html;
	
	if (empty($_REQUEST["bootstrap"])) {
		$bootstrap = true;
	} else {
		$bootstrap = $_REQUEST["bootstrap"] == "true";
	}
	$_SESSION["bootstrap"] = $bootstrap; 
	
	$header = true;
	include('readOnly.php');
    
?>