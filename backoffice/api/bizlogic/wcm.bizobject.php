<?php
/**
 * Project:     WCM
 * File:        wcm.bizobject.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * The wcmBizobject class inherits from the {@link wcmSysobject} class
 * and represents business objects.
 */
abstract class wcmBizobject extends wcmSysobject
{
    /**
     * (bool) TRUE if bizobject must be generated
     * => This property is automatically set to true when checkin() is invoked
     */
    public $mustGenerate = false;

    /**
     * (array) An associative array of tags (name => category)
     */
    public $xmlTags;

    /**
     * (wcmSemanticData) Contains the semantic data
     */
    public $semanticData;

    //
    // Original (import) properties (source)
    //

    /**
     * (string) Source of the bizobject where it was imported from.
     */
    public $source;

    /**
     * (string) Id in the source for this bizobject.
     */
    public $sourceId;

    /**
     * (string) Version in the source for this bizobject.
     */
    public $sourceVersion;

    /**
     * (string) Bizobject's permalinks
     */
    public $permalinks;

    /**
     * Constructor
     *
     * @param wcmProject $project The current project
     * @param int $id An optional id to refresh the bizobject
     */
    public function __construct($project = null, $id = 0)
    {
        parent::__construct($project, $id);
    }

    /**
     * Set all initial values of an object
     * This method is invoked by the constructor
     */
    protected function setDefaultValues()
    {
        parent::setDefaultValues();

        $this->mustGenerate = false;
        $this->source = null;
        $this->sourceId = null;
        $this->sourceVersion = null;
        $this->xmlTags = array();
        $this->semanticData = new wcmSemanticData();

        if ($this->workflow)
            $this->workflowState = $this->workflow->initialState;
    }

    /**
     * Unserialize some properties if needed
     * This method is called by nextEnum() and refresh()
     */
    public function unserializeProperties()
    {
        parent::unserializeProperties();

        $this->xmlTags = ($this->xmlTags) ? unserialize($this->xmlTags) : array();

        if (is_string($this->semanticData))
            $this->semanticData = unserialize($this->semanticData);
        elseif (!$this->semanticData)
            $this->semanticData = new wcmSemanticData();
    }

    /**
     * Gets an array of bizobjects matching a specific where clause.
     *
     * @param string     $className The name of the bizobject class to instanciate
     * @param wcmProject $project   The current project
     * @param string     $where     An optional where clause to apply on groups (default: null)
     * @param string     $orderBy   An optional order clause (default: null)
     *
     * @return array An associative array of bizobjects (keys are ids)
     */
    public static function getBizobjects($className, $where = null, $orderBy = null)
    {
        $enum = new $className();
        if (!$enum->beginEnum($where, $orderBy))
            return null;
        $objects = array();
        while ($enum->nextEnum())
        {
            $objects[$enum->id] = clone($enum);
        }
        $enum->endEnum();
        return $objects;
    }

    /**
     * Returns the number of expired objects
     *
     * @return int Number of expired object or null on error
     */
    public function getExpiredCount()
    {
        if (!property_exists($this, 'expirationDate'))
            return 0;

        return $this->getCount('expirationDate < ?', array(date('Y-m-d H:i:s')));

    }


    /**
     * Save and index object (bind, checkValidity, store and index)
     *
     * @param array $source An assoc array for binding to class vars (or null)
     * @param boolean $noWorkflow Internal use only (used to bypass lock and workflow)
     *
     * @return true on success, false otherwise
     */
    public function save($source = null, $noWorkflow = false)
    {
        // set mustGenerate to true
        $this->mustGenerate = true;

        return parent::save($source, $noWorkflow);
    }

    /**
     * Index object in search engine
     *
     * @return boolean true on success, false on failure
     */
    public function index()
    {
        return wcmBizsearch::getInstance()->indexBizobject($this);
    }

    /**
     * De-index object from search engine
     *
     * @return boolean true on success, false on failure
     */
    public function deindex()
    {
        return wcmBizsearch::getInstance()->deindexBizobject($this);
    }

