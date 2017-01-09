<script type="text/javascript">

$( document ).ready(function() {
    $('#anmeldung').DataTable( {
        "paging":   false,
        "ordering": false,
        "searching": false,
        "info":     false,
        "bJQueryUI": true,
        "sDom": 'lfrtip',
        "columnDefs": [
           	{ "width": "150px", "targets": 0 }
        ]
        <?php echo "," . $dataTableLangSetting ?>
    
    } );

    $('#weiteres').DataTable( {
        "paging":   false,
        "ordering": false,
        "searching": false,
        "info":     false,
        "bJQueryUI": true,
        "sDom": 'lfrtip'
        <?php echo "," . $dataTableLangSetting ?>
    } );    

	<?php if (empty($saveButtonText)) $saveButtonText = "Weiter"; ?>
    
    $("#submitButton").append('<input type="button" id="ok" name="ok" value="<?php echo $saveButtonText?>">');
    $("#hinweisPflichtfelder").append("<br><b>Hinweis:</b>Alle Pflichtfelder sind mit '*' gekennzeichnet");
    
    $("[pflicht='true']").each(function() {
    	$(this).append("  *");
    });

    addDatePicker("<?php echo CfgModel::load("neue.ag.zeitraum.von") ?>","<?php echo CfgModel::load("neue.ag.zeitraum.bis") ?>","<?php echo $dirPrefix?>images");

    $( "#klasse" ).buttonset();
    initEventHandling();
});


<?php
$info = "<img src='" . $dirPrefix . "images/trans.png' class='sprite info'' style='vertical-align:text-top;padding-right:5px;'>";

$termine = NeueAgModel::getAlleTermineMitAg(NeueAgModel::getIdByEditToken($editToken));
$ferienString = CfgModel::load("neue.ag.ferien");
include 'admin/include_checkDate.php';
?>

function initEventHandling() {
	$("#ok").click(function() {
		submit();
	});
}


function submit() {

	 if ($(".captcha-hidden").val() != "true") {
		 showAlert("Hinweis","Bitte zuerst das richtige Puzzleteil auswählen");
		 return;
	 }

	 $( "*" ).removeClass( "error" );
	 var errors = null;
	
	 var textInput = $('#mainForm input[type="text"], #mainForm textarea, #mainForm input[sonstigeCheckbox="true"]');
	 textInput.each(function() {
		 var name = $(this).attr("name");
		 if ( name != "ausserdem" && name != "termin_ueberbuchung" && name != "anzahl_helfer" && name != "termin_ersatz" && ($(this).attr("type") == "checkbox" && $(this).is(':checked') == false  || $(this).val() == "")) {
			 if ($(this).attr("type") == "checkbox") {
				$(this).parentsUntil($("tbody")).last().children().addClass( "error" );
			 } else {
			 	$(this).parentsUntil($("tbody")).last().children().first().addClass( "error" );
			 }
			 if (errors == null)
				 errors = new Object();
			 errors["text"] = "Bitte alle markierten Elemente ausfüllen oder abhaken!";
		 } 
	 });

	if (	$("[name='klasse1']").is(':checked') == false &&
			$("[name='klasse2']").is(':checked') == false && 
			$("[name='klasse3']").is(':checked') == false && 
			$("[name='klasse4']").is(':checked') == false) {
		//$("[name='klasse4']").parent().parent().parent().children().first().addClass( "error" );
		$("[name='klasse4']").parentsUntil($("tbody")).last().children().first().addClass( "error" );
		 if (errors == null)
			 errors = new Object();
		 errors["klasse"] = "Mindestens eine Klasse auswählen";
	}
	 
	 
	if (errors != null) {
		msg = "";
		for (var Eigenschaft in errors) {
			msg = msg + errors[Eigenschaft] + "<br>";
		}
		showAlert("Hinweis",msg);
	} else
		$("#mainForm").submit();
	
}

