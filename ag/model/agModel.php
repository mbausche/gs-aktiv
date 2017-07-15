<?php

//use RedBeanPHP;


/**
 * Klasse für den Datenzugriff
 */

class AgInsertResult {
	public $created = false;
	public $name = "";
	public $klasse= "";
	public $mail = "";
	public $anmeldeNummer = "";
	
	function __construct($name, $mail, $klasse, $anmeldeNummer) {
		$this->name = $name;
		$this->mail = $mail;
		$this->klasse = $klasse;
		$this->anmeldeNummer = $anmeldeNummer;
	}
}

class AgModel{

	/**
	 * Nimmt die Anmeldedaten aus der Session und speichert sie in der Datenbank 
	 * @return AgInsertResult
	 */
	public static function insertAnmeldung($prefixAnmeldung) {
		
		
		$result = new AgInsertResult(trim($_SESSION['vorname']) . " ". trim($_SESSION['nachname']),$_SESSION['mail'],$_SESSION['klasse'],$_SESSION["AnmeldungsId"]);
		$ags = R::findAll( 'ag' );
		
		$fingerprint = getFingerPrintFromSession(array("fingerprint","klasseFuerFilter","filterKlassen","AnmeldungsId","insert_result_id","insert_result_created","insert_result_mail","insert_result_klasse","insert_result_name","insert_filename"));
		$oldFingerprint = $_SESSION['fingerprint'];

		error_log("New Fingerprint: $fingerprint");
		error_log("Session: " . print_r($_SESSION,true));
		
		if ($fingerprint != $oldFingerprint) {
			
			$_SESSION['fingerprint'] = $fingerprint;
			
			$anmeldung = R::dispense( 'anmeldung' );
			$anmeldung['name'] = trim($_SESSION['vorname']) . " ". trim($_SESSION['nachname']);
			$anmeldung['klasse'] = $_SESSION['klasse'];
			$anmeldung['telefon'] = $_SESSION['telefon'];
			$anmeldung['mail'] = $_SESSION['mail'];
			$anmeldung['idee_fuer_neue_ag'] = $_SESSION['ideeFuerNeueAG'];
			$anmeldung['mithilfe_bei_aktueller_ag'] = $_SESSION['mithilfeBeiAktuellerAG'];
			$anmeldung['fotos_ok'] = $_SESSION['keineBilder'] != "Ja";
			$anmeldung['ist_mitglied'] = $_SESSION['mitglied'] == 'ja';
			$anmeldung['moechte_mitglied_werden'] = $_SESSION['wirWollenMitgliedWerden'] == 'Ja';
			
			$anmeldung['send_confirmation'] = $_SESSION['sendConfirmation'] == 'Ja';
			$anmeldung['zahlart'] = $_SESSION['zahlart'];
			if (empty($anmeldung['zahlart'])) {
				$anmeldung['zahlart'] = "schule";
			}
			
			if ($anmeldung['zahlart'] == 'bank') {
				$anmeldung['iban'] = $_SESSION['iban'];
				$anmeldung['kontoinhaber'] = $_SESSION['kontoinhaber'];
			}
			
			$summe = $_SESSION['summe'];
			$summe = str_replace(",", ".", $summe);
			
			$anmeldung['betrag'] = $summe;
			$anmeldung['direkt_eingabe'] = $_SESSION['direkt_eingabe'] == "1" ? 1 : 0;
			$anmeldung['geprueft'] = $_SESSION['geprueft'] == "1" ? 1 : 0;
			
			$uuid = $prefixAnmeldung . AgModel::generate_random_letters(6);
			$anmeldungen = R::findAll('anmeldung',' anmelde_nummer = ?', [ $uuid]);
			while (count($anmeldungen) > 0) {
				$uuid = $prefixAnmeldung . AgModel::generate_random_letters(6);
				$anmeldungen = R::findAll('anmeldung',' anmelde_nummer = ?', [ $uuid]);
			}
			
			$anmeldung['anmelde_nummer'] =  $uuid;
			$idAnmeldung = R::store( $anmeldung );
			
			foreach ($ags as $ag) {
				if ($_SESSION[$ag['ag_nummer']] ==  "binDabei") {
					$fuerAG = R::dispense('anmeldungfuerag' );
					$fuerAG['id_anmeldung'] = $idAnmeldung;
					$fuerAG['id_ag'] = $ag['id'];
					$fuerAG['status_anmeldung'] = 'nicht_geprueft';
					$fuerAG['status_mail'] = 'nicht_geprueft';
					R::store( $fuerAG );
				}
			}
			$_SESSION["AnmeldungsId"] = $anmeldung['anmelde_nummer'];
			$result->anmeldeNummer = $anmeldung['anmelde_nummer'];
			$result->created = true;
		} 
		
		return $result; 
		 
		
	}
	
	public static function generate_random_letters($length) {
		$random = '';
		for ($i = 0; $i < $length; $i++) {
			$random .= chr(rand(ord('A'), ord('Z')));
		}
		return $random;
	}
	
	
	public static function wipe() {
		R::wipe( 'ag' );
	}
	
