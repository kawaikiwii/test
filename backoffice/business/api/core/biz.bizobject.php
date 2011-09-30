<?php 
/**
 * Project:     WCM
 * File:        biz_object.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
 /**
 * The bizobject class inherits from the {@link wcmBizobject} class
 * and represents business objects.
 * This class can be customized for client needs
 */

abstract class bizobject extends wcmBizobject {
    /**
     * Exposes 'contents' to the getAssocArray
     *
     * @param bool $toXML TRUE if method is called in the context of toXML()
     *
     * @return An array of chapters getAssocArray
     */

    public function getAssoc_contents($toXML = false) {
        $contents = array();
        foreach ($this->getContents() as $content) {
            $contentAssoc = $content->getAssocArray($toXML);
            if ($toXML)
                $contentAssoc = $contentAssoc->toArray();
                
            $contents[] = $contentAssoc;
        }
        
        return $contents;
    }
    
    /**
     * Exposes 'bizRelations' to the getAssocArray
     *
     * @return An array of relateds objects
     */

    public function getAssoc_relateds() {
        $relateds = array();
        
        $bizRelations = wcmBizrelation::getBizobjectRelations($this);
        foreach ($bizRelations as $bizRelation) {
            $related = array("relation"=>$bizRelation, "object"=> new $bizRelation["destinationClass"]($this->getProject(), $bizRelation["destinationId"]));
            $relateds[] = $related;
        }
        return $relateds;
    }


    public function getRelatedsByClassAndKind($classname = null, $kind = null) {
        $relateds = array();
        
        $bizRelations = wcmBizrelation::getBizobjectRelations($this, $kind, $classname);
        foreach ($bizRelations as $bizRelation) {
            $related = array("relation"=>$bizRelation, "object"=> new $bizRelation["destinationClass"]($this->getProject(), $bizRelation["destinationId"]));
            $relateds[] = $related;
        }
        return $relateds;
    }

    public function getAssoc_relatedsByClassAndKind($classname = null, $kind = null) {
        return $this->getRelatedsByClassAndKind($classname, $kind);
    }


    public function getAssoc_getCurrentObj() {
        $obj = $this->getClass();
        $objInst = new $obj();
        $objInst->refresh($this->id);
        
        return array("object"=>$objInst);
    }

    public function getAssoc_sourceLabel() {
        $list = new wcmList();
        $list->refresh($this->source);
        
        if (isset($list->label))
            return $list->label;
        else
            return false;
    }

    public function getContentByFormat($format) {
        $contentRes = array();
        $content = new content();
        $content->getContentByFormat($this->getClass(), $this->id, $format);
        
        if (isset($content->id) && ! empty($content->id)) {
            $contentRes['title'] = $content->title;
            $contentRes['description'] = $content->description;
            $contentRes['text'] = $content->text;
        }
        
        return $contentRes;
    }

    public function getAssoc_contentByFormat($format = "default") {
        return $this->getContentByFormat($format);
    }

    public function getContentByFormatEntities($format = "default", $entities = "html,xml") {
        $contentRes = $this->getContentByFormat($format);
        
        $callEntities = explode(",", $entities);
        
        foreach ($callEntities as $callEntity) {
            if ($callEntity == "html") {
                $contentRes['title'] = htmlentities($contentRes['title'], ENT_NOQUOTES, "UTF-8", false);
                $contentRes['description'] = htmlentities($contentRes['description'], ENT_NOQUOTES, "UTF-8", false);
                $contentRes['text'] = htmlentities($contentRes['text'], ENT_NOQUOTES, "UTF-8", false);
            }
            if ($callEntity == "xml") {
                $contentRes['title'] = xmlEntities($contentRes['title']);
                $contentRes['description'] = xmlEntities($contentRes['description']);
                $contentRes['text'] = xmlEntities($contentRes['text']);
            }
            
        }
        
        return $contentRes;
    }


    public function getAssoc_categorization() {
        $categorization = array();
        
        if (isset($this->channelId) && ! empty($this->channelId)) {
            $channel = new channel($this->getProject(), $this->channelId);
            $categorization["mainChannel_title"] = getConst($channel->title);
            $categorization["mainChannel_css"] = $channel->css;
            $categorization["mainChannel_sourceId"] = $channel->sourceId;
            
            if (! empty($channel->getParentChannel()->id)) {
                $parentChannel = new channel($this->getProject(), $channel->getParentChannel()->id);
                $categorization["parentChannel_id"] = $parentChannel->id;
                $categorization["parentChannel_title"] = getConst($parentChannel->title);
	            $categorization["parentChannel_sourceId"] = $parentChannel->sourceId;
            }
        }
        return $categorization;
    }

    public function getAssoc_categorizationEntities($entities = "html,xml") {
        $categorization = $this->getAssoc_categorization();
        
        $callEntities = explode(",", $entities);
        
        foreach ($callEntities as $callEntity) {
            if ($callEntity == "html") {
                $categorization["mainChannel_title"] = htmlentities($categorization["mainChannel_title"], ENT_NOQUOTES, "UTF-8", false);
            }
            if ($callEntity == "xml") {
                $categorization["mainChannel_title"] = xmlEntities($categorization["mainChannel_title"]);
            }
            
        }
        
        return $categorization;
    }


    public function getAssoc_publicationDateFormatted($toXML = false) {
        if ($toXML)
            return null;
            
        $formatted = array();
        $arrayCut = array('hour'=>substr($this->publicationDate, 11, 2), 'minute'=>substr($this->publicationDate, 14, 2), 'second'=>substr($this->publicationDate, 17, 2), 'month'=>substr($this->publicationDate, 5, 2), 'day'=>substr($this->publicationDate, 8, 2), 'year'=>substr($this->publicationDate, 0, 4));
        $formatted['pathUnderRepository'] = $arrayCut['year'].'/'.$arrayCut['month'].'/'.$arrayCut['day'].'/'.$arrayCut['hour'].$arrayCut['minute'];
        $formatted['displayFormat'] = $arrayCut['day'].'-'.$arrayCut['month'].'-'.$arrayCut['year'].' '.$arrayCut['hour'].':'.$arrayCut['minute'].':'.$arrayCut['second'];
        
        return $formatted;
    }


    public function getAssoc_medias($toXML = false) {
        if ($toXML)
            return null;
            
        if ($this->getClass() == 'forecast')
            return null;
            
        $medias = array();
        
        $mediaRelations = wcmObjectrelation::getBizobjectRelations($this);
        
        $i = 0;
        foreach ($mediaRelations as $mediaRelation) {
            $medias[$i]['content'] = $mediaRelation;
            $temp = new photo();
            $temp->refresh($mediaRelation['destinationId']);
            $medias[$i]['media'] = $temp;
            $medias[$i]['dateTime'] = dateOptionsProvider::fieldDateToArray($temp->createdAt);
            $i++;
        }
        
        return $medias;
    }
    
    /**
     * Used on /templates/search/row.tpl template
     */

    public function getChannelName($channelId) {
        if ($channelId != 0) {
            $channel = new channel(null, $channelId);
            
            return getConst($channel->title);
        } else
            return '~';
    }
    
    /**
     * Returns the contents of the article
     *
     * @return  array An array containing all the bizccontents of the news
     */

    public function getContents() {
        //echo "TRACE=".$this->getClass()."-".$this->id;
        return bizobject::getBizobjects('content', 'referentClass="'.$this->getClass().'" AND referentId='.$this->id, 'format');
    }
    
    /**
     * Returns the title of the news
     *
     * @return  array An array containing all the bizccontents of the news
     */

