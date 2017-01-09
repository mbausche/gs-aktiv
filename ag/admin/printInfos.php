<?php

	include '../db.php';
	require_once("../funktionen.php");
	require_once('../ext/html2pdf/html2pdf.class.php');
		
	ini_set("log_errors", 1);     /* Logging "an" schalten */
	ini_set("error_log", "errorlog.txt");     /* Log-Datei angeben */
	
	
	$id = $_REQUEST['ag'];
	if ($id == "") {
		die("Es wurde keine AG ausgewählt!");
	}
	
	$preview = $_REQUEST['preview'] == "true";
	$absageGrund = $_REQUEST['absageGrund'];
	
	$ags = AgModel::getAnmeldungenForAg($id);
	
	$types = array("telefon","passt","schule","mail","bank");
	
	$anmeldungByCommType = array();
	
	
	foreach ($types as $type) {
		$anmeldungByCommType[$type] = array();
		foreach ($ags as $ag) {
			$id_anmeldung = $ag['id'];
			if (isset($_REQUEST[$type . '_' . $id_anmeldung])) {
				array_push($anmeldungByCommType[$type], $ag);
			}
		}
	}

	ob_start();
	
	header('Content-type: application/pdf; charset=utf-8');
	
	echo "<page>";
	?>
	<style type="text/css">
	<!--
	table, th, td {
   		border: 1px solid black;
	}
	
	td, th {
	 	padding: 2mm;
	}
	
	table {
		border-collapse: collapse;
	}
	
	</style>
	<?php 
	
	if ($preview) {
		echo "<h1>PREVIEW</h1>";
	}
	
	echo "<h2>Folgende Schüler sollten noch TELEFONISCH informiert werden:</h2> <br>";
	$arr = $anmeldungByCommType["telefon"];
	include 'include_common_infos.php';

	echo "<h2>Für Folgende Schüler passt alles:</h2> <br>";
	$arr = $anmeldungByCommType["passt"];
	include 'include_common_infos.php';

	echo "<h2>Folgende Überweisungen sollten noch durchgeführt werden</h2> <br>";
	$arr = $anmeldungByCommType["bank"];
	include 'include_bank_infos.php';
	
	$arr = $anmeldungByCommType["schule"];
	if (count($arr) == 0) {
		echo "<h2>In die Schule muss nichts mitgegeben werden.</h2>";
	}

	$arr = $anmeldungByCommType["mail"];
	if (count($arr) == 0) {
		echo "<h2>Mails werden keine versendet.</h2>";
	}	
	
	echo "</page>";
	
	$arr = $anmeldungByCommType["schule"];
	if (count($arr) > 0) {
		
		if (count($arr) == 1) {
		?>
		<page><h2>Das folgende Blatt ist für die Weitergabe in die Schule bestimmt</h2></page>
		<?php } else {
		?>
		<page><h2>Die folgenden <?php echo count($arr)?> Blätter sind für die Weitergabe in die Schule bestimmt</h2></page>
		<?php }
		
		foreach ($arr as $ag) { ?>
			<page>
			<?php 
			include "include_schul_infos.php";
			textInTable($text,"120mm","60mm","45mm"); 
			?>
			</page> 
			<?php 
			}
	}
		

	$arr = $anmeldungByCommType["mail"];
	if (count($arr) > 0) {
		echo "<page  orientation='landscape'>";
		echo "<h2>Mails (nur zur Sicherheit)</h2><table>";
		foreach ($arr as $ag) { ?>
				<?php 
				include "include_schul_infos.php";
				
				ob_start();
				textInTable($text,"120mm","60mm","45mm"); 
				//textInTable($text,"600px","200px","180px");
				$text = ob_get_clean();
								
				$subject = "Eltern-AG " . $bez. " für  ". $name . " Klasse ". $klasse . ": " . $statusText;
				if ($preview) {
					$result = "Not sent. Preview active";
				} else {
					$attachments = array();
					$attachmentNames = array();
					
					if (AgModel::istGebucht($ag,"status_anmeldung")) {
						$ics = getIcs($ag, false, false);
						if ($ics !== false) {
							$file = $ag["anmelde_nummer"] . "_" .  $ag["id"] .".ics";
							$localFile = "ics/" .  $file;
							file_put_contents($localFile, $ics);
							array_push($attachments, $localFile);
							array_push($attachmentNames, toUtf($file));
						}
					}
					
					$result = sendMail($mail, $name, $subject, $text,$attachments, $attachmentNames);
				}
				
				?>
				<tr>
					<td><b><?php echo $name?></b></td>
					<td><b><?php echo $klasse?></b></td>
					<td><b><?php echo $mail?></b></td>
					<td><b><?php echo $termin?></b></td>
				</tr>
				<tr><td><b>Betreff:</b></td><td colspan="4"><?php echo $subject?></td></tr> 
				<tr><td valign="top"><b>Mail-Text</b><br></td><td colspan="4"><?php echo $text?></td></tr>
				<tr><td valign="top"><b>Status</b><br></td><td colspan="4"><?php echo $result?></td></tr>
				<tr><td colspan="5"><br><hr><br></td></tr>
				<?php 
		}
		echo "</table></page>";
	}
		
	$pdfContent = ob_get_clean();
	
	$oben=30;    //mT
	$unten=10;   //mB
	$links=15;   //mL
	$rechts=15;  //mR
	
	$html2pdf = new HTML2PDF('P','A4','de', true, 'UTF-8', array($links, $oben, $rechts, $unten));
	$html2pdf->pdf->SetDisplayMode('real');
	$html2pdf->WriteHTML($pdfContent);
	$html2pdf->Output($pdf);	
	?>
	
