<?php
	session_destroy();

	include '../db.php';
	require_once("../funktionen.php");
	
	ini_set("log_errors", 1);     /* Logging "an" schalten */
	ini_set("error_log", "errorlog.txt");     /* Log-Datei angeben */

	if ($_REQUEST['generateNumbers'] == "true") {
		NeueAgModel::generateNumbers();
	}
	
	$copyId = $_REQUEST['copy']; 
	if (!empty($copyId)) {
		NeueAgModel::copyAG($copyId);
	}
	
	$deleteId = $_REQUEST['delete']; 
	if (!empty($deleteId)) {
		NeueAgModel::deleteAG($deleteId);
	}
	
	if (!empty($_REQUEST['feedback'])) {
		
		$id = $_REQUEST["id"];
		$response = $_REQUEST["response"];
		
		if ($response == "yes") {
			NeueAgModel::saveFeedBack($id,"1");
		} else if ($response == "no") {
			NeueAgModel::saveFeedBack($id,"0");
		} else if ($response == "ask") {
			NeueAgModel::saveFeedBack($id);
		}	
	}
	
	$ags = NeueAgModel::getAgs();
	
	$tableId = "";
	$title =  "NEUE Eltern-AGs verwalten";
	
	include 'header.php';
	
	if (!empty($_REQUEST['sendEditMail'])) {
		$id = $_REQUEST["id"];
		$ag = NeueAgModel::loadAG($id);
	
		$editLink = getBaseUrl() . 'neue_ag_anmelden.php?edit_token=' . $ag["edit_token"];
		$vorname = explode(" ", $ag["verantwortlicher_name"])[0];
	
		ob_start();
		include "template_edit_mail.php";
		$content = ob_get_clean();
		$subject = "Ändern der AG " . $ag["ag_name"];
	
		$status = sendMail($ag["verantwortlicher_mail"], $ag["verantwortlicher_name"], $subject, $content ,array($pdf));
		echo "Ändern der AG " . $ag["ag_name"] . " von " . $ag["verantwortlicher_name"] . ": " . $status . "<br>";
		echo "Link: " . $editLink . "<br><br>";
	
	}
	

	if ($_POST['sendMails'] == "true") {
		foreach ($_POST as $key => $idForMail) {
			if (startsWith($key, "select_")) {
				
				$ag = NeueAgModel::loadAG($idForMail);
				
				if (!isset($ag["response"]) || $ag["response"] == 2) {
					$url = getBaseUrl() . 'neue_ag_pdf.php?id='.$idForMail;
					
					$pdf = "../pdf/ag_" . $idForMail . ".pdf";
					file_put_contents($pdf, file_get_contents($url));
					
					$subject = "Überprüfung der AG " . $ag["ag_name"] . ": Passt soweit alles?";
	
					$yesLink = getBaseUrl() . 'neue_ag_feedback.php?id='.$idForMail . '&response=yes';
					$editLink = getBaseUrl() . 'neue_ag_anmelden.php?edit_token=' . $ag["edit_token"] . "&response=yes";
					
					//$noLink = getBaseUrl() . 'neue_ag_feedback.php?id='.$idForMail . '&response=no';
					ob_start();
					include "template_check_neue_ag.php";
					$content = ob_get_clean();								
					
					$status = sendMail($ag["verantwortlicher_mail"], $ag["verantwortlicher_name"], $subject, $content ,array($pdf));
					echo "Mail-Status für die AG " . $ag["ag_name"] . " von " . $ag["verantwortlicher_name"] . ": " . $status . "<br>";
					if ($status == 'Message sent!') {
						NeueAgModel::saveFeedBack($idForMail, 2);
					}
				} else {
					echo "Mail für die AG " . $ag["ag_name"] . " von " . $ag["verantwortlicher_name"] . " nicht versendet: Status ist bereits ok oder nok<br>";
				}
				
			}
		}
		
		echo "<br>";
		$ags = NeueAgModel::getAgs();
	}
	
	if ($_POST['generateEditToken'] == "true") {
		foreach ($_POST as $key => $idForGenToken) {
			if (startsWith($key, "select_")) {
				$ag = NeueAgModel::loadAG($idForGenToken);
				NeueAgModel::udpateEditToken($idForGenToken);
			}
		}
		$ags = NeueAgModel::getAgs();
	}
	
	
	
	$iconOK = '<img width="16" src="../images/thumbsUp.png" title="Alles ok">';
	$iconNOK = '<img width="16" src="../images/thumbsDown.png"  title="Das passt noch was nicht">';
	$iconASK = '<img width="16" src="../images/thumbsGray.png" title="Noch nachfragen">';
	$iconMail = '<img width="16" src="../images/mail.png" title="Mail versendet - Warten aut Antwort">';
	
