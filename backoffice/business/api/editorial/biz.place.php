<?php

/**
 * Project:     WCM
 * File:        biz.place.php
 *
 * @copyright   (c)2011 Relaxnews
 * @version     4.x
 *
 */
 /**
 * Definition of a place
 */

class place extends bizobject
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
    
    public $country;
      
    public $phone;
    
	public $email;
	
	public $website;

	public $facebook;
   
   
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
		if (parent::save($source))
		{
			// force indexation
			$this->index();
        	return $this->generate(false);   
		} 	
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

