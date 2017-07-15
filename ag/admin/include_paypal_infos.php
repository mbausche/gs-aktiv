<?php 
	if (count($arr) == 0) {
		echo "--- KEINE ---";
	} else {
		echo "<table>";
		?>
		<tr><th>Schüler</th><th>Status</th><th>Paypal-EMail</th><th>Betrag</th></tr>
		<?php
		foreach ($arr as $ag) {
			
			error_log(print_r($ag,true));

			
			$s = $ag['status_anmeldung'];
			$status = AgModel::getStatusText($s);
			$termin = AgModel::getTerminForStatus($ag, $s);
			$name = $ag['schueler_name'];
			$klasse = $ag['klasse'];
			$telefon = $ag['telefon'];
			$mail = $ag['mail'];
			$mailPaypal = $ag['mail_paypal'];
			$betrag = $ag['moechte_mitglied_werden'] == 1 || $ag['ist_mitglied'] ? formatAsCurrency($ag['betrag_mitglied']) : formatAsCurrency($ag['betrag_nicht_mitglied']);
			?>
				<tr>
				<td valign="top"><?php echo $name?> <?php echo $klasse?><br><?php echo $telefon?><br><?php echo $mail?></td>
				<td valign="top"><?php echo $status?> <?php echo $termin?></td>
				<td valign="top"><?php echo $mailPaypal?></td>
				<td valign="top"><?php echo $betrag?></td>
				</tr> 
				<?php 
			}
			echo "</table><br><br>";
	}

?>