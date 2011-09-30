<?php 
// Initialize WCM API
require_once (dirname(__FILE__).'/../inc/wcmInit.php');
if (!($session->userId)) {
    header("location: /");
    exit();
}

$pays = array();
$handle = fopen("../previsions.json", "r");
while (($buffer = fgets($handle, 4096)) !== false) {
	$buffer = trim($buffer);
	if(substr($buffer,0,6) == "'pays'") {
		$pos_debut = strpos($buffer,"'",7);
		$buffer = substr($buffer,$pos_debut+1);
		$buffer = substr($buffer,0,-2);
		if (!in_array($buffer,$pays) && !empty($buffer)) {
			$pays[] = $buffer;
		}
	}
}
sort($pays);
$file = "{ 'events': [";
$file .= "{'pays': 'Tous les pays'},";
$nbElem = count($pays);
$cpt = 0;
foreach($pays as $pay) {
	$cpt++;
	$file .= "{'pays': '".$pay."'}";
	if($cpt < $nbElem)
		$file .= ",";
}
$file .= "]}";
echo $file;
?>