    public function getTitle() {
        foreach ($this->getContents() as $content) {
            $contentAssoc = $content->getAssocArray(false);
            if ($contentAssoc['format'] == "default")
                return $contentAssoc['title'];
        }
    }
    
    /**
     * Returns the title of the news
     *
     * @return  array An array containing all the bizccontents of the news
     */

    public function getDescription() {
        foreach ($this->getContents() as $content) {
            $contentAssoc = $content->getAssocArray(false);
            if ($contentAssoc['format'] == "default")
                return $contentAssoc['description'];
        }
    }
    
    /**
     * generate all objects of a current classname
     * Used by external call (generateObjects.php) for massive generation
     *
     */

    public function generateAll($where = null, $orderBy = 'id', $source = null) {
        $className = $this->getClass();
        $enum = new $className();
        
        if (!$enum->beginEnum($where, $orderBy))
            return null;
            
        $i = 0;
        while ($enum->nextEnum()) {
            echo $className." : ".$enum->id."\n";
            if ($enum->generate(false, null, true))
                $i++;
            else
                echo $className." : ".$enum->id." - error !\n";
        }
        
        echo $className." : ".$i." generated\n";
        
        $enum->endEnum();
        sleep(1);
    }
    
    /**
     * Save all objects of a current classname
     * Used by external call (saveObjects.php) for massive save
     *
     */

    public function reindexAll($where = '', $orderBy = 'id', $source = null) {
        $className = $this->getClass();
        $enum = new $className();
        
        echo "ReindexAll\n";
        
        if (!$enum->beginEnum($where, $orderBy))
            return null;
        $bizsearch = wcmBizsearch::getInstance();
        
        $i = 0;
        while ($enum->nextEnum()) {
            echo $className." : ".$enum->id."\n";
            if ($bizsearch->indexBizobject($enum))
                $i++;
            else
                echo $className." : ".$enum->id." - error !\n";
        }
        
        echo $className." : ".$i." saved\n";
        
        $enum->endEnum();
        unset($bizsearch);
    }
    
    /**
     * Save all objects of a current classname
     * Used by external call (saveObjects.php) for massive save
     *
     */

    public function saveAll($where = '', $orderBy = 'id', $source = null) {
        $className = $this->getClass();
        $enum = new $className();
        
        if (!$enum->beginEnum($where, $orderBy))
            return null;
            
        $i = 0;
        while ($enum->nextEnum()) {
            echo $className." : ".$enum->id."\n";
            if ($enum->save($source))
                $i++;
            else
                echo $className." : ".$enum->id." - error !\n";
        }
        
        echo $className." : ".$i." saved\n";
        
        $enum->endEnum();
        sleep(1);
    }

    public function save($source = null, $skipGenerate = false) {
        // Force serialised fields items to re-generate
        if ($this->getClass() == 'news' || $this->getClass() == 'event' || $this->getClass() == 'slideshow' || $this->getClass() == 'video' || $this->getClass() == 'prevision' || $this->getClass() == 'notice') {
            // Path Saving
            if ($this->workflowState) {
                if (isset($source['publicationDate']))
                    $publicationDate = $source['publicationDate'];
                else
                    $publicationDate = $this->publicationDate;
                    
                $site = new site();
                $siteCode = $site->getCodeFromId($this->siteId);
                
                $publicationDate = dateOptionsProvider::fieldDateToArray($publicationDate);
                $publicationPath = "sites/".$site->code.'/'.$this->getClass().'/'.$publicationDate['year'].'/'.$publicationDate['month'].'/'.$publicationDate['day'];
                $filename = $publicationDate['hour'].$publicationDate['minute'].'.'.$this->id.'.%format%.html';
                $source['permalinks'] = $publicationPath.'/'.$filename;
            }
            
            if (isset($source['channelId']) && !isset($source['channelIds']))
                $source['channelIds'] = serialize(array($source['channelId']));
                
            if ($this->channelIds && !is_array($this->channelIds))
                $source['channelIds'] = isset($source['channelIds']) ? $source['channelIds'] : unserialize($this->channelIds);
                
            if ($this->listIds && !is_array($this->channelIds))
                $source['listIds'] = isset($source['listIds']) ? $source['listIds'] : unserialize($this->listIds);
                
            if ($this->folderIds && !is_array($this->channelIds))
                $source['folderIds'] = isset($source['folderIds']) ? $source['folderIds'] : unserialize($this->folderIds);
                
            //if (isset($this->formats))
            //    $source['formats'] = isset($source['formats']) ? $source['formats'] : unserialize($this->formats);
        }
        //Condition si l'objet qui est enregistré est un site
        if($this->getClass() == 'site'){
        	//A la création d'un site, on ajoute les droits d'accès à l'admin
        	$account = new account();
        	$account->refresh('20305');
        	$arrayPermissions = $account->getPermissions();
        	//Si la permission n'existe pas déjà, on la rajoute
        	if(!isset($arrayPermissions[$this->id])){
        			$account->addPermission($this->id, false, false);
        			$account->setPermissions();
        	}
        	
        }
        
        // store array IPTC informations 
        if (isset($source['iptc']))
        {
	        // store iptc informations
	    	$iptc = $source['iptc'];
	     	$tabIptc = array();
	     	
	     	if (!empty($iptc))
	     	{
	     		$datas = explode("|", $iptc);
	     		foreach ($datas as $data)
	     		{
	     			if (!empty($data))
	     			{
	     				$val = explode(":", $data);
	     				if (!empty($val))
	     					$tabIptc[$val[1]] = $val[0];	
	     			}
	     		}
	     	}
	     	
	     	$source['iptc'] = $tabIptc;
        }
                
        if (parent::save($source))
            return true;
        else
            return false;
    }

    public function delete() {
        $config = wcmConfig::getInstance();
        $publicationPath = $config['wcm.webSite.path'];
        
        if (! empty($this->permalinks)) {
            $path = $publicationPath.$this->permalinks;
            $pathToOldFiles = array('list', 'detail', 'media', 'print');
            
            foreach ($pathToOldFiles as $pathName) {
                $link = str_replace("%format%", $pathName, $path);
                
                if (file_exists($link))
                    @unlink($link);
                    
                if (file_exists($link) && ($className == 'slideshow' && $pathName == 'detail'))
                    @unlink(substr($link, 0, strlen($link) - 5).'.xml');
            }
        }
        
        $this->deleteContents();
        $this->deleteNoticeByReferent();
  
        return parent::delete();
    }
    
    /**
     * Delete all the chapters from the database
     */

    public function deleteContents() { // Delete all children chapters of this article
        $contents = $this->getContents();
        foreach ($contents as $content) {
            $content->delete(false);
        }
    }
    
    
	/**
     * Delete associated notice
     */
    public function deleteNoticeByReferent() 
    { 
    	$notice = new notice();
    	$notice->refreshByReferentObject($this->getClass(), $this->id);

    	if (!empty($notice->id))
    	{
    		$notice->delete();
    		return true;
    	}
    	else
    		return false;
    }
    
    /**
     * Update all the contents by the contents given in the array
     *
     * @param Array $array  Array of new contents
     */

    public function updateContents($newContent) {
        $this->title = $newContent['title'];
        
        if (isset($newContent['credits'])) {
            $this->credits = $newContent['credits'];
        }
        if (isset($newContent['specialUses'])) {
            $this->specialUses = $newContent['specialUses'];
        }
        $this->serialStorage['content'] = $newContent;
    }
    
    /**
     * Gets object ready to store by getting modified date, creation date etc
     * Will execute transition.
     *
     */

