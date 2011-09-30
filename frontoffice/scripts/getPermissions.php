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

$FOpermissions = array();
$permissions = $CURRENT_ACCOUNT->getPermissions("children");

if ($site->isAllowed("children")) {
    $services = $CURRENT_ACCOUNT->getServices($site->id);
    foreach ($services as $service) {
        $perm = array();
        $perm["service"][] = $service;
        $perm["channels"][] = implode($CURRENT_ACCOUNT->getRubriques($session->getSiteId(), $service), ",");
        $FOpermissions[] = $perm;
    }    
}

echo(json_encode(array("permissions"=>$FOpermissions)));
?>
