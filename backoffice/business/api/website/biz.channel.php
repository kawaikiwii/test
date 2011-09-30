<?php
/**
 * Project:     WCM
 * File:        biz.channel.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * Definition of a channel
 */
class channel extends bizobject
{
    /**
     * (int) site Id
     */
    public $siteId;

    /**
     * (int) parent Id
     */
    public $parentId = null;

    /**
     * (int) rank
     */
    public $rank;

    /**
     * (string) title
     */
    public $title;

    /**
     * (string) description
     */
    public $description;

    /**
     * (int) template id
     */
    public $templateId;

    /**
     * (string) image path
     */
    public $imagePath = "";

    /**
     * (string) keywords
     */
    public $keywords;

    /**
     * (string) management kind
     */
    public $managementKind;

    /**
     * (string) native request used to get content
     */

    public $request;

    /**
     * (int) publication Id
     */
    public $publicationId;

    /**
     * (string) css information
     */

    public $css;
    
    public $tokens;
    
    public $iptc;

    /**
     * Set all initial values of an object
     * This method is invoked by the constructor
     */
    protected function setDefaultValues()
    {
        parent::setDefaultValues();

        $this->parentId = null;
        $this->rank = 0;
        $this->managementKind = 'date';
        $config = wcmConfig::getInstance();
        $this->request = null; //serialize(array("query" => $config['wcm.channel.query']));
    }

    /**
     * Refresh channel by its title
     *
     * @param string $title Channel's title
     */
    public function refreshByTitle($title)
    {
        return $this->refresh($this->database->executeScalar("SELECT id FROM $this->tableName WHERE title=?", array($title)));
    }
 
 	/**
     * get channel Id from its sourceId
     *
     * @param int $sourceId Channel's sourceId
     */
    public function getIdFromSourceId($sourceId)
    {
    	$sql = "SELECT id FROM ".$this->tableName." WHERE sourceId=".$sourceId;
        $result = $this->database->executeScalar($sql);	
    	
    	if (!empty($result)) return $result;
    	else return false;
    }
    
    /**
     * Return the parent channel of the object
     *
     * @return channel parent channel or null
     */
    public function getParentChannel()
    {
        $channel = new channel(null, $this->parentId);
        return ($channel->id) ? $channel : null;
    }
	
	/**
	 * Return the complete path of channel
	 * @return 
	 *
	
	public function getChannelPath($separator = '-')
    {
    	$path = array();
		$path[] = $this->id;
		$channels = $this->getAncestors();
		foreach($channels as $channel) {
			$path[] = $channel->id;
		}
		
		$path = array_reverse($path);
		
		return (implode($separator, $path));
    }	*/
	
	
	
	public function getChannelPath($separator = '-', $field = null)
    {
    	$path = array();
		if ($field)
			$path[] = $this->$field;
		else
			$path[] = $this->id;
		$channels = $this->getAncestors();
		foreach($channels as $channel) 
		{
			if ($field)
				$path[] = $channel->$field;
			else
				$path[] = $channel->id;
		}
		
		$path = array_reverse($path);
		
		return (implode($separator, $path));
    }	
	
    /**
     * Inserts or Updates object in database
     *
     * @return boolean true on success, false on failure
     */
    protected function store()
    {
        if (!parent::store())
            return false;

        // Update cache of channels (of site)
        wcmCache::setElem('channel_of_site' . $this->siteId, $this->id, $this);
        return true;
    }

    /**
     * Deletes current object from database
     *
     * @return true on success or an error message (string)
     */
    public function delete($userId = null)
    {
        $sid = $this->siteId;
        $id = $this->id;
        if (!parent::delete())
            return false;

        // Update cache
        wcmCache::unsetElem('channel_of_site' . $sid, $id);

        return true;
    }

    /**
     * Computes the sql where clause matching foreign constraints
     * => This method must be overloaded by child class
     *
     * @param string $of Assoc Array with foreign constrains (key=className, value=id)
     *
     * @return string Sql where clause matching "of" constraints or null
     */
    function ofClause($of)
    {
        if ($of == null || !is_array($of))
            return;

        $sql = null;

        foreach($of as $key => $value)
        {
            switch($key)
            {
                case "site":
                    if ($sql != null) $sql .= " AND ";
                    $sql .= "siteId=".$value;
                    break;
            }
        }
        return $sql;
    }

    /**
    * Says whether the id of the instance is present in the relation array
    *
    * @param  $objectid - object to test
    * @param $relations - array of relation ids
    *
    * @return true if the id is prensent
    */
    public function scanlist($objectid, $objectclass, $relations)
    {
        foreach($relations as $key => $value)
        {
            $arr = explode('-', $value);
            $relclass = $arr[0];
            $relid = $arr[1];

            if($objectid == $relid && $objectclass == $relclass)
                return true;
        }
        return false;
    }

