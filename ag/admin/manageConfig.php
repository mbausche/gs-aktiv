<?php
	include '../db.php';
	require_once("../funktionen.php");
	
	ini_set("log_errors", 1);     /* Logging "an" schalten */
	ini_set("error_log", "errorlog.txt");     /* Log-Datei angeben */
	
	$tableId = "";
	$title =  "Konfiguration";
	

	if (isset($_REQUEST['newEntry'])) {
		$t = CfgModel::create($_REQUEST["name"], $_REQUEST["beschreibung"], $_REQUEST["wert"]);
		$filename = $t["name"] . ".php";
		file_put_contents($filename, "<?php \n//Generiert am: " . date(DATE_RFC822) . " ?>\n". $t["code"]);
	}
	
	if (isset($_REQUEST['loesche'])) {
		$t = CfgModel::delete($_REQUEST["id"]);
	}
	
		
	
	if (isset($_REQUEST['save'])) {
		for ($i = 1; $i <= 100; $i++) {
	    	$value = $_REQUEST['cfg_' . $i];
	    	if (!empty($value)) {
	    		CfgModel::save($i, $value);
	    	}
		}
	}
	
	include 'header.php';
	$configEntries = CfgModel::getEntries();
?>

<link rel=stylesheet href="../codemirror/doc/docs.css">
<link rel="stylesheet" href="../codemirror/lib/codemirror.css">
<script src="../codemirror/lib/codemirror.js"></script>
<script src="../codemirror/addon/edit/matchbrackets.js"></script>
<script src="../codemirror/mode/htmlmixed/htmlmixed.js"></script>
<script src="../codemirror/mode/xml/xml.js"></script>
<script src="../codemirror/mode/javascript/javascript.js"></script>
<script src="../codemirror/mode/css/css.js"></script>
<script src="../codemirror/mode/clike/clike.js"></script>
<script src="../codemirror/mode/php/php.js"></script>
<style type="text/css">.CodeMirror {border-top: 1px solid black; border-bottom: 1px solid black;}</style>

<script>
$(document).ready(function() {
	var table = $('#cfg').DataTable({
        "paging":   false,
        "info": true,
        "bJQueryUI": true,
        "sDom": 'lfrtip'
        <?php echo "," . $dataTableLangSetting ?>
	});

	table
	.columns( '.name' )
    .order( 'asc' )
	.page.len( -1 )
	.draw();

	<?php renderLastSearchHandling("table","manageConfig.php_cfg");?>	
	setContentTableVisible();

    $('#newTable').DataTable( {
    	"paging":   false,
        "ordering": false,
        "searching": false,
        "info":     false,
        "bJQueryUI": true,
        "sDom": 'lfrtip'
    	<?php echo "," .$dataTableLangSetting ?>
	} );
	
	$("#newButton").click(function() {
		var button = "#newButton";
		$( "#dialog-new" ).dialog( {
	        modal: true,
	        width: 500,
	        position: { my: "left top", at: "left bottom", of: button  },
	        buttons: {
	          Ok: function() {
	            $( "#newForm" ).submit();
	          }
	        }
	    });
	});

	 $( "[type='buttonDelete']" ).button({
		  icons: { primary: "ui-icon-trash" }
	 });	
	
	$( "#dialog-new" ).hide();	
	
	
});
</script>

<div id="dialog-new" title="Neuen Datensatz anlegen">
	<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post" id="newForm">
	<table id="newTable" class="display" style="width:100%; text-align: left; margin-right:auto;margin-left:0px">
	<thead style="display: none;">
	<tr>
	<td>col1</td><td>col1</td>
	</tr>
	</thead>
	<tbody>
	<tr><td valign="middle">Name</td><td valign="middle"><input size="30" maxlength="100" type="text" name="name" value=""></td></tr>
	<tr><td valign="middle">Beschreibung</td><td valign="middle"><input size="30" maxlength="200" type="text" name="beschreibung" value=""></td></tr>
	<tr><td valign="middle">Wert</td><td valign="middle"><textarea rows="10" cols="80" id="textarea_new" name="wert"></textarea></td></tr>
	</tbody>
	</table>
	<input type="hidden" name="newEntry" value="true"/>
	</form>
</div>

<img src="../images/new.png" title="Neuen Eintrag anlegen" id="newButton"><br>

<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">

<table id="cfg" class="display">
    <thead>
        <tr>
            <th class="name">Name</th>
        	<th>Kommentar</th>
        	<th>Wert</th>
        	<th></th>
        </tr>
    </thead>
    <tbody>
		<?php
			foreach ($configEntries as $t) {
		?>    
        <tr>
        	<td><?php echo $t["name"]?></td>
        	<td><?php echo $t["description"]?></td>
        	<td>
        	<?php if (strlen($t["value"]) < 50) {?>
        		<input type="text" id="cfg_<?php echo $t["id"]?>" size="100" maxlength="5000" name="cfg_<?php echo $t["id"]?>" value="<?php echo $t["value"]?>"><span style="display:none"><?php echo $t["value"]?></span>
        	<?php } else { ?>
        		<textarea rows="10" cols="80" id="textarea_new" name="cfg_<?php echo $t["id"]?>"><?php echo $t["value"]?></textarea>
        	<?php } ?>
        	</td>
        	<td><a type="buttonDelete" title="Löschen" href="<?php echo $_SERVER['PHP_SELF']?>?loesche=true&id=<?php echo $t["id"]?>"></a></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<br>
<input type="submit" name="save" value="Speichern"></input><br><br>
</form>
<br>


<?php include 'footer.php';?>

