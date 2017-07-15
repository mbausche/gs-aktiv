<?php error_log("readOnly.php")

?>


<?php 
if ($header == true) {
	if ($html == true || $bootstrap == true) { 
		header('Content-type: text/html; charset=utf-8');
	} else {
		header('Content-type: application/pdf; charset=utf-8');
	} 
}

if ($html == true) { 

	$title = "Eltern-AG Anmeldung";
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
	<script type="text/javascript">
	$(document).ready(function() {
		showHideStepTable();
	});
	</script>

<?php } else if ($bootstrap == true) { 
	$title = "Eltern-AG Anmeldung";
	include 'header_bootstrap.php';
	?>
	
<?php 
} else { ?>
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

	$name = $_SESSION['vorname'] . " " . $_SESSION['nachname'];
	$klasse = $_SESSION['klasse'];
	$telefon = $_SESSION['telefon'];
	$mail = $_SESSION['mail'];
	$mitglied = $_SESSION['mitglied'];
	if (empty($mitglied))
		$mitglied = 'Nein';
	
	if (empty($_SESSION['zahlart'])) {
		$_SESSION['zahlart'] = "schule";
	}
	$zahlart = $_SESSION['zahlart'];
	
	$summe = $_SESSION['summe'];
	
	$sonstiges = array();
	
	
	if ($zahlart == 'schule') {
		array_push($sonstiges,"Ich lege die Teilnahmegebühr/en in Höhe von <b>$summe &euro;</b> meiner Anmeldung bei.");
	} else {
		$link = CfgModel::load("paypallink") . "/" . $summe;
		array_push($sonstiges,"Ich überweise die Teilnahmegebühr/en in Höhe von <b>$summe &euro;</b> auf das Konto des Fördervereins");
		array_push($sonstiges,"Alternativ kann ich auch <img src='images/paypal.png' height='32'> verwenden");
		array_push($sonstiges,"Als Verwendungszweck gebe ich an: Die Anmeldenummer, Name und Klasse meines Kindes");
	}
	
	if ($_SESSION['wirWollenMitgliedWerden'] == "Ja") {
		array_push ($sonstiges,TEXT_WILL_MITGLIED_WERDEN);
	}
	if ($_SESSION['keineBilder'] == "Ja") {
		array_push ($sonstiges,TEXT_KEINE_FOTOS);
	}
	if ($_SESSION['bilder'] == "Ja") {
		array_push ($sonstiges,TEXT_FOTOS);
	}
	/*
	if ($_SESSION['sendConfirmation'] == "Ja") {
		array_push ($sonstiges,TEXT_SEND_CONFIRMATION);
	}
	*/
	if ($_SESSION['mithilfeBeiAktuellerAG'] != "") {
		array_push ($sonstiges,TEXT_MITHILFE  ." " . $_SESSION['mithilfeBeiAktuellerAG']);
	}
	if ($_SESSION['ideeFuerNeueAG'] != "") {
		array_push ($sonstiges,TEXT_IDEE . "  " . $_SESSION['ideeFuerNeueAG']);
	}
	
	array_push($sonstiges, TEXT_NACH_HAUSE_WEG);
	array_push($sonstiges, TEXT_1_EURO);
	
	$ID = preg_replace('/\s+/', '_', $name) . "_" . date("Ymd");
	
	if ($html == true)
		$teilnahme = "<table class='ags' style='min-width:50%'>";
	else if ($bootstrap == true)
		$teilnahme = '<table class="table table-striped table-bordered">';
	else
		$teilnahme = "<table class='ags' width='100%'>";
	
	
	if ($mitglied == 'ja') {
		$labelMitglied = "Kosten Mitglieder";
	} else {
		$labelMitglied = "Kosten Nicht-Mitglieder";
	}
	
	if ($zahlart == 'schule') {
		$labelZahlart = "Wie bisher über die Schule";
	} else {
		$labelZahlart = "Per Überweisung";
	}
	
	if ($bootstrap == true) {
		$kosten = "<th align='right'>$labelMitglied</th>";
	} else {
		$kosten = "<td align='right'><b>$labelMitglied</b></td>";
	}
	
	if ($bootstrap == true) {
		$teilnahme = $teilnahme."<thead><tr><th>Ag-Nr.</th><th>Name</th><th colspan='3'>Termin</th>";
	} else {
		$teilnahme = $teilnahme."<tr><td><b>Ag-Nr.</b></td><td><b>Name</b></td><td colspan='3'><b>Termin</b></td>";
	}
	if ($html == true) {
		$teilnahme = $teilnahme."<td colspan='2'><b>Zusatztermin</b></td>";
		$teilnahme = $teilnahme."<td colspan='2'><b>Ersatztermin</b></td>";
	}
	else if ($bootstrap == true) {
		$teilnahme = $teilnahme."<th colspan='2'>Zusatztermin</th>";
		$teilnahme = $teilnahme."<th colspan='2'>Ersatztermin</th>";
	}
	
	if ($bootstrap == true) {
		$teilnahme = $teilnahme."<th><b>Ort</b></th>".$kosten."</tr>";
	} else {
		$teilnahme = $teilnahme."<td><b>Ort</b></td>".$kosten."</tr>";
	}
	
	
	$teilnahme = $teilnahme . "<tbody>";
	
	foreach ($ags as $ag) {
		$paramName = str_replace(" ", "_", $ag['ag_nummer']);
		if ($_SESSION[$paramName] == 'binDabei') {
			$agname = trim_text($ag['name'],35);
			$ort = trim_text($ag['ort'],35);
			$teilnahme = $teilnahme . "<tr><td>" . $ag['ag_nummer']."</td>";
			$teilnahme = $teilnahme . "<td>$agname</td>";
			
			$terminArray  = AgModel::getTerminForStatusAsArray($ag, "zusage");
			$teilnahme = $teilnahme . "<td>$terminArray[0]</td><td>$terminArray[1]</td><td>$terminArray[2]$terminArray[3]$terminArray[4]</td>";
			if ($html == true || $bootstrap == true) {
				$terminArray  = AgModel::getTerminForStatusAsArray($ag, "termin2");
				$teilnahme = $teilnahme . "<td>$terminArray[0]</td><td>$terminArray[1]</td>";
				$terminArray  = AgModel::getTerminForStatusAsArray($ag, "ersatztermin");
				$teilnahme = $teilnahme . "<td>$terminArray[0]</td><td>$terminArray[1]</td>";
			}
				
			$teilnahme = $teilnahme . "<td>$ort</td>";
			if ($mitglied == "ja")
				$teilnahme = $teilnahme . "<td align='right'>".formatAsCurrency($ag['betrag_mitglied'])."</td></tr>";
			else
				$teilnahme = $teilnahme . "<td align='right'>".formatAsCurrency($ag['betrag_nicht_mitglied'])."</td></tr>";
				
		}
	}	
	
	$span = $html || $bootstrap ? 10 : 6;
	
	$teilnahme = $teilnahme . "<tr><td colspan='".$span."'><b>Summe:</b></td><td align='right'><b>". $summe ." &euro;</b></td></tr></tbody></table>";
?>	

<?php if ($html == true) { ?>
	<table>
	<tr><td valign="top">
	<table class="adresse">
	<?php  
	addContact("Name",$name);
	addContact("Klasse",$klasse);
	addContact("Telefon",$telefon);
	addContact("E-Mail",$mail);
	addContact("Mitglied",$mitglied);
	addContact("Zahlart",$labelZahlart);
	?>
	</table>
    </td>
    <td></td>
    <td>
    </td>
    </tr>
	</table>
	<h3>Mein Kind möchte an folgenden AGs teilnehmen</h3>
	<?php echo $teilnahme?>
	<h3>Sonstiges</h3>
	<?php if (array_count_values($sonstiges ) > 0) {
	echo "<ul>";
	foreach ($sonstiges as $eintrag) {
		echo "<li>- $eintrag<br>";
	} 
	echo "</ul>";
	}
	?>
	<br>
	<b>So gehts weiter</b>
	<?php renderImageTable("images/",array(2,3,4,5,6),false,false,true)?>	
    <br>
    <span id="reenterLink">Halt! Das stimmt noch was nicht. Bitte nochmal <a href="anmelden.php?reenter=true&silent=true"  type="button">Zurück zur Eingabe</a><br><br></span>
	
	</body>
	</html>
 <?php } if ($bootstrap == true) { ?>
 
  	<img src="images/heading.jpg" class="img-responsive img-rounded" alt="Header">
    <h1><?php echo $title?></h1>
 	<div class="panel panel-default">
	  <div class="panel-heading ">
	    	<span class="attention">Achtung! Die Anmeldung wurde noch nicht versandt! Überprüfen Sie bitte die Daten und Drücken sie auf</span> <a class="btn btn-info" role="button" id="insertAnmeldung" href="insertAnmeldung.php">Anmeldung abschicken</a><br>
	    	<span class="attention">Falls was noch nicht stimmt: Hier geht es </span> <a class="btn btn-info" role="button" href="anmelden2.php?reenter=true&silent=true">Zurück zur Eingabe</a>
	  </div>
	</div>
 	<div class="panel panel-default">
	  <div class="panel-heading">
	    <h3 class="panel-title">Schüler:</h3>
	  </div>
	  <div class="panel-body">
	  	<table class="table table-striped table-bordered">
			<?php  
			addContact("Name",$name);
			addContact("Klasse",$klasse);
			addContact("Telefon",$telefon);
			addContact("E-Mail",$mail);
			addContact("Mitglied",$mitglied);
			addContact("Zahlart",$labelZahlart);
			?>
	  	</table>
	  </div>
	</div>
	
	<div class="panel panel-default">
	  <div class="panel-heading">
	    <h3 class="panel-title">Mein Kind möchte an folgenden AGs teilnehmen:</h3>
	  </div>
	  <div class="panel-body">
	  	<?php echo $teilnahme?>
	  </div>
	</div>	
	
	<div class="panel panel-default">
	  <div class="panel-heading">
	    <h3 class="panel-title">Sonstiges</h3>
	  </div>
	  <div class="panel-body">
		<?php if (array_count_values($sonstiges ) > 0) {
		echo "<ul class='list-group'>";
		foreach ($sonstiges as $eintrag) {
			echo "<li class='list-group-item'>- $eintrag<br>";
		} 
		echo "</ul>";
		}?>
	  	</div>
	</div>		

	<div class="panel panel-default">
	  <div class="panel-heading">
	    <h3 class="panel-title">So geht's weiter:</h3>
	  </div>
	  <div class="panel-body">
	  	
	  	<?php if ($zahlart == "schule")
	  		renderImageTableBootstrap("images/",array(2,3,4,5,6),false,false,true);
	  	else 
	  		renderImageTableBootstrapBank("images/",array(2,3,4),false,false,true)
	  		
	  	?>
    	<span id="reenterLink">Halt! Das stimmt noch was nicht. Bitte nochmal <a class="btn btn-info" role="button" href="anmelden2.php?reenter=true&silent=true">Zurück zur Eingabe</a><br><br></span>
	  	
	  </div>
	</div>	
		
	
	</body>
	</html> 
	
<?php } ?>


<?php if ($pages[0] == true) { ?> 
<page>
	<div style="position: absolute; top: 0mm; left: 130mm "><img src='images/logo.png' style="width:45mm"/></div>
	<table style="min-width: 190mm">
	<tr><td valign="top">
    <span class="title">Anmeldung Eltern-AGs</span><br>
    <span class="small" >(Dieses Blatt in die Klasse mitgeben)</span><br><br>
	<table class="adresse">
	<?php  
	addContact("Anmeldung-Nr", $AnmeldungsId);
	addContact("Name",$name);
	addContact("Klasse",$klasse);
	addContact("Telefon",$telefon);
	addContact("E-Mail",$mail);
	addContact("Mitglied",$mitglied);
	?>
	</table>
    </td>
    <td></td>
    </tr>
	</table>
	<h3>Mein Kind möchte an folgenden AGs teilnehmen</h3>
	<?php echo $teilnahme?>
	<h3>Sonstiges</h3>
	<br>
	<?php if (array_count_values($sonstiges) > 0) {
		foreach ($sonstiges as $eintrag) {
			echo "<div class='sonstigePunkte'>$eintrag</div>";
		} 
	}
	?>
	
	
	<br><br>
	
	
	
	<br>
	<table>
	<tr><td>Datum<br><br></td><td>Unterschrift</td></tr>
	<tr><td>______________________</td><td>____________________________________________</td></tr>
	</table>
</page>

<?php 
} 

if ($pages[1] == true) {

$teilnahme = "<table class='ags' width='100%'  cellspacing='0'>
			<tr><td><b>Ag-Nr.</b></td><td><b>Name</b></td><td colspan='3'><b>Termin</b></td><td colspan='2'><b>Zusatztermin</b></td><td colspan='2'><b>Ersatztermin</b></td><td><b>Ort</b></td><td><b>Kontaktdaten</b></td></tr>";

$count = 0;

foreach ($ags as $ag) {
	$paramName = str_replace(" ", "_", $ag['ag_nummer']);
	if ($_SESSION[$paramName] == 'binDabei') {
		$count++;
		$agname = trim_text($ag['name'],35);
		$ort = trim_text($ag['ort'],35);
		if ($count % 2 == 0) {
			$teilnahme = $teilnahme . "<tr style='background-color: #C0C0C0;'>";
		} else {
			$teilnahme = $teilnahme . "<tr>";
		}
		
		$teilnahme = $teilnahme . "<td>".$ag['ag_nummer']."</td>";
		$teilnahme = $teilnahme . "<td>".$ag['name']."</td>";
		$terminArray = AgModel::getTerminForStatusAsArray($ag, "zusage");
		$teilnahme = $teilnahme . "<td>$terminArray[0]</td><td>$terminArray[1]</td><td>$terminArray[2]$terminArray[3]$terminArray[4]</td>";
		
		$terminArray = AgModel::getTerminForStatusAsArray($ag, "termin2");
		$teilnahme = $teilnahme . "<td>$terminArray[0]</td><td>$terminArray[1]</td>";
		
		$terminArray = AgModel::getTerminForStatusAsArray($ag, "ersatztermin");
		$teilnahme = $teilnahme . "<td>$terminArray[0]</td><td>$terminArray[1]</td>";

		$teilnahme = $teilnahme . "<td>$ort</td>";
		
		$teilnahme = $teilnahme . "<td>".$ag["verantwortlicher_name"]."<br>".formatAsMailto($ag["verantwortlicher_mail"])."<br>".$ag["verantwortlicher_telefon"]."</td>";
		
		$teilnahme = $teilnahme . "</tr>";
	}
}

$teilnahme = $teilnahme . "</table>";
?>

<page orientation="landscape">
    <span class="title">Übersicht Eltern-AGs <?php echo $name?></span><br>
    <span class="small" >(Für Ihre Pinnwand)</span><br><br>
	<?php echo $teilnahme?>
	<br><br>
    <div class="small" ><b>Hinweise:</b><br><br>- Den Status der Anmeldung können sie unter <a href="www.grundschule-aktiv.de/ag/abfrage.php?nummer=<?php echo $AnmeldungsId?>" target="_blank">www.grundschule-aktiv.de/ag</a> abfragen. Dazu benötigen Sie die Anmelde-Nummer <b><?php echo $AnmeldungsId?></b><br>
	- <?php echo TEXT_COMMENT_ZUSATZTERMIN?><br>
	- <?php echo TEXT_COMMENT_ERSATZTERMIN?><br>
	- <?php echo TEXT_COMMENT_UHRZEIT?><br>
	<?php if ($_SESSION['zahlart'] == 'bank') { ?>
	<br>
	- Bitte überweisen Sie den Betrag von <?php echo $summe?>&euro; auf das Konto des Fördervereins: <?php echo str_replace("<br>", " ", CfgModel::load("bankverbindung"))?><br>
	- Alternativ können sie uns das Geld auch per Paypal zusenden. Bitte verwenden Sie dazu folgenden Link: <a href="<?php echo CfgModel::load("paypallink")?>"><?php echo CfgModel::load("paypallink")?>/<?php echo $summe?></a> <?php echo CfgModel::load("paypal_kaeuferschutz")?><br>
	- <b>Verwendungszweck:</b> <?php echo "$name $klasse $AnmeldungsId" ?><br>
	- Rückerstattungen erfolgen direkt auf ihr Konto 
	<?php } ?>
	</div>
    <div style="position: absolute; top: -20mm; left: 225mm "><img src='images/logo.png' style="width:45mm"/></div>
    
	
</page>

<?php } ?>