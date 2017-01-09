<?php
	ini_set("log_errors", 1);     /* Logging "an" schalten */
 	ini_set("error_log", "errorlog.txt");     /* Log-Datei angeben */

	require_once("funktionen.php");
	require_once("db.php");

	$id = $_REQUEST["id"];
	$response = $_REQUEST["response"];
	
	$ag = NeueAgModel::loadAG($id);
	
	$title = "Rückmeldung für Eltern-AG '" . $ag["ag_name"] . "'";
	include 'header.php';

	if ($response == "yes") {
		NeueAgModel::saveFeedBack($id,"1");
		$status = sendMail("info@grundschule-aktiv.de", "Info", "POSITIVE Rückmeldung für AG " . $ag["ag_name"] . " von " . $ag["verantwortlicher_name"]);
		echo "<h2>Super, danke für die Rückmeldung</h2>\n<!-- $status -->";
	} else {
		NeueAgModel::saveFeedBack($id,"0");
		$status = sendMail("info@grundschule-aktiv.de", "Info", "NEGATIVE Rückmeldung für AG " . $ag["ag_name"] . " von " . $ag["verantwortlicher_name"]);
		echo "<h2>Okay wir melden uns bei dir!!!</h2>\n<!-- $status -->";
	}
	
?>



<?php include 'admin/footer.php'; ?>