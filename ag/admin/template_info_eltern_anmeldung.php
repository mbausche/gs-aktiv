<?php 
//Generiert am: Mon, 06 Mar 17 20:59:06 +0100 ?>
Liebe Eltern,<br>
<br>
Sie haben Ihr Kind <br>
<br>
<b><?php echo $name?></b><br>
<br>
zu der Teilnahme an einer oder mehreren AGs angemeldet. Anbei finden Sie eine Kopie der Anmeldung als PDF.<br>
Diese Mail ist für Ihre Unterlagen bestimmt oder für den Fall, das die erste ausgedruckte Version verloren ging.<br>
<br>
<b>Wieso bekomme ich diese Mail?</b><br>
<br>
Sie bekommen diese Mail sobald Ihre Anmeldung bei uns gespeichert wurde.<br>
<br>
<b>Falls noch nicht geschehen:</b><br>
Bitte überweisen Sie jetzt den Betrag (<?php echo $_SESSION['summe']?>€) auf das Konto des Fördervereins (<?php echo str_replace("<br>", " ", CfgModel::load("bankverbindung")) ?>)<br>
oder senden sie uns den Betrag per Paypal (<a href="<?php echo CfgModel::load("paypallink")?>/<?php echo $_SESSION['summe']?>"><img src="http://www.grundschule-aktiv.de/ag/images/paypal.png" height="20px"> Paypal-Bezahlvorgang starten</a>)<br>
<b>Bite beachten Sie:</b> Ihr Kind ist erst angemeldet, wenn die Gebühr enstprechend bezahlt wurde.<br><br>
<br>
Ob die Anmeldung bereits angekommen ist oder ob die Teilnahme bei den einzelnen AGs klappt
können sie <a href="http://www.grundschule-aktiv.de/ag/abfrage.php?nummer=<?php echo $AnmeldungsId?>">hier</a> überprüfen.
<br>
<br>
Viele Grüße<br>
das Eltern-AG-Team des Fördervereins