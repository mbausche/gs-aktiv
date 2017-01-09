<?php
	include '../db.php';
	require_once("../funktionen.php");
	
	ini_set("log_errors", 1);     /* Logging "an" schalten */
	ini_set("error_log", "errorlog.txt");     /* Log-Datei angeben */
	
	$tableId = "";
	
	$title =  "Mailing-Liste " . $_REQUEST['typ'];
	$mails = AgModel::getMailAdresses($_REQUEST['typ']);
	
	include 'header.php';
	
	?>

	
<link rel=stylesheet href="../codemirror/doc/docs.css">
<link rel="stylesheet" href="../codemirror/lib/codemirror.css">
<script src="../codemirror/lib/codemirror.js"></script>
<script src="../codemirror/addon/edit/matchbrackets.js"></script>
<script src="../codemirror/mode/htmlmixed/htmlmixed.js"></script>
<script src="../codemirror/mode/xml/xml.js"></script>
<script src="../codemirror/mode/javascript/javascript.js"></script>
<script src="../codemirror/mode/css/css.js"></script>
<script src="../codemirror/mode/clike/clike.js"></script>
<script src="../codemirror/mode/php/php.js"></script>
<style type="text/css">.CodeMirror {border-top: 1px solid black; border-bottom: 1px solid black;}</style>

<script>
$(document).ready(function() {
	setContentTableVisible();
});
</script>

<a type="button" style="margin-bottom: 5px" href="mailto:
<?php foreach ($mails as $mail) { ?>
<?php echo $mail['mail']?>;
<?php } ?>
">Alle</a><br>

<a type="button" style="margin-bottom: 5px" href="mailto:
<?php foreach ($mails as $mail) { ?>
<?php if (strpos($mail['mail'], "@grundschule-aktiv.de") === false) echo $mail['mail'] . ";"?>
<?php } ?>
">Alle (ohne grundschule-aktiv.de)</a><br>


<?php foreach ($mails as $mail) { ?>
<a type="button" style="margin-bottom: 5px" href="mailto:<?php echo $mail['mail']?>"><?php echo $mail['name']?> &lt;<?php echo $mail['mail']?>&gt;</a><br>
<?php } ?>
<br>

<?php include 'footer.php';?>

