<?php

/**
 * Project:     WCM
 * File:        biz.echoCity.php
 *
 * @copyright   (c)2010 Relaxnews
 * @version     4.x
 *
 */

/**
 * Definition of an article
 */
class echoCity extends bizobject
{
   /**
    * (int) site id
    */
    public $siteId;

   /**
    * (string) Title
    */
    public $title;

   /**
	 * (string) text
     */
   	public $description;
   	
    public $channelId;
    public $permalinks;
    public $kind;
    public $country;
    public $latitude;
    public $longitude;
    public $visa;
	public $web;
	public $climateDescription;
    public $populationDescription;
    public $languagesDescription;
    public $jetlagDescription;	
	public $populationNumber;
	public $populationDensity;
	public $climateTemperatureWinter;
	public $climateTemperatureSummer;
	public $officialLanguages;
	public $jetlagGmt;   
	public $mapCityCaption; 
	public $mapCityCredits; 	
	public $mapCityFile; 	
	public $mapTransportCaption;
	public $mapTransportCredits;
	public $mapTransportFile;		
	public $cityGeoNameId;  	
	public $countryGeoNameId;
	
    /**
     * Set all initial values of an object
     * This method is invoked by the constructor
     */
    protected function setDefaultValues()
    {
        parent::setDefaultValues();
        //$this->siteId = 0;
    }

    /**
     * CheckIn object in database and update search table
     *
     * @param array $source array for binding to class vars (or null)
     * @param int   $userId id of user who create ou update the document
     *
     * @return true on success, false otherwise
     *
     */
    public function checkin($source = null, $userId = null)
    {
        // Insert or update the article object
        if (!parent::checkin($source, $userId))
        {
        	wcmTrace("LES ECHOS : erreur checkin city ".$this->getClass());	
            return false;
        }

        return true;
    }
    
    
    /**
	* Returns data
	*
	* @return  array An array containing all the bizinserts of the city
	*/
	public function getEchoEmbassy()
	{
		return bizobject::getBizobjects('echoEmbassy', 'cityId='.$this->id, 'id');
    }
    
	public function getEchoHospital()
	{
		return bizobject::getBizobjects('echoHospital', 'cityId='.$this->id, 'id');
    }
	
	public function getEchoPolice()
	{
		return bizobject::getBizobjects('echoPolice', 'cityId='.$this->id, 'id');
    }
    
	public function getEchoAirport()
	{
		return bizobject::getBizobjects('echoAirport', 'cityId='.$this->id, 'id');
    }
    
	public function getEchoSlideshow()
	{
		return bizobject::getBizobjects('echoSlideshow', 'cityId='.$this->id, 'id');
    }
    
	public function getEchoHousing()
	{
		return bizobject::getBizobjects('echoHousing', 'cityId='.$this->id, 'id');
    }
    
	public function getEchoTakeOuts()
	{
		return bizobject::getBizobjects('echoTakeOuts', 'cityId='.$this->id, 'id');
    }
    
	public function getEchoMustSees()
	{
		return bizobject::getBizobjects('echoMustSees', 'cityId='.$this->id, 'id');
    }
    
	public function getEchoEvent()
	{
		return bizobject::getBizobjects('echoEvent', 'cityId='.$this->id, 'id');
    }
    
	public function getEchoStation()
	{
		return bizobject::getBizobjects('echoStation', 'cityId='.$this->id, 'id');
    }
    
	public function getEchoShow()
	{
		return bizobject::getBizobjects('echoShow', 'cityId='.$this->id, 'id');
    }
    
	public function getObjId($gBizobjects)
	{
		$tabId = array();
		$recup = $gBizobjects;
		if (!empty($recup))
		{
			foreach($recup as $obj)
				$tabId[] = $obj->id;
		}
		return $tabId;
    }
    
    /**
     * Deletes object from database
     *
     * @return true on success or an error message (string)
     *
     */
    public function delete()
    {
    	wcmTrace("LES ECHOS : delete city ".$this->getClass()." / ".$this->title);
    		
        // Delete all children 
        $this->deleteEchoEmbassy();
        $this->deleteEchoHospital();
        $this->deleteEchoPolice();
        $this->deleteEchoAirport();
        $this->deleteEchoSlideshow();
        $this->deleteEchoHousing();
        $this->deleteEchoTakeOuts();
        $this->deleteEchoMustSees();
        $this->deleteEchoEvent();
        $this->deleteEchoStation();
        $this->deleteEchoShow();
        
        return parent::delete();
    }

	public function deleteEchoEmbassy()
    {
        wcmTrace("LES ECHOS : delete Embassy"); 	
        // Delete all children 
        $echoEmbassy = $this->getEchoEmbassy();
        foreach($echoEmbassy as $result)
        {
        	$result->delete(false);
        }
    }
    
	public function deleteEchoHospital()
    {
        wcmTrace("LES ECHOS : delete Hospital"); 	
        // Delete all children 
        $echoHospital = $this->getEchoHospital();
        foreach($echoHospital as $result)
        {
            $result->delete(false);
        }
    }
    
	public function deleteEchoPolice()
    {
    	wcmTrace("LES ECHOS : delete Police"); 	
        
        // Delete all children 
        $echoPolice = $this->getEchoPolice();
        foreach($echoPolice as $result)
        {
            $result->delete(false);
        }
    }
    
	public function deleteEchoAirport()
    {
        wcmTrace("LES ECHOS : delete Airport"); 	
        // Delete all children 
        $echoAirport = $this->getEchoAirport();
        foreach($echoAirport as $result)
        {
            $result->delete(false);
        }
    }
    
	public function deleteEchoSlideshow()
    {
        wcmTrace("LES ECHOS : delete Slideshow"); 	
        // Delete all children 
        $echoSlideshow = $this->getEchoSlideshow();
        foreach($echoSlideshow as $result)
        {
            $result->delete(false);
        }
    }
    