	public static function insertAG($zeile, $testMail=false) {
		
		$zeile = mb_convert_encoding($zeile, 'UTF-8',mb_detect_encoding($zeile, 'UTF-8, ISO-8859-1', true));
		
		$namen = array();
		$namen['Alina Wagner'] = array('Vorname' => "Alina", "Name" => "Wagner", "Mail" => "wagner.alina@gmx.de","Telefon" => "07051/8064374");
		$namen['Andrea Manegold'] = array('Vorname' => "Andrea", "Name" => "Manegold", "Mail" => "manegold@kabelbw.de","Telefon" => "07051/700087");
		$namen['Annerose Bacher'] = array('Vorname' => "Annerose", "Name" => "Bacher", "Mail" => "a.bacher@grundschule-aktiv.de","Telefon" => "07053/6550");
		$namen['Antonia Hauser'] = array('Vorname' => "Antonia", "Name" => "Hauser", "Mail" => "a.hauser@grundschule-aktiv.de","Telefon" => "07051/938584");
		$namen['Birgit'] = array('Vorname' => "Birgit", "Name" => "Meyer", "Mail" => "birgit-meyer2002@gmx.net","Telefon" => "???");
		$namen['Carmen Bodio'] = array('Vorname' => "Carmen", "Name" => "Bodio", "Mail" => "c.bodio@grundschule-aktiv.de","Telefon" => "07051/1687631");
		$namen['Claudia Driesch'] = array('Vorname' => "Claudia", "Name" => "Driesch", "Mail" => "cdriesch@gmx.de","Telefon" => "07051 9300-29");
		$namen['Daniel'] = array('Vorname' => "Daniel", "Name" => "Rank", "Mail" => "sherzog77@web.de","Telefon" => "???");
		$namen['Eva'] = array('Vorname' => "Eva", "Name" => "Gallo", "Mail" => "evagallo@gmx.net","Telefon" => "07051-934719");
		$namen['Heike'] = array('Vorname' => "Heike", "Name" => "Koch", "Mail" => "???","Telefon" => "???");
		$namen['Jochen Saboynik'] = array('Vorname' => "Jochen", "Name" => "Saboynik", "Mail" => "jochen-saboynik@t-online.de", "Telefon" => "07051-930427");
		$namen['Katrin'] = array('Vorname' => "Katrin", "Name" => "Eissler", "Mail" => "k.gockeler@gmx.de", "Telefon" => "07053/304941");
		$namen['Michael Bauschert'] = array('Vorname' => "Michi", "Name" => "Bauschert", "Mail" => "m.bauschert@grundschule-aktiv.de", "Telefon" => "07051/930600");
		$namen['Nadine Mann'] = array('Vorname' => "Nadine", "Name" => "Mann", "Mail" => "n.mann@grundschule-aktiv.de", "Telefon" => "07051/930987");
		$namen['Silvia'] = array('Vorname' => "Silvia", "Name" => "Zimmer", "Mail" => "s.zimmer@grundschule-aktiv.de", "Telefon" => "???");
		$namen['Sonja Bily'] = array('Vorname' => "Sonja", "Name" => "Bily", "Mail" => "s.bily@grundschule-aktiv.de", "Telefon" => "07051/1679136");
		$namen['Sonia Förster'] = array('Vorname' => "Sonia", "Name" => "Förster", "Mail" => "alexanderfoerster@freenet.de", "Telefon" => "07051/934887");
		$namen['Wendy Bott'] = array('Vorname' => "Wendy", "Name" => "Bott", "Mail" => "w.bott@grundschule-aktiv.de", "Telefon" => "07051-7262");
		$namen['Andreas Bott'] = array('Vorname' => "Andreas", "Name" => "Bott", "Mail" => "andreas-bott@freenet.de", "Telefon" => "07051-7262");
		$namen['Jasmin Hartmann'] = array('Vorname' => "Jasmin", "Name" => "Hartmann", "Mail" => "hartmann.jasmin@gmx.de", "Telefon" => "0176/20774092");
		$namen['Sarah Niethammer'] = array('Vorname' => "Sarah", "Name" => "Niethammer", "Mail" => "sarah.niethammer@raibacalw.de", "Telefon" => "07051/92488-26");
		$namen['Simone Wengert'] = array('Vorname' => "Simone", "Name" => "Wengert", "Mail" => "maildermone@googlemail.com", "Telefon" => "0172-7646375");
		$namen['Stefanie Berkemer'] = array('Vorname' => "Stefanie", "Name" => "Berkemer", "Mail" => "scberkemer@kabelbw.de", "Telefon" => "07051/937074");
		$namen['Silvia Schütz'] = array('Vorname' => "Silvia", "Name" => "Schütz", "Mail" => "schütz_silvia@web.de", "Telefon" => "07053-967396");
		$namen['Marco Gackenheimer'] = array('Vorname' => "Marco", "Name" => "Gackenheimer", "Mail" => "marco4motion@gmx.de", "Telefon" => "0716-96513148");
		$namen['Mara Rathfelder'] = array('Vorname' => "Mara", "Name" => "Rathfelder", "Mail" => "atze1603@kabekbw.de", "Telefon" => "07051-8060188");
		
		$spalten = explode(";", $zeile);
		if (count($spalten) == 21) {
			$agInDB = R::dispense( 'ag' );

			$agInDB['ag_nummer'] = CfgModel::load("prefixAgNummer").str_pad($spalten[1], 2, '0', STR_PAD_LEFT);
			$agInDB['name'] = $spalten[2];

			$agInDB['klasse1'] = empty($spalten[3]) ? 0 : 1;
			$agInDB['klasse2'] = empty($spalten[4]) ? 0 : 1;
			$agInDB['klasse3'] = empty($spalten[5]) ? 0 : 1;
			$agInDB['klasse4'] = empty($spalten[6]) ? 0 : 1;
				
			$agInDB['termin'] = DateTime::createFromFormat("d.m.Y",$spalten[7]);
			$agInDB['termin_von'] = $spalten[8];
			$agInDB['termin_bis'] = $spalten[9];
			
			if (!empty($spalten[10])) {
				$agInDB['termin_ueberbuchung'] = DateTime::createFromFormat("d.m.Y",$spalten[10]);
				$agInDB['termin_ueberbuchung_von'] = $spalten[11];
				$agInDB['termin_ueberbuchung_bis'] = $spalten[12];
			}
				
			if (!empty($spalten[13])) {
				$agInDB['termin_ersatz'] = DateTime::createFromFormat("d.m.Y",$spalten[13]);
				$agInDB['termin_ersatz_von'] = $spalten[14];
				$agInDB['termin_ersatz_bis'] = $spalten[15];
			}
			
			$agInDB['ort'] = $spalten[16];
			$agInDB['betrag_mitglied'] = euroStringToFloat($spalten[17]);
			$agInDB['betrag_nicht_mitglied'] = euroStringToFloat($spalten[18]);
				
			$agInDB['max_kinder'] = stringToInt($spalten[19]);
			
			$person = $namen[trim($spalten[20])];
			if ($person == null) {
				die("Für Name " . $spalten[20]. " wurde keine person definiert!");
			}
			
			if ($testMail == "true") {
				$person['Mail'] = "eltern" . rand(1, 4). "@grundschule-aktiv.de";
			}
			
			$agInDB['verantwortlicher_name'] = $person['Vorname'] . " " . $person['Name'];
			$agInDB['verantwortlicher_mail'] = $person['Mail'];
			$agInDB['verantwortlicher_telefon'] = $person['Telefon'];
				
			$id = R::store( $agInDB );
			return $id;
		} else {
			return -1;
		}
	}

