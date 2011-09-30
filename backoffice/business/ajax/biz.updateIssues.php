<?php

/**
 * Project:     WCM
 * File:        biz.updateDropDown.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * This page is called by an Ajax call, It returns a the update content for the channel select
 *
 */

// Initialize system
require_once dirname(__FILE__).'/../../initWebApp.php';

// Get current project
$project = wcmProject::getInstance();

$bizobjectId = getArrayParameter($_REQUEST, 'bizobjectId', null);


// No browser cache
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', false );
header( 'Pragma: no-cache' );

// XML output
header("Content-Type: text/xml");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

// Write ajax response
echo "<ajax-response>\n";
echo "<response type=\"item\" id=\"issueDiv\"><![CDATA[";
wcmGUI::renderDropdownField('issueId', publication::getIssueList($bizobjectId), '', _BIZ_ISSUE);
echo "]]></response>";
echo "</ajax-response>";
