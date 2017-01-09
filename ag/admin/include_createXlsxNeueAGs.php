<?PHP
include_once("../ext/xlsx/xlsxwriter.class.php");

$ags = NeueAgModel::getAgs();

$header = array(
	'Nr.'=>'string',
	'Titel'=>'string',
	'Klassen'=>'string',
	'Termine'=>'string',
	'Termin 1'=>'string',
	'Wo-Tag'=>'string',
	'Zeit1'=>'string',
	'Termin 2 (Zusatz)'=>'string',
	'Wo-Tag2'=>'string',
	'Zeit2'=>'string',
	'Termin 3 (Ersatz)'=>'string',
	'Ort'=>'string',
	'Leitung'=>'string',
	'Kosten'=>'string',
	'Gebühr M'=>'string',
	'gebühr NM'=>'string',
	'max.Teiln.'=>'string',
	'Verantwortlich'=>'string',
	'Text'=>'string',
	'Klasse1'=>'string',
	'Klasse2'=>'string',
	'Klasse3'=>'string',
	'Klasse4'=>'string'		
);

$data1 = array();

foreach ($ags as $ag) {
	$agArray = array();
	$nr = $ag["ag_nummer"];
	if (!isset($nr) || empty($nr)) {
		$nr = $ag["id"];
	}
	array_push($agArray, $nr);
	array_push($agArray, $ag["ag_name"]);
	array_push($agArray, formatKlassen($ag));

	$terminArray = array();
	array_push($terminArray, formatSQLDate($ag["termin"],true) . " von " .  $ag["termin_von"] . "-" . $ag["termin_bis"]);
	if (isset($ag["termin_ueberbuchung"])) {
		array_push($terminArray, "Zusatztermin: " . formatSQLDate($ag["termin_ueberbuchung"],true) . " von " .  $ag["termin_von"] . "-" . $ag["termin_bis"]);
	}
	
	array_push($agArray, implode("\n",$terminArray));
	
	array_push($agArray, explode(",", formatSQLDate($ag["termin"],true))[1]);
	array_push($agArray, explode(",", formatSQLDate($ag["termin"],true))[0]);
	array_push($agArray, $ag["termin_von"] . "-" . $ag["termin_bis"]);
	
	array_push($agArray, explode(",", formatSQLDate($ag["termin_ueberbuchung"],true))[1]);
	array_push($agArray, explode(",", formatSQLDate($ag["termin_ueberbuchung"],true))[0]);
	array_push($agArray, $ag["termin_von"] . "-" . $ag["termin_bis"]);
	
	array_push($agArray, explode(",", formatSQLDate($ag["termin_ersatz"],true))[1]);

	array_push($agArray, $ag["ort"]);
	array_push($agArray, $ag["namen"]);
	array_push($agArray, ""); //Kosten
	array_push($agArray, formatAsCurrency($ag["betrag_mitglied"]));
	array_push($agArray, formatAsCurrency($ag["betrag_nicht_mitglied"]));
	
	array_push($agArray, $ag["max_kinder"]);
	array_push($agArray, $ag["verantwortlicher_name"]);
	
	array_push($agArray, implode("\n", array($ag["text_ausschreibung"],$ag["wichtige_infos"] )));
	
	array_push($agArray, $dir . ($ag["klasse1"] == 1 ? " 1 " : "   "));
	array_push($agArray, $dir . ($ag["klasse2"] == 1 ? " 2 " : "   "));
	array_push($agArray, $dir . ($ag["klasse3"] == 1 ? " 3 " : "   "));
	array_push($agArray, $dir . ($ag["klasse4"] == 1 ? " 4 " : "   "));
	
	array_push($data1, $agArray);
}

$writer = new XLSXWriter();
$writer->writeSheet($data1,'Tabelle1', $header);
$writer->writeToStdOut();
?>