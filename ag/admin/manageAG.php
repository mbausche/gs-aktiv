<?php
	include '../db.php';
	require_once("../funktionen.php");
	
	session_start();
	
	if (!empty($_REQUEST["resetAktiv"])) {
		$_SESSION["resetAktiv"] = $_REQUEST["resetAktiv"];
	} 
	
	$tableId = "";
	
	$id = $_REQUEST['ag'];
	$updateStates = $_REQUEST['updateStates'];
	
	if ($id == "") {
		die("Es wurde keine AG ausgewählt");
	}
	
	if ($updateStates != "") {
		AgModel::changeStateForAG($id);
	}
		
	if ($_REQUEST['alleZusagen'] == "true") {
		AgModel::alleZusagen($id);
	}
	
	if ($_REQUEST['editAG'] == "true") {
		AgModel::changeDateAndTime($id, $_REQUEST,'termin');
		AgModel::changeDateAndTime($id, $_REQUEST,'termin_ueberbuchung');
		AgModel::changeDateAndTime($id, $_REQUEST,'termin_ersatz');
		AgModel::updateMax($id, $_REQUEST['max']);
		AgModel::changeVerantwortlichkeit($id, $_REQUEST["verantwortlicher_name"],$_REQUEST["verantwortlicher_mail"],$_REQUEST["verantwortlicher_telefon"]);
	}
	if ($_REQUEST['editComment'] == "true") {
		AgModel::replaceComment($id, $_REQUEST["kommentar"],$_REQUEST["kommentar_privat"]);
	}
	
	if (isset($_REQUEST['newType']) && isset($_REQUEST['id'])) {
		if ($_REQUEST['newType'] != "reset") {
			AgModel::changeState($_REQUEST['id'], $_REQUEST['newType']);
		} else {
			AgModel::resetStates($_REQUEST['id']);
		}
		 
		
		$daten = AgModel::getAnmeldungsDaten($_REQUEST['id']);
		$name = $daten['schueler_name'];
		$klasse = $daten['klasse'];
		$status = AgModel::getStatusText($daten['status_anmeldung']);
		//$message = "Neuer Status für folgenden Schüler<br>$name, Klasse $klasse<br><br>Status: $status";		
	}
	
	$ag = AgModel::getAg($id);

	$countTermin1 = AgModel::countAnmeldungenForAg($ag['ag_nummer'], "zusage");
	$countTermin2 = AgModel::countAnmeldungenForAg($ag['ag_nummer'], "termin2");
	$countErsatztermin = AgModel::countAnmeldungenForAg($ag['ag_nummer'], "ersatztermin");
	$countAbsagen = AgModel::countAnmeldungenForAg($ag['ag_nummer'], "absage");

	$countTermin1Offen = AgModel::countAnmeldungenForAg($ag['ag_nummer'], "zusage", true);
	$countTermin2Offen = AgModel::countAnmeldungenForAg($ag['ag_nummer'], "termin2", true);
	$countErsatzterminOffen = AgModel::countAnmeldungenForAg($ag['ag_nummer'], "ersatztermin", true);
	$countAbsagenOffen = AgModel::countAnmeldungenForAg($ag['ag_nummer'], "absage", true);
	
	
	$classTermin1 = $classTermin2 = $classErsatzTermin = $classAbsage = "ok";
	if (isset($ag['max_kinder'])) {
		if ($countTermin1 > $ag['max_kinder'] && $ag['max_kinder'] > 0) {
			$classTermin1 = "error";
		} else if ($countTermin1Offen > 0) {
			$classTermin1 = "warning";
		}
		if ($countTermin2 > $ag['max_kinder']) {
			$classTermin2 = "error";
		} else if ($countTermin2Offen > 0) {
			$classTermin2 = "warning";
		}
		
		if ($countErsatztermin > $ag['max_kinder']) {
			$classErsatzTermin = "error";
		} else if ($countErsatzterminOffen > 0) {
			$classErsatzTermin = "warning";
		}
	}
	
	
	if ($countAbsagenOffen > 0) {
		$classAbsage = "warning";
	}
	
	include 'header.php';
	$title =  "Eltern-AG " . $id  ." - " . $ag['name'] . " verwalten";
