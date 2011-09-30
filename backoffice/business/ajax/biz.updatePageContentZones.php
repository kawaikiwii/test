<?php

/**
 * Project:     WCM
 * File:        biz.updateChannelPageContentZones.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * This page is called by an Ajax call. It returns the update content for the channel
 * design 'pageContent' div (see ../modules/mod_channel_design.php).
 *
 */

// Initialize system
require_once dirname(__FILE__).'/../../initWebApp.php';

// Get current project
$project = wcmProject::getInstance();

// Retrieve (some) parameters
$elementId = getArrayParameter($_REQUEST, "elementId", null);
$disabled = getArrayParameter($_REQUEST, "disabled", 0);
$bizobjectTemplateZones = getArrayParameter($_REQUEST, "bizobjectTemplateZones", null);

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
echo "<![CDATA[";

$options = array();
// FIXME $_REQUEST is double encoded. Needs to be decoded twice. Is a hack.
foreach (explode(',', urldecode($bizobjectTemplateZones)) as $pair)
{
  list($name, $value) = explode(':', urldecode($pair));
  $options[$name] = $value;
}

$attributes = array();
$attributes['name'] = $elementId.'_Select';
$attributes['id'] = $elementId.'_Select';
if ($disabled) $attributes['disabled'] = 'disabled';
wcmGUI::renderDropdownField($elementId.'_Select', $options, '', _BIZ_WIDGET_ZONE, $attributes);

echo "]]>";
echo "</response>\n";
echo "</ajax-response>\n";
?>
