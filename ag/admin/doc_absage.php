<?php if ($printHeader) { ?>
<?php if ($html == true) { 
	header('Content-type: text/html; charset=utf-8');
} else {
	header('Content-type: application/pdf; charset=utf-8');
}}

if ($html == true) { ?>
<html>
<head>
	<title>Absage</title>
	<style type="text/css">
	<!--
	-->
	</style>

</head>
<?php } else { ?>
<style type="text/css">
<!--
-->
</style>

<?php } ?>

<?php
	$ag = AgModel::getAnmeldungsDaten($id_anmeldung);
	$name = $ag['schueler_name'];
	$bez = $ag['ag_name'] . " (" . $ag['ag_nummer'] . ")";
	$datum = formatSQLDate($ag['termin']);
	
	ob_start();
	
	include "doc_absage_template.php";
	
	$text = ob_get_clean();
			
	error_log("Text: " . $text);

?>	

<?php if ($html == true) { ?>
	<body style="font-family:sans-serif">
	<?php echo $text?>
	</body>
	</html>
 <?php } else { ?>
<page>
	<?php echo $text ?>
	<div style="position: absolute; top: 0mm; left: 130mm "><img src='../images/logo.png' style="width:45mm"/></div>
</page>

<?php } ?> 

