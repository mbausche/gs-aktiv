<?php
	include '../db.php';
	require_once("../funktionen.php");
	
	session_start();
	
	$aktion = $_REQUEST['aktion'];
	$id = $_REQUEST['id'];
	$checkForMailNeuesMitglied = array();
	$iban = $_REQUEST["iban"];
	$kontoinhaber = $_REQUEST["kontoinhaber"];
	
	if (!empty($id) && !empty($aktion)) {
		if ($aktion == "pruefe") {
			$anmeldung = AgModel::getAnmeldungById($id);
			if ($anmeldung['geprueft'] == 0) {
				AgModel::setGeprueft($id, $iban, $kontoinhaber);
				$anmeldung = AgModel::getAnmeldungById($id);
				StatusModel::updateStatus($anmeldung['name'], $anmeldung["ist_mitglied"] );
				array_push($checkForMailNeuesMitglied, $id);
			} 
		}else if ($aktion == "pruefeAlle") {
			$ids = explode("|", $id);
			foreach ($ids as $idTmp) {
				$anmeldung = AgModel::getAnmeldungById($idTmp);
				if ($anmeldung['geprueft'] == 0) {
					AgModel::setGeprueft($idTmp);
					$anmeldung = AgModel::getAnmeldungById($idTmp);
					StatusModel::updateStatus($anmeldung['name'], $anmeldung["ist_mitglied"] );
					array_push($checkForMailNeuesMitglied, $idTmp);
				}
			}
		} else if ($aktion == "loesche") {
			AgModel::loescheAnmeldung($id);
			
		} else if ($aktion == "asMitglied") {
			$anmeldung = AgModel::getAnmeldungById($id);
			if ($anmeldung["ist_mitglied"] == 0) {
				$anmeldung = AgModel::updateAnmeldungById($id, 1, 1, $iban, $kontoinhaber);
			} else {
				AgModel::setGeprueft($id, $iban, $kontoinhaber);
			}
			StatusModel::updateStatus($anmeldung['name'], 1 );
			array_push($checkForMailNeuesMitglied, $id);
		} else if ($aktion == "asNichtMitglied") {
			$anmeldung = AgModel::getAnmeldungById($id);
			if ($anmeldung["ist_mitglied"] == 1) {
				$anmeldung = AgModel::updateAnmeldungById($id, 0, 1, $iban, $kontoinhaber);
			} else {
				AgModel::setGeprueft($id, $iban, $kontoinhaber);
			}
			StatusModel::updateStatus($anmeldung['name'], 0 );
		}
		if (count($checkForMailNeuesMitglied) > 0) {
			$pdfPath = "../pdf";
			foreach ($checkForMailNeuesMitglied as $idTmp) {
				$anmeldung = AgModel::getAnmeldungById($idTmp);
				if ($anmeldung['moechte_mitglied_werden'] == 1) {
					$name = $anmeldung['name'];
					$anmeldeNummer = $anmeldung['anmelde_nummer'];
					include 'include_mailNeuesMitglied.php';
				}
			}
			
		}
	}
	
	
	
	$tableId = "";
	$title =  "Anmeldungen prüfen";
	
	include 'header.php';
	
	$anmeldungen = AgModel::getAnmeldungenNichtGeprueft();
	$status = StatusModel::getAll();
	
	if (count($anmeldungen) == 0 ) {
		echo "Derzeit gibt es keine Anmeldungen die geprüft werden müssen!";
		echo "<script>setContentTableVisible();</script>";
		exit;
	} else {
	
?>

<script>

var dataTable;
var zahlart = "";

$(document).ready(function() {

	
	
	$( "#dialog-iban" ).hide();

	$( "#zahlartfilter" ).buttonset();
	
	dataTable = $('#anmeldungsTable').DataTable({
	        "paging":   false,
	        "info": false,
	        "bJQueryUI": true,
	        "sDom": 'lfrtip',
			/* Disable initial sort */
	        "aaSorting": []
			<?php echo "," . $dataTableLangSetting ?>
	 });

	 //Standard SearchhBox entfernen
	 $("#anmeldungsTable_filter").remove();
	

	$('#editIban').DataTable( {
	    	"paging":   false,
	        "ordering": false,
	        "searching": false,
	        "info":     false,
	        "bJQueryUI": true,
	        "sDom": 'lfrtip'
	    	<?php echo "," .$dataTableLangSetting ?>
	} );	

	$("#searchField").keyup(function() {
		searchTerm = $(this).val();
		var url = 'storeSessionData.php?key=pruefeAnmeldungen.anmeldungen_searchTerm&value=' + searchTerm;
		ajaxCall(url,ajaxok);
		
		filter();
	});
	

	$("[ibanButton='true']").click(function() {
			$("#iban_dlg_id").val($(this).attr("iban_dlg_id"));
			$("#iban_dlg_iban").val($(this).attr("iban_dlg_iban"));
			$("#iban_dlg_kontoinhaber").val($(this).attr("iban_dlg_kontoinhaber"));
			$("#iban_dlg_aktion").val($(this).attr("iban_dlg_aktion"));
			
			$( "#dialog-iban" ).dialog( {
		        modal: true,
		        width: 750,
		        buttons: {
		          Ok: function() {
		            $( "#ibanForm" ).submit();
		          }
		        }
		    });
	});

	$("#iban_dlg_iban").keyup(function() {
		iban = $(this).val();
		var msg = checkIban(iban);
		$("#iban_dlg_error_msg").html(msg);
	});
	

	$( "[buttonforZahlart='true']" ).click(function() {
		zahlart = $(this).val();
		var url = 'storeSessionData.php?key=pruefeAnmeldungen.zahlart&value=' + zahlart;
		ajaxCall(url,ajaxok);
		filter();
	});

	<?php 
		$filter = $_SESSION['pruefeAnmeldungen.zahlart'];
		if (!empty($filter)) {
			echo '$("#'. $filter.'").attr("checked",true).button("refresh");';
			echo "zahlart = '$filter';";
			echo "filter();";
	 	}
	?>
	 
	 $( "[type='buttonDelete']" ).button({
		  icons: { primary: "ui-icon-trash" }
	 });

	 function ajaxok() {

	 }
	 
	 function filter() {
		if (zahlart == "bank")
			zahlart = "Überweisung";
		else if (zahlart == "alle")
			zahlart = "";

		searchTerm = $("#searchField").val();

		if (zahlart.length > 0) {
			if (searchTerm.length > 0) {
				searchTerm = searchTerm + " ";
			}
			searchTerm = searchTerm + zahlart;
		}

		dataTable = dataTable.search(searchTerm, false, true, true);
		dataTable.draw();
		
		

	 }

	 setContentTableVisible();
	 
	  
});
</script>

<div id="dialog-iban" title="IBAN und Kontoinhaber eingeben">
<form action="pruefeAnmeldungen.php" method="post" id="ibanForm">
<table id="editIban" class="display" style="width:100%; text-align: left; margin-right:auto;margin-left:0px">
<thead style="display: none;">
<tr>
<td>col1</td><td>col1</td>
</tr>
</thead>
<tbody>
<tr><td valign="middle">IBAN</td><td valign="middle"><input size="30" maxlength="30" type="text" id="iban_dlg_iban" name="iban" value=""></td></tr>
<tr><td valign="middle">Kontoinhaber</td><td valign="middle"><input size="30" maxlength="50" type="text" id="iban_dlg_kontoinhaber" name="kontoinhaber" value=""></td></tr>
</tbody>
</table>
<input type="hidden" id="iban_dlg_id" name="id" value="">
<input type="hidden" id="iban_dlg_aktion" name="aktion" value="">
</form>
<br>
<span id="iban_dlg_error_msg" class="error"></span>
</div>

<?php 

$schuelerNamen = AgModel::getPersonenMitMehrAlsEinerAnmeldung();
if (count($schuelerNamen) > 0) { ?>

<div class="hint-warning">
<b>Hinweis: Für folgende Schüler gibt es mehr als eine Anmeldung:</b><br><br>
<?php foreach ($schuelerNamen as $n) {
	$anz = $n['anzahl'];
	$name = $n['name'];
	$klasse = $n['klasse'];
	echo "$name $klasse ($anz)<br>";
}?>
</div>

<?php
}
?>
<table width="100%">
<tr>
<td>
<div id="zahlartfilter">
<nobr>
<input buttonForZahlart='true' type='radio' id="bank" name='zahlart' value='bank'><label for='bank'>Filter: Bank</label>
<input buttonForZahlart='true' type='radio' id="schule" name='zahlart' value='schule'><label for='schule'>Filter: Schule</label>
<input buttonForZahlart='true' type='radio' id="alle" name='zahlart' value='alle'><label for='alle'>Filter: Alle</label>
</nobr>
</div>
</td>
<td align="right" width="80%">
Suchen nach: <input type="text" id="searchField" value="<?= $_SESSION["pruefeAnmeldungen.anmeldungen_searchTerm"] ?>">
</td></tr>
</table>


<table id="anmeldungsTable" class="display">
    <thead>
        <tr>
            <th>Nr</th>
        	<th>Name</th>
        	<th>Klasse</th>
        	<th>Zahlart</th>
        	<th>Mitglied (alt)</th>
        	<th>Mitglied (in Anmeldung)</th>
        	<th>Info</th>
        	<th>AGs</th>
        	<th>Datum</th>
        	<th>Betrag</th>
            <th>Aktionen</th>
        </tr>
    </thead>
    <tbody>
		<?php 
			
			$okIds = array();
		
			foreach ($anmeldungen as $anmeldung) {
			
			$id = $anmeldung['id'];
			$nr = $anmeldung['anmelde_nummer'];
			$name = $anmeldung['name'];
			$mail = $anmeldung['mail'];
			$telefon = $anmeldung['telefon'];
			
			$klasse = $anmeldung['klasse'];
			$datum = formatSQLDateTime(AgModel::getDateOfAnmeldungsEingang($id),false,"<br>");
			$ags = AgModel::getAgsOfAnmeldungAsHtml($id);
			
			$statusDBObject = $status[mb_strtolower($name)];
			$statusValue = 0;
			if (isset($statusDBObject) && $statusDBObject["status"] == 1) {
				$statusValue = 1;
			}
			$mitgliedAlt = formatAsYesNo($statusValue);
			$mitgliedAnmeldung = formatAsYesNo($anmeldung['ist_mitglied']);
			
			
			$css = "ok";
			$toolTip = "";
			$addButtonOK = true;
				
			if ($mitgliedAlt == "Ja") {
				if ($mitgliedAnmeldung == "Nein") {
					$css = "warning";
					$toolTip = "War Mitglied ist es aber nicht mehr";
					$addButtonOK = false;
				} 
			} else if ($mitgliedAlt == "Nein") {
				if ($mitgliedAnmeldung == "Ja") {
					$css = "error";
					$toolTip = "Ist noch kein Mitglied, meint er ist eins. Vielleicht hat er auch bereits einen Antrag abgegeben";
					$addButtonOK = false;
				}
			}
			$betrag = formatAsCurrency($anmeldung['betrag']);
			$betragZahl = $anmeldung['betrag'];
			$zahlart = $anmeldung['zahlart'];
			$iban = $anmeldung['iban'];
			$kontoinhaber = $anmeldung['kontoinhaber'];
			
			if ($addButtonOK == false) {
				$betragMG = AgModel::getBetragOfAnmeldung($anmeldung['id'], true);
				$betragNMG = AgModel::getBetragOfAnmeldung($anmeldung['id'], false);
					
				$textMG = "";
				$ttipMG = "";
				if ($betragZahl > $betragMG) {
					$textMG = " <span class='rueckzahlung'>" . formatAsCurrency($betragZahl-$betragMG) ."</span>";
					$ttipMG = "\nDer Schüler bekommt eine Rückzahlung";
				} else if ($betragZahl < $betragMG) {
					$textMG = " <span class='nachzahlung'>-" . formatAsCurrency($betragMG-$betragZahl) ."</span>";
					$ttipMG = "\nDer Schüler muss nachbezahlen";
				}
					
				$textNMG = "";
				$ttipNMG = "";
				if ($betragZahl > $betragNMG) {
					$textNMG = " <span class='rueckzahlung'>" . formatAsCurrency($betragZahl-$betragNMG) ."</span>";
					$ttipNMG = "\nDer Schüler bekommt eine Rückzahlung";
				} else if ($betragZahl < $betragNMG) {
					$textNMG = " <span class='nachzahlung'>-" . formatAsCurrency($betragNMG-$betragZahl) ."</span>";
					$ttipNMG = "\nDer Schüler muss nachbezahlen";
				}
			} else {
				array_push($okIds, $anmeldung['id']);
			}
			
		?>   
        <tr>
        	<td><?php echo formatAsAbfragenLink("../",$nr)?></td>
        	<td><b><?php echo $name?></b><br><?php echo formatAsMailto($mail)?>, <?php echo $telefon?></td>
        	<td><?php echo $klasse?></td>
        	<td><?php echo formatZahlart($anmeldung['zahlart'])?></td>
        	<td class="<?php echo $css?>"><?php echo $mitgliedAlt?></td>
        	<td class="<?php echo $css?>"><?php echo $mitgliedAnmeldung?></td>
        	<td>
        	<?php if (!empty($toolTip)) {?>
        	<img src="../images/trans.png"  class="sprite warning" title="<?php echo $toolTip?>" alt="<?php echo $toolTip?>">
        	<?php } ?>
        	</td>
        	<td><?php echo $ags?></td>
        	<td><?php echo $datum?></td>
        	<td class="right"><?php echo $betrag?></td>
        	<td><nobr>
        	<?php if ($addButtonOK == true) { ?>
        		<?php if ($zahlart == 'schule') { ?>
	        		<a type="button" title="Past so!" href="<?php echo $_SERVER['PHP_SELF']?>?aktion=pruefe&id=<?php echo $id?>">OK</a>
        		<?php } else { ?>
        			<a type="button" ibanButton='true' title="Past so! - Noch IBAN und Kontoinhaber hinterlegen" iban_dlg_id="<?php echo $id?>" iban_dlg_kontoinhaber="<?php echo $kontoinhaber?>" iban_dlg_aktion='pruefe' iban_dlg_iban="<?php echo $iban?>" href="#!">OK</a>
        		<?php } ?>
        	<?php } else { 
        		if ($zahlart == 'schule') {
					$hrefNMG = $_SERVER['PHP_SELF'] . "?aktion=asNichtMitglied&id=$id";
					$attribsNMG="";
					$hrefMG = $_SERVER['PHP_SELF'] . "?aktion=asMitglied&id=$id";
					$attribsMG="";
				} else {
					$hrefNMG = "#!";
					$attribsNMG=" ibanButton='true' iban_dlg_id='$id' iban_dlg_kontoinhaber='$kontoinhaber' iban_dlg_aktion='asNichtMitglied' iban_dlg_iban='$iban' ";
					$hrefMG = "#!";
					$attribsMG=" ibanButton='true' iban_dlg_id='$id' iban_dlg_kontoinhaber='$kontoinhaber' iban_dlg_aktion='asMitglied' iban_dlg_iban='$iban' ";
				}
        			
        	?>
        	<a type="button" title="Ist Nicht-Mitglied<?php echo $ttipNMG?>" href="<?php echo $hrefNMG?>" <?php echo  $attribsNMG?> >NMG<?php echo $textNMG?></a>
        	<a type="button" title="Ist Mitglied<?php echo $ttipMG?>" href="<?php echo  $hrefMG?>" <?php echo  $attribsMG?> >MG<?php echo $textMG?></a>
        	<?php } ?>
        	<a type="buttonDelete" title="Löschen" href="<?php echo $_SERVER['PHP_SELF']?>?aktion=loesche&id=<?php echo $id?>"></a>
        	</nobr>
        	</td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<?php if (count($okIds) > 0) {?>
<br>
<a type="button" title="Alle OK-Anmeldungen bestätigen" href="<?php echo $_SERVER['PHP_SELF']?>?aktion=pruefeAlle&id=<?php echo join("|", $okIds)?>">Alle OK-Anmeldungen bestätigen</a>
<?php } ?>

<?php 
}
include 'footer.php';

?>

