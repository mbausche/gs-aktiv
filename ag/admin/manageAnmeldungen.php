<?php
	include '../db.php';
	require_once("../funktionen.php");
	
	$tableId = "";
	$title =  "Anmeldungen verwalten";
	
	if ( !empty($_REQUEST["speichern"]) ) {
		AgModel::saveAnmeldung($_REQUEST["id"], $_REQUEST["name"],$_REQUEST["klasse"],$_REQUEST["mail"],$_REQUEST["telefon"], !empty($_REQUEST["mitglied"]), !empty($_REQUEST["fotos_ok"]) ,
			$_REQUEST["zahlart"],
			$_REQUEST["iban"],
			$_REQUEST["kontoinhaber"]
		);
		StatusModel::updateStatus($_REQUEST["name"], empty($_REQUEST["mitglied"]) ? 0 : 1);
	} 
	if ( !empty($_REQUEST["loeschen"]) ) {
		AgModel::loescheAnmeldung($_REQUEST["id"]);
	}
	
	include 'header.php';
?>

<script>
$(document).ready(function() {

	var table = $('#anmeldungsTable').DataTable( {
		fixedHeader: {
	        header: true,
	        footer: false
	    },
        "paging":   false,
        "info": true,
        "bJQueryUI": true,
        "sDom": 'lfrtip'
        <?php echo "," . $dataTableLangSetting?>
    } );	
	
	table
	.columns( '.tage' )
    .order( 'asc' )
    .draw();

	<?php renderLastSearchHandling("table","manageAnmeldungen.php_anmeldungsTable");?>
	setContentTableVisible();
	
	$("[name='iban']").keyup(function() {
		iban = $(this).val();
		var msg = checkIban(iban);
		if (msg != "") {
			$(this).prop("class","error");
			$(this).siblings("[type='iban_error']").show();
			$(this).siblings("[type='iban_error']").prop("title",msg);
		} else {
			$(this).prop("class","");
			$(this).siblings("[type='iban_error']").hide();
			$(this).siblings("[type='iban_error']").prop("title",msg);
		}
	});
	
	
	
});
</script>


<table id="anmeldungsTable" class="display">
    <thead>
        <tr>
        	<th>Nr</th>
        	<th>Name</th>
        	<th>Klasse</th>
            <th>Mail</th>
            <th>Telefon</th>
        	<th>Mitglied</th>
        	<th>Fotos OK</th>
        	<th>Betrag</th>
        	<th>Zahlart</th>
        	<th>IBAN</th>
        	<th>Kontoinhaber</th>
        	<th>Aktionen</th>
        </tr>
    </thead>
    <tbody>
		<?php 
			
			$anmeldungen = AgModel::getAnmeldungen(1);
			foreach ($anmeldungen as $anmeldung) {
				
		?>    
		
        <tr>
        	<td><form action="manageAnmeldungen.php" method="post"><?php echo formatAsAbfragenLink("../", $anmeldung['anmelde_nummer'])?></td>
        	<td><input type="text" name="name" value="<?php echo $anmeldung['name']?>"><span style="font-size: 0pt; visibility: hidden;"><?php echo $anmeldung['name']?></span></td>
        	<td><input type="text" name="klasse" size="3" value="<?php echo $anmeldung['klasse']?>"><span style="font-size: 0pt; visibility: hidden;"><?php echo $anmeldung['klasse']?></span></td>
        	<td><input type="text" name="mail"  size="30" value="<?php echo $anmeldung['mail']?>"><span style="font-size: 0pt; visibility: hidden;"><?php echo $anmeldung['mail']?></span></td>
        	<td><input type="text" name="telefon" value="<?php echo $anmeldung['telefon']?>"><span style="font-size: 0pt; visibility: hidden;"><?php echo $anmeldung['telefon']?></span></td>
        	<td><input type="checkbox" name="mitglied" <?php if ($anmeldung['ist_mitglied'] == 1) {echo "checked='checked'";}?>></td>
        	<td><input type="checkbox" name="fotos_ok" <?php if ($anmeldung['fotos_ok'] == 1) {echo "checked='checked'";}?>></td>
        	<td><?= formatAsCurrency($anmeldung["betrag"])?></td>
        	<td>
	        	<?php $text = $anmeldung['zahlart'] == 'bank' ? "Überweisung" : "Schule"; ?>
	        	
	        	<select name="zahlart">
	        		<option></option>
	        		<option <?php if ($anmeldung['zahlart'] == 'bank') {echo "selected";}?> value="bank">Überweisung</option>
	        		<option <?php if ($anmeldung['zahlart'] == 'schule') {echo "selected";}?> value="schule">Schule</option>
	        	</select>
	        	<span style='display:none'><?php echo $text?></span>
        	</td>
        	<td><nobr><input type="text" name="iban" value="<?php echo $anmeldung['iban']?>"><img style="display:none; vertical-align: sub; padding-left: 2px" type="iban_error" src="../images/warning.png"></nobr><span style="font-size: 0pt; visibility: hidden;"><?php echo $anmeldung['iban']?></span></td>
        	<td><input type="text" name="kontoinhaber" value="<?php echo $anmeldung['kontoinhaber']?>"><span style="font-size: 0pt; visibility: hidden;"><?php echo $anmeldung['kontoinhaber']?></span></td>
        	<td><input type="submit" name="speichern" value="Speichern"><input type="submit" name="loeschen" value="Löschen"><input type="hidden" name="id" value="<?php echo $anmeldung['id']?>"> </form></td>
        </tr>
       
        <?php } ?>
    </tbody>
</table>

<?php include 'footer.php';?>

