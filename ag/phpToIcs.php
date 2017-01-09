<?php

setlocale (LC_ALL, 'de_DE@euro', 'de_DE', 'de', 'ge');
require_once("funktionen.php");
require_once("db.php");

$anmeldung = AgModel::getAnmeldungsDaten($_REQUEST["id"]);

$result = getIcs($anmeldung,false);
if (result === false) {
	http_response_code(404);
	exit();
}

$filename = $anmeldung["anmelde_nummer"] . "_" .  $anmeldung["id"] .".ics";
header('Content-type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);
echo $result;