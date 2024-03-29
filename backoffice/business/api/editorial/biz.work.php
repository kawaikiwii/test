<?php

/**
 * Project:     WCM
 * File:        biz.work.php
 *
 * @copyright   (c)2009 Relaxnews
 * @version     4.x
 *
 */
 /**
 * Definition of an newstext
 */
class work extends bizobject
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
	   
	public $specific;
	
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
        //$this->publicationDate = $this->embargoDate = date('Y-m-d h:i:s');
        //$this->expirationDate = date('Y-m-d h:i:s', $timestamp);
        $this->siteId = $this->channelId = 0;
    }
   
	/**
	* Forecast Type List 
	*/
	function getTypeList()
	{
	    return wcmList::getListFromParentCodeForDropDownList("work_type");
	}
	
	function getMovieFormats()
	{
	    return array(null,"dvd"=>"DVD", "blu-ray"=>"Blu-ray", "dvd/blu-ray"=>"DVD/Blu-ray");
	}
	
	function getCdOutputType()
	{
	    return array("physical"=>"Physical", "digital"=>"Digital");
	}
	
	public function checkValidity()
    {
        // Attention cette fonction passe outre les tests d'origine faits dans wcm.bizobject.php
        // le pb est que la propriété publicationDate est de type dateTime or les tests initiaux
        // se basent sur un format de type date --> ce qui génère une erreur de type notice
       
        return true;
    }
	
    public function getAllSpecificProperties()
    {
    	$list = new wcmList();
    	$list->refreshByCode("work_type");
    	if (isset($list->id))
    		return wcmList::getContent($list->id);
    	else 
    		return false;
    }
    
    /**
	 * Update all the work info by the data given in the array
	 *
	 * @param Array $array  Array of new infos
	 */
	public function updateWorkTypeInfo($workInfo)
	{
		$this->specific = serialize($workInfo);
	}
    
	 /**
	 * Used for GUI
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
	
	public function save($source = null, $skipGenerate = false)
	{
		//return parent::save($source);
		
		if (!parent::save($source))
		return false;
		// add relation to organisation object with specific kind
     	if (isset($source['cinema_organisation_distributed']) && !empty($source['cinema_organisation_distributed']))
     		$this->setRelation($source['cinema_organisation_distributed'], "organisation", bizrelation::IS_DISTRIBUTED_BY);  		
     	
     	if (isset($source['video_organisation_distributed']) && !empty($source['video_organisation_distributed']))
     		$this->setRelation($source['video_organisation_distributed'], "organisation", bizrelation::IS_DISTRIBUTED_BY);  		
     	
     	if (isset($source['book_organisation_edited']) && !empty($source['book_organisation_edited']))
     		$this->setRelation($source['book_organisation_edited'], "organisation", bizrelation::IS_EDITED_BY);
     	
     	if (isset($source['videogame_organisation_edited']) && !empty($source['videogame_organisation_edited']))
     		$this->setRelation($source['videogame_organisation_edited'], "organisation", bizrelation::IS_EDITED_BY);
     
		return true;	
		
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
	
	/**
	 * get specific info from work
	 *
	 */
	public function getSpecificInfos()
	{		
		$data = array();
		if (isset($this->specific) && !empty($this->specific))
		{
			if (!is_array($this->specific))
				$data = unserialize($this->specific);
			else
				$data = $this->specific;
				
			return $data;
		}
		else
			return false;
	}
	
	public function getSpecificInfosTrad($value)
	{
		$arrayTrad = array(
			"originalTitle"=>"Titre Original",
			"duration"=>"Durée",
			"director"=>"Réalisateur",
			"casting"=>"Casting",
			"summary"=>"Résumé",
			"gender"=>"Genre",
			"country"=>"Pays",
			"otype"=>"Type",
			"titlenb"=>"Nombre de titres",
			"musicStyle"=>"Style de musique",
			"label"=>"Label",
			"copies"=>"Copies",
			"format"=>"Format",
			"bonus"=>"Bonus",
			"price"=>"Prix",
			"pagenb"=>"Nombre de pages",
			"theme"=>"Thème",
			"producer"=>"Producteur",
			"developer"=>"Développeur",
			"public"=>"Public",
			"plateforms"=>"Plateforme"
		);
		
		if (isset($arrayTrad[$value])) 
			return $arrayTrad[$value];
		else 
			return $value;
	}
	
}

