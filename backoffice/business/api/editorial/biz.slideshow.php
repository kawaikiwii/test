<?php 
/**
 * Project:     WCM
 * File:        biz.slideshow.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
 
 /**
 * Slideshow object
 * This is the basic slideshow object
 *
 */

class slideshow extends bizobject {
    /**
     * (int) channel id
     */
    public $channelId;
    
    /**
     * (int) site id
     */
    public $siteId;
    
    /**
     * (string) Slideshow title
     */
    public $title;
    
    /**
     * (string) Keywords
     */
    //public $keywords;
    
    /**
     * (string) Slideshow description
     */
    //public $description;
    
    /**
     * (string) Slideshow HTML text
     */
    //public $text;
    
    /**
     * (date) publication date
     */
    public $publicationDate = null;
    
    /**
     * (date) expiration date
     */
    public $expirationDate = null;
    
    public $embargoDate;
    public $channelIds;
    public $listIds;
    public $folderIds;
    
    public $cId;
    public $iptc;
    //
    // Web 2.0 properties (moderation / contribution / rating / hits)
    //
    
    /**
     * (string) Article moderation kind - see getModerationKindList()
     */
    //public $moderationKind;
    
    /**
     * (string) Contribution mode - see getContributionStateList()
     */
    //public $contributionState;
    
    /**
     * (int) Number of rating done
     */
    //public $ratingCount;
    
    /**
     * (int) Total of ratings
     */
    //public $ratingTotal;
    
    /**
     * (float) Rating value (average: ratingTotal/ratingCount)
     */
    //public $ratingValue;
    
    /**
     * (int) Total number of hits (popularity)
     */
    //public $hitCount;
    
    /**
     * (int) template id
     */
    //public $templateId;
    
    /**
     * Set all initial values of an object
     * This method is invoked by the constructor
     */

