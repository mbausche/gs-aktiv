<?php
	$title = "Eltern-AG-Portal";
	include_once 'header_bootstrap.php';
?>

<script type="text/javascript">

$( document ).ready(function() {

		$("#sogehts").click(function() {
			$("#bilderBank").hide();
			$("#bilder").toggle("slow");
		});

		$("#sogehtsBank").click(function() {
			$("#bilder").hide();
			$("#bilderBank").toggle("slow");
		});		

});
</script>
</head>
<body>
  <div class="container-fluid">
	<div class="row" style="height:100px; background-image: url('images/heading.jpg');background-repeat: repeat-x">
		<div class="col-md-12"> 
				
		</div>
	</div>
	</div>

<?php 

$anmeldung = CfgModel::load("aktiv.anmeldung") == "true";
$neueAG = CfgModel::load("aktiv.neueag.anmelden") == "true";
?>

<?php if (!$anmeldung && !$neueAG) { ?>

<br>
Derzeit ist keine Anmeldung zu einer AG möglich und es können auch derzeit keine AG-Vorschläge mehr eingereicht werden!<br>
<br>
Bei Fragen erreichen Sie uns per Mail unter info@grundschule-aktiv.de

<?php } ?>

<?php if ($anmeldung ) { ?>
<br>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">Eltern-AG Anmeldung</h3>
	</div>
	<div class="row">
		<div class="col-md-6" style="vertical-align: middle; text-align: center;">
			<br>
			<a type="button" class="btn btn-default" id="anmelden" href="anmelden2.php">Zu einer AG anmelden</a><br><br>
			<a type="button" class="btn btn-default" href="abfrage.php">Ist mein Kind angemeldet?</a><br><br>
	  	</div>
	  	<div class="col-md-6">
	  		<h4>Hilfe:</h4>
			<a  type="button" class="btn btn-link" id="sogehts" href="#">Zahlart 1: Geld mit in die Schule geben - So geht's</a><br>
			<a  type="button" class="btn btn-link" id="sogehtsBank" href="#">Zahlart 2: So geht's per Überweisung</a><br>
			<a  type="button" href="anmelden_infos_bank.php" target="_blank" type="button" class="btn btn-link">Mehr Infos zum Bezahlen per Überweisung</a><br>
	  	</div>
	</div>
</div>
	
<div id="bilder" style="display:none">
<br>
<?php renderImageTableBootstrap("./images/",array(1,2,3,4,5,6),true)?>
</div>

<div id="bilderBank" style="display:none">
<br>
<?php renderImageTableBootstrapBank("./images/",array(1,2,3,4),true)?>
</div>


<?php } ?>

<?php if ($neueAG) { ?>

<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">Eine neue AG anbieten</h3>
	</div>
	<div align="center">
		<br>
		<a type="button" class="btn btn-default" href="neue_ag_anmelden.php?copy=false">Ich möchte im nächsten Heft eine AG anbieten</a><br><br>
	</div>
</div>


<?php } ?>


</body>
</html>