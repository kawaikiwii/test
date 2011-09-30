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
$aPrints = array();

switch ($classname) {
    case "bin":
    
        $bin = new wcmBin($project);
        if ($bin->beginEnum("id=".$id)) {
            while ($bin->nextEnum()) {
                $aContents = explode('/', $bin->content);
                if ($aContents) {
                    $ini = 0;
                    foreach ($aContents as $aContent) {
                        if ($aContent) {
                            list($objectClass, $objectId) = explode('_', $aContent, 2);
                            
                            if ($objectClass && $objectId) {
                                $bizObject = new $objectClass($project, $objectId);
                                if ($bizObject->isAllowed())
                                    $aPrints[] = $bizObject;
                            }
                        }
                    }
                }
            }
        }
        
        break;
    case "folder":
        break;
    default:
        $bizObject = new $classname($project, $id);
        if ($bizObject->isAllowed())
            $aPrints[] = $bizObject;
        break;
}
$str = "";
foreach ($aPrints as $aPrint) {
    $permalinks = str_replace("%format%", "print", $aPrint->permalinks);
    $filename = $config['wcm.webSite.repository'].$permalinks;
    
    if (!file_exists($filename)) {
        echo("ERROR : file was not found".$permalinks);
        exit;
    }
    $str .= '<div class="ari-preview">';
    $str .= file_get_contents($filename);
    $str .= "</div>";
}

unset($aPrints);

$minifyCss = new Minify_Build($_gc["afprelax.css"]);

?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php echo APP_NAME?></title>
        <link rel="shortcut icon" href="/rp/images/default/favicon.ico"/>
        <style type="text/css">
            
            .ari-preview {
                border-bottom: 1px solid grey;
            }
        </style>
    </head>
    <body style="background-color:#fff;" onload="window.print();">
        <?php echo($str)?>
    </body>
</html>
