<?php
/**
 * Project:     WCM
 * File:        biz.manageSaveSearch.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

// Initialize system
require_once dirname(__FILE__).'/../../initWebApp.php';

// Get current project
$project = wcmProject::getInstance();

// Retrieve current session
$session = wcmSession::getInstance();

$action      = getArrayParameter($_REQUEST, "command", null);
$name        = getArrayParameter($_REQUEST, "name", null);
$description = getArrayParameter($_REQUEST, "description", null);
$queryString = getArrayParameter($_REQUEST, "queryString", null);
$url         = getArrayParameter($_REQUEST, "url", null);
$id          = getArrayParameter($_REQUEST, "id", null);
$divId       = getArrayParameter($_REQUEST, "divId", null);
$dashboard   = getArrayParameter($_REQUEST, "dashboard", 0);
$shared   = getArrayParameter($_REQUEST, "shared", 0);

$md = new savedSearchControl();
// No browser cache
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', false );
header( 'Pragma: no-cache' );

// Xml output
header('Content-Type: text/xml;charset=UTF-8');
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

// Write ajax response
echo "<ajax-response>\n";
echo '<response type="item" id="'.$divId.'"><![CDATA[';
switch ($action)
{
    case "create":
        $md->saveSearch($name, $description, $queryString, $url, $dashboard, $shared);
        echo $md->initialLoad('showSavedSearches');
        break;
    case "remove":
        $md->removeSearch($id);
        echo $md->initialLoad('showSavedSearches');
        break;
    case "showHistory":
        $md->initialLoad($action);
        break;
    case "showSavedSearches":
        echo $md->initialLoad($action);
        break;
    case "showPublicSavedSearches":
    	echo $md->initialLoad($action);
        break;
}
echo ']]></response>';
echo "</ajax-response>";
?>