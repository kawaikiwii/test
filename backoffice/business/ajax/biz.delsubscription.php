<?php

/**
 * Project:     WCM
 * File:        biz.delsubscription.php
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
$newsletter_id = getArrayParameter($_REQUEST, "newsletter_id", null);
$subscription_id = getArrayParameter($_REQUEST, "subscription_id", null);

// No browser cache
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', false );
header( 'Pragma: no-cache' );

$subscription = new subscription($subscription_id);
$subscription->refresh($subscription_id);
if($subscription->delete())
{
	// XML output
	header("Content-Type: text/html");
	
	// Write ajax response
	echo "1";
}
?>