<?php
	include '../db.php';
	require_once("../funktionen.php");
	
	$tableId = "";
	$title =  "Eltern-AGs verwalten";
	
	include 'header.php';
	
	function insertCountLabel($countText, $showIcon, $iconClass, $tooltip ="") {
		if ($showIcon) {
			$tooltip = implode("\n", $tooltip);
			echo "<img class='$iconClass' src='../images/trans.png' title='$tooltip'>";
		}
		echo "<span class='counText'>$countText</span>";
	}
	
	function insertHtml($html, $showHtml, $iconClass, $tooltip ="") {
		if ($showHtml) {
			$tooltip = implode("\n", $tooltip);
			echo "<img class='$iconClass' src='../images/trans.png' title='$tooltip'>";
		}
		echo $html;
	}
	
?>

<script>

$(document).ready(function() {

	var table = $('#agTable').DataTable( {
		fixedHeader: {
	        header: true,
	        footer: false
	    },
        "paging":   false,
        "info": false,
        "bJQueryUI": true,
        "sDom": 'lfrtip'
        <?php echo "," . $dataTableLangSetting?>
    } );	

	table
	.columns( '.tage' )
    .order( 'asc' )
    .draw();

	<?php renderLastSearchHandling("table","manageAGs.php_agTable");?>

    $("tr[type='hideByDefault']").hide();    

	setContentTableVisible();
    
    
    $("#checkShowAll").click(function( event ) {
        if (this.checked) {
        	$("tr[type='hideByDefault']").show("slow");
        } else {
	    	$("tr[type='hideByDefault']").hide("slow");
        }
    });

	
});
</script>


