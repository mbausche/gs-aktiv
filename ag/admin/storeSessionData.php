<?php 

session_start();

$key = $_REQUEST['key'];
$value = $_REQUEST['value'];

if (!empty($key)) {
	$_SESSION[$key] = $value;
}

?>