?>

<script>

$(document).ready(function() {

    var enableInfoButton = "<?php if ($countTermin1Offen > 0 || $countTermin2Offen > 0 || $countErsatzterminOffen > 0 || $countAbsagenOffen > 0) echo "enable" ; else echo "disable";?>";
    var enableInfoButtonVerantwortliche = "enable";//enableInfoButton == "enable" ? "disable" : "enable";
    
    $( "#btn_info_parents" ).button( enableInfoButton );

    $( "a[for=verantwortliche]" ).button( enableInfoButtonVerantwortliche );
    
	var table = $('#anmeldungen').DataTable({
	 	 	"paging":   false,
	        "info":     true,
	        "bJQueryUI": true,
	        "sDom": 'ft',
	 	 	/* Disable initial sort */
	        "aaSorting": []
			<?php echo "," . $dataTableLangSetting ?>
	 });

	<?php renderLastSearchHandling("table","manageAG.php_anmeldungen");?>
	setContentTableVisible();
    
	$("#editButton").click(function() {
		var button = "#editButton";
		$( "#dialog-editAG" ).dialog( {
	        modal: true,
	        width: 750,
	        position: { my: "left top", at: "left bottom", of: button  },
	        buttons: {
	          Ok: function() {
	            $( "#editAGForm" ).submit();
	          }
	        }
	    });
	});


	$("#btn_info_parents_dates").click(function() {
		$( "#dialog-defineDateChanges" ).dialog( {
	        modal: true,
	        width: 700,
	        buttons: {
	          Ok: function() {
	            $( "#defineChangesForm" ).submit();
	          }
	        }
	    });
	});

	
    
    $('#ag').DataTable( {
    	"paging":   false,
        "ordering": false,
        "searching": false,
        "info":     false,
        "bJQueryUI": true,
        "sDom": 'lfrtip'
		<?php echo "," .$dataTableLangSetting ?>
    } );


    $('#editAG, #editAG2, #editAG3').DataTable( {
    	"paging":   false,
        "ordering": false,
        "searching": false,
        "info":     false,
        "bJQueryUI": true,
        "sDom": 'lfrtip'
    	<?php echo "," .$dataTableLangSetting ?>
} );


    $('#printInfosDates').DataTable( {
    	"paging":   false,
        "ordering": false,
        "searching": false,
        "info":     false,
        "bJQueryUI": true,
        "sDom": 'lfrtip'
    	<?php echo "," .$dataTableLangSetting ?>
} );

    
    $( "#accordion_<?php echo $ag['ag_nummer']?>" ).accordion({
    	collapsible: true,
    	animate: 100
    	<?php 
    		$sessionVal = $_SESSION["accordion_" . $ag['ag_nummer'] ];
    		if (!empty($sessionVal)) {
				echo ",active: " . $sessionVal;
			}
    	?>
    });

    $( "#accordion_<?php echo $ag['ag_nummer']?>" ).on( "accordionactivate", function( event, ui ) {
    	var active = $( "#accordion_<?php echo $ag['ag_nummer']?>" ).accordion( "option", "active" );
        $.ajax({
      	  url: "storeSessionData.php?key=accordion_<?php echo $ag['ag_nummer']?>&value=" + active
      	});
    	
    } );

    addDatePicker("<?php echo CfgModel::load("aktive.ag.zeitraum.von") ?>","<?php echo CfgModel::load("aktive.ag.zeitraum.bis") ?>","../images");

	$( "#dialog-editAG" ).hide();
	$( "#dialog-defineDateChanges" ).hide();

} );

<?php 
$termine = AgModel::getAlleTermineMitAg($id);
$ferienString = CfgModel::load("aktive.ag.ferien");
include 'include_checkDate.php';
?>


</script>


<div id="accordion_<?php echo $ag['ag_nummer']?>">
<h3><?php echo $title?></h3>
<div id="accordion_ag_content">
<table style="text-align: left; margin-right:auto;margin-left:0px;border:0px;padding:0px; margin 0px">
<tr>
<td valign="top" style="width:50px; text-align: right">
	<img title="AG-Daten anpassen" id="editButton" src="../images/edit.png" height="32"><br><br>