    protected function store() {
        // yul
        /*
         echo '<pre>';
         //print_r( wcmMVC_Action::getAction());
         print_r($this);
         echo '</pre>';
         print_r('<h1>HELLO</h1>');
         exit();*/
        
        if (!parent::store())
            return false;
            
        $newContent = getArrayParameter($this->serialStorage, 'content');
        $newSchedule = getArrayParameter($this->serialStorage, 'schedule');
        
        if ($newContent) {
            $oldContents = $this->getContents();
            
            /*
             * Existing object, update process
             */
            if ($oldContents != NULL) {
                foreach ($oldContents as $iteration=>$myContent) {
                    $myContent = new content($iteration);
                    $myContent->save($newContent);
                }
            }
            
            /*
             * In case this is a new object
             */
            else {
                $content = new content();
                
                $newContent['referentId'] = $this->id;
                $newContent['referentClass'] = $this->getClass();
                $newContent['id'] = 0;
                
                if (!$content->save($newContent)) {
                    $this->lastErrorMsg = $content->lastErrorMsg;
                    return false;
                }
            }
        }
        if ($newSchedule) {
            $this->deleteSchedules();
            
            $schedule = new schedule();
            
            $newSchedule['referentId'] = $this->id;
            $newSchedule['referentClass'] = $this->getClass();
            $newSchedule['id'] = 0;
            
            if (!$schedule->save($newSchedule)) {
                $this->lastErrorMsg = $schedule->lastErrorMsg;
                return false;
            }
        }
        return true;
    }
    
    /*
     Delete the old .html file if exist
     */

    public function checkBeforeGenerate($id, $siteId, $className, $workflowState, $config) {
        $publicationPath = $config['wcm.webSite.path'];
        $newObject = new $className(null, $id);
        
        if (! empty($newObject->permalinks)) {
            $path = $publicationPath.$newObject->permalinks;
            $pathToOldFiles = array('list', 'detail', 'media', 'print');
            
            foreach ($pathToOldFiles as $pathName) {
                $link = str_replace("%format%", $pathName, $path);
                
                if (file_exists($link))
                    @unlink($link);
                    
                if (file_exists($link) && ($className == 'slideshow' && $pathName == 'detail'))
                    @unlink(substr($link, 0, strlen($link) - 5).'.xml');
            }
            return true;
        } else
            return false;
    }
    
    /*
     * Create a new Notice
     */

    function createNoticeForMedia() {
    
        $config = wcmConfig::getInstance();
        $db = new wcmDatabase($config['wcm.businessDB.connectionString']);
        $idMedia = $this->id;
        $mediaType = $this->getClass();
        //$query = "SELECT id, cId, source FROM `biz_notice` WHERE cId = '$idMedia' AND source = '$mediaType'";
        $query = "SELECT id, referentId, referentClass FROM `biz_notice` WHERE referentId = '$idMedia' AND referentClass = '$mediaType'";
        
        $rs = $db->executeQuery($query);
        $rs->first();
        
        while ($record = $rs->getRow()) {
            //if ($record['cId'] == $idMedia && $record['source'] == $mediaType)
            if ($record['referentId'] == $idMedia && $record['referentClass'] == $mediaType) {
                $idNoticeFound = $record['id'];
                break;
            } else {
                break;
            }
        }
        
        if (!isset($idNoticeFound)) {
            $newNotice = new notice();
            $newNotice->title = "(".ucfirst($mediaType).") - `".$this->title."`";
            $newNotice->channelId = $this->channelId;
            $newNotice->channelIds = $this->channelIds;
            $newNotice->publicationDate = $this->publicationDate;
            $newNotice->workflowState = $this->workflowState;
            $newNotice->siteId = $this->siteId;
            //$newNotice->cId = $this->id;
            $newNotice->id = 0;
            //$newNotice->source = $this->getClass();
            $newNotice->mustGenerate = 1;
            $newNotice->referentClass = $this->getClass();
            $newNotice->referentId = $this->id;
            
            if ($newNotice->save()) {
                $newMediaContents = $this->getContents();
                foreach ($newMediaContents as $newMediaContent) 
                {
                    $newContentObj = clone ($newMediaContent);
                    $newContentObj->referentId = $newNotice->id;
                    $newContentObj->referentClass = 'notice';
                    $newContentObj->id = 0;
                    
                    if ($newContentObj->save()) 
                    {
                        $finalNotice = new notice(null, $newNotice->id);
                        return $finalNotice->save();
                    } 
                    else 
                        return false;
                }
            } 
            else 
                return false;
        } 
        else 
        {
        
            //echo "cette notice existe : ".$idNoticeFound;exit();
            
            $newNotice = new notice(null, $idNoticeFound);
            $newNotice->title = "(".ucfirst($mediaType).") - `".$this->title."`";
            $newNotice->channelId = $this->channelId;
            $newNotice->channelIds = $this->channelIds;
            $newNotice->publicationDate = $this->publicationDate;
            $newNotice->workflowState = $this->workflowState;
            $newNotice->mustGenerate = 1;
            $newNotice->referentClass = $this->getClass();
            $newNotice->referentId = $this->id;
            
            $newMediaContents = $this->getContents();
            
            if ($newNotice->save()) {
                $noticeContents = $newNotice->getContents();
                
                // notice content empty !!! get parent object content...
                if (empty($noticeContents))
                {
                	foreach ($newMediaContents as $newMediaContent) 
	                {
	                    $newContentObj = clone ($newMediaContent);
	                    $newContentObj->referentId = $newNotice->id;
	                    $newContentObj->referentClass = 'notice';
	                    $newContentObj->id = 0;
	                    $newContentObj->save();
	                }
                }
                else
                {
                	// notice content exist , upadate content...
	                foreach ($newMediaContents as $newMediaContent) 
	                {
	                    foreach ($noticeContents as $noticeContent) 
	                    {
	                        $noticeContent->title = $newMediaContent->title;
	                        $noticeContent->description = $newMediaContent->description;
	                        $noticeContent->text = $newMediaContent->text;
	                        
	                        if ($noticeContent->save()) 
	                            return true;
	                        else 
	                            return false;
	                    }
	                }
                }
                
            } 
            else 
                return false;
        }
    }
    
    /*
     *	return Label From Id
     */

    function getListLabelFromId($labelId) {
        $listAndSublistsDatas = wcmList::getArborescenceList();
        
        foreach ($listAndSublistsDatas as $list_id=>$list_label) {
            if ($list_id == $labelId) {
                $pos = strrpos($list_label, '-');
                return ucfirst(trim(substr($list_label, $pos + 1)));
            }
        }
        
        return false;
    }


    function getListLabelFromIdFast($labelId) {
        $sourceTable = array('10'=>'AFP', '12'=>'Relaxnews', '250'=>'AFP/Relaxnews');
        if (array_key_exists($labelId, $sourceTable)) {
            return $sourceTable[$labelId];
        } else
            return '~';
    }
    
    /**
     *
     * @return array contains the relation and the object linked to the bizobject
     */

    public function getRelateds() {
        $relateds = array();
        
        $bizRelations = wcmBizrelation::getBizobjectRelations($this);
        foreach ($bizRelations as $bizRelation) {
            $objectRelated = new $bizRelation["destinationClass"]($this->getProject(), $bizRelation["destinationId"]);
            
            $related = array("relation"=>$bizRelation, "object"=>$objectRelated);
            $relateds[] = $related;
        }
        unset($bizRelation);
        return $relateds;
    }

