<?php

//use RedBeanPHP;

function myErrorHandler($fehlercode, $fehlertext, $fehlerdatei, $fehlerzeile)
{
 	if (!(error_reporting() & $fehlercode)) {
 		// Dieser Fehlercode ist nicht in error_reporting enthalten
 		return;
 	}

	switch ($fehlercode) {
		case E_USER_ERROR:
			echo "<b>Mein FEHLER</b> [$fehlercode] $fehlertext<br />\n";
			echo "  Fataler Fehler in Zeile $fehlerzeile in der Datei $fehlerdatei";
			echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
			echo "Abbruch...<br />\n";
					exit(1);
					break;

					case E_USER_WARNING:
					echo "<b>Meine WARNUNG</b> [$fehlercode] $fehlertext<br />\n";
					break;

					case E_USER_NOTICE:
					echo "<b>Mein HINWEIS</b> [$fehlercode] $fehlertext<br />\n";
					break;

					default:
					echo "Unbekannter Fehlertyp: [$fehlercode] $fehlertext<br />\n";
					break;
	}

	/* Damit die PHP-interne Fehlerbehandlung nicht ausgeführt wird */
	return true;
}


/**
 * Klasse für den Datenzugriff
 */
class TemplateModel{
	public static  function getTemplates() {
		return R::findAll('templates');
	}
	
	public static function save($id, $code) {
		$t = R::load( 'templates',$id );
		$t['code'] = $code;
		R::store( $t );
	}

	public static function delete($id) {
		$t = R::load( 'templates',$id );
		R::trash( $t );
	}
	
	public static function create($template, $beschreibung, $code) {
		$t = R::dispense( 'templates' );
		$t['name'] = $template;
		$t['description'] = $beschreibung;
		$t['code'] = $code;
		$id = R::store( $t );
		return $t;
	}
	
	
	public static function load($id) {
		return R::load( 'templates',$id );
	}
	
	
}
?>