</td>
<td valign="top">
<table id="ag" class="display" style="width:500px; text-align: left; margin-right:auto;margin-left:0px">
<thead style="display: none;">
<tr>
<td>col1</td><td>col1</td><td>col1</td>
</tr>
</thead>
<tbody>
<tr><td><b>Termin</td><td><?php echo getFormattedDateTime($ag,'termin')?></td><td class="<?php echo $classTermin1?>"><b>Teilnehmer: <?php echo $countTermin1?> (noch nicht informiert: <?php echo $countTermin1Offen?>)</td></tr>

<?php if (isset($ag['termin_ueberbuchung'])) {?>
<tr><td><b>2. Termin</td><td><?php echo getFormattedDateTime($ag,'termin_ueberbuchung')?></td><td class="<?php echo $classTermin2?>"><b>Teilnehmer: <?php echo $countTermin2?> (noch nicht informiert: <?php echo $countTermin2Offen?>)</td></tr>
<?php } else {?>
<tr><td><b>2. Termin</td><td>-</td><td>&nbsp;</td></tr>
<?php } ?>

<?php if (isset($ag['termin_ersatz'])) {?>
<tr><td><b>Ersatztermin</td><td><?php echo getFormattedDateTime($ag,'termin_ersatz')?></td><td class="<?php echo $classErsatzTermin?>"><b>Teilnehmer: <?php echo $countErsatztermin?> (noch nicht informiert: <?php echo $countErsatzterminOffen?>)</td></tr>
<?php } else {?>
<tr><td><b>Ersatztermin</td><td>-</td><td>&nbsp;</td></tr>
<?php } ?>
<tr><td class="<?php echo $classTermin2?>"><b>Absagen</td><td>&nbsp;</td><td  class="<?php echo $classTermin2?>"><b><?php echo $countAbsagen?> Kinde(r) (Noch nicht informiert: <?php echo $countAbsagenOffen?>)</td></tr>

<?php if ($ag['max_kinder'] != NULL) {?>
<tr><td><b>Max</td><td><?php echo $ag['max_kinder']?> Kinder </td><td>&nbsp;</td></tr>
<?php } else { ?>
<tr><td><b>Max</td><td>-</td><td>&nbsp;</td></tr>
<?php } ?>
<tr><td><b>Kosten<br>(Mitglied/Nicht-Mitglied)</td><td><?php echo formatAsCurrency($ag['betrag_mitglied'])?></td><td><?php echo formatAsCurrency($ag['betrag_nicht_mitglied'])?></td></tr>
<tr><td><b>Verantwortlich</td><td><?php echo $ag['verantwortlicher_name']?></td><td><?php echo $ag['verantwortlicher_telefon'] . ", " . formatAsMailto($ag['verantwortlicher_mail'])?></td></tr>
</tbody>
</table>
</td><td valign="top">
<a type="button" id="btn_info_parents" href="prepareInfos.php?ag=<?php echo $id?>">Eltern informieren (Zusagen/Absagen)</a><br><br>
<a type="button" id="btn_info_parents_dates" href="#">Eltern informieren (Terminänderung)</a><br><br>
<a type="button" for="verantwortliche" href="printInfosVerantwortliche.php?ag=<?php echo $id?>&preview=true" target="_blank">Veranstalter informieren (Preview)</a><br><br>
<a type="button" for="verantwortliche" href="printInfosVerantwortliche.php?ag=<?php echo $id?>">Veranstalter informieren</a><br><br>
<a type="button" for="verantwortliche" href="printInfosVerantwortliche.php?ag=<?php echo $id?>&preview=true&rueckmeldung=true" target="_blank">Veranstalter-Rückmeldung (Preview)</a><br><br>
<a type="button" for="verantwortliche" href="printInfosVerantwortliche.php?ag=<?php echo $id?>&rueckmeldung=true">Veranstalter-Rückmeldung</a><br><br>
</td>
<td valign="top">