    /**
    * Returns content of the channel
    *
    * @param  int  $limit Optional maximum number of objects to return
    * @param  date $date  Optional date used to compute channel's content (default is today's date)
    * @param  bool $returnBizobject Return related bizobject when a bizrelation if found (default is false)
    * @param  bool $toXML TRUE when this method is invoked in the context of 'toXML()' (default is false)
    *
    * @return array Array of bizobjects (or null if management method is invalid)
    */
    public function getContent($limit=10, $date = null, $returnBizobject = false, $toXML = false)
    {
        // set default value for date parameter?
        if ($date === null) $date = date('Y-m-d');

        //get query
        $search_query = unserialize($this->request);

        $query = (isset($search_query['query'])?$search_query['query']:'');
		//replace smart tag @channelId if any
		$query = str_replace("@channelId", $this->id, $query); 
        
        $orderBy = isset($search_query['orderBy'])?$search_query['orderBy']:null;
        $limit = (isset($search_query['limit']) && is_numeric($search_query['limit']))?$search_query['limit']:$limit;

        $results = array();
        if($query != "")
        {
            $config = wcmConfig::getInstance();
            $engine = $config['wcm.search.engine'];
            $search = wcmBizsearch::getInstance($engine);
            $total = $search->initSearch('quickSearch', $query, $orderBy);
            $results = $search->getDocumentRange(0, $limit, 'quickSearch', false);
        }
        //get relations
        $relations = wcmBizrelation::getBizobjectRelations($this,wcmBizrelation::IS_COMPOSED_OF, null, $toXML);

        //sort them in order
        $mixedresults = array();
        $forcedcontentids = array();
        foreach($relations as $rel)
        {
            $destclass = $rel['destinationClass'];
            $destid = $rel['destinationId'];
            $forcedcontentids[] = $destclass."-".$destid;

            if($returnBizobject)
                $mixedresults[$rel['rank']] = new $destclass(wcmProject::getInstance(), $destid);
            else
                $mixedresults[$rel['rank']] = $rel;

        }
        $counter = 0;
        $counter2 = 1;
        for($i=1; $i<$limit; $i++)
        {
            if(isset($results[$counter]) && $this->scanlist($results[$counter]->id, get_class($results[$counter]), $forcedcontentids))
            {
                $counter++;
            }
            else
            {
                if(!isset($mixedresults[$counter2]) && isset($results[$counter]))
                {
                    $mixedresults[$counter2] = $results[$counter];
                    $counter++;
                }
                else if(!isset($mixedresults[$counter2]))
                {
                    $mixedresults[$counter2] = null;
                }

                $counter2++;
            }

        }
        //sort using indexes (which are the ranks)
        ksort($mixedresults);

        return $mixedresults;
    }

    /**
    * Returns an array of bizobjects for a specific date
    *
    * @param  int $limit Optional maximum number of objects to return
    * @param  date  $referenceDate Date used for content retrieval (or null for last available content)
    *
    * @return array Array of bizobjects
    */
    public function getContent_date($limit=0, $referenceDate = null)
    {
        // Prepare returned result
         $content = array();

        // Check referenceDate
        if (!$referenceDate) $referenceDate = date('Y-m-d');

        // Build the bizrelation object from the channel class and id
        $bizrelation = new wcmBizrelation();
        $bizrelation->sourceClass = $this->getClass();
        $bizrelation->sourceId = $this->id;
        $bizrelation->kind = bizrelation::IS_COMPOSED_OF;

       // Get last validityDate
        $referenceDate = $bizrelation->getLatestValidityDate($referenceDate);

        if ($referenceDate)
        {
            $bizrelation->beginEnumOnDate($lastDate, 0, $limit);
            while($bizrelation->nextEnum())
            {
                $bizobject = new $bizrelation->destinationClass(null, $bizrelation->destinationId);
                if ($bizobject->id)
                {
                    $content[] = $bizobject;
                }
                else
                {
                    // related object does not exists anymore!
                    $bizrelation->delete();
                }
            }
            $bizrelation->endEnum();
        }

        return $content;
    }

    /**
     * Returns articles that match the query or that just
     * belongs to the channel if no query has been set
     *
     * @param int $offset Offset used for the search (0 by default)
     * @param int $limit Maximum number of articles returned (0, by default, for all)
     *
     * @return array An associative array of bizobjects (keys are ids)
     */
    public function getContent_query($offset=0, $limit=0)
    {
        $content = array();

        if ($this->request)
        {
            // Invoke bizsearch
        }
        else
        {
            // Retrieve articles
            $article = new article();
            $article->beginEnum('channelId='.$this->id, 'publicationDate DESC', $offset, $limit);
            while ($article->nextEnum())
            {
                $content[] = clone($article);
            }
            $article->endEnum();
            unset($article);
        }
        return $content;
    }

    /**
     * Returns an array of all channel's ancestors
     * The array is sorted in ancestry order (e.g. parent, grand-parent, etc...)
     *
     * @return array An array of channel objects
     */
    public function getAncestors()
    {
        $ancestors = array();

        $channel = $this;
        while ($channel->parentId)
        {
            $channel->refresh($channel->parentId);
            if ($channel->id)
            {
                $ancestors[] = clone($channel);
            }
            else
            {
                // Well, this parent is invalid, it's a good
                // opportunity to fix that!
                $channel->parentId = null;
                $channel->save();
            }
        }
        unset($channel);

        return $ancestors;
    }

    /**
     * Returns the list of available method to organize content
     *
     * @return array An assoc array (method => method title)
     */
    public function getChannelOrganisationList()
    {
        $array = array("date"   => _BIZ_DATE_MANAGEMENT,
                       "query" => 'Query based',
                       "mixed" => _BIZ_MIXED_MANAGEMENT);
        foreach($array as $key => $value)
        {
            if (!method_exists($this, "getContent_".$key))
               unset($array[$key]);
        }

        return $array;
    }

	

