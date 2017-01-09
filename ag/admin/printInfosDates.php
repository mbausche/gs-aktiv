<?php
	include '../db.php';
	require_once("../funktionen.php");
	require_once('../ext/html2pdf/html2pdf.class.php');
		
	$id = $_REQUEST['ag'];
	if ($id == "") {
		die("Es wurde keine AG ausgewählt!");
	}
	
	$preview = $_REQUEST['preview'] == "true";
	
	ob_start();
	
	header('Content-type: application/pdf; charset=utf-8');
	
	$typ = "termin";
	$dbState = "zusage";
	$terminString = "Termin";
	include "include_info_dates.php"; 
	
	$typ = "termin_ueberbuchung";
	$dbState = "termin2";
	$terminString = "2. Termin (wegen Überbuchung)";
	include "include_info_dates.php"; 
	
	$typ = "termin_ersatz";
	$dbState = "ersatztermin";
	$terminString = "Ersatztermin";
	include "include_info_dates.php";
	
	$pdfContent = ob_get_clean();
	
	$oben=30;    //mT
	$unten=10;   //mB
	$links=15;   //mL
	$rechts=15;  //mR
	
	$html2pdf = new HTML2PDF('P','A4','de', true, 'UTF-8', array($links, $oben, $rechts, $unten));
	$html2pdf->pdf->SetDisplayMode('real');
	$html2pdf->WriteHTML($pdfContent);
	$html2pdf->Output($pdf);	
	?>
	
