<?php

/**
 * Project:     WCM
 * File:        biz.event.php
 *
 * @copyright   (c)2008 Relaxnews
 * @version     4.x
 *
 */
 /**
 * Definition of an newstext
 */
class location extends bizobject
{

	/**
	 * (int) site id
	 */
	public $siteId;

	/**
	 * (int) channel id
	 */
	public $channelId;
	public $channelIds;

	/**
	 * (date) Publication date
	 */
	public $publicationDate;

	/**
	 * (date) Expiration date
	 */
	public $expirationDate;

	/**
	 * (string) default title
	 */
	public $title;

	/**
	 * (date) embargo date
	 */
	public $embargoDate;
	
	public $listIds;
	public $folderIds;
	
	
	public $address_1;
	public $address_2;
	public $zipcode;
	public $cityId;
	public $city;
	public $countryId;
	public $country;
	public $phone;
	public $email;
	public $website;



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
        //$this->publicationDate = $this->embargoDate = date('Y-m-d h:i:s');
        //$this->expirationDate = date('Y-m-d h:i:s', $timestamp);
        $this->siteId = $this->channelId = 0;
    }
   
    public function getCountryCodeById()
    {
    	if (!empty($this->countryId))
    	{
    		$country = new country();
    		return $country->getCountryCodeByGeonameId($this->countryId);
    	}
    	else
    	return false;
    }
    
    public function checkValidity()
    {
        // if (!parent::checkValidity()) return false;
       
        return true;
       
    }
	
	public function save($source = null)
    {
    	return parent::save($source);
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
		if ($this->xmlTags)
		{
			if (!is_array($this->xmlTags)) $xmlTags = unserialize($this->xmlTags);
			else $xmlTags = $this->xmlTags;
			
			foreach($xmlTags as $tag)
			{
				$content .= ','.$tag;
			}
		}
		
		$contents = $this->getContents();
		
		foreach ($contents as $contentItem)
		{
			if ($contentItem->description != NULL)
			    $content .= trim($contentItem->description, " \t\n\r\0\x0B.").".\n";
			if ($contentItem->text != NULL)
			    $content .= trim($contentItem->text, " \t\n\r\0\x0B.").".\n";
		}
		
		return $content;
	}

	public function countryInfos()
	{
		if($this->countryId == null && $this->country == null)
		return null;
		
		$enum = new country;
		if($this->countryId != null) {
			$request = "geonameId = ".$this->countryId;
		} else {
			$request = "country = '".$this->country."'";
		}
	    if (!$enum->beginEnum($request, "country ASC LIMIT 0, 1"))
	        return null;
	    while ($enum->nextEnum()) {
			// $infos[] = new country(null, $enum->id);
	        $result = new country(null, $enum->id);
			break;
	    }
	    $enum->endEnum();
		unset ($enum);
		return $result;
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
