<?php
	include '../db.php';
	require_once("../funktionen.php");
	
	$tableId = "";
	$title =  "Admin-Seiten";
	
	include 'header.php';
	
?>

<script>
</script>

<?php 

session_start();

if ($_REQUEST['password'] == "RV93q8" || $_SESSION["admin_index_authorized"] == "true") { 

$_SESSION["admin_index_authorized"] = "true";

?>

<a type="button" href="removeClass4.php">4. Klässler aus der Status-Tabelle entfernen</a><br><br>
<a type="button" href="prepareForNew.php">Tabelle für neues AG-Heft vorbereiten</a><br><br>

<?php } else { ?>
		<form method="post" action="index_admin.php">
		Passwort: <input type="password" value="" size="20" name="password"><br><br>
		<input type="submit" value="OK" name="submit">
		</form>
<?php } 

include 'footer.php';

?>
<script type="text/javascript">
setContentTableVisible();
</script>

