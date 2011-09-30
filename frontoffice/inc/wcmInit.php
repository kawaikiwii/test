<?php 
// Initialize WCM API
if (!defined('WCM_CONFIG_FILE')) {
    define('WCM_CONFIG_FILE', realpath(dirname(__FILE__).'/../wcm/xml/configuration.xml'));
}

require_once (dirname(__FILE__).'/../wcm/initApi.php');
$config = wcmConfig::getInstance();
$project = wcmProject::getInstance();
$session = wcmSession::getInstance();

// Set error reporting level
error_reporting($config['wcm.errorHandling.reportLevel']);



require_once (dirname(__FILE__).'/../wcm/business/api/toolbox/biz.relax.toolbox.php');
//require_once (dirname(__FILE__).'/../wcm/globalsVars.php');
// Initialize FrontOffice API
require_once (dirname(__FILE__)."/../api/wcm.siteSearcher.php");
require_once (dirname(__FILE__)."/../api/biz.binControl.php");
require_once (dirname(__FILE__)."/../api/biz.misc.php");
require_once (dirname(__FILE__)."/../api/biz.site.php");

// Configure Minify API
set_include_path(dirname(__FILE__).'/../min/lib'.PATH_SEPARATOR.get_include_path());
require 'Minify/Build.php';
$_gc = ( require dirname(__FILE__)."/../min/groupsConfig.php");

$CURRENT_USER = $CURRENT_ACCOUNT= NULL;
if (isset($session->userId) && $session->userId) {
    $CURRENT_USER = $session->getUser();
    $CURRENT_ACCOUNT = new account();
    $CURRENT_ACCOUNT->refreshByWcmUser($CURRENT_USER->id);
    
    $CURRENT_ACCOUNT_MANAGER = new wcmUser();
    $CURRENT_ACCOUNT_MANAGER->refresh($CURRENT_ACCOUNT->managerId);
}
?>
