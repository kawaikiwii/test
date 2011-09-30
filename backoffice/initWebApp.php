<?php
/**
 * Project:     WCM
 * File:        initWebApp.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

// Check configuration
// - Redirect to installation if needed
//
if (!file_exists(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'xml/configuration.xml'))
{
    header('Location: install');
    exit();
}
else
{
    // No browser cache
    header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
    header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
    header( 'Cache-Control: no-store, no-cache, must-revalidate' );
    header( 'Cache-Control: post-check=0, pre-check=0', false );
    header( 'Pragma: no-cache' );
    header( 'Content-Type: text/html;charset=UTF-8' );
}

// Initialize API and retrieve project, session and configuration
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'initApi.php');
$config  = wcmConfig::getInstance();
$project = wcmProject::getInstance();
$session = wcmSession::getInstance();

// Set error reporting level
error_reporting($config['wcm.errorHandling.reportLevel']);

// Change session working site?
if (isset($_REQUEST['_wcmSiteId']))
{
    $session->setSiteId($_REQUEST['_wcmSiteId']);
}
elseif (!isset($_SESSION['siteId']))
{
    $_SESSION['siteId'] = $config['wcm.default.siteId'];
}

// Set session language (check for change request)
$session->setLanguage(getArrayParameter($_REQUEST, '_wcmLanguage', 
                       $_SESSION['wcmSession']->getLanguage()));
// Change session action?
if (isset($_REQUEST['_wcmAction']))
{
    $session->setCurrentAction($_REQUEST['_wcmAction']);
}

if ($session->hasTimedOut())
{
    $session->logout();
    $_SESSION['wcm']['tmp']['previousActionBeforeTimeout'] = $session->getCurrentAction();
}