?>

<script>

$(document).ready(function() {

	var table = $('#agTable').DataTable( {
		fixedHeader: {
	        header: true,
	        footer: false
	    },
        "paging":   false,
        "info": true,
        "bJQueryUI": true,
        "sDom": 'lfrtip'
        <?php echo "," . $dataTableLangSetting?>
    } );	


	var html = $('#agTable_filter').find("label").append("&nbsp;(Warnhinweis findet fehlerhafte Einträge, AllesOK die bei denen alles okay ist)");
	
    $("#sendMails").click(function() {
        $("input[name='sendMails']").attr("value",true);
    	$("#mainForm").submit();
    });

    $("a[type='showEditToken']").click(function() {
    	var link = "<?php echo getBaseUrl() ?>neue_ag_anmelden.php?edit_token=" + $(this).attr("edit_token");
    	prompt("EditLink",link);
    });

    $("#selectAll").click(function() {
    	$("input[type='checkbox']").prop('checked', true);
    });
    
    $("#generateEditToken").click(function() {
        $("input[name='generateEditToken']").attr("value",true);
    	$("#mainForm").submit();
    });
    
	<?php renderLastSearchHandling("table","manageNeueAGs.php_agTable");?>

	setContentTableVisible();
	
});
</script>

<a href="#" id="selectAll" type="button">Alle markieren</a>&nbsp;
<a href="<?php echo $_SERVER['PHP_SELF']?>?generateNumbers=true" type="button">AG-Nummern erzeugen</a>&nbsp;
<a href="#" id="sendMails" type="button">Mails an Verantwortliche senden</a>&nbsp;
<a href="#" id="generateEditToken" type="button">Edit-Token erzeugen</a>&nbsp;
<a href="createZipNeueAGs.php" type="button">Zip erzeugen</a>&nbsp;
<a href="createXlsxNeueAGs.php" type="button">Xlsx erzeugen</a>&nbsp;
<a href="importNeueAGs.php" type="button">Import Vorjahre</a>&nbsp;
<br>
<form id="mainForm" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
<input type="hidden" name="sendMails" value="false">
<input type="hidden" name="generateEditToken" value="false">
<table id="agTable" class="display">
    <thead>
        <tr>
        	<th></th>
            <th>Status</th>
            <th>Nr</th>
        	<th>AG</th>
        	<th>Termine & Uhrzeit</th>
        	<th>Check</th>
        	<th>Name</th>
        	<th>Verantwortlich</th>
        	<th>Infos</th>
        	<th>Max. Kinder</th>
        	<th>Anz. Helfer</th>
        	<th>Klassen</th>
        	<th>Betrag<br>Mitgl./Nicht-Mitgl.</th>
        	<th>Ort</th>
        	<th>Bild</th>
        	<th>Aktionen</th>
        </tr>
    </thead>
    <tbody>
		<?php 
	
		foreach ($ags as $ag) {
		?>
		
		<tr>
			<td><input type="checkbox" name="select_<?php echo $ag['id']?>" value="<?php echo $ag['id']?>"></td>
        	<td>
        		<?php if (!isset($ag['response'])) { 
        			echo $iconASK;
        		 } else if ($ag['response'] == 1) {
        			echo $iconOK;
        		 } else if ($ag['response'] == 0) {
        			echo $iconNOK;
        		 } else if ($ag['response'] == 2) {
        			echo $iconMail;
        		 } ?>
        		 <span style='display:none'>Antwort: <?php echo $ag['response']?></span>
        	</td>
			<td><?php echo $ag['ag_nummer']?></td>
        	<td><?php echo $ag['ag_name']?></td>
        	<td><nobr><?php echo formatSQLDate($ag['termin'],true) . " " . $ag['termin_von'] . "-" . $ag['termin_bis'] ?></nobr><br>
        	<?php echo formatNotEmpty("2. Termin: ","<br>",formatSQLDate($ag['termin_ueberbuchung'],true))?>
        	<?php echo formatNotEmpty("Ersatz: ","<br>",formatSQLDate($ag['termin_ersatz'],true))?>
        	</td>
        	<td><table class="checkTable"><?php
        		try {
					$messages = NeueAgModel::checkAG($ag);
					$errCount = 0;
					foreach ($messages as $label => $comment ) {
						echo "<tr><td>$label</td>";
						if (empty($comment)) {
							echo "<td><img class='sprite check' src='../images/trans.png'></td>";
						} else {
							echo "<td><img class='sprite warning' src='../images/trans.png' title='$comment'></td>";
							$errCount++;
						}
						echo "</tr>";
						}
				} catch (Exception $e) {
					error_log('Exception abgefangen: ',  $e->getMessage());
				}
        	?>
        	</table>
        	<?php 
        	if ($errCount == 0) {
        		echo "<span style='display:none'>AllesOK</span>";
        	} else {
        		echo "<span style='display:none'>Warnhinweise</span>";
        	}
        	?>
        	</td>
        	<td><?php echo $ag['namen']?></td>
        	<td><?php echo $ag['verantwortlicher_name']?><br><?php echo $ag['verantwortlicher_telefon']?><br><?php echo formatAsMailto($ag['verantwortlicher_mail'])?></td>
        	<td><?php echo str_abbreviate($ag['text_ausschreibung'],75)?><?php echo formatWithCaption("Wichtige Infos",str_abbreviate($ag["wichtige_infos"],50))?><?php echo formatWithCaption("Ausserdem",str_abbreviate($ag["ausserdem"],50))?></td>
        	<td><?php echo formatMaxKinder($ag['max_kinder'])?></td>
        	<td><?php echo $ag['anzahl_helfer']?></td>
        	<td><?php echo formatKlasse($ag,'klasse1')?><?php echo formatKlasse($ag,'klasse2')?><?php echo formatKlasse($ag,'klasse3')?><?php echo formatKlasse($ag,'klasse4')?></td>
        	<td><?php echo formatAsCurrency($ag['betrag_mitglied'])?>/<?php echo formatAsCurrency($ag['betrag_nicht_mitglied'])?></td>
        	<td><?php echo $ag['ort']?></td>
        	<td><?php echo getImageLink("true",$ag, "../","max-width:50px")?></td>
        	<td>
        		<a href="editNeueAG.php?edit_token=<?php echo $ag["edit_token"]?>"><img width="16" src="../images/edit.png"></a>
        		<a href="manageNeueAGs.php?copy=<?php echo $ag["id"]?>"><img width="16" src="../images/copy.png"></a>
        		<a href="manageNeueAGs.php?delete=<?php echo $ag["id"]?>"><img width="16" src="../images/delete.png"></a>
        		<a href="../neue_ag_pdf.php?id=<?php echo $ag["id"]?>" target="_blank"><img width="16" src="../images/pdf.gif"></a>
        		<a href="manageNeueAGs.php?feedback=true&id=<?php echo $ag["id"]?>&response=yes"><?php echo $iconOK?></a>
        		<a href="manageNeueAGs.php?feedback=true&id=<?php echo $ag["id"]?>&response=no"><?php echo $iconNOK?></a>
        		<a href="manageNeueAGs.php?feedback=true&id=<?php echo $ag["id"]?>&response=ask"><?php echo $iconASK?></a>
        		<?php if (isset($ag["edit_token"])) { ?>
        			<a href="manageNeueAGs.php?sendEditMail=true&id=<?php echo $ag["id"]?>"><img width="16" src="../images/mail.png" title="Edit-Mail versenden"></a>
        			<a type="showEditToken" href="#" edit_token="<?php echo $ag["edit_token"]?>"><img width="16" src="../images/edit_link.png" title="Edit-Link anzeigen"></a>
        		<?php } ?>
        		</td>
        	</tr>
        <?php } ?>
    </tbody>
</table>
</form>
<?php include 'footer.php';?>

