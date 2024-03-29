<?php 
/**
 * Project:     WCM
 * File:        ajax/autocomplete/wcm.geoloccitylist.php
 *
 * @copyright   (c)2010 Relaxnews
 * @version     4.x
 * @author		LSJ
 *
 */
 
// Initialize the system
require_once dirname(__FILE__).'/../../../initWebApp.php';
include(WCM_DIR . '/includes/services/Geonames/Geonames.php');
$session = wcmSession::getInstance();
$prefix = getArrayParameter($_REQUEST, "prefix", '');
$max = getArrayParameter($_REQUEST, "max", 30);
$countryCode = getArrayParameter($_REQUEST, "countryCode", '');

$mySite = $session->getSite();
$lang = $mySite->language;

echo '<ul style="padding: 5px 5px;margin: 0;border-bottom: 1px solid #999;border-right: 1px solid #999; width: 100%">';

function generateListe($prefix, $max, $lang, $countryCode) 
{
	$options = array( 'username' => 'spajot' );
	$g = new Bgy_Service_Geonames($options);
	
    $results  = $g->search(array(
    "name_startsWith" => $prefix, 
    "country" => array($countryCode), 
    //"featureCode" => array("PPL","PPLA","PPLX","PPLQ","PPLL","PPLS","PPLW","PPLR","PPLG","PPLC","PPLA2"), 
    "featureCode" => array("PPL","PPLA","PPLS","PPLA2"), 
    "style" => "full", 
    "maxRows" => $max)
    );
	
    foreach ($results as $result) 
	{
		// init des variables country, region et codePostal
		$country = $result["countryName"];
		$region = $result["adminName1"];			
		// init de la liste de résultat avec toutes les valeurs
		echo '<li onclick="parent.document.getElementById(\'city\').value =\''.$result["name"].'\';parent.document.getElementById(\'cityId\').value =\''.$result["geonameId"].'\';" id="'.$result["geonameId"].'" style="display: block; -moz-border-radius: 3px;padding: 0 100px 0 5px;cursor: pointer;margin-top: 2px;" title="'.$result["name"].' ('.$region.' - '.$country.')">';
		echo $result["name"];
        echo '</li>';      
	}
		
	/*
	// mise en place d'une jointure avec la table biz_alternate_names pour avoir la version anglaise du nom de la ville
	$project = wcmProject::getInstance();
	$connector = $project->datalayer->getConnectorByReference("geoloc");
	$db = $connector->getBusinessDatabase();
	$checkCountryCode = "";
	
	//ajout du contryCode dans la requete pour filter les résultats
	if (!empty($countryCode))
    	$checkCountryCode = "AND C.countryCode='".$countryCode."'";	
    	
    $query = "SELECT * FROM biz_city AS C INNER JOIN biz_alternate_names AS A ON C.geonameId=A.geonameId WHERE A.isolanguage='en' ".$checkCountryCode." AND A.alternate_name LIKE '".$prefix."%' ORDER BY A.alternate_name ASC LIMIT 0, ".$max;	
    	    	
    $rs = $db->ExecuteQuery($query);
	// pour être sur d'avoir un résultat sinon on bascule sur l'autocomplet standard
	$rs2 = $db->ExecuteQuery($query);
	
	if ($rs != null && $rs2->next())
	{
		while ($rs->next())
	    {
			echo '<li onclick="parent.document.getElementById(\'city\').value =\''.$rs->get('alternate_name').'\';parent.document.getElementById(\'cityId\').value =\''.$rs->get('geonameId').'\';" id="'.$rs->get('geonameId').'" style="display: block; -moz-border-radius: 3px;padding: 0 100px 0 5px;cursor: pointer;margin-top: 2px;" title="'.$rs->get('alternate_name').'">';
			echo $rs->get('alternate_name');
		    echo '</li>';
	    }
	}
	else 
	{	
		$enum = new city;
	    
	    if (!empty($countryCode))
	    	$sql = "name like '".$prefix."%' AND countryCode='".$countryCode."'";
	    else 
	    	$sql = "name like '".$prefix."%'";  	
	    
	    if (!$enum->beginEnum( $sql, "name ASC LIMIT 0, $max"))
	        return null;
	        
	    while ($enum->nextEnum()) {
	    	$admin1 = "";
	    	$admin2 = "";
			$countryName = "";
	    	if($enum->admin1 != "") {
	    		$obj1 = new country;
			    if (!$obj1->beginEnum("ISO = '".$enum->countryCode."'", "country ASC LIMIT 0, 1"))
			        return null;
				while ($obj1->nextEnum()) {
					$countryName .= $obj1->country;
				}
				unset ($obj1);
	    	}
	    	if($enum->admin1 != "") {
	    		$obj2 = new admin1Codes;
			    if (!$obj2->beginEnum("admin1 = '".$enum->countryCode.".".$enum->admin1."'", "asciiname ASC LIMIT 0, 1"))
			        return null;
				while ($obj2->nextEnum()) {
					$admin1 .= "-".$obj2->asciiname;
				}
				unset ($obj2);
	    	}
	    	if($enum->admin2 != "") {
	    		$obj3 = new admin2Codes;
			    if (!$obj3->beginEnum("admin2 = '".$enum->countryCode.".".$enum->admin2."'", "asciiname ASC LIMIT 0, 1"))
			        return null;
				while ($obj3->nextEnum()) {
					$admin2 .= "-".$obj3->asciiname;
				}
				unset ($obj3);  
						
	    	}
	    	switch(strtolower($enum->countryCode)) {
	    		case "us"||"gb":
	    			$lib = $enum->countryCode.'-'.$enum->admin1;
	    			break;
				default:
					$lib = $enum->countryCode.'-'.$enum->admin2;				
	    	}
	    	
	    	$value = "";
	    	// récupère l'alternateName par défaut en Anglais
	    	//$alternateName = new alternate_names();
	    	//$value = $alternateName->getNameByGeonameId($enum->geonameId, "en");
	    	
	    	if (empty($value)) $value = $enum->name;
	    	
	        echo '<li onclick="parent.document.getElementById(\'city\').value =\''.$value.'\';parent.document.getElementById(\'cityId\').value =\''.$enum->geonameId.'\';" id="'.$enum->geonameId.'" style="display: block; -moz-border-radius: 3px;padding: 0 100px 0 5px;cursor: pointer;margin-top: 2px;" title="'.$value.' ('.$countryName.$admin1.$admin2.')">';
			echo $value;
	        //echo $enum->name.' ('.$countryName.$admin1.$admin2.')';
	        echo '</li>';
	    }
	    $enum->endEnum();
		
		unset ($enum);
	}
	*/
}

generateListe($prefix, $max, $lang, $countryCode);

echo '</ul>';
