<?php
/**
 * Project:     WCM
 * File:        biz.controlSearchResults.php
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

$module          = getArrayParameter($_REQUEST, "module", null);
$action          = getArrayParameter($_REQUEST, "action", null);
$asset_id        = getArrayParameter($_REQUEST, "asset_id", null);
$options         = getArrayParameter($_REQUEST, "options", null);
$divId           = getArrayParameter($_REQUEST, "divId", null);

$md = new $module();
$md->$action($assetId, $options);

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
echo $md->initialLoad();
echo ']]></response>';
echo "</ajax-response>";
?>