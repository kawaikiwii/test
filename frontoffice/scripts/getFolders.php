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

if ($_REQUEST["node"] != "fSrc") {
    exit();
}

$aFolders = array();
$aFolders["auto"]["text"] = FOLDER_AUTO;
$aFolders["temp"]["text"] = FOLDER_TEMP;
$aFolders["perm"]["text"] = FOLDER_PERM;
$aFolders["auto"]["iconCls"] = "ari-folder-auto";
$aFolders["temp"]["iconCls"] = "ari-folder-temp";
$aFolders["perm"]["iconCls"] = "ari-folder-perm";
$aFolders["auto"]["cls"] = "ari-node-folder-auto";
$aFolders["temp"]["cls"] = "ari-node-folder-temp";
$aFolders["perm"]["cls"] = "ari-node-folder-perm";

$folder = new folder();
$where = "siteId = '".$session->getSiteId()."' AND workflowstate = 'published'";
if ($folder->beginEnum($where, "type DESC, rank ASC")) {
    while ($folder->nextEnum()) {
        $aFolder = array();
        $aFolder["text"] = htmlspecialchars($folder->title);
        $aFolder["cls"] = "ari-node-folder-".$folder->type;
        $aFolder["id"] = $folder->id;
        $aFolder["leaf"] = true;
        $aFolder["iconCls"] = "ari-folder-".$folder->type;
        $aFolders[$folder->type]["children"][] = $aFolder;
    }
}

$folders = array();
foreach ($aFolders as $folder) {
    $folders[] = $folder;
    
}
echo(json_encode(($folders)));

?>