    /**
     * Returns hierarchical array of channel's paths for current site
     *
     * @param array $channels Array to update (should be initialized)
     * @param int $excludeId Optional ID of menu to exclud forom the list
     * @param string $prefix Initial prefix (used for recursive call)
     * @param int $parentId Initial channel (or zero to take root channels, by default)
     * @param string $separator Separator tu use in path (default is ' :: ')
     *
     * @return array An assoc array of channels in hierarchical order (key is id, value is channel path)
     */
    static function getChannelHierarchy(array &$channels = null, $excludeId=0, $prefix=null, $parentId=0, $separator = ' :: ')
    {
        if (!$channels) $channels = array();
        $project = wcmProject::getInstance();
        $session = wcmSession::getInstance();

        // load channels of site from cache
        $channelList = wcmCache::fetch('channel_of_site' . $session->getSiteId());
		
        if ($channelList === FALSE)
        {
            $enum = new channel($project);
            if (!$enum->beginEnum('siteId = ' . $session->getSiteId() , 'parentId, rank')) return null;
            $channelList = array();
            while ($enum->nextEnum())
            {
                $channelList[$enum->id] = clone($enum);
            }
            $enum->endEnum();
            wcmCache::store('channel_of_site' . $session->getSiteId(), $channelList);
        }

        foreach($channelList as $channel)
        {
        	//echo $channel->title."<br />\n";
            if ($channel->parentId == $parentId)
            {	//echo $channel->title."<br />\n";
            	$channels[""] = null;
                // Add menu (unless it is excluded)
                $path = ($prefix) ? $prefix . $separator . getConst($channel->title) : getConst($channel->title);
                if ($channel->id != $excludeId)
                {
                    $channels[$channel->id] = $path;
                }

                // Recursively add sub-menus
                channel::getChannelHierarchy($channels, $excludeId, $path, $channel->id, $separator);
            }
        }
        return $channels;
    }
	
	static function getChannelHierarchyBySiteId($siteId=0, array &$channels = null, $excludeId=0, $prefix=null, $parentId=0, $separator = ' :: ')
    {
        if (!$channels) $channels = array();
        $project = wcmProject::getInstance();
        $session = wcmSession::getInstance();

        // load channels of site from cache
        if (empty($siteId))
        	$siteId = $session->getSiteId();			
		
		$channelList = wcmCache::fetch('channel_of_site' . $siteId);
        if ($channelList === FALSE)
        {
            $enum = new channel($project);
            if (!$enum->beginEnum('siteId = ' . $siteId , 'parentId, rank')) return null;
            $channelList = array();
            while ($enum->nextEnum())
            {
                $channelList[$enum->id] = clone($enum);
            }
            $enum->endEnum();
            wcmCache::store('channel_of_site' . $siteId, $channelList);
        }

        foreach($channelList as $channel)
        {
        	//echo $channel->title."<br />\n";
            if ($channel->parentId == $parentId)
            {	//echo $channel->title."<br />\n";
            	//$channels[""] = null;
                // Add menu (unless it is excluded)
                $path = ($prefix) ? $prefix . $separator . getConst($channel->title) : getConst($channel->title);
                if ($channel->id != $excludeId)
                {
                    $channels[$channel->id] = $path;
                }

                // Recursively add sub-menus
                channel::getChannelHierarchyBySiteId($siteId, $channels, $excludeId, $path, $channel->id, $separator);
            }
        }
        return $channels;
    }
    
	static function getChannelHierarchyConst(array &$channels = null, $excludeId=0, $prefix=null, $parentId=0, $separator = ' :: ')
    {
        if (!$channels) $channels = array();
        $project = wcmProject::getInstance();
        $session = wcmSession::getInstance();

        // load channels of site from cache
        $channelList = wcmCache::fetch('channel_of_site' . $session->getSiteId());
		
        if ($channelList === FALSE)
        {
            $enum = new channel($project);
            if (!$enum->beginEnum('siteId = '.$session->getSiteId().' AND workflowState="published"' , 'rank, parentId')) return null;
            $channelList = array();
            while ($enum->nextEnum())
            {
                $channelList[$enum->id] = clone($enum);
            }
            $enum->endEnum();
            wcmCache::store('channel_of_site' . $session->getSiteId(), $channelList, 1800);
        }

        foreach($channelList as $channel)
        {
        	//echo $channel->title."<br />\n";
            if ($channel->parentId == $parentId)
            {	//echo $channel->title."<br />\n";
            	//$channels[""] = null;
                // Add menu (unless it is excluded)
                $path = ($prefix) ? $prefix . $separator . $channel->title : $channel->title;
                if ($channel->id != $excludeId)
                {
                    $channels[$channel->id] = $path;
					$GLOBALS['channelHierarchy'][$channel->id]['id'] = $channel->title;
					$GLOBALS['channelHierarchy'][$channel->id]['workflowState'] = $channel->workflowState;
                }

                // Recursively add sub-menus
                channel::getChannelHierarchyConst($channels, $excludeId, $path, $channel->id, $separator);
            }
        }
        return $channels;
    }
	