<table id="agTable" class="display">
    <thead>
        <tr>
            <th>Nr</th>
        	<th>Name</th>
        	<th>Termin</th>
        	<th class="tage">Verbleibende Tage</th>
        	<th>Anmeldungen Gesamt</th>
        	<th>Anmeldungen Noch nicht bestätigt</th>
            <th>Anmeldungen Termin 1</th>
            <th>Anmeldungen Termin 2</th>
            <th>Ersatztermin</th>
            <th>Absagen</th>
            <th>Max. Pro Termin</th>
            <th>Off. Nachrichten an Eltern</th>
            <th>Veranst.-Info <img style="vertical-align: bottom;" class='sprite info' src='../images/trans.png' title='Wurde bereits eine Info-Mail an der Veranstalter verschickt?'></th>
        </tr>
    </thead>
    <tbody>
		<?php 
			
			$ags = AgModel::getAgs();
			
		
			foreach ($ags as $ag) {
			$countAll = AgModel::countAnmeldungenForAg($ag['ag_nummer']);
			$countNichtGeprueft = AgModel::countAnmeldungenForAg($ag['ag_nummer'], "nicht_geprueft");
			$countTermin1 = AgModel::countAnmeldungenForAg($ag['ag_nummer'], "zusage");
			$countTermin2 = AgModel::countAnmeldungenForAg($ag['ag_nummer'], "termin2");
			$countErsatztermin = AgModel::countAnmeldungenForAg($ag['ag_nummer'], "ersatztermin");
			$countAbsagen = AgModel::countAnmeldungenForAg($ag['ag_nummer'], "absage");
				
			$countOffen = AgModel::countAnmeldungenForAg($ag['ag_nummer'], "zusage", true,true)
				+ AgModel::countAnmeldungenForAg($ag['ag_nummer'], "termin2", true)
				+ AgModel::countAnmeldungenForAg($ag['ag_nummer'], "ersatztermin", true)
				+ AgModel::countAnmeldungenForAg($ag['ag_nummer'], "absage", true);
				
			
			$agInDB = AgModel::getAg($ag['ag_nummer']);
			$max = $agInDB['max_kinder'];
			$termin = formatSQLDate($agInDB['termin']);
			$d1 = new DateTime("now");
			$d2 = new DateTime("now");
			$d2->setTimestamp(strtotime($agInDB['termin']));
			$tage =  AgModel::getTage($ag['ag_nummer']);
			$tageTermin2 =  empty($agInDB['termin_ueberbuchung']) ? -1 : AgModel::getTage($ag['ag_nummer'],'termin_ueberbuchung');
			$tageTerminErsatz =  empty($agInDB['termin_ersatz']) ? -1 : AgModel::getTage($ag['ag_nummer'],'termin_ersatz');
			
			$infoMail = strpos($agInDB['kommentar_privat'],'Infomail an Veranstalter verschickt') === false ? false : true;
			
			$agNr = $agInDB['ag_nummer'];
			$toolTip = array();
			$showError1 = $showError2 =  $showError3 = $showWarning1 =$showWarning2 = $showWarning3 = false ;
				
			if (isset($max) && $max > 0  && ($countTermin1 > $max || $countTermin2 > $max || $countErsatztermin  > $max)) {
			   $css = "error";
			   if ($countTermin1 > $max) {
			   		array_push($toolTip, "Zu Viele Kinder im 1. Termin");
			   		$showError1 = true;
			   }
			   	
		   	   if ($countTermin2 > $max) {
					array_push($toolTip, "Zu Viele Kinder im 2. Termin");
					$showError2 = true;
			   }
		   			
		   	   if ($countErsatztermin > $max) {
					array_push($toolTip, "Zu Viele Kinder im Ersatztermin");
					$showError3 = true;
			   }
		   	   		
			} else if ($countNichtGeprueft > 0 || $countOffen > 0 || !$infoMail) {
               $css = "warning";
		   	   if ($countNichtGeprueft > 0) {
					$showWarning1 = true;
					array_push($toolTip, "Es gibt noch $countNichtGeprueft nicht bestätigte Anmeldungen");
			   }
		   			
		   	   if ($countOffen > 0){
					$showWarning2 = true;
		   	   		array_push($toolTip, "Es sollten noch $countOffen Eltern benachrichtigt werden.");
			   }
		   	   if (!$infoMail) {
					$showWarning3 = true;
		   	   		array_push($toolTip, "Der Veranstalter wurde noch nicht benachrichtigt.");
			   }
		   	    
			} else {
				$css = "ok";
			}	

			$rowType = "showByDefault";

			if ($tage < 0 && $tageTermin2 < 0 && $tageTerminErsatz < 0) {
				if ($css == "ok") {
					$rowType = "hideByDefault";
				}
			} 		

				
		?>    
        <tr type="<?php echo $rowType?>">
        	<td class="<?php echo $css?>"><?php echo $ag['ag_nummer']?></td>
        	<td class="<?php echo $css?>"><a type='button' class="<?php echo $css?>" style="text-align:left; min-width: 200px; max-width: 200px" href="manageAG.php?ag=<?php echo $agNr?>"><?php echo $ag->name?></a></td>
        	<td class="<?php echo $css?>"><?php echo $termin?></td>
        	<td class="<?php echo $css?>"><?php echo $tage?></td>
        	<td class="right <?php echo $css?>"><?php echo $countAll?></td>
        	<td class="right <?php echo $css?>"><?php insertCountLabel($countNichtGeprueft,$showWarning1,"sprite warning",$toolTip) ?></td>
            <td class="right <?php echo $css?>"><?php insertCountLabel($countTermin1,$showError1,"sprite error",$toolTip)?></td>
            <td class="right <?php echo $css?>"><?php insertCountLabel($countTermin2,$showError2,"sprite error",$toolTip)?></td>
            <td class="right <?php echo $css?>"><?php insertCountLabel($countErsatztermin,$showError3,"sprite error",$toolTip)?></td>
            <td class="right <?php echo $css?>"><?php echo $countAbsagen?></td>
            <td class="right <?php echo $css?>"><?php echo isset($max) ? $max : "-"?></td>
            <td class="right <?php echo $css?>"><?php insertCountLabel($countOffen,$showWarning2,"sprite warning",$toolTip)?></td>
            <td class="center <?php echo $css?>"><?php insertHtml($infoMail ? "<img class='sprite check' src='../images/trans.png' title='".$agInDB['kommentar_privat']."' >" : "-",$showWarning3,"sprite warning",$toolTip)?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<br><br><br>
<div id="footer">
<input type="checkbox" id="checkShowAll">Alle anzeigen (Auch die AGs, die bereits laufen und keine Warnhinweise haben)<br><br>
</div>

<?php include 'footer.php';?>

