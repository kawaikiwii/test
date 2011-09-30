<?php

/**
 * Project:     WCM
 * File:        wcm.getConnectorTable.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 * @see         wcm.getConnectorTable.js
 *
 */

// Initialize system
require_once dirname(__FILE__).'/../initWebApp.php';

// Get current project
$project = wcmProject::getInstance();

// Retrieve query parameters
$connectorId    = getArrayParameter($_REQUEST, "connectorId", null);
$divId          = getArrayParameter($_REQUEST, "divId", null);
$currentOption  = getArrayParameter($_REQUEST, "currentOption", null);

// No browser cache
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', false );
header( 'Pragma: no-cache' );

// Xml output
header("Content-Type: text/xml");
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
// Write ajax response
echo '<ajax-response>' . "\n";
echo '<response type="item" id="' . $divId . '">' . "\n";
echo '<![CDATA[';
if (!renderBizTableList($currentOption, $connectorId))
{
    echo _NO_TABLE_MATCH;
}
else
{
    echo '<select id="connectorTable" name="connectorTable" style="width:500px">';
    echo '<option value="0">(' .  _NONE . ')</option>';
    echo renderBizTableList($currentOption, $connectorId);
    echo '</select>';
}
echo ']]>';
echo '</response>' . "\n";
echo '</ajax-response>' . "\n";
?>