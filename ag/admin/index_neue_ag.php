<?php
	$ags = NeueAgModel::getAgs(" order by id desc");
?>
<script type="text/javascript" src="../scripts/tagCloud/tagcloud.js"></script> 
<script type="text/javascript"> 
var tc = TagCloud.create();
var date = new Date();

<?php
	foreach ($ags as $t) {
?>  
	date = date.addDays(-10);
	tc.add('<?php echo $t["ag_name"]?>', <?php echo rand(17,20)?>, 'manageNeueAGs.php', date.getTime());
<?php } ?>
tc.loadEffector('CountSize').base(15).range(3);
tc.loadEffector('DateTimeColor');

</script>
<style type="text/css"> 
div#mytagcloud {
  
}
ul.tagcloud-list {
  font-size: 100%;
  font-weight: bold;
  font-family: 'Roboto',sans-serif;
  padding: 2px;
  margin: 10px;
}
li.tagcloud-base {
  font-size: 24px;
  display: inline;
}
a.tagcloud-anchor {
  text-decoration: none;
}
a.tagcloud-earliest {
  color: #7DA6F1;
}
a.tagcloud-earlier {
  color: #5B90F1;
}
a.tagcloud-later {
  color: #1359D9;
}
a.tagcloud-latest {
  color: #08368A;
}
</style>

<script>
var showCloud = false;
$(document).ready(function() {
	$("#neue_ag").DataTable({
		"paging":   false,
		"info": false,
        "ordering": false,
        "searching":   false,
        "bJQueryUI": true,
        "sDom": 'lfrtip'
		<?php echo "," . $dataTableLangSetting ?>
	});
	tc.setup('mytagcloud');
	$("#cloud, #table").click(toggle);
	toggle();
});

function toggle() {
	showCloud = !showCloud;
	if (showCloud) {
		$("#table, #mytagcloud").show();
		$("#cloud, #neue_ag").hide();
	} else {
		$("#table, #mytagcloud").hide();
		$("#cloud, #neue_ag").show();
	}
}


</script>
<h2 class="center">Neue AGs (<?php echo count($ags)?>)</h2>
<table id="neue_ag">
    <thead>
        <tr>
            <th><nobr>AG</nobr></th>
        	<th><nobr>Verantwortlich</nobr></th>
        </tr>
    </thead>
    <tbody>
		<?php
			foreach ($ags as $t) {
		?>    
        <tr>
        	<td>
        		<a href="manageNeueAGs.php"><?php echo $t["ag_name"]?></a>
        	</td>
        	<td><?php echo $t["verantwortlicher_name"]?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<div id="mytagcloud"></div> 
<a id="table" href="#"><img src="../images/table.png"></a>
<a id="cloud" href="#"><img src="../images/cloud.png"></a>