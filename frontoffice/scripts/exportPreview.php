<?php 
// Initialize WCM API
require_once (dirname(__FILE__).'/../inc/wcmInit.php');
if (!($session->userId)) {
    header("location: /");
    exit();
}
$site = $session->getSite();
$session->setLanguage($site->language);
// Retrieve FO Language Pack
require_once (dirname(__FILE__)."/../sites/".$site->code."/conf/lang.php");
ini_set("max_execution_time", "18000");

//$binControl = new binControl();
//$documents = $binControl->getDocuments($_REQUEST["binId"]);

$classname = $_REQUEST["classname"];
$item = new $classname ();
$item->refresh( $_REQUEST["id"]);

$documents = array();
$documents[] = $item;

if ($item->isAllowed("primaire","children")) {
	$exportRule = new exportRule();
	$exportRule->refresh($_REQUEST["exportRuleId"]);
	
	$exportRule->execute($documents);
}

/*rmdir_r("/tmp/wcm/exports/20091214_104456_22");
rmdir_r("/tmp/wcm/exports/20091214_104543_22");
rmdir_r("/tmp/wcm/exports/20091214_104646_22");
rmdir_r("/tmp/wcm/exports/20091216_134707_1");
rmdir_r("/tmp/wcm/exports/20091216_134737_1");
rmdir_r("/tmp/wcm/exports/20091216_134821_1");*/

?>