    public function getAllDuplicatedObjectsFromIds($ids) {
        $duplicatedObjects = array();
        $className = $this->getClass();
        
        foreach ($ids as $id) {
            $sql = "SELECT * FROM biz_".$className." WHERE cId='".$id."'";
            $rows = $this->database->executeQuery($sql);
            
            $duplicatedObjectStock = array();
            
            foreach ($rows as $row) {
                $duplicatedObjectStock[] = $row;
            }
            
            $duplicatedObjects[$id] = $duplicatedObjectStock;
        }
        
        return $duplicatedObjects;
    }


    public function getAllSites() {
        $site = new site();
        //return array('fr' => 5, 'en' => 4);
        return $site->getArrayLangSites();
    }

    public function getChannelTagsMatch($chanId, $siteId, $mainChannel = false) {
        $idChannel = array();
        $channel = new channel();
        $channel->refresh($chanId);
        
        //if ($mainChannel)
        $searchTags = $channel->tokens;
        //else
        //	$searchTags = explode(",", $channel->tokens);
        
        $ArrayObjectsStored = wcmCache::fetch('ArrayObjectsStored');
        if ( empty($ArrayObjectsStored)) {
            $ArrayObjectsStored = $channel->storeObjects(null, false, $siteId);
        }
        
        //if ($mainChannel)
        //{
        $tabtags = array();
        
        foreach ($ArrayObjectsStored[$siteId]["channel"] as $rubricId=>$rubricValue) {
            $tabtags[$rubricId] = $rubricValue["tokens"];
        }
        
        foreach ($tabtags as $key=>$val) {
            if ($searchTags == $val) {
                return $key;
            }
        }
        //}
        /*else
         {
         $tabtags = array();
         foreach ($ArrayObjectsStored[$siteId]["channel"] as $rubricId=>$rubricValue)
         $tabtags[$rubricId] = $rubricValue["tokens"];
         
         foreach ($tabtags as $key=>$val)
         {
         foreach($searchTags as $tag)
         {
         //echo "<br>&&&& tag : ".$tag;
         //print_r($tabtags);
         if (in_array($tag, explode(",", $val)))
         {
         $idChannel[] = $key;
         }
         }
         }
         $idChannel = array_unique($idChannel);
         }*/

        return $idChannel;
    }

    public function duplicateCurrentObjetInOtherLanguages($params = NULL) {
        if ($params == NULL)
            return false;
            
        $sites = $this->getAllSites();
        $languagesId = $params['chooseLanguage'];
        $userAssign = $params['userAssign'];
        
        //$pingSession->userId = $userAssign;
        $originalUserId = $_SESSION['wcmSession']->userId;
        $_SESSION['wcmSession']->userId = $userAssign;
        
        foreach ($sites as $siteId=>$siteLanguage) {
            if ($languagesId == $siteId) {
                $newLanguageObject = clone ($this);
                
                /* Disabled 30.01.2009
                 * if ($this->cId == NULL || $this->cId == 0)
                 {
                 $newLanguageObject->cId = $this->id;
                 }*/
                $newLanguageObject->cId = $this->id;
                $newLanguageObject->siteId = $siteId;
                $newLanguageObject->revisionNumber = 0;
                $newLanguageObject->versionNumber = 0;
                $newLanguageObject->workflowState = 'draft';
                $newLanguageObject->mustGenerate = 0;
                
                if (isset($newLanguageObject->import_feed)) {
                    $newLanguageObject->import_feed = NULL;
                }
                
                $newLanguageObject->id = 0;
                
                $chanid = $this->getChannelTagsMatch($this->channelId, $siteId, true);
                if ($chanid)
                    $newLanguageObject->channelId = $chanid;
                    
                $tabChannelId = array();
                
                if (! empty($this->channelIds)) {
                    if (!is_array($this->channelIds))
                        $channelIds = unserialize($this->channelIds);
                    else
                        $channelIds = $this->channelIds;
                        
                    foreach ($channelIds as $channel)
                        $tabChannelId[] = $this->getChannelTagsMatch($channel, $siteId);
                }
                
                if (! empty($tabChannelId))
                    $newLanguageObject->channelIds = $tabChannelId;
                    
                $newLanguageObject->store();
                
                $relations = $this->getRelations();
                $objectId = $newLanguageObject->id;
                $objectClass = $newLanguageObject->getClass();
                foreach ($relations as $relation) {
                    $relation->sourceId = $newLanguageObject->id;
                    $relation->id = 0;
                    $relation->store();
                }
                
                $contents = $this->getContents();
                foreach ($contents as $content) {
                    $content->referentId = $newLanguageObject->id;
                    $content->id = 0;
                    $content->store();
                }
                
                $config = wcmConfig::getInstance();
                $bizsearch = wcmBizsearch::getInstance();
                $bizsearch->indexBizobject($newLanguageObject);
                
                $newLanguageObject->save();
                $this->save();
                
                $url = $config['wcm.backOffice.url'].'business/pages/duplication.php?message=true&urlId='.$objectId;
                
                unset($newLanguageObject);
            }
        }
        
        $_SESSION['wcmSession']->userId = $originalUserId;
        $pingSession = wcmSession::getInstance();
        $pingSession->ping();
        
        header("Location: ".$url);
    }
    
    /**
     * Execute le code php du fichier include
     *
     * @return string executed php code
     * @param string $filename
     */

    function getXmlContents($filename) {
        if (is_file($filename)) {
            ob_start();
            include $filename;
            $contents = ob_get_contents();
            ob_end_clean();
            return $contents;
        }
        return false;
    }
    
    /**
     * Gets current notification id and code
     *
     * @return array with id=>code
     */

    public function getAssoc_notifications() {
        if ($this->getClass() == ('news' || 'event' || 'slideshow' || 'video' || 'notice')) {
            if (! empty($this->listIds)) {
                if (is_array($this->listIds))
                    $objectList = $this->listIds;
                else
                    $objectList = unserialize($this->listIds);
                    
                $wcmList = wcmList::getListFromParentCode("notification");
                
                $result = array();
                
                foreach ($objectList as $lid) {
                    if (array_key_exists($lid, $wcmList))
                        $result[$lid] = $wcmList[$lid];
                }
                return $result;
            } else
                return false;
        } else
            return false;
    }
    
    /**
     * Gets list from listCode
     *
     * @return array with id=>code
     */

    public function getAssoc_list($listCode) {
        if (! empty($listCode))
            return wcmList::getListFromParentCode($listCode);
        else
            return false;
    }
    
    /**
     * Get list of listIds
     *
     * @return array
     */
	
    public function getAssoc_getListIds() {
        if (! empty($this->listIds)) {
            if (is_array($this->listIds))
                $objectList = $this->listIds;
            else
                $objectList = unserialize($this->listIds);
            return $objectList;
        } else {
            return false;
        }
    }
    
    /**
     * Get the Channel SourceIds
     *
     * @return array
     */

    public function getAssoc_ChannelSourceIds() {
        if ($this->getClass() == ('news')) {
            if (isset($this->channelIds) && ! empty($this->channelIds)) {
                //if ($this->channelIds)
                if (!is_array($this->channelIds))
                    $channelIds = unserialize($this->channelIds);
                else
                    $channelIds = $this->channelIds;
                    
                $sourceIds = array();
                foreach ($channelIds as $channelId) {
                    $channel = new channel();
                    $channel->refresh($channelId);
                    $parent = $channel->parentId;
                    $channelparent = new channel();
                    $channelparent->refresh($parent);
                    $tablo = $this->getElementFromIcmCategory($channel->sourceId);
                    //					$sourceIds[] = array('channelId'=>$channelId, 'sourceId'=>$channel->sourceId, 'sourceParentId'=>$channelparent->sourceId);
                    $sourceIds[] = array('channelId'=>$channelId, 'sourceId'=>$channel->sourceId, 'sourceParentId'=>$channelparent->sourceId, 'path'=>$tablo["path"]);
                }
                return $sourceIds;
            } else
                return false;
        } else
            return false;
    }
    
