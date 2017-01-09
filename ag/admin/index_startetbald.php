<?php 
$startetBald = AgModel::getStartetBald();
?>

<script>
$(document).ready(function() {

	var table = $("#start").DataTable({
		"paging":   false,
		"info": false,
        "ordering": false,
        "searching":   false,
        "bJQueryUI": true,
        "sDom": 'lfrtip'
		<?php echo "," . $dataTableLangSetting ?>
	});
});
</script>

<h2 class="center">Startet Bald</h2>
<table id="start" class="display">
    <thead>
        <tr>
            <th class="name">AG</th>
        	<th class="nobr anzahl">Startet in ...Tagen</th>
        </tr>
    </thead>
    <tbody>
		<?php
			foreach ($startetBald as $t) {
		?>    
        <tr>
        	<td><?php echo "<nobr>" . formatAsManageAGLink("./", trim_text($t["name"],40), $t["ag_nummer"])."</nobr>"?></td>
        	<td><?php echo $t["tage"]?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>

