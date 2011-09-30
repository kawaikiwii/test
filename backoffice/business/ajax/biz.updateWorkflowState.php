<?php

/**
 * Project:     WCM
 * File:        biz.updateWorkflowState.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * This page is called by an Ajax call, It returns a the update content for the workflowState select
 *
 */

// Initialize system
require_once dirname(__FILE__).'/../../initWebApp.php';

// Get current project
$project = wcmProject::getInstance();

// Retrieve (some) parameters
$elementId = getArrayParameter($_REQUEST, "elementId", null);
$selectId = getArrayParameter($_REQUEST, "selectId", null);
$selectName = getArrayParameter($_REQUEST, "selectName", null);
$className = getArrayParameter($_REQUEST, "className", null);

// No browser cache
header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
header( "Last-Modified: ".gmdate( "D, d M Y H:i:s" )." GMT" );
header( "Cache-Control: no-store, no-cache, must-revalidate" );
header( "Cache-Control: post-check=0, pre-check=0", false );
header( "Pragma: no-cache" );

// XML output
header("Content-Type: text/xml");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

// Write ajax response
echo "<ajax-response>\n";
echo "<response type='item' id='".$elementId."'>\n";

echo "<select id='".$selectId."' name='".$selectName."' style='width:100%'>\n";
echo "<option value=''>("._BIZ_ALL.")</option>\n";
if ($className)
{
    //TODO: Retrieve workflow state
}
else
{
    //TODO: Retrieve all workflow states
}
renderHtmlOptions($workflowStateList);
echo "</select>\n";

echo "</response>\n";
echo "</ajax-response>\n";
?>
