<?php 
/**
 * Project:     WCM
 * File:        ajax/autocomplete/wcm.geoloccountrylist.php
 *
 * @copyright   (c)2010 Relaxnews
 * @version     4.x
 * @author		LSJ
 *
 */
 
// Initialize the system
require_once dirname(__FILE__).'/../../../initWebApp.php';
$session = wcmSession::getInstance();
$prefix = getArrayParameter($_REQUEST, "prefix", '');
$max = getArrayParameter($_REQUEST, "max", 15);
$mySite = $session->getSite();
$lang = $mySite->language;

echo '<ul style="padding: 5px 5px;margin: 0;border-bottom: 1px solid #999;border-right: 1px solid #999; width: 100%">';

function generateListe($prefix, $max, $lang) {
    $enum = new country;
	$config = wcmConfig::getInstance();
	$url = $config['wcm.backOffice.url'].'business/ajax/autocomplete/wcm.locationGeolocCityList.php';
    
    if (!$enum->beginEnum("country LIKE '".$prefix."%'", "country ASC LIMIT 0, $max"))
        return null;
    while ($enum->nextEnum()) {
        echo '<li id="'.$enum->geonameId.'" style="display: block; -moz-border-radius: 3px;padding: 0 100px 0 5px;cursor: pointer;margin-top: 2px;" title="'.$enum->country.' ('.$enum->ISO.')" onClick="updateCountry(this.id, \''.$enum->country.'\', \''.$enum->ISO.'\', \''.$url.'\');">';
		if($enum->getAlternateName($lang) == "" || $lang == "en") echo $enum->country;
		else echo $enum->getAlternateName($lang). " (". $enum->country.")";
        echo '</li>';
    }
    $enum->endEnum();
	
	unset ($enum);
}

generateListe($prefix, $max, $lang);

echo '</ul>';
