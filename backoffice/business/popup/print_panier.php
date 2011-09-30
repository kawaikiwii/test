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
$config = wcmConfig::getInstance();
$bo_url = $config['wcm.backOffice.url'];

$bizobject = new wcmBin($project);

if (isset($_GET['template']) && isset($_GET['binid']))
{
	$params = array( 'binid' => $_GET['binid'] );
	
	echo "<html>";
	echo "<head>";
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$bo_url."skins/main.css\" />";
	echo "</head>";
	echo "<body onload='window.print()'>";

    $tg = new wcmTemplateGenerator();
    echo $tg->executeTemplate($_GET['template'], $params);
	
    echo "</body>";
	echo "</html>";
	
}

	