<?php
	require_once("funktionen.php");
	require_once("db.php");
	
	$anmeldeDaten =  AgModel::getAnmeldedaten($_REQUEST['nummer']);
	$anmeldung = AgModel::getAnmeldung($_REQUEST['nummer']);

	if (count($anmeldeDaten) == 0 || empty($_REQUEST['nummer'])) {
		include 'include_404.php';
		die();
	}
	
	header('Expires: Thu, 19 Nov 1981 08:52:00 GMT');
	header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
	header('Pragma: no-cache');
	
	if (empty($_REQUEST['debug'])) {
		header('Content-type: text/calendar; charset=utf-8');
		header('Content-Disposition: inline; filename='.$_REQUEST['nummer'].'.ics');
	} else {
		header('Content-type: text/plain');
	}


?>
BEGIN:VCALENDAR
VERSION:2.1
PRODID:-//localhost//NONSGML grundschule-aktiv.de//
METHOD:PUBLISH
X-WR-CALNAME:Eltern AGs <?php echo $anmeldung['name']."\n"?>
X-WR-CALDESC:Eltern AGs <?php echo $anmeldung['name']."\n"?>
X-WR-TIMEZONE:Europe/Berlin
BEGIN:VTIMEZONE
TZID:Europe/Berlin
X-LIC-LOCATION:Europe/Berlin
BEGIN:DAYLIGHT
TZOFFSETFROM:+0100
TZOFFSETTO:+0200
TZNAME:CEST
DTSTART:19700329T020000
RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=-1SU
END:DAYLIGHT
BEGIN:STANDARD
TZOFFSETFROM:+0200
TZOFFSETTO:+0100
TZNAME:CET
DTSTART:19701025T030000
RRULE:FREQ=YEARLY;BYMONTH=10;BYDAY=-1SU
END:STANDARD
END:VTIMEZONE

<?php 
foreach ($anmeldeDaten as $ag) {
	include 'include_ag_as_icalevent.php';
}
$andere = AgModel::getAndereAnmeldungen($anmeldung['name'],$anmeldung['klasse'],$anmeldung['anmelde_nummer']);
if (count($andere) > 0) {
?>
<?php foreach ($andere as $anmeldung) {
	$anmeldeDaten =  AgModel::getAnmeldedaten($anmeldung['anmelde_nummer']);
	foreach ($anmeldeDaten as $ag) {
		include 'include_ag_as_icalevent.php';
	}
 } 
} ?>
END:VCALENDAR
