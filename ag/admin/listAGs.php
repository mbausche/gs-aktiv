<?php
	include_once '../db.php';
	require_once("../funktionen.php");
	
	$tableId = "";
	if (empty($title))
		$title =  "AG-Liste";	
	
	$ags = AgModel::getAgs();
	
	include 'header.php';
?>

<link rel="stylesheet" href="../scripts/dataTables/css/fixedHeader.dataTables.min.css">
<script src="../scripts/dataTables/js/dataTables.fixedHeader.min.js"></script>



<script>
$(document).ready(function() {

	var table = $('#ags').DataTable( {
		<?php echo $dataTableLangSetting . "," ?>
		fixedHeader: {
	        header: true,
	        footer: false
	    }
	});

	table
// 	.columns( '.name' )
//  .order( 'asc' )
	.page.len( -1 )
	.draw();

	setContentTableVisible();
	
	
});
</script>

<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">

<table id="ags" class="display">
    <thead>
        <tr>
            <th>ID</th>
        	<th>Nummer</th>
        	<th>Name</th>
            <th>Name</th>
        	<th>Mail</th>
        	<th>Telefon</th>
            <th>Termin</th>
        	<th>Bei Überbuchung</th>
        	<th>Ersatztermin</th>
        	<th>max. Kinder</th>
        	<th>Klasse 1</th>
        	<th>Klasse 2</th>
        	<th>Klasse 3</th>
        	<th>Klasse 4</th>
        	<th>Betrag Mitglied</th>
        	<th>Betrag Nicht-Mitglied</th>
        	<th>Ort</th>
        </tr>
    </thead>
    <tbody>
		<?php
			foreach ($ags as $ag) {
		?>    
		
        <tr>
            <td><?php echo $ag['id']?></td>
            <td><?php echo $ag['ag_nummer']?></td>
            <td><?php echo $ag['name']?></td>
            <td><?php echo $ag['verantwortlicher_name']?></td>
            <td><?php echo $ag['verantwortlicher_mail']?></td>
            <td><?php echo $ag['verantwortlicher_telefon']?></td>
            <td><?php echo AgModel::getTerminForStatus($ag,"zusage")?></td>
            <td><?php echo AgModel::getTerminForStatus($ag,"termin2")?></td>
            <td><?php echo AgModel::getTerminForStatus($ag,"ersatztermin")?></td>
            <td><?php echo $ag['max_kinder']?></td>
            <td><?php echo formatAsYesNo($ag['klasse1']) ?></td>
            <td><?php echo formatAsYesNo($ag['klasse2']) ?></td>
            <td><?php echo formatAsYesNo($ag['klasse3']) ?></td>
            <td><?php echo formatAsYesNo($ag['klasse4']) ?></td>
            <td><?php echo formatAsCurrency($ag['betrag_mitglied']) ?></td>
            <td><?php echo formatAsCurrency($ag['betrag_nicht_mitglied']) ?></td>
            <td><?php echo $ag['ort'] ?></td>
       </tr>
		
		
        <?php } ?>
    </tbody>
</table>
<br>
<input type="submit" name="save" value="Speichern"></input><br><br>
</form>
<br>


<?php include 'footer.php';?>



