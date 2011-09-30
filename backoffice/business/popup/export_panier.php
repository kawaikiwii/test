<?php
/**
 * Project:     WCM
 * File:        popup/export_panier.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

// Initialize system
require_once dirname(__FILE__).'/../../initWebApp.php';

// Retrieve current session (or create new one)
$session = wcmSession::getInstance();
$project = wcmProject::getInstance();

$bizobject = new wcmBin($project);

if (isset($_GET['template']) && isset($_GET['binid']))
{
	$params = array( 'binid' => $_GET['binid'] );

    $tg = new wcmTemplateGenerator();
    echo $tg->executeTemplate($_GET['template'], $params);
}

	