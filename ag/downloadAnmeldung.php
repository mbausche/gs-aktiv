<?php 
require_once("funktionen.php");
$anmeldeNummer = $_REQUEST["nummer"];

if (!empty($anmeldeNummer)) {
	$anmeldungPDF = findAnmeldungsPDF("./pdf/",$anmeldeNummer);
	if (!empty($anmeldungPDF)) {
		
		header("Content-type: application/pdf; charset=UTF-8");
		header("Content-Disposition: attachment; filename=$anmeldungPDF");
		header("Pragma: no-cache");
		header("Expires: 0");
		
		$file = file_get_contents("./pdf/$anmeldungPDF", FILE_USE_INCLUDE_PATH);
		echo $file;
		
	} else {
		include 'include_404.php';
	}
		
} else {
	include 'include_404.php';
}



//Variablen die erwartet werden:
//$name 
//$anmeldeNummer
//$pdfPath
$filenameStart = strtolower(preg_replace('/\s+/', '_', $name));

if ($handle = opendir($pdfPath)) {
	while (false !== ($entry = readdir($handle))) {
		if (startsWith($entry, $filenameStart)
			&& endsWith($entry, "_mail.pdf")
			&& strpos($entry, $anmeldeNummer) !== FALSE) {
			
			$to = CfgModel::load("mail.mitgliederverwaltung.address");
			$toName = CfgModel::load("mail.mitgliederverwaltung.name");
			
			ob_start();
			include 'template_neues_mitglied.php';
			$content = ob_get_clean();
			$subject = $name . " möchte Mitglied werden.";
			sendMail($to, $toName, $subject, $content, array($pdfPath . "/" . $entry));
			break;
		}
	}

	closedir($handle);
}




?>