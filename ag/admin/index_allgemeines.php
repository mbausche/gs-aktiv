<?php 

$statisticHref = "<a href=\"statistic.php\">Vorjahre</a>";

?>

<style>
table.pie-legend, td.pie-legend {
	border: 0px solid black;
	border-spacing:0px;
	padding: 0px;
	margin: 0px;
}

td.pie-legend {
	border-bottom: 1px solid #ddd;
	text-align: right;
}

td.pie-legend-left {
	border-bottom: 1px solid #ddd;
	text-align: left;
}

#myChart {
	text-align: center;
}


</style>

<script src="../scripts/chartjs/Chart.min.js"></script>
<script>
$(document).ready(function() {

	$("#allgemeines, #allgemeines1").DataTable({
        "paging":   false,
        "ordering": false,
        "searching": false,
        "info":     false,
        "bJQueryUI": true,
        "sDom": 'lfrtip'
        <?php echo "," . $dataTableLangSetting ?>
	});


	<?php 
		$anmeldungenUngeprueft = AgModel::countAnmeldungenFuerAGs("nicht_geprueft");
		$anmeldungenTermin1 = AgModel::countAnmeldungenFuerAGs("zusage");
		$anmeldungenTermin2 = AgModel::countAnmeldungenFuerAGs("termin2");
		$anmeldungenAbsage = AgModel::countAnmeldungenFuerAGs("absage");
		$anmeldungenErsatz = AgModel::countAnmeldungenFuerAGs("ersatztermin");
		
		$summe = $anmeldungenUngeprueft + $anmeldungenTermin1 + $anmeldungenTermin2 + $anmeldungenAbsage + $anmeldungenErsatz;
		
?>

	var data = [
	            {
	                value: <?php echo $anmeldungenTermin1?>,
	                color: "#59D959",
	                highlight: "#7FE17F",
	                label: new Array("Termin 1",<?php echo $summe == 0 ? 0 : round($anmeldungenTermin1 / $summe * 100,1)?>)
	            },				
	            {
	                value: <?php echo $anmeldungenTermin2?>,
	                color: "#FF9B0B",
	                highlight: "#FBBA5D",
	                label: new Array("Termin 2",<?php echo $summe == 0 ? 0 : round($anmeldungenTermin2 / $summe * 100,1)?>)
	            },				
	            {
	                value: <?php echo $anmeldungenErsatz?>,
	                color: "#AA64A9",
	                highlight: "#BD89BC",
	                label: new Array("Ersatztermin",<?php echo $summe == 0 ? 0 : round($anmeldungenErsatz / $summe * 100,1)?>)
	            },				
	            {
	                value: <?php echo $anmeldungenAbsage?>,
	                color:"#F7464A",
	                highlight: "#FF5A5E",
	                label: new Array("Absagen",<?php echo $summe == 0 ? 0 : round($anmeldungenAbsage / $summe * 100,1)?>)
	            },
	            {
	                value: <?php echo $anmeldungenUngeprueft?>,
	                color:"#C0C0C0",
	                highlight: "#808080",
	                label: new Array("Ungeprüft",<?php echo $summe == 0 ? 0 : round($anmeldungenUngeprueft / $summe * 100,1)?>)
	            }
	            
	          ];

	var ctx = $("#myChart").get(0).getContext("2d");
	var myPieChart = new Chart(ctx).Pie(data,{
		animateRotate: false,
		animateScale: false,
		segmentStrokeWidth : 1,
		legendTemplate : "<table class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><tr><td  class=\"<%=name.toLowerCase()%>-legend-left\" style=\"background-color:<%=segments[i].fillColor%>\"><%if(segments[i].label){%> <%=segments[i].label[0]%><%}%></td><td class=\"<%=name.toLowerCase()%>-legend\" style=\"background-color:<%=segments[i].fillColor%>\"><%=segments[i].value%></td><td class=\"<%=name.toLowerCase()%>-legend\" style=\"background-color:<%=segments[i].fillColor%>\"><%=segments[i].label[1]\%>%</td></tr><%segments[i].label = segments[i].label[0]; %> <%}%></table>"		

	});
	var legend =  myPieChart.generateLegend();
	$("#legend").html(legend);
	$(".pie-legend tr:last").after('<tr><td class="pie-legend-left">Summe</td><td class="pie-legend"><?php echo $summe?></td><td class="pie-legend">100%</td></tr>');

});
</script>

<h2 class="center">Allgemeines</h2>
<table id="allgemeines" class="display">
    <thead style="display: none;">
        <tr>
            <th>&nbsp;</th>
        	<th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
		<?php
		$schuelerUngeprueft = AgModel::countAnmeldungen(0);
		$schueler = AgModel::countAnmeldungen(1);
		$anmeldungen = AgModel::countAnmeldungenForAg();
		$ags = AgModel::countAGs();
		$anmeldungen = AgModel::countAnmeldungenForAg();
		
		$online = AgModel::countAnmeldungenOnline(true);
		$offline = AgModel::countAnmeldungenOnline(false);
		
		$schule = AgModel::countAnmeldungenZahlart("schule");
		$bank = AgModel::countAnmeldungenZahlart("bank");
		
		if ($schueler > 0)
			$durchschnitt = number_format ( $anmeldungen / $schueler , 2 , "," , ".");
		else 
			$durchschnitt = "";

		if ($ags > 0)
			$durchschnitt1 = number_format ( $anmeldungen / $ags , 2 , "," , ".");
		else
			$durchschnitt = "";
		
		?>    
        <tr><td>AGs</td><td><?php echo $ags?></td></tr>
        <tr><td>Angemeldete Schüler (Geprüft)</td><td><?php echo $schueler?></td></tr>
        <?php 
        if ($schuelerUngeprueft > 0) {
        	?><tr><td class="warning"><a class="warning" href="pruefeAnmeldungen.php">Angemeldete Schüler (Ungeprüft)</a></td><td class="waning"><a  class="warning" href="pruefeAnmeldungen.php"><?php echo $schuelerUngeprueft?></a></td></tr><?php 
        } else {
			?><tr><td class="ok">Angemeldete Schüler (Ungeprüft) </td><td class="ok"><?php echo $schuelerUngeprueft?></td></tr><?php
        }
        ?>
   		<?php 
        if ($anmeldungenUngeprueft > 0) {
        	$css = "warning";
        } else {
        	$css = "ok";
        }
        ?>     
        <tr><td>Online/Offline</td><td class="nobr"><?php echo $online ?> / <?php echo $offline ?></td></tr>
        <tr><td>Bez. per Schule/Überweisung</td><td class="nobr"><?php echo $schule ?> / <?php echo $bank ?></td></tr>
        <tr><td><a class="<?php echo $css?>" href="manageAGs.php">Anmeldungen</a><br><br><canvas id="myChart" width="200" height="200"></canvas></td><td>
		<span id="legend"></span>
        </td></tr>
        <tr><td>Durschnitt</td><td class="nobr"><?php echo $durchschnitt ?> AGs pro Schüler</td></tr>
        <tr><td>&nbsp;</td><td class="nobr"><?php echo $durchschnitt1 ?> Anmeldungen pro AG</td></tr>
        <tr><td><?php echo $statisticHref ?></td><td class="nobr">&nbsp;</td></tr>
   </tbody>
</table>

