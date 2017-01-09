<?php
	require_once("ext/phpMailer/PHPMailerAutoload.php");

	define("TEXT_NACH_HAUSE_WEG", "Der Hin- und Rückweg zu einer AG liegt in der Verantwortung der Eltern! Falls mein Kind nicht selbstständig nach Hause gehen darf, teile ich das dem/der Kursleiter/in mit!");
	define("TEXT_1_EURO","Wir haben die 1&euro;-Zusatzversicherung für unser Kind abgeschlossen!");
	define("TEXT_KEINE_FOTOS","Ich bin <b>NICHT</b> einverstanden, dass Fotos, die der Grundschul-Förderverein während seiner Veranstaltungen von meinem Kind macht, zu Zwecken der Öffentlichkeitsarbeit verwendet werden.");
	define("TEXT_FOTOS","Ich bin einverstanden, dass Fotos, die der Grundschul-Förderverein während seiner Veranstaltungen von meinem Kind macht, zu Zwecken der Öffentlichkeitsarbeit verwendet werden.");
	define("TEXT_WILL_MITGLIED_WERDEN","Ich/Wir möchte/n Mitglied im Verein werden. Bitte lassen Sie uns ein Anmeldeformular zusammen mit näheren Informationen zukommen");
	define("TEXT_SEND_CONFIRMATION","Lassen Sie mir für die AGs, an denen mein Kind teilnehmen kann, bitte auch eine Mail zukommen!");
	define("TEXT_MITHILFE","Ich kann mir vorstellen, bei folgender AG dieses Heftes zu helfen:");
	define("TEXT_IDEE","Ich habe eine Idee für ähnliche Aktionen oder möchte bei einer Eltern-Aktion in Zukunft gerne mithelfen:");
	define("TEXT_COMMENT_ZUSATZTERMIN","'<b>Zusatztermin</b>': Der Zusatztermin findet statt, sobald die AG überbucht ist. Ihr Kind wird möglicherweise für diesen Termin eingeplant.");
	define("TEXT_COMMENT_ERSATZTERMIN","'<b>Ersatztermin</b>': Die AG findet an diesem Termin statt, falls die AG an einem der anderen beiden Terminen abgesagt werden muss, z.B. durch Krankheit der AG-Leitung.");
	define("TEXT_COMMENT_UHRZEIT","Die Uhrzeit gilt, falls nicht anders vermerkt, für alle Termine.");

	define("MAIL_RESULT","Message sent!");
	
	function copyRequestValuesToSession($backupArray) {
		
		$backup = array();
		
		foreach ($backupArray as $key) {
			$backup[$key] = $_SESSION[$key];
		}
		
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		
		foreach ($backupArray as $key) {
			$_SESSION[$key] = $backup[$key];
		}
		
		foreach ($_REQUEST as $key => $value) {
			$_SESSION[$key] = $value;
		}
	}
	
	function copySessionValuesToArray($backupArray) {
	
		$target = array();
		$backup = array();
	
		foreach ($backupArray as $key) {
			$backup[$key] = $target[$key];
		}
	
		foreach ($target as $key => $value) {
			unset($target[$key]);
		}
	
		foreach ($backupArray as $key) {
			$target[$key] = $backup[$key];
		}
	
		foreach ($_SESSION as $key => $value) {
			$target[$key] = $value;
		}
		return $target;
	}
	
	
	function addChecked($key, $value, $defaultValue = false) {
		if ($_SESSION[$key] == $value || empty($_SESSION[$key]) && $defaultValue == true) {
			echo " checked='checked' ";
		}
	}
	
	function addValue($key) {
		$value = $_SESSION[$key];
		if (!empty($value)) {
			echo " value='$value' ";
		}
	}
	
	function destroySession() {
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
	}	

	/**
	 * trims text to a space then adds ellipses if desired
	 * @param string $input text to trim
	 * @param int $length in characters to trim to
	 * @param bool $ellipses if ellipses (...) are to be added
	 * @param bool $strip_html if html tags are to be stripped
	 * @return string
	 */
	function trim_text($input, $length, $ellipses = true, $strip_html = true) {
		//strip tags, if desired
		if ($strip_html) {
			$input = strip_tags($input);
		}
	
		//no need to trim, already shorter than trim length
		if (strlen($input) <= $length) {
			return $input;
		}
	
		//find last space within length
		$last_space = strrpos(substr($input, 0, $length), ' ');
		$trimmed_text = substr($input, 0, $last_space);
	
		//add ellipses (...)
		if ($ellipses) {
			$trimmed_text .= '...';
		}
	
		return $trimmed_text;
	}
	
	function addContact($label, $value) {
		echo "<tr><td><b>$label</b></td><td>$value</td></tr>";
	}
	
	function startsWith($haystack, $needle) {
		// search backwards starting from haystack length characters from the end
		return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
	}
	function endsWith($haystack, $needle) {
		// search forward starting from end minus needle length characters
		return $needle === "" || strpos($haystack, $needle, strlen($haystack) - strlen($needle)) !== FALSE;
	}
	
	function formatSQLDate($date, $includeWeekDay = false) {
		if (isset($date)) {
			$timestamp = strtotime( $date );
			if ($includeWeekDay) {
				setlocale(LC_ALL, 'de_DE@euro', 'de_DE', 'de', 'ge');
				return strftime("%a, %d.%m.%Y",$timestamp);
			} else {
				return date( 'd.m.Y', $timestamp );
			}
		} else {
			return "";
		}
	}
	
	function formatNotEmpty($prefix, $suffix, $text) {
		if (!empty($text))
			return $prefix . $text . $suffix;
		return $text;
	}
	
	function str_abbreviate($string = "", $max=10, $appendToAbbreviated = "...") {
		if (strlen($string) > $max) {
			return substr($string, 0, $max - strlen($appendToAbbreviated)).$appendToAbbreviated;
		}
		return $string;
	}
	
	function formatWithCaption($caption, $value) {
		if (!empty($value)) {
			return "<br><br><b>$caption</b> $value";
		}
		return "";
	}
	
	function formatMaxKinder($value) {
		if (!empty($value)) {
			return $value;
		}
		return "unbegrenzt";
	}
	
	function formatKlasse($ag, $key) {
		$value = $ag[$key];
		if (empty($value) || $value == 0) {
			return "<img class='grayOut' id='$key' src='../images/trans.png'>";
		}
		return "<img id='$key' src='../images/trans.png'>";
	}
	function formatKlassen($ag) {
		$a = array();
		
		if ($ag["klasse1"] == 1) {
			array_push($a, "1");
		}
		if ($ag["klasse2"] == 1) {
			array_push($a, "2");
		}
		if ($ag["klasse3"] == 1) {
			array_push($a, "3");
		}
		if ($ag["klasse4"] == 1) {
			array_push($a, "4");
		}
		$result = implode(",", $a);
		
		if ($result == "1,2,3,4")
			return "1-4";
		if ($result == "1,2")
			return "1+2";
		if ($result == "3,4")
			return "3+4";
		if ($result == "2,3,4")
			return "2-4";
		if ($result == "1,2,3")
			return "1-3";
		return $result;
	}
	
	function getImageLink($html, $neueAG, $dirPrefix, $width= "") {
		if ($width == "") {
			if ($html == true) {
				$width="max-width:200px";					
			} else {
				$width="width:100mm";
			}
		}
		$imgLink = "&nbsp;";
		if ($neueAG["bild"] != null) {
			$filename = $dirPrefix."images_tmp/ag" . $neueAG["id"] . "_" . $neueAG["bild_name"];
			file_put_contents($filename, $neueAG["bild"]);
			$imgLink = "<br><img src='$filename' style='$width'/>";
		}
		return $imgLink;
		
	}
	
	function getImageLinkAG($html, $ag, $dirPrefix, $style= "") {
		if ($width == "") {
			if ($html == true) {
				$width="max-width:200px";
			} else {
				$width="width:100mm";
			}
		}
		$imgLink = "&nbsp;";
		if ($ag["bild"] != null) {
			$filename = $dirPrefix."images_tmp/ag" . $ag["id"] . "_" . $ag["bild_name"];
			file_put_contents($filename, $ag["bild"]);
			$imgLink = "<br><img class='img-responsive img-rounded' src='$filename' style='$style'/>";
		}
		return $imgLink;
	}	
	
	function getColored($colored, $class, $content) {
		if ($colored) {
			$content = "<span class='$class'>$content</span>";
		}
		return $content;
	}
	
	function formatCurrentDate() {
		return date( 'd.m.Y');
	}
	
	function formatSQLDateTime($date, $includeWeekDay = false, $betweenDateAndTime=" ") {
		if (isset($date)) {
			$timestamp = strtotime( $date );
			if ($includeWeekDay) {
				setlocale(LC_ALL, 'de_DE@euro', 'de_DE', 'de', 'ge');
				return strftime("%a, %d.%m.%Y",$timestamp). $betweenDateAndTime . date( 'H:i', $timestamp );
			} else {
				return date( 'd.m.Y', $timestamp ) . $betweenDateAndTime . date( 'H:i', $timestamp );
			}
		} else {
			return "";
		}
	}
		
	function changeDayAndMonth($dateGerman) {
		$a = explode(".", $dateGerman);
		$tmp = $a[0];
		$a[0] = $a[1];
		$a[1] = $tmp;
		
		return join(".", $a);
	}
	
	function formatSQLDateForJavascript($date) {
		if (isset($date)) {
			$timestamp = strtotime( $date );
			return intval(date( 'd', $timestamp )) . "." .intval(date( 'm', $timestamp )) .  "." .date( 'Y', $timestamp );
		} else {
			return "";
		}
	}
	
	function formatAsYesNo($value) {
		if ($value == 1)
			return "Ja";
		else if ($value == 0)
			return "Nein";
		
		return $value;
	}
	
	function formatAsMailto($adress) {
		if (!empty($adress)) {
			return "<a href='mailto:$adress'>$adress</a>";	
		} else {
			return "";
		}
	}
	
	function formatZahlart($zahlart) {
		if ($zahlart == 'schule') {
			return "Schule";
		} 
		if ($zahlart == 'bank') {
			return "Überweisung";
		} 
		return $zahlart;
	}
	function formatAsAbfragenLink($prefix, $anmeldeNmmer) {
		if (!empty($anmeldeNmmer)) {
			$url = $prefix . "abfrage.php?nummer=" . $anmeldeNmmer;
			return "<a target='_blank' href='$url'>$anmeldeNmmer</a>";
		} else {
		return "";
		}
	}
	
	function formatAsManageAGLink($prefix, $name, $ag_id) {
		if (!empty($name) && !empty($ag_id)) {
			$url = $prefix . "manageAG.php?ag=" . $ag_id;
			return "<a href='$url'>$name</a>";
		} else {
		return "";
		}
	}
	
	function formatAsDownloadAnmeldungLink($prefix, $anmeldeNmmer) {
		if (!empty($anmeldeNmmer)) {
			$url = $prefix . "downloadAnmeldung.php?nummer=" . $anmeldeNmmer;
			$imageUrl = $prefix . "images/pdf.png";
			return "<a href='$url'><img class='linkIcon' src='$imageUrl'></a>";
		} else {
			return "";
		}
	}
	
	function findAnmeldungsPDF($pdfPath, $anmeldeNummer) {
		if ($handle = opendir($pdfPath)) {
			while (false !== ($entry = readdir($handle))) {
				if (strpos($entry, $anmeldeNummer) !== false
				&& !endsWith($entry, "_mail.pdf")) {
					closedir($handle);
					return $entry;
				}
			}
			closedir($handle);
		}
		return "";
		
	}
	
	function formatAsLinks($linkArray, $between = "") {
		$result = array();
		foreach ($linkArray as $name => $l) {
			if (is_int($name)) {
				$name= $l;
			}
			array_push($result, "<a href='$l' target='_blank'>$name</a>");
		}
		return join($between, $result);
	}
	
	function formatAsCellText($value) {
		if (!empty($value)) {
			return $value;
		} else {
			return "-";
		}
	}	
	
	function formatAsCurrency($value) {
		return number_format ( $value , 2 , "," , ".") . " €";
		//$fmt = new NumberFormatter( 'de_DE', NumberFormatter::CURRENCY );
		//return $fmt->formatCurrency($value, "EUR");
	}
	
	function getFormattedDateTime($ag, $termin) {
		return formatSQLDate($ag[$termin]) . " " . $ag[$termin.'_von']. "-" . $ag[$termin.'_bis'];
	}
	
	function textInTable($text, $widthTextCell = "120mm", $widthImageCell = "60mm", $widthImage = "45mm") {
		echo "<table>
		<tr>
		<td style='width:$widthTextCell'>
		$text
		</td>
		<td style='width:$widthImageCell; vertical-align: top; text-align: right'>
		<img src='../images/logo.png' style='width:$widthImage'/>
		</td>
		</tr>
		</table>";
	}
	
	function textInTableWithTitle($title, $text, $widthTitleCell = "120mm", $widthImageCell = "60mm", $widthImage = "45mm") {
		echo "<table>
		<tr>
		<td style='width:$widthTitleCell;vertical-align: top;'>
		$title
		</td>
		<td style='width:$widthImageCell; vertical-align: top; text-align: right'>
		<img src='../images/logo.png' style='width:$widthImage'/>
		</td>
		</tr>
		</table>
		$text";
	}	
	
	function ics_dateToCal($timestamp) {
		return date('Ymd\THis', $timestamp); //\Z
	}
	// Escapes a string of characters
	function ics_escapeString($string) {
		return preg_replace('/([\,;])/','\\\$1', $string);
	}
	
	
	function getIcs($anmeldung, $addHeader = true, $useMailStatus=true) {
		
		if ($useMailStatus) {
			$field = "status_mail";
		} else {
			$field = "status_anmeldung";
		}
		
		if ($anmeldung[$field] == "zusage") {
			$datestart = strtotime( $anmeldung["termin"] . " " . $anmeldung["termin_von"] );
			$dateend = strtotime( $anmeldung["termin"] . " " . $anmeldung["termin_bis"] );
		}
		else if ($anmeldung[$field] == "termin2") {
			$datestart = strtotime( $anmeldung["termin_ueberbuchung"] . " " . $anmeldung["termin_ueberbuchung_von"] );
			$dateend = strtotime( $anmeldung["termin_ueberbuchung"] . " " . $anmeldung["termin_ueberbuchung_bis"] );
		}
		else if ($anmeldung[$field] == "ersatztermin") {
			$datestart = strtotime( $anmeldung["termin_ersatz"] . " " . $anmeldung["termin_ersatz_von"] );
			$dateend = strtotime( $anmeldung["termin_ersatz"] . " " . $anmeldung["termin_ersatz_bis"] );
		} else {
			return false;
		}
		
		$summary = "ElternAG (" . $anmeldung["schueler_name"] .") - ". $anmeldung["ag_name"];
		$address = $anmeldung["ort"];
		$description = $anmeldung["verantwortlicher_name"] . "\\n" . $anmeldung["verantwortlicher_mail"] . "\\n" . $anmeldung["verantwortlicher_telefon"];
		$filename = $anmeldung["anmelde_nummer"] . "_" .  $anmeldung["id"] .".ics";
		
		if ($addHeader) {
			header('Content-type: text/calendar; charset=utf-8');
			header('Content-Disposition: attachment; filename=' . $filename);
		}
		
		return 
		"BEGIN:VCALENDAR\n".
		"VERSION:2.0\n".
		"PRODID:-//grundschule-aktiv.de/agcal//NONSGML v1.0//EN\n".
		"BEGIN:VEVENT\n".
		"DTEND:". ics_dateToCal($dateend). "\n".
		"UID:".uniqid()."\n".
		"DTSTAMP:".ics_dateToCal(time()) . "\n".
		"LOCATION:".ics_escapeString($address). "\n".
		"DESCRIPTION:".ics_escapeString($description). "\n".
		"SUMMARY:".ics_escapeString($summary). "\n".
		"DTSTART:".ics_dateToCal($datestart). "\n".
		"END:VEVENT\n".
		"END:VCALENDAR\n";
		
	}

	function sendMail($to, $toName, $subject, $content="", $attachmentArray = array(), $attachmentNamesArray = array()) {
		
		error_log("Send Mail to " . $to . " Subject: " . $subject);
		
		for ($i = 0; $i < 3; $i++) {
			try {
				$result = sendMailImpl($to, $toName, $subject, $content, $attachmentArray, $attachmentNamesArray);
				if ($result == MAIL_RESULT) {
					error_log($result);
					return $result;
				}
				error_log("Cannot send Mail. Reason: " . $result);
				if ($i < 2) {
					error_log("Retry");
				} else {
					error_log("Giving up");
					return $result;
				}
			} catch (Exception $e) {
				error_log("Cannot send Mail. Reason: " . $e->getMessage());
				if ($i < 2) {
					error_log("Retry");
				} else {
					error_log("Giving up");
					return "Fehler: " . $e->getMessage();
				}
			}
			sleep(2);
		}
		
	}
	
	function sendMailImpl($to, $toName, $subject, $content, $attachmentArray = array(), $attachmentNamesArray = array()) {

		$mailserver= CfgModel::load("mail.server");
		$smtpPort=CfgModel::load("smtp.port");
		$user=CfgModel::load("mail.user");
		$pass=CfgModel::load("mail.password");

		$imapSentItems=CfgModel::load("imap.sent.items");

		$from = CfgModel::load("mail.from.adress");
		$fromName = CfgModel::load("mail.from.name");
		$replyToMail = CfgModel::load("mail.reply.adress");
		$replyToName = CfgModel::load("mail.from.name");
		
		
		if (!startsWith($content, "<html>")) {
			$content = "<html><head><meta charset='utf-8'/></head><body>" . $content . "</body></html>";
		}
		
		$contentPlainText = str_replace("<br>", "\n", $content);
		$contentPlainText = strip_tags($contentPlainText);
		
		//Create a new PHPMailer instance
		$mail = new PHPMailer;

		$mail->CharSet = 'UTF-8';
		//Tell PHPMailer to use SMTP
		
		$mail->Sendmail = CfgModel::load("sendmail.path");
		
		if (file_exists($mail->Sendmail)) {
			$mail->isSendmail();
		} else {
			$mail->isSMTP();
			//Enable SMTP debugging
			// 0 = off (for production use)
			// 1 = client messages
			// 2 = client and server messages
			// 3 = as 2, plus more information about the initial connection.
			// 4: as 3, plus even lower-level information, very verbose.
			// You don't need to use levels above 2 unless you're having trouble connecting at all - it will just make output more verbose and more difficult to read.
			$mail->SMTPDebug = 0;
			//Ask for HTML-friendly debug output
			$mail->Debugoutput = 'echo';
			
			$mail->Debugoutput = function($str, $level) {
				$msg = "PHPMAILER [$level] message: $str";
				error_log($msg);
			};
		}
		
		//Set the hostname of the mail server
		$mail->Host = $mailserver;
		//Set the SMTP port number - likely to be 25, 465 or 587
		$mail->Port = $smtpPort;
		//Whether to use SMTP authentication
		$mail->SMTPAuth = true;
		//Username to use for SMTP authentication
		$mail->Username = $user;
		//Password to use for SMTP authentication
		$mail->Password = $pass;
		//Set who the message is to be sent from
		$mail->setFrom($from, $fromName);
		//Set an alternative reply-to address
		$mail->addReplyTo($replyToMail, $replyToNames);
		//Set who the message is to be sent to
		$mail->addAddress($to, $toName);
		$mail->addCC($cc, $ccName);
		//Set the subject line
		$mail->Subject = $subject;
		//Read an HTML message body from an external file, convert referenced images to embedded,
		//convert HTML into a basic plain-text alternative body
		$mail->msgHTML($content);
		//Replace the plain text body with one created manually
		$mail->AltBody = $contentPlainText;
		//Attach an image file
		
		if (count($attachmentArray) != count($attachmentNamesArray)) {
			foreach ($attachmentArray as $attachment) {
				$mail->addAttachment($attachment);
			}
		} else {
			$count = 0;
			while ($count < count($attachmentArray)) {
				$mail->addAttachment($attachmentArray[$count],$attachmentNamesArray[$count]);
				$count++;
			}
		}
		
		
		//send the message, check for errors
    	if (!$mail->send()) {
    		return "Mailer Error: " . $mail->ErrorInfo;
    	} else {
    		$mbox = $imapSentItems;
    		$imapStream = imap_open($mbox, $mail->Username, $mail->Password);
    		
    		if (imap_last_error() != false) {
    			return "Versendete Mail konnte nicht abgelegt werden.\nMailbox konnte nicht geöffnet werden: " . imap_last_error();
    		}
  			imap_append($imapStream, $mbox, $mail->getSentMIMEMessage(), "\\Seen");	
    	    		if (imap_last_error() != false) {
    			return "Versendete Mail konnte nicht abgelegt werden.\nMail konnte der Mailbox nicht hinzugefügt werden: " . imap_last_error();
    	    }
  			imap_close($imapStream);
    	    if (imap_last_error() != false) {
    			return "Versendete Mail konnte nicht abgelegt werden.\nMailbox konnte nicht geschlossen werden: " . imap_last_error();
    	    }
  			return MAIL_RESULT;
   		}
		
	}
	
	function renderLastSearchHandling($javascriptTable, $tableId) {
		session_start();
		
		$ajaxCall = "//ajax_call_start\n$.ajax({ url: url, error: function(jqXHR, textStatus, errorThrown) {\nconsole.log(jqXHR.status + ' (' + errorThrown + ')');} });\n//ajax_call_end\n";
		
		$term = $_SESSION[$tableId];
		if (!empty($term)) {
			echo $javascriptTable . ".search('".$term."').draw();";
		}
		$orderCol = $_SESSION[$tableId . "_order_col"];
		$orderDirection = $_SESSION[$tableId . "_order_direction"];
		
		if (!empty($term)) {
			echo $javascriptTable . ".search('".$term."').draw();\n";
		}
		
		if (!empty($orderCol) && !empty($orderDirection)) {
			echo $javascriptTable . ".order([$orderCol,'$orderDirection']).draw();\n";
		}
		
		echo $javascriptTable . ".on( 'search.dt', function (event, settings) {\n
				if ($('.dataTables_filter input').size() > 0) {	\n
					var search = " . $javascriptTable . ".search();\n
					var value = $('.dataTables_filter input').val();\n
					var url = 'storeSessionData.php?key=" . $tableId . "&value=' + value;\n
					$ajaxCall\n
				}
			});\n
			";
		
		echo "\n\n";
		
		echo $javascriptTable . ".on( 'order.dt', function (event, settings) {\n
			var order = " . $javascriptTable . ".order();\n
			if (order.length > 0) {\n
				var url = 'storeSessionData.php?key=" . $tableId . "_order_col&value=' + order[0][0];\n
				$ajaxCall
				url = 'storeSessionData.php?key=" . $tableId . "_order_direction&value=' + order[0][1];\n
				$ajaxCall
			}\n
			\n});\n";
		
	}
	
	function renderImageTable($imagePathPrefix, $arrayWithSteps, $createAnmeldenLink = false, $createPDFLink = false, $createSaveLink=false) {
		
		
		$steps = array(1,2,3,4,5,6);
		
		$desc = array();
		if ($createAnmeldenLink) {
			$desc[1] = '<a id="pdfLink" href="anmelden.php">Formular ausfüllen</a>';
		} else {
			$desc[1] = "Formular ausfüllen";
		}
		
		if ($createSaveLink) {
			$desc[2] = 'Eingaben überprüfen und <a id="insertAnmeldung" href="insertAnmeldung.php">Anmeldung abschicken</a>';
		} else {
			$desc[2] = "Eingaben überprüfen und Anmeldung abschicken";
		}
		
		
		if ($createPDFLink) {
			$desc[3] = '<a id="pdfLink" href="pdf.php">PDF erzeugen</a>';
		} else {
			$desc[3] = "PDF erzeugen";
		}
		
		$desc[4] = "PDF ausdrucken und das erste Blatt unterschreiben. <b>Falls Sie keinen Drucker besitzen</b> reicht es auch, wenn sie die Anmeldenummer, Name+Klasse des Kindes und den Gesamtbetrag auf einen Zettel schreiben und den dann mit in die Schule geben.";
				$desc[5] = "Das erste Blatt zusammen mit dem Geld in einen Umschlag stecken<br>Das zweite Blatt ist für Sie bestimmt und muss nicht mit abgegeben werden.<br><br> (Außen auf den Umschlag 'Anmeldung Eltern-AGs und den Namen Ihres Kindes)";
		$desc[6] = "Geben Sie den Umschlag ihrem Kind mit in die Schule<br><br><b>Sobald das Geld zusammen mit der Anmeldung bei uns angekommen ist, wird die Anmeldung geprüft und ihr Kind ist angemeldet!</b>";
		
		echo "<table>";
		echo "<tr style='height:60px'>";
		foreach ($steps as $step) {
			$class = "";
			$hakenVisible=";display:none";
			if (!in_array($step, $arrayWithSteps)) {
				$class = "disabled";
				$hakenVisible = ""; 
			}
			echo '<td style="width:150px; text-align:center; vertical-align:top">
					<div style="width: 150px; position: relative">
	                    <img id="stepTitle'.$step.'" class="'.$class.'" src="'.$imagePathPrefix.'step'.$step.'.png" width="80" height="50" style="position: absolute; top: 0; left: 35px; z-index: 1">
	                    <img id="haken'.$step.'" src="'.$imagePathPrefix.'haken_green.png" width="32" height="32" style="opacity: 1; position: absolute; top: 19; left: 9px; z-index: 10 '.$hakenVisible.'">
					</div>
			</td>';
		}
		echo "</tr>";
		echo "<tr>";
		foreach ($steps as $step) {
			$class = "";
			if (!in_array($step, $arrayWithSteps)) {
				$class = "disabled"; 
			}
			echo "<td id='step$step' style='width:150px; vertical-align:top; padding-left:10px; padding-right:10px' class='$class'>".$desc[$step]."</td>";	
		}
		echo "</tr>";
		echo "</table>";
		
	}
	
	function renderImageTableBootstrap($imagePathPrefix, $arrayWithSteps, $createAnmeldenLink = false, $createPDFLink = false, $createSaveLink=false) {
	
	
		$steps = array(1,2,3,4,5,6);
	
		$desc = array();
		if ($createAnmeldenLink) {
			$desc[1] = '<a class="btn btn-info" role="button" id="pdfLink" href="anmelden.php2">Formular ausfüllen</a>';
		} else {
			$desc[1] = "Formular ausfüllen";
		}
	
		if ($createSaveLink) {
			$desc[2] = 'Eingaben überprüfen und <a class="btn btn-info" role="button" id="insertAnmeldung" href="insertAnmeldung.php">Anmeldung abschicken</a>';
		} else {
			$desc[2] = "Eingaben überprüfen und Anmeldung abschicken";
		}
		
	
		if ($createPDFLink) {
			$desc[3] = '<a class="btn btn-info" role="button" type="pdfLink" href="javascript:pdfClicked()">PDF erzeugen</a><br>(Nur erforderlich, wenn Sie die Anmeldung ausdrucken oder speichern wollen)';
		} else {
			$desc[3] = "PDF erzeugen<br>(Nur erforderlich, wenn Sie die Anmeldung ausdrucken oder speichern wollen)";
		}
	
		$desc[4] = "PDF ausdrucken und das erste Blatt unterschreiben. <b>Falls Sie keinen Drucker besitzen</b> reicht es auch, wenn sie die Anmeldenummer, Name+Klasse des Kindes und den Gesamtbetrag auf einen Zettel schreiben und den dann mit in die Schule geben.";
		$desc[5] = "Das erste Blatt zusammen mit dem Geld in einen Umschlag stecken<br>Das zweite Blatt ist für Sie bestimmt und muss nicht mit abgegeben werden.<br><br> (Außen auf den Umschlag 'Anmeldung Eltern-AGs und den Namen Ihres Kindes)";
		$desc[6] = "Geben Sie den Umschlag ihrem Kind mit in die Schule<br><br><b>Sobald das Geld zusammen mit der Anmeldung bei uns angekommen ist, wird die Anmeldung geprüft und ihr Kind ist angemeldet!</b>";

		echo "	<script type='text/javascript'>
				$(document).ready(function() {
					showHideStepTable(); //allgemein.js
					window.onresize = function(event) {
						showHideStepTable();
					};		
				});
				</script>
		
				<table class='table table-bordered table-striped' id='stepTableSmall'><tbody>";
		renderStepsBootstrap($desc, array_slice($steps,0,2),$arrayWithSteps,$imagePathPrefix);
		renderStepsBootstrap($desc, array_slice($steps,2,2),$arrayWithSteps,$imagePathPrefix);
		renderStepsBootstrap($desc, array_slice($steps,4,2),$arrayWithSteps,$imagePathPrefix);
		echo "</tbody></table><br>";
		
		echo "<table class='table table-bordered' id='stepTableLarge' style='display:none'><tbody>";
		renderStepsBootstrap($desc, $steps,$arrayWithSteps,$imagePathPrefix);
		echo "</tbody></table>";
		
	}
	

	function renderImageTableBootstrapBank($imagePathPrefix, $arrayWithSteps, $createAnmeldenLink = false, $createPDFLink = false, $createSaveLink=false, $verwendungszweck = "&lt;Name und Klasse des Schülers&gt; &lt;Anmeldenummer&gt;") {
	
		$imagePathPrefix = $imagePathPrefix . "bank_";
	
		$steps = array(1,2,3,4);
	
		$desc = array();
		if ($createAnmeldenLink) {
			$desc[1] = '<a class="btn btn-info" role="button" id="pdfLink" href="anmelden.php2">Formular ausfüllen</a>';
		} else {
			$desc[1] = "Formular ausfüllen";
		}
	
		if ($createSaveLink) {
			$desc[2] = 'Eingaben überprüfen und <a class="btn btn-info" role="button" id="insertAnmeldung" href="insertAnmeldung.php">Anmeldung abschicken</a>';
		} else {
			$desc[2] = "Eingaben überprüfen und Anmeldung abschicken";
		}
	
	
		if ($createPDFLink) {
			$desc[3] = '<a class="btn btn-info" role="button" type="pdfLink" href="javascript:pdfClicked()">PDF erzeugen</a><br>(Nur erforderlich, wenn Sie die Anmeldung ausdrucken oder speichern wollen)';
		} else {
			$desc[3] = "PDF erzeugen<br>(Nur erforderlich, wenn Sie die Anmeldung ausdrucken oder speichern wollen)";
		}
	
		$desc[4] = "Das Geld überweisen an:<br>Föderverein Grundschule<br>".CfgModel::load("bankverbindung")."<br><b>Verwendungszweck:</b><br> $verwendungszweck <br><br>Sobald das Geld bei uns angekommen ist, wird die Anmeldung geprüft und ihr Kind ist angemeldet!";
	
		echo "	<script type='text/javascript'>
				$(document).ready(function() {
					showHideStepTable(); //allgemein.js
					window.onresize = function(event) {
						showHideStepTable();
					};
				});
				</script>
	
				<table class='table table-bordered table-striped' id='stepTableSmall'><tbody>";
		renderStepsBootstrap($desc, array_slice($steps,0,2),$arrayWithSteps,$imagePathPrefix);
		renderStepsBootstrap($desc, array_slice($steps,2,2),$arrayWithSteps,$imagePathPrefix);
		echo "</tbody></table><br>";
	
		echo "<table class='table table-bordered' id='stepTableLarge' style='display:none'><tbody>";
		renderStepsBootstrap($desc, $steps,$arrayWithSteps,$imagePathPrefix);
		echo "</tbody></table>";
	
	}	
		
	function renderStepsBootstrap( $desc, $steps,$arrayWithSteps,$imagePathPrefix) {
		
		
		echo "<tr style='height:60px'>";
		foreach ($steps as $step) {
			$class = "";
			$hakenVisible=";display:none";
			if (!in_array($step, $arrayWithSteps)) {
				$class = "disabled";
				$hakenVisible = "";
			}
			echo '<td style="width:150px; text-align:center; vertical-align:top">
					<div style="width: 150px; position: relative">
	                    <img type="stepTitle'.$step.'" class="'.$class.'" src="'.$imagePathPrefix.'step'.$step.'.png" width="80" height="50" style="position: absolute; top: 0; left: 35px; z-index: 1">
	                    <img type="haken'.$step.'" src="'.$imagePathPrefix.'haken_green.png" width="32" height="32" style="opacity: 1; position: absolute; top: 19; left: 9px; z-index: 10 '.$hakenVisible.'">
					</div>
			</td>';
		}
		echo "</tr>";
		echo "<tr>";
		foreach ($steps as $step) {
			$class = "";
			if (!in_array($step, $arrayWithSteps)) {
				$class = "disabled";
			}
			echo "<td type='step$step' style='width:150px; vertical-align:top; padding-left:10px; padding-right:10px' class='$class'>".$desc[$step]."</td>";
		}
		echo "</tr>";
				
		
	}
	
	
	function getFingerPrintFromSession($exclude) {
	
		$include = array();
		foreach ($_SESSION as $key => $value) {
			if (!in_array($key, $exclude)) {
				array_push($include, $key);
			}
		}
		
		asort($include);
		$keyValues = array();
		foreach ($include as $key) {
			$keyValues[$key] = $_SESSION[$key];
		}
		$fp = print_r($keyValues,true);
		return $fp;
	}
	
	function euroStringToFloat($value) {
		if (empty($value)) {
			return "0.0";
		} else {
			$value = str_replace("€", "", $value);
			$value = str_replace(",", ".", $value);
			$value = str_replace(" ", "", $value);
			return $value;
		}
	}
	
	function stringToInt($value) {
		if (empty($value)) {
			return null;
		} else {
			return intval($value);
		}
	}
	
	function toIso($var) {
		$enc = mb_detect_encoding($var, 'UTF-16,UTF-8, ISO-8859-1', true);
		$result = mb_convert_encoding($var, 'ISO-8859-1',$enc);
		return $result;
	}
	
	function toZipCP850($var) {
		$enc = mb_detect_encoding($var, 'UTF-16,UTF-8, ISO-8859-1', true);
		$result = mb_convert_encoding($var, 'CP850',$enc);
		return $result;
	}
	
	function toUtf($var) {
		$enc = mb_detect_encoding($var, 'UTF-16,UTF-8, ISO-8859-1', true);
		$result = mb_convert_encoding($var, 'UTF-8',$enc);
		return $result;
	}
	
	/**
	 * @param unknown $timeString sould be in Format hh:mm
	 */
	function checkTime($timeString) {
		$a = explode(":", $timeString);
		if (count($a) == 2) {
			$h = intval($a[0]);
			$m = intval($a[1]);
			return $m < 60 && $m >= 0 && $h > 8 && $h < 22;
		}
		return false;
	}
	
	/**
	 * @param unknown $phoneString
	 */
	function checkPhone($phoneString) {
		
		try {
			$phoneString = str_replace("+", "", $phoneString);
			$phoneString = str_replace("_", "", $phoneString);
			$phoneString = str_replace("/", "", $phoneString);
			$phoneString = str_replace("/", "", $phoneString);
			$phoneString = str_replace(")", "", $phoneString);
			$phoneString = str_replace("(", "", $phoneString);
			return is_numeric($phoneString);
		} catch (Exception $e) {
			error_log('Exception abgefangen: ',  $e->getMessage());
		}
	}
	
	function addHiddenField($var, $name) {
		if (!empty($var)) {
			echo "<input type='hidden' name='$name' value='$var' />";
		}
	}
	
	function getBaseUrl() {
		$ex = explode("/", $_SERVER[PHP_SELF]);
		array_pop($ex);//Die Seite
		$admin = array_pop($ex);//Den admin-Teil?
		if ($admin != "admin") {
			array_push($ex, $admin);
		}
		$url = implode("/", $ex);
		$url = 'http://' . $_SERVER['HTTP_HOST'] .$url . "/";
		return $url;
	}
	
	
?>