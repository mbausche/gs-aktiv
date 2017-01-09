<script type="text/javascript">
<?php
$typ = $_REQUEST["typ"];
if (empty($typ)) {
	$typ = "alle";	
}
$termine = NeueAgModel::getAlleTermineMitAg("",$typ);
$ferienString = CfgModel::load("neue.ag.ferien");
include 'include_checkDate.php';
?>

$( document ).ready(function() {
	addDatePickerOverview("<?php echo CfgModel::load("neue.ag.zeitraum.von") ?>","<?php echo CfgModel::load("neue.ag.zeitraum.bis") ?>");

	<?php 
	
	$array = array();
	
	foreach ($termine as $termin) {
		$array1 = array();
		
		array_push($array1, "getDate(\"". formatSQLDateForJavascript($termin["termin"]) ."\")" );
		array_push($array1, "\"".$termin["ag_name"]."\"");
		array_push($array1, "\"".$termin["typ"]."\"");
		array_push($array1, "\"".$termin["von"] . "-" . $termin["bis"]."\"");
		array_push($array1, "\"".$termin["verantwortlicher_name"]."\"");
		array_push($array, implode(",", $array1));
	}
	
	$jsArray = "[" . implode("]\n,[", $array) . "]"
	?>
	var dataSet = [
	<?php echo $jsArray ?>
	];

    $('#tabelle_uebersicht').DataTable( {
        data: dataSet,
        autoWidth: false,
        columns: [
            { title: "Termin",
              data: "date",
              render: function ( data, type, full, meta ) {
            	  return  full[0] ? full[0].format() : "-";
              }
            },
            { title: "AG" },
            { title: "Typ" },
            { title: "Uhrzeit" },
            { title: "Verantwortlich" }
        ],
	 	"paging":   false,
        "info":     false,
        "sortable": false,
        "bJQueryUI": true,
        "sDom": 'ft',
 	 	/* Disable initial sort */
        "aaSorting": []
		<?php echo "," . $dataTableLangSetting ?>
        
    } );	
	
		
});
</script>
<style>
#tabelle_uebersicht_wrapper {
	max-width: 800px;
}

.ui-datepicker-inline {
	border: 0px;
}

.ui-datepicker-group-middle , .ui-datepicker-group-first {
	padding-right: 5px;
}

.ui-datepicker-group-middle , .ui-datepicker-group-last {
	padding-left: 5px;
	border-left: 1px solid gray;
}

</style>

<div id="datepicker_uebersicht"></div>
<div class="cal_error_legend">ROTE FELDER: An diesm Termin findet bereits eine AG statt</div>
<div class="cal_error_termin2_legend">ORANGE FELDER: An diesm Termin findet bereits der Ersatztermin oder der 2. Termin für eine AG statt</div>
<div class="cal_holiday_legend">VIOLETTE FELDER: Dieser Termin liegt in den Ferien</div>
<div>AUSGEGRAUTE FELDER: Der Termin liegt vor dem frühester Zeitpunkt für eine AG (<?php echo CfgModel::load("neue.ag.zeitraum.von") ?>) oder nach dem spätesten Zeitpunkt (<?php echo CfgModel::load("neue.ag.zeitraum.bis") ?>)
</div>
<?php if ($typ == "alle") {?>
<h2>AG-Termine</h2>
<a type="button" href="<?php echo $_SERVER['PHP_SELF']?>?typ=termin1">Nur 1. Termin anzeigen</a>
<?php } else { ?>
<h2>AG-Termine (nur 1. Termin)</h2>
<a type="button" href="<?php echo $_SERVER['PHP_SELF']?>?typ=alle">Alle Termine anzeigen</a>
<?php } ?>
<a type="button" href="<?php echo getBaseUrl() . "neue_ag_anmelden.php"?>">Ich will eine AG anbieten</a>

<table id="tabelle_uebersicht" class="display" style="width:100%"></table>

<?php 
include_once 'footer.php';
?>