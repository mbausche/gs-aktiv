<?php
	include '../db.php';
	include '../conf.php';
	require_once("../funktionen.php");
	require_once('../ext/html2pdf/html2pdf.class.php');
		
	$ags = AgModel::getAnmeldungenForSchulleitung();
	$preview = $_REQUEST['preview'] == 'true';
	
	ob_start();
	
	header('Content-type: application/pdf; charset=utf-8');
	
	?>
	
	
	<style type="text/css">
	<!--

	h2
	{
		margin-top: 15mm;
	}	
	
	.error
	{
	color: red;
	}
	
	
	-->
	</style>	
	
	<?php 
	
	echo "<page>";
	if ($preview) {
		echo "<h1>PREVIEW</h1>";
	}
	echo "<h1>Übersicht Eltern-AGs</h1>";
	echo "<h1>$halbjahr</h1>";
	
	?>
	
			<table>
			
			<thead>
			<tr>
				<td style="width:15mm"><b>Klasse</b></td>
				<td style="width:40mm"><b>Name</b></td>
				<td align="right" style="width:40mm"><b>Anzahl Anmeldungen</b></td>
			</tr>
			</thead>
			<tbody>
	
	
	<?php 
	
	$last = "";
	
	foreach ($ags as $ag) {
		$klasse = $ag['klasse'];
		$name = $ag['schueler_name'];
		$anzahl = $ag['anzahl'];
		
		if ($last != $klasse) {
			?>
			<tr><td colspan="3"><hr></td></tr>
			<?php 
		}

		$last = $klasse;
		?>
				
		<tr>
		<td class="<?php echo "$css"?>"><?php echo $klasse?></td>
		<td class="<?php echo "$css"?>"><?php echo $name?></td>
		<td align="right" class="<?php echo "$css"?>"><?php echo $anzahl?></td>
		</tr>
				
		<?php 
				
	}
			
	echo "</tbody></table>";
			
	echo "</page>";
	echo '<div style="position: absolute; top: 0mm; left: 130mm "><img src="../images/logo.png" style="width:45mm"/></div>';

	$pdfContent = ob_get_clean();
	
	$oben=30;    //mT
	$unten=10;   //mB
	$links=15;   //mL
	$rechts=15;  //mR
	
	$pdf = "../pdf/Uebersicht_schulleitung-" . $halbjahrFilenameSuffix . ".pdf";
	
	$html2pdf = new HTML2PDF('P','A4','de', true, 'UTF-8', array($links, $oben, $rechts, $unten));
	$html2pdf->pdf->SetDisplayMode('real');
	$html2pdf->WriteHTML($pdfContent);
	
	if ($preview) {
		$html2pdf->Output($pdf);
	} else {
		$html2pdf->Output($pdf,'F');
		
		$subject = "Übersicht Eltern-AGs für das " . $halbjahr;
		
		ob_start();
		include "template_info_schulleitung.php";
		$text = ob_get_clean();
		
		$result = "Die Mail wurde versandt<br><br>Antwort vom Mailserver:<br><br>" . 
					sendMail($mailSchulleitung, "Schulleitung", $subject, $text, array($pdf));
		
		$host  = $_SERVER['HTTP_HOST'];
		$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		$extra = "manageAGs.php?msg=".$result. "&title=Mail wurde versandt!";
		header("Location: http://$host$uri/$extra");
	}
	
	?>
	
