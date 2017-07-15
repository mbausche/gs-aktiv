<?php 
session_start();
$title = "Anmeldung speichern";

if ($_SESSION["bootstrap"] == "true") {
	include_once 'header_bootstrap.php';
} else {
	include_once 'header.php';
}


require_once("funktionen.php");
require_once("db.php");
require_once("conf.php");

// 	ini_set("log_errors", 1);     /* Logging "an" schalten */
// 	ini_set("error_log", "errorlog.txt");     /* Log-Datei angeben */

//Zuerst speichern wir die Anmeldung in der Datenbank, falls sich inhaltliche Änderungen ergeben haben
$result = AgModel::insertAnmeldung($prefixAnmeldung);
$neueAnmeldung = $result->created;
$name =  $result->name;
$klasse =  $result->klasse;
$mail = $result->mail;
$AnmeldungsId = $result->anmeldeNummer;
$summe = $_SESSION['summe'];

// $_SESSION["insert_result_created"] = $neueAnmeldung;
// $_SESSION["insert_result_name"] = $name;
// $_SESSION["insert_result_id"] = $AnmeldungsId;
// $_SESSION["insert_result_klasse"] = $klasse;
// $_SESSION["insert_result_mail"] = $mail;

$ags = AgModel::getAgs();

//Zuerst speichern wir die Anmeldung in der Datenbank, falls sich inhaltliche Änderungen ergeben haben
// $neueAnmeldung = $_SESSION["insert_result_created"];
// $name =  $_SESSION["insert_result_name"];
// $mail = $_SESSION["insert_result_mail"];
// $AnmeldungsId = $_SESSION["insert_result_id"];

$pages = array();
$pages[0] = $_SESSION["zahlart"] == "schule";
$pages[1] = true;

$html = false;
$bootstrap = false;
$header = false;
ob_start();
include('readOnly.php');
$content = ob_get_clean();

require_once('ext/html2pdf/html2pdf.class.php');
// seitenränder (in mm)
$oben=30;    //mT
$unten=10;   //mB
$links=15;   //mL
$rechts=15;  //mR

$pdfFileName = $ID. "_" .$AnmeldungsId.".pdf";

$_SESSION['insert_filename'] = $pdfFileName;

$pdf = "pdf/" . $pdfFileName;
$pdfMail = "pdf/" . $ID."_" .$AnmeldungsId."_mail.pdf";
$html2pdf = new HTML2PDF('P','A4','de', true, 'UTF-8', array($links, $oben, $rechts, $unten));
$html2pdf->pdf->SetDisplayMode('real');
$html2pdf->WriteHTML($content);
//In Datei schreiben (Komplette Anmeldung)
$html2pdf->Output( toIso($pdf),'F');
//$html2pdf->Output($pdf);

$pages[1] = $_SESSION["zahlart"] == 'bank';

ob_start();
include('readOnly.php');
$content = ob_get_clean();


$html2pdf = new HTML2PDF('P','A4','de', true, 'UTF-8', array($links, $oben, $rechts, $unten));
$html2pdf->WriteHTML($content);
//In Datei schreiben (Mail-Anhang mit sufix _mail.pdf)
$html2pdf->Output(toIso($pdfMail),'F');

error_log("Server: " . $_SERVER['SERVER_ADDR']);

if ($neueAnmeldung) {
	$subject = "Kopie der Anmeldung " .  $AnmeldungsId . " für " .$name;
	ob_start();
	include "admin/template_info_eltern_anmeldung.php";
	$content = ob_get_clean();
	$fileEntry = findAnmeldungsPDF("./pdf/",$AnmeldungsId);
	sendMail($mail, $name , $subject, $content, array("./pdf/" . $fileEntry), array(toUtf($fileEntry)));
}


?>

	<script type="text/javascript">

	$(document).ready(function() {
		$("#readyLink").hide();
	});

	function pdfClicked() {
		$("[type='stepTitle3']").prop("class","disabled");
		$("[type='step3']").prop("class","disabled");
		$("[type='haken3']").show();
		document.location.href="pdf.php";
	}
	
	</script>

<?php if ($_SESSION["bootstrap"] == true) { ?>
</head>
<body>
<img src="images/heading.jpg" class="img-responsive img-rounded" alt="Header">
<br>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title ok">Die Anmeldung wurde erfolgreich durchgeführt!</h3>
		<br>
		Bitte überweisen Sie jetzt den Betrag auf das Konto des Fördervereins (<?php echo str_replace("<br>", " ", CfgModel::load("bankverbindung")) ?>)<br>
		oder senden sie uns den Betrag per Paypal (<a href="<?php echo CfgModel::load("paypallink")?>/<?php echo $_SESSION['summe']?>" target="_blank"><img src="images/paypal.png" height="20px"> Paypal aufrufen</a>)<br>
		Sie bekommen von uns auch noch eine Mail mit der Anmeldebestätigung und der Bankverbindung bzw. dem Paypal-Link
	</div>
	<div class="panel-body">
	    <table class="table table-striped table-bordered">
        <tbody>
          <tr>
            <td>Name</td>
            <td><?php echo $name?></td>
          </tr>
          <tr>
            <td>Klasse</td>
            <td><?php echo $klasse?></td>
          </tr>
          <tr>
            <td>Anmeldenummer</td>
            <td><?php echo $AnmeldungsId?></td>
          </tr>
          <tr>
            <td>Betrag</td>
            <td><?php echo $summe?>&euro;</td>
          </tr>
        </tbody>
      </table>
	</div>
	<div class="panel-heading">
		<h3 class="panel-title">Wie gehts jetzt weiter?</h3>
	</div>
	<div class="panel-body">
		<?php 
			if ($_SESSION["zahlart"] == "schule") 
				renderImageTableBootstrap("images/",array(3,4,5,6),false,true,false);
			else
				renderImageTableBootstrapBank("images/",array(3,4),false,true,false,$name . " " . $klasse . " ". $AnmeldungsId);
		
		?>
		<br>
		<a class="btn btn-info" role="button" href="anmelden2.php?reenter=false&direkt_eingabe=<?php echo $_SESSION['direkt_eingabe']?>&geprueft=<?php echo $_SESSION['geprueft']?>&silent=true" type="button">Nochmal eine Anmeldung ausfüllen</a>
		
	</div>
</div>
<?php } else { ?>

</head>
<body>

<h3 class="panel-title">Die Anmeldung wurde gespeichert</h3>

	    <table>
        <tbody>
          <tr>
            <td>Name</td>
            <td><?php echo $name?></td>
          </tr>
          <tr>
            <td>Klasse</td>
            <td><?php echo $klasse?></td>
          </tr>
          <tr>
            <td>Anmeldenummer</td>
            <td><?php echo $AnmeldungsId?></td>
          </tr>
          <tr>
            <td>Betrag</td>
            <td><?php echo $summe?>&euro;</td>
          </tr>
        </tbody>
      </table>
<h3>Wie gehts jetzt weiter?</h3>

<?php 
renderImageTable("images/",array(3,4,5,6),false,true,false);
?>
      
<br>

	<span id="readyLink">Fertig! <a href="anmelden.php?reenter=false&direkt_eingabe=<?php echo $_SESSION['direkt_eingabe']?>&geprueft=<?php echo $_SESSION['geprueft']?>&silent=true" type="button">Nochmal eine Anmeldung ausfüllen</a></span>
    
	<?php if ($_SESSION['direkt_eingabe'] == 1) {?>
	<br><br>
	<a type="button" href="admin/index.php">Zurück zur Admin-Oberfläche</a>
	<?php } ?>
</body>
</html>
	

<?php } ?>
