<?php

	session_start();

	include '../db.php';
	require_once("../funktionen.php");

	$tableId = "";
	$title = "Editieren einer Eltern-AG";

	include "header.php";
	
	$dirPrefix = "../";
	$redirectAfterSave = "admin/manageNeueAGs.php";
	$editForm = "admin/editNeueAG.php?edit_token=".$_REQUEST["edit_token"];
	$forceUpdate="true";
	$includeAGNummer=true;
	$saveButtonText="Speichern";
	$editToken= $_REQUEST["edit_token"];
	$currentId = NeueAgModel::getIdByEditToken($_REQUEST["edit_token"]);

	$data = NeueAgModel::loadAGForEdit($currentId);
	$data["error"] = $_SESSION["error"];
	
	unset($_SESSION["error"]);
	
	
	include '../include_neu_ag_stammdaten_form.php'; 
?>

<script>
$(document).ready(function() {
	setContentTableVisible();
	$( "a[type=button]" )
    .button()
    	.click(function( event ) {
    });
})	;
</script>


<div id="hinweisPflichtfelder"/></div>
<br>
<div id="submitButton"/></div>
<input type="hidden" class="captcha-hidden" name="captcha" value="true" />
</form>
</div>
<?php 
include_once 'footer.php';
?>