<form action="manageAG.php" method="post" id="editCommentForm">
<table id="editAG2" class="display" style="width:100%; text-align: left; margin-right:auto;margin-left:0px">
<thead style="display: none;">
<tr>
<td>col1</td>
<td>col1</td>
</tr>
</thead>
<tbody>
<tr><td valign="top">Kommentar (Mail)<br><textarea cols="40" rows="12" name="kommentar"><?php echo $ag["kommentar"];?></textarea></td>
    <td valign="top">Kommentar (Privat)<br><textarea cols="40" rows="12" name="kommentar_privat"><?php echo $ag["kommentar_privat"];?></textarea></td>
</tr>
<tr><td valign="top"><input type="submit" name="speichern" value="Speichern"></td><td>&nbsp;</td></tr>
</tbody>
</table>

<input type="hidden" name="editComment" value="true"/>
<input type="hidden"  name="ag" value="<?php echo $id?>">
</form>

</td>
</tr>
</table>
</div>
</div>

<br>

<div id="dialog-editAG" title="AG-Daten ändern">
<form action="manageAG.php" method="post" id="editAGForm">
<table id="editAG" class="display" style="width:100%; text-align: left; margin-right:auto;margin-left:0px">
<thead style="display: none;">
<tr>
<td>col1</td><td>col1</td><td>col1</td><td>col1</td><td>col1</td>
</tr>
</thead>
<tbody>
<?php $typ = 'termin'?>
<tr><td valign="middle">1. Termin</td><td valign="middle"><input size="10" maxlength="10" type="text" name="<?php echo $typ?>" value="<?php echo formatSQLDate($ag[$typ]);?>" dateField="true"></td><td valign="middle">von: <input size="5" maxlength="5" type="text" name="<?php echo $typ?>_von" value="<?php echo $ag[$typ.'_von'];?>"></td><td valign="middle">bis: <input size="5" maxlength="5" type="text" name="<?php echo $typ?>_bis" value="<?php echo $ag[$typ.'_bis'];?>"></td><td  id="info_termin"></td></tr>
<?php $typ = 'termin_ueberbuchung'?>
<tr><td valign="middle">2. Termin</td><td valign="middle"><input size="10" maxlength="10" type="text" name="<?php echo $typ?>" value="<?php echo formatSQLDate($ag[$typ]);?>" dateField="true"></td><td valign="middle">von: <input size="5" maxlength="5" type="text" name="<?php echo $typ?>_von" value="<?php echo $ag[$typ.'_von'];?>"></td><td valign="middle">bis: <input size="5"  maxlength="5" type="text" name="<?php echo $typ?>_bis" value="<?php echo $ag[$typ.'_bis'];?>"></td><td  id="info_termin_ueberbuchung"></td></tr>
<?php $typ = 'termin_ersatz'?>
<tr><td valign="middle">Ersatztermin</td><td valign="middle"><input size="10" maxlength="10" type="text" name="<?php echo $typ?>" value="<?php echo formatSQLDate($ag[$typ]);?>" dateField="true"></td><td valign="middle">von: <input size="5" maxlength="5" type="text" name="<?php echo $typ?>_von" value="<?php echo $ag[$typ.'_von'];?>"></td><td valign="middle">bis: <input size="5"  maxlength="5" type="text" name="<?php echo $typ?>_bis" value="<?php echo $ag[$typ.'_bis'];?>"></td><td  id="info_termin_ersatz"></td></tr>
</tbody>
</table>
<hr>
<table id="editAG3" class="display" style="width:100%; text-align: left; margin-right:auto;margin-left:0px">
<thead style="display: none;">
<tr>
<td>col1</td><td>col1</td>
</tr>
</thead>
<tbody>
<tr><td valign="middle">Max. Anzahl Kinder</td><td valign="middle"><input size="10" maxlength="10" type="text" name="max" value="<?php echo $ag["max_kinder"]?>"></td></tr>
<tr><td valign="middle">Verantwortlich</td><td valign="middle"><input size="30" maxlength="75" type="text" name="verantwortlicher_name" value="<?php echo $ag["verantwortlicher_name"]?>"></td></tr>
<tr><td valign="middle">Mail</td><td valign="middle"><input size="30" maxlength="50" type="text" name="verantwortlicher_mail" value="<?php echo $ag["verantwortlicher_mail"]?>"></td></tr>
<tr><td valign="middle">Telefon</td><td valign="middle"><input size="30" maxlength="25" type="text" name="verantwortlicher_telefon" value="<?php echo $ag["verantwortlicher_telefon"]?>"></td></tr>
</tbody>
</table>
<input type="hidden" name="editAG" value="true"/>
<input type="hidden"  name="ag" value="<?php echo $id?>">
</form>
</div>