    /**
     * Deletes the object from database.
     *
     * @return boolean True on success, false on failure
     *
     */
    public function delete()
    {
        // Remove biz relations
        @wcmBizrelation::removeBizobject($this);
        
        if ($this->getComments())
        {
            foreach ($this->getComments() as $comment)
            {
                $comment->delete();
            }
        }

        // Delete bizobject
        return parent::delete();
    }

    /**
     * Explicit serialization of object
     */
    public function serialize()
    {
        // Serialize all relations in the serial storage!
        $this->serialStorage['relations'] = wcmBizrelation::archiveBizobject($this);
        
        return parent::serialize();
    }

    /**
     * Restore dependencies
     * This method is called by restore/archive methods and is used to restore in
     * the database all dependencies (e.g. relations, sub-parts, etc...)
     */
    protected function restoreDependencies()
    {
        // restore relations
        wcmBizrelation::restoreBizobject($this, getArrayParameter($this->serialStorage, 'relations'));

        parent::restoreDependencies();

    }

    /**
     * Updates the 'mustGenerate' property in the business DB
     * Warning: for performance considerations, the business search index will not be affected!
     *
     * @param bool $newValue The optional new value for the 'mustGenerate' property (default: false)
     *
     * @return bool TRUE on success, FALSE on failure
     */
    public function updateMustGenerate($newValue = false)
    {
        $sql = "UPDATE " . $this->tableName . " SET mustGenerate=? WHERE id=?";
        return $this->database->executeStatement($sql, array($newValue, $this->id));
    }

    /**
     * Preview bizobject
     * => Execute template 'preview/{className|'default'}.tpl with following parameters
     *          widgetMode  The widget mode (see params)
     *          bizobject   The bizobject
     *          context     The bizobject assoc array
     *
     * @param int $widgetMode Widget display mode (bit field, default is wcmWidget::VIEW_CONTENT)
     * @param string $templateId Optional template to use (default is bizobject->templateId)
     * @param wcmLogger $logger Optional logger (if null, the generator will create its own)
     *
     * @return string Result of template execution
     */
    public function preview($widgetMode = wcmWidget::VIEW_CONTENT, $templateId = null, $logger = null)
    {
        // Inject templateId if needed
        if (!$templateId)
        {
            $templateId = 'preview/' . $this->getClass() . '.tpl';
        }
        
        // Find template
        $config = wcmConfig::getInstance();
        if (!file_exists($config['wcm.templates.path'] . $templateId))
        {
            $templateId = 'preview/default.tpl';
        }

        // Execute template
        $generator = new wcmTemplateGenerator($logger, false, $widgetMode);
        return $generator->executeTemplate($templateId, array(
                                                            'widgetMode' => $widgetMode,
                                                            'obizobject' => $this,
                                                            'bizobject' => $this->getAssocArray(false)));
    }

    /**
     * Generates specific pages (according to object id).
     * => We search for content_generation with special naming convention
     *    starting either by 'autogen_always' or  'autogen_{classname}'
     *
     * @param boolean   $recursive       Try also to generate related bizobject (biz_relation) (default: true)
     * @param wcmLooger $logger          The logger to use (or null) (default: null)
     * @param boolean   $forceGeneration Force generation even if 'mustGenerate' property is false (default: false)
     * @param boolean   $firstCall       Whether this is the first call (default: true)
     * @param int       $$widgetMode     Widget mode (for ICE and design; default: wcmWidget::VIEW_CONTENT)
     *
     * @return boolean True on success, false on failure
     */
    public function generate($recursive=true, $logger = null, $forceGeneration = false, $firstCall = true, $widgetMode = wcmWidget::VIEW_CONTENT)
    {
        if (!$this->id)
            return false;

        //Mis en commentaire par stef pour forcer la génération de media vidéo, sans se faire *** 
        /*if (!$forceGeneration && !$this->mustGenerate)
        {
            if ($logger) $logger->logMessage(_GENERATION_NOTHING);
            return true;
        }*/

        $project = wcmProject::getInstance();
        $className = $this->getClass();
        $generator = new wcmTemplateGenerator($logger, false, $widgetMode);

        // Generate the "autogen_always%" and "autogen_{className}%" generation content on first call
        if ($firstCall)
        {
            foreach($project->generator->getGenerationContents() as $content)
            {
                if (strpos('autogen_always_', $content->name) === 0 || strpos('autogen_'.$className.'_', $content->name) === 0)
                {
                    // Extend context with current bizobject and set class and id
                    $generator->executeGenerationContent($content->id, array($className => $this->id,
                                                                             "className" => $className,
                                                                             "id" => $this->id));
                }
            }
        }

        // Set bizobject 'mustGenerate' to false
        if ($this->mustGenerate)
            $this->updateMustGenerate(false);

        // Generate linked elements
        if ($recursive)
        {
            $sql = "SELECT destinationId, destinationClass FROM biz__relation WHERE sourceClass='".$className."' AND sourceId=".$this->id;
            $rows = $this->database->executeQuery($sql);
            foreach ($rows as $row)
            {
                $linkedClass = $row["destinationClass"];
                $linked = new $linkedClass();
                if ($linked->refresh($row["destinationId"]))
                    $linked->generate(false, $logger, false);
            }
        }

        return true;
    }

