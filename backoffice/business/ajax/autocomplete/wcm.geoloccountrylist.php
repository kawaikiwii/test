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
//$geonameId = getArrayParameter($_REQUEST, "geonameId", '');
//$countryName = getArrayParameter($_REQUEST, "countryName", '');
$max = getArrayParameter($_REQUEST, "max", 15);
$mySite = $session->getSite();
$lang = $mySite->language;

echo '<ul style="padding: 5px 5px;margin: 0;border-bottom: 1px solid #999;border-right: 1px solid #999; width: 100%">';

//function generateListe($prefix, $countryName, $max, $lang) {
function generateListe($prefix, $max) {
//    $enum = new alternate_names;
//    if (!$enum->beginEnum("alternate_name LIKE '".$prefix."%' AND isolanguage = '".$lang."'", "isPreferredName DESC, alternate_name ASC LIMIT 0, $max"))
    $enum = new country;
    
    if (!$enum->beginEnum("country LIKE '".$prefix."%'", "country ASC LIMIT 0, $max"))
        return null;
    while ($enum->nextEnum()) {
//        echo '<li id="'.$enum->geonameId.'" style="display: block; -moz-border-radius: 3px;padding: 0 100px 0 5px;cursor: pointer;margin-top: 2px;" title="'.$enum->geonameId.'">';
//        echo $enum->alternate_name;
//        echo '</li>';
        echo '<li id="'.$enum->geonameId.'" style="display: block; -moz-border-radius: 3px;padding: 0 100px 0 5px;cursor: pointer;margin-top: 2px;" title="'.$enum->country.' ('.$enum->ISO.')">';
        echo $enum->country;
        echo '</li>';
    }
    $enum->endEnum();
	
	unset ($enum);
}

generateListe($prefix, $max);

echo '</ul>';
