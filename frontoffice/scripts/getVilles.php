<?php 
// Initialize WCM API
require_once (dirname(__FILE__).'/../inc/wcmInit.php');
if (!($session->userId)) {
    header("location: /");
    exit();
}

$villes = array();
$handle = fopen("../".$_GET["file"], "r");
while (($buffer = fgets($handle, 4096)) !== false) {
	$buffer = trim($buffer);
	if(substr($buffer,0,7) == "'ville'") {
		$pos_debut = strpos($buffer,"'",7);
		$buffer = substr($buffer,$pos_debut+1);
		$buffer = substr($buffer,0,-2);
		if (!in_array($buffer,$villes) && !empty($buffer)) {
			$villes[] = $buffer;
		}
	}
}
sort($villes);
$file = "{ 'events': [";
$file .= "{'ville': 'Tous les lieux'},";
$nbElem = count($villes);
$cpt = 0;
foreach($villes as $ville) {
	$cpt++;
	$file .= "{'ville': '".$ville."'}";
	if($cpt < $nbElem)
		$file .= ",";
}
$file .= "]}";
echo $file;
?>