    /**
     * Exposes 'language' in the getAssocArray
     *
     * @param bool $toXML TRUE if method is called in the context of toXML()
     *
     * @return string Language associated to bizobject
     */
    public function getAssoc_language($toXML = false)
    {
        return $this->getLanguage();
    }

    /**
     * Exposes 'site' in the getAssocArray
     *
     * @param bool $toXML TRUE if method is called in the context of toXML()
     *
     * @return array bizobject site's assocArray (or null)
     */
    public function getAssoc_site($toXML = false)
    {
        if ($toXML) return null;

        $site = $this->getSite();
        return ($site) ? $site->getAssocArray($toXML) : null;
    }

    /**
     * Exposes 'channel' in the getAssocArray
     *
     * @param bool $toXML TRUE if method is called in the context of toXML()
     *
     * @return array bizobject channel's assocArray (or null)
     */
    public function getAssoc_channel($toXML = false)
    {
        if ($toXML) return null;

        $channel = $this->getChannel();
        return ($channel) ? $channel->getAssocArray($toXML) : null;
    }

    /**
     * Exposes 'photos' to the getAssocArray
     *
     * @param bool $toXML TRUE if method is called in the context of toXML()
     *
     * @return An array of photos getAssocArray
     */
    public function getAssoc_photos($toXML = false)
    {
        $photos = array();
        foreach($this->getPhotos() as $photo)
        {
            $wcmPhoto = new photo();
            $wcmPhoto->refresh($photo['destinationId']);

            $photoAssoc = $wcmPhoto->getAssocArray($toXML);
            if ($toXML)
                $photoAssoc = $photoAssoc->toArray();

            $photos[] = $photoAssoc;
        }
        return $photos;
    }

    /**
     * Exposes 'publication' in the getAssocArray
     *
     * @param bool $toXML TRUE if method is called in the context of toXML()
     *
     * @return array bizobject publication's assocArray (or null)
     */
    public function getAssoc_publication($toXML = false)
    {
        if ($toXML) return null;

        $publication = $this->getPublication();
        return ($publication) ? $publication->getAssocArray($toXML) : null;
    }

    /**
     * Exposes 'issue' in the getAssocArray
     *
     * @param bool $toXML TRUE if method is called in the context of toXML()
     *
     * @return array bizobject issue's assocArray (or null)
     */
    public function getAssoc_issue($toXML = false)
    {
        if ($toXML) return null;

        $issue = $this->getIssue();
        return ($issue) ? $issue->getAssocArray($toXML) : null;
    }

    /*
     * Exposes 'comments' to the getAssocArray
     *
     * @param bool $toXML TRUE if method is called in the context of toXML()
     *
     * @return An array of comments getAssocArray
     */
    public function getAssoc_comments($toXML = false)
    {
        if ($toXML) return null;

        $return = array();
        $comments = $this->getComments();
        if ($comments)
        {
            foreach($comments as $comment)
            {
                $return[] = $comment->getAssocArray($toXML);
            }
        }
        return $return;
    }

