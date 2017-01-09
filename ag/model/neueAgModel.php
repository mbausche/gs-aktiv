<?php

//use RedBeanPHP;


class NeueAgFieldNames {
	public $textFields = array("namen",
			"verantwortlicher_name",
			"verantwortlicher_mail",
			"verantwortlicher_telefon",
			"ag_name",
			"text_ausschreibung",
			"wichtige_infos",
			"termin_von",
			"termin_bis",
			"max_kinder",
			"anzahl_helfer",
			"ort",
			"ausserdem",
			"ag_nummer");
	public $checkboxes = array("klasse1","klasse2","klasse3","klasse4");
	public $dateFields = array("termin","termin_ueberbuchung","termin_ersatz");
	public $floatFields = array("betrag_mitglied","betrag_nicht_mitglied");
	public $pictureFields = array("bild_name","bild_mime_type","bild");
}
/**
 * Klasse für den Datenzugriff
 */
class NeueAgModel {
	
	public static function insertFromRequest($forceUpdate = false, $edit_token = 0) {

		if ($forceUpdate) {
			$ag = R::findOne('neueag',' edit_token = ?', [$edit_token]);
		} else {
			$ag = R::findOne('neueag',' verantwortlicher_name = ? and ag_name = ?'
					, [ $_REQUEST['verantwortlicher_name'], $_REQUEST['ag_name']  ]);
		}
		
		if ($ag === null) {
			$ag = R::dispense( 'neueag' );
			$created = true;
		} else {
			$created = false;
		}
		
		$n = new NeueAgFieldNames();
		
		$_REQUEST["termin_von"] = str_replace(".", ":", $_REQUEST["termin_von"]);
		$_REQUEST["termin_bis"] = str_replace(".", ":", $_REQUEST["termin_bis"]);
		
		if ($_REQUEST["response"] == "yes") {
			$ag["response"] = 1;
		}
		
		foreach ($n->textFields as $f) {
			if (!empty($_REQUEST[$f])) {
				$ag[$f] = $_REQUEST[$f];
			} else {
				$ag[$f] = null;
			}
		}
		
		foreach ($n->checkboxes as $f) {
			if (!empty($_REQUEST[$f])) {
				$ag[$f] = 1;
			} else {
				$ag[$f] = 0;
			}
		}
		
		foreach ($n->dateFields as $f) {
			if (!empty($_REQUEST[$f])) {
				$ag[$f] = DateTime::createFromFormat("d.m.Y",$_REQUEST[$f]);;
			} else {
				$ag[$f] = null;
			}
		}		
		
		foreach ($n->floatFields as $f) {
			if (!empty($_REQUEST[$f])) {
				$ag[$f] = euroStringToFloat($_REQUEST[$f]);
			} else {
				$ag[$f] = 0;
			}
		}
		
		$uploaddir = './uploads/';
		$uploadfile = $uploaddir . basename($_FILES['bild']['name']);
		error_log("FILES: " . print_r($_FILES,true));
		
		if (!empty($_FILES['bild']['name']) && !empty($_FILES['bild']['tmp_name'])) {
			error_log("Datei: " . $_FILES['bild']['name']);
			if (move_uploaded_file($_FILES['bild']['tmp_name'], $uploadfile)) {
				$ag["bild_name"] = $_FILES['bild']['name'];
				$ag["bild_mime_type"] = $_FILES['bild']['type'];
				$ag["bild"] = file_get_contents($uploadfile);
			} 
		}
		
		if (!empty($_REQUEST["ag_nummer"])) {
			$ag["ag_nummer"] = $_REQUEST["ag_nummer"];
		}
		

		if ($created) {
			$ag["edit_token"] = NeueAgModel::getEditToken($ag); 
		}
		$id = R::store( $ag );
		
		
		
		
		return array($created,$id);
		
	}
	
	public static function insertFromArchive($archiveTableName, $archiveId) {
	
		$old = R::load($archiveTableName,$archiveId);
		unset($old['id']);
		unset($old['ag_nummer']);
		unset($old['edit_token']);
		unset($old['response']);
		
		$ag = R::dispense( 'neueag' );

		foreach ($old as $key => $value){
			$ag[$key] = $value;				
		}
		
		$id = R::store( $ag );
		
		return "Importiert: " . $archiveTableName . ": " . 	$old["ag_name"]; 
	}	
	
	/**
	 * 
	 * @param unknown $id
	 * @param unknown $response 0 = Nicht okay, 1 = okay, 2 = Warten auf Mail
	 * @return multitype:boolean unknown
	 */
	public static function saveFeedBack($id, $response) {
		$ag = R::findOne('neueag',' id = ?', [$id]);
		$ag["response"] = $response;
		R::store( $ag );
	}	
	
