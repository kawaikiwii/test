<?php 
require_once (dirname(__FILE__).'/inc/wcmInit.php');

$CURRENT_SITECODE 		= getDefaultSiteCode();;
$DISABLED_ACCESS 		= false;
$ANNOUNCE_MAINTENANCE 	= false;

require_once (dirname(__FILE__).'/inc/siteInit.php');


include (dirname(__FILE__).'/sites/'.$site->code.'/lostpass.php');
?>