    /**
     * Exposes 'related' to the getAssocArray (all object with IS_RELATED_TO relationship)
     *
     * @param bool $toXML TRUE if method is called in the context of toXML()
     *
     * @return An array of the related bizobject's getAssocArray
     */
    public function getAssoc_related($toXML = false)
    {
        return $this->getAssoc_relations($toXML, bizrelation::IS_RELATED_TO);
    }

    /**
     * Exposes 'partOf' to the getAssocArray (all object with IS_PART_OF relationship)
     *
     * @param bool $toXML TRUE if method is called in the context of toXML()
     *
     * @return An array of the related bizobject's getAssocArray
     */
    public function getAssoc_partOf($toXML = false)
    {
        return $this->getAssoc_relations($toXML, bizrelation::IS_PART_OF);
    }

    /**
     * Exposes 'composedOf' to the getAssocArray (all object with IS_COMPOSED_OF relationship)
     *
     * @param bool $toXML TRUE if method is called in the context of toXML()
     *
     * @return An array of the related bizobject's getAssocArray
     */
    public function getAssoc_composedOf($toXML = false)
    {
        return $this->getAssoc_relations($toXML, bizrelation::IS_COMPOSED_OF);
    }

    /**
     * Exposes 'relations' to the getAssocArray (all objects in relation with current bizobject)
     *
     * @param bool $toXML TRUE if method is called in the context of toXML()
     *
     * @return An array of the related bizobject's getAssocArray
     */
    public function getAssoc_relations($toXML = false, $relationKind = null)
    {
        if ($toXML) return null;

        $objects = array();
        foreach(wcmBizrelation::getBizobjectRelations($this, $relationKind, null, $toXML) as $object)
        {
            $obj = new $object['destinationClass']();
            $obj->refresh($object['destinationId']);
            $objects[] = $obj->getAssocArray($toXML);
        }
        return $objects;
    }
    
    /**
     * Returns all relations of the bizobject
     *
     * @return array An array of wcmBizRelation objects
     */
    public function getRelations()
    {
        $relations = array();
        $relation = new wcmBizrelation;

        $where = "sourceClass='" . $this->getClass() . "' AND sourceId=" . $this->id;
        $relation->beginEnum($where, "validityDate, rank");
        while ($relation->nextEnum())
        {
            $relations[] =  clone($relation);
        }
        $relation->endEnum();
        unset($relation);

        return $relations;
    }
    

    /**
     * Returns the comments of the bizobject
     *
     * @return array An array containing all comments at the root level
     */
    public function getComments()
    {

        $where = "referentId=" . $this->id . " AND referentClass='" . $this->getClass() . "' AND parentId IS NULL";
        return bizobject::getBizobjects('contribution', $where);
    }

    /**
     * Returns bizoboject's site
     *
     * @return site Bizobject's site or null
     */
    public function getSite()
    {
        // Is current object a site!? or else does it have a 'siteId' property
        if ($this instanceof site || !property_exists($this, 'siteId'))
            return null;

        $site = new site(null, $this->siteId);
        return ($site->id) ? $site : null;
    }

    /**
     * Returns bizobject's channel
     *
     * @return channel Bizobject's channel or null
     */
    public function getChannel()
    {
        // Is current object a channel?
        if ($this instanceof channel)
        {
            $channel = new channel(null, $this->parentId);
        }
        elseif(property_exists($this, 'channelId'))
        {
            $channel = new channel(null, $this->channelId);
        }
        else
        {
            return null;
        }

        return ($channel->id) ? $channel : null;
    }

    /**
     * Gets the photos related to the object.
     *
     * @return array An array of contribution objects
     */
    public function getPhotos()
    {
        return wcmBizrelation::getBizobjectRelations($this, bizrelation::IS_COMPOSED_OF, 'photo', false);
    }

    /**
     * Gets the issue related to the bizobject
     *
     * @return issue The issue object related to the bizobject
     */
    public function getIssue()
    {
        if ($this instanceOf issue)
            return null;

        if (property_exists($this, 'issueId'))
        {
            $issue = new issue(null, $this->issueId);
        }
        else
        {
            $channel = $this->getChannel();
            if (!$channel)
                return null;

            $issue = $channel->getIssue();
        }

        return ($issue && $issue->id) ? $issue : null;
    }

