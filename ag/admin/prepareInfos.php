<?php
	include '../db.php';
	require_once("../funktionen.php");

	ini_set("log_errors", 1);     /* Logging "an" schalten */
	ini_set("error_log", "errorlog.txt");     /* Log-Datei angeben */
	
	
	$id = $_REQUEST['ag'];
	
	if ($id == "") {
		die("Es wurde keine AG ausgewählt");
	}
	
	$ag = AgModel::getAg($id);
	$ags = AgModel::getAnmeldungenForAg($id);
	
	if (isset($doPrint)) {
		foreach($_REQUEST as $key => $value) {
			if (startsWith($key, "print_")) {
				$id_anmeldung = $value;
				$html = false;
				$printHeader = true;
				ob_start();
				include "doc_absage.php";
				$content = ob_get_clean();
				require_once('ext/html2pdf/html2pdf.class.php');
				error_log("Content: "  .$content);
// 				echo $content;
// 				die();
				// seitenränder (in mm)
				$oben=30;    //mT
				$unten=10;   //mB
				$links=15;   //mL
				$rechts=15;  //mR
				$html2pdf = new HTML2PDF('P','A4','de', true, 'UTF-8', array(15, 30, 15, 10));
				$html2pdf->pdf->SetDisplayMode('real');
				$html2pdf->WriteHTML($content);
				$html2pdf->Output($pdf);				
			}
		}
		
	} else {
		$title =  "Infos für Eltern-AG '" . $id  ." - " . $ag["name"] . "' drucken / versenden";
		$tableId = "";
		include 'header.php';
?>

<script>
$(document).ready(function() {

	$( "input[cbType=selectRow]" ).click(function( event ) {
		updatePrintButtonState();
    });

	$( "input[cbType=selectAllSchule]" ).click(function( event ) {
		selectAll("schule",this.checked);
    });
	
	$( "input[cbType=selectAllMail]" ).click(function( event ) {
		selectAll("mail",this.checked);
	});

	$( "input[cbType=selectAllTelefon]" ).click(function( event ) {
		selectAll("telefon",this.checked);
    });

	$( "input[cbType=selectAllBank]" ).click(function( event ) {
		selectAll("bank",this.checked);
    });
	
	$( "input[cbType=selectAllPasst]" ).click(function( event ) {
		selectAll("passt",this.checked);
    });
	
	$( "input[commType=mail]").each(function () {
		if (this.checked) {
			$( "input[cbType=selectAllMail]" ).prop('checked', true);
		}
	});
	$( "input[commType=schule]").each(function () {
		if (this.checked) {
			$( "input[cbType=selectAllSchule]" ).prop('checked', true);
		}
	});
	$( "input[commType=telefon]").each(function () {
		if (this.checked) {
			$( "input[cbType=selectAllTelefon]" ).prop('checked', true);
		}
	});

	$( "input[commType=bank]").each(function () {
		if (this.checked) {
			$( "input[cbType=selectAllBank]" ).prop('checked', true);
		}
	});
	
	
	$( "input[commType=passt]").each(function () {
		if (this.checked) {
			$( "input[cbType=selectAllPasst]" ).prop('checked', true);
		}
	});
	
	
	updatePrintButtonState();

	var table = $('#infos').DataTable({
 	 	"paging":   false,
        "info":     true,
        "bJQueryUI": true,
        "sDom": 'ft',
 	 	/* Disable initial sort */
        "aaSorting": []
		<?php echo "," . $dataTableLangSetting ?>
	});
	table
    .columns( '.action' )
    .order( 'asc' )
    .draw();    

	setContentTableVisible();
	
} );

function selectAll(commType, checked) {
	$( "input[commType="+commType+"]" ).prop('checked', checked);
	updatePrintButtonState();
}

function updatePrintButtonState() {

	var rows = 0; 
	var rowsOK = 0;
	$("tr.anmeldeRow").each(function () {
		rows++;
		var checked = false;
		$(this).find("input[cbType=selectRow]" ).each(function () {
			if (this.checked) {
				checked = true;
			}
	    });
	    if (checked)
		    rowsOK ++;
	});
	var enable = rows == rowsOK ? "enable" : "disable";
    $("#btn_submit").button( enable );
    $("#btn_preview").button( enable );
    
    if (rows != rowsOK)
    	$("#hint").show();
    else
    	$("#hint").hide();

    $("#btn_preview").click(function( event ) {
    	$("input[name=preview]").val("true");
    	$("#mainForm").submit();
    });

    $("#btn_submit").click(function( event ) {
    	$("input[name=preview]").val("false");
    	$("#mainForm").submit();
    });
    
}

</script>

<form id="mainForm" action="printInfos.php" method="post" target="_blank">

<input type="hidden" name="ag" value="<?php echo $id?>"/>

<table id="infos" class="display">
    <thead>
        <tr>
            <th>Info an Schule</th>
            <th>Info per Mail</th>
            <th>Info per Telefon</th>
            <th>Überweisung</th>
            <th>Passt so</th>
            <th class="action">Aktion</th>
            <th>Schüler</th>
            <th>Mail</th>
            <th>Telefon</th>
            <th>Klasse</th>
            <th>Datum der Anmeldung</th>
            <th>Zahlart</th>
        </tr>
    </thead>
    <tbody>
    
    <?php
		foreach ($ags as $ag) {
			$status = $ag['status_anmeldung'];
			$sendConfirmation = $ag['send_confirmation'];
			if ($status != $ag['status_mail'] 
				&&  $status != "nicht_geprueft"
				) {
				$name = $ag['schueler_name'];
				$klasse = $ag['klasse'];
				$datum = $ag['datum_eingang'];
				$id_anmeldung = $ag['id'];
				
				$telefon = $ag['telefon'];
				$mail = $ag['mail'];
				$zahlart = $ag['zahlart'];
				
				$statusPasst = $statusMail = $statusSchule = $statusTelefon = 1;
				$statusBank = 0;

				if ($status == "absage") {
					if ($zahlart == "schule") {
						$statusSchule = 2;
					} else if (!empty($mail)) {
						$statusMail = 2;
					} else {
						$statusTelefon = 2;
					}
					if ($zahlart == "bank") {
						$statusBank = 2;
					}

				} else if ($status == "ersatztermin" || $status == "termin2") {
					if (!empty($mail)) {
						$statusMail = 2;
					} else if ($zahlart == "schule") {
						$statusSchule = 2;
					} else {
						$statusTelefon = 2;
					}
				} else if ($status == "zusage") {
					if (!empty($mail) && $sendConfirmation == 1) {
						$statusMail = 2;
					} else {
						$statusPasst = 2;
					}
				}
				
				
				
				?>
        <tr class="anmeldeRow">
        	<td><?php if ($statusSchule > 0) {?><input <?php if ($statusSchule == 2) { echo "checked"; }?> type="checkbox" commType="schule" cbType="selectRow" name="schule_<?php echo $id_anmeldung?>" value="<?php echo $id_anmeldung?>"/><?php } ?></td>
        	<td><?php if ($statusMail > 0) {?><input <?php if ($statusMail == 2) { echo "checked"; }?> type="checkbox" commType="mail" cbType="selectRow" name="mail_<?php echo $id_anmeldung?>" value="<?php echo $id_anmeldung?>"/><?php } ?>&nbsp;</td>
        	<td><?php if ($statusTelefon > 0) {?><input <?php if ($statusTelefon == 2) { echo "checked"; }?> type="checkbox" commType="telefon" cbType="selectRow" name="telefon_<?php echo $id_anmeldung?>" value="<?php echo $id_anmeldung?>"/><?php } ?></td>
        	<td><?php if ($statusBank > 0) {?><input checked type="checkbox" commType="bank" cbType="selectRow" commType="bank" name="bank_<?php echo $id_anmeldung?>" value="<?php echo $id_anmeldung?>"/><?php } ?></td>
        	<td><?php if ($statusPasst > 0) {?><input <?php if ($statusPasst == 2) { echo "checked"; }?> type="checkbox" commType="passt" cbType="selectRow" name="passt_<?php echo $id_anmeldung?>" value="<?php echo $id_anmeldung?>"/><?php } ?></td>
        	<td><?php echo AgModel::getActionText($status); ?></td>
        	<td><?php echo $name?></td>
        	<td><?php echo $mail?>&nbsp;</td>
        	<td><?php echo $telefon?></td>
        	<td><?php echo $klasse?></td>
        	<td><?php echo formatSQLDate($datum)?></td>
        	<td><?php echo formatZahlart($ag['zahlart'])?></td>
        </tr>
        <?php } } ?>
    </tbody>
</table>
<br>
Alle an/aus: 
<input type="checkbox" cbType="selectAllSchule">Schule
<input type="checkbox" cbType="selectAllMail">Mail
<input type="checkbox" cbType="selectAllTelefon">Telefon
<input type="checkbox" cbType="selectAllBank">Überweisung
<input type="checkbox" cbType="selectAllPasst">Passt
<br>
<br>
<input type="hidden" name="preview" value="false">
Grund für die Absage: <select name="absageGrund">
<option value="zuwenig">Zu Wenig Teilnehmer</option>
<option value="zuviel">Zu Viele Teilnehmer</option>
<option value="schueler">Der Schüler hat abgesagt</option>
<option value="helferkind">Helferkind muss nicht bezahlen</option>
<option value="sonstige">Sonstiger</option>
</select><br><br>
<input id="btn_preview" type="submit" name="ok" value="Druck starten / Mails versenden (Preview)" ><input id="btn_submit" type="submit" name="ok" value="Druck starten / Mails versenden" >&nbsp; <span id="hint">Hinweis: Pro Zeile muss mind. eine Kommunikationsform ausgewählt sein, damit der Druck gestartet werden kann!</span>
<br>
<br>
<a type='button' href="manageAG.php?ag=<?php echo $id?>&updateStates=true">Alles gut! - Öffentlichen Status aktualisieren</a>

</form>

<a type='button' href="manageAGs.php">Alle AGs anzeigen</a> <a type='button' href="manageAG.php?ag=<?php echo $id?>">Zurück zur AG <?php echo $id?></a>

<?php include 'footer.php'; } ?>