	public static function insertAGFromNeueAG($neueAG) {
	
			$fields = array("ag_nummer",
							"verantwortlicher_name"
							,"verantwortlicher_mail"
							,"verantwortlicher_telefon"
							,"text_ausschreibung"
							,"wichtige_infos"
							,"termin"
							,"termin_von"
							,"termin_bis"
							,"termin_ersatz"
							,"termin_ueberbuchung"
							,"max_kinder"
							,"anzahl_helfer"
							,"klasse1"
							,"klasse2"
							,"klasse3"
							,"klasse4"
							,"betrag_mitglied"
							,"betrag_nicht_mitglied"
							,"ort"
							,"ausserdem"
							,"bild_name"
							,"bild_mime_type"
							,"bild"
							,"klasse4"
							,"betrag_mitglied"
			);
		
		
			$agInDB = R::dispense( 'ag' );
	
			$agInDB["name"] = $neueAG["ag_name"];
			foreach ($fields as $field) {
				$agInDB[$field] = $neueAG[$field];
			}

			if (isset($neueAG["termin_ueberbuchung"]))
			{
				$agInDB["termin_ueberbuchung_von"] = $neueAG["termin_von"];
				$agInDB["termin_ueberbuchung_bis"] = $neueAG["termin_bis"];
			}			
			
			if (isset($neueAG["termin_ersatz"]))
			{
				$agInDB["termin_ersatz_von"] = $neueAG["termin_von"];
				$agInDB["termin_ersatz_bis"] = $neueAG["termin_bis"];
			}			
			
			$id = R::store( $agInDB );
			return $id;
	}
	
	

	
	public static function countAnmeldungen($geprueft = null) {
		if ($geprueft === null) {
			$all = R::findAll( 'anmeldung' );
		} else {
			$all = R::findAll( 'anmeldung', 'geprueft = ?', [$geprueft] );
		}
		return count($all);
	}
	
	public static function countAnmeldungenOnline($online = true) {
		$direktEingabe = $online ? 0 : 1;
		$all = R::findAll( 'anmeldung', 'direkt_eingabe = ?', [$direktEingabe] );
		return count($all);
	}
	
	public static function countAnmeldungenZahlart($zahlart) {
		$all = R::findAll( 'anmeldung', 'zahlart = ?', [$zahlart] );
		return count($all);
	}
	
	public static function countAnmeldungenPaypal() {
		$all = R::findAll( 'anmeldung', 'mail_paypal is not null',[]);
		return count($all);
	}
	
	public static function countAGs() {
		$all = R::findAll( 'ag' );
		return count($all);
	}
	
	public static function countAnmeldungenForSchueler($nameSchueler,  $status) {
		if (isset($status)) {
			$all = R::findAll('anmeldungen_geprueft',' schueler_name = ? and status_anmeldung = ?', [ $nameSchueler, $status ]);
		} else {
			$all = R::findAll('anmeldungen_geprueft',' schueler_name = ?', [ $nameSchueler]);
		}
		return count($all);
	}	
	
	public static function countAnmeldungenFuerAGs($status) {
		$all = R::findAll('anmeldungen_geprueft','status_anmeldung = ? ', [ $status ]);
		return count($all);
	}
	
	public static function countAnmeldungenForAg($agNummer="", $status="", $onlyWithOtherInMail = false, $onlyThoseWantingConfirmation = false) {
		
		if ($agNummer == "") {
			$all = R::findAll('anmeldungen_geprueft');
		} else if ($status == "") {
			$all = R::findAll('anmeldungen_geprueft',' ag_nummer = ? ', [ $agNummer ]);
		} else {
			$suffix = "";
			if ($onlyThoseWantingConfirmation) {
				$suffix = "and send_confirmation = 1";
			}
			
			if ($onlyWithOtherInMail) {
				$all = R::findAll('anmeldungen_geprueft',' ag_nummer = ? and status_anmeldung = ? and (status_mail <> ? or status_mail is NULL)' . $suffix, [ $agNummer, $status, $status ]);
			} else {
				$all = R::findAll('anmeldungen_geprueft',' ag_nummer = ? and status_anmeldung = ? ' . $suffix, [ $agNummer, $status ]);
			}
		}
		
		return count($all);
	}	

	public static function countAnmeldungenForAgNoMail($agNummer, $status) {
		$all = R::findAll('anmeldungen_geprueft',' ag_nummer = ? and status_anmeldung = ? and (mail = ""  or mail is null)', [ $agNummer, $status ]);
		return count($all);
	}
	
	
	
	public static function getAnmeldungenForAg($agNummer, $orderClause = "order by datum_geprueft") {
		$all = R::findAll('anmeldungen_geprueft',' ag_nummer = ? ' . $orderClause, [ $agNummer]);
		return $all;
	}	

	public static function getAnmeldungenForAgWithStatus($agNummer, $state) {
		$all = R::findAll('anmeldungen_geprueft',' ag_nummer = ? and status_anmeldung = ?', [ $agNummer, $state]);
		return $all;
	}	
	
