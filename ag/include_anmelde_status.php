


<?php
if ($includeAGNummer == true) {?>
	
<h2>Anmeldung <?php echo $anmeldung['anmelde_nummer']?> <?php echo formatAsDownloadAnmeldungLink("./",$anmeldung['anmelde_nummer'])?> </h2>

<?php } ?>
<table type="anmeldung" id="anmeldung_<?php echo $anmeldung['anmelde_nummer']?>" style="width:500px;margin-left: 0px">
<thead style="display:none">
<tr>
<th></th>
<th></th>
</tr>
</thead>
<tbody>

<?php if ($includeNameAndKlasse == true) { ?>
<tr><td><nobr>Name des Kindes</nobr></td><td><?php echo $anmeldung['name']?></td></tr>
<tr><td>Klasse</td><td><?php echo $anmeldung['klasse']?></td></tr>
<?php } ?>

<tr><td>Anmeldung erzeugt am</td><td><?php echo formatSQLDateTime(AgModel::getDateOfAnmeldungsEingang($anmeldung['id']))?></td></tr>
<tr><td>Anmeldung geprüft am</td><td><?php echo formatSQLDate(AgModel::getDateOfAnmeldungsPruefung($anmeldung['id']))?></td></tr>
<tr><td>Status</td><td><?php if ($anmeldung['geprueft'] == 0) echo 'Nicht geprüft'; else echo 'Geprüft';?></td></tr>

</table>
<br>
<table type="ags" class="angemeldeteAgs" id="ags_<?php echo $anmeldung['anmelde_nummer']?>">
<thead>
<tr>
<th>Ag-Nr.</th>
<th>Name</th>
<th>Status</th>
<th>Gebucht für Termin</th>
<th>Ort</th>
<th>Auch angemeldet</th>
<th>Kontaktdaten</th>
<th>Kalender-Eintrag</th>
</tr>
</thead>
<tbody>
<?php 
$anmeldeDaten =  AgModel::getAnmeldedaten($anmeldung['anmelde_nummer']);
$anmeldeDatenSorted = array();

foreach ($anmeldeDaten as $ag) {
	$termin = AgModel::getTerminCompareStringForStatus($ag, $ag["status_mail"], "99999");
	$anmeldeDatenSorted[$termin.  "-" . $ag["ag_nummer"]] = $ag;
}

ksort($anmeldeDatenSorted);
 
foreach ($anmeldeDatenSorted as $ag) {
	$termin = AgModel::getTerminForStatus($ag, $ag["status_mail"], true, "-");
	if ($termin == "-") {
		$auch = "";
		$link = "";
	} else {
		$auch = AgModel::getAndereKinder($ag["ag_nummer"], $ag["status_mail"], $anmeldung['name']);
		$link = "<a href='phpToIcs.php?id=".$ag["id"]."'><img src='images/calendar_32.png'></a>"; 
	}
	
	echo "<tr>";
	echo "<td>".$ag["ag_nummer"]. "</td>";
	echo "<td>".$ag["ag_name"]."</td>";
	echo "<td>".AgModel::getStatusText($ag["status_mail"])."</td>";
	echo "<td>".$termin."</td>";
	echo "<td>".$ag["ort"]."</td>";
	echo "<td type='auch'>".$auch."</td>";
	echo "<td>".$ag["verantwortlicher_name"]."<br>".formatAsMailto($ag["verantwortlicher_mail"])."<br>".$ag["verantwortlicher_telefon"]."</td>";
	echo "<td align='center'>$link</td>";
	echo "</tr>";
}
?>
</tbody>
</table>
