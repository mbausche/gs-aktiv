<?php error_log("readOnly.php")

?>

<?php if ($html == true) { 
	header('Content-type: text/html; charset=utf-8');
} else {
	header('Content-type: application/pdf; charset=utf-8');
} 

if ($html == true) { 

	$title = $ag["ag_name"];
	include 'header.php';
?>
	<link rel="stylesheet" href="css/main.css">
	<style type="text/css">
	<!--
	table.ags td
	{
	    font-size:    8pt;
	    padding-right: 1mm;
	}
	
	table.sonstiges td
	{
		font-family:sans-serif;
		padding-bottom: 2mm;
	
	}
	
	table.agsLarge td
	{
		font-size:    12pt;
	    padding-right: 3mm;
	}
	
	.small
	{
		font-size:    8pt;
	}
	
	.title
	{
		font-size:    22pt;
	    font-weight: bold;
	}
	-->
	</style>

<?php } else { ?>
<style type="text/css">
<!--
table.ags td
{
    font-size:    8pt;
    padding-right: 1mm;
}

table.sonstiges td
{
    padding-bottom: 2mm;

}

table.agsLarge td
{
    font-size:    10pt;
    padding-right: 3mm;
}

.small
{
    font-size:    8pt;
}

.title
{
    font-size:    22pt;
    font-weight: bold;
}

.sonstigePunkte {
   margin-bottom: 10pt;
   text-indent: 30pt;
}

-->
</style>

<?php } ?>

<?php
	require_once("funktionen.php");

	
	
	$felder = array();
	$felder["AG-Nummer"]  = empty($ag["ag_nummer"]) ? "noch nicht bekannt" : $ag["ag_nummer"];
	$felder["Namen"]  = $ag["namen"];
	$felder["Kontaktperson"]  = $ag["verantwortlicher_name"];
	$felder["Mail"]  = formatAsMailto($ag["verantwortlicher_mail"]);
	$felder["Telefon"]  = $ag["verantwortlicher_telefon"];
	$felder["Text"]  = $ag["text_ausschreibung"];
	$felder["Wichtige Infos"]  = $ag["wichtige_infos"];
	$felder["1. Termin"]  = formatSQLDate($ag["termin"]);
	$felder["2. Termin"]  = formatSQLDate($ag["termin_ueberbuchung"]);
	$felder["Ersatztermin"]  = formatSQLDate($ag["termin_ersatz"]);
	$felder["Uhrzeit"]  = $ag["termin_von"] . "-" . $ag["termin_bis"] . " Uhr";
	$felder["Max. Anzahl Teilnehmer"]  = $ag["max_kinder"];
	$felder["Benötigte Helfer"]  = $ag["anzahl_helfer"];
	$felder["Klasse 1"]  = formatAsYesNo($ag["klasse1"]);
	$felder["Klasse 2"]  = formatAsYesNo($ag["klasse2"]);
	$felder["Klasse 3"]  = formatAsYesNo($ag["klasse3"]);
	$felder["Klasse 4"]  = formatAsYesNo($ag["klasse4"]);
	$felder["Teilnahmebetrag Mitglieder / NichtMitglied"] = formatAsCurrency($ag["betrag_mitglied"]) . " / " . formatAsCurrency($ag["betrag_nicht_mitglied"]);
	$felder["Ort"] = $ag["ort"];
	$felder["Das möchte ich euch noch mitteilen"]  = $ag["ausserdem"];
	
	if ($html == true)
		$teilnahme = "<table class='ags' style='min-width:50%'>";
	else
		$teilnahme = "<table class='ags'>";
	
	$felderAnzahl = count($felder);

	$imgLink = getImageLink($html,$ag,"./");
		
	foreach ($felder as $key => $value) {
		$tmp = formatAsCellText($value);
		if ($html == true)
			$teilnahme = $teilnahme . "<tr><td><b>$key</b></td><td>$tmp</td></tr>\n";
		else 
			$teilnahme = $teilnahme . "<tr><td style='width:60mm'><b>$key</b></td><td style='width:100mm'>$tmp</td></tr>\n";
			
	}
	$teilnahme = $teilnahme . "</table>\n";
	$teilnahme = $teilnahme . "$imgLink\n";
	
?>	

<?php if ($html == true) { ?>
	<?php
		echo $teilnahme;
		include 'admin/footer.php';
	?>
<?php } else { ?>
	<page>
	<h1><?php echo $ag["ag_name"] ?></h1>
	<?php error_log($teilnahme); 
	echo $teilnahme;?>
	</page>
<?php } ?>
 
