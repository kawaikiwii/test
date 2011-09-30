<?php

/**
 * Project:     WCM
 * File:        biz.contribution.php
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

// Initialize return values
$message    = null;
$renderIds  = null;
$xslMode    = "list";
$renderNull = 0;

// Retrieve parameters
$command    = getArrayParameter($_REQUEST, "command", null);
$itemId     = getArrayParameter($_REQUEST, "itemId", 0);
$divId      = getArrayParameter($_REQUEST, "divId", 0);
$messageId  = getArrayParameter($_REQUEST, "idMessage", null);

// Retrieve contribution object
$contribution = new contribution($project);
$contribution->refresh($itemId);
switch($command)
{
case "validate":
    $contribution->validate($session->userId);
    break;

case "delete":
    $contribution->delete();
    break;
}

// No browser cache
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', false );
header( 'Pragma: no-cache' );

// Xml output
header( 'Content-Type: text/xml' );
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

// Write ajax response
echo "<ajax-response>\n";

$renderParameters = array();
$renderParameters["mode"] = $xslMode;
$renderParameters["callback"] = "onSelectItem";

switch($command)
{
case "delete":
    echo "<response type='item' id='".$divId."'><![CDATA[]]></response>\n";
    break;

default:
    echo "<response type='item' id='".$divId."'><![CDATA[";
    echo renderBizobject($contribution, 'render_contribution', $renderParameters);
    echo "]]></response>\n";
    break;
}

echo "<response type='item' id='messageContribution'><![CDATA[]]></response>\n";

// Return message ?
if ($messageId != null)
{
    echo "<response type='item' id='".$messageId."'>";
    if ($message)
        echo "<![CDATA[ ".$message." ]]>";
    echo "</response>\n";
}
echo "</ajax-response>";

?>
