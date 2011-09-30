<?php

/**
 * Project:     WCM
 * File:        biz.forecast.php
 *
 * @copyright   (c)2009 Relaxnews
 * @version     4.x
 *
 */
 /**
 * Definition of an newstext
 */
class forecast extends bizobject
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
    
    public $scheduleIds;
    
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
	
	/**
	* Forecast Rating List 
	*/
	function getRatingList()
	{
	    return array(null, "0", "1", "2", "3", "4");
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
		$config = wcmConfig::getInstance();
        $generate = $this->checkBeforeGenerate($this->id, $this->siteId, 'forecast', $this->workflowState, $config);  
        
        if (parent::save($source))
        {
        	// si la prévision est associée à une liste de type échéancier ou prévistar on créé une notice
        	if (!empty($this->listIds))
        	{
        		if (!is_array($this->listIds)) $liste = unserialize($this->listIds);
        		else $liste = $this->listIds;
        		
        		$idList1 = wcmList::getIdFromCode("prev_previstars");
        		$idList2 = wcmList::getIdFromCode("prev_echeancier");
        		
        		if (array_search($idList1, $liste) || array_search($idList2, $liste))
        		{
        			$this->createNoticeForMedia();
        		}
        	}
        		
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
	
}
