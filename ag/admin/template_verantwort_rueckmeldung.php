<?php 
//Generiert am: Sun, 18 Sep 16 20:00:14 +0200 ?>
Hallo <?php echo $anrede?>,<br>
<br>
im Anhang findest du die aktuellen Anmeldungen für deine AG
<br>
<br>
<b><?php echo $bez?></b><br>
<br>
<br>
Gib mir bitte Rückmeldung, wie die Schüler verteilt werden sollen bzw. wievielen Schülern ich absagen soll.<br>
Max. Anzahl an Kindern pro Termin die derzeit bei uns hinterlegt ist: <?php echo $anzahl?><br>
<br>

Viele Grüße<br>
<?php echo CfgModel::load("name.verwaltung")?>