	public static function loadAGForEdit($id) {
		$ag = R::load( 'neueag', $id );
		$result = array();
		
		$n = new NeueAgFieldNames();
		
		foreach ($n->textFields as $f) {
			if (!empty($ag[$f])) {
				$result[$f] = $ag[$f];
			} else {
				$result[$f] = null;
			}
		}
		
		foreach ($n->pictureFields as $f) {
			if (!empty($ag[$f])) {
				$result[$f] = $ag[$f];
			} else {
				$result[$f] = null;
			}
		}
		
		foreach ($n->checkboxes as $f) {
			if (!empty($ag[$f])) {
				$result[$f] = "Ja";
			} else {
				$result[$f] = "Nein";
			}
		}
		
		foreach ($n->dateFields as $f) {
			if (!empty($ag[$f])) {
				$result[$f] = formatSQLDate($ag[$f]);
			} else {
				$result[$f] = null;
			}
		}
		
		foreach ($n->floatFields as $f) {
			if (!empty($ag[$f])) {
				$result[$f] = euroStringToFloat($ag[$f]);
			} else {
				$result[$f] = "0";
			}
		}	

		if (empty($result["max_kinder"])) {
			$result["max_kinder"] = "unbegrenzt";
		}
		
		return $result;
		
	}
	
	
	public static function getAgs($order = 'order by termin') {
		return R::findAll('neueag',$order);
	}
	
	public static function getAgsVorjahre( ) {
		
		$result = array();
		
		$vorjahre = explode(",", CfgModel::load("prefix.vorjahre"));
		foreach ($vorjahre as $j) {
			$tmp = explode("=", $j);
			$result[$tmp[0]] = R::findAll($tmp[0] . "neueag"," order by ag_nummer"); 
		}
		
		return $result; 
	}
	
	public static function generateNumbers() {
		$ags = NeueAgModel::getAGs();
		$count = 0; 
		foreach ($ags as $ag) {
			//$ag = R::findOne('neueag',' id = ?', [$ag['id']]);
			$ag["ag_nummer"] = null;
			R::store( $ag );
		}

		$ags = NeueAgModel::getAGs();
		$count = 1;
		foreach ($ags as $ag) {
			//$tmp = R::load( 'neueag', $ag['id'] );
			$nr = CfgModel::load("prefixAgNummer") . str_pad($count, 2,"0",STR_PAD_LEFT);
			$ag["ag_nummer"] = $nr;
			R::store( $ag );
			$count++;
		}
	}
	
	
	public static function loadAG($id) {
		$ag = R::load( 'neueag', $id );
		return $ag;
	}

	
	public static function copyAG($id) {
		$ag = R::load( 'neueag', $id );
		$ag1 = R::dispense( 'neueag' );
		foreach ($ag as $key => $value) {
			$ag1[$key] = $value;
		}
		unset($ag1["id"]);
		unset($ag1["ag_nummer"]);
		$ag1["ag_name"] = $ag["ag_name"] . " - COPY";
		//temp token
		$ag1["edit_token"] = microtime() . NeueAgModel::getEditToken($ag1);
		R::store( $ag1 );
		//real token
		$ag1["edit_token"] = NeueAgModel::getEditToken($ag1);
		R::store( $ag1 );
		return $ag1;
	}
	
	public static function deleteAG($id) {
		$ag = R::load( 'neueag',$id );
		R::trash( $ag );
	}
	
