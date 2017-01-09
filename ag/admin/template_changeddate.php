<?php 
//Generiert am: Sun, 18 Sep 16 20:00:14 +0200 ?>
Liebe Eltern,<br>
<br>
ihr Kind <b><?php echo $name?></b> möchte gerne an der AG <?php echo $bez?> teilnehmen.<br>
Der <?php echo $terminString?> für die AG  hat sich leider geändert.<br>
<br>
Die AG findet jetzt statt am:<br>
<br>
<b><?php echo $termin?></b><br>
<?php echo $hinweisNeueUhrzeit?>
<br><br>
Sollte Ihr Kind an diesem Termin nicht teilnehmen können, setzen Sie sich bitte mit<br>
<?php echo CfgModel::load("kontakt.verwaltung")?><br>in Verbindung.<br> 
<br>
Vielen Dank für Ihr Verständnis.
<br>
Ihr Eltern-AG-Team des Fördervereins