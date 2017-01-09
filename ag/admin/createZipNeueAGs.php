<?PHP
include '../db.php';
require_once("../funktionen.php");

if (!is_dir('./zip')) {
	mkdir('./zip');
}

array_map('unlink', glob("./zip/*"));

$zip = new ZipArchive();
$filename = "./zip/".CfgModel::load("prefixAgNummer").".zip";

if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
	exit("cannot open <$filename>\n");
}

ob_start();
include "include_createXlsxNeueAGs.php";
$content = ob_get_clean();

//$url = getBaseUrl() ."admin/createXlsxNeueAGs.php";
//file_put_contents("./zip/ags.xlsx", file_get_contents($url));
$zip->addFromString("ags.xlsx", $content);
//$zip->addFile("./zip/ags.xlsx","ags.xlsx");

$ags = NeueAgModel::getAgs();
foreach ($ags as $ag) {
	if (isset($ag["bild_name"])) {
		$nr = $ag["ag_nummer"];
		if (!isset($nr) || empty($nr)) {
			$nr = $ag["id"];
		}
		$suffix = ".".strtolower(array_pop(explode(".", $ag["bild_name"])));

		//Für die Datei im Filesystem
		$name = toIso($ag["ag_name"]);		
		$imgFileName = $nr . "_" . $name . $suffix;
		
		//Für die Datei in der Zip-Datei
		$imgFileName1 =  toZipCP850($nr . "_" . $ag["ag_name"] . $suffix);
		
		$withPath = "./zip/" . $imgFileName;
		file_put_contents($withPath, $ag["bild"]);
		$zip->addFile($withPath, $imgFileName1);
	}
}

$zip->close();

$base = basename($filename);

header("Content-Type: application/zip");
header("Content-Disposition: attachment; filename=$base");
header("Content-Length: " . filesize($filename));

readfile($filename);

exit;
?>