	/**
	 * @param string $id termine dieser ag ausschließen
	 * @param string $typ alle (default) oder termin1
	 */
	public static function getAlleTermineMitAg($id="", $typ="alle") {
		
		$where = "";
		$and = "";
		if (isset($id) && !empty($id)) {
			$where = " WHERE ID != " . $id;
			$and = " AND ID != " . $id;
		}
		
		if ($typ == "alle") 
			return R::getAll("(SELECT distinct verantwortlicher_name, ag_name, termin, '1. Termin' as typ, termin_von as von, termin_bis as bis FROM neueag $where) 
				UNION distinct 
				(SELECT distinct verantwortlicher_name, ag_name, termin_ueberbuchung as termin, '2. Termin' as typ, termin_von as von, termin_bis as bis FROM neueag where termin_ueberbuchung is not null $and) 
				UNION distinct 
				(SELECT distinct verantwortlicher_name, ag_name, termin_ersatz as termin, 'Ersatztermin' as typ, termin_von as von, termin_bis as bis FROM neueag where termin_ersatz is not null $and) 
				ORDER BY typ asc, termin ASC, von ASC");
		else 
			return R::getAll("SELECT distinct verantwortlicher_name, ag_name, termin, '1. Termin' as typ, termin_von as von, termin_bis as bis FROM neueag $where
					ORDER BY typ asc, termin ASC, von ASC");
						
	}
	
	public static function  udpateEditToken($id) {
		$ag = R::findOne('neueag',' id = ?', [$id]);
		$ag["edit_token"] = NeueAgModel::getEditToken($ag);
		R::store( $ag );
	}
	
	public static function getEditToken($ag) {
		return md5($ag["namen"] . $ag["ag_name"] . $ag["id"]);
	}
	
	public static function getIdByEditToken($editToken) {
		if (!empty($editToken)) {
			$ag = R::findOne('neueag',' edit_token = ?', [$editToken]);
			return $ag["id"];
		}
	}
	
	public static function getByEditToken($editToken) {
		$ag = R::findOne('neueag',' edit_token = ?', [$editToken]);
		return $ag;
	}
	
	public static function emptyTables() {
		$tables = array("neueag");
		foreach ($tables as $oldTable) {
			R::wipe($oldTable);
		}
	}
	
	public static function copyTables($prefix, $suffix="") {
		$tables = array("neueag");
		foreach ($tables as $oldTable) {
			$newTable = $prefix . $oldTable. $suffix;
			R::getAll("CREATE TABLE " . $newTable . " LIKE " . $oldTable);
			R::getAll("INSERT " . $newTable . " SELECT * FROM " . $oldTable);
		}
	}
	
	public static function checkAG($ag) {
		$result = array();
		
		$termine = array($ag["termin"],$ag["termin_ueberbuchung"],$ag["termin_ersatz"]);
		$labels = array("1. Termin","2. Termin","Ersatztermin");
		$required = array(true,false,false);
		
		$ferienArray = explode(";", CfgModel::load("neue.ag.ferien"));
		$ferien = array();
		
		$strAgStart= CfgModel::load("neue.ag.zeitraum.von");
		$tsAgStart = strtotime($strAgStart);
		
		$strAgEnde = CfgModel::load("neue.ag.zeitraum.bis");
		$tsAgEnde = strtotime($strAgEnde);
		
		foreach ($ferienArray as $paarString) {
			$paar = explode("-", $paarString);
			array_push($ferien, array(strtotime($paar[0]),
			strtotime($paar[1]),
			$paarString
			));
		}

		$arrDate = array();
		$arrContact = array();
		$arrOthers = array();
		
		for ($i = 0; $i < count($termine); $i++) {
			
			if (empty($termine[$i])) {
				if ($required[$i]) {
					array_push($arrDate, $labels[$i]. " nicht definiert");
				}
			} else {
				
				$strDate = formatSQLDate($termine[$i]);
				if (strpos($ag["ausserdem"], $strDate . ":ok") === false) {
					$curr = strtotime($termine[$i]);
					$infos=getdate(strtotime( $termine[$i] ));
					$wday = $infos["wday"];
					if ($wday == 0) {
						array_push($arrDate, $labels[$i]. " liegt an einem Sonntag");
					} 
					else if ($wday == 6) {
						array_push($arrDate, $labels[$i]." liegt an einem Samstag");
					}
	
					if ($curr < $tsAgStart) {
						array_push($arrDate, $labels[$i] . " liegt vor dem Beginn der AGs (".$strAgStart.")");
					}
					if ($curr > $tsAgEnde) {
						array_push($arrDate, $labels[$i] . " liegt nach dem Ende der AGs (". $strAgEnde .")");
					}
					
					foreach ($ferien as $f) {
						$from = $f[0];
						$to = $f[1];
						if ($curr >= $from && $curr <= $to) {
							array_push($arrDate, $labels[$i] . " liegt in den Ferien ($f[2])");
						}
					}
				} 
				
			}
		}

		
		if (!filter_var($ag["verantwortlicher_mail"], FILTER_VALIDATE_EMAIL)) {
			array_push($arrContact, "Mail-Adresse nicht gültig");
		} 
		if (!checkPhone($ag["verantwortlicher_telefon"])) {
			array_push($arrContact, "Telefonnumer nicht gültig");
		} 
		if (!checkTime($ag["termin_von"])) {
			array_push($arrDate, "Uhrzeit von hat nicht das Format: hh:mm bzw liegt nicht zwischen 8 und 22 Uhr");
		}
		if (!checkTime($ag["termin_bis"])) {
			array_push($arrDate, "Uhrzeit bis hat nicht das Format: hh:mm bzw liegt nicht zwischen 8 und 22 Uhr");
		}
		
		if ($ag["betrag_mitglied"] != 0 && $ag["betrag_nicht_mitglied"] != 0  &&  $ag["betrag_mitglied"] >= $ag["betrag_nicht_mitglied"]) {
			array_push($arrOthers, "Mitglieder sollten weniger bezahlen als Nicht-Mitglieder");
		}
		
		if (empty($ag["bild"])) {
			array_push($arrOthers, "Kein Bild");
		}
		
		
		$result["Termine"] = implode("\n",$arrDate);
		$result["Kontakt"] = implode("\n",$arrContact);
		$result["Andere"] = implode("\n",$arrOthers);
		
		return $result;
	}
	
	
}
//ALTER TABLE `neueag` ADD `edit_token` VARCHAR(200) NULL ;
?>
