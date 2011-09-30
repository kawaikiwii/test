<?php

/**
 * Project:     WCM
 * File:        biz.news.php
 *
 * @copyright   (c)2008 Relaxnews
 * @version     4.x
 *
 */


class notice extends bizobject
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
	
	public $import_feed;

	public $referentClass;
   
	public $referentId;
   
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
   
    public function checkValidity()
    {
        // if (!parent::checkValidity()) return false;
       
        return true;
    }
	
	public function getAssoc_homeElements($toXML = false)
    {
    	if ($toXML) return null;
		
        $project = wcmProject::getInstance();
        $connector = $project->datalayer->getConnectorByReference("biz");
        $db = $connector->getBusinessDatabase();
		$channelsIdsFiltred = channel::getSubChannelsIds();
		$items = array();

		foreach ($channelsIdsFiltred as $channel)
		{
			$statement = "SELECT * FROM biz_notice WHERE workflowState = 'draft' AND (";
			
			foreach ($channel['ids'] as $loopId => $id)
			{
				if ($loopId != sizeof($channel['ids'])-1)
				{
					$statement .= " channelId = '".$id."' OR";
				}
				else
				{
					$statement .= " channelId = '".$id."' )";
				}
			}
			
			$statement .= " ORDER BY publicationDate DESC LIMIT 10";

	        $rs = $db->executeQuery($statement);
	
			$i = 0;
			while($rs->next())
			{
				$current_news = new notice(null, $rs->get("id"));
				$categorization = $current_news->getAssoc_categorization();
				$medias = $current_news->getAssoc_medias();
				
				$items[$channel['rubric']][$i]['newsObject'] = $current_news;
				$items[$channel['rubric']][$i]['newsContents'] = $current_news->getAssoc_contents();
				$items[$channel['rubric']][$i]['newsCategorization'] = $categorization;
				$items[$channel['rubric']][$i]['newsMedias'] = $medias;
				
				$i++;
			}
		}
		
		return $items;
	}
	
	public function save($source = null, $skipGenerate = false)
	{
		if (isset($source['chooseLanguage']))
		{
			$this->duplicateCurrentObjetInOtherLanguages(array('chooseLanguage' => $source['chooseLanguage'], 'userAssign' => $source['userAssign']));
		}
		else
		{
			$config = wcmConfig::getInstance();
	        	if ($skipGenerate == false)
			{
				$generate = $this->checkBeforeGenerate($this->id, $this->siteId, 'notice', $this->workflowState, $config);
			}
			else { $generate = false; }
			
			if ($generate)
			{
				if (parent::save($source))
				{
					if ($this->generate(false))
					{
						return true; 
					}
					
					else return false;
				}
			}
			else return parent::save($source);
		}
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
	
	public function refreshByReferentObject($referentClass, $referentId) 
	{
        $sql = 'SELECT id FROM '.$this->getTableName().' WHERE referentClass=? AND referentId=?';
        $id = $this->database->executeScalar($sql, array($referentClass, $referentId));
        return $this->refresh($id);
    }
	
	
	/**
	 * 
	 */
	/*
	public function toXML() {
		$xml =  $this->getXmlContents (WCM_DIR . "/business/structures/". $this->getClass() ."/base.php");
		return($xml);
	}
	*/
}

