<?php
	include '../db.php';
	require_once("../funktionen.php");
	require_once('../ext/html2pdf/html2pdf.class.php');

	ini_set("log_errors", 1);     /* Logging "an" schalten */
	ini_set("error_log", "errorlog.txt");     /* Log-Datei angeben */

	//foreach ($_SERVER as $key => $value) {
	//	echo "$key = $value<br>	";
	//}
	
	
/**
 * This example shows making an SMTP connection with authentication.
 */
//SMTP needs accurate times, and the PHP time zone MUST be set
//This should be done in your php.ini, but this is how to do it if you don't have access to that

//Create a new PHPMailer instance
$mail = new PHPMailer(true);
//Tell PHPMailer to use SMTP
if (file_exists($mail->Sendmail)) {
	echo "Using sendMail<br>";
	$mail->isSendmail();
} else {
	echo "Using SMTP<br>";
	$mail->isSMTP();
}

//Enable SMTP debugging
// 0 = off (for production use)
// 1 = client messages
// 2 = client and server messages
$mail->SMTPDebug = 4;
//Ask for HTML-friendly debug output
$mail->Debugoutput = 'echo';
//Set the hostname of the mail server
$mail->Host = "smtp.strato.de";
//Set the SMTP port number - likely to be 25, 465 or 587
$mail->Port = 25;
//Whether to use SMTP authentication
$mail->SMTPAuth = true;
//Username to use for SMTP authentication
$mail->Username = "ag.verwaltung@grundschule-aktiv.de";
//Password to use for SMTP authentication
$mail->Password = "ag.verwaltung,,13";
//Set who the message is to be sent from
$mail->setFrom('ag.verwaltung@grundschule-aktiv.de', 'AG-Verwaltung');
//Set who the message is to be sent to
$mail->addAddress('Michael.Bauschert@web.de', 'Michael Bauschert');
//Set the subject line
$mail->Subject = 'PHPMailer SMTP test';
//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
$mail->msgHTML("<html>Test</html>");
//Replace the plain text body with one created manually
$mail->AltBody = 'This is a plain-text message body';

$mail->Debugoutput = function($str, $level) {
	$msg = "PHPMAILER [$level] message: $str";
	error_log($msg);
	echo "$msg<br>";
};

//Attach an image file
$mail->addAttachment('../images/fehlerteufel.jpg');

//send the message, check for errors
if (!$mail->send()) {
    echo "Mailer Error: " . $mail->ErrorInfo;
} else {
    echo "Message sent!";
}	
	

	
?>