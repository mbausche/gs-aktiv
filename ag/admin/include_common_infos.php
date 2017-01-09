<?php 
	if (count($arr) == 0) {
		echo "--- KEINE ---";
	} else {
		echo "<table>";
		foreach ($arr as $ag) {
			$s = $ag['status_anmeldung'];
			$status = AgModel::getStatusText($s);
			$termin = AgModel::getTerminForStatus($ag, $s);
			$name = $ag['schueler_name'];
			$klasse = $ag['klasse'];
			$telefon = $ag['mail'];
			?>
				<tr>
				<td><?php echo $name?></td>
				<td><?php echo $klasse?></td>
				<td><?php echo $telefon?></td>
				<td><?php echo $status?></td>
				<td><?php echo $termin?></td>
				</tr> 
				<?php 
			}
			echo "</table><br><br>";
	}

?>