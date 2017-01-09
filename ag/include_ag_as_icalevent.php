<?php
$terminStart = AgModel::getIcalDateForStatus($ag, $ag["status_mail"], false);
if (!empty($terminStart)) {
	$terminEnd = AgModel::getIcalDateForStatus($ag, $ag["status_mail"], true);
	$uid = md5(uniqid(mt_rand(), true)) . "@grundschule-aktiv.de";
	$ts = gmdate('Ymd').'T'. gmdate('His') . "Z";	
?>
BEGIN:VEVENT
UID: <?php echo $uid."\n"?>
DTSTAMP: <?php echo $ts."\n"?>
CATEGORIES: Eltern-Ags
DESCRIPTION: Verantwortlich <?php echo $ag["verantwortlicher_name"]?>, Tel <?php echo $ag["verantwortlicher_telefon"].",". $ag["verantwortlicher_mail"]."\n" ?>
DTSTART:<?php echo $terminStart."\n"?>
DTEND:<?php echo $terminEnd."\n"?>
LOCATION:<?php echo $ag['ort']."\n"?>
ORGANIZER:MAILTO:<?php echo $ag["verantwortlicher_mail"]."\n"?>
<?php if ($_REQUEST["inlcudeName"] == "true") { ?>
SUMMARY:<?php echo $ag['schueler_name']?>: Eltern AG <?php echo $ag['ag_name']."\n"?>
<?php } else {?>
SUMMARY:Eltern AG <?php echo $ag['ag_name']."\n"?>
<?php } ?>
TRANSP:OPAQUE
X-MICROSOFT-CDO-BUSYSTATUS:FREE
X-MICROSOFT-CDO-IMPORTANCE:1
X-MICROSOFT-DISALLOW-COUNTER:FALSE
X-MS-OLK-ALLOWEXTERNCHECK:TRUE
X-MS-OLK-AUTOSTARTCHECK:FALSE
X-MS-OLK-CONFTYPE:0
END:VEVENT
<?php } else { ?>
X-COMMENT:Teilnahme an AG <?php echo $ag['ag_name']?> noch nicht bestätigt oder abgesagt. Status:  <?php AgModel::getStatusText($ag["status_mail"])?>
<?php } ?>