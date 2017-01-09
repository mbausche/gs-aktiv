<?php 
if (isset($_REQUEST[$typ])) {
	echo "<page>";
	if ($preview) {
		echo "<h1>PREVIEW</h1>";
	}
	$ags = AgModel::getAnmeldungenForAgWithStatus($id, $dbState);
	if (count($ags) > 0) {
		if ($terminString == "Termin") {
			echo "<h2>Mails für Terminänderung</h2>";
		} else {
			echo "<h2>Mails für Terminänderung für ". $terminString . "</h2>";
		}
		
		foreach ($ags as $ag) {
			$name = $ag['schueler_name'];
			$klasse = $ag['klasse'];
			$telefon = $ag['telefon'];
			$mail = $ag['mail'];
			if (empty($mail)) {
				echo "<h3 style='color:red'>Info-Mail an $name ($klasse) Tel $telefon wurde nicht versendet! Es existiert keine Mail-Adresse</h3>";
			}
		}
		
		echo "<table>";
		
		foreach ($ags as $ag) { ?>
							<?php 
							$status = $ag['status_anmeldung'];
							$statusText = AgModel::getStatusText($dbState);
							$termin = AgModel::getTerminForStatus($ag, $dbState);
							$name = $ag['schueler_name'];
							$bez = $ag['ag_nummer'] . " - " . $ag['ag_name'];
							$klasse = $ag['klasse'];
							$telefon = $ag['telefon'];
							$mail = $ag['mail'];
							
							if (isset($_REQUEST[$typ . "_neueUhrzeit"])) {
								$hinweisNeueUhrzeit = "<span style='color:red'><b>(Achtung geänderte Uhrzeit!!)</b></span>";
							} else {
								$hinweisNeueUhrzeit = "";
							}
							
							ob_start();
							
							include "template_changeddate.php";
							
							$text = ob_get_clean();
								
							$subject = "Eltern-AG " . $bez. " für  ". $name . " Klasse ". $klasse . ": Terminänderung ";
							
							if ($preview) {
								$result = "Not sent. Preview active";
							} else {
								if (!empty($mail)) {
									$result = sendMail($mail, $name, $subject, $text);
								} else {
									$result = "<span style='color:red'><b>Mail wurde nicht versendet! Es existiert keine Mail-Adresse</b></span>";
								}
							}
							
							?>
							<tr>
								<td><b><?php echo $name?></b></td>
								<td><b><?php echo $klasse?></b></td>
								<td><b><?php echo $mail?></b></td>
								<td><b><?php echo $statusText?></b></td>
								<td><b><?php echo $termin?></b></td>
							</tr> 
							<tr><td valign="top"><b>Mail-Text</b><br></td><td colspan="4"><?php echo $text?></td></tr>
							<tr><td valign="top"><b>Status</b><br></td><td colspan="4"><?php echo $result?></td></tr>
							<tr><td colspan="5"><br><hr><br></td></tr>
							<?php 
					}
					echo "</table>";
		} else {
			echo "<h2>Keine Anmeldungen mit Status " . AgModel::getStatusText('zusage') . "</h2>";
		}
		
		echo "</page>";
	} 
?>
