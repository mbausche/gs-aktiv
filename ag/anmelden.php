<?php
	$title = "Eltern-AG Anmeldung";
	include_once 'header.php';
?>

<link rel="stylesheet" href="scripts/dataTables/css/fixedHeader.dataTables.min.css">
<script src="scripts/dataTables/js/dataTables.fixedHeader.min.js"></script>

<?php 
if ($_REQUEST['reenter'] == "true") {
	session_start();
} else {
	session_start();
	destroySession();
	session_start();
}

$order = "heft";
if (!empty($_SESSION["order"])) {
	$order = $_SESSION["order"];	
}
if (!empty($_REQUEST["order"])) {
	$order = $_REQUEST["order"];
}

error_log(print_r($_SESSION,true));

$ags = $order == "heft" ? AgModel::getAgsByAGNummer() : AgModel::getAgsByDatum();

if ($_REQUEST['direkt_eingabe'] == "1") {
	$_SESSION['direkt_eingabe'] = "1";
} else {
	$_SESSION['direkt_eingabe'] = "0";
}

if ($_REQUEST['geprueft'] == "1") {
	$_SESSION['geprueft'] = "1";
} else {
	$_SESSION['geprueft'] = "0";
}


?>

<script type="text/javascript">
var ags = new Array ();
var ags1 = new Array ();
var betrag = 0;
var checkedAgCount = 0;

/*
 * filterKlassen in Session
 * 0 = nachfragen
 * 1 = filtern
 * 2 = nicht filtern 
 */

var currentKlasse = "<?php if (!empty($_SESSION['klasseFuerFilter'])) { echo $_SESSION['klasseFuerFilter']; } else { echo "0"; } ?>";
var filterKlassen = 1;//<?php if (!empty($_SESSION['filterKlassen'])) { echo $_SESSION['filterKlassen']; } else { echo "0"; } ?>;

$( document ).ready(function() {
	<?php 
	
	foreach ($ags as $ag) {
		$betrag = number_format($ag["betrag_mitglied"],2,".","");
		$betrag1 = number_format($ag["betrag_nicht_mitglied"],2,".","");
		$nr = $ag['ag_nummer'];
		echo "ags['$nr'] = $betrag;\n";
		echo "ags1['$nr'] = $betrag1;\n";
	}
	?>

    $('#ags').DataTable( {
		fixedHeader: {
	        header: true,
	        footer: false
	    },
        "paging":   false,
        "ordering": false,
        "searching": false,
        "info":     false,
        "bJQueryUI": true,
        "sDom": 'lfrtip'
	    <?php echo "," .$dataTableLangSetting ?>
    } );

    $( "#klasse" ).buttonset();
    $( "#radiosMitglied" ).buttonset();
    $( "#ok" ).button();
    $( "[type='button']" ).button();
    
    
	<?php if ($_SESSION['geprueft'] == 1) { ?>
	$("[name='Kenntnis']").attr("checked","checked");
	<?php } ?>

	$( "#hintKlasse").hide();
	initEventHandling();	
	checkBeitraege();
	calcSum();

	fnFilterKlassen(currentKlasse);
	
});

function fnFilterKlassen(klasse) {
	if (klasse == "GFK" ||  klasse >= 1 && klasse <= 4) {
		$( "[type='ag_row']" ).each(function() {
			  var cb = $( this ).find("[type='checkbox']");
			
			  if ($( this ).attr("klasse" + klasse) == "1"
				 || cb.prop("checked") == true) {
				  $( this ).show("fast");
			  } else {
				  $( this ).hide("fast");
			  }
		});
		 
		$( "#hintKlasse").show("fast");
		$("#spanKlasse").html(klasse);
	} else {
		$( "[type='ag_row']" ).show("fast");
		$( "#hintKlasse").hide("fast");
	}
}



/*
function filterKlassen(klasse) {
	if (klasse >= 1 && klasse <= 4) {
		$( "[type='ag_row']" ).hide("fast");
		$( "[klasse" + klasse + "='1']" ).show("fast");
	}
}*/


