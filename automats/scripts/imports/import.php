<?php
require_once (dirname( __FILE__ ).'/../../inc/wcmInit.php');

switch ($argv[1]) {
case "carbeo":
	require 'carbeo/biz.carbeo.php';
	$import = new wcmImportCarbeo(array("siteId" => 6));
	$import->process();
	break;
case "airquality":
	require 'airquality/biz.airquality.php';
	$import = new wcmImportAirquality(array("siteId" => 6));
	$import->process();
	break;
case "allocine_top5":
	require 'allocine/biz.alloCineTopBA.php';
	$import = new wcmImportAlloCineTopBA(array("siteId" => 6, "top" => 5));
	$import->process();
	break;
case "allocine_top10":
	require 'allocine/biz.alloCineTopBA.php';
	$import = new wcmImportAlloCineTopBA(array("siteId" => 6, "top" => 10));
	$import->process();
	break;
}


/*
 *  /usr/bin/php /var/www/dev/automats/scripts/imports/import.php airquality
 *  
 *  > 
 * 
 * */

//repertoire de travail tmp : /tmp/wcm/

?>
