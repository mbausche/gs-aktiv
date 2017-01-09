<?php
	session_destroy();

	include '../db.php';
	require_once("../funktionen.php");
	
	ini_set("log_errors", 1);     /* Logging "an" schalten */
	ini_set("error_log", "errorlog.txt");     /* Log-Datei angeben */

	$agsPerTable = NeueAgModel::getAgsVorjahre();
	
	$tableId = "";
	$title =  "Eltern-AGs importieren";
	
	include 'header.php';
	
	$messages = array();
	
	if (!empty($_POST['import'])) {
		foreach ($_REQUEST as $name => $value) {
			if (startsWith($name, "select_")) {
				$tmp = explode("_", $value);
				array_push($messages, NeueAgModel::insertFromArchive($tmp[0] . "neueag", $tmp[1]));
			}
		}
	}
	
?>

<script>

$(document).ready(function() {

	var table = $('#agTable').DataTable( {
		fixedHeader: {
	        header: true,
	        footer: false
	    },
        "paging":   false,
        "info": true,
        "bJQueryUI": true,
        "sDom": 'lfrtip'
        <?php echo "," . $dataTableLangSetting?>
    } );	


	<?php renderLastSearchHandling("table","importNeueAGs.php_agTable");?>

	setContentTableVisible();
	
});
</script>

<?php foreach ($messages as $m) { ?>
<?= $m ?><br>
<?php } ?>
<br>
<form id="mainForm" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">

<input type="submit" name="import" value="Importieren">&nbsp;
<a href="manageNeueAGs.php" type="button">Neue AGs</a>&nbsp;
<br>
<table id="agTable" class="display">
    <thead>
        <tr>
        	<th>Import?</th>
            <th>Nr</th>
        	<th>AG</th>
        	<th>Termine & Uhrzeit</th>
        	<th>Name</th>
        	<th>Verantwortlich</th>
        	<th>Infos</th>
        	<th>Max. Kinder</th>
        	<th>Anz. Helfer</th>
        	<th>Klassen</th>
        	<th>Betrag<br>Mitgl./Nicht-Mitgl.</th>
        	<th>Ort</th>
        	<th>Bild</th>
        </tr>
    </thead>
    <tbody>
		<?php 
		foreach ($agsPerTable as $table => $ags) {
			foreach ($ags as $ag) {
		?>
		
		<tr>
			<td><input type="checkbox" name="select_<?php echo $table ?>_<?php echo $ag['id']?>" value="<?php echo $table ?>_<?php echo $ag['id']?>"></td>
			<td><?php echo $ag['ag_nummer']?></td>
        	<td><?php echo $ag['ag_name']?></td>
        	<td><nobr><?php echo formatSQLDate($ag['termin'],true) . " " . $ag['termin_von'] . "-" . $ag['termin_bis'] ?></nobr><br>
        	<?php echo formatNotEmpty("2. Termin: ","<br>",formatSQLDate($ag['termin_ueberbuchung'],true))?>
        	<?php echo formatNotEmpty("Ersatz: ","<br>",formatSQLDate($ag['termin_ersatz'],true))?>
        	</td>
        	<td><?php echo $ag['namen']?></td>
        	<td><?php echo $ag['verantwortlicher_name']?><br><?php echo $ag['verantwortlicher_telefon']?><br><?php echo formatAsMailto($ag['verantwortlicher_mail'])?></td>
        	<td><?php echo str_abbreviate($ag['text_ausschreibung'],75)?><?php echo formatWithCaption("Wichtige Infos",str_abbreviate($ag["wichtige_infos"],50))?><?php echo formatWithCaption("Ausserdem",str_abbreviate($ag["ausserdem"],50))?></td>
        	<td><?php echo formatMaxKinder($ag['max_kinder'])?></td>
        	<td><?php echo $ag['anzahl_helfer']?></td>
        	<td><?php echo formatKlasse($ag,'klasse1')?><?php echo formatKlasse($ag,'klasse2')?><?php echo formatKlasse($ag,'klasse3')?><?php echo formatKlasse($ag,'klasse4')?></td>
        	<td><?php echo formatAsCurrency($ag['betrag_mitglied'])?>/<?php echo formatAsCurrency($ag['betrag_nicht_mitglied'])?></td>
        	<td><?php echo $ag['ort']?></td>
        	<td><?php echo getImageLink("true",$ag, "../","max-width:50px")?></td>
        </tr>
        <?php } } ?>
    </tbody>
</table>
<input type="submit" name="import" value="Importieren">
</form>
<?php include 'footer.php';?>