	public function deleteEchoHousing()
    {
        wcmTrace("LES ECHOS : delete Housing"); 	
        // Delete all children 
        $echoHousing = $this->getEchoHousing();
        foreach($echoHousing as $result)
        {
            $result->delete(false);
        }
    }

    public function deleteEchoTakeOuts()
    {
        wcmTrace("LES ECHOS : delete take outs"); 	
        // Delete all children 
        $echoTakeOuts = $this->getEchoTakeOuts();
        foreach($echoTakeOuts as $result)
        {
            $result->delete(false);
        }
    }
    
	public function deleteEchoMustSees()
    {
        wcmTrace("LES ECHOS : delete Must sees"); 	
        // Delete all children 
        $echoMustSees = $this->getEchoMustSees();
        foreach($echoMustSees as $result)
        {
            $result->delete(false);
        }
    }
    
	public function deleteEchoEvent()
    {
        wcmTrace("LES ECHOS : delete event"); 	
        // Delete all children 
        $echoEvent = $this->getEchoEvent();
        foreach($echoEvent as $result)
        {
            $result->delete(false);
        }
    }
    
	public function deleteEchoStation()
    {
        wcmTrace("LES ECHOS : delete station"); 	
        // Delete all children 
        $echoStation = $this->getEchoStation();
        foreach($echoStation as $result)
        {
            $result->delete(false);
        }
    }
    
	public function deleteEchoShow()
    {
        wcmTrace("LES ECHOS : delete show"); 	
        // Delete all children 
        $echoShow = $this->getEchoShow();
        foreach($echoShow as $result)
        {
            $result->delete(false);
        }
    }
    
	/**
     * Update 
     *
     * @param Array $array  Array of new inserts
     */
    public function updateEchoEmbassy($newEchoEmbassy)
    {
        $this->serialStorage['echoEmbassy'] = $newEchoEmbassy;
    }
    
	public function updateEchoHospital($newEchoHospital)
    {
        $this->serialStorage['echoHospital'] = $newEchoHospital;
    }

    public function updateEchoPolice($newEchoPolice)
    {
        $this->serialStorage['echoPolice'] = $newEchoPolice;
    }

	public function updateEchoAirport($newEchoAirport)
    {
        $this->serialStorage['echoAirport'] = $newEchoAirport;
    }
    
	public function updateEchoSlideshow($newEchoSlideshow)
    {
        $this->serialStorage['echoSlideshow'] = $newEchoSlideshow;
    }
    
	public function updateEchoHousing($newEchoHousing)
    {
        $this->serialStorage['echoHousing'] = $newEchoHousing;
    }
    
	public function updateEchoTakeOuts($newEchoTakeOuts)
    {
        $this->serialStorage['echoTakeOuts'] = $newEchoTakeOuts;
    }
    
	public function updateEchoMustSees($newEchoMustSees)
    {
        $this->serialStorage['echoMustSees'] = $newEchoMustSees;
    }
    
	public function updateEchoEvent($newEchoEvent)
    {
        $this->serialStorage['echoEvent'] = $newEchoEvent;
    }
    
	public function updateEchoStation($newEchoStation)
    {
        $this->serialStorage['echoStation'] = $newEchoStation;
    }
    
	public function updateEchoShow($newEchoShow)
    {
        $this->serialStorage['echoShow'] = $newEchoShow;
    }
    
