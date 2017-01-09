<?php
	include '../db.php';
	require_once("../funktionen.php");
	
	ini_set("log_errors", 1);     /* Logging "an" schalten */
	ini_set("error_log", "errorlog.txt");     /* Log-Datei angeben */
	
	$tableId = "";
	
	$title =  "Statistik Vorjahre ";
	
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

	var table = $('#statistic').DataTable({
        "paging":   false,
        "info": true,
        "bJQueryUI": true,
		/* Disable initial sort */
        "aaSorting": [],
        "sDom": 'lfrtip'
        <?php echo "," . $dataTableLangSetting ?>
	});

	setContentTableVisible();
});
</script>

<table id="statistic" class="display">
    <thead>
        <tr>
            <th>Jahr</th>
            <th>AGs</th>
        	<th>Schüler</th>
        	<th>Anmeldungen zu AGs</th>
        	<th>Annmeldungen pro Schüler</th>
        	<th>Annmeldungen pro AG</th>
        	<th>Zusagen</th>
        	<th>2. Termin</th>
        	<th>Ersatztermin</th>
        	<th>Absagen</th>
        </tr>
    </thead>
    <tbody>
		<?php
			$prefixe = explode(",", CfgModel::load("prefix.vorjahre"));
			array_push($prefixe, "");
			
			foreach ($prefixe as $prefixAndYear) {
			
			$prefix = explode("=", $prefixAndYear)[0];
			
			$jahr = $prefix;
			if ($jahr == "") {
				$jahr = "Aktuell (" . CfgModel::load("prefixAgNummer") . ")";
			}
			
			$selectAGs = "SELECT count(*) as Anzahl FROM ".$prefix."ag";
			$selectSchueler = "SELECT DISTINCT Name from ".$prefix."anmeldung";
			$selectAnmeldungen = "SELECT count(*) as Anzahl FROM ".$prefix."anmeldungfuerag";
			$selectZusagen = "SELECT count(*)  as Anzahl FROM ".$prefix."anmeldungfuerag where status_anmeldung = 'zusage'";
			$selectTermin2 = "SELECT count(*)  as Anzahl FROM ".$prefix."anmeldungfuerag where status_anmeldung = 'termin2'";
			$selectErsatztermin = "SELECT count(*)  as Anzahl FROM ".$prefix."anmeldungfuerag where status_anmeldung = 'ersatztermin'";
			$selectAbsagen = "SELECT count(*)  as Anzahl FROM ".$prefix."anmeldungfuerag where status_anmeldung = 'absage'";
			
			$ags = R::getRow($selectAGs)["Anzahl"];
			$schueler = count(R::getAll($selectSchueler));
			$anmeldungen = R::getRow($selectAnmeldungen)["Anzahl"];
			$proSchueler = number_format ( $anmeldungen / $schueler , 2 , "," , ".");
			$proAg = number_format ( $anmeldungen / $ags , 2 , "," , ".");

			$zusagen = R::getRow($selectZusagen)["Anzahl"];
			$zusagen = $zusagen . " (" . round($zusagen / $anmeldungen * 100,1) . "%)";
			
			$termin2 = R::getRow($selectTermin2)["Anzahl"];
			$termin2 = $termin2 . " (" . round($termin2 / $anmeldungen * 100,1) . "%)";
			
			$ersatztermin = R::getRow($selectErsatztermin)["Anzahl"];
			$ersatztermin = $ersatztermin . " (" . round($ersatztermin / $anmeldungen * 100,1) . "%)";
			
			$absagen = R::getRow($selectAbsagen)["Anzahl"];
			$absagen = $absagen . " (" . round($absagen / $anmeldungen * 100,1) . "%)";
				
			
		?>    
        <tr>
        	<td><?= $jahr ?></td>
        	<td><?= $ags?></td>
        	<td><?= $schueler?></td>
        	<td><?= $anmeldungen?></td>
        	<td><?= $proSchueler?></td>
        	<td><?= $proAg?></td>
        	<td><?= $zusagen?></td>
        	<td><?= $termin2?></td>
        	<td><?= $ersatztermin?></td>
        	<td><?= $absagen?></td>
        	</tr>
        <?php } ?>
    </tbody>
</table>



<?php include 'footer.php';?>


