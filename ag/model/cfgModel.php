<?php

//use RedBeanPHP;

/**
 * Klasse für den Datenzugriff
 */
class CfgModel{
	public static function getEntries() {
		return R::findAll('config');
	}
	
	public static function save($id, $value) {
		if (!empty($value)) {
			$t = R::load( 'config',$id );
			$t['value'] = $value;
			R::store( $t );
		}
	}

	public static function load($name) {
		$entry = R::findOne('config',' name = ?', [ $name]);
		if ($entry !== null)
			return $entry['value'];
		else
			return "???" . $name . "???";
	}
	
	public static function delete($id) {
		$t = R::load( 'config',$id );
		R::trash( $t );
	}
	
	public static function create($name, $beschreibung, $value) {
		$t = R::dispense( 'config' );
		$t['name'] = $name;
		$t['description'] = $beschreibung;
		$t['value'] = $value;
		$id = R::store( $t );
		return $t;
	}	
}
?>