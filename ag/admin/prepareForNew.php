
<?php

	include '../db.php';
	require_once("../funktionen.php");
	
	session_start();
	
	$prefix = $_REQUEST["prefix"];
	$suffix = $_REQUEST["suffix"];
	
	$title =  "Tabellen für neuen Anmeldezeitraum vorbereiten";
	include 'header.php';
	
	if (!empty($prefix) || !empty($suffix)) {

		try {
			
			$dateString = date ("Ymd_His_");
			
			//Sichern
			AgModel::copyTables($dateString);
			NeueAgModel::copyTables($dateString);
			echo 'AG-/NeueAG-Tabellen gesichert. Prefix: '.$dateString.'<br>';
			
	 		AgModel::copyTables($prefix, $suffix);
	 		NeueAgModel::copyTables($prefix, $suffix);
	 		
	 		//'Probe-Anlegen'
	 		$neueAGs = NeueAgModel::getAgs('order by ag_nummer');
	 		foreach ($neueAGs as $neueAG) {
	 			echo 'Test-Anlegen von '.$neueAG['ag_name'] .'<br>';
	 			AgModel::insertAGFromNeueAG($neueAG);
	 		}
	 		AgModel::emptyTables();
		 		
	 		$neueAGs = NeueAgModel::getAgs('order by ag_nummer');
	 		foreach ($neueAGs as $neueAG) {
	 			echo 'Anlegen von '.$neueAG['ag_name'] .'<br>';
	 			AgModel::insertAGFromNeueAG($neueAG);
	 		}
	
	 		NeueAgModel::emptyTables();
		} catch (Exception $e) {
			echo 'Exception abgefangen: ',  $e->getMessage(), "<br>";
		}		
		
 		echo "Tabellen wurden kopiert!";
	} else {
		?>
		<ul class="bullets">
		<li><b>Alle beteiligten Tabellen werden gesichert</b></li>
		<li>Die Tabellen für die aktuelle Anmeldung ag, anmeldung, anmeldungfuerag werden in kopiert nach &lt;Prefix&gt;&lt;Tabellenname&gt;</li>
		<li>Die Tabellen werden geleert</li>
		<li>Die AGs werden aus der neuenAG-Tabelle in die Ag-Tabelle kopiert.</li>
		</ul>
		<form method="get" action="prepareForNew.php">
		Prefix für aktuellen Zeitraum: <input type="text" value="" size="20" name="prefix"> (z.B. F16_ für Frühjahr 2016)<br><br>
		Suffix: <input type="text" value="" size="20" name="suffix"><br><br>
		<input type="submit" value="OK" name="submit">
		</form>
		<?php 
	}
	
	?>
	<br><br><a type="button" href="index_admin.php">Zurück zu den Admin-Seiten</a>
	<?php 
		
	
	include 'footer.php';
?>
<script type="text/javascript">
setContentTableVisible();
</script>


