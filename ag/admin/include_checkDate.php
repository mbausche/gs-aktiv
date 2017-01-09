function checkDate(date) {
	var s = date.getDate() + "." + (date.getMonth()+1) + "." + date.getFullYear();

	var result = "";
	var style = "";
	<?php 
	foreach ($termine as $termin) {	?>
	
		if (s == "<?php echo formatSQLDateForJavascript($termin["termin"]) ?>" ) {
			<?php if ($termin["typ"] == "1. Termin") 
					echo "style = 'cal_error';\n";
				else
					echo "if (style != 'cal_error') style = 'cal_error_termin2';\n"; 
			?>
			if (result.length > 0) {
				result = result + "\n";
			}
			result = result + "<?php echo $termin["ag_name"] ?> (<?php echo $termin["typ"] ?>, <?php echo $termin["von"] ?> - <?php echo $termin["bis"] ?> Uhr)";
		}
		
	<?php } ?> 
	
	if (result.length > 0) {
		result = "Hier finden folgende AGs statt:\n\n" + result;
		return [true,style,result];
	}
	
	<?php 
	$ferienArray = explode(";", $ferienString);
	foreach ($ferienArray as $paarString) {
		$paar = explode("-", $paarString);
		$von = changeDayAndMonth($paar[0]);
		$bis = changeDayAndMonth($paar[1]);
		?>
		var von = new Date("<?php echo $von?>");
		var bis = new Date("<?php echo $bis?>");

		if (date.getTime() >= von.getTime() && date.getTime() <= bis.getTime()) {
			return [true,"cal_holiday","Der Termin liegt in den Ferien"];
		} 
		
		<?php 
		
	}
	
	?>
	
	return [true,"",""];
}
