<?php 
header("HTTP/1.1 404 Not Found",false, 404);
?>
<html><head>
<title>404 Not Found</title>
</head><body>
<h1>Not Found</h1>

<?php 
$qs = $_SERVER['QUERY_STRING'];
if (!empty($qs))
	$qs = "?" . $qs;
?>

<p>The requested URL <?php echo $_SERVER['PHP_SELF'] . $qs?> was not found on this server.</p>
</body></html>
