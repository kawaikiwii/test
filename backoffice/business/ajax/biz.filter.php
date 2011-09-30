<?php
/**
 * Project:     WCM
 * File:        biz.filter.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

// Initialize system and retrieve project
require_once dirname(__FILE__).'/../../initWebApp.php';

// No browser cache
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', false );
header( 'Pragma: no-cache' );

// Xml output
header('Content-Type: text/xml;charset=UTF-8');
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

// Get request parameters
$filterName = getArrayParameter($_REQUEST, 'filterName', null);
$options = array(
    'filter' => json_decode(getArrayParameter($_REQUEST, 'filter', null)),
    'searchId' => getArrayParameter($_REQUEST, 'searchId', null),
    'searchEngine' => getArrayParameter($_REQUEST, 'searchEngine', null),
    );

// Write ajax response
echo "<ajax-response>\n";
echo "<response type='item' id='".$filterName."'>\n";

wcmModule('business/search/filters/' . $filterName, $options);

echo "</response>\n";
echo "</ajax-response>";

?>