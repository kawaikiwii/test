<?php 
require_once (dirname(__FILE__).'/inc/wcmInit.php');

$CURRENT_SITECODE 		= getDefaultSiteCode();;
$DISABLED_ACCESS 		= false;
$ANNOUNCE_MAINTENANCE 	= false;

require_once (dirname(__FILE__).'/inc/siteInit.php');

if (isset($session->userId) && $session->userId) {
    $session->ping();
    include (dirname(__FILE__).'/sites/'.$site->code.'/app.php');
    exit();
}
include (dirname(__FILE__).'/sites/'.$site->code.'/index.php');
?>
