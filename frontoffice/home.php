<?php

require_once (dirname(__FILE__).'/inc/wcmInit.php');

if (isset($session->userId) && $session->userId) {
    $session->ping();
	$site = $session->getSite();
    include (dirname(__FILE__).'/sites/'.$site->code.'/home.php');
    exit();
}

?>