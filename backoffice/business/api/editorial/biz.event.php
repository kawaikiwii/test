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

class event extends bizobject {

    /**
     * (int) site id
     */
    public $siteId;
    
    /**
     * (int) channel id
     */
    public $channelId;
    public $listIds;
    public $channelIds;
    public $folderIds;
    
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
    
    public $startDate;
    public $endDate;
    
    public $phone;
    public $email;
    public $website;
    
    public $sourceLocation;
    
    public $price;
    public $dateComment;
    public $pressContact;
    
    public $cId;
    
    public $homeSelection;
    
    public $iptc;
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
        
        $this->siteId = $this->channelId = 0;
    }

    public function checkValidity() {
        // if (!parent::checkValidity()) return false;
        
        return true;
        
    }

    public function save($source = null) {
        if (isset($source['chooseLanguage'])) {
            $this->duplicateCurrentObjetInOtherLanguages(array('chooseLanguage'=>$source['chooseLanguage'], 'userAssign'=>$source['userAssign']));
        } else {
            $config = wcmConfig::getInstance();
            
            $this->checkBeforeGenerate($this->id, $this->siteId, 'video', $this->workflowState, $config);
            
            if (parent::save($source)) {
                if ($this->generate(false)) {
                    $this->createNoticeForMedia();
                    return true;
                } else
                    return false;
                    
            } else
                return false;
                
        }
    }

    public function getAssoc_schedules($toXML = false) {
        $schedules = array();
        foreach ($this->getSchedules() as $schedule) {
            $scheduleAssoc = $schedule->getAssocArray($toXML);
            if ($toXML)
                $scheduleAssoc = $scheduleAssoc->toArray();
                
            $schedules[] = $scheduleAssoc;
        }
        
        return $schedules;
    }
    
    /**
     * Returns the schedules of the event
     *
     * @return  array An array containing all the bizccontents of the news
     */

    public function getSchedules() {
        return bizobject::getBizobjects('schedule', 'referentClass="'.$this->getClass().'" AND referentId='.$this->id, 'format');
    }


    public function deleteSchedules() {
        $schedules = $this->getSchedules();
        foreach ($schedules as $schedule) {
            $schedule->delete(false);
        }
    }
    
    /**
     * Update all the schedules by the contents given in the array
     *
     * @param Array $array  Array of new contents
     */

    public function updateSchedules($newSchedule) {
        if (isset($newSchedule['startsAt'])) {
            $this->startDate = $newSchedule['startsAt'];
            $this->endDate = $newSchedule['endsAt'];
        }
        
        $this->serialStorage['schedule'] = $newSchedule;
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


    public function lastEventsSelected($numberOfMonth = 3, $siteId) {
        $className = $this->getClass();
        $enum = new $className();
        $result = array();
        
        if (!$enum->beginEnum("workflowState='published' AND homeSelection=1 AND startdate > now() AND startdate <= DATE_ADD(NOW(), INTERVAL + ".$numberOfMonth." MONTH) AND publicationDate <= NOW() AND siteId=".$siteId, "startdate DESC"))
			return null;
                
        while ($enum->nextEnum()) {
            $item = array();
            $item['id'] = $enum->id;
            $item['permalinks'] = $enum->permalinks;
            $result[] = $item;
        }
        $enum->endEnum();
        
        return $result;
    }

    public function mustSeeEvents($numberOfMonth = 3, $siteId) {
        $className = $this->getClass();
        $enum = new $className();
        $result = array();
        
        if (!$enum->beginEnum("workflowState='published' AND DATE(startDate)>=DATE(NOW()) AND startdate <= DATE_ADD(NOW(), INTERVAL + ".$numberOfMonth." MONTH) AND publicationDate <= NOW() AND siteId='$siteId'", "startDate DESC"))
			return null;
        //         if (!$enum->beginEnum("workflowState='published' AND homeSelection!=1 AND startDate!='NULL' AND channelId != 'NULL' AND DATE(startDate)>=DATE(NOW()) AND siteId=".$siteId, "startDate ASC limit 0,".$displayNumber))
        
        while ($enum->nextEnum()) {
            //echo $enum->id;
            $item = array();
            $item['id'] = $enum->id;
            $item['permalinks'] = $enum->permalinks;
            $result[] = $item;
        }
        
        $enum->endEnum();
        
        return $result;
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
}