	public static function getAnmeldungsDaten($id) {
		$daten = R::findOne('anmeldungen_geprueft',' id = ?', [ $id]);
		return $daten;
	}
	
	public static function getAg($agNummer) {
		return R::findOne('ag',' ag_nummer = ? ', [ $agNummer ]);
	}
	
	public static function getAgs() {
		return R::findAll('ag');
	}
	
	
	/**
	 * Gibt einen Array zurück, der als Key das Jahr und als Werte die Liste mit allen AGs enthält 
	 * @return 
	 */
	public static function getAgsAllYears() {
		$agsByYear = array();
		
		AgModel::addForYear(date("Y"), R::getAll('select * from ag order by name'),$agsByYear);

		$prefixe = explode(",", CfgModel::load("prefix.vorjahre"));
		foreach ($prefixe as $prefixAndYear) {
			$a = explode("=", $prefixAndYear);
			$prefix = $a[0];
			$year = $a[1];
			$ags = R::getAll('select * from '.$prefix.'ag order by name');
			AgModel::addForYear($year, $ags,$agsByYear);
		}
		
		return $agsByYear;
	}
	
	public static function addForYear($year, $ags, &$agsByYear) {
		$a = $agsByYear[$year];
		if ($a == NULL) {
			$a = array();
		}
		foreach ($ags as $ag) {
			$name = $ag["name"];
			$name = str_replace(" I ", " ", $name);
			$name = str_replace(" II ", " ", $name);
			
			while (endsWith($name, "I")) {
				$name = substr($name, 0, strlen($name)-1);
			}
			$name = trim($name);
			if (!array_key_exists($name, $a)) {
				$a[$name] = $name;
			}
		}
		$agsByYear[$year] = $a;
	}
	
	
	public static function getAgsByAGNummer() {
		return R::getAll('select * from ag order by ag_nummer');
	}
	
	public static function getAgsByDatum() {
		return R::getAll('select * from ag order by termin');
	}
	
	public static function resetStates($id) {
		$anmeldungFuerAG = R::load( 'anmeldungfuerag', $id );
		$anmeldungFuerAG['status_anmeldung'] = "nicht_geprueft";
		$anmeldungFuerAG['status_mail'] = "nicht_geprueft";
		R::store( $anmeldungFuerAG );
	}
	
	public static function changeState($id, $newType) {
		$anmeldungFuerAG = R::load( 'anmeldungfuerag', $id );
		$anmeldungFuerAG['status_anmeldung'] = $newType;

		/**
		if ($newType == "zusage") {
			$anmeldung = R::load( 'anmeldung', $anmeldungFuerAG['id_anmeldung'] );
			if ($anmeldung['send_confirmation'] == 0) {
				$anmeldungFuerAG['status_mail'] = $newType;
			}
		}
		 */

		R::store( $anmeldungFuerAG );
	}

	public static function changeStateMail($id, $newType) {
		$anmeldungFuerAG = R::load( 'anmeldungfuerag', $id );
		$anmeldungFuerAG['status_mail'] = $newType;
		R::store( $anmeldungFuerAG );
	}
	
	public static function changeStateForAG($agNummer) {
		$ags = AgModel::getAnmeldungenForAg($agNummer);
		foreach ($ags as $ag) {
			$anmeldungFuerAG = R::load( 'anmeldungfuerag', $ag['id'] );
			$anmeldungFuerAG['status_mail'] = $anmeldungFuerAG['status_anmeldung']; 
			R::store( $anmeldungFuerAG );
		}
	}
	
	public static function getAndereKinder($agNummer, $statusMail, $name) {
		
		$all = R::findAll('anmeldungen_geprueft',' ag_nummer = ? and status_mail = ? and schueler_name <> ? order by schueler_name' . $suffix, [ $agNummer, $statusMail, $name ]);
		
		$result = array();
		foreach ($all as $a) {
			$result[] = $a["schueler_name"];
		}

		$result = implode(", ", $result);
		return $result;
	}
	
	public static function appendComment($agNummer, $comment = "", $comment_privat="") {
		$ag = AgModel::getAg($agNummer);
		$ag = R::load( 'ag', $ag['id'] );
		if (!empty($comment)) {
			$ag["kommentar"] = $ag["kommentar"] . "\n" . $comment;
		}
		
		if (!empty($comment_privat)) {
			$ag["kommentar_privat"] = $ag["kommentar_privat"] . "\n" .$comment_privat;
		}
		R::store( $ag );
	}
	
	public static function replaceComment($agNummer, $comment = "", $comment_privat="") {
		$ag = AgModel::getAg($agNummer);
		$ag = R::load( 'ag', $ag['id'] );
		if (!empty($comment)) {
			$ag["kommentar"] = $comment;
		}
		
		if (!empty($comment_privat)) {
			$ag["kommentar_privat"] = $comment_privat;
		}
		R::store( $ag );
	}
	
	public static function updateMax($agNummer, $max) {
		$ag = AgModel::getAg($agNummer);
		$ag = R::load( 'ag', $ag['id'] );
		if (empty($max)) {
			$ag["max_kinder"] = null;
		} else {
			$ag["max_kinder"] = $max;
		}
		R::store( $ag );
	}
	
	
	public static function changeVerantwortlichkeit($agNummer, $name, $mail, $telefon) {
		$ag = AgModel::getAg($agNummer);
		$ag = R::load( 'ag', $ag['id'] );
		
		$ag["verantwortlicher_name"] = $name;
		$ag["verantwortlicher_mail"] = $mail;
		$ag["verantwortlicher_telefon"] = $telefon;
		
		R::store( $ag );
	}
	