    /**
     * Gets object ready to store by getting modified date, creation date etc
     * Will execute transition.
     *
     */
    protected function store()
    {
        if(!parent::store()) return false;
        
		$newEchoEmbassy = getArrayParameter($this->serialStorage, 'echoEmbassy');
		$newEchoHospital = getArrayParameter($this->serialStorage, 'echoHospital');
		$newEchoPolice = getArrayParameter($this->serialStorage, 'echoPolice');
		$newEchoAirport = getArrayParameter($this->serialStorage, 'echoAirport');
		$newEchoSlideshow = getArrayParameter($this->serialStorage, 'echoSlideshow');
		$newEchoHousing = getArrayParameter($this->serialStorage, 'echoHousing');
		$newEchoTakeOuts = getArrayParameter($this->serialStorage, 'echoTakeOuts');
		$newEchoMustSees = getArrayParameter($this->serialStorage, 'echoMustSees');
		$newEchoEvent = getArrayParameter($this->serialStorage, 'echoEvent');
		$newEchoStation = getArrayParameter($this->serialStorage, 'echoStation');
		$newEchoShow = getArrayParameter($this->serialStorage, 'echoShow');
		
		if($newEchoEmbassy)
		{
			$tabNewEmbassy = array();
			$tabActualEmbassy = $this->getEchoEmbassy();		
			foreach($newEchoEmbassy as $result)
			{			
				if (isset($result['id'])) $tabNewEmbassy[] = $result['id'];
				$echoEmbassy = new echoEmbassy();
				$echoEmbassy->cityId = $this->id;
				if (!$echoEmbassy->save($result))
				{
					$this->lastErrorMsg = 'Embassy Save::'.$echoEmbassy->lastErrorMsg;
					wcmTrace("LES ECHOS : Embassy Save error");
				}
			}	
			// on compare les id des anciennes données avec les nouvelles
			if (!empty($tabActualEmbassy) && !empty($tabNewEmbassy))
			{
				foreach ($tabActualEmbassy as $objet)
				{
					if (!in_array($objet->id, $tabNewEmbassy))
					{
						$objet->delete(false);
					}
				}
			}				
		}
		
    	if($newEchoHospital)
		{
			$tabNewHospital = array();
			$tabActualHospital = $this->getEchoHospital();		
			foreach($newEchoHospital as $result)
			{
				if (isset($result['id'])) $tabNewHospital[] = $result['id'];
				$echoHospital = new echoHospital();
				$echoHospital->cityId = $this->id;
				if (!$echoHospital->save($result))
				{
					$this->lastErrorMsg = 'Hospital Save::'.$echoHospital->lastErrorMsg;
					wcmTrace("LES ECHOS : Hospital Save error");
				}	
			}
			// on compare les id des anciennes données avec les nouvelles
			if (!empty($tabActualHospital) && !empty($tabNewHospital))
			{
				foreach ($tabActualHospital as $objet)
				{
					if (!in_array($objet->id, $tabNewHospital))
					{
						$objet->delete(false);
					}
				}
			}	
		}
		
    	if($newEchoPolice)
		{
			$tabNewPolice = array();
			$tabActualPolice = $this->getEchoPolice();		
			foreach($newEchoPolice as $result)
			{
				if (isset($result['id'])) $tabNewPolice[] = $result['id'];
				$echoPolice = new echoPolice();
				$echoPolice->cityId = $this->id;
				if (!$echoPolice->save($result))
				{
					$this->lastErrorMsg = 'Police Save::'.$echoPolice->lastErrorMsg;
					wcmTrace("LES ECHOS : Police Save error");
				}	
			}
			// on compare les id des anciennes données avec les nouvelles
			if (!empty($tabActualPolice) && !empty($tabNewPolice))
			{
				foreach ($tabActualPolice as $objet)
				{
					if (!in_array($objet->id, $tabNewPolice))
					{
						$objet->delete(false);
					}
				}
			}	
		}
		
    	if($newEchoAirport)
		{
			$tabNewAirport = array();
			$tabActualAirport = $this->getEchoAirport();		
			foreach($newEchoAirport as $result)
			{
				if (isset($result['id'])) $tabNewAirport[] = $result['id'];
				$echoAirport = new echoAirport();
				$echoAirport->cityId = $this->id;
				if (!$echoAirport->save($result))
				{
					$this->lastErrorMsg = 'Airport Save::'.$echoAirport->lastErrorMsg;
					wcmTrace("LES ECHOS : Airport Save error");
				}	
			}
			// on compare les id des anciennes données avec les nouvelles
			if (!empty($tabActualAirport) && !empty($tabNewAirport))
			{
				foreach ($tabActualAirport as $objet)
				{
					if (!in_array($objet->id, $tabNewAirport))
					{
						$objet->delete(false);
					}
				}
			}	
		}
		
    	if($newEchoSlideshow)
		{
			$tabNewSlideshow = array();
			$tabActualSlideshow = $this->getEchoSlideshow();		
			foreach($newEchoSlideshow as $result)
			{
				if (isset($result['id'])) $tabNewSlideshow[] = $result['id'];
				$echoSlideshow = new echoSlideshow();
				$echoSlideshow->cityId = $this->id;
				if (!$echoSlideshow->save($result))
				{
					$this->lastErrorMsg = 'Slideshow Save::'.$echoSlideshow->lastErrorMsg;
					wcmTrace("LES ECHOS : Slideshow Save error");
				}
			}
			// on compare les id des anciennes données avec les nouvelles
			if (!empty($tabActualSlideshow) && !empty($tabNewSlideshow))
			{
				foreach ($tabActualSlideshow as $objet)
				{
					if (!in_array($objet->id, $tabNewSlideshow))
					{
						$objet->delete(false);
					}
				}
			}	
		}
		
		if($newEchoHousing)
		{
			$tabNewHousing = array();
			$tabActualHousing = $this->getEchoHousing();			
			foreach($newEchoHousing as $result)
			{
				if (isset($result['id'])) $tabNewHousing[] = $result['id'];
				$echoHousing = new echoHousing();
				$echoHousing->cityId = $this->id;
				if (!$echoHousing->save($result))
				{
					$this->lastErrorMsg = 'Housing Save::'.$echoHousing->lastErrorMsg;
					wcmTrace("LES ECHOS : Housing Save error");
				}	
			}
			// on compare les id des anciennes données avec les nouvelles
			if (!empty($tabActualHousing) && !empty($tabNewHousing))
			{
				foreach ($tabActualHousing as $objet)
				{
					if (!in_array($objet->id, $tabNewHousing))
					{
						$objet->delete(false);
					}
				}
			}
		}
		
    	if($newEchoTakeOuts)
		{
			$tabNewTakeOuts = array();
			$tabActualTakeOuts = $this->getEchoTakeOuts();			
			foreach($newEchoTakeOuts as $result)
			{
				if (isset($result['id'])) $tabNewTakeOuts[] = $result['id'];
				$echoTakeOuts = new echoTakeOuts();
				$echoTakeOuts->cityId = $this->id;
				if (!$echoTakeOuts->save($result))
				{
					$this->lastErrorMsg = 'TakeOuts Save::'.$echoTakeOuts->lastErrorMsg;
					wcmTrace("LES ECHOS : TakeOuts Save error");
				}	
			}
			// on compare les id des anciennes données avec les nouvelles
			if (!empty($tabActualTakeOuts) && !empty($tabNewTakeOuts))
			{
				foreach ($tabActualTakeOuts as $objet)
				{
					if (!in_array($objet->id, $tabNewTakeOuts))
					{
						$objet->delete(false);
					}
				}
			}
		}
		
    	if($newEchoMustSees)
		{
			$tabNewMustSees = array();
			$tabActualMustSees = $this->getEchoMustSees();			
			foreach($newEchoMustSees as $result)
			{
				if (isset($result['id'])) $tabNewMustSees[] = $result['id'];
				$echoMustSees = new echoMustSees();
				$echoMustSees->cityId = $this->id;
				if (!$echoMustSees->save($result))
				{
					$this->lastErrorMsg = 'TakeOuts Save::'.$echoMustSees->lastErrorMsg;
					wcmTrace("LES ECHOS : TakeOuts Save error");
				}	
			}
			
			// on compare les id des anciennes données avec les nouvelles
			if (!empty($tabActualMustSees) && !empty($tabNewMustSees))
			{
				foreach ($tabActualMustSees as $objet)
				{
					if (!in_array($objet->id, $tabNewMustSees))
					{
						$objet->delete(false);
					}
				}
			}
		}
		
    	if($newEchoEvent)
		{
			$tabNewEvent = array();
			$tabActualEvent = $this->getEchoEvent();				
			foreach($newEchoEvent as $result)
			{
				if (isset($result['id'])) $tabNewEvent[] = $result['id'];
				$echoEvent = new echoEvent();
				$echoEvent->cityId = $this->id;
				if (!$echoEvent->save($result))
				{
					$this->lastErrorMsg = 'Event Save::'.$echoEvent->lastErrorMsg;
					wcmTrace("LES ECHOS : Event Save error");
				}
			}
			
			// on compare les id des anciennes données avec les nouvelles
			if (!empty($tabActualEvent) && !empty($tabNewEvent))
			{
				foreach ($tabActualEvent as $objet)
				{
					if (!in_array($objet->id, $tabNewEvent))
					{
						$objet->delete(false);
					}
				}
			}
		}
			
    	if($newEchoStation)
		{
			$tabNewStation = array();
			$tabActualStation = $this->getEchoStation();	

			foreach($newEchoStation as $result)
			{
				if (isset($result['id'])) $tabNewStation[] = $result['id'];
				$echoStation = new echoStation();
				$echoStation->cityId = $this->id;
				if (!$echoStation->save($result))
				{
					$this->lastErrorMsg = 'Station Save::'.$echoStation->lastErrorMsg;
					wcmTrace("LES ECHOS : Station Save error");
				}
			}
			
			// on compare les id des anciennes données avec les nouvelles
			if (!empty($tabActualStation) && !empty($tabNewStation))
			{
				foreach ($tabActualStation as $objet)
				{
					if (!in_array($objet->id, $tabNewStation))
					{
						$objet->delete(false);
					}
				}
			}
		}
		
    	if($newEchoShow)
		{
			$tabNewShow = array();
			$tabActualShow = $this->getEchoShow();				
			foreach($newEchoShow as $result)
			{
				if (isset($result['id'])) $tabNewShow[] = $result['id'];
				$echoShow = new echoShow();
				$echoShow->cityId = $this->id;
				if (!$echoShow->save($result))
				{
					$this->lastErrorMsg = 'Show Save::'.$echoShow->lastErrorMsg;
					wcmTrace("LES ECHOS : Show Save error");
				}
			}
			
			// on compare les id des anciennes données avec les nouvelles
			if (!empty($tabActualShow) && !empty($tabNewShow))
			{
				foreach ($tabActualShow as $objet)
				{
					if (!in_array($objet->id, $tabNewShow))
					{
						$objet->delete(false);
					}
				}
			}
		}
			
        return true;
    }

