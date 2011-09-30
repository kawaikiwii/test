<?php
// Initialize WCM API
if (!defined('WCM_CONFIG_FILE'))
{
    define('WCM_CONFIG_FILE', realpath(dirname( __FILE__ ).'/../wcm/xml/configuration.xml'));
}

require_once (dirname( __FILE__ ).'/../wcm/initWebApp.php');
$config = wcmConfig::getInstance();
$project = wcmProject::getInstance();
$session = wcmSession::getInstance();
// Set error reporting level
error_reporting($config['wcm.errorHandling.reportLevel']);

require_once (dirname( __FILE__ ).'/../wcm/business/api/toolbox/biz.relax.toolbox.php');



?>
