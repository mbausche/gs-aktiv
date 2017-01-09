<?php

	ini_set("log_errors", 1);     /* Logging "an" schalten */
	ini_set("error_log", "pdf.txt");     /* Log-Datei angeben */

	session_start();

	require_once("funktionen.php");
	require_once("db.php");
	require_once("conf.php");

	$id = $_REQUEST["id"];
	$test = $_REQUEST["test"] == "true";
	if (empty($id)) {
		include 'include_404.php';
		exit();
	}
	
	$ag = NeueAgModel::loadAG($id);

	$html = $test;
	
	ob_start();
    include('neue_ag_readOnly.php');
    $content = ob_get_clean();
    
    if (!$test) {
    	
    	error_log("PDF: " + $content);
    	
	    require_once('html2pdf/html2pdf.class.php');
	    // seitenränder (in mm)
	    $oben=30;    //mT
	    $unten=10;   //mB
	    $links=15;   //mL
	    $rechts=15;  //mR
	    
	    $html2pdf = new HTML2PDF('P','A4','de', true, 'UTF-8', array($links, $oben, $rechts, $unten));
	    $html2pdf->pdf->SetDisplayMode('real');
	    $html2pdf->WriteHTML($content);
	    $html2pdf->Output();
    } else {
    	echo $content;
    }
    

    
?>