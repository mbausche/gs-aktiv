Folgende Kurse gab es in der Vergangenheit!<br>
<br>
Vielleicht ist da ja etwas dabei, dass Sie anbieten k&ouml;nnten oder dass Ihnen die Idee f&uuml;r eine neue AG liefert:<br>
<br>

<?php 
require_once("funktionen.php");
require_once("db.php");

$ags = AgModel::getAgsAllYears();

foreach ($ags as $year => $array) {
	echo "<h3>$year</h3><div><ul>";
	foreach ($array as $ag) {
		echo "<li>$ag</li>";
	}
	echo "</ul></div>";
}


?>