<div id="dialog-defineDateChanges" title="Welche Termin-Änderungen sollen mitgeteilt werden?">
<form action="printInfosDates.php" method="post" id="defineChangesForm" target="_blank">

<table id="printInfosDates" class="display" style="width:680px; text-align: left; margin-right:auto;margin-left:0px">
<thead style="display: none;">
<tr>
<td>col1</td><td>col1</td><td>col1</td><td>col1</td>
</tr>
</thead>
<tbody>
<?php $typ = 'termin';
if (isset($ag[$typ])) { ?>
<tr><td valign="middle">1. Termin</td><td valign="middle"><input type="checkbox" name="<?php echo $typ?>" value="true"></td><td valign="middle">Hinweis neue Uhrzeit <input type="checkbox" name="<?php echo $typ?>_neueUhrzeit" value="true"></td>
<td>
<?php if (AgModel::countAnmeldungenForAgNoMail($id, 'zusage') > 0) { ?><img src="../images/trans.png" class="sprite warning">&nbsp;Achtung es gibt Anmeldungen ohne Mail-Adresse!<?php } ?>
&nbsp;</td>
</tr>

<?php } ?>
<?php  $typ = 'termin_ueberbuchung';
if (isset($ag[$typ])) {
?>
<tr><td valign="middle">2. Termin</td><td valign="middle"><input type="checkbox" name="<?php echo $typ?>" value="true"></td><td valign="middle">Hinweis neue Uhrzeit <input type="checkbox" name="<?php echo $typ?>_neueUhrzeit" value="true"></td>
<td>
<?php if (AgModel::countAnmeldungenForAgNoMail($id, 'termin2') > 0) { ?><img src="../images/trans.png" class="sprite warning">&nbsp;Achtung es gibt Anmeldungen ohne Mail-Adresse!<?php } ?>
&nbsp;</td>


</tr>
<?php } ?>
<?php $typ = 'termin_ersatz';
if (isset($ag[$typ])) {
?>
<tr><td valign="middle">Ersatztermin</td><td valign="middle"><input type="checkbox" name="<?php echo $typ?>" value="true"></td><td valign="middle">Hinweis neue Uhrzeit <input type="checkbox" name="<?php echo $typ?>_neueUhrzeit" value="true"></td>
<td>
<?php if (AgModel::countAnmeldungenForAgNoMail($id, 'ersatztermin') > 0) { ?><img src="../images/trans.png" class="sprite warning">&nbsp;Achtung es gibt Anmeldungen ohne Mail-Adresse!<?php } ?>
&nbsp;</td>

</tr>
<?php } ?>

<tr><td valign="middle">Preview</td><td valign="middle"><input type="checkbox" name="preview" value="true"></td><td>&nbsp;</td><td>&nbsp;</td></tr>

</tbody>
</table>

<input type="hidden" name="editAG" value="true"/>
<input type="hidden"  name="ag" value="<?php echo $id?>">
</form>
</div>