	public static function changeDateAndTime($agNummer, $request, $fieldname) {
		
		
		$ag = AgModel::getAg($agNummer);
		$ag = R::load( 'ag', $ag['id'] );
		
		$value = $request[$fieldname];
		if (empty($value)) {
			$ag[$fieldname] = null;
		} else {
			$date = DateTime::createFromFormat('d.m.Y', $value);
			$ag[$fieldname] = $date;
		}

		$key = $fieldname . "_von";
		$time = $request[$key];
		if (empty($time)) {
			$ag[$key] = null;
		} else {
			$ag[$key] = $time;
		}
		
		$key = $fieldname . "_bis";
		$time = $request[$key];
		if (empty($time)) {
			$ag[$key] = null;
		} else {
			$ag[$key] = $time;
		}
		
		R::store( $ag );
	}
	
	/**
	 * $anmeldung Datensatz aus anmeldungen_geprueft
	 * $fieldname status_anmeldung oder status_mail (=default)
	 * 
	 * true wenn der Status zusage,termin2 oder ersatztermin ist
	 */
	
	public static function istGebucht($anmeldung, $fieldname = "status_mail") {
		return $anmeldung[$fieldname] == "zusage" || $anmeldung[$fieldname] == "termin2" || $anmeldung[$fieldname] == "ersatztermin";
	}
	
	public static function getStatusText($dbStatus) {
		$typen = array("nicht_geprueft" => "Nicht bestätigt","zusage" => "Zugesagt","termin2" => "Umgebucht auf Termin 2","absage" => "Abgesagt", "ersatztermin" => "Umgebucht auf Ersatztermin");
		return $typen[$dbStatus];
	}
	
	public static function getActionText($dbStatus) {
		$typen = array("nicht_geprueft" => "-","zusage" => "Zusagen","termin2" => "Umbuchen auf Termin 2","absage" => "Absagen", "ersatztermin" => "Umgebuchen auf Ersatztermin");
		return $typen[$dbStatus];
	}
	
	public static function getAnmeldungenForSchulleitung() {
		return R::getAll("SELECT klasse, schueler_name, count(*) as anzahl FROM `anmeldungen_geprueft` group by schueler_name, klasse order by klasse, schueler_name");
	}

	public static function alleZusagen($agNummer) {
		$liste =  AgModel::getAnmeldungenForAg($agNummer);
		foreach ($liste as $ag) {
			if ($ag['status_anmeldung'] == 'nicht_geprueft') {
				AgModel::changeState($ag['id'], 'zusage');
			}
		}
	}
	
	public static function formatSQLDate($date, $includeWeekday=true, $includeDate=true) {
		if (isset($date)) {
			$tag[0] = "So";
			$tag[1] = "Mo";
			$tag[2] = "Di";
			$tag[3] = "Mi";
			$tag[4] = "Do";
			$tag[5] = "Fr";
			$tag[6] = "Sa";
 
			$timestamp = strtotime( $date );
			$tagnummer = date("w", $timestamp); // Tag ermitteln
			
			$middle = $includeWeekday && $includeDate ? ", " : ""; 
			return  ($includeWeekday ? $tag[$tagnummer] : "") . $middle . ($includeDate ? date( 'd.m.Y', $timestamp ) : "");
		} else {
			return "";
		}
	}
	
	public static function formatSQLDateForCompare($date) {
		if (isset($date)) {
			$timestamp = strtotime( $date );
			return date( 'Ymd', $timestamp );
		} else {
			return "";
		}
	}
	
	public static function formatSQLDateIcal($date) {
		if (isset($date)) {
			$timestamp = strtotime( $date );
			return  date( 'Ymd', $timestamp );
		} else {
			return "";
		}
	}	
	
	public static function formatSQLTimeIcal($time) {
		$values = explode(":", $time);
		$values[0] = str_pad($values[0]-1,2,"0",STR_PAD_LEFT);
		$values[1] = str_pad($values[1],2,"0",STR_PAD_LEFT);
		$values[] = "00";
		return implode("", $values);
	}	
	
	
	
	public static function getTerminForStatus($ag, $status, $includeTime = true, $defaultValue = "") {
		if ($status == 'zusage') {
			if (!empty($ag['termin']))
				return AgModel::formatSQLDate($ag['termin']) . ($includeTime ?  " von " . $ag['termin_von'] . " - " . $ag['termin_bis'] : "");
		}
		else if ($status == 'termin2') {
			if (!empty($ag['termin_ueberbuchung']))
				return AgModel::formatSQLDate($ag['termin_ueberbuchung']) . ($includeTime ?  " von " . $ag['termin_ueberbuchung_von'] . " - " . $ag['termin_ueberbuchung_bis'] : "");
		}
		else if ($status == 'ersatztermin') {
			if (!empty($ag['termin_ersatz']))
				return AgModel::formatSQLDate($ag['termin_ersatz']) . ($includeTime ?  " von " . $ag['termin_ersatz_von'] . " - " . $ag['termin_ersatz_bis'] : "");
		} 
		return $defaultValue;
	}
	
	public static function getTerminCompareStringForStatus($ag, $status, $defaultValue = "") {
		if ($status == 'zusage') {
			if (!empty($ag['termin']))
				return AgModel::formatSQLDateForCompare($ag['termin']);
		}
		else if ($status == 'termin2') {
			if (!empty($ag['termin_ueberbuchung']))
				return AgModel::formatSQLDateForCompare($ag['termin_ueberbuchung']);
		}
		else if ($status == 'ersatztermin') {
			if (!empty($ag['termin_ersatz']))
				return AgModel::formatSQLDateForCompare($ag['termin_ersatz']);
		}
		return $defaultValue;
	}	
	