function initEventHandling() {
	$( "[content='ag_checkbox']" ).click(function() {
		calcSum();
	});

	$("#ok").click(function() {
		submit();
	});

	<?php 
		$klasse = $_SESSION['klasse'];
		if (!empty($klasse)) {
			echo '$("#radio'. $klasse.'").attr("checked",true).button("refresh");';
	 	}
	 ?>
	
	$("#radio_mitglied").click(function() {
		checkBeitraege();
	});
	
	$("#radio_nicht_mitglied").click(function() {
		checkBeitraege();
	});

	$("[name='wirWollenMitgliedWerden']").click(function() {
		var isMitglied = $('input[name=mitglied]:checked').val() == 'ja';
		var neuesMitglied = $('input[name=wirWollenMitgliedWerden]').is( ":checked" );
		if (neuesMitglied && !isMitglied) {
			showAlert("Hinweis","Wenn Sie Mitglied werden wollen, können Sie weiter oben gleich 'Mitglied' auswählen.");
		}
	});

	$( "[buttonforklasse='true']" ).click(function() {
		var klasseValue = $(this).val();
		var klasse = 0;
		if (klasseValue.toLowerCase().indexOf("gfk") > -1) {
			klasse = "GFK";
		} else if (klasseValue.indexOf("1") > -1) {
			klasse = "1";
		} else if (klasseValue.indexOf("2") > -1) {
			klasse = "2";
		}
		else if (klasseValue.indexOf("3") > -1) {
			klasse = "3";
		}
		else if (klasseValue.indexOf("4") > -1) {
			klasse = "4";
		} 
		if (currentKlasse != klasse) {
			applyFilter(klasse);
		}
	});
}


function applyFilter(klasse) {
	storeSessionData("./","klasseFuerFilter",klasse);
    //storeSessionData("admin/","filterKlassen",1);
	fnFilterKlassen(klasse);
    currentKlasse = klasse;
    filterKlassen = 1;
}

function submit() {
	if (checkedAgCount == 0) {
		showAlert("Hinweis","Bitte zuerst die AGs ausgewählen!");
		return;
	}

	if ($("[name='name']").val() == "") {
		showAlert("Hinweis","Bitte noch einen Namen eingeben!");
		return;
	}

	var idKlasse = $("#klasse :radio:checked").attr("id");
	if (typeof idKlasse === "undefined") {
		showAlert("Hinweis","Bitte noch die Klasse eingeben!");
		return;
	} 
	if ($("[name='telefon']").val() == "" && $("[name='mail']").val() == "") {
		showAlert("Hinweis","Bitte noch eine Telefonnummer oder Mail-Adresse eingeben!");
		return;
	}

	if ($("[name='mitglied']").is(':checked') == false) {
		showAlert("Hinweis","Bitte geben Sie noch an ob sie Mitglied sind oder nicht!");
		return;
	}
	
	if ($("[name='Kenntnis']").is(':checked') == false) {
		showAlert("Hinweis","Bitte noch ankreuzen, dass Sie die 'Weiteren Hinweise' zur Kenntnis genommen haben ");
		return;
	}

	$("#mainForm").submit();
	
}

function checkBeitraege() {

	var isMitglied = $('input[name=mitglied]:checked').val() == 'ja';
	if (isMitglied) {
		$( "[type='betrag_nicht_mitglied']" ).hide();
		$( "[type='betrag_mitglied']" ).show();
	} else {
		$( "[type='betrag_mitglied']" ).hide();
		$( "[type='betrag_nicht_mitglied']" ).show();
	}
	
	calcSum();

	
}

