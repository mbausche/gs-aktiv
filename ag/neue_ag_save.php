
<?php
	ini_set("log_errors", 1);     /* Logging "an" schalten */
 	ini_set("error_log", "errorlog.txt");     /* Log-Datei angeben */

	require_once("funktionen.php");
	session_start();
	
	$url = "neue_ag_anmelden.php?keepValues=true";
	if (!empty($_REQUEST["editForm"])) {
		$url = $_REQUEST["editForm"];
	}
	
	if ($_SERVER['REQUEST_METHOD'] != "POST") {
		$_SESSION["error"] = "Falsche Eingabemethode";
		header("Location: " . $url); /* Browser umleiten */
		return;
	}
	
	if ($_REQUEST['namen'] == $_REQUEST['ag_name']
		|| empty($_REQUEST['termin'])) {
		copyRequestValuesToSession();
		$_SESSION["error"] = "Falsche Eingabe";
		header("Location: " . $url); /* Browser umleiten */
		return;
	}	
	
	if (!empty($_FILES['bild']['name']) ) {
		if ($_FILES['bild']['size'] == 0) {
			copyRequestValuesToSession();
			$_SESSION["error"] = "Die Grafik konnte nicht gespeichert werden. Evtl. war die Grafik zu groß!";
			header("Location: " . $url); /* Browser umleiten */
			return;
		}else if (empty($_FILES['bild']['tmp_name'])) {
			copyRequestValuesToSession();
			$_SESSION["error"] = "Die Grafik konnte nicht gespeichert werden. Fehler beim Speichern der Grafik auf dem Server";
			header("Location: " . $url); /* Browser umleiten */
			return;
		}else if ($_FILES['bild']['type'] != "image/jpg" && $_FILES['bild']['type'] != "image/jpeg" && $_FILES['bild']['type'] != "image/png") {
			copyRequestValuesToSession();
			$_SESSION["error"] = "Die Grafik konnte nicht gespeichert werden. Es werden nur jpg- und png-Grafiken unterstützt!";
			header("Location: " . $url); /* Browser umleiten */
			return;
		} else if ($_FILES['bild']['error'] != 0) {
			copyRequestValuesToSession();
			$_SESSION["error"] = "Die Grafik konnte nicht gespeichert werden. Evtl. war die Grafik zu groß oder das Format wird nicht unterstützt!";
			header("Location: " . $url); /* Browser umleiten */
			return;
		} 
	}
	
	$_SESSION["error"] = "";
	
	require_once("funktionen.php");
	require_once("db.php");

	$result = "Es wurde keine Aktion durchgeführt";
	if ($_REQUEST["forceUpdate"] == "true") {
		if ($_REQUEST["response"] == "yes") {
			sendMail("info@grundschule-aktiv.de", "Info", "POSITIVE Rückmeldung für AG " . $_REQUEST["ag_name"] . " von " . $_REQUEST["verantwortlicher_name"]);
		}
		$array = NeueAgModel::insertFromRequest(true,$_REQUEST["edit_token"]);
	} else {
		$array = NeueAgModel::insertFromRequest();
	} 
	
	$result = ($array[0] ? "Neue AG " : "AG Geändert") . " <b>'".$_REQUEST["ag_name"] . "'</b>";
	$id = $array[1];
	
	$ex = explode("/", $_SERVER[PHP_SELF]);
	$ex[count($ex)-1] = 'neue_ag_pdf.php?id='.$id;
	$url = implode("/", $ex);
	$url = 'http://' . $_SERVER['HTTP_HOST'] .$url;
	$pdf = "pdf/ag_" . $id . ".pdf";
	file_put_contents($pdf, file_get_contents($url));

	if (!empty($_REQUEST["redirectAfterSave"])) {
		$url = $_REQUEST["redirectAfterSave"];
		header("Location: " . $url);
		return;
	}
	
	$subject = CfgModel::load("prefixAgNummer") . ": " .  ($array[0] ? "Neue AG " : "AG Geändert") . $_REQUEST["ag_name"] . " von " . $_REQUEST["verantwortlicher_name"];
	sendMail("info@grundschule-aktiv.de", "Info", $subject, "" ,array($pdf));
	
	$title = "Veranstalten einer Eltern-AG";
	include 'header.php';
	
?>

<?php echo $result?><br><br>

Danke dass du eine AG anbietest!!!<br>
<br>
<b>Wie geht's jetzt weiter?</b><br>
<br>
<ul>
<li>- Du kannst die AG als PDF speichern oder ausdrucken (siehe unten)
<li>- Wir prüfen die AG und stimmen Änderungen mit dir ab
<li>- Die AG bekommt eine eindeutige Nummer
<li>- Die AG wird im Heft abgedruckt 
</ul>
<br><br>
<a type="button" href="neue_ag_pdf.php?id=<?php echo $id?>" target="_blank">PDF anzeigen</a><br><br>
<a type="button" href="neue_ag_anmelden.php">Eine weitere AG anbieten</a>

<?php include 'admin/footer.php'; ?>