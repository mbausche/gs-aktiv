<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>datepicker demo</title>
    <style type="text/css">
  
  .error {
  	background-color: red;
  }
  
  .warning {
  	background-color: orange;
  }
  
  </style>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
  
</head>

<body>
 
<p>Date: <input type="text" id="datepicker"></p>
 
<script>
$(document).ready(function() {
	$( "#datepicker" ).datepicker({
		beforeShowDay: function(date) {
			return checkDate(date);
		}
	});
});


function checkDate(date) {
	var s = date.getDate() + "." + (date.getMonth()+1) + "." + date.getFullYear();
	if (s == "10.1.2016") {
		return [true,"error","Schon belegt!"];
	}
	return [true,""];
}

</script>
 
</body>
</html>