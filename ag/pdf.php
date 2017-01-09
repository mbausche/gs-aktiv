<?php

	session_start();

	require_once("funktionen.php");
	require_once("db.php");
	require_once("conf.php");

	$pdfFileName = $_SESSION['insert_filename']; 
	$pdf = "pdf/" . $pdfFileName;
	
    header("Content-type: application/pdf; charset=UTF-8");
    header("Content-Disposition: attachment; filename=$pdfFileName");
    header("Pragma: no-cache");
    header("Expires: 0");
    
    $file = file_get_contents($pdf, FILE_USE_INCLUDE_PATH);
    echo $file;
    
?>