    /**
     * Get the ChannelIds
     *
     * @return array
     */

    public function getAssoc_Channels() 
    {
        if ($this->getClass() == ('news') || $this->getClass() == ('video')) 
        {
            if (isset($this->channelIds) && ! empty($this->channelIds)) 
            {
            	$channels = array();
                if (!is_array($this->channelIds))
                    $channelIds = unserialize($this->channelIds);
                else
                    $channelIds = $this->channelIds;
                    
                foreach ($channelIds as $channelId) 
                {
                    //$channel = new channel();
                    //$channel->refresh($channelId);
                    $channelName = $this->getChannelName($channelId);
                    $channelCode = $this->cleanCodeName($channelName);
                    $channels[] = array('channelId'=>$channelId, 'channelName'=>$channelName, 'channelCode'=>$channelCode);
                }
                return $channels;
            } 
            else
                return false;
        } 
        else
            return false;
    }
    
    /**
     * Get the parent channel css
     *
     * @return css
     */

    public function getAssoc_slugLine() {
        $slugLine = array();
        if (isset($this->channelIds) && ! empty($this->channelIds)) {
            if (!is_array($this->channelIds))
                $channelIds = unserialize($this->channelIds);
            else
                $channelIds = $this->channelIds;
                
            $channel = new channel();
            foreach ($channelIds as $channelId) {
                $channel->refresh($channelId);
                $slug = array();
                $slug["type"] = "channel";
                $slug["id"] = $channel->id;
                $slug["title"] = $channel->title;
                
                $slugLine[] = $slug;
            }
        }
        
        if (! empty($this->listIds)) {
            if (is_array($this->listIds))
                $listIds = $this->listIds;
            else
                $listIds = unserialize($this->listIds);
                
            $list = wcmList::getListFromParentCode("thema");
            
            foreach ($listIds as $listId) {
                if (array_key_exists($listId, $list)) {
                    $thema = new wcmList();
                    $thema->refresh($listId);
                    
                    $slug = array();
                    $slug["type"] = "thema";
                    $slug["id"] = $thema->id;
                    $slug["label"] = $thema->label;
                    $slug["title"] = getConst($thema->label);
                    
                    $slugLine[] = $slug;
                    
                }
            }
            
            $list = wcmList::getListFromParentCode("News_targets");
            
            foreach ($listIds as $listId) {
                if (array_key_exists($listId, $list)) {
                    $target = new wcmList();
                    $target->refresh($listId);
                    
                    $slug = array();
                    $slug["type"] = "target";
                    $slug["id"] = $target->id;
                    $slug["label"] = $target->label;
                    $slug["title"] = getConst($target->label);
                    
                    $slugLine[] = $slug;
                    
                }
            }
						
        }
        
        if (isset($this->folderIds) && ! empty($this->folderIds)) {
            if (!is_array($this->folderIds))
                $folderIds = unserialize($this->folderIds);
            else
                $folderIds = $this->folderIds;
                
            $folder = new folder();
            foreach ($folderIds as $folderId) {
                $folder->refresh($folderId);
                //if ($folder->workflowState == "published" && $folder->siteId == $this->siteId) {
                    $slug = array();
                    $slug["type"] = "folder";
                    $slug["id"] = $folder->id;
                    $slug["title"] = $folder->title;
                    
                    $slugLine[] = $slug;
                //}
            }
        }
        
        return ($slugLine);
    }

	/**
     * Get the geograpicals targets
     *
     * @return listid
     */

    public function getAssoc_geoloc() {
        $slugLine = array();
        if (! empty($this->listIds)) {
            if (is_array($this->listIds))
                $listIds = $this->listIds;
            else
                $listIds = unserialize($this->listIds);
                
            $list = wcmList::getListFromParentCode("News_geo_targets");
            
            foreach ($listIds as $listId) {
                if (array_key_exists($listId, $list)) {
                    $thema = new wcmList();
                    $thema->refresh($listId);
                    
                    $slug = array();
                    $slug["type"] = "geoloc";
                    $slug["id"] = $thema->id;
                    $slug["label"] = $thema->label;
                    $slug["title"] = getConst($thema->label);
                    
                    $slugLine[] = $slug;
                    
                }
            }
						
        }
        
        return ($slugLine);
    }

    public function getAssoc_mainChannelCss() {
        if (isset($this->channelId) && ! empty($this->channelId)) {
            $channel = new channel();
            $channel->refresh($this->channelId);
            return $channel->css;
        } else
            return false;
    }
    
    /**
     * Retrieve items from XML conf file (/xml/configuration.xml)
     *
     */

    function getItemFromXml($className, $nodeName, $nodeChildsName) {
        $rootItemsArray = array();
        $config = wcmConfig::getInstance();
        
        if ($className != false) {
            if (isset($config['afprelax.'.$nodeName.'.'.$className.'.'.$nodeChildsName]))
                $rootItemsArray[] = $config['afprelax.'.$nodeName.'.'.$className.'.'.$nodeChildsName];
            else {
                $i = 0;
                while (isset($config['afprelax.'.$nodeName.'.'.$className.'.'.$nodeChildsName.'.'.$i])) {
                    $rootItemsArray[] = $config['afprelax.'.$nodeName.'.'.$className.'.'.$nodeChildsName.'.'.$i];
                    $i++;
                }
            }
        } else {
            if (isset($config['afprelax.'.$nodeName.'.'.$nodeChildsName]))
                $rootItemsArray[] = $config['afprelax.'.$nodeName.'.'.$nodeChildsName];
            else {
                $i = 0;
                while (isset($config['afprelax.'.$nodeName.'.'.$nodeChildsName.'.'.$i])) {
                    $rootItemsArray[] = $config['afprelax.'.$nodeName.'.'.$nodeChildsName.'.'.$i];
                    $i++;
                }
            }
        }
        
        return $rootItemsArray;
    }
    
    /**
     * get gï¿½nï¿½ral informations from object
     *
     * @author relaxnews
     */

    public function getInfoFromObject($object, $where, $orderBy = 'id') {
        $data = array();
        $enum = new $object();
        
        if (!$enum->beginEnum($where, $orderBy))
            return null;
            
        while ($enum->nextEnum()) {
            $parentTitle = "";
            
            if (isset($enum->parentId)) {
                $objts = clone ($enum);
                // get origin parentId
                while (! empty($objts->parentId))
                    $objts->refresh($objts->parentId);
                    
                $parentTitle = $objts->title;
                unset($objts);
            }
            
            if ($object == "channel") {
                $data[$enum->id] = array('title'=>getConst($enum->title), 'workflowState'=>$enum->workflowState, 'parentId'=>$enum->parentId, 'parentTitle'=>getConst($parentTitle), 'tokens'=>$enum->tokens);
            } else {
                $data[$enum->id] = array('title'=>getConst($enum->title), 'workflowState'=>$enum->workflowState, 'parentId'=>$enum->parentId, 'parentTitle'=>getConst($parentTitle));
            }
        }
        $enum->endEnum();
        
        return $data;
    }
    
    /**
     * store informations for specific objects
     *
     * @author relaxnews
     */

