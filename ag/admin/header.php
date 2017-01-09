<html>
<head>

<?php 
setlocale (LC_ALL, 'de_DE@euro', 'de_DE', 'de', 'ge');
include "include_dataTables_lang.php";

if (isset($_REQUEST['msg'])) {
	$message = $_REQUEST['msg'];
	$dialogTitle = $_REQUEST['title'];
}

?>

<title><?php echo $title?></title>
<meta charset="utf-8"/>
<link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>


<link rel="stylesheet" type="text/css" href="../scripts/jquery-ui/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="../scripts/dataTables/datatables.min.css"/>
<link rel="stylesheet" type="text/css" href="../scripts/dataTables/css/fixedHeader.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="../css/main.css"/>

<script type="text/javascript" src="../scripts/allgemein.js"></script>
<script type="text/javascript" src="../scripts/jquery/jquery.js"></script>
<script type="text/javascript" src="../scripts/jquery-ui/jquery-ui.js"></script>
<script type="text/javascript" src="../scripts/dataTables/datatables.js"></script>
<script type="text/javascript" src="../scripts/dataTables/js/dataTables.fixedHeader.min.js"></script>

<script type="text/javascript">

	function setContentTableVisible() {
		$('#ajaxWait').hide();
		$('#contentTable').css('opacity',1);
	}

	$(document).ready( function () {

		$( "a[type=button], input[type=submit]" )
	    .button()
	    	.click(function( event ) {
	    });
	
		
		<?php if ($tableId != "") { 
			echo "$('#" .$tableId ."').DataTable({". $dataTableLangSetting . "});";
		}?>

		$( "#dialog-message" ).dialog({
	        modal: true,
	        buttons: {
	          Ok: function() {
	            $( this ).dialog( "close" );
	          }
	        }
	    });

		$( "#dialog-auswertungen" ).hide();
		$( "#dialog-verwaltung" ).hide();
		$( "#dialog-neue_ag" ).hide();
		
		$( "#btnAuswertungen" )
	    .button()
	    	.click(function( event ) {
	    		var button = "#btnAuswertungen";
	    		$( "#dialog-auswertungen" ).dialog( {
	    	        modal: true,
	    	        width: 350,
	    	        position: { my: "left top", at: "left bottom", of: button  },
	    	        buttons: {}
	    	    });
		    });

		$( "#btnVerwaltung" )
	    .button()
	    	.click(function( event ) {
	    		var button = "#btnVerwaltung";
	    		$( "#dialog-verwaltung" ).dialog( {
	    	        modal: true,
	    	        width: 350,
	    	        position: { my: "left top", at: "left bottom", of: button  },
	    	        buttons: {}
	    	    });
		    });		

		$( "#btnNeueAG" )
	    .button()
	    	.click(function( event ) {
	    		var button = "#btnNeueAG";
	    		$( "#dialog-neue_ag" ).dialog( {
	    	        modal: true,
	    	        width: 350,
	    	        position: { my: "left top", at: "left bottom", of: button  },
	    	        buttons: {}
	    	    });
		    });		

    	$( "#index" ).button({
			  icons: { primary: "ui-icon-home" }
		});

		

	} );
</script>

</head>
<body>
<table id="adminHeader">
<tr><td>
<br>
<br>
<br>
</td></tr>
</table>

<div id="ajaxWait" class="ui-widget-content">
<div id="cellAjaxWait">
	<img src="../images/ajax-loader.gif"><br>
	Seite wird geladen...
</div>
</div>

<table style="width:100%;opacity:0	" id="contentTable">
<?php if (!isset($includeMenu) || $includeMenu != false) {?>
<tr>
<td>

<a type="button" id="index" href="index.php"/></a>&nbsp;
<a type="button" href="../anmelden.php?direkt_eingabe=1">Anmeldung Eingeben</a>&nbsp;
<a type="button" id="pruefeAnmeldungen"  href="pruefeAnmeldungen.php">Anmeldungen pruefen</a>&nbsp;
<a type="button" id="manageAGs" href="manageAGs.php">AGs verwalten</a>&nbsp;
<a type="button" id="listStatus" href="listStatus.php">Status der Schüler anzeigen</a>&nbsp;
<a id="btnAuswertungen" href="#">Auswertungen</a>&nbsp;
<a id="btnVerwaltung" href="#">Verwaltung</a>&nbsp;
<a id="btnNeueAG" href="#">Neue-AGs</a>&nbsp;

<?php } ?>

</td>
</tr>
<tr><td>
<br>
<h1><?php echo $title?></h1>

<?php if (!empty($message)) { ?>

<div id="dialog-message" title="<?php echo $dialogTitle?>">
  <p>
    <span class="ui-icon ui-icon-circle-check" style="float:left; margin:0 7px 50px 0;"></span>
    <?php echo $message;?>
  </p>
</div>

<?php } ?>

<div id="dialog-auswertungen" title="Auswertungen">
<a type="button" id="btn_info_pdf_verantwortliche" href="printInfosSchulleitung.php?preview=true" target="_blank">Schulleitung informieren (Preview)</a><br><br>
<a type="button" id="btn_info_verantwortliche" href="printInfosSchulleitung.php">Schulleitung informieren</a><br><br>
<a type="button" id="mailingList" href="mailingList.php?typ=Teilnehmer">Mail-Verteiler Teilnehmer</a><br><br>
<a type="button" id="mailingList" href="mailingList.php?typ=Veranstalter">Mail-Verteiler AG-Veranstalter</a><br><br>
<a type="button" id="mailingList" href="mailingList.php?typ=Alle">Mail-Verteiler Alle</a><br><br>
<a type="button" id="btn_summen" href="printInfosSummen.php">Übersicht mit Summen (Excel)</a><br><br>
<a type="button" id="btn_statistic" href="statistic.php">Statistik Vorjahre</a><br><br>
<a type="button" id="btn_alleAnmeldungen" href="anmeldungen_alle.php">Anmeldungen Vorjahre</a>
</div>

<div id="dialog-verwaltung" title="Verwaltung">
<a type="button" id="manageAnmeldungen" href="manageAnmeldungen.php">Geprüfte Anmeldungen verwalten</a><br><br>
<a type="button" id="manageTemplates" href="manageTemplates.php">Vorlagen verwalten</a><br><br>
<a type="button" id="manageConfig" href="manageConfig.php">Konfiguration</a><br><br>
<a type="button" id="indexAdmin" href="index_admin.php">Weitere Admin-Seiten</a><br><br>
</div>

<div id="dialog-neue_ag" title="Neue-AGs">
<a type="button" id="manageNeueAGs" href="manageNeueAGs.php">Neue AGs verwalten</a><br><br>
<a type="button" id="termineNeueAGs" href="neue_ag_termine.php">Terminübersicht</a><br><br>
</div>