function calcSum() {

	var isMitglied = $('input[name=mitglied]:checked').val() == 'ja';
	
	betrag = 0;
	checkedAgCount = 0;
	$( "[content='ag_checkbox']" ).each(function () {
        if (this.checked) {
        	checkedAgCount++;
            if (isMitglied)
            	betrag = betrag  + ags[$(this).attr("name")];
            else
            	betrag = betrag  + ags1[$(this).attr("name")];
        }
	});

	formatted = betrag.formatMoney(2, ',', '.'); 
	$("[content='betrag']").html(formatted);
	$("[name='summe']").val(formatted);
	
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
<form action="summary.php" method="get" id="mainForm">
<?php 

$anmeldungHint = CfgModel::load("hint.anmeldung");

if ($_SESSION['direkt_eingabe'] == "0" && $_REQUEST["silent"] != "true" && $_SESSION["silent"] != "true" && !empty(trim($anmeldungHint))) {
?>

<script>
$(document).ready( function () {
	$( "#dialog-attention" ).dialog({
	    modal: true,
	    position: { my: "top", at: "top+10%", of: window},
	    minWidth: 500,
	    buttons: {
	      Ok: function() {
	        $( this ).dialog( "close" );
	      }
	    }
	});
});
</script>

<div id="dialog-attention" title="<?php echo CfgModel::load("hint.anmeldung.title")?>">
<?php echo $anmeldungHint?>
</div>

<?php } ?>
<table>
<tr><td><nobr>Vorname</nobr></td><td><input type="text" name="vorname" size="50" value="<?php echo $_SESSION['vorname']?>"></td></tr>
<tr><td><nobr>Nachname</nobr></td><td><input type="text" name="nachname" size="50" value="<?php echo $_SESSION['nachname']?>"></td></tr>
<tr><td>Klasse</td><td>
<div id="klasse">
<?php 

$klassenArray = explode(",", CfgModel::load("klassen"));
foreach ($klassenArray as $k) {
	echo "<input buttonForKlasse='true' type='radio' id='radio$k' name='klasse' value='$k'><label for='radio$k'>$k</label>";
}

?>

</select>


</td></tr>
<tr><td>Telefon</td><td><input type="text" name="telefon" size="50" value="<?php echo $_SESSION['telefon']?>"></td></tr>
<tr><td>E-Mail</td><td><input type="text" name="mail" size="50" value="<?php echo $_SESSION['mail']?>"></td></tr>

<?php 

$mitgliedChecked = "";
$nichtMitgliedChecked = "";

if ($_SESSION['mitglied'] == 'ja') {
	$mitgliedChecked = " checked='checked'";
} else {
	$nichtMitgliedChecked = " checked='checked'";
}
?>

<tr><td colspan="2">
<div id="radiosMitglied">
<input type="radio" name="mitglied" value="ja" id="radio_mitglied" <?php echo $mitgliedChecked?>><label for="radio_mitglied">Mitglied</label>
<input type="radio" name="mitglied" value="nein"  id="radio_nicht_mitglied" <?php echo $nichtMitgliedChecked?>><label for="radio_nicht_mitglied">Nicht-Mitglied</label>
</div>

</td></tr>
</table>

<h2><nobr>Mein Kind möchte die folgenden Kurse besuchen (Bitte entsprechend ankreuzen)
<?php if ($order == "heft") { ?>
<a type="button" href="<?php echo $_SERVER['PHP_SELF']?>?order=datum&silent=true">Nach Datum sortieren</a>
<?php } else { ?>
<a type="button" href="<?php echo $_SERVER['PHP_SELF']?>?order=heft&silent=true">Sortieren wie im AG-Heft</a>
<?php } ?>
</nobr></h2>
<div id="hintKlasse"><b>Hinweis:</b> Derzeit werden nur die AGs für Klasse <span id="spanKlasse"></span> und alle selektierten AGs angezeigt!<br><br></div>
<table id="ags" class="display">
<thead>
<tr>
<th>&nbsp;</th>
<th>Ag-Nr.</th>
<th>Name</th>
<th>Klassen</th>
<th>Termin</th>
<th>Uhrzeit</th>
<th>Zusatztermin</th>
<th>Ersatztermin</th>
<th>Ort</th>
<th><span type='betrag_mitglied'><nobr>Kosten</nobr></span><span type='betrag_nicht_mitglied'><nobr>Kosten</nobr></span></th>
</tr>
</thead>
<tbody>
<?php 
foreach ($ags as $ag) {

	$tage =  AgModel::getTage($ag['ag_nummer']);

	if ($tage > 3 ) {

		$checked = "";
		if ($_SESSION[$ag['ag_nummer']] == "binDabei") {
			$checked = "checked='checked'";
		}
		$betragMitglied = formatAsCurrency($ag["betrag_mitglied"]);
		$betragNichtMitglied = formatAsCurrency($ag["betrag_nicht_mitglied"]);
		
		echo "<tr type='ag_row' klassegfk=".$ag['klasse1']." klasse1=".$ag['klasse1']."  klasse2=".$ag['klasse2']."  klasse3=".$ag['klasse3']."  klasse4=".$ag['klasse4']." >";
		echo "<td><input type='checkbox' content='ag_checkbox' name='" . $ag['ag_nummer'] . "' value='binDabei' $checked></td>";
		echo "<td>".$ag["ag_nummer"]."</td>";
		echo "<td>".$ag["name"]."</td>";
		echo "<td>".AgModel::getStringForKlassen($ag)."&nbsp;</td>";
		echo "<td style='text-align:right'>".AgModel::getTerminForStatus($ag, "zusage",false)."</td>";
		echo "<td style='text-align:right'>".AgModel::getUhrzeitForStatus($ag, "zusage")."</td>";
		echo "<td style='text-align:right'>".AgModel::getTerminForStatus($ag, "termin2",false)."</td>";
		echo "<td style='text-align:right'>".AgModel::getTerminForStatus($ag, "ersatztermin",false)."</td>";
		echo "<td>".$ag["ort"]."</td>";
		echo "<td align='right'><span type='betrag_mitglied'>". $betragMitglied . "</span><span type='betrag_nicht_mitglied'>" . $betragNichtMitglied . "</span></td>";
		echo "</tr>";
	}
}

?>

<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td align="right"><nobr><b>Summe: <span content="betrag">0</span> €</b></nobr></td></tr>
</tbody>
</table>
<input type="hidden" name="summe" value="0">
<input type="hidden" name="zahlart" value="bank">
<input type="hidden" name="bootstrap" value="false">
<h2>Sonstiges:</h2>
<table>
<tr><td valign="top"><input type="checkbox" name="wirWollenMitgliedWerden" value="Ja" <?php if ($_SESSION['wirWollenMitgliedWerden'] == "Ja") echo "checked='checked'";?> ></td><td><?php echo TEXT_WILL_MITGLIED_WERDEN?></td></tr>
<tr><td valign="top"><input type="checkbox" name="keineBilder" value="Ja"  <?php if ($_SESSION['keineBilder'] == "Ja") echo "checked='checked'";?>></td><td><?php echo TEXT_KEINE_FOTOS?></td></tr>
<tr><td valign="top"><input type="checkbox" name="sendConfirmation" value="Ja"  <?php if ($_SESSION['sendConfirmation'] == "Ja") echo "checked='checked'";?>></td><td><?php echo TEXT_SEND_CONFIRMATION?></td></tr>
<tr><td colspan="2"><br><?php echo TEXT_MITHILFE?><br><input type="text" name="mithilfeBeiAktuellerAG" size="70" value="<?php echo $_SESSION['mithilfeBeiAktuellerAG']?>"><br><br></td></tr>
<tr><td colspan="2"><?php echo TEXT_IDEE?><br><input type="text" name="ideeFuerNeueAG" size="70" value="<?php echo $_SESSION['ideeFuerNeueAG']?>"></td></tr>
</table>
<h2>Weitere Hinweise</h2>
<table>
<tr><td colspan="2">Ich lege die Teilnahmegebühr/en in Höhe von <b><span content="betrag">0</span> €</b> meiner Anmeldung bei.</td></tr>
<tr><td colspan="2"><?php echo TEXT_NACH_HAUSE_WEG?></td></tr>
<tr><td colspan="2"><?php echo TEXT_1_EURO?></td></tr>
<tr><td valign="top" colspan="2"><input type="checkbox" name="Kenntnis" <?php if ($_SESSION['Kenntnis'] == "on") echo "checked='checked'";?>>&nbsp;Ich habe die Weiteren Hinweise zur Kenntnis genommen!</td></tr>
</table>
<br>

<input type="button" id="ok" name="ok" value="Weiter">

</form>
</body>
</html>