    public function storeObjects($object = null, $cache = true, $idsite = null) {
        if ($object)
            $objects = array($object);
        else
            $objects = $this->getItemFromXml(false, 'storeObjectInCache', 'object');
            
        if ($idsite == null)
            $site = $this->getAllSites();
        else
            $site = array($idsite=>$idsite);
            
        $arrayObjects = array();
        
        if (! empty($site)) {
            foreach ($site as $siteId=>$siteLangue) {
                if (! empty($objects)) {
                    foreach ($objects as $object) {
                        $data = array();
                        $data = $this->getInfoFromObject($object, "siteId ='".$siteId."'");
                        
                        if (! empty($data))
                            $arrayObjects[$siteId][$object] = $data;
                    }
                }
            }
        }
        if ($cache == true) {
            wcmCache::store('ArrayObjectsStored', $arrayObjects);
            return true;
        } else
            return $arrayObjects;
    }

    static function truncate($string, $length, $addDot = false) {
        $output = "";
        settype($string, 'string');
        settype($length, 'integer');
        for ($a = 0; $a < $length AND $a < strlen($string); $a++)
            $output .= $string[$a];
            
        if ($addDot && (strlen($string) > $length))
            $output .= "...";
        return ($output);
    }

    static function getListSourceForGui($sourceId, $exclude=null) 
    {
    	$session = wcmSession::getInstance();
		$currentSite = $session->getSite();
		
        $sourceList = wcmList::getListLabelsFromParentCode("source");
        
        // test for BIPH universe - init source to biph 
        if (empty($sourceId) && (($currentSite->code == "bfr") || ($currentSite->code == "ben")) )
        {
        	$list = new wcmList();
        	$list->refreshByCode("biph");
        	$sourceId = $list->id;	
        }	
        
        // test for BANG universe - init source to bang 
        if (empty($sourceId) && (($currentSite->code == "bgfr") || ($currentSite->code == "bgen")) )
        {
        	$list = new wcmList();
        	$list->refreshByCode("bang");
        	$sourceId = $list->id;	
        }	
        
        if (!empty($sourceList)) {
        	return wcmFormGUI::renderRadiosFieldWithKey('source', $sourceList, $sourceId, _BIZ_SOURCE, "V", $exclude);
        }
              
        return false;
    }

    public function checkIfObjectIsClonableInOtherUniverse($objectId, $siteId) {
        $clonable = false;
        $className = $this->getClass();
        
        $sql = "SELECT id FROM biz_".$className." WHERE cId='".$objectId."' AND siteId='".$siteId."'";
        $result = $this->database->executeScalar($sql);
        if ($result == null)
            $clonable = true;
        else
            return false;
            
        if (! empty($this->cId)) {
            $sql2 = "SELECT id FROM biz_".$className." WHERE id='".$this->cId."' AND siteId='".$siteId."'";
            $result2 = $this->database->executeScalar($sql2);
            if ($result2 == null)
                $clonable = true;
            else
                return false;
        }
        
        return $clonable;
    }

    public function getChannelMatchInOtherUniverse($channelId, $siteId) {
        //$idChannelRelaxfilToAfpRelax = array("211"=>"1", "220"=>"2", "221"=>"2", "195"=>"3", "238"=>"4", "212"=>"5", "214"=>"8", "215"=>"9", "216"=>"9", "217"=>"9", "213"=>"12", "223"=>"13", "224"=>"13", "225"=>"13", "226"=>"16", "227"=>"19", "228"=>"20", "222"=>"21", "229"=>"22", "230"=>"22", "231"=>"22", "232"=>"22", "235"=>"22", "236"=>"22", "237"=>"22", "239"=>"25", "240"=>"25", "241"=>"25", "245"=>"28", "243"=>"29", "242"=>"30", "244"=>"31", "199"=>"32", "200"=>"32", "201"=>"32", "197"=>"36", "210"=>"36", "198"=>"37", "202"=>"38", "203"=>"38", "204"=>"38", "196"=>"42", "205"=>"43", "206"=>"43", "207"=>"43");
        //$idChannelAfpRelaxToRelaxfil = array("1"=>"211", "2"=>"220", "3"=>"195", "4"=>"238", "5"=>"212", "8"=>"214", "9"=>"215", "12"=>"213", "13"=>"223", "16"=>"226", "19"=>"227", "20"=>"228", "21"=>"222", "22"=>"220", "25"=>"239", "28"=>"245", "29"=>"243", "30"=>"242", "31"=>"244", "32"=>"199", "36"=>"197", "37"=>"198", "38"=>"202", "42"=>"196", "43"=>"205", "46"=>"205");
        
        $result = "";
        switch ($siteId) {
            // afprelax FR to relaxfil FR
            case 5:
                //if (array_key_exists($channelId, $idChannelAfpRelaxToRelaxfil))
                //    $result = $idChannelAfpRelaxToRelaxfil[$channelId];
                $chanid = $this->getChannelTagsMatch($channelId, 6);
                if ($chanid)
                    $result = $chanid;
                break;
            // relaxfil FR to afprelax FR
            case 6:
                //if (array_key_exists($channelId, $idChannelRelaxfilToAfpRelax))
                //    $result = $idChannelRelaxfilToAfpRelax[$channelId];
                $chanid = $this->getChannelTagsMatch($channelId, 5);
                if ($chanid)
                    $result = $chanid;
                break;
        }
        return $result;
    }

    public function cloneInOtherUniverse($source) {
        $config = wcmConfig::getInstance();
        
        $relations = $this->getRelations();
        $contents = $this->getContents();
        $channelIds = $this->channelIds;
        $channelId = $this->channelId;
        
        $check = false;
        $oldId = $this->id;
        
        if ($this->generate(false))
            $originalNotice = $this->createNoticeForMedia();
            
        $defaultSiteId = $this->siteId;
        
        switch ($this->siteId) {
            // duplicate in relaxfil FR
            case 5:
                $this->siteId = 6;
                $check = true;
                break;
            // duplicate in afprelax FR
            case 6:
                $this->siteId = 5;
                $check = true;
                break;
        }
        
        if ($check && $this->checkIfObjectIsClonableInOtherUniverse($oldId, $this->siteId)) {
            $this->id = 0;
            $this->revisionNumber = null;
            $this->cId = $oldId;
            
            // cas particulier Slideshow - mettre par défaut en draft dans univers AFP RELAX FR
            if ($this->getClass() == "slideshow" &&  ($this->siteId == 5))
            	$this->workflowState = "draft";
            else 
            	$this->workflowState = "published";
            
            $this->versionNumber = 0;
            $this->permalinks = null;
            
            $chanid = $this->getChannelMatchInOtherUniverse($channelId, $defaultSiteId);
            if ($chanid)
                $this->channelId = $chanid;
                
            $tabChannelId = array();
            if (! empty($channelIds)) {
                foreach ($channelIds as $channel) {
                    $chid = $this->getChannelMatchInOtherUniverse($channel, $defaultSiteId);
                    if ($chid)
                        $tabChannelId[] = $chid;
                }
            }
            
            if (! empty($tabChannelId))
                $this->channelIds = $tabChannelId;
                
            if (parent::save($source)) {
                foreach ($relations as $relation) {
                    $relation->sourceId = $this->id;
                    $relation->id = 0;
                    $relation->store();
                }
                
                $newcontent = new content();
                foreach ($contents as $content) {
                    $content->referentId = $this->id;
                    $content->id = 0;
                    $content->store();
                }
                
                if ($this->generate(false))
                    $this->createNoticeForMedia();
                    
                // gestion du permalink
                if (isset($this->publicationDate) && ! empty($this->publicationDate)) {
                    $site = new site();
                    $siteCode = $site->getCodeFromId($this->siteId);
                    
                    $publicationDate = dateOptionsProvider::fieldDateToArray($this->publicationDate);
                    $publicationPath = "sites/".$site->code.'/'.$this->getClass().'/'.$publicationDate['year'].'/'.$publicationDate['month'].'/'.$publicationDate['day'];
                    $filename = $publicationDate['hour'].$publicationDate['minute'].'.'.$this->id.'.%format%.html';
                    $this->permalinks = $publicationPath.'/'.$filename;
                    $this->save();
                }
                
                $url = $config['wcm.backOffice.url'].'index.php?_wcmAction=business/'.$this->getClass().'&id='.$oldId;
                header("Location: ".$url);
            } else
                return false;
        } else {
            $this->siteId = $defaultSiteId;
            return $originalNotice;
        }
    }

