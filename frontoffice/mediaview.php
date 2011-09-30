<?php 
// Initialize WCM API
require_once (dirname(__FILE__).'/inc/wcmInit.php');
if (!(isset($session->userId) && $session->userId)) {
    exit();
}
$session->ping();
$CURRENT_SITECODE = $session->getSite()->code;
require_once (dirname(__FILE__).'/inc/siteInit.php');

$classname = isset($_REQUEST["classname"]) ? $_REQUEST["classname"] : null;
$id = isset($_REQUEST["id"]) ? $_REQUEST["id"] : null;

if (!$classname AND !$id) {
    echo "WRONG PARAMETERS";
    exit();
}

$bizObject = new $classname($project, $id);

if ($bizObject->isAllowed()) {

    $permalinks = str_replace("%format%", "media", $bizObject->permalinks);
    $filename = $config['wcm.webSite.repository'].$permalinks;
    
    if (!file_exists($filename)) {
        @$bizObject->generate();
	
    }

@$bizObject->generate();
    
    if (!file_exists($filename)) {
        echo("ERROR : file was not found : ".$permalinks);
        exit;
    }
    
	 $str = file_get_contents($filename);
?>
<div id="mediaview" class="ari-mediaview">
    <?php echo($str)?>
</div>
<?php 
}
else {
    @ include (dirname(__FILE__)."/sites/$site->code/restricted.php");
}
?>
