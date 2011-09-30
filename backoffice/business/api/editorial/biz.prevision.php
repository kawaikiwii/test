<?php

/**
 * Project:     WCM
 * File:        biz.prevision.php
 *
 * @copyright   (c)2011 Relaxnews
 * @version     4.x
 *
 */
 /**
 * Definition of a prevision
 */

class prevision extends bizobject
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
     * (date) Publication date
     */
    public $publicationDate;
  
    /**
     * (date) embargo date
     */
    public $embargoDate;
	 
    /**
     * (date) Expiration date
     */
    public $expirationDate;
   
    /**
     * (string) default title
     */
    public $title;
    
    public $sourceLocation;
   		
    public $cId;
    
	public $type;
	
	public $ratingValue;

	public $startDate;
   
    public $endDate;
    
    public $informations;
    
    public $place;
    
    public $websites;
    
    public $iptc;
   
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
    
    protected function propertyToXML($propKey, $propValue) {
        if (($propKey == 'place') && !is_array($propValue)) {
            /* On récupère les lieux associés à la prévision */
            $array_place = $this->getRelationPlaceObjectId($this,3);
            /* Boucle pour chaque relation lieu */
            for($i = 0;$i<count($array_place);$i++){
                /* On instancie la classe place */
                $place = new place(null, $array_place[$i]);
                /* On transforme l'objet en tableau */
                $place = get_object_vars($place);
                $propValue[$i]["city"] = $place["city"];
                $propValue[$i]["country"] = $place["country"];
            }
        }
        return parent::propertyToXML($propKey, $propValue);
    }
   
	/**
	* prevision Type List 
	*/
	function getTypeList()
	{
	        return array("anniversaire"=>"Anniversaire", "ephemeride"=>"Ephéméride", "infogene"=>"Info géné");
	}
	
	/**
	* prevision Rating List 
	*/
	function getRatingList()
	{
	    return array("1", "2", "3");
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
        	// on gère les biz-relation de type contact
	        if (isset($source['contact']) && !empty($source['contact']))
     			$this->setRelation($source['contact'], "organisation", bizrelation::IS_CONTACT_OF);  		
     		        
        	return $this->generate(false);
        }     	
        else
			return false;
    }
	
 	/**
	 * Used for GUI (interfaces)
	 *
	 * @return string with destinations id 
	 */
	static public function getRelationObjectIdByKindForGui($object, $kind)
	{	
		$relationId = array();
		$relations = wcmBizrelation::getBizobjectRelations($object, $kind);
     	
		if (!empty($relations))
		{
			foreach($relations as $rel)
			{
                            $relationId[] = $rel["destinationId"];
			}
		}
		return $relationId;
	}
	
        /**
	 * Used for GUI (interfaces)
	 *
	 * @return string with destinations id 
	 */
	static public function getRelationPlaceObjectId($object, $kind)
	{	
		$relationId = array();
		$relations = wcmBizrelation::getBizobjectRelations($object, $kind);
     	
		if (!empty($relations))
		{
			foreach($relations as $rel)
			{
                            if($rel["destinationClass"] == "place")
                                $relationId[] = $rel["destinationId"];
			}
		}
		return $relationId;
	}
        
        
        
	 /**
	 * Used to set relation from work to several objects
	 *
	 * @return number of relations
	 */
	public function setRelation($destids, $destClass, $kind)
	{	
		$relations = wcmBizrelation::getBizobjectRelations($this,$kind);
     	if (!empty($relations))
     	{ 
     		$rel = new bizrelation();
     		$rel->removeSpecificObjectByKind($this->getClass(),$this->id, $destClass, $kind);
     	}		
		
     	$newrelations = explode("|", $destids);
		
		if (sizeof($newrelations)<1)
			$newrelations = array($destids);
		
		foreach ($newrelations as $value)
     	{ 		
     		$rel = new bizrelation();
     		$rel->sourceClass = $this->getClass();
     		$rel->sourceId = $this->id;		
     		$rel->insertSpecificObject($destClass, $value, $kind,  $this->title);	
     	}
     	
     	return sizeof($newrelations);
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
	
	
}