    public function getIcmListCatFromWcmList() {
        return array("123"=>array("icmId"=>"38772", "path"=>"38848-38772"), "124"=>array("icmId"=>"38773", "path"=>"38848-38773"), "107"=>array("icmId"=>"38774", "path"=>"38771-38774"), "112"=>array("icmId"=>"38776", "path"=>"38771-38776"), "105"=>array("icmId"=>"38778", "path"=>"38771-38778"), "102"=>array("icmId"=>"38779", "path"=>"38771-38779"), "111"=>array("icmId"=>"38780", "path"=>"38771-38780"), "245"=>array("icmId"=>"38781", "path"=>"38870-38781"), "109"=>array("icmId"=>"38839", "path"=>"38771-38839"), "128"=>array("icmId"=>"38849", "path"=>"38848-38849"), "1565"=>array("icmId"=>"39418", "path"=>"38771-39418"), "243"=>array("icmId"=>"38925", "path"=>"38848-38925"), "247"=>array("icmId"=>"38871", "path"=>"39016-38871"), "251"=>array("icmId"=>"38876", "path"=>"38870-38876"), "225"=>array("icmId"=>"38997", "path"=>"38994-38997"), "226"=>array("icmId"=>"38998", "path"=>"38994-38998"), "227"=>array("icmId"=>"38999", "path"=>"38994-38999"), "228"=>array("icmId"=>"39000", "path"=>"38994-39000"), "229"=>array("icmId"=>"39001", "path"=>"38994-39001"), "230"=>array("icmId"=>"39002", "path"=>"38994-39002"), "232"=>array("icmId"=>"39004", "path"=>"39003-39004"), "233"=>array("icmId"=>"39005", "path"=>"39003-39005"), "234"=>array("icmId"=>"39006", "path"=>"39003-39006"), "235"=>array("icmId"=>"39007", "path"=>"39003-39007"), "242"=>array("icmId"=>"39008", "path"=>"39003-39008"), "248"=>array("icmId"=>"39017", "path"=>"39016-39017"), "249"=>array("icmId"=>"39018", "path"=>"39016-39018"), "106"=>array("icmId"=>"39049", "path"=>"38771-39049"), "104"=>array("icmId"=>"39051", "path"=>"38771-39051"), "100"=>array("icmId"=>"39415", "path"=>"38771-39415"), "101"=>array("icmId"=>"39416", "path"=>"38771-39416"), "103"=>array("icmId"=>"39417", "path"=>"38771-39417"), "108"=>array("icmId"=>"39418", "path"=>"38771-39418"), "110"=>array("icmId"=>"39419", "path"=>"38771-39419"), "1617"=>array("icmId"=>"39484", "path"=>"38848-39484"), "1618"=>array("icmId"=>"39481", "path"=>"38848-39481"), "1619"=>array("icmId"=>"39482", "path"=>"38848-39482"), "1620"=>array("icmId"=>"39483", "path"=>"38848-39483"));
    }

    public function getWcmChannelFromIcmCategory() {
        return array("37957"=>array("channelId"=>"195", "path"=>"37956-37957"), "37958"=>array("channelId"=>"211", "path"=>"37956-37958"), "37959"=>array("channelId"=>"220", "path"=>"37956-37959"), "37960"=>array("channelId"=>"238", "path"=>"37956-37960"), "38007"=>array("channelId"=>"196", "path"=>"37956-37957-38007"), "38008"=>array("channelId"=>"197", "path"=>"37956-37957-38008"), "38009"=>array("channelId"=>"198", "path"=>"37956-37957-38009"), "38010"=>array("channelId"=>"199", "path"=>"37956-37957-38010"), "38012"=>array("channelId"=>"202", "path"=>"37956-37957-38012"), "38013"=>array("channelId"=>"205", "path"=>"37956-37957-38013"), "38014"=>array("channelId"=>"221", "path"=>"37956-37959-38014"), "38015"=>array("channelId"=>"222", "path"=>"37956-37959-38015"), "38016"=>array("channelId"=>"239", "path"=>"37956-37960-38016"), "38017"=>array("channelId"=>"223", "path"=>"37956-37959-38017"), "38018"=>array("channelId"=>"224", "path"=>"37956-37959-38017-38018"), "38019"=>array("channelId"=>"226", "path"=>"37956-37959-38019"), "38025"=>array("channelId"=>"212", "path"=>"37956-37958-38025"), "38026"=>array("channelId"=>"227", "path"=>"37956-37959-38026"), "38027"=>array("channelId"=>"228", "path"=>"37956-37959-38027"), "38028"=>array("channelId"=>"242", "path"=>"37956-37960-38028"), "38029"=>array("channelId"=>"213", "path"=>"37956-37958-38029"), "38030"=>array("channelId"=>"214", "path"=>"37956-37958-38030"), "38077"=>array("channelId"=>"215", "path"=>"37956-37958-38077"), "38078"=>array("channelId"=>"229", "path"=>"37956-37959-38078"), "38079"=>array("channelId"=>"243", "path"=>"37956-37960-38079"), "38080"=>array("channelId"=>"244", "path"=>"37956-37960-38080"), "38082"=>array("channelId"=>"245", "path"=>"37956-37960-38082"), "38389"=>array("channelId"=>"230", "path"=>"37956-37959-38389"), "38628"=>array("channelId"=>"235", "path"=>"37956-37959-38628"), "38982"=>array("channelId"=>"216", "path"=>"37956-37958-38077-38982"), "38983"=>array("channelId"=>"217", "path"=>"37956-37958-38077-38983"), "38984"=>array("channelId"=>"203", "path"=>"37956-37957-38012-38984"), "38985"=>array("channelId"=>"204", "path"=>"37956-37957-38012-38985"), "38986"=>array("channelId"=>"200", "path"=>"37956-37957-38010-38986"), "38987"=>array("channelId"=>"201", "path"=>"37956-37957-38010-38987"), "38988"=>array("channelId"=>"206", "path"=>"37956-37957-38013-38988"), "38989"=>array("channelId"=>"207", "path"=>"37956-37957-38013-38989"), "38990"=>array("channelId"=>"231", "path"=>"37956-37959-38389-38990"), "38991"=>array("channelId"=>"232", "path"=>"37956-37959-38389-38991"), "38992"=>array("channelId"=>"240", "path"=>"37956-37960-38016-38992"), "38993"=>array("channelId"=>"241", "path"=>"37956-37960-38016-38993"), "39023"=>array("channelId"=>"236", "path"=>"37956-37959-38628-39023"), "39024"=>array("channelId"=>"237", "path"=>"37956-37959-38628-39024"), "39025"=>array("channelId"=>"225", "path"=>"37956-37959-38017-39025"), "39046"=>array("channelId"=>"210", "path"=>"37956-37957-39046"), "38010"=>array("channelId"=>"254", "path"=>"37956-37957-38010"), "38027"=>array("channelId"=>"255", "path"=>"37956-37959-38027"), "38029"=>array("channelId"=>"248", "path"=>"37956-37958-38029"), "38077"=>array("channelId"=>"249", "path"=>"37956-37958-38077"), "38077"=>array("channelId"=>"250", "path"=>"37956-37958-38077"), "38082"=>array("channelId"=>"256", "path"=>"37956-37960-38082"), "38082"=>array("channelId"=>"257", "path"=>"37956-37960-38082"), "38389"=>array("channelId"=>"252", "path"=>"37956-37959-38389"), "38389"=>array("channelId"=>"253", "path"=>"37956-37959-38389"), "38628"=>array("channelId"=>"251", "path"=>"37956-37959-38628"));
    }
    
