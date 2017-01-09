<?php
	ini_set("log_errors", 1);     /* Logging "an" schalten */
 	ini_set("error_log", "errorlog.txt");     /* Log-Datei angeben */

	require_once("funktionen.php");
	require_once("db.php");

	$editToken = $_REQUEST["edit_token"]; 
	
	$ag = NeueAgModel::getByEditToken($editToken);
	
	$title = "Änderung für Eltern-AG '" . $ag["ag_name"] . "'";
	include 'header.php';

	echo "<h2>Die AG wurde geändert</h2>\n";
	
?>

<a type="button" href="neue_ag_pdf.php?id=<?php echo $ag["id"]?>" target="_blank">PDF anzeigen</a><br><br>

<?php include 'admin/footer.php'; ?>