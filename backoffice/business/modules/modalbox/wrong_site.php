<?php

require_once dirname(__FILE__).'/../../../initWebApp.php';

$session = wcmSession::getInstance();
$config = wcmConfig::getInstance();

$currentSite = new site();
$objectSite = new site();
$currentSite->refresh($_POST['currentSiteId']);
$objectSite->refresh($_POST['objectSiteId']);

?>
<?php echo _BIZ_SWITCH_TO_OBJECT_SITE; ?> (<i><?php echo $objectSite->title; ?></i>).