    /**
     * get category Id and category path (icm) from listId (wcm)
     *
     * @author relaxnews
     */

    public function getElementFromWcmList($elem = null) {
        if ($elem) {
            $icmList = $this->getIcmListCatFromWcmList();
            return $icmList[$elem];
        }
    }
    
    /**
     * get channelId (wcm) and category path (icm) from icm category Id (sourceId wcm)
     *
     * @author relaxnews
     */

    public function getElementFromIcmCategory($elem = null) {
        if ($elem) {
            $wcmChannel = $this->getWcmChannelFromIcmCategory();
            return $wcmChannel[$elem];
        }
    }
    
    /**
     * cleanStringFromSpecialChar
     *
     * remove special chars from string
     * @param string $data  = the string to clean
     * @param bool $xml TRUE if use in XML context
     *
     * @author relaxnews
     */

    static function cleanStringFromSpecialChar($data) {
        //wcmTrace("Clean data : ".$data);
    	$replaceTab = array("â€š"=>",", "Æ’"=>"", "â€ž"=>"", "â€¦"=>"...", "â€ "=>"", "â€¡"=>"", "Ë†"=>"", "â€°"=>"", "Å "=>"", "â€¹"=>"&#8249;", "Å’"=>"OE", "â€˜"=>"'", "â€™"=>"'", "â€œ"=>"\"", "â€�"=>"\"", "â€¢"=>".", "â€“"=>"-", "â€”"=>"-", "Ëœ"=>"", "â„¢"=>"", "Å¡"=>"s", "â€º"=>"&#8250;", "Å“"=>"oe", "Å¸"=>"Y", "â‚¬"=>"&#8364;");
        
        $data = str_replace(array_keys($replaceTab), array_values($replaceTab), $data);
        //wcmTrace("new data : ".$data);
        
        return $data;
    }

	 //$children permet de prendre en compte ou pas les sous-catÃ©gories
    public function getAccountPermissions($children = "") {
        $session = wcmSession::getInstance();
        $permissions = wcmCache::fetch($session->userId."-permissions");
        if ( empty($permissions)) {        
        		$account = new account();
            $account->refreshByWcmUser($session->userId);
            $permissions = $account->getPermissions($children);
            wcmCache::store($session->userId."-permissions", $permissions, 60);
        }
        
        unset($session);
        unset($account);
        return ($permissions);
    }
    
    /**
     *
     */

    //$children permet de prendre en compte ou pas les sous-catÃ©gories
    function isServiceAllowed($children = "") {
        $session = wcmSession::getInstance();
        $classname = $this->getClass();
        $permissions = $this->getAccountPermissions($children);
        
        if (count($permissions) == 0)
            return (true);
        
        if (!array_key_exists($session->getSiteId(), $permissions)) {
            return (false);
        } else {
            if (array_key_exists("*", $permissions[$session->getSiteId()]))
                return (true);
        }
        
        if (array_key_exists($classname, $permissions[$session->getSiteId()])) {
            return (true);
        } else {
            return (false);
        }
        
        return (false);
        
    }
    
    //Lors de l'appel de la fonction, laisser Ã  vide si catÃ©gories secondaires, sinon mettre "primaire"
    //De mÃªme, mettre "children" s'il faut prendre en compte les sous-catÃ©gories implicites (ex : livres et BD quand livres-BD est autorisÃ©), sinon laisser Ã  vide
    function isAllowed($category = "", $children = "") {
        $classname = $this->getClass();
        if($classname == "notice")
			$classname = $this->referentClass;
        $permissions = $this->getAccountPermissions($children);
        
        if (count($permissions) == 0)
            return (true);
        
        if (!array_key_exists($this->siteId, $permissions)) {
            return (false);
        } else {
            if (array_key_exists("*", $permissions[$this->siteId]))
                return (true);
        }
        
        if (!array_key_exists($classname, $permissions[$this->siteId])) {
            return (false);
        } else {
            if (array_key_exists("*", $permissions[$this->siteId][$classname]))
                return (true);
        }
        
        //Permet de ne sÃ©lectionner que les rubriques primaires
        if($category == "primaire")
            $channels[] = $this->channelId;
        else
            $channels = unserialize($this->channelIds);
        $intersect = array_intersect($channels, $permissions[$this->siteId][$classname]);
        /*echo "<xmp>";
         print_r($channels);
         print_r($permissions);
         print_r($intersect);
         echo "</xmp>";
         */
        if (count($intersect) > 0)
            return (true);
            
        return (false);
        
    }
    
	public function cleanCodeName($name) {
        $replaceTab = array("&"=>"", " "=>"", "/"=>"", "\\"=>"");
        
        $name = str_replace(array_keys($replaceTab), array_values($replaceTab), $name);
        return trim($name);
    }
    
    /*
     * fonction utilisÃ©e pour le clonage d'un objet dans un univers donnÃ©
     */
	public function duplicateObjetInSpecificSite($siteId)
	{     
		if ($this->checkIfObjectIsClonableInOtherUniverse($this->id, $siteId))
		{
			$newLanguageObject = clone ($this);           
			$newLanguageObject->cId = $this->id;
			$newLanguageObject->siteId = $siteId;
			$newLanguageObject->revisionNumber = 0;
			$newLanguageObject->versionNumber = 0;
			$newLanguageObject->workflowState = 'draft';
			$newLanguageObject->mustGenerate = 0;
			$newLanguageObject->id = 0;
			
			$chanid = $this->getChannelTagsMatch($this->channelId, $siteId, true);
			if ($chanid)
			    $newLanguageObject->channelId = $chanid;
			
			$tabChannelId = array();
			
			if (!empty($this->channelIds)) {
			    if (!is_array($this->channelIds))
				$channelIds = unserialize($this->channelIds);
			    else
				$channelIds = $this->channelIds;
			
			    foreach ($channelIds as $channel)
				$tabChannelId[] = $this->getChannelTagsMatch($channel, $siteId);
			}
			
			if (! empty($tabChannelId))
			    $newLanguageObject->channelIds = $tabChannelId;
			
			$newLanguageObject->store();
			
			$relations = $this->getRelations();
			$objectId = $newLanguageObject->id;
			$objectClass = $newLanguageObject->getClass();
			foreach ($relations as $relation) {
			    $relation->sourceId = $newLanguageObject->id;
			    $relation->id = 0;
			    $relation->store();
			}
			
			$contents = $this->getContents();
			foreach ($contents as $content) {
			    $content->referentId = $newLanguageObject->id;
			    $content->id = 0;
			    $content->store();
			}
			
			$config = wcmConfig::getInstance();
			$bizsearch = wcmBizsearch::getInstance();
			$bizsearch->indexBizobject($newLanguageObject);
			
			$newLanguageObject->save();
			$this->save();
			
			unset($newLanguageObject);
			
			return true;
		}
		else
			return false;
	}
}

