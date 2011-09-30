<?php 
// Initialize WCM API
require_once (dirname(__FILE__).'/../inc/wcmInit.php');
if (!($session->userId)) {
    header("location: /");
    exit();
}

$user = new account();
$user->refreshByWcmUser($session->userId);

$universe = $user->getPermissionsUniverse();

$permissions = array();
$perm = array();
foreach($universe as $univers=>$univ)
	$perm["id"][] = "0-".$univ;
$permissions[] = $perm;

echo(json_encode(array("permissionsExternalFolders"=>$permissions)));
?>