	static function getChannelHierarchyConst2(array &$channels = null, $excludeId=0, $prefix=null, $parentId=0, $separator = ' :: ')
    {
        if (!$channels) $channels = array();
        $project = wcmProject::getInstance();
        $session = wcmSession::getInstance();

        // load channels of site from cache
        $channelList = wcmCache::fetch('channel_of_site' . $session->getSiteId());
		
        if ($channelList === FALSE)
        {
            $enum = new channel($project);
            if (!$enum->beginEnum('siteId = ' . $session->getSiteId() , 'parentId, rank')) return null;
            $channelList = array();
            while ($enum->nextEnum())
            {
                $channelList[$enum->id] = clone($enum);
            }
            $enum->endEnum();
            wcmCache::store('channel_of_site' . $session->getSiteId(), $channelList);
        }

        foreach($channelList as $channel)
        {
        	//echo $channel->title."<br />\n";
            if ($channel->parentId == $parentId)
            {	//echo $channel->title."<br />\n";
            	$channels[""] = null;
                // Add menu (unless it is excluded)
                $path = ($prefix) ? $prefix . $separator . $channel->title : $channel->title;
                if ($channel->id != $excludeId)
                {
                    $channels[$channel->id] = $channel->title;
                }

                // Recursively add sub-menus
                channel::getChannelHierarchyConst($channels, $excludeId, $path, $channel->id, $separator);
            }
        }
        return $channels;
    }
		
	static function getSubChannelsIds($firstLevelLimit = false)
	{
		$rubrics = array('_RLX_WELLBEING', '_RLX_HOUSEHOME', '_RLX_ENTERTAINMENT', '_RLX_TOURISM');
		
		$hierarchy = channel::getChannelHierarchyConst();
		$idsAllowed = array();
		
		$i = 0;
		foreach ($rubrics as $rubric)
		{
			foreach ($hierarchy as $id => $item)
			{
				$nameOfRootChannel = '_RLX_RUBRICS';
				$nameOfSubChannel = $rubric;

				if (preg_match('`^'.$nameOfRootChannel.' :: '.$nameOfSubChannel.' :: (.)+$`i', $item))
				{
					if($firstLevelLimit)
					{
						if (preg_match('`^'.$nameOfRootChannel.' :: '.$nameOfSubChannel.' :: ([A-Z0-9_ ])+$`i', $item))
						{
							$channelObject = new channel(null, $id);
							$idsAllowed[$i]['rubric'] = $rubric;
							$idsAllowed[$i]['ids'][] = $id;
							$idsAllowed[$i]['title'][] = $channelObject->title;
							unset($channelObject);
						}
					}
					else
					{
						$channelObject = new channel(null, $id);
						$idsAllowed[$i]['rubric'] = $rubric;
						$idsAllowed[$i]['ids'][] = $id;
						$idsAllowed[$i]['title'][] = $channelObject->title;
						unset($channelObject);
					}
				}
			}
			$i++;
		}
		
		return $idsAllowed;
	}
	
	
	
	
	static function getRootChannel($subChannelId)
	{
		$allChannelsIds = channel::getSubChannelsIds();

		foreach ($allChannelsIds as $channelId)
		{
			if (in_array($subChannelId, $channelId['ids']))
			{
				return $channelId['rubric'];
			}
		}
	}
	
	
	
	static function getRootChannelFromSameLevel($level)
	{
		$allChannelsIds = channel::getSubChannelsIds();

		foreach ($allChannelsIds as $channelId)
		{
			if (in_array($subChannelId, $channelId['ids']))
			{
				return $channelId['rubric'];
			}
		}
	}
	

	
	
	/**
     * Get Channels associated to a specified siteId. Returns only equivalence to the current siteId
     * 
     * @param $siteId		(int)		Site ID to browse equivalence
     */
     
	static function getChannelsLanguageEquivalence($siteId)
	{
		$config = wcmSession::getInstance();
		$originalSiteId = $config->getSiteId();
		
		// Get Channels from original & duplicate Site
		$channelsOriginal = channel::getChannelHierarchyConst();
		$config->setSiteId($siteId);
		$channelsDuplicate = channel::getChannelHierarchyConst();
		$config->setSiteId($originalSiteId);
		
		$channelsOriginalFiltred = array();
		$channelsDuplicateFiltred = array();
		$channelsFilreds = array();
		
		foreach ($channelsOriginal as $channelId => $channelText)
		{
			if (preg_match('`(.+) :: ([A-Z0-9_]+)$`i', $channelText, $captured))
			{
				$channelsOriginalFiltred[$captured[2]] = $channelId;
			}
		}
		
		foreach ($channelsDuplicate as $channelId => $channelText)
		{
			if (preg_match('`(.+) :: ([A-Z0-9_]+)$`i', $channelText, $captured))
			{
				$channelsDuplicateFiltred[$captured[2]] = $channelId;
			}
		}
		
		// Set the final array containing translated Channels Text
		foreach($channelsOriginalFiltred as $channelText => $channelId)
		{
			if (isset($channelsDuplicateFiltred[$channelText]))
			{
				$channelsFilreds[$channelId] = $channelsDuplicateFiltred[$channelText];
			}
		}
		
		$config->ping();
		return $channelsFilreds;
	}
	
	
	/**
     * Return all RUBRICS
     * 
     * @param $siteId		(int)		Site ID to browse equivalence
     */
	
