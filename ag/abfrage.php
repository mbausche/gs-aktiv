<?php
	ob_implicit_flush (false);
	$title = "Status Eltern-AG Anmeldung ". $_REQUEST['nummer'];
	require_once("funktionen.php");
	$addAfterHeading = formatAsDownloadAnmeldungLink("./",$_REQUEST['nummer']);
	include_once 'header.php';
?>

<?php 

$anmeldeDaten =  AgModel::getAnmeldedaten($_REQUEST['nummer']);
$showHelp = false;

if (count($anmeldeDaten) == 0 || empty($_REQUEST['nummer'])) { 
	if ($_REQUEST['again'] == "true") {
		echo "Anmeldenummer aus dem Anmeldeformular eingeben:";
	} else if (!empty($_REQUEST['hilfe'])) {
		$showHelp = true;
		$mail = $_REQUEST['mail'];
		if (!empty($mail)) {
			$liste = AgModel::getAnmeldungenByMail($mail);
			if (count($liste) > 0) {
				$links = array();
			
				foreach ($liste as $anmeldung) {
					$links["http://www.grundschule-aktiv.de/ag/abfrage.php?nummer=" . $anmeldung["anmelde_nummer"]] = $anmeldung["anmelde_nummer"] . " (" . $anmeldung["name"] . ")";
				}
			
				ob_start();
				include "admin/template_anmeldenummern.php";
				$content = ob_get_clean();
				
				sendMail($_REQUEST['mail'], "", "Anmeldenummern", $content);
			
				echo "Die Mail wurde versendet!";
				?>
				<br><br>
				<form method="get" action="abfrage.php"><input type="submit" value="Ich hab meine Anmeldenummer erhalten und möchte Sie eingeben!" name=""></form>
				<?php 
				exit;
			} else {
				echo "Für die Mail-Adresse $mail wurde keine Anmeldung gefunden!";
			}
			$showHelp = false;
		} else if ($_REQUEST['sendMail'] == "true") {
			echo "Bitte noch die Mail-Adresse eingeben:";
		} else {
			echo "Bitte gib uns deine Mail-Adresse und wir schicken dir eine Mail mit allen Anmeldenummern die wir finden können:";
		}
	} else if (empty($_REQUEST['nummer'])) { 
		echo "Bitte geben Sie zuerst die die Anmeldenummer aus dem Anmeldeformular ein:";
	} else { 
		echo "Die Anmeldenummer <b>".$_REQUEST['nummer']."</b> wurde im System nicht gefunden!. Geben sie eine andere Anmeldenummer ein:"; 
	}
	?>
	<br><br>
	<form method="get" action="abfrage.php">
	<?php if ($showHelp) { ?>
		Mail-Adresse: <input type="text" value="" size="50" name="mail"> <input type="submit" value="OK" name="hilfe">
		<input type="hidden" value="true" name="sendMail">
	<?php } else { ?>
		Anmeldenummer: <input type="text" value="" size="20" name="nummer"> <input type="submit" value="OK" name="submit"><br><br>
		<input type="submit" value="Helft mir, ich hab meine Anmeldenummer vergessen" name="hilfe">
	<?php } ?>
	</form>
	<br><br>
	<b>Hinweis: </b>Die Anmeldenummer befindet sich auf dem PDF-Dokument, dass sie bei der Anmeldung erzeugt haben:<br><br>
	<img src="images/sample.png">
	
<?php } else { 
	$anmeldung = AgModel::getAnmeldung($_REQUEST['nummer']);
?>

<script type="text/javascript">

$( document ).ready(function() {
	$("[type='ags']").DataTable({
        "paging":   false,
        "ordering": false,
        "searching": false,
        "info":     false,
        "bJQueryUI": true,
        "sDom": 'lfrtip'
		<?php echo "," . $dataTableLangSetting ?>
    });

	setTimeout( function(){ 
		$("[type='auch']").each(function (index) {
			var text = $( this ).text();
			$( this ).html(text + ".");
			$( this ).css("font-size","8pt");
		});
	 }  , 10 );
    
	
	$("[type='anmeldung']").DataTable({
        "paging":   false,
        "ordering": false,
        "searching": false,
        "info":     false,
        "bJQueryUI": true,
        "sDom": 'lfrtip'
		<?php echo "," . $dataTableLangSetting ?>
    }).draw();

});

</script>

<style>
 table.angemeldeteAgs tr.even td {
 	background-color: #EDEEEF;
 }
 table.angemeldeteAgs tr td {
 	padding-left: 2px;
 	padding-top: 0px;
 	padding-bottom: 0px;
}
</style>

<?php 
$includeNameAndKlasse = true;
$includeAGNummer = false;
include 'include_anmelde_status.php';
?>

<?php 

$andere = AgModel::getAndereAnmeldungen($anmeldung['name'],$anmeldung['klasse'],$anmeldung['anmelde_nummer']);
if (count($andere) > 0) {
?>
<h1>Für Ihr Kind existieren noch weitere Anmeldungen:</h1>
<?php foreach ($andere as $anmeldung) {
	$includeNameAndKlasse = false;
	$includeAGNummer = true;
	include 'include_anmelde_status.php';
 } } ?>

<br>
<br>
<a type="button" href="<?php echo $_SERVER['PHP_SELF']?>">Eine weitere Anmeldung überprüfen</a>
<br><br>
Bei weiteren Fragen können Sie sich gerne an <?php echo CfgModel::load("kontakt.verwaltung")?> wenden.<br>
<br>
<!-- 
Die aktuellen Termine können über die folgende URLs z.B. in Google Kalender importiert werden (<a href="http://www.wann-is-was.de/anleitung-ical-kalender-in-google-kalender-importieren/" target="_blank">Anleitung</a>):
<ul>
<?php $url = "http://www.grundschule-aktiv.de/ag/ical.php?nummer=" . $_REQUEST['nummer'] ?>
<li><a href="<?php echo $url?>" target="_blank"><?php echo $url?></a> (Kalendereintrag ohne Name des Kindes)</li>
<?php $url = "http://www.grundschule-aktiv.de/ag/ical.php?nummer=" . $_REQUEST['nummer'] . "&includeName=true" ?>
<li><a href="<?php echo $url?>" target="_blank"><?php echo $url?></a> (Kalendereintrag mit Name des Kindes)</li>
</ul>
<br>
<br>
 -->
 <?php } ?> 
</body>
</html>

<?php 
flush();
ob_implicit_flush (true);
?>
	