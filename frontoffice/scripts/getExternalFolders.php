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

if ($_REQUEST["node"] != "eFSrc") {
    exit();
}

$user = new account();
$user->refreshByWcmUser($session->userId);

$folders = array();

$aFolders = array();

$folder = new folder();
$folderUniverse = $folder->getFoldersMultiUniverse($site->id);
foreach($folderUniverse as $folderUnivers) {
	$folder->refresh($folderUnivers);
	$where = "id = '".$folderUnivers."' AND workflowstate = 'published'";
	if ($folder->beginEnum($where, "type DESC, rank ASC")) {
	    if ($folder->nextEnum()) {
    	    if(!array_key_exists("0-".$folder->siteId,$aFolders)) {
	        	$site = new site();
	        	$site->refresh($folder->siteId);
	    		$aFolders[$folder->siteId]["text"] = $site->description;
				$aFolders[$folder->siteId]["cls"] = "ari-node-folder-auto";
				$aFolders[$folder->siteId]["iconCls"] = "ari-folder-auto";
				$aFolders[$folder->siteId]["id"] = "0-".$folder->siteId;
	    	}
	        $aFolder = array();
	        $aFolder["text"] = htmlspecialchars($folder->title);
	        $aFolder["cls"] = "ari-node-folder-".$folder->type;
	        $aFolder["id"] = $folder->id;
	        $aFolder["leaf"] = true;
	        $aFolder["iconCls"] = "ari-folder-".$folder->type;
	        $aFolders[$folder->siteId]["children"][] = $aFolder;
		}
	}
}
foreach ($aFolders as $afolder)
	$folders[] = $afolder;

echo(json_encode(($folders)));
?>
