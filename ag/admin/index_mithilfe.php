<script>
$(document).ready(function() {

	$("#mithilfe").DataTable({
		"paging":   false,
		"info": false,
        "ordering": false,
        "searching":   false,
        "bJQueryUI": true,
        "sDom": 'lfrtip'
		<?php echo "," . $dataTableLangSetting ?>
	});

$("[mithilfe_mailbutton='true']").click(function() {
	setIdeeOK($(this).attr("mithilfe_id"));
});
	
$("[mithilfe_okbutton='true']").click(function() {
	setIdeeOK($(this).attr("mithilfe_id"));
});

function setIdeeOK(id) {
	ajaxCall("setMithilfeOK.php?id=" + id, function() {
		$("[mithilfe_row='" + id + "']").hide();
	});
}

});

</script>
<?php if (count($mithilfe) > 0) { ?>
<h2 class="center">Mithilfe</h2>
<table id="mithilfe" class="display">
    <thead>
        <tr>
            <th><nobr>Familie von</nobr></th>
        	<th>hilft bei AG</th>
        	<th>Aktionen</th>
        </tr>
    </thead>
    <tbody>
		<?php
			foreach ($mithilfe as $t) {
		?>    
        <tr mithilfe_row="<?php echo $t["id"]?>">
        	<td>
        		<?php echo $t["name"]?><?php echo $t["klasse"]?><br>
        		<?php echo formatAsAbfragenLink("../", $t["anmelde_nummer"]) ?><br>
        		<?php echo formatAsMailto($t["mail"])?><br>
        		<?php echo $t["telefon"]?>
        	</td>
        	<td><?php echo $t["mithilfe_bei_aktueller_ag"]?></td>
        	<td>
        		<a mithilfe_id="<?php echo $t["id"]?>" mithilfe_mailbutton="true" type="button" title="Unterstützung per Mail weiterleiten" href="mailto:?subject=Mithilfe von Fam. <?php echo $t["name"]?> bei der AG: <?php echo $t["mithilfe_bei_aktueller_ag"]?>">Mail</a>
        		<a mithilfe_id="<?php echo $t["id"]?>" mithilfe_okbutton="true" type="button" title="Unterstützung wurde bereits weitergeleitet">OK</a>
        	</td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<?php } ?>