	static function getAllRubrics()
	{
		$config = wcmSession::getInstance();
		$allChannels = channel::getChannelHierarchyConst();
		$allRubrics = array();
		
		foreach ($allChannels as $channelId => $channelText)
		{
			if (preg_match('`_RLX_RUBRICS :: (.+)$`i', $channelText, $captured))
			{
				$newO = new channel();
				$newO->refresh($channelId);
				$allRubrics[] = array('id' => $newO->id, 'label' => $newO->title, 'parentId' => $newO->parentId, 'parentLabel' => $newO->getParentChannel()->title);
				unset($newO);
			}
		}
		
		return $allRubrics;
	}
/* 
	static function getAllRubrics()
	{
		$config = wcmSession::getInstance();
		$allChannels = channel::getChannelHierarchyConst();
		$allRubrics = array();
		
		foreach ($allChannels as $channelId => $channelText)
		{
			///if (preg_match('`_RLX_RUBRICS :: (.+)$`i', $channelText, $captured))
			//{
				$channel = new channel();
				$channel->refresh($channelId);
				$pchannel = new channel();
				$pchannel->refresh($channel->parentId);
				$allRubrics[] = array(
				'id' => $channel->id, 
				'label' => $channel->title, 
				'parentId' => $channel->parentId, 
				'parentLabel' => $pchannel->title);
				unset($channel);
			//}
		}
		
		return $allRubrics;
	}
*/	
	
    /**
     * get all channel parents
     *
     * @param (int)  $channelId  : the initial channel Id
     *
     */
	static function getArrayParents($channelId)
    {
    	$project = wcmProject::getInstance();     
        $parents = array();
        $chan = new channel($project, $channelId);

        $deep = -1;
        while ($chan->parentId)
        {
            $chan->refresh($chan->parentId);
            
            if ($chan->id)
            {
                $parents[$deep] = $chan->id;
            }
            $deep--;           
        }
        unset($chan);

        return $parents;
    }
	
    /**
     * get all channel childs
     *
     * @param (int)  	$parentId   : the initial channel Id
     * @param (int)  	$siteId  	: filter by site id
     * @param (int)  	$deep  		: property used by fonction for deep informations
     *
     */	
	static function getArrayChannelChilds($parentId = 0, $siteId = null, $depthLimit = null, $depthIndex = null)
    {
    	$database = wcmProject::getInstance()->bizlogic->getBizClassByClassName('channel')->getConnector()->getBusinessDatabase();
        $tableName = '#__channel';
		if (!isset($depthIndex)) $depthIndex = 0;
        if (!isset($nodes)) $nodes = array();
        
        $sql = "";
        if (empty($parentId))
        	$sql  .= "SELECT * FROM ".$tableName." WHERE parentId IS NULL";
		else
			$sql  .= "SELECT * FROM ".$tableName." WHERE parentId=".$parentId;
		
		if (isset($siteId) && !empty($siteId))
			$sql  .= " AND siteId=".$siteId;
		
		//if (isset($orderBy) && !empty($orderBy))
		//	$sql  .= " ORDER BY ".$orderBy;
		$sql  .= " ORDER BY parentId, rank";
		
		$rs = $database->executeQuery($sql);
		if ($rs != null)
		{
			$depthIndex++;
			if (!empty($depthLimit) && $depthIndex == ($depthLimit+1))
				return $nodes;
				
		    while ($rs->next())
		    {
		    	$getChilds = channel::getArrayChannelChilds($rs->get('id'), $siteId, $depthLimit, $depthIndex);
		    	if (!empty($getChilds)) $nodes[$rs->get('id')]["childs"] = $getChilds;
		    	$nodes[$rs->get('id')]["title"] = getConst($rs->get('title'));
		    	$nodes[$rs->get('id')]["parentId"] = $rs->get('parentId');
		    	$nodes[$rs->get('id')]["workflowState"] = $rs->get('workflowState');	    	
		    	$nodes[$rs->get('id')]["depth"] = $depthIndex;	
		    	$nodes[$rs->get('id')]["id"] = $rs->get('id');	        	
		    }
		}
		return $nodes;
    }
    
	static function getArrayChannelFinalChilds($parentId = 0, $siteId = null, $depthLimit = null, $depthIndex = null)
    {
    	$database = wcmProject::getInstance()->bizlogic->getBizClassByClassName('channel')->getConnector()->getBusinessDatabase();
        $tableName = '#__channel';
		if (!isset($depthIndex)) $depthIndex = 0;
        if (!isset($nodes)) $nodes = array();
        
        $sql = "";
        if (empty($parentId))
        	$sql  .= "SELECT * FROM ".$tableName." WHERE parentId IS NULL";
		else
			$sql  .= "SELECT * FROM ".$tableName." WHERE parentId=".$parentId;
		
		if (isset($siteId) && !empty($siteId))
			$sql  .= " AND siteId=".$siteId;
		
		//if (isset($orderBy) && !empty($orderBy))
		//	$sql  .= " ORDER BY ".$orderBy;
		$sql  .= " ORDER BY parentId, rank";
		
		$rs = $database->executeQuery($sql);
		if ($rs != null)
		{
			$depthIndex++;
			if (!empty($depthLimit) && $depthIndex == ($depthLimit+1))
				return $nodes;
				
		    while ($rs->next())
		    {
		    	$getChilds = channel::getArrayChannelFinalChilds($rs->get('id'), $siteId, $depthLimit, $depthIndex);
		    	if (!empty($getChilds)) $nodes[$rs->get('id')] = $getChilds;
		    	else
		    	{
			    	$nodes[$rs->get('id')]["title"] = getConst($rs->get('title'));
			    	$nodes[$rs->get('id')]["parentId"] = $rs->get('parentId');
			    	$nodes[$rs->get('id')]["workflowState"] = $rs->get('workflowState');	    	
			    	$nodes[$rs->get('id')]["depth"] = $depthIndex;	
			    	$nodes[$rs->get('id')]["id"] = $rs->get('id');	
			    	$nodes[$rs->get('id')]["tokens"] = $rs->get('tokens');	
			    	$nodes[$rs->get('id')]["css"] = $rs->get('css');	
		    	}       	
		    }
		}
		return $nodes;
    }
    /**
     * Generate html content to display channels
     *
     * @param (object)   $bizobject    : the current bizobject
     *
     */

