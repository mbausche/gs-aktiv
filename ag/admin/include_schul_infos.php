<?php 
	
	$status = $ag['status_anmeldung'];
	$statusText = AgModel::getStatusText($status);
	if ($absageGrund == "helferkind") {
		$statusText = "Erstattung für 'Helferkind'";
	}
	$termin = AgModel::getTerminForStatus($ag, $status);
	if ($status == "absage") {
		//Für eine Absage holen wir uns den Haupt-Termin
		$termin = AgModel::getTerminForStatus($ag, "zusage");
	} else {
		$termin = AgModel::getTerminForStatus($ag, $status);
	}
	$name = $ag['schueler_name'];
	$bez = "'" . $ag['ag_nummer'] . " - " . $ag['ag_name'] . "'";
	$klasse = $ag['klasse'];
	$telefon = $ag['telefon'];
	$mail = $ag['mail'];
	if ($ag["ist_mitglied"] || $ag["moechte_mitglied_werden"]) {
		$betragErstattung = formatAsCurrency($ag["betrag_mitglied"]);   
	} else {
		$betragErstattung = formatAsCurrency($ag["betrag_nicht_mitglied"]);
	}
	
	if ($ag['zahlart'] == 'schule') {
		$wieWirdErstattet = CfgModel::load("text.erstattung.schule");
	} else {
		if (strlen($ag['mail_paypal']) > 0) {
			$wieWirdErstattet = CfgModel::load("text.erstattung.paypal");
		} else {
			$wieWirdErstattet = CfgModel::load("text.erstattung.bank");
		}
	}
	
	ob_start();
	
	if ($status == "absage") {
		if ($absageGrund == "zuwenig") {
			include "template_absage_zuwenig.php";
		} else if ($absageGrund == "zuviel") {
			include "template_absage_zuviel.php";
		} else if ($absageGrund == "schueler") {
			include "template_absage_schueler.php";
		} else if ($absageGrund == "helferkind") {
			include "template_anmeldung_helferkind.php";
		} else {
			include "template_absage_sonstige.php";
		}
	} 
	else if ($status == "zusage")
		include "template_zusage.php";
	
	else if ($status == "termin2")
		include "template_termin2.php";
	
	else if ($status == "ersatztermin")
		include "template_ersatztermin.php";
	
	$text = ob_get_clean();
	
?>