    /**
     * Check validity of object
     *
     * A generic method which can (should ?) be overloaded by the child class
     *
     * @return boolean true when object is valid
     *
     */
    public function checkValidity()
    {
    	
    	if (trim($this->title . ' ') == '')
        {
            $this->lastErrorMsg = "Infos Pratiques : le nom de la ville est obligatoire";
            return false;
        }
    	else if (trim($this->country . ' ') == '')
        {
            $this->lastErrorMsg = "Infos Pratiques : le nom de du pays est obligatoire";
            return false;
        }
        else
        	return true;
    }

    public static function getKind()
    {
    	return array();
    }
    
    static function getPositionByAddress($title, $line, $postalCode, $city, $country)
    {
    	$config = wcmConfig::getInstance();
    	$position = array();
    	
    	$key = $config['wcm.apiGoogleMap.key'];
		$address = $title." ".$line." ".$postalCode." ".$city." ".$country;
		$address = strtolower($address);
		$address = urlencode($address);
		$url = "http://maps.google.com/maps/geo?q=".$address."&output=xml&oe=utf8&key=".$key;
		//wcmTrace("geoloc : ".$url);	 
		// Retrieve the URL contents
   		$page = file_get_contents($url);

	   	// Parse the returned XML file
	   	//$xml = new SimpleXMLElement($page);
		$xml = new SimpleXMLElement(utf8_encode($page));
		
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
    
 	function checkExistence() 
 	{
        if (isset($this->title) && !empty($this->title))
            return true;
        else
            return false;
    }
    
	public function save($source = null)
	{
		if (isset($source['cityGeoNameId']) && !empty($source['cityGeoNameId']))
		{
			$city = new city();
			$city->refreshByGeonameId($source['cityGeoNameId']);
			$this->latitude = $city->latitude;
			$this->longitude = $city->longitude;
		}
		if (!parent::save($source))
    	{
    		wcmTrace("LES ECHOS : erreur save city ".$this->getClass());
    		return false;
    	}
    	else
    	{		
    		wcmTrace("LES ECHOS : save city ".$this->getClass());
    		return true;
    	}
    }
    
    /*
	 * récupérér les alternates names de la base geoloc
	 */
    public function getAlternateName($geoNameId, $originalName)
    {
    	if (!empty($geoNameId) && !empty($originalName))
    	{
	    	$alternate_names = new alternate_names(); 	
	    	$name = $alternate_names->getNameByGeonameId($geoNameId, "fr");
			
	    	if (!empty($name)) 
		    	return $this->putSafeText($name);
	    	else 
	    		return $this->putSafeText($originalName);
    	}
    	else 
    		return $this->putSafeText($originalName);
    }
    
    /*
	 * supprimer les caractère spéciaux dans le XML
	 */
	public function putSafeText($text)
    {
    	$text = str_replace("&", "&amp;", $text); 
    	
    	return $text;
    }
    
    /*
	 * préparation de la structure XML pour l'export
	 */
    public function exportXmlStructure()
	{
		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
	
		$xml .= "<city id=\"echoCity-".$this->id."\">\n";  

		$xml .= "<infos>\n";  
	    $xml .= "    <name>".$this->getAlternateName($this->cityGeoNameId, $this->title)."</name>\n";  
	    $xml .= "    <address>\n";  
	    $xml .= "        <country geonameId=\"".$this->countryGeoNameId."\">".$this->getAlternateName($this->countryGeoNameId, $this->country)."</country>\n";  
	    $xml .= "        <position latitude=\"".$this->latitude."\" longitude=\"".$this->longitude."\"/>\n";  
	    $xml .= "    </address>\n";  
	    $xml .= "</infos>\n";  
	    
	    $xml .= "<practicalInfos>\n";  
	    $xml .= "    <description>".$this->putSafeText($this->description)."</description>\n";  
	    $xml .= "    <population>\n";  
	    $xml .= "        <populationDescription>".$this->putSafeText($this->populationDescription)."</populationDescription>\n";  
	    $xml .= "        <populationNumber>".$this->populationNumber."</populationNumber>\n";  
	    $xml .= "        <populationDensity>".$this->populationDensity."</populationDensity>\n";  
	    $xml .= "    </population>\n";  
	        
	    $xml .= "    <climate>\n";  
	    $xml .= "        <climateDescription>".$this->putSafeText($this->climateDescription)."</climateDescription>\n";  
	    $xml .= "        <climateTemperatureWinter>".$this->climateTemperatureWinter." °C</climateTemperatureWinter>\n";  
	    $xml .= "        <climateTemperatureSummer>".$this->climateTemperatureSummer." °C</climateTemperatureSummer>\n";  
	    $xml .= "    </climate>\n";  
	        
	    $xml .= "    <visa>".$this->visa."</visa>\n";  
	        
	    $xml .= "    <languages>\n";  
	    $xml .= "        <languagesDescription>".$this->putSafeText($this->languagesDescription)."</languagesDescription>\n";  
	    $xml .= "        <officialLanguages>".$this->putSafeText($this->officialLanguages)."</officialLanguages>\n";  
	    $xml .= "    </languages>\n";  
	        
	    $xml .= "    <jetlag>\n";  
	    $xml .= "        <jetlagDescription>".$this->putSafeText($this->jetlagDescription)."</jetlagDescription>\n";  
	    $xml .= "        <jetlagGmt>\n";  
	    $xml .= "            GMT ".$this->jetlagGmt."h\n";  
	    $xml .= "        </jetlagGmt>\n";  
	    $xml .= "    </jetlag>\n";  
	    $xml .= "    <web>".$this->web."</web>\n";  

	    $arrayEmbassies = $this->getEchoEmbassy();
	    if (!empty($arrayEmbassies))
	    {
		    $xml .= "<embassies>\n";  
	    	foreach($arrayEmbassies as $data)
	        {
	    		$xml .= "<embassy>\n";  
			    $xml .= "   <name>".$this->putSafeText($data->title)."</name>\n";  
			    $xml .= "   <address>\n";  
			    $xml .= "		<line>".$this->putSafeText($data->address)."</line>\n";  
			    $xml .= "		<postalCode>".$data->postalCode."</postalCode>\n";  
			    $xml .= "		<city geonameId=\"".$data->cityGeoNameId."\">".$this->getAlternateName($data->cityGeoNameId, $data->city)."</city>\n";  
			    $xml .= "		<country geonameId=\"".$data->countryGeoNameId."\">".$this->getAlternateName($data->countryGeoNameId, $data->country)."</country>\n";  
			    $xml .= "		<position latitude=\"".$data->latitude."\" longitude=\"".$data->longitude."\"/>\n";  
			    $xml .= "   </address>\n";  
			    $xml .= "   <contactInfo>\n";  
			    $xml .= "		<phone>".$data->phone."</phone>\n";  
			    $xml .= "		<email>".$data->email."</email>\n";  
			    $xml .= "		<web>".$data->web."</web>\n";  
			    $xml .= "   </contactInfo>\n";  
			    $xml .= "</embassy>\n";     
	        }
	        $xml .= "</embassies>\n";  
	    }

	    $arrayHospital = $this->getEchoHospital();
	    if (!empty($arrayHospital))
	    {
		    $xml .= "<hospitals>\n";  
	    	foreach($arrayHospital as $data)
	        {
			    $xml .= "<hospital>\n";  
			    $xml .= "    <name>".$this->putSafeText($data->title)."</name>\n";  
			    $xml .= "    <address>\n";  
			    $xml .= "		<line>".$this->putSafeText($data->address)."</line>\n";  
			    $xml .= "		<postalCode>".$data->postalCode."</postalCode>\n";  
			    $xml .= "		<city geonameId=\"".$data->cityGeoNameId."\">".$this->getAlternateName($data->cityGeoNameId, $data->city)."</city>\n";  
			    $xml .= "		<country geonameId=\"".$data->countryGeoNameId."\">".$this->getAlternateName($data->countryGeoNameId, $data->country)."</country>\n";  
			    $xml .= "		<position latitude=\"".$data->latitude."\" longitude=\"".$data->longitude."\"/>\n";  
			    $xml .= "    </address>\n";  
			    $xml .= "    <contactInfo>\n";  
			    $xml .= "		<phone>".$data->phone."</phone>\n";  
			    $xml .= "		<email>".$data->email."</email>\n";  
			    $xml .= "		<web>".$data->web."</web>\n";  
			    $xml .= "    </contactInfo>\n";  
			    $xml .= "</hospital>\n"; 
	          }
	        $xml .= "</hospitals>\n";  
	    } 
	        
	    $arrayPolices = $this->getEchoPolice();
	    if (!empty($arrayPolices))
	    {
		    $xml .= "<polices>\n";  
	    	foreach($arrayPolices as $data)
	        {  
			    $xml .= "<police>\n";  
			    $xml .= "    <name>".$this->putSafeText($data->title)."</name>\n";  
			    $xml .= "    <address>\n";  
			    $xml .= "		<line>".$this->putSafeText($data->address)."</line>\n";  
			    $xml .= "		<postalCode>".$data->postalCode."</postalCode>\n";  
			    $xml .= "		<city geonameId=\"".$data->cityGeoNameId."\">".$this->getAlternateName($data->cityGeoNameId, $data->city)."</city>\n";  
			    $xml .= "		<country geonameId=\"".$data->countryGeoNameId."\">".$this->getAlternateName($data->countryGeoNameId, $data->country)."</country>\n";  
			    $xml .= "		<position latitude=\"".$data->latitude."\" longitude=\"".$data->longitude."\"/>\n";  
			    $xml .= "    </address>\n";  
			    $xml .= "    <contactInfo>\n";  
			    $xml .= "		<phone>".$data->phone."</phone>\n";  
			    $xml .= "		<email>".$data->email."</email>\n";  
			    $xml .= "		<web>".$data->web."</web>\n";  
			    $xml .= "    </contactInfo>\n";  
			    $xml .= "</police>\n";  
			 }
	        $xml .= "</polices>\n";  
	    } 
	        
	    $xml .= "    <maps>\n";  
	    $xml .= "        <map kind=\"city\">\n";  
	    $xml .= "            <caption>".$this->putSafeText($this->mapCityCaption)."</caption>\n";  
	    $xml .= "            <credits>".$this->putSafeText($this->mapCityCredits)."</credits>\n";  
	    $xml .= "            <file>".$this->mapCityFile."</file>\n";  
	    $xml .= "        </map>\n";  
	    $xml .= "        <map kind=\"transport\">\n";  
	    $xml .= "            <caption>".$this->putSafeText($this->mapTransportCaption)."</caption>\n";  
	    $xml .= "            <credits>".$this->putSafeText($this->mapTransportCredits)."</credits>\n";  
	    $xml .= "            <file>".$this->mapTransportFile."</file>\n";  
	    $xml .= "        </map>\n";  
	    $xml .= "    </maps>\n";  
	    $xml .= "</practicalInfos>\n";  

	    $arrayAirports = $this->getEchoAirport();
	    if (!empty($arrayAirports))
	    {	    
	    	$xml .= "<airports>\n";  
	    	foreach($arrayAirports as $data)
	        {
			    $xml .= "    <airport>\n";  
			    $xml .= "        <name>".$this->putSafeText($data->title)."</name>\n";  
			    $xml .= "        <description>".$this->putSafeText($data->description)."</description>\n";  
			    $xml .= "        <address>\n";  
			    $xml .= "            <line>".$this->putSafeText($data->address)."</line>\n";  
			    $xml .= "            <postalCode>".$data->postalCode."</postalCode>\n";  
			    $xml .= "            <city geonameId=\"".$data->cityGeoNameId."\">".$this->getAlternateName($data->cityGeoNameId, $data->city)."</city>\n";  
			    $xml .= "            <country geonameId=\"".$data->countryGeoNameId."\">".$this->getAlternateName($data->countryGeoNameId, $data->country)."</country>\n";  
			    $xml .= "            <position latitude=\"".$data->latitude."\" longitude=\"".$data->longitude."\"/>\n";  
			    $xml .= "            <transportation>".$this->putSafeText($data->transportation)."</transportation>\n";  
			    $xml .= "        </address>\n";  
			    $xml .= "        <contactInfo>\n";  
			    $xml .= "            <phone>".$data->phone."</phone>\n";  
			    $xml .= "            <email>".$data->email."</email>\n";  
			    $xml .= "            <web>".$data->web."</web>\n";  
			    $xml .= "        </contactInfo>\n";  	            
			    $xml .= "        <maps>\n";  
			    $xml .= "            <map kind=\"airport\">\n";  
			    $xml .= "                <caption>".$this->putSafeText($data->mapTitle)."</caption>\n";  
			    $xml .= "                <credits>".$this->putSafeText($data->mapCredits)."</credits>\n";  
			    $xml .= "                <file>".$data->mapFile."</file>\n";  
			    $xml .= "            </map>\n";  
			    $xml .= "        </maps>\n";  
			    $xml .= "    </airport>\n";  
	        }
        	$xml .= "</airports>\n";  
	    }
	    
	    $arraySlideshows = $this->getEchoSlideshow();
	    if (!empty($arraySlideshows))
	    {
			$xml .= "<slideshow>\n";  
			foreach($arraySlideshows as $data)
	        {
			    $xml .= "    <link>\n";  
			    $xml .= "        <caption>".$this->putSafeText($data->title)."</caption>\n";  
			    $xml .= "        <credits>".$this->putSafeText($data->credits)."</credits>\n";  
			    $xml .= "        <file>".$data->file."</file>\n";  
			    $xml .= "    </link>\n";  
	        }
	    	$xml .= "</slideshow>\n"; 
	    }  
	    
	    $arrayHousings = $this->getEchoHousing();
	    if (!empty($arrayHousings))
	    {
	    	$xml .= "<housings>\n"; 
	    	foreach($arrayHousings as $data)
	        {
			    $xml .= "    <housing kind=\"".$data->kind."\">\n";  
			    $xml .= "        <name>".$this->putSafeText($data->title)."</name>\n";  
			    $xml .= "        <commentDates>".$this->putSafeText($data->commentDates)."</commentDates>\n";  
			    $xml .= "        <commentPrice>".$this->putSafeText($data->commentPrice)."</commentPrice>\n";  
			    $xml .= "        <address>\n";  
			    $xml .= "            <line>".$this->putSafeText($data->address)."</line>\n";  
			    $xml .= "            <postalCode>".$data->postalCode."</postalCode>\n";  
			    $xml .= "            <city geonameId=\"".$data->cityGeoNameId."\">".$this->getAlternateName($data->cityGeoNameId, $data->city)."</city>\n";  
			    $xml .= "            <country geonameId=\"".$data->countryGeoNameId."\">".$this->getAlternateName($data->countryGeoNameId, $data->country)."</country>\n";  
			    $xml .= "            <position latitude=\"".$data->latitude."\" longitude=\"".$data->longitude."\"/>\n";  
			    $xml .= "        </address>\n";  
			    $xml .= "        <contactInfo>\n";  
			    $xml .= "            <phone>".$data->phone."</phone>\n";  
			    $xml .= "            <email>".$data->email."</email>\n";  
			    $xml .= "            <web>".$data->web."</web>\n";  
			    $xml .= "        </contactInfo>\n";  
			    $xml .= "    </housing>\n"; 
	        }
	    	$xml .= "</housings>\n";  
	    }
	    
		$arrayTakeouts = $this->getEchoTakeOuts();
	    if (!empty($arrayTakeouts))
	    {
	    	$xml .= "<takeOuts>\n"; 
	    	foreach($arrayTakeouts as $data)
	        {
			    $xml .= "    <takeOut kind=\"".$data->kind."\">\n";  
			    $xml .= "        <name>".$this->putSafeText($data->title)."</name>\n";  
			    $xml .= "        <commentDates>".$this->putSafeText($data->commentDates)."</commentDates>\n";  
			    $xml .= "        <commentPrice>".$this->putSafeText($data->commentPrice)."</commentPrice>\n";  
			    $xml .= "        <address>\n";  
			    $xml .= "            <line>".$this->putSafeText($data->address)."</line>\n";  
			    $xml .= "            <postalCode>".$data->postalCode."</postalCode>\n";  
			    $xml .= "            <city geonameId=\"".$data->cityGeoNameId."\">".$this->getAlternateName($data->cityGeoNameId, $data->city)."</city>\n";  
			    $xml .= "            <country geonameId=\"".$data->countryGeoNameId."\">".$this->getAlternateName($data->countryGeoNameId, $data->country)."</country>\n";  
			    $xml .= "            <position latitude=\"".$data->latitude."\" longitude=\"".$data->longitude."\"/>\n";  
			    $xml .= "        </address>\n";  
			    $xml .= "        <contactInfo>\n";  
			    $xml .= "            <phone>".$data->phone."</phone>\n";  
			    $xml .= "            <email>".$data->email."</email>\n";  
			    $xml .= "            <web>".$data->web."</web>\n";  
			    $xml .= "        </contactInfo>\n";  
			    $xml .= "    </takeOut>\n"; 
	        }
	    	$xml .= "</takeOuts>\n";  
	    }
	    
		$arrayMustsees = $this->getEchoMustSees();
	    if (!empty($arrayMustsees))
	    {
	    	$xml .= "<mustSees>\n"; 
	    	foreach($arrayMustsees as $data)
	        {
			    $xml .= "    <mustSee kind=\"".$data->kind."\">\n";  
			    $xml .= "        <name>".$this->putSafeText($data->title)."</name>\n";  
			    $xml .= "        <commentDates>".$this->putSafeText($data->commentDates)."</commentDates>\n";  
			    $xml .= "        <commentPrice>".$this->putSafeText($data->commentPrice)."</commentPrice>\n";  
			    $xml .= "        <address>\n";  
			    $xml .= "            <line>".$this->putSafeText($data->address)."</line>\n";  
			    $xml .= "            <postalCode>".$data->postalCode."</postalCode>\n";  
			    $xml .= "            <city geonameId=\"".$data->cityGeoNameId."\">".$this->getAlternateName($data->cityGeoNameId, $data->city)."</city>\n";  
			    $xml .= "            <country geonameId=\"".$data->countryGeoNameId."\">".$this->getAlternateName($data->countryGeoNameId, $data->country)."</country>\n";  
			    $xml .= "            <position latitude=\"".$data->latitude."\" longitude=\"".$data->longitude."\"/>\n";  
			    $xml .= "        </address>\n";  
			    $xml .= "        <contactInfo>\n";  
			    $xml .= "            <phone>".$data->phone."</phone>\n";  
			    $xml .= "            <email>".$data->email."</email>\n";  
			    $xml .= "            <web>".$data->web."</web>\n";  
			    $xml .= "        </contactInfo>\n";  
			    $xml .= "    </mustSee>\n"; 
	        }
	    	$xml .= "</mustSees>\n";  
	    }
	    
	    $arrayEvents = $this->getEchoEvent();
	    if (!empty($arrayEvents))
	    {
	    	$xml .= "<events>\n"; 
	    	foreach($arrayEvents as $data)
	        {
		        $xml .= "<event kind=\"".$data->kind."\">\n"; 
		        $xml .= "    <title>".$this->putSafeText($data->title)."</title>\n"; 
		        $xml .= "    <start>".$data->startDate."</start>\n"; 
		        $xml .= "    <end>".$data->endDate."</end>\n"; 
		        $xml .= "    <commentDates>".$this->putSafeText($data->commentDates)."</commentDates>\n"; 
		        $xml .= "    <description>".$this->putSafeText($data->description)."</description>\n"; 
		        $xml .= "    <price>".$data->price."</price>\n"; 
		        $xml .= "    <commentPrice>".$this->putSafeText($data->commentPrice)."</commentPrice>\n"; 
		        $xml .= "    <contactInfo>\n"; 
		        $xml .= "        <phone>".$data->phone."</phone>\n"; 
		        $xml .= "        <email>".$data->email."</email>\n"; 
		        $xml .= "        <web>".$data->web."</web>\n"; 
		        $xml .= "    </contactInfo>\n";    
		        $xml .= "    <location>\n"; 
		        $xml .= "        <name>".$this->putSafeText($data->placeName)."</name>\n"; 
		        $xml .= "        <address>\n"; 
		        $xml .= "            <line>".$this->putSafeText($data->address)."</line>\n"; 
		        $xml .= "            <postalCode>".$data->postalCode."</postalCode>\n"; 
		        $xml .= "            <city geonameId=\"".$data->cityGeoNameId."\">".$this->getAlternateName($data->cityGeoNameId, $data->city)."</city>\n"; 
		        $xml .= "            <country geonameId=\"".$data->countryGeoNameId."\">".$this->getAlternateName($data->countryGeoNameId, $data->country)."</country>\n";                  
		        $xml .= "            <position latitude=\"".$data->latitude."\" longitude=\"".$data->longitude."\"/>\n"; 
		        $xml .= "        </address>\n"; 
		        $xml .= "    </location>\n";           
		        $xml .= "    <links>\n"; 
		        $xml .= "        <link>\n"; 
		        $xml .= "            <caption>".$this->putSafeText($data->linkTitle)."</caption>\n"; 
		        $xml .= "            <credits>".$this->putSafeText($data->linkCredits)."</credits>\n"; 
		        $xml .= "            <file>".$data->linkFile."</file>\n"; 
		        $xml .= "        </link>\n"; 
		        $xml .= "    </links>\n"; 
		        $xml .= "</event>\n"; 
	        }
	        
	        $arrayShows = $this->getEchoShow();
	        if (!empty($arrayShows))
	    	{
		    	foreach($arrayShows as $data)
		        {
			        $xml .= "<event kind=\"salon\">\n"; 
			        $xml .= "    <title>".$this->putSafeText($data->title)."</title>\n"; 
			        $xml .= "    <start>".$data->startDate."</start>\n"; 
			        $xml .= "    <end>".$data->endDate."</end>\n"; 
			        $xml .= "    <commentDates>".$this->putSafeText($data->commentDates)."</commentDates>\n"; 
			        $xml .= "    <description>".$this->putSafeText($data->description)."</description>\n"; 
			        $xml .= "    <price>".$data->price."</price>\n"; 
			        $xml .= "    <commentPrice>".$this->putSafeText($data->commentPrice)."</commentPrice>\n"; 
			        $xml .= "    <contactInfo>\n"; 
			        $xml .= "        <phone>".$data->phone."</phone>\n"; 
			        $xml .= "        <email>".$data->email."</email>\n"; 
			        $xml .= "        <web>".$data->web."</web>\n"; 
			        $xml .= "    </contactInfo>\n";    
			        $xml .= "    <location>\n"; 
			        $xml .= "        <name>".$this->putSafeText($data->placeName)."</name>\n"; 
			        $xml .= "        <address>\n"; 
			        $xml .= "            <line>".$this->putSafeText($data->address)."</line>\n"; 
			        $xml .= "            <postalCode>".$data->postalCode."</postalCode>\n"; 
			        $xml .= "            <city geonameId=\"".$data->cityGeoNameId."\">".$this->getAlternateName($data->cityGeoNameId, $data->city)."</city>\n"; 
			        $xml .= "            <country geonameId=\"".$data->countryGeoNameId."\">".$this->getAlternateName($data->countryGeoNameId, $data->country)."</country>\n";                  
			        $xml .= "            <position latitude=\"".$data->latitude."\" longitude=\"".$data->longitude."\"/>\n"; 
			        $xml .= "        </address>\n"; 
			        $xml .= "    </location>\n";           
			        $xml .= "    <links>\n"; 
			        $xml .= "        <link>\n"; 
			        $xml .= "            <caption>".$this->putSafeText($data->linkTitle)."</caption>\n"; 
			        $xml .= "            <credits>".$this->putSafeText($data->linkCredits)."</credits>\n"; 
			        $xml .= "            <file>".$data->linkFile."</file>\n"; 
			        $xml .= "        </link>\n"; 
			        $xml .= "    </links>\n"; 
			        $xml .= "</event>\n"; 
		        }
	    	}
	        
	    	$xml .= "</events>\n"; 
	    }
	    
		$arrayStations = $this->getEchoStation();
	    if (!empty($arrayStations))
	    {	    
	    	$xml .= "<stations>\n";  
	    	foreach($arrayStations as $data)
	        {
			    $xml .= "    <station>\n";  
			    $xml .= "        <name>".$this->putSafeText($data->title)."</name>\n";  
			    $xml .= "        <description>".$this->putSafeText($data->description)."</description>\n";  
			    $xml .= "        <address>\n";  
			    $xml .= "            <line>".$this->putSafeText($data->address)."</line>\n";  
			    $xml .= "            <postalCode>".$data->postalCode."</postalCode>\n";  
			    $xml .= "            <city geonameId=\"".$data->cityGeoNameId."\">".$this->getAlternateName($data->cityGeoNameId, $data->city)."</city>\n";  
			    $xml .= "            <country geonameId=\"".$data->countryGeoNameId."\">".$this->getAlternateName($data->countryGeoNameId, $data->country)."</country>\n";  
			    $xml .= "            <position latitude=\"".$data->latitude."\" longitude=\"".$data->longitude."\"/>\n";  
			    $xml .= "            <transportation>".$this->putSafeText($data->transportation)."</transportation>\n";  
			    $xml .= "        </address>\n";  
			    $xml .= "        <contactInfo>\n";  
			    $xml .= "            <phone>".$data->phone."</phone>\n";  
			    $xml .= "            <email>".$data->email."</email>\n";  
			    $xml .= "            <web>".$data->web."</web>\n";  
			    $xml .= "        </contactInfo>\n";  	            
			    $xml .= "        <maps>\n";  
			    $xml .= "            <map kind=\"station\">\n";  
			    $xml .= "                <caption>".$this->putSafeText($data->mapTitle)."</caption>\n";  
			    $xml .= "                <credits>".$this->putSafeText($data->mapCredits)."</credits>\n";  
			    $xml .= "                <file>".$data->mapFile."</file>\n";  
			    $xml .= "            </map>\n";  
			    $xml .= "        </maps>\n";  
			    $xml .= "    </station>\n";  
	        }
        	$xml .= "</stations>\n";  
	    }
	    	    
		$xml .= "</city>\n";	
		
		return $xml;
	}
}
