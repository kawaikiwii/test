<?php

/**
 * Project:     WCM
 * File:        biz.alerte.php
 *
 * @copyright   (c)2009 Nstein Technologies
 * @version     4.x
 *
 */

// Initialize system
require_once dirname(__FILE__).'/../../initWebApp.php';

// Get current project
$project = wcmProject::getInstance();

// Retrieve parameters
$filename	= getArrayParameter($_REQUEST, "filename", null);
$divId	= getArrayParameter($_REQUEST, "divId", null);

$content = file_get_contents($filename);

// Response
header("Content-Type: text/xml");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
echo "<ajax-response>\n";
echo "<response type=\"item\" id=\"".$divId."\"><![CDATA[";
echo "<pre>".$content."</pre>";
echo "]]></response>";
echo "</ajax-response>";