    function getChannelsHtml($bizobject, $siteId = null)
    {
    	$session = wcmSession::getInstance();
    	//$channels = channel::getChannelHierarchyConst(); 

    	$setSiteId = $session->getSiteId();
    	
    	$channels = wcmCache::fetch('ArraySiteChannels-'.$setSiteId);
		if (empty($channels) || !is_array($channels))
		{
			wcmCache::store('ArraySiteChannels-'.$setSiteId, channel::getChannelHierarchyConst(), 3600);
			$channels = wcmCache::fetch('ArraySiteChannels-'.$setSiteId);
		}
		
		if (isset ($bizobject->channelIds)) 
		{ 
			if (is_array($bizobject->channelIds)) 
				$bizobject->channelIds = serialize($bizobject->channelIds);  

			$channelIds = unserialize($bizobject->channelIds); 
		}
        else 
        	$channelIds = array (); 
        
        $rootChannels = array(0 => NULL);  	
        
        $html = '<table class="channels_choice" cellpadding="0" cellspacing="0"><tr>';
       
        /* Get IPTC Tags */
        //$t_iptcTagsOriginals = getIptcTags();
        
        	foreach ($channels as $id=>$path)
            {
                $parts = explode(' :: ', $path);
                $decalage = sizeof($parts)-1;               
                
                $rank_letter = strtolower(chr(64+min(5, count($parts))));
                $rank_letter_prev = $rank_letter;
                $currentLabelConst = array_pop($parts);
                //echo $currentLabelConst;
                
                if ($rank_letter == 'a')
                    $html .= '</td><td class="channel_choice_block">';
                else
                	$html .= '<span class="channel_choice_item level_c">';
               
                $currentChannelIdFound = false;
                	
                // on récupère les rubriques parentes et on prépare le javascript pour cocher les checbox correspondantes
                	$parentChannel = channel::getArrayParents($id);
                	$js = "";
                	if (!empty($parentChannel))
                	{
                		$jspiptc = "";
                		foreach($parentChannel as $key=>$channel)
                		{
                			// on détermine la nouvelle valeur de la rank_letter suivant la profondeur des parent channels
                			$channelIptc = new channel();
                			$channelIptc->refresh($channel);
                			$tabchannelIptc = $channelIptc->getChannelIPTC();
                			$countIptc = sizeof($tabchannelIptc);
                			
                			if (!empty($tabchannelIptc))
                				$jspiptc = '_addIptcTags(\''.$channel.'\', \''.$countIptc.'\', $(this));';
                			
                			$letter = ord($rank_letter) + $key;
                			$charLetter = chr($letter);
                			$elementId = $channel."_".$charLetter;
                			$js .= "if ($(this).checked == true) {document.getElementById('".$elementId."').checked=1;document.getElementById('".$elementId."').setAttribute('rel', 'checked');}".$jspiptc;
                		}
                	}
            	
             		/* We insert IPTC tags here */
                    $i = 0;
                   
                    $channel = new channel();
                    $channel->refresh($id);
                    $iptc = $channel->getChannelIPTC();
                    
                    if (!empty($iptc))
                    {
                    	foreach ($iptc as $key=>$val)
                    	{
                    		echo '<input type="hidden" name="relatedIptcTags_'.$id.'_'.$i.'" id="relatedIptcTags_'.$id.'_'.$i.'" value="'.$val.':'.$key.'" />';
                            echo '<ul class="tags"></ul>';
                            $i++;
                    	}
                    }
                    
                    $html .= '<input type="radio" name="channelId" id="radio_'.$id.'" value="'.$id.'"';
                    $html .= ($i != 0)?' onclick="_addIptcTags(\''.$id.'\', \''.$i.'\', $(this));document.getElementById(\''.$id.'_'.$rank_letter.'\').checked=1;document.getElementById(\''.$id.'_'.$rank_letter.'\').setAttribute(\'rel\', \'checked\');'.$js.'"':' onclick="document.getElementById(\''.$id.'_'.$rank_letter.'\').checked=1;document.getElementById(\''.$id.'_'.$rank_letter.'\').setAttribute(\'rel\', \'checked\');'.$js.'"';               
                    //$html .= ' onclick="document.getElementById(\''.$id.'_'.$rank_letter.'\').checked=1;'.$js.'"';               
                    $html .= ($id == $bizobject->channelId)?' checked />':' />';
                    $html .= '&nbsp;';
                                  
                    $html .= '<input style="margin-left:'.$decalage.'0px;" type="checkbox"';                
                    $html .= ($i != 0)?' onclick="_addIptcTags(\''.$id.'\', \''.$i.'\', $(this));'.$js.'"':' onclick="'.$js.'"';               
                    $html .= ' name="channelIds[]" id="'.$id.'_'.$rank_letter.'" value="'.$id.'"';
                    
                    foreach ($channelIds as $currentChannelId)
                    {
                        if ($currentChannelId == $id && !$currentChannelIdFound)
                        {
                            $html .= ' rel="checked" checked />';
                            $currentChannelIdFound = true;
                            break;
                        }
                    }
                    if (!$currentChannelIdFound)
                        $html .= ' rel="none" />';
                        
                   	if ($rank_letter != 'a')
                    	$html .= '<label for="'.$id.'_'.$rank_letter.'">'.getConst($currentLabelConst)."</label></span>";
                	else
                		$html .= '<label for="'.$id.'_'.$rank_letter.'"><b>'.getConst($currentLabelConst)."</b></label></span>";
                	
           }

            $html .= '</tr></table>';
        return $html;
    }
    