    /**
     * Gets the publication related to the bizobject
     *
     * @return publication The publication related to the bizobject
     */
    public function getPublication()
    {
        if ($this instanceOf publication)
            return null;

        if (property_exists($this, 'publicationId'))
        {
            $publication = new publication(null, $this->publicationId);
        }
        else
        {
            $issue = $this->getIssue();
            if (!$issue)
                return null;

            $publication = $issue->getPublication();
        }

        return ($publication && $publication->id) ? $publication : null;
    }

    /**
     * Gets the 'semantic' text that will be passed to the Text-Mining Engine.
     * IMPORTANT: Overwrite this method as it returns NULL by default!
     *
     * @return string The semantic text to mine (or null if none)
     */
    public function getSemanticText()
    {
        return null;
    }

    /**
     * Gets the language of the object.
     *
     * @return string The language of the object
     */
    public function getLanguage()
    {
        $config = wcmConfig::getInstance();

        $site = $this->getSite();
        if (!$site)
            return $config['wcm.default.language'];

        return ($site->language) ?  $site->language : $config['wcm.default.language'];
    }

    /**
     * Adds a user rating to the rank of the object.
     *
     * @param int $rateValue The user rating (usually from 1 to 5)
     */
    public function addRank($rateValue)
    {
        $this->ratingCount++;
        $this->ratingTotal += $rateValue;
        $this->ratingValue = (1.0 * $this->ratingTotal) / $this->ratingCount;
        $this->store();
    }

    /**
     * Adds a hit to the hit count of the object.
     */
    public function addHit()
    {
        // add new hit count
        $this->hitCount++;
        $this->store();
    }


    /**
     * Gets the contributions related to the object.
     *
     * @param int $parentId  Optional ID of the parent object (default is NULL)
     *
     * @return array An array of contribution objects
     */
    public function getContributions($parentId = null)
    {
        $where  = "referentId='".$this->id."'";
        $where .= " AND referentClass='".$this->getClass()."' AND parentId";
        $where .= ($parentId) ? ('='.$parentId) : ' IS NULL';

        return bizobject::getBizobjects("contribution", $where);
    }

    /**
     * Gets the number of contributions related to the object.
     *
     * @param int $parentId  Optional ID of the parent object (default is NULL)
     *
     * @return int The number of contributions related to the object
     */
    public function getContributionCount($parentId = null)
    {
        $sql  = 'SELECT COUNT(*) FROM #__contribution WHERE';
        $sql .= ' referentClass=? AND referentId=? AND parentId';
        $sql .= ($parentId === null) ? ' IS NULL' : '=?';

        $params = array($this->getClass(), $this->id, $parentId);

        return $this->database->executeScalar($sql, $params);
    }

    /**
     * Refresh current object from source and sourceId
     *
     * @return true on success, false either
     */
    public function refreshFromSource($source, $sourceId)
    {
        $sql  = "SELECT id FROM " . $this->tableName;
        $sql .= " WHERE source='".$source."' AND sourceId='".$sourceId."'";
        $rows = $this->database->executeQuery($sql);
        $id = 0;
        foreach ($rows as $row)
        {
            $id = $row["id"];
            break;
        }

        if (!$id)
            return false;
        else
            $this->refresh($id);
    }

    /**
     * Returns an XML representation of a property
     *
     * @param string $propKey    Property key
     * @param mixed  $propValue  Property value
     */
    protected function propertyToXML($propKey, $propValue)
    {
        $xml = null;

        // Treat special properties
        if ($propKey == 'xmlTags')
        {
            $xml = '<tag-categories>';
            if ($this->xmlTags)
            {
                foreach ($this->xmlTags as $category => $tags)
                {
                    $xml .= '<tags category="' . $category . '">';
                    if ($tags)
                    {
                        foreach ($tags as $tag)
                        {
                            $xml .= '<tag>' . wcmXML::xmlEncode($tag) . '</tag>';
                        }

                        $xml .= '</tags>';
                    }
                }
            }
            $xml .= '</tag-categories>';
        }
        elseif($propKey == 'semanticData' && $this->semanticData instanceof wcmSemanticData)
        {
            $xml = $this->semanticData->toXML();
        }
        else
        {
            if ($propKey == 'publicationDate')
            {
                $propValue = dateToISO8601($propValue);
            }
            $xml = parent::propertyToXML($propKey, $propValue);
        }

        return $xml;
    }