Number.prototype.formatMoney = function(c, d, t){
	var n = this, 
	    c = isNaN(c = Math.abs(c)) ? 2 : c, 
	    d = d == undefined ? "." : d, 
	    t = t == undefined ? "," : t, 
	    s = n < 0 ? "-" : "", 
	    i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", 
	    j = (j = i.length) > 3 ? j % 3 : 0;
	   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};

</script>

<?php if (!empty($data["error"])) {?>
<script>
$(document).ready( function () {
	$( "#dialog-error" ).dialog({
	    modal: true,
	    position: { my: "top", at: "top+10%", of: window},
	    minWidth: 500,
	    dialogClass: "error-dialog",
	    buttons: {
	      Ok: function() {
	        $( this ).dialog( "close" );
	      }
	    }
	});
});
</script>
<div id="dialog-error" title="Fehler beim Verarbeiten des Formulares">
<?php echo $data["error"]?>
</div>
<?php } ?>
<div>
<form enctype="multipart/form-data" action="<?php echo $dirPrefix?>neue_ag_save.php" method="post" id="mainForm">
<input type="hidden" name="speichern" value="true" />

<?php 
addHiddenField($redirectAfterSave,"redirectAfterSave");
addHiddenField($_REQUEST["response"],"response");
addHiddenField($editForm,"editForm");
addHiddenField($forceUpdate, "forceUpdate");
addHiddenField($editToken,"edit_token");
?>

<input type="hidden" name="MAX_FILE_SIZE" value="1048576" />
<table class="formTable display" id="anmeldung" style="margin: 0">
<thead style="display: none;">
	<th>Feld</th>
    <th>Wert</th>
    <th>Kommentar</th>
</thead>
<tbody>
<?php if ($includeAGNummer) { ?>
<tr><td pflicht="true"><nobr>AG-Nummer</nobr></td><td><input type="text" name="ag_nummer" size="50" maxlength="75" value="<?php echo $data['ag_nummer']?>"></td><td>Aufpassen: Jede AG-Nummer darf nur einmal existieren!</td></tr>
<?php } ?>

<tr><td pflicht="true"><nobr>Namen</nobr></td><td><input type="text" name="namen" size="50" maxlength="75" value="<?php echo $data['namen']?>"></td><td><?php echo $info?>Hier angeben, welche Personen die AG veranstalten</td></tr>
<tr><td pflicht="true"><nobr>Kontaktperson</nobr></td><td><input type="text" name="verantwortlicher_name" size="50" maxlength="75" value="<?php echo $data['verantwortlicher_name']?>"></td><td><?php echo $info?>Hier angeben, welche Person bei Fragen kontaktiert wird</td></tr>
<tr><td pflicht="true"><nobr>Telefon</nobr></td><td><input type="text" name="verantwortlicher_telefon" size="50" maxlength="25" value="<?php echo $data['verantwortlicher_telefon']?>"></td><td></td></tr>
<tr><td pflicht="true"><nobr>Email</nobr></td><td><input type="text" name="verantwortlicher_mail" size="50" value="<?php echo $data['verantwortlicher_mail']?>"></td><td></td></tr>
<tr><td pflicht="true"><nobr>Ich würde gerne diese AG anbieten</nobr></td><td><input type="text" name="ag_name" size="50" maxlength="75" value="<?php echo $data['ag_name']?>"></td><td></td></tr>
<tr><td pflicht="true"><nobr>Text für die Ausschreibung</nobr></td><td><textarea name="text_ausschreibung" rows="5" cols="50"><?php echo $data['text_ausschreibung']?></textarea></td><td></td></tr>
<tr><td pflicht="true"><nobr>Wichtige Infos für den Kurs</nobr><br>(Kommt ins Heft)</td><td><textarea name="wichtige_infos" rows="5" cols="50"><?php echo $data['wichtige_infos']?></textarea></td><td></td></tr>
<tr><td pflicht="true"><nobr>1. Termin</nobr></td><td><input type="text" dateField="true" name="termin" size="10" maxlength="10" value="<?php echo $data['termin']?>">&nbsp;<a href="<?php echo $dirPrefix?>neue_ag_termine.php" target="_blank" type="button">Welche Termine sind noch frei?</a></td><td  id="info_termin"></td></tr>
<tr><td><nobr>2. Termin (Bei Überbuchung)</nobr></td><td><input type="text" dateField="true" name="termin_ueberbuchung" size="10" maxlength="10" value="<?php echo $data['termin_ueberbuchung']?>"></td><td id="info_termin_ueberbuchung"></td></tr>
<tr><td>Ersatztermin<br>(Falls der 1. oder 2. Termin ausfällt,<br>wenn z.B. der Veranstalter krank wird)</td><td><input type="text" dateField="true" name="termin_ersatz" size="10" maxlength="10" value="<?php echo $data['termin_ersatz']?>"></td><td id="info_termin_ersatz"></td></tr>
<tr><td pflicht="true"><nobr>Uhrzeit von / bis</nobr></td><td><input type="text" name="termin_von" size="5" value="<?php echo $data['termin_von']?>"> / <input type="text" name="termin_bis" size="5" value="<?php echo $data['termin_bis']?>"></td><td><?php echo $info?>Format: 15:00</td></tr>
<tr><td pflicht="true">Für Klasse</td><td>
<div id="klasse">
<?php 