	public static function getUhrzeitForStatus($ag, $status, $defaultValue = "") {
		if ($status == 'zusage') {
			if (!empty($ag['termin']))
				return $ag['termin_von'] . " - " . $ag['termin_bis'];
		}
		else if ($status == 'termin2') {
			if (!empty($ag['termin_ueberbuchung']))
				return $ag['termin_ueberbuchung_von'] . " - " . $ag['termin_ueberbuchung_bis'];
		}
		else if ($status == 'ersatztermin') {
			if (!empty($ag['termin_ersatz']))
				return $ag['termin_ersatz_von'] . " - " . $ag['termin_ersatz_bis'];
		} 
		return $defaultValue;
	}
	
	public static function getIcalDateForStatus($ag, $status, $endTime = false) {
		if ($status == 'zusage') {
			if (!empty($ag['termin']))
				return  AgModel::formatSQLDateIcal($ag['termin']) . "T". ($endTime ?  AgModel::formatSQLTimeIcal($ag['termin_bis']) : AgModel::formatSQLTimeIcal($ag['termin_von'])) . "Z";
		}
		else if ($status == 'termin2') {
			if (!empty($ag['termin_ueberbuchung']))
				return  AgModel::formatSQLDateIcal($ag['termin_ueberbuchung']) . "T". ($endTime ?  AgModel::formatSQLTimeIcal($ag['termin_ueberbuchung_bis']) : AgModel::formatSQLTimeIcal($ag['termin_ueberbuchung_von'])). "Z";
		}
		else if ($status == 'ersatztermin') {
			if (!empty($ag['termin_ersatz']))
				return  AgModel::formatSQLDateIcal($ag['termin_ersatz']) . "T". ($endTime ?  AgModel::formatSQLTimeIcal($ag['termin_ersatz_bis']) : AgModel::formatSQLTimeIcal($ag['termin_ersatz_von'])). "Z";
		}
		return "";
	}
	
	public static function getTerminForStatusAsArray($ag, $status) {
		if ($status == 'zusage') {
			if (!empty($ag['termin']))
				return array(
						AgModel::formatSQLDate($ag['termin'],true,false),
						AgModel::formatSQLDate($ag['termin'],false,true),
						$ag['termin_von'],
						"-",
						$ag['termin_bis']);
		}
		else if ($status == 'termin2') {
			if (!empty($ag['termin_ueberbuchung']))
				return array(
						AgModel::formatSQLDate($ag['termin_ueberbuchung'],true,false),
						AgModel::formatSQLDate($ag['termin_ueberbuchung'],false,true),
						$ag['termin_ueberbuchung_von'],
						"-",
						$ag['termin_ueberbuchung_bis']);
		}
		else if ($status == 'ersatztermin') {
			if (!empty($ag['termin_ersatz']))
				return array(
						AgModel::formatSQLDate($ag['termin_ersatz'],true,false),
						AgModel::formatSQLDate($ag['termin_ersatz'],false,true),
						$ag['termin_ersatz_von'],
						"-",
						$ag['termin_ersatz_bis']);
		} 
		return array("","","","","");
	}
	
	public static function getAnmeldungenNichtGeprueft() {
		$all = R::getAll('select * from anmeldung where geprueft = 0 or geprueft is NULL order by id asc');
		return $all;
	}
	
	public static function getDateOfAnmeldungsEingang($id) {
		$anmeldung = R::findOne('anmeldung',' id = ? ', [ $id ]);
		return $anmeldung['datum_eingang']; 
	}
	
	public static function getDateOfAnmeldungsPruefung($id) {
		$anmeldung = R::findOne('anmeldung',' id = ? ', [ $id ]);
		return $anmeldung['datum_geprueft']; 
	}
	
	public static function getAgsOfAnmeldungAsHtml($id) {
		$anmeldungen = R::findAll('anmeldungen_ungeprueft',' id_anmeldung = ? ', [ $id ]);
		$result = "";
		foreach ($anmeldungen as $a) {
			if (!empty($result)) {
				$result = $result . ", ";
			}
			$result = $result . $a['ag_nummer'] . " " . $a['ag_name'];
		}
		return $result; 
	}
	
	public static function setGeprueft($id, $iban="", $kontoinhaber="", $mailPaypal="") {
		$anmeldung = R::load( 'anmeldung',$id );
		$anmeldung['geprueft'] = 1;
		$anmeldung['datum_geprueft'] = date("Y-m-d H:i:s");
		if (!empty($iban) && !empty($kontoinhaber)) {
			$anmeldung['iban'] = $iban;
			$anmeldung['kontoinhaber'] = $kontoinhaber;
		}
		
		$anmeldung['mail_paypal'] = $mailPaypal;
		
		R::store( $anmeldung );
	}
	
	public static function getAnmeldedaten($anmelde_nummer) {
		$anmeldungen = R::findAll('anmeldungen_ungeprueft',' anmelde_nummer = ? ', [ $anmelde_nummer ]);
		if (count($anmeldungen) == 0) {
			$anmeldungen = R::findAll('anmeldungen_geprueft',' anmelde_nummer = ? ', [ $anmelde_nummer ]);
		}
		return $anmeldungen;
	}
		
	public static function getAnmeldung($anmelde_nummer) {
		return R::findOne('anmeldung',' anmelde_nummer = ? ', [ $anmelde_nummer ]);
	}
	
	public static function getAndereAnmeldungen($name, $klasse, $anmelde_nummer_nicht_diese) {
		$sql = "SELECT * FROM anmeldung WHERE anmelde_nummer != '$anmelde_nummer_nicht_diese' and name = '$name' and Klasse = '$klasse' order by datum_eingang";
		return R::getAll($sql);
	}
	
	public static function getAnmeldungen($geprueft) {
		return R::findAll('anmeldung',' geprueft = ? ', [ $geprueft ]);
	}
	
	public static function getAnmeldungenByMail($mail) {
		return R::findAll('anmeldung',' mail = ? ', [ $mail ]);
	}
	
	public static function getAnmeldungById($id) {
		return R::findOne('anmeldung',' id = ? ', [ $id ]);
	}
	
