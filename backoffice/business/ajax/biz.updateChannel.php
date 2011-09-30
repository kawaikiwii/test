<?php

/**
 * Project:     WCM
 * File:        biz.updateChannel.php
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

// Retrieve (some) parameters
$siteId = getArrayParameter($_REQUEST, "siteId", null);
$divName = getArrayParameter($_REQUEST, "divName", null);
$selectName = getArrayParameter($_REQUEST, "selectName", null);

$site = new site($project, $siteId);

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
echo "<response type=\"item\" id=\"".$divName."\"><![CDATA[";

echo '<select name="'.$selectName.'" id="'.$selectName.'" style="width:100%">';
echo '<option value="">('._BIZ_ALL.')</option>';
$where = "parentId is null";
if($siteId != "")
    $where .= " AND siteId =".$siteId;
renderHtmlChannelOptions($project, null, $where);
echo '</select>';

echo "]]></response>";
echo "</ajax-response>";

?>
