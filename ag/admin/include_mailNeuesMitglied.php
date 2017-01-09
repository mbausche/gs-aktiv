<?php 

//Variablen die erwartet werden:
//$name 
//$anmeldeNummer
//$pdfPath

$to = CfgModel::load("mail.mitgliederverwaltung.address");
$toName = CfgModel::load("mail.mitgliederverwaltung.name");
ob_start();
include 'template_neues_mitglied.php';
$content = ob_get_clean();
$subject = $name . " möchte Mitglied werden.";
$anmeldungPDF = findAnmeldungsPDF($pdfPath,$anmeldeNummer);
if (!empty($anmeldungPDF)) {
	sendMail($to, $toName, $subject, $content, array($pdfPath . "/" . $anmeldungPDF));
} else  {
	sendMail($to, $toName, $subject, $content);
} 



?>