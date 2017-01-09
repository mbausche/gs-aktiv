<?php
	include_once '../db.php';
	require_once("../funktionen.php");
	
	$aktion = $_REQUEST['aktion'];
	$id = $_REQUEST['id'];
	
	if (!empty($aktion) && !empty($id)) {
		if ($aktion == "istMitglied") {
			StatusModel::updateStatus($id, 1, true);
		} else if ($aktion == "istNichtMitglied") {
			StatusModel::updateStatus($id, 0, true);
		} else if ($aktion == "loeschen") {
			StatusModel::delete($id,true);
		}
	}
	
	if ($aktion == "neu") {
		StatusModel::updateStatus($_REQUEST["name"], $_REQUEST["status"] == "mitglied" ? 1 : 0);
	}	
	
	
	$tableId = "";
	if (empty($title))
		$title =  "Status-Liste";	
	
	$status = StatusModel::getAll();
	
	include 'header.php';
?>

<link rel="stylesheet" href="../scripts/dataTables/css/fixedHeader.dataTables.min.css">
<script src="../scripts/dataTables/js/dataTables.fixedHeader.min.js"></script>



<script>
$(document).ready(function() {

	var table = $('#status').DataTable( {
		fixedHeader: {
	        header: true,
	        footer: false
	    },
	    "paging":   false,
	    "info": true,
	    "bJQueryUI": true,
	    "sDom": 'lfrtip'
        
		<?php echo "," . $dataTableLangSetting ?>
	});

	<?php renderLastSearchHandling("table","listStatus.php_status");?>

	setContentTableVisible();

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
	<tr><td valign="middle">Mitglied</td><td valign="middle"><input type="checkbox" name="status" value='mitglied'></td></tr>
	</tbody>
	</table>
	<input type="hidden" name="aktion" value="neu"/>
	</form>
</div>

<img src="../images/new.png" title="Neuen Eintrag anlegen" id="newButton"><br>


<table id="status" class="display">
    <thead>
        <tr>
        	<th>Name</th>
            <th>Mitglied ?</th>
            <th>Aktionen</th>
        </tr>
    </thead>
    <tbody>
		<?php
			foreach ($status as $name => $statusDBObject) {
				$id = $statusDBObject["id"];
		?>    
		
        <tr>
            <td><?php echo $statusDBObject['name']?></td>
            <td><?php echo formatAsYesNo($statusDBObject['status'])?></td>
            <td>
            <?php if ($statusDBObject['status'] == 1) { ?>
	            <a type="button" title="Ist Nicht-Mitglied" href="<?php echo $_SERVER['PHP_SELF']?>?aktion=istNichtMitglied&id=<?php echo $id?>">NMG</a>
            <?php } else { ?>
    	        <a type="button" title="Ist Mitglied" href="<?php echo $_SERVER['PHP_SELF']?>?aktion=istMitglied&id=<?php echo $id?>">MG</a>
            <?php } ?>
            <a type="buttonDelete" title="Löschen" href="<?php echo $_SERVER['PHP_SELF']?>?aktion=loeschen&id=<?php echo $id?>"></a>
            </td>
       </tr>
        <?php } ?>
    </tbody>
</table>
<br>
<br>

<?php include 'footer.php';?>



