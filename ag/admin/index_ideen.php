<script>
$(document).ready(function() {

	$("#ideen").DataTable({
		"paging":   false,
		"info": false,
        "ordering": false,
        "searching":   false,
        "bJQueryUI": true,
        "sDom": 'lfrtip'
		<?php echo "," . $dataTableLangSetting ?>
	});

$("[idee_mailbutton='true']").click(function() {
	setIdeeOK($(this).attr("idee_id"));
});
	
$("[idee_okbutton='true']").click(function() {
	setIdeeOK($(this).attr("idee_id"));
});

function setIdeeOK(id) {
	ajaxCall("setIdeeOK.php?id=" + id, function() {
		$("[idee_row='" + id + "']").hide();
	});
}

});

</script>
<?php if (count($ideen) > 0) { ?>

<h2 class="center">Ideen</h2>
<table id="ideen" class="display">
    <thead>
        <tr>
            <th><nobr>Familie von</nobr></th>
        	<th><nobr>hat folgende Idee</nobr></th>
        	<th>Aktionen</th>
        </tr>
    </thead>
    <tbody>
		<?php
			foreach ($ideen as $t) {
		?>    
        <tr idee_row="<?php echo $t["id"]?>">
        	<td>
        		<?php echo $t["name"]?><?php echo $t["klasse"]?><br>
        		<?php echo formatAsAbfragenLink("../", $t["anmelde_nummer"]) ?><br>
        		<?php echo formatAsMailto($t["mail"])?><br>
        		<?php echo $t["telefon"]?>
        	</td>
        	<td><?php echo $t["idee_fuer_neue_ag"]?></td>
        	<td>
        		<a idee_id="<?php echo $t["id"]?>" idee_mailbutton="true" type="button" title="Idee per Mail weiterleiten" href="mailto:<?php echo CfgModel::load("ideensammler")?>?subject=Idee von Fam. <?php echo $t["name"]?>: <?php echo $t["idee_fuer_neue_ag"]?>">Mail</a>
        		<a idee_id="<?php echo $t["id"]?>" idee_okbutton="true" type="button" title="Idee wurde bereits weitergeleitet">OK</a>
        	</td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<?php } ?>

<?php include 'index_mithilfe.php';?>