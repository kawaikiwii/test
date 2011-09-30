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
$session = wcmSession::getInstance();
$prefix = getArrayParameter($_REQUEST, "prefix", '');
$max = getArrayParameter($_REQUEST, "max", 15);
$mySite = $session->getSite();
$lang = $mySite->language;

echo '<ul style="padding: 5px 5px;margin: 0;border-bottom: 1px solid #999;border-right: 1px solid #999; width: 100%; position: absolute; left: 20px;">';

function generateListe($prefix, $max, $lang) 
{
    $enum = new city;
    
    // attention cette liste se base sur les communes de FRANCE
    if (!$enum->beginEnum("name like '".$prefix."%' AND ( countryCode='FR' OR countryCode='MC')", "name ASC LIMIT 0, ".$max))
        return null;
        
    while ($enum->nextEnum()) 
    {
    	$admin1 = "";
    	$admin2 = "";
		$countryName = "";
		
    	if($enum->admin1 != "") 
    	{
    		$obj2 = new admin1Codes;
		    if (!$obj2->beginEnum("admin1 = '".$enum->countryCode.".".$enum->admin1."'", "asciiname ASC LIMIT 0, 1"))
		        return null;
		        
			while ($obj2->nextEnum()) 
				$admin1 .= "-".$obj2->asciiname;
			
			unset ($obj2);
    	}
    	
    	if($enum->admin2 != "") 
    	{
    		$obj3 = new admin2Codes;
		    if (!$obj3->beginEnum("admin2 = '".$enum->countryCode.".".$enum->admin2."'", "asciiname ASC LIMIT 0, 1"))
		        return null;
			
		    while ($obj3->nextEnum()) 
				$admin2 .= "-".$obj3->asciiname;
			
			unset ($obj3);    		
    	}
    	
    	// init code postal, région, pays
    	$region = substr($admin1,1);
    	$codePostal = "";
    	$cp = explode(",", $enum->alternateNames);
    	if (is_array($cp)) $codePostal =  $cp[0];
    	else $codePostal =  $enum->alternateNames;
    	
    	$pays = new country();
    	$country = $pays->getCountryNameByCode($enum->countryCode);
    	
        echo '<li onclick="document.getElementById(\'region\').value =\''.addslashes($region).'\';document.getElementById(\'zipcode\').value =\''.$codePostal.'\';document.getElementById(\'country\').value =\''.addslashes($country).'\'" id="'.$enum->geonameId.'" style="display: block; -moz-border-radius: 3px;padding: 0 100px 0 5px;cursor: pointer;margin-top: 2px;" title="'.$enum->name.' ('.$country.$admin1.$admin2.')">';
		echo $enum->name;
        echo '</li>';
    }
    $enum->endEnum();
	
	unset ($enum);
}

generateListe($prefix, $max, $lang);

echo '</ul>';
