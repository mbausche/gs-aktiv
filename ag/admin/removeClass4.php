
<?php

	include '../db.php';
	require_once("../funktionen.php");

	session_start();	
	
	$title =  "4. Klässler aus der Status-Tabelle entfernen";
	include 'header.php';
	
	if (count($_REQUEST) > 0) {
		try {
			foreach ($_REQUEST as $name => $value) {
				if ($value == "delete") {
					$name = urldecode($name);
					StatusModel::delete($name,false);
					echo "Gelöscht $name<br>";
				}		
			}
		} catch (Exception $e) {
			echo 'Exception abgefangen: ',  $e->getMessage(), "<br>";
		}		
 		
	} else {
		
		$anmeldungen = AgModel::getAnmeldungenByKlasse("4");
		$names = array();
		foreach($anmeldungen as $a) {
			$name = $a["name"];
			if (!array_key_exists($name, $names)) {
				$names[$name] = $name;
			}
		}
		
		if (count($names) > 0) {
		?>
		<b>Folgende 4. Klässler werden gelöscht:</b><br>
		<form method="post" action="<?= $_SERVER['PHP_SELF']?>">
		<?php 
		foreach ($names as $name) {
		?>
			<input type="checkbox" name="<?= urlencode($name) ?>" value="delete" checked><?=$name?><br>
		<?php } ?>
		
		<input type="submit" value="OK" name="submit">
		</form>
		<?php 
		} else {
			echo "Derzeit gibt es keine 4. Klässler in der Datenbank!<br>";
		}
		
	}
	
	?>
	<br><br><a type="button" href="index_admin.php">Zurück zu den Admin-Seiten</a>
	<?php 
	
	include 'footer.php';
?>
<script type="text/javascript">
setContentTableVisible();
</script>


