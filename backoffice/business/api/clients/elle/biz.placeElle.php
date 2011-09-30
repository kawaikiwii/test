<?php

/**
 * Project:     WCM
 * File:        biz.placeElle.php
 *
 * @copyright   (c)2011 Relaxnews
 * @version     4.x
 *
 */
 /**
 * Definition of a place
 */

class placeElle extends bizobject
{ 
    /**
     * (int) site id
     */
    public $siteId;
   
    /**
     * (int) channel id
     */
    public $channelId;
	
     /**
     * (array int) channel ids
     */   
	 public $channelIds;
    /**
     * (array int) list ids
     */   
    public $listIds;
	
	 /**
     * (array int) folders ids
     */   
	public $folderIds;
   
   
    /**
     * (string) default title
     */
    public $title;
    
    public $address;

    public $zipcode;
    
    public $city;
    
    public $region;
    
    public $country;
      
    public $phone;
    
	public $email;
	
	public $website;

	public $people;
	
	public $text;
	
	public $opening;
	public $price;
	
    public $latitude;
    public $longitude;
    
    public $theme;
    //public $themeDescription;
    
    public $zone_print;
    public $placeTitle;
    
    public $focus;
    
    private $tabIdRegions = array ( "Rhone-Alpes"=>"921",
			"Provence-Alpes-Cote d'Azur"=>"922",
			"Poitou-Charentes"=>"923",
			"Picardie"=>"924",
			"Pays de la Loire"=>"925",
			"Nord-Pas-de-Calais"=>"926",
			"Midi-Pyrenees"=>"927",
			"Lorraine"=>"928",
			"Limousin"=>"929",
			"Languedoc-Roussillon"=>"930",
			"Ile-de-France"=>"931",
			"Haute-Normandie"=>"932",
			"Franche-Comte"=>"933",
			"Corsica"=>"934",
			"Champagne-Ardenne"=>"935",
			"Centre"=>"936",
			"Bretagne"=>"937",
			"Bourgogne"=>"938",
			"Basse-Normandie"=>"939",
			"Auvergne"=>"940",
			"Aquitaine"=>"941",
			"Alsace"=>"942");
    
    /**
     * Set all initial values of an object
     * This method is invoked by the constructor
     */
    protected function setDefaultValues()
    {
    	$timestamp = time();
        $date_time_array = getdate($timestamp);
       
        $hours = $date_time_array['hours'];
        $minutes = $date_time_array['minutes'];
        $seconds = $date_time_array['seconds'];
        $month = $date_time_array['mon'];
        $day = $date_time_array['mday'];
        $year = $date_time_array['year'];
       
        $timestamp = mktime($hours, $minutes, $seconds, $month+6, $day, $year);
		
        parent::setDefaultValues();
        
    	$this->siteId = $this->channelId = 0;
    	//$this->versionNumber = 0;
    	// init workflowState
    	$this->workflowState = "published";
    }
   
	public function getChannels()
    {
    	$tabChannel = array();
    	$channels = bizobject::getBizobjects("channel", "workflowState='published' AND siteId=15", "id");
    	
    	foreach ($channels as $key=>$obj)
    		$tabChannel[$key] = $obj->title;
    	
    	asort($tabChannel); 
    		
        return $tabChannel;
    }
    
	public static function getThemes()
    {
    	return array( 	//"3419" => "3419 - Activités, restos, bons plans... tout pour les kids",
		    			"3419" => "3419 - ENFANTS, tout pour les occuper",
		    			"3420" => "3420 - Nos balades secrètes.",
		    			"3421" => "3421 - Best of des restos à moins de 30 €",
		    			"3322" => "3322 - Où croiser les people cet été ?",
		    			"3423" => "3423 - Spécial fête",
		    			"3324" => "3324 - Les meilleurs marchés");
    }
    
	public static function getZones()
    {
    	return array( 	"1" => "Aquitaine",
		    			"2" => "Charentes",
		    			"3" => "Pays de la loire",
		    			"4" => "Bretagne",
		    			"5" => "Normandie",
		    			"6" => "Nord Pas De Calais",
    	    			"7" => "Côte d'azur",
    	    			"8" => "Languedoc",
    	    			"9" => "Provence");
    }
    
