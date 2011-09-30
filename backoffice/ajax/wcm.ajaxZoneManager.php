<?php

// Initialize system
require_once dirname(__FILE__).'/../initWebApp.php';

// No browser cache
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', false );
header( 'Pragma: no-cache' );

// Xml output
header("Content-Type: text/xml");
echo '<?xml version="1.0" encoding="UTF-8"?>'. "\n";
echo '<ajax-response>' . PHP_EOL;


// Les données du context
$bizClass = getArrayParameter($_REQUEST, 'bizClass', '');
$bizId = getArrayParameter($_REQUEST, 'bizId', '');
$bizobject = new $bizClass(wcmProject::getInstance(), $bizId);


parse_str($_REQUEST['zoneContent'], $list);
parse_str(getArrayParameter($_REQUEST, 'settings',''),$settings);

// If the zone is empty juste create the tab index to clear it
if(count($list) === 0)
        $list[$_REQUEST['zoneName']] = '';

// Save new zone content
wcmProject::getInstance()->layout->setZoneContents($bizobject, $list, $settings);

// Erase cache
$config = wcmConfig::getInstance();
eraseDirectory($config['wcm.webSite.path'] . 'cache/' . $bizobject->getClass() . '/' . $bizobject->id);

echo '</ajax-response>'. PHP_EOL;