     /**
     * Generate html content to display channels
     * revision of getLinearChannelsHtml for single channels
     * @param (object)   $bizobject    : the current bizobject
     *
     */
    
    function getLinearChannelsHtml($bizobject, $siteId = null)
    {
    	$session = wcmSession::getInstance();
    	//$channels = channel::getChannelHierarchyConst(); 

    	$setSiteId = $session->getSiteId();
    	
    	$channels = wcmCache::fetch('ArraySiteChannels-'.$setSiteId);
		if (empty($channels) || !is_array($channels))
		{
			wcmCache::store('ArraySiteChannels-'.$setSiteId, channel::getChannelHierarchyConst(), 3600);
			$channels = wcmCache::fetch('ArraySiteChannels-'.$setSiteId);
		}
		
		if (isset ($bizobject->channelIds)) 
		{ 
			if (is_array($bizobject->channelIds)) 
				$bizobject->channelIds = serialize($bizobject->channelIds);  

			$channelIds = unserialize($bizobject->channelIds); 
		}
        else 
        	$channelIds = array (); 
        
        $rootChannels = array(0 => NULL);  	
        
        $html = '<table class="channels_choice" cellpadding="0" cellspacing="0"><tr><td class="channel_choice_block">';
       
        /* Get IPTC Tags */
        //$t_iptcTagsOriginals = getIptcTags();
        
        	foreach ($channels as $id=>$path)
            {
                $parts = explode(' :: ', $path);
                $decalage = sizeof($parts)-1;               
                
                $rank_letter = strtolower(chr(64+min(5, count($parts))));
                $rank_letter_prev = $rank_letter;
                $currentLabelConst = array_pop($parts);
                //echo $currentLabelConst;
                
                /*
                if ($rank_letter == 'a')
                    $html .= '</td><td class="channel_choice_block">';
                else
                	$html .= '<span class="channel_choice_item level_c">';
               */
                $currentChannelIdFound = false;
                	
                // on récupère les rubriques parentes et on prépare le javascript pour cocher les checbox correspondantes
                	$parentChannel = channel::getArrayParents($id);
                	$js = "";
                	if (!empty($parentChannel))
                	{
                		$jspiptc = "";
                		foreach($parentChannel as $key=>$channel)
                		{
                			// on détermine la nouvelle valeur de la rank_letter suivant la profondeur des parent channels
                			$channelIptc = new channel();
                			$channelIptc->refresh($channel);
                			$tabchannelIptc = $channelIptc->getChannelIPTC();
                			$countIptc = sizeof($tabchannelIptc);
                			
                			if (!empty($tabchannelIptc))
                				$jspiptc = '_addIptcTags(\''.$channel.'\', \''.$countIptc.'\', $(this));';
                			
                			$letter = ord($rank_letter) + $key;
                			$charLetter = chr($letter);
                			$elementId = $channel."_".$charLetter;
                			$js .= "if ($(this).checked == true) {document.getElementById('".$elementId."').checked=1;document.getElementById('".$elementId."').setAttribute('rel', 'checked');}".$jspiptc;
                		}
                	}
            	
             		/* We insert IPTC tags here */
                    $i = 0;
                   
                    $channel = new channel();
                    $channel->refresh($id);
                    $iptc = $channel->getChannelIPTC();
                    
                    if (!empty($iptc))
                    {
                    	foreach ($iptc as $key=>$val)
                    	{
                    		echo '<input type="hidden" name="relatedIptcTags_'.$id.'_'.$i.'" id="relatedIptcTags_'.$id.'_'.$i.'" value="'.$val.':'.$key.'" />';
                            echo '<ul class="tags"></ul>';
                            $i++;
                    	}
                    }
                    
                    $html .= '<input type="radio" name="channelId" id="radio_'.$id.'" value="'.$id.'"';
                    $html .= ($i != 0)?' onclick="_addIptcTags(\''.$id.'\', \''.$i.'\', $(this));document.getElementById(\''.$id.'_'.$rank_letter.'\').checked=1;document.getElementById(\''.$id.'_'.$rank_letter.'\').setAttribute(\'rel\', \'checked\');'.$js.'"':' onclick="document.getElementById(\''.$id.'_'.$rank_letter.'\').checked=1;document.getElementById(\''.$id.'_'.$rank_letter.'\').setAttribute(\'rel\', \'checked\');'.$js.'"';               
                    //$html .= ' onclick="document.getElementById(\''.$id.'_'.$rank_letter.'\').checked=1;'.$js.'"';               
                    $html .= ($id == $bizobject->channelId)?' checked />':' />';
                    $html .= '&nbsp;';
                                  
                    $html .= '<input style="margin-left:'.$decalage.'0px;" type="checkbox"';                
                    $html .= ($i != 0)?' onclick="_addIptcTags(\''.$id.'\', \''.$i.'\', $(this));'.$js.'"':' onclick="'.$js.'"';               
                    $html .= ' name="channelIds[]" id="'.$id.'_'.$rank_letter.'" value="'.$id.'"';
                    
                    foreach ($channelIds as $currentChannelId)
                    {
                        if ($currentChannelId == $id && !$currentChannelIdFound)
                        {
                            $html .= ' rel="checked" checked />';
                            $currentChannelIdFound = true;
                            break;
                        }
                    }
                    if (!$currentChannelIdFound)
                        $html .= ' rel="none" />';
                        
                   	if ($rank_letter != 'a')
                    	$html .= '<label for="'.$id.'_'.$rank_letter.'">'.getConst($currentLabelConst)."</label></span><br />";
                	else
                		$html .= '<label for="'.$id.'_'.$rank_letter.'"><b>'.getConst($currentLabelConst)."</b></label></span><br />";
                	
           }

            $html .= '</td></tr></table>';
        return $html;
    }
    