	public function checkValidity()
    {
        // Attention cette fonction passe outre les tests d'origine faits dans wcm.bizobject.php
        // le pb est que la propriété publicationDate est de type dateTime or les tests initiaux
        // se basent sur un format de type date --> ce qui génère une erreur de type notice
       
        return true;
    }
	
	public function save($source = null, $skipGenerate = false)
	{	
		// on vire les espaces, points, tirets
		if (isset($source["phone"]) && !empty($source["phone"]))
		{
			$remove = array(' ', '.', '-');
			$source["phone"] = str_replace($remove, "", $source["phone"]);
		}
		
		// on s'assure que http:// soit bien rajouté dans l'url du site, gestion cas non renseigné et renseigné
		if (isset($source["website"]) && !empty($source["website"]))
		{
			$source["website"] = str_replace("http://", "", $source["website"]);
			$source["website"] = "http://".$source["website"];
		}
			
		// on associe les descriptions aux thèmes
		/*
		if (isset($source["theme"]) && !empty($source["theme"]))
		{
			// tableau de correspondance des descriptions de thèmes
			$themeDesc = array ( //"3419"=>"Loisirs sportifs et artistiques, clubs de plage, culture…mais aussi restos,  les bons plans des  mamans et enfants. Notre guide pour les 3-15 ans.",
								 "3419"=>"Partir en famille, c'est un vrai bonheur !  Surtout quand les enfants s'amusent... Notre sélection d'adresses pour un séjour réussi.",
								 "3420"=>"La région regorge de merveilles peu connues des touristes mais qui méritent largement la visite. Notre best-of.",
								 "3421"=>"Avec terrasse, avec vue, les pieds dans l’eau, à l’ombre d’un chêne… Nos bons plans pour se régaler à petits prix.",
								 "3322"=>"Shopping, plages, bars, restos, clubs… Toutes les adresses pour croiser les célébrités pendant leurs vacances.",
								 "3423"=>"Restos, clubs, bars où prendre l’apéro. Suivez le guide de la night.",
								 "3324"=>"Petits producteurs, spécialités à ramener… Nos adresses pour découvrir les plus beaux marchés de la région.");
			
			//on vérifie si l'id existe dans le tableau de correspondances
			if (isset($themeDesc[$source["theme"]]))
				$this->themeDescription = $themeDesc[$source["theme"]];
		}
		*/
		// on associe une id à la région selectionnée
		if (isset($source["region"]) && !empty($source["region"]))
		{
			//on vérifie si l'id existe dans le tableau de correspondance
			$cleanRegion = relaxGUI::remove_accents($source["region"]);
			if (isset($this->tabIdRegions[$cleanRegion]))
				$source["region"]= $this->tabIdRegions[$cleanRegion];		
		}
		
		// on ne récupère que les 6 premiers caractères du code postal
		if (isset($source["zipcode"]) && !empty($source["zipcode"]))
			$source["zipcode"] = substr($source["zipcode"], 0, 6);		
				
		if (parent::save($source))
		{
			// force indexation
			//$this->index();
        	return $this->generate(false);  
        	
		} 	
        else
			return false;
    }
	
    // correspondance id region -> nom
	public function getRegionLabel($id)
    {
    	$tabRegionsLabel = array_flip($this->tabIdRegions);
		
		if (isset($tabRegionsLabel[$id]))
			return $tabRegionsLabel[$id];
		else
			return false;
    }
    
	/**
	 * Gets the 'semantic' text that will be passed to the Text-Mining Engine
	 *
	 * @return string The semantic text to mine
	 */
	public function getSemanticText()
	{
	    $content = '';
	
		if ($this->title)
		    $content .= trim($this->title, " \t\n\r\0\x0B.").".\n";
		
		if ($this->xmlTags != NULL)
		{
			foreach($this->xmlTags['tags'] as $tag)
			{
				$content .= ','.$tag;
			}
		}
		
		$contents = $this->getContents();
		
		if (isset($contents) && is_array($contents))
		{
			foreach ($contents as $contentItem)
			{
				if ($contentItem->description != NULL)
				    $content .= trim($contentItem->description, " \t\n\r\0\x0B.").".\n";
				if ($contentItem->text != NULL)
				    $content .= trim($contentItem->text, " \t\n\r\0\x0B.").".\n";
			}
		}
		else { $content = ''; }
		
		return $content;
	}
	
