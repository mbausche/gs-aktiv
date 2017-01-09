<?php 
include '../db.php';

$id = $_REQUEST['id'];
if (!empty($id)) {
	AgModel::setIdeeOK($id);	
}


?>
