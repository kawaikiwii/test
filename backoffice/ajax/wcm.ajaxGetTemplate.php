<?php
/**
 * Project:     WCM
 * File:        wcm.ajaxGetTemplate.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

// Initialize system
require_once dirname(__FILE__).'/../initWebApp.php';

$templateId = getArrayParameter($_REQUEST, 'templateId', null);
$divId      = getArrayParameter($_REQUEST, 'divId', null);

if ($templateId)
{
    $session = wcmSession::getInstance();
    $project = $session->getProject();
    $wcmTemplate = new wcmTemplate($project);
    $wcmTemplate->refresh($templateId);
}
// No browser cache
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', false );
header( 'Pragma: no-cache' );

// Xml output
header("Content-Type: text/xml");
echo '<?xml version="1.0" encoding="UTF-8"?>'. "\n";

// Write ajax response
echo '<ajax-response>' . "\n";
echo '<response type="item" id="' . $divId . '"><![CDATA[' . $wcmTemplate->content . ']]></response>'. "\n";
echo '</ajax-response>'. "\n";

?>