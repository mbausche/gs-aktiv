<?php 

$top10 = AgModel::getTop10();
?>

<script>
$(document).ready(function() {

	var table = $("#top10").DataTable({
		"paging":   false,
		"info": false,
        "ordering": false,
        "searching":   false,
        "bJQueryUI": true,
        "sDom": 'lfrtip'
		<?php echo "," . $dataTableLangSetting ?>
	});

	table
	.columns( '.anzahl' )
    .order( 'desc' )
    .draw();
	
});
</script>

<h2 class="center">Top 10</h2>
<table id="top10" class="display">
    <thead>
        <tr>
            <th class="nobr name">AG</th>
        	<th class="nobr anzahl">Anmeldungen</th>
        </tr>
    </thead>
    <tbody>
		<?php
			foreach ($top10 as $t) {
		?>    
        <tr>
        	<td><?php echo "<nobr>".formatAsManageAGLink("./",  trim_text($t["ag_name"],40), $t["ag_nummer"]). "</nobr>"?></td>
        	<td><?php echo $t["anzahl"]?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>

