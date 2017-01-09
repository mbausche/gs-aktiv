<?php

//use RedBeanPHP;

/**
 * Klasse für den Datenzugriff
 */
class StatusModel{

	public static function wipe() {
		R::wipe( 'status' );
	}
	
	
	public static function insertStatus($zeile) {
	
		$zeile = mb_convert_encoding($zeile, 'UTF-8',mb_detect_encoding($zeile, 'UTF-8, ISO-8859-1', true));
		$spalten = explode(";", $zeile);
		if (count($spalten) == 3) {
			$status = R::dispense( 'status' );
			$status['name'] = $spalten[1] . " " .$spalten[0];
			if (trim($spalten[2]) == 'ja') {
				$status['status'] =  1;
			} else {
				$status['status'] =  0;
			}
			
			$id = R::store( $status );
			return $id;
		} else {
			return -1;
		}
	}
	
	
	public static function updateStatus($name, $status, $isID=false) {
		if ($isID)
			$s = R::load('status',$name);
		else 
			$s = R::findOrCreate( 'status', ['name' => $name] );
		//$s = R::load('status', $s[id]);
		$s['status'] = $status;
		R::store($s);
	}
	
	public static function delete($name, $isID=false) {
		if ($isID)
			$s = R::load('status',$name);
		else 
			$s = R::findOrCreate( 'status', ['name' => $name] );
		R::trash($s);
	}
	/**
	 * Liest alle Status einträge 
	 * @param unknown $ag
	 * @return ein Array. Key ist der Name (lowercase) und value der Status (1=mitglied, 0=Nicht-Mitglied)
	 */
	public static function getAll() {
		
		$statusEntries = R::findAll( 'status' );
		$result = array();
		
		foreach ($statusEntries as $status) {
			$name = mb_strtolower($status['name'],'UTF-8');
			$result[$name] = $status; 
		}
		return $result;
	}
	
}
?>