<table id="anmeldungen" class="display">
    <thead>
        <tr>
        	<th>#</th>
            <th>Schüler</th>
            <th>Klasse</th>
            <th>Kontakt</th>
            <th>Anmelde-Nr.</th>
            <th>Zahlart</th>
            <th>Angelegt</th>
            <th>Geprüft</th>
            <th>Anzahl Anmeldungen</th>
            <th>Absagen</th>
        	<th>Status<br>(lokal)</th>
        	<th>Status<br>(öffentl.)</th>
        	<th>Aktionen</th>
        </tr>
    </thead>
    <tbody>
    
    <?php
    	$ags = AgModel::getAnmeldungenForAg($id);
    	$count = 0;
		foreach ($ags as $ag) {
			$count++;
			$name = $ag['schueler_name'];
			$klasse = $ag['klasse'];
			$telefon = $ag['telefon'];
			$mail = $ag['mail'];
			$anmeldeNummer = $ag['anmelde_nummer'];
				
			$countAbsagen = AgModel::countAnmeldungenForSchueler($name, "absage");
			$countAll = AgModel::countAnmeldungenForSchueler($name);
			$datum = formatSQLDateTime($ag['datum_eingang'],false,"<br>");
			$datumPruefung = formatSQLDateTime($ag['datum_geprueft'],false,"<br>");
			$status = $ag['status_anmeldung'];
			$statusText = AgModel::getStatusText($status);
			$statusMail = $ag['status_mail'];
			$statusMailText = AgModel::getStatusText($statusMail);
			$id2 = $ag['id'];
			
			$aktionen = "";
			
			$typen = array("zusage" => "Zusagen" ,"absage" => "Absagen");
			
			if (isset($ag['termin_ersatz'])) {
				$typen["ersatztermin"] = "Umbuchen Ersatztermin";
			}
			if (isset($ag['termin_ueberbuchung'])) {
				$typen["termin2"] = "Umbuchen Termin 2";
			}
			
			if ($_SESSION["resetAktiv"] == "true")
				$typen["reset"] = "Reset";
				
			$page = $_SERVER['PHP_SELF'];
			
			foreach ($typen as $typ => $label) {
				if ($status != $typ) {
					$aktionen = $aktionen . "<a type='button' href=\"$page?id=$id2&ag=$id&newType=$typ\">$label</a>&nbsp;";
				}
			}
			
			$css = "";
			$showWarningStatus = false;
			if ($status == "nicht_geprueft") {
				$css = "warning";
				//$showWarningStatus = true;
			}
			
		?>    
        <tr>
        	<td class="<?php echo $css?>"><?php echo $count?></td>
        	<td class="<?php echo $css?>"><?php echo $name?></td>
        	<td class="<?php echo $css?>"><?php echo $klasse?></td>
        	<td class="<?php echo $css?>"><?php echo $telefon?>, <?php echo formatAsMailto($mail)?></td>
        	<td class="<?php echo $css?>"><?php echo formatAsAbfragenLink("../",$anmeldeNummer)?></td>
        	<td class="<?php echo $css?>"><?php echo formatZahlart($ag['zahlart'])?></td>
        	<td class="<?php echo $css?>"><?php echo $datum?></td>
        	<td class="<?php echo $css?>"><?php echo $datumPruefung?></td>
        	<td class="<?php echo $css?>"><?php echo $countAll?></td>
        	<td class="<?php echo $css?>"><?php echo $countAbsagen?></td>
        	<td class="<?php echo $css?>"><?php echo $statusText?><?php if ($showWarningStatus) {?><img class="rowIconRight" src="../images/trans.png" class="sprite warning"><?php }?></td>
        	<td class="<?php echo $css?>"><?php echo $statusMailText?></td>
        	<td class="<?php echo $css?>"><?php echo $aktionen?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<br>
<table width=100%>
  <tr>
    <td><a type='button' href="manageAGs.php">Alle AGs anzeigen</a></td>
    <td align="right">
    	<a type='button' href="manageAG.php?ag=<?php echo $id?>&alleZusagen=true">Alle 'Nicht bestätigten' zusagen</a>
    	<?php if ($_SESSION["resetAktiv"] != "true") { ?>
	    	<a type='button' href="manageAG.php?ag=<?php echo $id?>&resetAktiv=true" title="Aktiviert die Funktion 'Reset' für alle Anmeldungen, führt aber keinen Reset durch" >Reset aktivieren</a>
    	<?php } ?>
    </td>
  </tr>
</table>



<?php include 'footer.php';?>

