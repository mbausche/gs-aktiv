<?php

	require_once '../db.php';
	require_once '../funktionen.php';
	
	$tableId = "";
	$title =  "";
	
	include 'header.php';
	
	$mithilfe = AgModel::getMithilfe();
	$ideen = AgModel::getIdeen();
	$neueAgs = NeueAgModel::getAgs(" order by id desc");
	
?>
<script type="text/javascript" src="../scripts/packery/packery.pkgd.min.js"></script>
<!-- 
<table class="index_table">
<tr>
<td valign="top">
</td>
<td valign="top">
</td>
</tr>
</table> -->
<div id="container">
<div class="item w2 index-widget ui-widget-content"><?php include 'index_allgemeines.php';?></div>
<div class="item index-widget ui-widget-content"><?php include 'index_top10.php';?></div>
<div class="item index-widget ui-widget-content"><?php include 'index_startetbald.php';?></div>
<?php if (count($neueAgs) > 0 ) { ?>
<div class="item index-widget ui-widget-content"><?php include 'index_neue_ag.php';?></div>
<?php } ?>
<?php if (count($mithilfe) > 0 || count($ideen) > 0 ) { ?>
<div class="item w2 index-widget ui-widget-content"><?php include 'index_ideen.php';?></div>
<?php } ?>

</div>

<script>
$(document).ready(function() {

	var $container = $('#container');
	// init
	$container.packery({
	  itemSelector: '.item',
	  gutter: 0,
	  transitionDuration: "1s",
	});

	// get item elements, jQuery-ify them
	//var $itemElems = $container.find('.item');
	// make item elements draggable
	//$itemElems.draggable();
	// bind Draggable events to Packery
	//$container.packery( 'bindUIDraggableEvents', $itemElems );
	setContentTableVisible();
		
	
});
</script>


<?php include 'footer.php'; ?>

