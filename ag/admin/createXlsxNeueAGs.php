<?PHP

include '../db.php';
require_once("../funktionen.php");

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="AGListe_'. CfgModel::load("prefixAgNummer") .'.xlsx"');
header('Cache-Control: max-age=0');

include 'include_createXlsxNeueAGs.php';

?>