<?php
	session_start();
	
	require_once("funktionen.php");
	require_once("db.php");
	
	$data = array();
	
	if (!empty($_REQUEST["edit_token"])) {
		$currentId = NeueAgModel::getIdByEditToken($_REQUEST["edit_token"]);
		$data = NeueAgModel::loadAGForEdit($currentId);
		$title = "Ändern der AG " . $data["ag_name"];
	} else {
		$title = "Veranstalten einer Eltern-AG";
	}
	include 'header.php';
	
	error_log("SESSION: " . print_r($_SESSION,true));
	
	if ($_REQUEST["keepValues"] == "true") {
		$data = copySessionValuesToArray();
		if (!empty($_SESSION['ag_name'])) {?> 
			<a type="button" href="neue_ag_anmelden.php">Leeres Formular</a><br><br> 
		<?php 
		} 
	} else if (!empty($_SESSION['ag_name'])) {
?>
	<a type="button" href="neue_ag_anmelden.php?keepValues=true">Formular mit schon mal eingegebenen Werten füllen</a><br><br>
<?php 
	}	

	if (!empty($_REQUEST["edit_token"])) {
		$editForm = "neue_ag_anmelden.php?edit_token=".$_REQUEST["edit_token"]."&response=" . $_REQUEST["response"];
		$redirectAfterSave = "neue_ag_geandert.php?edit_token=".$_REQUEST["edit_token"];
		$forceUpdate="true";
		if ($_REQUEST["response"] == "yes")
			$saveButtonText="Okay jetzt passts!";
		else 
			$saveButtonText="Aktualisieren";
		$editToken= $_REQUEST["edit_token"];
		$data["error"] = $_SESSION["error"];
		$data["captcha"] = "true";
	}
	
	include 'include_neu_ag_stammdaten_form.php'; 
?>

<?php if (empty($_REQUEST["edit_token"])) { ?>
<h3>Folgendes habe ich zur Kenntnis genommen:</h3>
<table id="weiteres"  class="display" style="margin:0">
<thead style="display: none;">
	<th>Feld</th>
    <th>Kommentar</th>
</thead>
<tbody>
<tr><td pflicht="true"><input type="checkbox" sonstigeCheckbox="true" name="check1" value="Ja" <?php if ($data['check1'] == "Ja") echo "checked='checked'";?> ></td><td>Meine Materialrechnungen rechne ich direkt mit <?php echo CfgModel::load("name.kassierer") ?> (Kassierein) ab. (Tel. <?php echo CfgModel::load("tel.kassierer") ?>, Mail: <?php echo formatAsMailto(CfgModel::load("mail.kassierer")) ?>)</td></tr>
<tr><td pflicht="true"><input type="checkbox" sonstigeCheckbox="true" name="check2" value="Ja" <?php if ($data['check2'] == "Ja") echo "checked='checked'";?> ></td><td>Eine TeilnehmerInnenliste bekomme ich von <?php echo CfgModel::load("kontakt.verwaltung") ?></td></tr>
<tr><td pflicht="true"><input type="checkbox" sonstigeCheckbox="true" name="check3" value="Ja" <?php if ($data['check3'] == "Ja") echo "checked='checked'";?> ></td><td>Den Schulschlüssel fordere ich zwei Tage vor der AG bei <?php echo CfgModel::load("name.schluessel") ?> (Tel. <?php echo CfgModel::load("tel.schluessel") ?>, <?php echo formatAsMailto(CfgModel::load("mail.schluessel")) ?>) an</td></tr>
<tr><td pflicht="true"><input type="checkbox" sonstigeCheckbox="true" name="check4" value="Ja" <?php if ($data['check4'] == "Ja") echo "checked='checked'";?> ></td><td>Der Verein hat für alle Mitglieder und HelferInnen eine Haftpflicht- und Unfallversicherung abgeschlossen. Somit sind auch die VeranstalterInnen der Eltern-AGs abgesichert.</td></tr>
<tr><td pflicht="true"><input type="checkbox" sonstigeCheckbox="true" name="check5" value="Ja" <?php if ($data['check5'] == "Ja") echo "checked='checked'";?> ></td><td>Sollten eigene Kinder an dem Kurs teilnehmen, sind diese von der Kursgebühr befreit.</td></tr>
</tbody>
</table>
<?php } ?>
<?php if ($data["captcha"] != "true") { ?>
<script src="scripts/captcha/puzzleCAPTCHA.js"></script>
<link rel="stylesheet" href="scripts/captcha/puzzleCAPTCHA.css">
<script>
	$(document).ready(function() {
	    $("#PuzzleCaptcha").PuzzleCAPTCHA({
	        imageURL:'images/captcha.jpg',
	        targetInput: ".captcha-hidden",
	        targetVal: "true",
	        onSuccess: function() {
				$("#outerCaptcha").hide("slow");
	        },
	        width:300,
	        height:200
	    });
	});
</script>
<div id="outerCaptcha">
<h3>Bitte hier noch das richtige Puzzleteil auswählen <img src="images/help.png" title="Hinweis: Das ist eine reine Vorsichtsmaßnahme, weil unser Formular ab und zu von Hackern besucht wird"></h3>

<div id="PuzzleCaptcha"></div>
</div>
<?php } ?>
<div id="hinweisPflichtfelder"/></div>

<br>
<div id="submitButton"/></div>
<input type="hidden" class="captcha-hidden" name="captcha" value="<?php echo $data["captcha"]?>" />

</form>
</div>
<?php 
include_once 'admin/footer.php';
?>