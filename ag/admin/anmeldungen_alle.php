<?php
	include '../db.php';
	require_once("../funktionen.php");
	
	ini_set("log_errors", 1);     /* Logging "an" schalten */
	ini_set("error_log", "errorlog.txt");     /* Log-Datei angeben */
	
	$tableId = "";
	
	$title =  "Statistik Vorjahre ";
	
	include 'header.php';
	
	?>

	
<link rel=stylesheet href="../scripts/codemirror/doc/docs.css">
<link rel="stylesheet" href="../scripts/codemirror/lib/codemirror.css">
<script src="../scripts/codemirror/lib/codemirror.js"></script>
<script src="../scripts/codemirror/addon/edit/matchbrackets.js"></script>
<script src="../scripts/codemirror/mode/htmlmixed/htmlmixed.js"></script>
<script src="../scripts/codemirror/mode/xml/xml.js"></script>
<script src="../scripts/codemirror/mode/javascript/javascript.js"></script>
<script src="../scripts/codemirror/mode/css/css.js"></script>
<script src="../scripts/codemirror/mode/clike/clike.js"></script>
<script src="../scripts/codemirror/mode/php/php.js"></script>
<style type="text/css">.CodeMirror {border-top: 1px solid black; border-bottom: 1px solid black;}</style>

<script>
$(document).ready(function() {

	var table = $('#alle').DataTable({
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

<table id="alle" class="display">
    <thead>
        <tr>
            <th>Jahr</th>
            <th>Name</th>
            <th>Mail</th>
            <th>Telefon</th>
            <th>IBAN</th>
            <th>Kontoinhaber</th>
        </tr>
    </thead>
    <tbody>
		<?php
			$prefixe = explode(",", CfgModel::load("prefix.vorjahre"));
			array_push($prefixe, "");
			$keys = array();
			
			foreach ($prefixe as $prefixAndYear) {
			
				$prefix = explode("=", $prefixAndYear)[0];
				
				$jahr = $prefix;
				if ($jahr == "") {
					$jahr = "Aktuell (" . CfgModel::load("prefixAgNummer") . ")";
				}
				
				$anmeldungen = R::getAll("SELECT * FROM ".$prefix."anmeldung order by name");
					
				foreach ($anmeldungen as $anmeldung) {

					$key = $anmeldung["name"].$anmeldung["mail"].$anmeldung["telefon"].$anmeldung["iban"].$anmeldung["kontoinhaber"];
					
					if (array_key_exists($key, $keys)) {
						continue;
					}
					
					$keys[$key] = $anmeldung;
					
			
		?>    
        <tr>
        	<td><?= $jahr ?></td>
        	<td><?= $anmeldung["name"]?></td>
        	<td><?= $anmeldung["mail"]?></td>
        	<td><?= $anmeldung["telefon"]?></td>
        	<td><?= $anmeldung["iban"]?></td>
        	<td><?= $anmeldung["kontoinhaber"]?></td>
        	</tr>
        <?php } 
        }

        ?>
    </tbody>
</table>



<?php include 'footer.php';?>