$klassenArray = array(1,2,3,4);
foreach ($klassenArray as $k) {
	$checked = "";
	if ($data['klasse'. $k] == "Ja") $checked = "checked='checked'";
	echo "<input buttonForKlasse='true' type='checkbox' id='radio$k' name='klasse$k' value='Ja' $checked><label for='radio$k'>$k</label>";
}

?>

</select>
</td><td><?php echo $info?> Klasse 1 schließt auch die Grundschulförderklasse mit ein</td></tr>
<tr><td pflicht="true"><nobr>Max. Anzahl Teilnehmer</nobr></td><td><input type="text" name="max_kinder" size="50" value="<?php echo $data['max_kinder']?>"></td><td><?php echo $info?>Anzahl oder 'unbegrenzt' falls die Anzahl keine Rolle spielt</td></tr>
<tr><td pflicht="true"><nobr>Teilnahmebetrag Mitglieder / NichtMitglied</nobr></td><td><input type="text" name="betrag_mitglied" size="5" value="<?php echo $data['betrag_mitglied']?>"> / <input type="text" name="betrag_nicht_mitglied" size="5" value="<?php echo $data['betrag_nicht_mitglied']?>"></td><td></td></tr>
<tr><td pflicht="true"><nobr>Ort der Veranstaltung</nobr></td><td><input type="text" name="ort" size="50" value="<?php echo $data['ort']?>"></td><td></td></tr>
<tr><td><nobr>Benötigte Anzahl an Helfern</nobr></td><td><input type="text" name="anzahl_helfer" size="5" value="<?php echo $data['anzahl_helfer']?>"></td><td><?php echo $info?>Nur eintragen, falls der Förderverein die AG-Helfer suchen soll</td></tr>
<tr><td><nobr>Das möchte ich euch noch mitteilen</nobr><br>(Kommt nicht ins Heft)</td><td><textarea name="ausserdem" rows="5" cols="50"><?php echo $data['ausserdem']?></textarea></td><td></td></tr>
<tr><td><nobr>Bild für das AG-Heft</nobr></td><td><input type="file" name="bild"></td><td><?php echo $info?>Bitte Copyright beachten.<br>Größe mind. 800*600, Max 1 MB<br><br>
Bibliotheken mit freien Bildern:<br> <?php echo formatAsLinks(array("http://de.freeimages.com/","https://pixabay.com/de/","https://www.pexels.com","Wiki" => "http://www.grundschule-aktiv.de/wiki/doku.php?id=fotos:index"),"<br>") ?>
</td></tr>
<tr><td>Aktuelles Bild:</td><td><?php echo getImageLink("true",$data, $dirPrefix,"max-width:200px")?></td><td><?php echo $data['bild_name']?></td></tr>
</tbody>
</table>