    /**
     * This function can be used to customize the initialisation of a specific property
     * from a XML node (invoked by initFromXML() method)
     *
     * @param string  $property  Property name to initialize
     * @param XMLNode $node      XML node used for initialization
     */
    protected function initPropertyFromXMLNode($property, $node)
    {
        if ($property == 'xmlTags')
        {
            // Assume <tags> <tag category="{category}">{tag}</tag> ... </tags>
            $this->xmlTags = array();
            foreach($node->childNodes as $child)
            {
                $this->xmlTags[$child->nodeValue] = $child->getAttribute('category');
            }
        }
        elseif ($property == 'semanticData')
        {
            $this->semanticData->fromXML($node);
        }
        elseif ($property == 'publicationDate')
        {
            $this->$property = dateFromISO8601($node->nodeValue);
        }
        else
        {
            parent::initPropertyFromXMLNode($property, $node);
        }
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
        if (!parent::checkValidity()) return false;
        
        if ($this->source  && strlen($this->source) > 255)
        {
            $this->lastErrorMsg = _BIZ_ERROR_SOURCE_TOO_LONG;
            return false;
        }

        if ($this->sourceId  && strlen($this->sourceId) > 255)
        {
            $this->lastErrorMsg = _BIZ_ERROR_SOURCE_ID_TOO_LONG;
            return false;
        }

        if ($this->sourceVersion  && strlen($this->sourceVersion) > 255)
        {
            $this->lastErrorMsg = _BIZ_ERROR_SOURCE_VERSION_TOO_LONG;
            return false;
        }
        
        if ($this->permalinks  && strlen($this->permalinks) > 255)
        {
            $this->lastErrorMsg = _BIZ_ERROR_PERMALINKS_TOO_LONG;
            return false;
        }
        
        if (property_exists($this, 'siteId') && $this->siteId == '')
        {
            $this->lastErrorMsg = _BIZ_ERROR_SITE_IS_MANDATORY;
            return false;
        }

        if (property_exists($this, 'publicationDate') && property_exists($this, 'expirationDate'))
        {
            if (($this->publicationDate != '' && $this->expirationDate != '') &&
                ($this->publicationDate > $this->expirationDate))
            {
                $this->lastErrorMsg = _BIZ_ERROR_EXP_PUB_DATE_FORMAT;
                return false;
            }
        }

        if (property_exists($this, 'publicationDate') && $this->publicationDate != '')
        {
        	$date = explode('-', $this->publicationDate);
            if (!checkdate($date[1], $date[2], $date[0]))
            {
                $this->lastErrorMsg =  _BIZ_ERROR_PUBLICATION_DATE_FORMAT;
                return false;
            }
        }        
        

        if (property_exists($this, 'expirationDate') && $this->expirationDate != '')
        {
            $date = explode('-', $this->expirationDate);
            if (!checkdate($date[1], $date[2], $date[0]))
            {
                $this->lastErrorMsg =  _BIZ_ERROR_EXPIRATION_DATE_FORMAT;
                return false;
            }
        }
        return true;
    }

    /**
     * Update all the chapters by the chapters given in the array
     *
     * @param Array $array  Array of new chapters
     */
    public function updateBizRelations($list, $newRelations)
    {

        $this->serialStorage['_list'][] = $list;
        $this->serialStorage[$list] = $newRelations;
    }

    /**
     * Update all the chapters by the chapters given in the array
     *
     * @param Array $array  Array of new chapters
     */
    public function updateZones($newZones)
    {

        $this->serialStorage['_designZones'] = $newZones;
    }
 	
	  /**
     * Update serial storage for a specific key
     *
     * @param string $key
     * @param Array $array  Array of new chapters
     */
    public function updateSerialStorage($key, $array)
    {
    	$this->serialStorage[$key] = serialize($array);
    }

