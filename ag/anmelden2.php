<?php 

$title = "Eltern-AG Anmeldung";
include_once 'header_bootstrap.php';

if ($_REQUEST['reenter'] == "true") {
	session_start();
} else {
	session_start();
	destroySession();
	session_start();
}

$order = "heft";
if (!empty($_SESSION["order"])) {
	$order = $_SESSION["order"];
}
if (!empty($_REQUEST["order"])) {
	$order = $_REQUEST["order"];
}

error_log(print_r($_SESSION,true));

$ags = $order == "heft" ? AgModel::getAgsByAGNummer() : AgModel::getAgsByDatum();

if ($_REQUEST['direkt_eingabe'] == "1") {
	$_SESSION['direkt_eingabe'] = "1";
} else {
	$_SESSION['direkt_eingabe'] = "0";
}

if ($_REQUEST['geprueft'] == "1") {
	$_SESSION['geprueft'] = "1";
} else {
	$_SESSION['geprueft'] = "0";
}

?>
	
	<script type="text/javascript">
	var ags = new Array ();
	var ags1 = new Array ();
	var agNamen = new Array ();
	var betrag = 0;
	var agsSelected = 0;
	var formatted = 0;
	var checkingJoined = false;
	/*
	 * filterKlassen in Session
	 * 0 = nachfragen
	 * 1 = filtern
	 * 2 = nicht filtern 
	 */

	var currentKlasse = "<?php if (!empty($_SESSION['klasseFuerFilter'])) { echo $_SESSION['klasseFuerFilter']; } else { echo "0"; } ?>";
	var filterKlassen = 1;//<?php if (!empty($_SESSION['filterKlassen'])) { echo $_SESSION['filterKlassen']; } else { echo "0"; } ?>;

	
    $( document ).ready(function() {

    	<?php 
    			foreach ($ags as $ag) {
    				$betrag = number_format($ag["betrag_mitglied"],2,".","");
    				$betrag1 = number_format($ag["betrag_nicht_mitglied"],2,".","");
    				$name = $ag["name"];
    				$nr = $ag['ag_nummer'];
    				echo "ags['$nr'] = $betrag;\n";
    				echo "ags1['$nr'] = $betrag1;\n";
    				echo "agNamen['$nr'] = \"$name\";\n";
    			}
    	?>
    			        
		$("input[agcheckbox='true']").checkboxpicker({
			  html: true,
			  offLabel: 'Nicht teilnehmen',
			  onLabel: 'Teilnehmen',
			  offIconCls: 'glyphicon-thumbs-down',
			  onIconCls: 'glyphicon-thumbs-up'
		});

		$("input[agcheckbox='true']").change(function() {
			if (!checkingJoined) {
				toastr.clear();
				checkingJoined = true;
				checkJoined(this.name, this.checked);
				checkingJoined = false;
				calcSum();
				showSum(true);
			}
		});

		 $("[pflicht='true']").each(function() {
		    	$(this).append("  *");
		    });		

		$("[name='mitglied']").change(function() {
// 			if ($(this).prop("checked") == true)
// 				$("#panel_moechte_mitglied_werden").hide();
// 			else
// 				$("#panel_moechte_mitglied_werden").show();
			
			checkBeitraege();
			showSum(false);
		});

// 		$("#wirWollenMitgliedWerden").change(function() {
// 			checkBeitraege();
// 			showSum(false);
// 		});

		$("input[type='checkbox']").checkboxpicker({
			  html: true,
			  offLabel: 'Nein',
			  onLabel: 'Ja',
			  offIconCls: 'glyphicon-thumbs-down',
			  onIconCls: 'glyphicon-thumbs-up'
		});		


		$( "#zahlart_bank, #zahlart_schule" ).change(function() {
			if ($("#zahlart_bank").prop("checked")) {
				$( "#div_iban" ).show();
				$( "#div_kontoinhaber" ).show();
				$( "#hinweis_betrag_bank").show();
				$( "#hinweis_betrag_bar").hide();
			} else {
				$( "#div_iban" ).hide();
				$( "#div_kontoinhaber" ).hide();
				$( "#hinweis_betrag_bank").hide();
				$( "#hinweis_betrag_bar").show();
}
		});

		<?php if (empty($_SESSION['zahlart']) || $_SESSION['zahlart'] == 'schule') {?>
			$( "#div_iban" ).hide();
			$( "#div_kontoinhaber" ).hide();
		<?php }   	?>

		$( "#klasse" ).change(function() {
			var klasseValue = $(this).val();
			var klasse = 0;
			if (klasseValue.toLowerCase().indexOf("gfk") > -1) {
				klasse = "GFK";
			} else if (klasseValue.indexOf("1") > -1) {
				klasse = "1";
			} else if (klasseValue.indexOf("2") > -1) {
				klasse = "2";
			}
			else if (klasseValue.indexOf("3") > -1) {
				klasse = "3";
			}
			else if (klasseValue.indexOf("4") > -1) {
				klasse = "4";
			} 
			if (currentKlasse != klasse) {
				applyFilter(klasse);
			}
		});		

		<?php if ($_SESSION['geprueft'] == 1) { ?>
		$("#Kenntnis").prop("checked",true);
		<?php } ?>		
		$("#fotos").prop("checked",true);
		$("#okmails").prop("checked",true);
		checkBeitraege();
		fnFilterKlassen(currentKlasse);
		$( "#sumAlert" ).hide();
		$( "#bs_alert").hide();
		
		$("#ok").click(function() {
			submit();
		});

		<?php if ($_SESSION['sendConfirmation'] == 'Ja') { ?>
		$('#sendConfirmation').prop('checked', true);
		<?php } ?>
			
		<?php if ($_SESSION['wirWollenMitgliedWerden'] == 'Ja') { ?>
		<!--$('#wirWollenMitgliedWerden').prop('checked', true);-->
		<?php } ?>

		<?php if ($_SESSION['bilder'] == 'Ja') { ?>
		$('#bilder').prop('checked', true);
		<?php } ?>

		<?php foreach ($ags as $ag) {
		if ($_SESSION[$ag['ag_nummer']] == "binDabei") {
			?> $('#input-<?php echo $ag['ag_nummer']?>').prop('checked', true); <?php 
		}}
		?>
		
    });

	function checkJoined(checkboxName, checkBoxSelected) {
		<?php   
			$parts = explode("|", CfgModel::load("joined.ags"));
			foreach ($parts as $part) {
				$tmp = explode(",",$part);
				//Javascript-Array erzeugen
				$part = "[\"" . implode("\",\"", $tmp) . "\"]";
				?> 
				checkJoinedImpl(checkboxName, checkBoxSelected,<?=$part?>);
				<?php
			}
		?>
	}
	

	function checkJoinedImpl(checkboxName, checkBoxSelected, joinedAgs) {

		var idx=-1;
		
		for (var i = 0; i < joinedAgs.length; i++) {
		    if (joinedAgs[i] == checkboxName) {
				idx = i;
		    }
		}

		if (idx > -1) {
			for (var i = 0; i < joinedAgs.length; i++) {
				if (i != idx) {
					if ($("input[name='"+ joinedAgs[i] +"']").prop('checked') != checkBoxSelected) {
						$("input[name='"+ joinedAgs[i] +"']").prop('checked', checkBoxSelected);
						if (checkBoxSelected) {
							toastr.info("Die AG " + joinedAgs[i] + " " + agNamen[joinedAgs[i]] + " wurde ebenfalls ausgewählt!");	
						} else {
							toastr.info("Die AG " + joinedAgs[i] + " " + agNamen[joinedAgs[i]] + " wurde ebenfalls abgewählt!");
						}
					}
				}
			}	
		}
	}
    
	function showSum(showAll) {
		if (showAll || formatted != "0,00") {
			toastr.info("Betrag: " + formatted + "€");		
		}
	}	
	
    
    function submit() {
    	if (agsSelected == 0) {
    		showAlert("Hinweis","Bitte zuerst die AGs ausgewählen!");
    		return;
    	}

    	if ($("[name='name']").val() == "") {
    		showAlert("Hinweis","Bitte noch einen Namen eingeben!");
    		return;
    	}

    	if ($("[name='klasse']").val() == "0") {
    		showAlert("Hinweis","Bitte noch die Klasse auswählen!");
    		return;
    	} 
    	if ($("[name='telefon']").val() == "" && $("[name='mail']").val() == "") {
    		showAlert("Hinweis","Bitte noch eine Telefonnummer oder Mail-Adresse eingeben!");
    		return;
    	}

    	if ($("[name='Kenntnis']").prop('checked') == false) {
    		showAlert("Hinweis","Bitte noch ankreuzen, dass Sie die 'Weiteren Hinweise' zur Kenntnis genommen haben ");
    		return;
    	}

    	/*
    	if ($("#zahlart_bank").prop("checked") && $("[name='iban']").val() == "") {
    		showAlert("Hinweis","Bitte noch die IBAN eingeben!");
    		return;
    	}
    	if ($("#zahlart_bank").prop("checked") && $("[name='kontoinhaber']").val() == "") {
    		showAlert("Hinweis","Bitte noch den Kontoinhaber eingeben!");
    		return;
    	}*/
    	
    	$("#mainForm").submit();
    	
    }
    

    function fnFilterKlassen(klasse) {
    	if (klasse == "GFK" ||  klasse >= 1 && klasse <= 4) {
    		$( "[type='ag_panel']" ).each(function() {
    			  var cb = $( this ).find("[type='checkbox']");
    			
    			  if ($( this ).attr("klasse" + klasse) == "1"
    				 || cb.prop("checked") == true) {
    				  $( this ).show("fast");
    			  } else {
    				  $( this ).hide("fast");
    			  }
    		});
    		 
    		$( "#hintKlasse").show("fast");
    		$("#spanKlasse").html(klasse);
    	} else {
    		$( "[type='ag_row']" ).show("fast");
    		$( "#hintKlasse").hide("fast");
    	}
    }

    
    
    function applyFilter(klasse) {
    	storeSessionData("./","klasseFuerFilter",klasse);
        //storeSessionData("admin/","filterKlassen",1);
    	fnFilterKlassen(klasse);
        currentKlasse = klasse;
        filterKlassen = 1;
    }
    
    function checkBeitraege() {
    	var isMitglied = $('#mitglied_ja').prop("checked") == true;
    	if (isMitglied) {
    		$( "[type='betrag_nicht_mitglied']" ).hide();
    		$( "[type='betrag_mitglied']" ).show();
    	} else {
    		$( "[type='betrag_mitglied']" ).hide();
    		$( "[type='betrag_nicht_mitglied']" ).show();
    	}
    	calcSum();
    }

    function calcSum() {

    	var isMitglied = $('#mitglied_ja').prop("checked") == true;
        	
    	betrag = 0;
    	agsSelected = 0;
    	$( "input[agcheckbox='true']" ).each(function () {
            if (this.checked) {
            	agsSelected++;
                if (isMitglied)
                	betrag = betrag  + ags[$(this).attr("name")];
                else
                	betrag = betrag  + ags1[$(this).attr("name")];
            }
    	});

    	formatted = betrag.formatMoney(2, ',', '.'); 
    	$("[content='betrag']").html(formatted);
    	$("[paypallink='true']").attr("href","<?php echo CfgModel::load("paypallink")?>/" + formatted);
    	
    	$("[name='summe']").val(formatted);
    	
    }

    Number.prototype.formatMoney = function(c, d, t){
    	var n = this, 
    	    c = isNaN(c = Math.abs(c)) ? 2 : c, 
    	    d = d == undefined ? "." : d, 
    	    t = t == undefined ? "," : t, 
    	    s = n < 0 ? "-" : "", 
    	    i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", 
    	    j = (j = i.length) > 3 ? j % 3 : 0;
    	   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
    };
    
    </script>
    <style>
    #bs_alert {
	    position: fixed;
	    top: 0;
	    left: 0;
	    z-index: 100;
	    width: 100%;
	}