    protected function setDefaultValues() {
        $timestamp = time();
        $date_time_array = getdate($timestamp);
        
        $hours = $date_time_array['hours'];
        $minutes = $date_time_array['minutes'];
        $seconds = $date_time_array['seconds'];
        $month = $date_time_array['mon'];
        $day = $date_time_array['mday'];
        $year = $date_time_array['year'];
        
        $timestamp = mktime($hours, $minutes, $seconds, $month + 6, $day, $year);
        
        parent::setDefaultValues();
        //$this->publicationDate = $this->embargoDate = date('Y-m-d');
        //$this->expirationDate = date('Y-m-d', $timestamp);
        $this->siteId = $this->channelId = 0;
        
        // case BANG init source and channel
        $session = wcmSession::getInstance();
        $currentSite = $session->getSite();
        $channel = new channel();
        
        if ($currentSite->code == "bgfr") // BANG FR
	    	$channel->refreshByDescription("mainbangfr");
	        
	    if ($currentSite->code == "bgen") // BANG EN
	    	$channel->refreshByDescription("mainbangen");
	    
    	if (isset($channel->id) && !empty($channel->id))
	    {
	       	$this->channelId = $channel->id; // id default channelId 
	        $this->channelIds = array($channel->id); // id default channelIds 
	    }
	    // end case BANG
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

    public function checkin($source = null, $userId = null) {
        // Insert or update the slideshow object
        return parent::checkin($source, $userId);
    }
    
    /**
     * Returns the number of photos associated to this slideshow
     *
     * @param bool $toXML TRUE if method is called in the context of toXML()
     *
     * @return int Total number of photos
     */

    function getAssoc_photoCount($toXML = false) {
        return $this->getPhotoCount();
    }
    
    /**
     * Exposes 'photos' in the getAssocArray()
     *
     * @param bool $toXML TRUE if method is called in the context of toXML()
     *
     * @return array An array of photos getAssocArray()
     */

    public function getAssoc_photos($toXML = false) {
        $photos = array();
        
        foreach ($this->getPhotos() as $photo) {
            $photoAssoc = $photo->getAssocArray($toXML);
            if ($toXML)
                $photoAssoc = $photoAssoc->toArray();
                
            $photos[] = $photoAssoc;
        }
        
        return $photos;
    }
    
    /**
     * Computes the sql where clause matching foreign constraints
     * => This method must be overloaded by child class
     *
     * @param string $of Assoc Array with foreign constrains (key=className, value=id)
     *
     * @return string Sql where clause matching "of" constraints or null
     */

    function ofClause($of) {
        if ($of == null || !is_array($of))
            return;
            
        $sql = null;
        
        foreach ($of as $key=>$value) {
            switch ($key) {
                case "site":
                    if ($sql != null)
                        $sql .= " AND ";
                    $sql .= "siteId=".$value;
                    break;
                    
                case "channel":
                    if ($sql != null)
                        $sql .= " AND ";
                    $sql .= "channelId=".$value;
                    break;
            }
        }
        return $sql;
    }
    
    /**
     * Returns the number of photos associated to this slideshow
     *
     * @return int Total number of photos
     */

    function getPhotoCount() {
        $result = 0;
        
        $bizrelation = new bizrelation();
        $where = "sourceId=".$this->id." AND sourceClass='".$this->getClass()."' AND destinationClass='photo' AND kind=3";
        if ($bizrelation->beginEnum($where, "rank")) {
            $result = $bizrelation->enumCount();
            $bizrelation->endEnum();
        }
        unset($bizrelation);
        
        return $result;
    }
    
    // use for slideshow xml file generation - remove html extension and replace by xml

    public function getAssoc_xmlslideshow() {
        if (! empty($this->permalinks))
            return substr($this->permalinks, 0, -4)."xml";
        else
            return false;
    }
    
    /**
     * Retrieve photos associated to slideshow
     *
     * @param int $offset Index of first photo to retrieve (default is 0)
     * @param int $limit  Maximum number of photos to retrieve (default iz 0 for all)
     *
     * @return array An array of photo objects belonging to slideshow
     */

    function getPhotos($offset = 0, $limit = -1) {
        $photos = array();
        
        $query = "sourceId=".$this->id." AND sourceClass='".$this->getClass()."' AND destinationClass='photo' AND kind=3";
        
        $bizrelation = new bizrelation();
        if ($bizrelation->beginEnum($query, "rank", $offset, $limit)) {
            // Enumerate content
            while ($bizrelation->nextEnum()) {
                $photos[] = new photo($this->getProject(), $bizrelation->destinationId);
            }
            $bizrelation->endEnum();
        }
        
        return $photos;
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
        // if (!parent::checkValidity()) return false;
     	if (empty($this->channelId))
        {
            $this->lastErrorMsg = _BIZ_ERROR_CHANNELID_IS_MANDATORY;
            //return false;
        }
        
        return true;
    }

    public function save($source = null) {
        /*
         if (empty($this->id))
         $clone = true;
         else
         $clone = false;
         */
        if (isset($source['chooseLanguage'])) {
            $this->duplicateCurrentObjetInOtherLanguages(array('chooseLanguage'=>$source['chooseLanguage'], 'userAssign'=>$source['userAssign']));
        } else {
            $config = wcmConfig::getInstance();
            
            //if (!empty($this->id)) 
            $this->checkBeforeGenerate($this->id, $this->siteId, 'slideshow', $this->workflowState, $config);
            
            if (parent::save($source)) {
                if ($this->generate(false)) {
                    $this->createNoticeForMedia();
                    if ($this->workflowState == "published") {
                        $this->cloneInOtherUniverse($source);
                    }
                    return true;
                } else
                    return false;
            } else
                return false;
        }
    }
    
    /**
     * Gets the 'semantic' text that will be passed to the Text-Mining Engine
     *
     * @return string The semantic text to mine
     */

    public function getSemanticText() {
        $content = '';
        
        if ($this->title)
            $content .= trim($this->title, " \t\n\r\0\x0B.").".\n";
            
        if ($this->xmlTags != NULL) {
            foreach ($this->xmlTags['tags'] as $tag) {
                $content .= ','.$tag;
            }
        }
        
        $contents = $this->getContents();
        
        if (!empty($contents))
        {
	        foreach ($contents as $contentItem) {
	            if ($contentItem->description != NULL)
	                $content .= trim($contentItem->description, " \t\n\r\0\x0B.").".\n";
	            if ($contentItem->text != NULL)
	                $content .= trim($contentItem->text, " \t\n\r\0\x0B.").".\n";
	        }
        }
        
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
     * Refresh object from unique sourceVersion
     *
     * @param int sourceVersion used to refresh 
     *
     * @return the slideshow object
     */
	public function refreshBySourceVersion($sourceVersion) {
        $sql = 'SELECT id FROM '.$this->getTableName().' WHERE sourceVersion=?';
        $id = $this->database->executeScalar($sql, array($sourceVersion));
        return $this->refresh($id);
    }
    
}