	public function getPositionByAddress($addressLine, $postalCode, $city, $country)
    {
    	$config = wcmConfig::getInstance();
    	$position = array();
    	$timeSleep = 200000;
    	
    	$key = $config['wcm.apiGoogleMap.key'];
		$address = $addressLine." ".$postalCode." ".$city." ".$country;
    	$address = strtolower($address);
		$address = urlencode($address);
		$url = "http://maps.google.com/maps/geo?q=".$address."&output=xml&key=".$key;
		usleep($timeSleep);
		//wcmTrace("geoloc : ".$url);	 
		// Retrieve the URL contents
   		$page = file_get_contents($url);

	   	// Parse the returned XML file
	   	$xml = new SimpleXMLElement($page);
		
	   	if (!empty($xml->Response->Placemark->Point->coordinates))
	   	{
		   	// Parse the coordinate string
			list($longitude, $latitude) = explode(",", $xml->Response->Placemark->Point->coordinates);
			
			if (!empty($longitude) && !empty($latitude))
			{
				$position['longitude'] = $longitude;
				$position['latitude'] = $latitude;
				
				return $position;
			}
			else
				return false;
	   	}
	   	else
	   	{
	   		//On retire la rue
	   		$address = $postalCode." ".$city." ".$country;
	    	$address = strtolower($address);
			$address = urlencode($address);
			$url = "http://maps.google.com/maps/geo?q=".$address."&output=xml&key=".$key;
			usleep($timeSleep);
			//wcmTrace("geoloc : ".$url);	 
			// Retrieve the URL contents
	   		$page = file_get_contents($url);
	
		   	// Parse the returned XML file
		   	$xml = new SimpleXMLElement($page);
		   	
		   	if (!empty($xml->Response->Placemark->Point->coordinates))
		   	{
			   	// Parse the coordinate string
				list($longitude, $latitude) = explode(",", $xml->Response->Placemark->Point->coordinates);
				
				if (!empty($longitude) && !empty($latitude))
				{
					$position['longitude'] = $longitude;
					$position['latitude'] = $latitude;
					
					return $position;
				}
				else
					return false;
		   	}
		   	else
		   	{
		   		//On retire le code postal
		   		$address = $city." ".$country;
		    	$address = strtolower($address);
				$address = urlencode($address);
				$url = "http://maps.google.com/maps/geo?q=".$address."&output=xml&key=".$key;
				usleep($timeSleep);
				//wcmTrace("geoloc : ".$url);	 
				// Retrieve the URL contents
		   		$page = file_get_contents($url);
		
			   	// Parse the returned XML file
			   	$xml = new SimpleXMLElement($page);
			   	
			   	if (!empty($xml->Response->Placemark->Point->coordinates))
			   	{
				   	// Parse the coordinate string
					list($longitude, $latitude) = explode(",", $xml->Response->Placemark->Point->coordinates);
					
					if (!empty($longitude) && !empty($latitude))
					{
						$position['longitude'] = $longitude;
						$position['latitude'] = $latitude;
						
						return $position;
					}
					else
						return false;
			   	}
			   	else
			   	{
			   		//On retire la ville
			   		$address = $country;
			    	$address = strtolower($address);
					$address = urlencode($address);
					$url = "http://maps.google.com/maps/geo?q=".$address."&output=xml&key=".$key;
					usleep($timeSleep);
					//wcmTrace("geoloc : ".$url);	 
					// Retrieve the URL contents
			   		$page = file_get_contents($url);
			
				   	// Parse the returned XML file
				   	$xml = new SimpleXMLElement($page);
				   	
				   	if (!empty($xml->Response->Placemark->Point->coordinates))
				   	{
					   	// Parse the coordinate string
						list($longitude, $latitude) = explode(",", $xml->Response->Placemark->Point->coordinates);
						
						if (!empty($longitude) && !empty($latitude))
						{
							$position['longitude'] = $longitude;
							$position['latitude'] = $latitude;
							
							return $position;
						}
						else
							return false;
				   	}
				   	else
			   			return false;
			   	}
		   	}
	   	}
    }
}

