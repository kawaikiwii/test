<?php

/**
 * Project:     WCM
 * File:        biz.news.php
 *
 * @copyright   (c)2008 Relaxnews
 * @version     4.x
 *
 */
 /**
 * Definition of an newstext
 */
class news extends bizobject
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


	public $embedVideo;
   
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
     	if (empty($this->channelId))
        {
            $this->lastErrorMsg = _BIZ_ERROR_CHANNELID_IS_MANDATORY;
            //return false;
        }
        
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
			$statement = "SELECT * FROM biz_news WHERE workflowState = 'draft' AND (";
			
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
				$current_news = new news(null, $rs->get("id"));
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
	
	public function getAssoc_illustration()
    {
    	//$config = wcmConfig::getInstance();       
    	if (!empty($this->properties))
    	{ 
    		return $this->properties;
    	}
    	return false;
	}
	
	/**
     * this function extract embed content in order to create a specific video
     */
	public function extractEmbedToVideo()
	{
		// test listids with special case such as reminder (252) and agenda (253)
		$checkList = false;
		if (!empty($this->listIds))
		{
			if (!is_array($this->listIds))
				$list = unserialize($this->listIds);
			else 
				$list = $this->listIds;
				
			if (in_array('252', $list) || in_array('253', $list))
				$checkList = true;
		}
		
		// on ne créé pas de vidéo si agenda ou reminder coché
		if (!empty($this->embedVideo) && !$checkList)
		{
			// on récupère le content de la news	   	
	    	$contents = $this->getContents(); 		    	
	    	$video = new video();
	    	
	    	// on teste si une vidéo n'existe pas déjà avec l'id de la news
	    	$testVideoId = $video->refreshBySourceId($this->id);
	    	
	    	if (empty($testVideoId->id))
	    	{
	    		$video->title = $this->title;
		    	$video->siteId = $this->siteId;
		    	$video->sourceId = $this->id;
		    	$video->source = $this->source;
		    	
		    	if (!empty($this->listIds))
		    		$video->listIds = $this->listIds;	
		    	
		    	$videoEmbed = $this->embedVideo;
		    	// on remplace les valeurs de hauteur et largeur par des valeurs par défaut
		    	$videoEmbed = preg_replace('~((?:width)\s?[=:]\s?[\'"]?)[0-9]+~i','${1}450', $videoEmbed);
		    	$videoEmbed = preg_replace('~((?:height)\s?[=:]\s?[\'"]?)[0-9]+~i','${1}360', $videoEmbed);
		    	$video->embed = $videoEmbed;
		    	    	
		    	$video->workflowState = $this->workflowState;
		    	$video->publicationDate = $this->publicationDate;
		    	$video->channelId = $this->channelId;
		    	$video->channelIds = $this->channelIds;
		    	
		    	if ($video->save())
		    	{
		    		//wcmTrace("[EXTRACT EMBED] id : ".$video->id." / title : ".$video->title." (news : ".$this->id.") saved !");  
		    		
		    		//init contenu de la vidéo
		        	$content = new content();
			        $content->referentId = $video->id;
			        $content->referentClass = $video->getClass();
			        $content->provider = "";
			        $content->title = $video->title;
			        
			    	if (isset($contents) && is_array($contents))
					{
						foreach ($contents as $contentItem)
						{
							 $content->description = $contentItem->description;
							 $content->text = $contentItem->text;
						}
					}
			        
			        if (!$content->save())
			        	wcmTrace("[EXTRACT EMBED] id : ".$video->id."  content error !");  	    			
	
		    		// on parcourt les bizrelations de type photo de la news pour les associer à la vidéo
		    		foreach ($this->getPhotos() as $photo) 
			    	{	
						$bizRelation = new bizrelation();
				        $bizRelation->sourceClass = "video";
				        $bizRelation->sourceId = $video->id;
				        $bizRelation->kind = bizrelation::IS_COMPOSED_OF;
				        $bizRelation->destinationClass = "photo";
				        $bizRelation->destinationId = $photo['destinationId'];
				        $bizRelation->title = $photo['title'];
				        $bizRelation->rank = $bizRelation->getLastPosition() + 1;
						$bizRelation->addBizrelation();
				       	//wcmTrace("[EXTRACT EMBED] photo id : ".$photo['destinationId']."  bizrelation  created !");  	    		
			    	}   
			    				
			    	$video->save();
			    	wcmTrace("[EXTRACT EMBED] news title : ".$video->title." video (id:".$video->id.") creation done (news : ".$this->id.") !");
		    		// on met à jour l'info extract sur la propriété sourceVersion de la news
			    	if ($this->sourceVersion != "extract")
			    	{
			    		$this->sourceVersion = "extract";
			    		$this->save();
			    	}
			    	return true; 
		    	}
		    	else
		    	{
		    	    wcmTrace("[EXTRACT EMBED] news title : ".$video->title." video creation error (news : ".$this->id.") !");
		    	    return false;  
		    	}
		    			    	    	    	 
	    	} 
	    	else
	    	{
	    		wcmTrace("[EXTRACT EMBED] existing video : ".$testVideoId->id." (news : ".$this->id.") !"); 
	    		return false;
	    	}	
	    }
	    else
	    	return false;	
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
				$generate = $this->checkBeforeGenerate($this->id, $this->siteId, 'news', $this->workflowState, $config);
			else 
				$generate = false; 
			
			if ($generate)
			{
				if (parent::save($source))
				{
					// si la news est publiée on teste s'il faut créer une vidéo à la vollée 
					if ($this->workflowState == "published") 	
						$this->extractEmbedToVideo();
					
					if ($this->generate(false))
						return true; 					
					else 
						return false;
				}
			}
			else 
			{
				if (parent::save($source))
				{
					// si la news est publiée on teste s'il faut créer une vidéo à la vollée 
					if ($this->workflowState == "published") 	
						$this->extractEmbedToVideo();
						
					return true;
				}
				else
					return false;
			}
			
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
	
	/**
     * Returns an XML representation of a property
     *
     * @param string $propKey    Property key
     * @param mixed  $propValue  Property value
     */
	protected function propertyToXML($propKey, $propValue) 
	{
		if (($propKey == 'photos') && is_array($propValue))
		{
			for ($i=0; $i<sizeof($propValue);$i++)
			{
				if (isset($propValue[$i]['formats']) && !is_array($propValue[$i]['formats']))
					$propValue[$i]['formats'] = unserialize($propValue[$i]['formats']);
			}	
		}
				
        return parent::propertyToXML($propKey, $propValue);
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
    
    /**
     * create a slideshow from a news
     *
     */
	public function createSlideshow() 
	{			
			$config = wcmConfig::getInstance();     
			
	        $slideshow = new slideshow();
	        // on vérifie l'existence d'un diaporama qui aurait déjà été créé avec cette news  
			$slideshow->refreshBySourceVersion($this->id);
			
			// s'il n'existe pas, on le créé
	        if (empty($slideshow->id))
	        {
	            $slideshow->title = $this->title;
	            $slideshow->workflowState = "draft"; 
	            $slideshow->publicationDate = $this->publicationDate;                          
	            $slideshow->revisionNumber = null;
	            $slideshow->versionNumber = 0;
	            $slideshow->permalinks = null;
	            $slideshow->sourceVersion = $this->id;
	            $slideshow->channelId = $this->channelId;
	            $slideshow->channelIds = $this->channelIds;
	            $slideshow->folderIds = $this->folderIds;
	            $slideshow->listIds = $this->listIds;
	            $slideshow->siteId = $this->siteId;
	            $slideshow->source = $this->source;
	            $slideshow->sourceId = $this->sourceId;
	            
	            if ($slideshow->save()) 
	            {     
	            	// récupération des bizrelations de type photo 	            	  	
	                $relations = $this->getRelations();
	        		
	                foreach ($relations as $relation) 
	                {
	                	if ($relation->destinationClass == "photo")
	                	{
		                    $relation->sourceId = $slideshow->id;
		                    $relation->sourceClass = $slideshow->getClass();
		                    $relation->id = 0;
		                    $relation->store();
	                	}
	                }
	                
	                // récupération du content
	                $contents = $this->getContents();
	                
	                foreach ($contents as $content) {
	                    $content->referentId = $slideshow->id;
	                    $content->referentClass = $slideshow->getClass();
	                    $content->id = 0;
	                    $content->store();
	                }
	                     
	                // gestion du permalink
	                if (isset($slideshow->publicationDate) && !($slideshow->publicationDate)) 
	                {
	                    $site = new site();
	                    $siteCode = $site->getCodeFromId($slideshow->siteId);
	                    
	                    $publicationDate = dateOptionsProvider::fieldDateToArray($slideshow->publicationDate);
	                    $publicationPath = "sites/".$site->code.'/'.$this->getClass().'/'.$publicationDate['year'].'/'.$publicationDate['month'].'/'.$publicationDate['day'];
	                    $filename = $publicationDate['hour'].$publicationDate['minute'].'.'.$slideshow->id.'.%format%.html';
	                    $slideshow->permalinks = $publicationPath.'/'.$filename;
	                    
	                }
	                
	                if ($slideshow->save())
	               	 	$this->generate(false);
	                
	                $url = $config['wcm.backOffice.url'].'index.php?_wcmAction=business/'.$slideshow->getClass().'&id='.$slideshow->id;
	                header("Location: ".$url);
	            }
	            else 
	            	return false;
            } 
            else
                return false;        
    }
    
}

