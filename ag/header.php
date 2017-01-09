<?php
	setlocale (LC_ALL, 'de_DE@euro', 'de_DE', 'de', 'ge');	
	require_once("funktionen.php");
	require_once("db.php");
	
	include "admin/include_dataTables_lang.php";
	
	ini_set("log_errors", 1);     /* Logging "an" schalten */
 	ini_set("error_log", "errorlog.txt");     /* Log-Datei angeben */
?>
<html>
<head>
<title><?php echo $title?></title>
<meta charset="utf-8"/>
<link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="scripts/jquery-ui/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="scripts/dataTables/datatables.min.css"/>
<link rel="stylesheet" type="text/css" href="css/main.css"/>
<script type="text/javascript" src="scripts/jquery/jquery.js"></script>
<script type="text/javascript" src="scripts/jquery-ui/jquery-ui.js"></script>
<script type="text/javascript" src="scripts/dataTables/datatables.js"></script>
<script type="text/javascript" src="scripts/allgemein.js"></script>

<script type="text/javascript">
$(document).ready( function () {
	$("#dialog-message").hide();
	$( "a[type=button]" )
    .button()
    	.click(function( event ) {
    });
	
});
</script>

<style type="text/css">
<!--

.red
{
	color: red;
}

table td
{
    font-size:    10pt;
}

table.ags td
{
    font-size: 9pt;
	padding: 0px !important;
	padding-right: 3px !important;
	white-space: nowrap;    
}

body {
}

h2 {
    font-size:    14pt;
}

h3 {
    font-size:    12pt;
}

-->
</style>

</head>
<body>
<div id="dialog-message" title="" >
  <p>
    <span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 50px 0;"></span>
    <span id="dialog-message-content"></span>
  </p>
</div>

<table id="userHeader">
<tr><td>
<br>
<br>
<br>
</td></tr>
</table>
<h1><?php echo $title.$addAfterHeading?></h1>