	public static function updateAnmeldungById($id, $istMitglied, $geprueft, $iban="", $kontoinhaber="", $mailPaypal="") {
		$anmeldung = R::load('anmeldung',$id);
		
		//Beträge aktualisieren, wenn sich der Mitgliedstatus ändert
		if ($anmeldung["ist_mitglied"] != $istMitglied) {
			$summe = 0;
			$list = R::findAll('anmeldungfuerag' ,'id_anmeldung = ?',[$id]);
			foreach ($list as $entry) {
				$ag = R::load('ag',$entry['id_ag']);
				if ($istMitglied == 0) {
					$summe = $summe + $ag["betrag_nicht_mitglied"];
				} else {
					$summe = $summe + $ag["betrag_mitglied"];
				}
			}
			$anmeldung["betrag"] = $summe;
		}
		
		if (!empty($iban) && !empty($kontoinhaber)) {
			$anmeldung['iban'] = $iban;
			$anmeldung['kontoinhaber'] = $kontoinhaber;
		}
		
		$anmeldung['mail_paypal'] = $mailPaypal;
		
		$anmeldung["ist_mitglied"] = $istMitglied;
		$anmeldung["geprueft"] = $geprueft;
		
		R::store($anmeldung);
		return $anmeldung;
	}
	
	
	public static function getTop10() {
		$all = R::getAll('select ag_name, count(*) as anzahl, ag_nummer from anmeldungen_geprueft group by ag_name order by anzahl desc limit 10');
		return $all;
	}
	
	public static function getStartetBald() {
		$all = R::getAll('select name, DATEDIFF(termin, now()) as tage, ag_nummer from ag where DATEDIFF(termin, now()) >= 0 order by termin asc limit 10');
		return $all;
	}
	
	public static function getTage($agNummer, $terminTyp = "termin") {
		$row = R::getRow('select DATEDIFF('.$terminTyp.', now()) as tage from ag where ag_nummer = ?',[$agNummer]);
		error_log("row: " . print_r($row,true));
		return $row['tage'];
	}
		
	public static function getIdeen() {
		$all = R::getAll("select * from anmeldung where geprueft = 1 and idee_fuer_neue_ag != '' and idee_fuer_neue_ag is not null and idee_fuer_neue_ag_ok != 1");
		return $all;
	}
	
	public static function setIdeeOK($id) {
		$anmeldung = R::load( 'anmeldung',$id );
		$anmeldung["idee_fuer_neue_ag_ok"] = 1;
		R::store( $anmeldung );
	}	
	
	public static function getMithilfe() {
		$all = R::getAll("select * from anmeldung where geprueft = 1 and mithilfe_bei_aktueller_ag != '' and mithilfe_bei_aktueller_ag is not null and mithilfe_bei_aktueller_ag_ok != 1");
		return $all;
	}
	
	public static function setMithilfeOK($id) {
		$anmeldung = R::load( 'anmeldung',$id );
		$anmeldung["mithilfe_bei_aktueller_ag_ok"] = 1;
		R::store( $anmeldung );
	}	
	
	public static function saveAnmeldung($id,$name, $klasse, $mail, $telefon, $mitglied = false, $fotosOk = false, $zahlart = "schule", $iban="", $kontoinhaber = "", $mailPaypal) {
		$anmeldung = R::load( 'anmeldung',$id );
		$anmeldung["name"] = $name;
		$anmeldung["klasse"] = $klasse;
		$anmeldung["mail"] = $mail;
		$anmeldung["telefon"] = $telefon;
		$anmeldung["ist_mitglied"] = $mitglied ? "1" : "0";
		$anmeldung["fotos_ok"] = $fotosOk ? "1" : "0";
		$anmeldung["zahlart"] = $zahlart;
		$anmeldung["iban"] = $iban;
		$anmeldung["kontoinhaber"] = $kontoinhaber;
		$anmeldung["mail_paypal"] = $mailPaypal;
		R::store( $anmeldung );
	}
		
	
	public static function loescheAnmeldung($id) {
		$anmeldung = R::load( 'anmeldung',$id );
		R::trash( $anmeldung );
		$list = R::findAll('anmeldungfuerag' ,'id_anmeldung = ?',[$id]);
		foreach ($list as $entry) {
			$obj = R::load( 'anmeldungfuerag',$entry['id'] );
			R::trash( $obj );
		}
		
	}
	
	
	public static function copyTables($prefix, $suffix) {
		$tables = array("ag","anmeldung","anmeldungfuerag");
		foreach ($tables as $oldTable) {
			$newTable = $prefix . $oldTable. $suffix;
			R::getAll("CREATE TABLE " . $newTable . " LIKE " . $oldTable);
			R::getAll("INSERT " . $newTable . " SELECT * FROM " . $oldTable);
		}
	}

	public static function emptyTables() {
		$tables = array("ag","anmeldung","anmeldungfuerag");
		foreach ($tables as $oldTable) {
			R::wipe($oldTable);
		}
	}
	
	public static function getSummen($agNummer) {

		$ag = AgModel::getAg($agNummer);
		
		$beitragMitglied = $ag["betrag_mitglied"];
		$beitragNichtMitglied = $ag["betrag_nicht_mitglied"];
		
		$anmeldungenNichtmitglieder = count(R::findAll('anmeldungen_geprueft',' ag_nummer = ? and status_mail != ? and status_mail != ? and ist_mitglied = ? ', [ $agNummer,'nicht_geprueft', 'absage', 0]));		
		$anmeldungenMitglieder = count(R::findAll('anmeldungen_geprueft',' ag_nummer = ? and status_mail != ? and status_mail != ? and ist_mitglied = ? ', [ $agNummer,'nicht_geprueft', 'absage', 1]));

		$absagenNichtmitglieder = count(R::findAll('anmeldungen_geprueft',' ag_nummer = ? and status_mail = ? and ist_mitglied = ? ', [ $agNummer,'absage', 0]));
		$absagenMitglieder = count(R::findAll('anmeldungen_geprueft',' ag_nummer = ? and status_mail = ? and ist_mitglied = ? ', [ $agNummer,'absage', 1]));
		
		$result = array($anmeldungenMitglieder * $beitragMitglied
				, $anmeldungenNichtmitglieder * $beitragNichtMitglied
				, $absagenMitglieder * $beitragMitglied
				, $absagenNichtmitglieder * $beitragNichtMitglied
		);
		
		return $result;
	}
	
