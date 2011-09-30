<?php

/**
 * Project:     WCM
 * File:        managelist.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * This page is called by an Ajax call, It processes modifications to saved searches and bins (later)
 *
 */

// Initialize system
require_once dirname(__FILE__).'/../../initWebApp.php';

// Get current project
$project = wcmProject::getInstance();

// Retrieve (some) parameters
$object_id = getArrayParameter($_REQUEST, "object_id", null);
$object_class = getArrayParameter($_REQUEST, "object_class", null);
$action = getArrayParameter($_REQUEST, "action", null);
$params = getArrayParameter($_REQUEST, "params", null);

// No browser cache
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', false );
header( 'Pragma: no-cache' );

$object = new $object_class($object_id);

switch ($action) 
{
	case 'delete':
		echo $object->delete();
    	break;
	
	case 'updateshared':
		$params = json_decode($params);
		$object->shared = $params->params->value;
		echo $object->save();
		break;
	case 'updateshowui':
		$session = wcmSession::getInstance(); 
		$params = json_decode($params);
		$show = $params->params->value;
		$userId = $session->userId;
		
		$showui = ($object->showui == "")?array():json_decode($object->showui);
		if($show)
		{
			$showui[] = $userId;
			$object->showui = json_encode($showui);
		}
		else
		{
			unset($showui[array_search($userId, $showui)]);
			$object->showui = json_encode($showui);
		}
		echo $object->save();
		break;
}


?>