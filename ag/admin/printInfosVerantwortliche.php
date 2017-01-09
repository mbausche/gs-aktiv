<?php
	include '../db.php';
	require_once("../funktionen.php");
	require_once('../html2pdf/html2pdf.class.php');

	
	$preview = $_REQUEST['preview'] == 'true';
	
	$id = $_REQUEST['ag'];
	if ($id == "") {
		die("Es wurde keine AG ausgewählt!");
	}
	
	$agInDB = AgModel::getAg($id);
	$ags = AgModel::getAnmeldungenForAg($id, " order BY klasse");
	
	$bez = "'" . $agInDB['ag_nummer'] . " - " . $agInDB['name'] . "'";
	$anrede = $agInDB['verantwortlicher_name'];
	$anzahl = $agInDB['max_kinder'];
	if (empty($anzahl)) {
		$anzahl = " - KEINE - ";
	}
	$array = explode(" ", $anrede);
	if (count($array > 0)) {
		$anrede = $array[0];
	}

	ob_start();
	if ($_REQUEST["rueckmeldung"] == "true") {
		$subject = "Benötigte Rückmeldung für die Eltern-AG " . $bez;
		include "template_verantwort_rueckmeldung.php";
	} else {
		$subject = "Informationen für die Eltern-AG " . $bez;
		include "template_verantwort_final.php";
	}
	$mailText = ob_get_clean();
	
	
	$states = array("zusage","termin2","absage","ersatztermin","nicht_geprueft");
	$termineForState = array("zusage" => "termin","termin2" => "termin_ueberbuchung","absage" => "","ersatztermin" => "termin_ersatz","nicht_geprueft" => "");
	
	$anmeldungByCommState = array();
	foreach ($states as $state) {
		$anmeldungByCommState[$state] = array();
		foreach ($ags as $ag) {
			$stateInDB = $ag['status_mail'];
			if ($stateInDB == $state) {
				array_push($anmeldungByCommState[$state], $ag);
			}
		}
	}

	ob_start();
	
	header('Content-type: application/pdf; charset=utf-8');
	
	?>
	
	
	<style type="text/css">
	<!--

	h2
	{
		margin-top: 15mm;
	}	
	
	.error
	{
	color: red;
	}
	
	
	-->
	</style>	
	
	<?php 
	
	echo "<page>";
	if ($preview) {
		echo "<h1>PREVIEW</h1>";
		echo "<h2>Mail</h2>";
		echo "<b>Betreff: " . $subject . "</b><br><br>";
		echo $mailText;
		echo "<h2>Anhang</h2>";
	}	
	
	ob_start();
	
	foreach ($anmeldungByCommState as $keyState => $valueAgArray) {
		
		if (empty($termineForState[$keyState]) || !empty($agInDB[$termineForState[$keyState]])) {


		$termin = AgModel::getTerminForStatus($agInDB, $keyState);
		if (!empty($termin)) {
			$termin = " am " . $termin;
		}
		
		echo "<h2>" . AgModel::getStatusText($keyState) . " " . $termin . "</h2>";
		if (count($valueAgArray) == 0) {
			echo "--- Kein Eintrag --- ";
		} else {
			?>
			
			<table>
			<thead>
			<tr>
				<td style="width:10mm"><b>Nr</b></td>
				<td style="width:30mm"><b>Name</b></td>
				<td style="width:15mm"><b>Klasse</b></td>
				<td style="width:30mm"><b>Telefon</b></td>
				<td style="width:30mm"><b>Mail</b></td>
				<td style="width:20mm"><b>Keine Bilder</b></td>
			</tr>
			</thead>
			<tbody>
			<?php 
			
			foreach ($valueAgArray as $key => $ag) {
				
				$name = $ag['schueler_name'];
				$klasse = $ag['klasse'];
				$telefon = $ag['telefon'];
				$mail = $ag['mail'];
				$keineBilder = $ag['fotos_ok'] == 0 ? "X": "&nbsp;"; 
				$css = $ag['fotos_ok'] == 0 ? "error": "";
				
				?>
				
				<tr>
				<td class="<?php echo "$css"?>"><?php echo ($key+1)?></td>
				<td class="<?php echo "$css"?>"><?php echo $name?></td>
				<td class="<?php echo "$css"?>"><?php echo $klasse?></td>
				<td class="<?php echo "$css"?>"><a href="tel:<?php echo $telefon?>"><?php echo $telefon?></a></td>
				<td class="<?php echo "$css"?>"><a href="mailto:<?php echo $mail?>"><?php echo $mail?></a></td>
				<td class="<?php echo "$css"?>"><?php echo $keineBilder?></td>
				</tr>
				
				<?php 
				
			}
			
			echo "</tbody></table>";
			
			//Mailto-Link erzeugen
			$mailArray = array();
			foreach ($valueAgArray as $key => $ag) {
				$mailArray[$ag['mail']] = $ag['mail'];
			}
			
			sort($mailArray);
			$mail = implode(";", $mailArray);
			?>
			<a href="mailto:<?= $mail ?>">Mail an alle Eltern (Für diesen Termin)</a>
			<?php 
			}
		}
	}
	
	if (!empty($agInDB['kommentar'])) {
		echo "<h2>Kommentar:</h2>";
		echo str_replace("\n", "<br><br>", $agInDB['kommentar']);
	}
	
	$pageContent = ob_get_clean();
	
	$title = "<h1>" . $bez . "</h1>";
	$title = $title . "<table><tr><td><b>Anzahl Kinder pro Termin</b></td><td>".$anzahl. "</td></tr>";
	$title = $title . "<tr><td><b>Termin 1</b></td><td>". AgModel::getTerminForStatus($agInDB, "zusage",true,"-- KEINER --") . "</td></tr>";
	$title = $title . "<tr><td><b>Termin 2</b></td><td> ". AgModel::getTerminForStatus($agInDB, "termin2",true,"-- KEINER --"). "</td></tr>";
	$title = $title . "<tr><td><b>Ersatz-Termin</b></td><td>".AgModel::getTerminForStatus($agInDB, "ersatztermin",true,"-- KEINER --"). "</td></tr></table><br>";
	$title = $title . "<h2>Aktuelle Teilnehmer</h2>";
	
	
	echo textInTableWithTitle($title, $pageContent);
	echo "</page>";

	$pdfContent = ob_get_clean();
	
	$oben=30;    //mT
	$unten=10;   //mB
	$links=15;   //mL
	$rechts=15;  //mR
	
	$pdf = "../pdf/" . $agInDB['ag_nummer'] . ".pdf";
	
	$html2pdf = new HTML2PDF('P','A4','de', true, 'UTF-8', array($links, $oben, $rechts, $unten));
	$html2pdf->pdf->SetDisplayMode('real');
	$html2pdf->WriteHTML($pdfContent);
	
	if ($_REQUEST['preview'] == 'true') {
		$html2pdf->Output($pdf);
	} else {
		$html2pdf->Output($pdf,'F');
		
		$resp = sendMail($agInDB['verantwortlicher_mail'], $agInDB['verantwortlicher_name'], $subject, $mailText, array($pdf));
		$result = "Die Mail wurde versandt<br><br>Antwort vom Mailserver:<br><br>" . $resp;
		
		if ($_REQUEST["rueckmeldung"] == "true") {
			AgModel::appendComment($id,"","Rückmeldungsmail verschickt am " . formatCurrentDate() . ".\nAntwort vom Mailserver: " .$resp);
		} else {
			AgModel::appendComment($id,"","Infomail an Veranstalter verschickt am " . formatCurrentDate() . ".\nAntwort vom Mailserver: " .$resp);
		}

		
		
		$host  = $_SERVER['HTTP_HOST'];
		$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		$extra = "manageAG.php?ag=".$id."&msg=".$result. "&title=Mail wurde versandt!";
		header("Location: http://$host$uri/$extra");
	}
	
	
	
	?>
	