	  /**
     * Updates the properties of type date in UTC, and relocation dates to reading.
     *  
     * @param string $mode value "read" or "write"
     */
    public function gmtShift($mode=null)
    {
        if(!$mode) return false;
        else
        {
          $session = wcmSession::getInstance();
          $userObj = new wcmUser;
          $userObj->refresh($session->userId);

          if($mode=='write')
            $gmtShift = $userObj->timezone * -1;  // value type : 1, -1...
          elseif($mode=='read')
            $gmtShift = $userObj->timezone;
            
          if(substr($gmtShift,0,1) != '-') $gmtShift = '+'.$gmtShift;
        }
        
        $properties = getPublicProperties($this);
        foreach ($properties as $property => $value)
        {
          if(is_string($value) && ($property != 'createdAt') && ($property != 'modifiedAt'))
          {
            if (preg_match("/^([0-9]{2,4})-([0-1][0-9])-([0-3][0-9]) (?:([0-2][0-9]):([0-5][0-9]))?/", trim($value)))
            { 
            /*
              wcmtrace('MODE GMT => '.$mode);
              wcmtrace('DATE from => '.$this->$property);
              wcmtrace('DATE op => '.$gmtShift." hours");
            */
              $date = new DateTime($this->$property);
              $date->modify($gmtShift." hours"); // format type : +1 hours, -3 hours
              $this->$property = $date->format("Y-m-d H:i:s");
            /*
              wcmtrace('DATE to => '.$this->$property);
            */
            }
          }
        }
    }
    
    /**
     * Loads or refreshes the object content.
     *
     * @param int $id An optional object ID; if not specifed the current ID is used (default: null)
     *
     * @return bizobject The refreshed object, or null on failure
     *
     */
    public function refresh($id = null)
    {
        if (!parent::refresh($id)) return null;

        //gestion GMT
        $this->gmtShift('read');
        
        $properties = getPublicProperties($this);
        return $this;
    }
    
    
    /**
     * Gets object ready to store by getting modified date, creation date etc
     * Will execute transition.
     */
    protected function store()
    {
        //gestion GMT
        $this->gmtShift('write');

        
        if(!parent::store()) return false;

        /**
         * Save new relations?
         *
         * Check is serialStorage contains specific entries
         * '_list' => an array of list to save (e.g _br_{kind} to save relation of kind {kind})
         * '_br_{kind}' => an array of array containing bizrelation properties
         */
        $newLists = getArrayParameter($this->serialStorage, '_list');
        if(is_array($newLists))
        {
            foreach($newLists as $listId)
            {
                $listInfos = explode('_', $listId);
                if(getArrayParameter($listInfos, 1)==='br' && $kind = getArrayParameter($listInfos,2))
                {
                    // remove previous relations
                    $relation = new wcmBizrelation();
                    $relation->sourceClass = $this->getClass();
                    $relation->sourceId = $this->id;
                    $relation->removeByKind($kind,false);
                    $relation->rank = 0;
                    $relation->kind = $kind;

                    // add new relations (picked-up from serialStorage)
                    $newRelations = getArrayParameter($this->serialStorage, $listId, array());
                    
                    foreach(getArrayParameter($newRelations, 'destinationId', array()) as $relationKey => $destId)
                    {
                        $relation->id = 0;
                        $relation->rank++;
                        
                        if($newRelations['destinationId'][$relationKey] != 0)
                        {
                            foreach($newRelations as $prop => $value)
                            {
								$relation->$prop = $newRelations[$prop][$relationKey];
                            }
                            $relation->save();
                        }
                    }
                }
            }
        }
		//print_r($newLists);
        unset($this->serialStorage['_list']);
        unset($this->serialStorage['_br_1']);
        unset($this->serialStorage['_br_2']);
        unset($this->serialStorage['_br_3']);


        /**
         * Check if there are design zones and save them if necessary (in the same fashion as bizRelations)
         */
        $newZones = getArrayParameter($this->serialStorage, '_designZones');
        $widgetsettings = getArrayParameter($this->serialStorage, '_widgetsettings', array());
        
        if($newZones)
        {
            $contents = array();
            parse_str($newZones, $contents);
            
            wcmProject::getInstance()->layout->setZoneContents($this, $contents, $newZones, unserialize($widgetsettings));
        }
        unset($this->serialStorage['_designZones']);
        
        return true;
    }

}
