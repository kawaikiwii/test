<?php

/**
 * Project:     WCM
 * File:        updatePortalZone.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

// Initialize system
require_once dirname(__FILE__).'/../../initWebApp.php';

// Get current project, etc.
$project = wcmProject::getInstance();
$layout  = $project->layout;

/*
 * $_REQUEST contains specific variables:
 *
 * bizclass => classname of the bizobject concerned
 * bizid    => id of the bizobject concerned
 * cmd      => command to execute: reset, update
 * zone     => name of specific zone concerned ('' => all zones)
 * content  => zone content(s) for cmd == 'update'
 * updateDb => True if command is to update DB
 *
 * If a zone is specified:
 *
 * content == <block-1>/<title-1>~<block-2>/<title-2>~...
 *
 * Otherwise:
 *
 * content == <zone-1>_<content-1>|<zone-2>_<content-2>|...
 *
 * where each <content-i> is as for a specified zone.
 *
 */
$bizclass = getArrayParameter($_REQUEST, "bizclass", '');
$bizid    = getArrayParameter($_REQUEST, "bizid",    0);
$cmd      = getArrayParameter($_REQUEST, "cmd",      '');
$zone     = getArrayParameter($_REQUEST, "zone",     '');
$content  = getArrayParameter($_REQUEST, 'content',  null);
$updateDb = getArrayParameter($_REQUEST, 'updateDb', 'false') == 'true';

// Instanciate bizobject
$bizobject = new $bizclass($project, $bizid);

if($zone)
    $designZone = new wcmDesignZone($bizclass, $bizid, $zone);

// No browser cache
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', false );
header( 'Pragma: no-cache' );

// Handle command
switch($cmd)
{
case "reset":
    if ($zone)
    {
        // Reset zone content in session, and in DB if $updateDb is true
        $designZone->delete($updateDb);
    }
    else
    {
        // Reset zone contents (plural!) in session, and in DB if $updateDb is true
        $layout->resetZoneContents($bizobject, $updateDb);
    }
    break;

case "update":
    // URL-decode zone content
    $content = urldecode($content);
    
    if ($zone)
    {
        if($content!='') {
            $temp = split('~', $content);
            foreach($temp as $value)
            {
                list($name, $value) = split('-', $value,2);
                list($pos, $value) = split('/', $value, 2);
                $designZone->addWidget($name);
            }
        }
        $designZone->save($updateDb);
    }
    else
    {
        // Parse zone contents (plural!)
        $zones = array();
        $contents = explode('|', $content);
        foreach ($contents as $content)
        {
            list($zoneName, $zoneContent) = explode('_', $content, 2);
            $designZone = new wcmDesignZone($bizclass, $bizid, $zoneName);

            if($zoneContent!='') {

                $temp = split('~', $zoneContent);
                foreach($temp as $value)
                {
                    list($name, $value) = split('-', $value,2);
                    list($pos, $value) = split('/', $value, 2);
                    $designZone->addWidget($name);
                }
            }
            $designZone->save($updateDb);
        }

    }
    break;
}

?>
