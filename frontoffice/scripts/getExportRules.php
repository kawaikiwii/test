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

$binId = (isset($_REQUEST["cmpId"])) ? $_REQUEST["cmpId"] : null;
$binId = explode("-", $binId);
$binId = $binId[2];

$aExportRules = array();

$exportRules = new exportRule();
$exportRules = $exportRules->getExportRulesByPermission($session->userId);

foreach ($exportRules as $exportRule) {
    $handler = "function(item) {ARe.bin.launchExport('$exportRule->id', '$binId')}";
    if ($binId == "preview") {
        $handler = "function(item) {ARe.previewExport('$exportRule->id')}";
    }
    
    $aExportRule = array();
    $aExportRule["text"] = utf8_decode($exportRule->title);
    $aExportRule["id"] = $exportRule->id;
    $aExportRule["handler"] = $handler; //"function(item) {ARe.bin.launchExport('$exportRule->id', '$binId')}";
    $aExportRules[] = $aExportRule;
}
echo(json_encode(($aExportRules)));


?>