</style>
  </head>
  <body>
  
  <div class="container-fluid">
	<div class="row" style="height:100px; background-image: url('images/heading.jpg');background-repeat: repeat-x">
		<div class="col-md-12"> 
				
		</div>
	</div>
	</div>
  	<h1><?php echo $title?></h1>
  <?php if (count($_REQUEST) == 0 && !empty(CfgModel::load("hint.anmeldung"))) {?>
  		
  	<div class="panel panel-default">
		<div class="panel-heading">
	    	<h3 class="panel-title"><?php echo CfgModel::load("hint.anmeldung.title")?></h3>
	  	</div>
		<div class="row">
			<div class="col-md-6">
				<br>
				<div align="center"><img src="images/<?php echo CfgModel::load("hint.anmeldung.image")?>"></div>
		  	</div>
		  	<div class="col-md-6">
		  		<br>
			    <h4 class="text-primary"><?php echo CfgModel::load("hint.anmeldung")?></h4>
			    <br>
			    <a href="anmelden2.php?start=true" type="button" class="btn btn-default">OK, Verstanden. Weiter geht's</a>
		  	</div>
		</div>
	</div>
	  	
  		
  <?php } else { ?>
  	<form action="summary.php" id="mainForm">
    <input type="hidden" name="summe" value="0">
    <input type="hidden" name="html" value="false">
    <input type="hidden" name="bootstrap" value="true">
    <input type="hidden" name="zahlart" value="bank">
    <input type="hidden" name="sendConfirmation" value="Ja">
    
    
	<div class="panel panel-default">
	  <div class="panel-heading">
	    <h3 class="panel-title">Ich möchte folgendes Kind anmelden:</h3>
	  </div>
	  <div class="panel-body">
	    <div class="form-group">
		  	<label for="vorname" pflicht="true">Vorname:</label>
		  	<input type="text" name="vorname" size="50" value="<?php echo $_SESSION['vorname']?>" class="form-control" id="vorname">
		</div>
	    <div class="form-group">
		  	<label for="nachname" pflicht="true">Nachname:</label>
		  	<input type="text" name="nachname" size="50" value="<?php echo $_SESSION['nachname']?>" class="form-control" id="nachname">
		</div>
	    <div class="form-group">
		  	<label for="telefon" pflicht="true">Telefon:</label>
		  	<input type="tel" name="telefon" size="50" value="<?php echo $_SESSION['telefon']?>" class="form-control" id="telefon">
		</div>
		<div class="form-group">
		  	<label for="mail" pflicht="true">Mail:</label>
		  	<input type="email" name="mail" size="50" value="<?php echo $_SESSION['mail']?>" class="form-control" id="mail">
		</div>
		
		<div class="form-group">
		  <label for="klasse" pflicht="true">Klasse:</label>
		  <select class="form-control" id="klasse" name="klasse">
		  		<option value="0">Bitte Auswählen</option>
				<?php 
				$klassenArray = explode(",", CfgModel::load("klassen"));
				foreach ($klassenArray as $k) {
					$selected = "";
					if ($k == $_SESSION['klasse']) {
						$selected = " selected ";
					}
					echo "<option class='option_klasse_$k' value='$k' $selected>$k</option>";
				}
				?>
		  </select>
		</div>
		<div class="btn-group">
			<label>
	  		Mitglied Ja/Nein?
	  		</label>
	  		<div>
	  		Wenn Sie Mitglied werden möchten, können Sie den <a href="/wp-content/uploads/2014/10/antrag-auf-mitgliedschaft.pdf" target="_blank"><b>Antrag</b></a> einfach ausdrucken, ausfüllen und ihrem Kind direkt mit in die Schule geben. In diesem Fall können Sie hier schon den Haken bei 'Ja' machen!<br>
	  		<a href="/wp-content/uploads/2014/10/antrag-auf-mitgliedschaft.pdf" target="_blank"><b>Download Antragsformular</b></a>
	  		
	  		</div>
		  	<div class="funkyradio">
			  	<div class="funkyradio-success">
		            <input type="radio" name="mitglied" id="mitglied_ja" value="ja" <?php addChecked("mitglied","ja",false) ?>/>
		            <label for="mitglied_ja">Ja, Wir sind Mitglied im Förderverein</label>
	        	</div>	
			  	<div class="funkyradio-default">
		            <input type="radio" name="mitglied" id="mitglied_nein" value="nein" <?php addChecked("mitglied","nein",true) ?>/>
		            <label for="mitglied_nein">Nein, Wir sind noch kein Mitglied</label>
	        	</div>	
	        </div>
	  		
	  	</div>
	    <!-- 	
		<div class="form-group">
			<br>
		  	<label for="zahlart">Zahlart:</label>
		  	<div>
		  	Hier könen Sie wählen, ob sie wie bisher über die Schule bezahlen und Erstattungen über die Schule bekommen wollen<br>
		  	oder ob Sie per Überweisung bezahlen und Erstattungen ebenfalls per Überweisung bekommen.
		  	</div>
		  	
		  	<div class="funkyradio">
			  	<div class="funkyradio-default">
		            <input type="radio" name="zahlart" id="zahlart_schule" value="schule" <?php addChecked("zahlart","schule",true) ?>/>
		            <label for="zahlart_schule">Ich bezahle wie bisher auch über die Schule</label>
	        	</div>	
			  	<div class="funkyradio-success">
		            <input type="radio" name="zahlart" id="zahlart_bank" value="bank" <?php addChecked("zahlart","bank") ?>/>
		            <label for="zahlart_bank">Ich überweise das Geld <a href="anmelden_infos_bank.php" target="_blank" type="button" class="btn btn-link">Mehr Infos zum Bezahlen per Überweisung</a></label>
	        	</div>	
	        </div>
	    </div>
	    <div class="form-group" id='div_iban'>
		  	<label for="iban" pflicht="true">IBAN (Für Rückerstattungen):</label>
		  	<input type="text" name="iban" size="50" value="<?php echo $_SESSION['iban']?>" class="form-control" id="iban">
		</div>
	    <div class="form-group" id='div_kontoinhaber'>
		  	<label for="kontoinhaber" pflicht="true">Kontoinhaber (Für Rückerstattungen):</label>
		  	<input type="text" name="kontoinhaber" size="50" value="<?php echo $_SESSION['kontoinhaber']?>" class="form-control" id="kontoinhaber">
		</div>
		-->
		</div>
	</div>		    
    
  	<br/><br/>
  	
<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Mein Kind möchte an folgenden AGs teilnehmen</h3>
  </div>
  <div class="panel-body">  	
<?php 
foreach ($ags as $ag) {

	$tage =  AgModel::getTage($ag['ag_nummer']);

	if ($tage > 3 ) {

		$betragMitglied = formatAsCurrency($ag["betrag_mitglied"]);
		$betragNichtMitglied = formatAsCurrency($ag["betrag_nicht_mitglied"]);
		
		?>
		
		<div class="panel panel-default" type='ag_panel' klassegfk="<?php echo $ag['klasse1']?>" klasse1="<?php echo $ag['klasse1']?>"  klasse2="<?php echo $ag['klasse2']?>"  klasse3="<?php echo $ag['klasse3']?>"  klasse4="<?php echo $ag['klasse4']?>">
		  <div class="panel-heading">
		  <div class="container-fluid">
			<div class="row">
			  <div class="col-xs-12 col-md-3 col-sm-5">
				    <h3 class="panel-title"><?php echo $ag["ag_nummer"] ." " . $ag["name"] ?></h3>
					<div><?php echo getImageLinkAG("true",$ag, "./","max-width:200px;margin-bottom:20px")?></div>
			  </div>
			  <div class="col-xs-2 col-md-2 col-sm-2">
				    <h3 class="panel-title" style="white-space: nowrap;">Klasse <?php echo AgModel::getStringForKlassen($ag,true)?></h3>
				    <div>&nbsp;</div>
			  </div>
			  <div class="col-xs-12 col-md-2 col-sm-5">
				    Termin: <?php echo AgModel::getTerminForStatus($ag, "zusage",false) . " " . AgModel::getUhrzeitForStatus($ag, "zusage")?><br/>
				    <?php if (!empty($ag['termin_ueberbuchung'])) {?>
				    Zuasatztermin: <?php echo AgModel::getTerminForStatus($ag, "termin2",false)?><br/>
				    <?php } ?>
				    Ort: <?php echo $ag["ort"] ?><br/>
				    <span type='betrag_mitglied'>
				    Betrag Mitglied: <?php echo $betragMitglied ?>
				    </span>
				    <span type='betrag_nicht_mitglied'>
				    Betrag Nicht-Mitglied: <?php echo $betragNichtMitglied ?>
				    </span>
			  </div>
			</div>		
			</div>  
		  </div>
		  <div class="panel-body">
			<input agcheckbox="true" id="input-<?php echo $ag['ag_nummer']?>" name="<?php echo $ag['ag_nummer']?>" value="binDabei" type="checkbox" data-reverse>
		  </div>
		</div>		
		
		<?php 
	}
}
?>
</div>
</div>	
  	<br/><br/>

<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Sonstiges</h3>
  </div>
  <div class="panel-body">    	
  	
  	<div class="form-group">
		<label>
  		Dürfen wir von ihrem Kind, während der AG Bilder machen, die zum Zwecke der Öffentlichkeitsarbeit verwendet werden?<br/>
  		Die Namen von Kindern werden grundsätzlich nicht veröffentlicht!
  		</label>
  		<div>
  		<input id="bilder" name="bilder" value="Ja" type="checkbox" data-reverse <?php addChecked("bilder","Ja") ?>>
  		</div>
  	</div>

  	<!-- 
  	<div class="form-group" id="panel_moechte_mitglied_werden">
		<label>
  		Wollen sie Mitglied im Förderverein werden?
  		</label>
  		<div>
  		<input id="wirWollenMitgliedWerden" name="wirWollenMitgliedWerden" value="Ja" type="checkbox" data-reverse <?php addChecked("wirWollenMitgliedWerden","Ja") ?>>
  		</div>
  	</div>
  	 -->
  	
  	<!-- 
  	<div class="form-group">
		<label>
  		Sollen wir Ihnen für die AGs bei denen Ihr Kind angemeldet ist auch eine E-Mail zukommen lassen?
  		</label>
  		<div>
  		<input id="sendConfirmation" name="sendConfirmation" value="Ja" type="checkbox" data-reverse <?php addChecked("sendConfirmation","Ja") ?>>
  		</div>
  	</div> -->
  	  	
    <div class="form-group">
	  	<label for="mithilfe">Ich kann mir vorstellen, bei folgender AG dieses Heftes zu helfen:</label>
	  	<input type="text" name="mithilfeBeiAktuellerAG" size="50" class="form-control" id="mithilfe" <?php addValue("mithilfeBeiAktuellerAG")?>>
	</div>
    <div class="form-group">
	  	<label for="idee">Ich habe eine Idee für ähnliche Aktionen oder möchte bei einer Eltern-Aktion in Zukunft gerne mithelfen:</label>
	  	<input type="text" name="ideeFuerNeueAG" size="50" class="form-control" id="idee" <?php addValue("ideeFuerNeueAG")?>>
	</div>  
	</div></div>
	<div class="panel panel-default">
	  <div class="panel-heading">
	    <h3 class="panel-title">Weitere Hinweise</h3>
	  </div>
	  <div class="panel-body">
	  <ul>
	  <!--  
	  <li id="hinweis_betrag_bar">Ich lege die Teilnahmegebühr/en in Höhe von <span style="font-weight: bold;" content='betrag'>0,00</span><span style="font-weight: bold;">&euro;</span> meiner Anmeldung bei.</li>
	  -->
	  <li id="hinweis_betrag_bank">- Ich überweise die Teilnahmegebühr/en in Höhe von <span style="font-weight: bold;" content='betrag'>0,00</span><span style="font-weight: bold;">&euro;</span> auf das Konto des Fördervereins bzw ich versende das Geld per <img src="images/paypal.png" height="20px"></li>
	  <li>- Als Verwendungszweck gebe ich an: Die Anmeldenummer, Name und Klasse meines Kindes</li>
	  <li>- Der Hin- und Rückweg zu einer AG liegt in der Verantwortung der Eltern! Falls mein Kind nicht selbstständig nach Hause gehen darf, teile ich das dem/der Kursleiter/in mit!</li>
	  <li>- Wir haben die 1€-Zusatzversicherung für unser Kind abgeschlossen!</li>
	  </ul>
		<label  pflicht="true">
  		Ich habe die Hinweise zur Kenntnis genommen
  		</label>
  		<div>
  		<input id="Kenntnis" type="checkbox" name="Kenntnis" value="Ja" data-reverse <?php addChecked("Kenntnis","Ja") ?> >
  		</div>
  		
  		
	</div>		
	</div>
	<div class="alert alert-info" role="alert">
  	Felder mit * sind Pflichtfelder
  	</div>
		  	
  	<button id="ok" type="button" class="btn btn-default">Anmeldung abschicken</button>
  	</form>
<?php } ?>
  	
  </body>
</html>