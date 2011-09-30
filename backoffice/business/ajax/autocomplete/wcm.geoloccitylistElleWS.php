<?php 
/**
 * Project:     WCM
 * File:        ajax/autocomplete/wcm.geoloccitylistElle.php
 *
 * @copyright   (c)2011 Relaxnews
 * @version     4.x
 * @author		CC
 *
 */
 
// Initialize the system
require_once dirname(__FILE__).'/../../../initWebApp.php';
include(WCM_DIR . '/includes/services/Geonames/Geonames.php');
$session = wcmSession::getInstance();
$prefix = getArrayParameter($_REQUEST, "prefix", '');
$max = getArrayParameter($_REQUEST, "max", 15);
$mySite = $session->getSite();
$lang = $mySite->language;

echo '<ul style="padding: 5px 5px;margin: 0;border-bottom: 1px solid #999;border-right: 1px solid #999; width: 100%; position: absolute; left: 20px;">';

function generateListe($prefix, $max, $lang) 
{	
	$options = array( 'username' => 'spajot' );
	
	$g = new Bgy_Service_Geonames($options);
	//print_r($g->getSupportedMethods());

    $results  = $g->search(array(
    "name_startsWith" => $prefix, 
    "country" => array("FR","MC"), 
    //"featureCode" => array("PPL","PPLA","PPLX","PPLQ","PPLL","PPLS","PPLW","PPLR","PPLG","PPLC","PPLA2"), 
    "featureCode" => array("PPL","PPLA","PPLS","PPLA2"), 
    "style" => "full", 
    "maxRows" => $max)
    );
	
    //print_r($results);
    
    foreach ($results as $result) 
	{
		// init des variables country, region et codePostal
		$country = $result["countryName"];
		$region = $result["adminName1"];
		$codePostal = "";		
		
		// cas où l'on a plusieurs occurences sur la même variable, on force la création d'un tableau pour accéder à toutes les valeurs
		if (isset($result["alternateNames"]) && is_array($result["alternateNames"]))
		{
			foreach ($result["alternateNames"] as $value)
			{
				if (isset($value["lang"]) && ($value["lang"]=="post")) $codePostal = $value["name"];
			}
		}
				
		// init de la liste de résultat avec toutes les valeurs
		echo '<li onclick="document.getElementById(\'region\').value =\''.addslashes($region).'\';document.getElementById(\'zipcode\').value =\''.$codePostal.'\';document.getElementById(\'country\').value =\''.addslashes($country).'\'" id="'.$result["geonameId"].'" style="display: block; -moz-border-radius: 3px;padding: 0 100px 0 5px;cursor: pointer;margin-top: 2px;" title="'.$result["name"].' ('.$region.' - '.$country.')">';
		echo $result["name"];
        echo '</li>';
        
	}
	
	/*
	// url du webservice geonames
	$xml = 'http://ws.geonames.org/search?name_startsWith='.$prefix.'&maxRows='.$max.'&country=FR&country=MC&featureCode=PPL&featureCode=PPLA&featureCode=PPLX&featureCode=PPLQ&featureCode=PPLL&featureCode=PPLS&featureCode=PPLW&featureCode=PPLR&featureCode=PPLG&featureCode=PPLA2&style=full';
	//echo $xml;
	$elements = simplexml_load_file($xml);
	
	foreach ($elements->geoname as $result) 
	{
		// on caste le resulat pour avoir une version tableau et non objet ( pb d'accès à certains résultats )
		$tabResult = (array) $result;
		
		// init des variables country, region et codePostal
		$country = $result->countryName;
		$region = $result->adminName1;
		$codePostal = "";		
		
		// cas où l'on a plusieurs occurences sur la même variable, on force la création d'un tableau pour accéder à toutes les valeurs
		if (isset($tabResult["alternateName"]))
		{
			$cp = (array) $tabResult["alternateName"];
			
			foreach ($cp as $value)
			{
				if (is_numeric($value)) $codePostal = $value;
			}
		}
				
		// init de la liste de résultat avec toutes les valeurs
		echo '<li onclick="document.getElementById(\'region\').value =\''.addslashes($region).'\';document.getElementById(\'zipcode\').value =\''.$codePostal.'\';document.getElementById(\'country\').value =\''.addslashes($country).'\'" id="'.$result->geonameId.'" style="display: block; -moz-border-radius: 3px;padding: 0 100px 0 5px;cursor: pointer;margin-top: 2px;" title="'.$result->name.' ('.$region.' - '.$country.')">';
		echo $result->name;
        echo '</li>';
	}	
	*/	
}

generateListe($prefix, $max, $lang);

echo '</ul>';
