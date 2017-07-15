<?php
	include '../db.php';
	require_once("../funktionen.php");
	
	$tableId = "";
	$title =  "Vorlagen verwalten";
	
	if (isset($_REQUEST['newEntry'])) {
		$t = TemplateModel::create($_REQUEST["template"], $_REQUEST["beschreibung"], $_REQUEST["code"]);
	    $filename = $t["name"] . ".php";
	    file_put_contents($filename, "<?php \n//Generiert am: " . date(DATE_RFC822) . " ?>\n". $t["code"]);
	}
	
	if (isset($_REQUEST['loesche'])) {
		$t = TemplateModel::load($_REQUEST["id"]);
		$filename = $t["name"] . ".php";
		unlink($filename);
		$t = TemplateModel::delete($_REQUEST["id"]);
	}
	
		
	if (isset($_REQUEST['save'])) {
		for ($i = 1; $i <= 100; $i++) {
	    	$code = $_REQUEST['code_' . $i];
	    	if (!empty($code)) {
	    		TemplateModel::save($i, $code);
	    		$t = TemplateModel::load($i);
	    		$filename = $t["name"] . ".php";
	    		file_put_contents($filename, "<?php \n//Generiert am: " . date(DATE_RFC822) . " ?>\n". $code);
	    	}
		}
	}
	
	include 'header.php';
	$templates = TemplateModel::getTemplates();
?>

<link rel=stylesheet href="../scripts/codemirror/doc/docs.css">
<link rel="stylesheet" href="../scripts/codemirror/lib/codemirror.css">
<script src="../scripts/codemirror/lib/codemirror.js"></script>
<script src="../scripts/codemirror/addon/edit/matchbrackets.js"></script>
<script src="../scripts/codemirror/mode/htmlmixed/htmlmixed.js"></script>
<script src="../scripts/codemirror/mode/xml/xml.js"></script>
<script src="../scripts/codemirror/mode/javascript/javascript.js"></script>
<script src="../scripts/codemirror/mode/css/css.js"></script>
<script src="../scripts/codemirror/mode/clike/clike.js"></script>
<script src="../scripts/codemirror/mode/php/php.js"></script>
<style type="text/css">
.CodeMirror {
	border-top: 1px solid black;
	border-bottom: 1px solid black;
}

#templateTable_filter {
    float: left;
}
</style>

<script>
$(document).ready(function() {

	<?php
	foreach ($templates as $t) {
	?>   
	
	CodeMirror.fromTextArea(document.getElementById("textarea_<?php echo $t['id']?>"), {
	    lineNumbers: false,
	    mode: "php"
	});

	

	<?php } ?>

	CodeMirror.fromTextArea(document.getElementById("textarea_new"), {
	    lineNumbers: false,
	    mode: "php"
	});
	
	
	var table = $('#templateTable').DataTable( {
        "paging":   false,
        "info": true,
        "bJQueryUI": true,
        "sDom": 'lfrtip'
		<?php echo "," . $dataTableLangSetting ?>
    } );

	<?php renderLastSearchHandling("table","manageTemplates.php_templateTable");?>
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
	        width: 750,
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
	<form action="manageTemplates.php" method="post" id="newForm">
	<table id="newTable" class="display" style="width:100%; text-align: left; margin-right:auto;margin-left:0px">
	<thead style="display: none;">
	<tr>
	<td>col1</td><td>col1</td>
	</tr>
	</thead>
	<tbody>
	<tr><td valign="middle">Template</td><td valign="middle"><input size="30" maxlength="100" type="text" name="template" value=""></td></tr>
	<tr><td valign="middle">Beschreibung</td><td valign="middle"><input size="30" maxlength="200" type="text" name="beschreibung" value=""></td></tr>
	<tr><td valign="middle">Code</td><td valign="middle"><textarea rows="20" cols="80" id="textarea_new" name="code" ></textarea></td></tr>
	</tbody>
	</table>
	<input type="hidden" name="newEntry" value="true"/>
	</form>
</div>

<img src="../images/new.png" title="Neuen Eintrag anlegen" id="newButton"><br>
<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">

<table id="templateTable" class="display">
    <thead>
        <tr>
            <th>Name</th>
        	<th>Kommentar</th>
        	<th>Code</th>
        </tr>
    </thead>
    <tbody>
		<?php
			foreach ($templates as $t) {
		?>    
        <tr>
        	<td valign="middle"><?php echo $t["name"]?><br><br><a type="buttonDelete" title="Löschen" href="<?php echo $_SERVER['PHP_SELF']?>?loesche=true&id=<?php echo $t["id"]?>"></a></td>
        	<td valign="middle"><?php echo $t["description"]?></td>
        	<td><textarea rows="20" cols="80" id="textarea_<?php echo $t["id"]?>" name="code_<?php echo $t["id"]?>" ><?php echo $t["code"]?></textarea></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<br>
<input type="submit" name="save" value="Speichern"></input><br><br>
</form>
<br>


<?php include 'footer.php';?>

