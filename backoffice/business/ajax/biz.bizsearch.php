<?php
/**
 * Project:     WCM
 * File:        business/ajax/biz.bizsearch.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

// Initialize the system
require_once dirname(__FILE__).'/../../initWebApp.php';

// Execute the action
wcmMVC_Action::execute('business/search', $_REQUEST);
$context = wcmMVC_Action::getContext();

$resultName = $context->name . ucfirst($context->pageType) . 'Result';

// No browser cache
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', false );
header( 'Pragma: no-cache' );

// Xml output
header('Content-Type: text/xml;charset=UTF-8');
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

// Write the ajax response
echo "<ajax-response>\n";
echo '<response type="item" id="'.$resultName.'"><![CDATA[';

wcmModule('business/search/result', array($context));

echo "]]></response>\n";
echo "</ajax-response>\n";
?>