	static function getChannelHierarchyConstBySite($siteId = null, array &$channels = null, $excludeId=0, $prefix=null, $parentId=0, $separator = ' :: ')
    {
        if (!$channels) $channels = array();
        $project = wcmProject::getInstance();
        
        // load channels of site from cache
        $channelList = wcmCache::fetch('channel_of_site' . $siteId);
		
        if ($channelList === FALSE)
        {
            $enum = new channel($project);
            if (!$enum->beginEnum('siteId = '.$siteId.' AND workflowState="published"' , 'rank, parentId')) return null;
            $channelList = array();
            while ($enum->nextEnum())
            {
                $channelList[$enum->id] = clone($enum);
            }
            $enum->endEnum();
            wcmCache::store('channel_of_site' . $siteId, $channelList, 1800);
        }

        foreach($channelList as $channel)
        {
        	//echo $channel->title."<br />\n";
            if ($channel->parentId == $parentId)
            {	
            	$path = ($prefix) ? $prefix . $separator . $channel->title : $channel->title;
                if ($channel->id != $excludeId)
                {
                    $channels[$channel->id] = $path;
					$GLOBALS['channelHierarchy'][$channel->id]['id'] = $channel->title;
					$GLOBALS['channelHierarchy'][$channel->id]['workflowState'] = $channel->workflowState;
                }

                // Recursively add sub-menus
                channel::getChannelHierarchyConstBySite($siteId, $channels, $excludeId, $path, $channel->id, $separator);
            }
        }
        return $channels;
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
        if (!parent::checkValidity())
            return false;

        if (trim(' ' . $this->title) == '')
        {
            $this->lastErrorMsg = _BIZ_ERROR_TITLE_IS_MANDATORY;
            return false;
        }

        if (strlen($this->title) > 255)
        {
            $this->lastErrorMsg = _BIZ_ERROR_TITLE_TOO_LONG;
            return false;
        }

        if ($this->keywords && strlen($this->keywords) > 255)
        {
            $this->lastErrorMsg = _BIZ_ERROR_KEYWORDS_TOO_LONG;
            return false;
        }

        return true;
    }
	
    /**
     * Get all IPTC correspondance from XML file
     *
     * @return array[IPTC_ID][LANG][LABEL]
     *
     */
	static function getArrayAllChannelIPTC() 
	{
		$data = wcmCache::fetch('ArrayChannelIPTCStored');
		
		if (empty($data))
		{
			// language restriction
			$lang = array("fr", "en", "es", "de");
			$domXml = new DOMDocument;
		    $domXml->load(WCM_DIR.'/business/xml/taxonomies/afpscheme-subjects.xml');
		    $domXPath = new DOMXPath($domXml);
	    	
		    $xml = array(); 
		    $xml2 = array();
	 
		    foreach($domXml->getElementsByTagName('Term') as $link) 
		    {
		    	foreach ($link->getElementsByTagName('Label') as $link2) 
		    	{
		    		if ((string)$link2->getAttribute('scheme') == "Xafp" && in_array($link2->getAttribute('xml:lang'),$lang))
		    		 	$xml2[$link2->getAttribute('xml:lang')] = $link2->nodeValue;
		    	}
		    		 
		        $xml[$link->getAttribute('code')] = $xml2;
		    }
		    $data = wcmCache::store('ArrayChannelIPTCStored', $xml, -1);
		}
    		
    	return $data;
     }
    
    /**
     * Get current channel IPTC 
     *
     * @return array[IPTC_ID][LABEL]
     *
     */
    public function getChannelIPTC()
    {
    	$data = array();
    	if (!empty($this->iptc))
    	{
    		if (!is_array($this->iptc))
    			return unserialize($this->iptc);
    		else 
    			return $this->iptc;
    	}
    	else
    		return false;
    }
    
	public function save($source = null)
    { 	  	
		if (!parent::save($source)) { return false; }
		else { return $this->storeObjects(); }
	}
	
	public function getArrayAncestors()
    {
        $ancestors = array();

        $channel = $this;
        while ($channel->parentId)
        {
            $channel->refresh($channel->parentId);
            if ($channel->id)
                $ancestors[] = $channel->id;
        }
        unset($channel);

        return $ancestors;
    }
	
	/**
     * Refresh channel by its description
     *
     * @param string $description Channel's description
     */
    public function refreshByDescription($description)
    {
        return $this->refresh($this->database->executeScalar("SELECT id FROM $this->tableName WHERE description=?", array($description)));
    }
}
