<?php

/**
 * Project:     WCM
 * File:        biz.importMngt.php
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
$object    = getArrayParameter($_REQUEST, "object", null);
$itemId     = getArrayParameter($_REQUEST, "itemId", 0);
$divId      = getArrayParameter($_REQUEST, "divId", 0);
$messageId  = getArrayParameter($_REQUEST, "idMessage", null);

// Create new object
$obj = new $object();
$obj->refresh($itemId);

switch($command)
{
	case "delete":
	    $obj->delete();
	    break;
		
	default:
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

switch($command)
{
	case "delete":
	    echo "<response type='item' id='".$divId."'><![CDATA[]]></response>\n";
	    break;
	
	default:
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