	public static function getBetragOfAnmeldung($agId, $betragMitglied) {
		if ($betragMitglied == true) {
			$col = "betrag_mitglied";
		} else {
			$col = "betrag_nicht_mitglied";
		}
		$sql = "select sum($col) as summe from anmeldungen_ungeprueft where id_anmeldung = " . $agId;
		$row = R::getRow($sql);
		return $row["summe"];
	}

	public static function getPersonenMitMehrAlsEinerAnmeldung() {
		$result = array();
		$entries = R::getAll("SELECT name, klasse, count(*) as anzahl FROM anmeldung where geprueft = 0 group by name, klasse order by anzahl desc");
		foreach ($entries as $entry) {
			if ($entry["anzahl"] > 1) {
				array_push($result, $entry);
			}
		}
		return $result;
	}
	
	public static function getMailAdresses($typ = "Veranstalter", $alleJahre = false) {
		
		$result = array();
		
		AgModel::getMailAdressesImpl($typ, $result);
		
		if ($alleJahre) {
			$prefixe = explode(",", CfgModel::load("prefix.vorjahre"));
			foreach ($prefixe as $prefixAndYear) {
				$prefix = explode("=", $prefixAndYear)[0];
				AgModel::getMailAdressesImpl($typ, $result, $prefix);
			}
		}
		
		return array_values($result);
		
	}
	
	
	public static function getMailAdressesImpl($typ = "Veranstalter", &$result, $prefix="") {
		$sql1 = "SELECT distinct verantwortlicher_name as name, verantwortlicher_mail as mail from ".$prefix."ag order by verantwortlicher_name";
		$sql2 = "SELECT distinct name, mail from ".$prefix."anmeldung where mail is not null and mail <> '' order by name";
		if ($typ == "Veranstalter")
			$tmp = R::getAll($sql1);
		else if ($typ == "Teilnehmer")
			$tmp = R::getAll($sql2);
		else if ($typ == "Alle") {
			$sql = " Select name, mail from (($sql1) UNION distinct ($sql2)) as t1 order by name";
			error_log($sql);
			$tmp = R::getAll($sql);
		}
				
		foreach ($tmp as $entry) {
			if (!(array_key_exists($entry["mail"], $result))) {
				$result[$entry["mail"]] = $entry;
			}
		}
				
	}
	
	public static function getStringForKlassen($ag, $colored=false) {
		
		$result = array();
		if ($ag["klasse1"] == 1) {
			array_push($result, getColored($colored,"option_klasse_1a","1"));
		}
		if ($ag["klasse2"] == 1) {
			array_push($result, getColored($colored,"option_klasse_2a","2"));
		}
		if ($ag["klasse3"] == 1) {
			array_push($result, getColored($colored,"option_klasse_3a","3"));
		}
		if ($ag["klasse4"] == 1) {
			array_push($result, getColored($colored,"option_klasse_4a","4"));
		}
		return join(",", $result);
	}
	
	public static function getAlleTermineMitAg($agNummer) {
		$sql = "(SELECT distinct name as ag_name, termin, '1. Termin' as typ, termin_von as von, termin_bis as bis FROM ag where ag_nummer != '$agNummer') UNION distinct (SELECT distinct name as ag_name, termin_ueberbuchung as termin, '2. Termin' as typ, termin_ueberbuchung_von as von, termin_ueberbuchung_bis as bis FROM ag where ag_nummer != '$agNummer') UNION distinct (SELECT distinct name as 	ag_name, termin_ersatz as termin, 'Ersatztermin' as typ, termin_ersatz_von as von, termin_ersatz_bis as bis FROM ag where ag_nummer != '$agNummer')";
		$result = R::getAll($sql);
		return $result;
	}
	
	public static function getAnmeldungenByKlasse($klasse="") {
		if (empty($klasse)) {
			return false;
		}
		$entries = R::getAll("SELECT * FROM `anmeldung` JOIN STATUS ON anmeldung.name = status.name WHERE `klasse` LIKE '%$klasse%' ORDER BY anmeldung.NAME");
		return $entries;
	}
}
/*

ALTER TABLE `ag` ADD `text_ausschreibung` TEXT NULL AFTER `verantwortlicher_telefon`, ADD `wichtige_infos` TEXT NULL AFTER `text_ausschreibung`;
ALTER TABLE `ag` ADD `anzahl_helfer` INT NULL AFTER `max_kinder`;
ALTER TABLE `ag` CHANGE `betrag_mitglied` `betrag_mitglied` FLOAT NULL DEFAULT '0', CHANGE `betrag_nicht_mitglied` `betrag_nicht_mitglied` FLOAT NULL DEFAULT '0';
ALTER TABLE `ag` CHANGE `ort` `ort` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_german2_ci NULL DEFAULT NULL;
ALTER TABLE `ag` ADD `ausserdem` TEXT NULL AFTER `ort`, ADD `bild_name` VARCHAR(100) NULL AFTER `ausserdem`, ADD `bild_mime_type` VARCHAR(50) NULL AFTER `bild_name`, ADD `bild` LONGBLOB NULL AFTER `bild_mime_type`;


ALTER TABLE `anmeldung` ADD `zahlart` ENUM('schule','bank') NULL DEFAULT 'schule' ;
ALTER TABLE `anmeldung` ADD `iban` VARCHAR(40) NULL ;
ALTER TABLE `anmeldung` ADD `kontoinhaber` VARCHAR(50) NULL;

 */

?>
