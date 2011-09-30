<?php
/**
 * Project:     WCM
 * File:        wcm.templateSelector.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     3.2
 */

// Initialize system
require_once dirname(__FILE__).'/../initWebApp.php';

$file = getArrayParameter($_REQUEST, 'file', null);
$path = getArrayParameter($_REQUEST, 'path', null);
$mode = getArrayParameter($_REQUEST, 'mode', null);
$uploadedFile = getArrayParameter($_REQUEST, 'uploadedFile', null);

$code = '';
if (!$file && !$path)
{
    $code .= 'File ' . $path . '/' . $file . ' not found';
}
else
{
    $code .= '<pre>';
    $code .= htmlentities(file_get_contents($path . '/' . $file));
    $code .= '</pre>';
    $filesize = filesize($path . '/' . $file) . ' bytes';
}

// No browser cache
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', false );
header( 'Pragma: no-cache' );

// Xml output
header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";

// Write ajax response
echo '<ajax-response>' . "\n";
echo '<response type="item" id="selectedtemplate">' . "\n";
echo '<![CDATA[';
// Return command result
echo $code;
echo ']]>';
echo '</response>' . "\n";
echo '<response type="item" id="filesize">' . "\n";
echo '<![CDATA[';
// Return command result
echo $filesize;
echo ']]>';
echo '</response>' . "\n";
echo '</ajax-response>' . "\n";