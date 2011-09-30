<?php
/**
 * Project:     WCM
 * File:        wcm.sysobject.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * The System class is the parent abstract class
 * of all WCM system and business objects
 */
abstract class wcmSysobject extends wcmSecureObject
{
    /**
     * Date and time of creation
     */
    public $createdAt;

    /**
     * Identifier of creator
     */
    public $createdBy;

    /**
     * (int) revision number (auto-increment at each save() call)
     */
    public $revisionNumber;

    /**
     * Date and time of last modification
     */
    public $modifiedAt;

    /**
     * Identifier of modifier
     */
    public $modifiedBy;

    /**
     * (int) version number (auto-increment at each archive() call)
     */
    public $versionNumber;

    /**
     * (string) State of sysobject (in workflow)
     */
    public $workflowState = null;

    /**
     * (array) An assoc array of extended properties (name => value)
     */
    public $properties;

    /**
     * (wcmWorkflow) sysobject's workflow
     */
    protected $workflow;


    /**
     * (wcmSysclass) sysobject's master class
     */
    protected $masterClass;

    /**
     * Constructor
     *
     * Can be overloaded/supplemented by the child class
     *
     * @param wcmProject  $project    PnWProject object
     * @param int         $id         Optional id (used to refresh object)
     *
     */
    public function __construct($project = null, $id = 0)
    {
        // Initialize masterclass, connector, database and workflow
        $this->getMasterClass();

        // Set default values
        $this->setDefaultValues();

        // Refresh object?
        if ($id)
        {
            $this->refresh($id);
        }
    }

    /**
     * Set all initial values of an object
     * This method is invoked by the constructor
     */
    protected function setDefaultValues()
    {
        parent::setDefaultValues();

        $this->createdBy = $this->modifiedBy = 0;
        $this->createdAt = $this->modifiedAt = date('Y-m-d H:i:s');
        $this->revisionNumber = 0;
        $this->versionNumber = 0;
        $this->properties = array();

        if ($this->workflow)
            $this->workflowState = $this->workflow->initialState;
    }

    /**
     * Unserialize some properties if needed
     * This method is called by nextEnum() and refresh()
     */
    public function unserializeProperties()
    {
        $this->properties = ($this->properties) ? unserialize($this->properties) : array();
    }

    /**
     * Returns the business or system class which defines current sysobject
     *
     * @return mixed An instance or wcmBizclass or wcmSysclass
     */
    public function getMasterClass()
    {
        $project = wcmProject::getInstance();

        /* if (!isset($this->masterClass))
        {
            // Not having the sysclass/bizclass is a fatal error!
            if ($this instanceOf wcmBizobject)
            {
                $mc = $project->bizlogic->getBizclassByClassName($this->getClass());
                if (!$mc)
                    die('FATAL ERROR: WCM is not well configured: Cannot find bizclass for ' . $this->getClass());

                $connector = $mc->getConnector();
                if (!$connector)
                    die('FATAL ERROR: Invalid connector reference for bizclass ' . $this->getClass());

                $this->database = $connector->getBusinessDatabase();
            }
            else
            {
                $mc = $project->bizlogic->getSysclassByClassName($this->getClass());
                if (!$mc)
                    die('FATAL ERROR: WCM is not well configured: Cannot find sysclass for ' . $this->getClass());

                $this->database = $project->database;
            }
            $this->masterClass = $mc;

            // Retrieve connector table, lock mode and workflow
            $this->tableName = $mc->connectorTable;
            $this->isLockOptimistic = $mc->allowOptimisticLock;

            // A workflow cannot load dynamically a workflow to avoid recursion (endless loop)
            if (($this instanceOf wcmWorkflow) || (!$mc->workflowId))
            {
                $this->workflow = null;
            }
            else
            {
                $this->workflow = $project->workflowManager->getWorkflowById($mc->workflowId);
            }
        } */

	/* OPTIMISATION NSTEIN 29/06/2009 */
	if (!isset($this->masterClass))
        {
            $this->database = wcmMasterClassRegistry::getDatabase($this);
            $this->masterClass = wcmMasterClassRegistry::getMC($this);

            // Retrieve connector table, lock mode and workflow
            $this->tableName = $this->masterClass->connectorTable;
            $this->isLockOptimistic = $this->masterClass->allowOptimisticLock;
            if (($this instanceOf wcmWorkflow) || (!$this->masterClass->workflowId))
		$this->workflow = null;
	    else
		$this->workflow = $project->workflowManager->getWorkflowById($this->masterClass->workflowId);
        }

        return $this->masterClass;

        return $this->masterClass;
    }

    /**
     * Execute a transition of the workflow
     *
     * @param  wcmWorkflowTransition $transition the transition to execute
     *
     * @return bool TRUE on success, FALSE on failure (check lastErrorMsg)
     */
    public function executeTransition(wcmWorkflowTransition $transition)
    {
        if (!$this->workflow || !$transition)
            return false;

        // Check validity
        if (($transition->fromState != $this->workflowState) &&
            ($transition->fromState != wcmWorkflowTransition::ANY_STATE) &&
            ($transition->fromState != wcmWorkflowTransition::SELF_STATE))
        {
            $this->lastErrorMsg = 'Transition ' . getConst($transition->name) . ' cannot be executed on ' . $this->getClass() . ' with this state';
            return false;
        }

        // Check permission
        $session = wcmSession::getInstance();
        if (!$session->isAllowed($transition, wcmPermission::P_EXECUTE))
        {
            $this->lastErrorMsg = 'Transition ' . getConst($transition->name) . ' is not allowed to be executed on ' . $this->getClass() . ' for current user';
            return false;
        }

        // Load workflow script
        $script = $this->getWorkflowScript();

        // Execute pre-condition workflow
        if ($script)
        {
            // execute beforeXXX or else beforeTransition
            $funcName = 'before' . ucfirst($transition->code);
            if (!method_exists($script, $funcName))
            {
                wcmProject::getInstance()->logger->logVerbose('function ' . $funcName . ' not found for workflow; using beforeTransition as fallback');
                $funcName = 'beforeTransition';
            }
            if (!call_user_func(array($script, $funcName), $transition))
                return false;
        }

        // Change state (except for the magic SELF state)
        $previousState = $this->workflowState;
        if ($transition->toState != wcmWorkflowTransition::SELF_STATE)
            $this->workflowState = $transition->toState;

        // Execute post-processing workflow
        if ($script)
        {
            try
            {
                // execute onXXX or else onTransition
                $funcName = 'on' . ucfirst($transition->code);
                if (!method_exists($script, $funcName))
                {
                    wcmProject::getInstance()->logger->logVerbose('function ' . $funcName . ' not found for workflow; using onTransition as fallback');
                    $funcName = 'onTransition';
                }
                call_user_func(array($script, $funcName), $transition);
            }
            catch (Exception $e)
            {
                // restore previous state
                $this->workflowState = $previousState;

                $this->lastErrorMsg = 'executeTransition (' . $transition->name . ') failed: ' . $e->getMessage();
                wcmProject::getInstance()->logger->logWarning($this->lastErrorMsg);
                return false;
            }
        }

        // save without workflow call
        return $this->save(null, true);
    }

    /**
     * Returns an instance of wcmIWorkflowScript according to
     * current sysobject
     *
     * @return wcmIWorkflowScript An instance of wcmIWorkflowScript or null
     */
    public function getWorkflowScript()
    {
        // Check workflow and script
        if (!$this->workflow)
            return null;

        $filename = WCM_DIR . '/business/workflow/' . $this->workflow->script;
        if (file_exists($filename))
        {
            require_once($filename);
        }
        else
        {
            // Invalid workflow!
            $this->lastErrorMsg = 'WCM is not well configured: cannot find workflow script: ' . $filename;
            wcmProject::getInstance()->logger->logError($this->lastErrorMsg);
            die($this->lastErrorMsg);
        }
        
        //  Load script (assuming workflow script class name equals to filename, without extension)
        $className = substr($this->workflow->script, 0, strlen($this->workflow->script)-4);
        $script = new $className($this);
        if (!($script instanceOf wcmIWorkflowScript))
        {
            // Invalid workflow script!
            $this->lastErrorMsg = 'Invalid workflow script: "' . $filename . '" does not implement wcmIWorkflowScript';
            wcmProject::getInstance()->logger->logError($this->lastErrorMsg);
            die($this->lastErrorMsg);
        }

        return $script;
    }

    /**
     * Get this bizobject's associated workflow (through bizclass)
     *
     * @return  wcmWorkflow
     */
    public function getWorkflow()
    {
        return $this->workflow;
    }


    /**
     * Get this bizobject's associated workflow state
     *
     * @return  wcmWorkflowState
     */
    public function getWorkflowState()
    {
        if (!$this->workflow)
            return null;

        $project = wcmProject::getInstance();
        $workflowState = $project->workflowManager->getWorkflowStateByCode($this->workflowState);
        if (!$workflowState)
            $workflowState = $project->workflowManager->getWorkflowStateByCode($workflow->initialState);

        return $workflowState;
    }

    /**
     * Get all available and allowed transitions for this object
     *
     * @return  array Assoc array of wcmWorkflowTransition objects (key is transition code)
     */
    public function getAvailableTransitions()
    {
        $transitions = array();
        if ($this->workflow)
        {
            $session = wcmSession::getInstance();
            $project = wcmProject::getInstance();
            foreach($project->workflowManager->getTransitions() as $code => $transition)
            {
                if ($transition->workflowId == $this->workflow->id &&
                    ($transition->fromState == wcmWorkflowTransition::ANY_STATE ||
                     $transition->fromState == $this->workflowState))
                {
                    // check execute permission
                    if ($session->isAllowed($transition, wcmPermission::P_EXECUTE))
                    {
                        $transitions[$code] = $transition;
                    }
                }
            }
        }
        return $transitions;
    }

    /**
     * Index object in search engine
     *
     * @return boolean true on success, false on failure
     */
    public function index()
    {
        //@TODO: implements generic sys_search?
        return true;
    }

    /**
     * De-index object from search engine
     *
     * @return boolean true on success, false on failure
     */
    public function deindex()
    {
        //@TODO: implements generic sys_search?
        return true;
    }

    /**
     * Store current object into database
     *
     * @return boolean true on success.
     */
    protected function store()
    {
        // Retrieve userId from session
        $userId = wcmSession::getInstance()->userId;
        // Always update the modifiedBy and modifiedAt fields
        if ($userId) $this->modifiedBy = $userId;
        $this->modifiedAt = date('Y-m-d H:i:s');

        // An null (or zero) id implies a "create" operation
        if (!$this->id)
        {
            // Update the createdBy and createdAt fields
            if ($userId) $this->createdBy = $userId;
            $this->createdAt = date("Y-m-d H:i:s");
        }

        // Update or insert object in database
        $this->revisionNumber++;
        if (!$this->database->storeSysObject($this))
        {
            $this->revisionNumber--;
            $this->lastErrorMsg = $this->getClass($this)." ::store failed : " . $this->database->getErrorMsg();
            return false;
        }

        return true;
    }

    /**
     * Deletes current object from database
     *
     * @return boolean true on success.
     */
    public function delete()
    {
        // Clear last error message
        $this->lastErrorMsg = '';

        // load workflow
        $script = $this->getWorkflowScript();

        // Execute pre-condition workflow
        if ($script)
        {
            if (!call_user_func(array($script, 'beforeDelete')))
                return false;
        }

        if (!parent::delete())
            return false;
            

        // Execute post-condition workflow
        if ($script)
        {
            try
            {
                call_user_func(array($script, 'onDelete'));
            }
            catch (Exception $e)
            {
                wcmProject::getInstance()->logger->logWarning('onDelete failed: ' . $e->getMessage());
            }
        }

        return true;
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
        // Clear last error message
        $this->lastErrorMsg = '';

        // Check lock
        if (!$this->checkLock('save')) return false;

        // Bind, check and store
        if (!$this->bind($source))   return false;
        
        // Invoke workflow?
        if ($noWorkflow)
        {
            // Check validity and finally store
            if (!$this->checkValidity()) return false;
            if (!$this->store())         return false;
        }
        else
        {
            // load workflow
            $script = $this->getWorkflowScript();
            $action  = ($this->id) ? 'Update' : 'Create';

            // Execute pre-condition workflow
            if ($script)
            {
                if (!call_user_func(array($script, 'before' . $action)))
                    return false;
            }

            // Check validity and store
            if (!$this->checkValidity()) return false;
            if (!$this->store())         return false;

            // execute post-condition workflow
            if ($script)
            {
                try
                {
                    call_user_func(array($script, 'on' . $action));
                }
                catch (Exception $e)
                {
                    wcmProject::getInstance()->logger->logWarning('onSave failed: ' . $e->getMessage());
                }
            }
        }
        
        // As we cannot ensure transactional mode between store and index,
        // even if the indexation may fail the method will return true...
        $this->index();

        return true;
    }

    /**
     * Refresh and then attempt to lock current object
     *
     * @return bool TRUE on success, FALSE otherwise (the object can be locked or just deleted)
     */
    public function checkout()
    {
        // Clear last error message
        $this->lastErrorMsg = '';

        // Ignore new object
        if (!$this->id) return true;

        // Remember oldId in case this object has been deleted
        $oldId = $this->id;
        $this->refresh();

        // Check existence
        if (!$this->id)
        {
            $this->lastErrorMsg = $this->getClass() . '::checkout failed : object ' . $oldId . ' has been deleted';
            return false;
        }

        // Lock
        return $this->lock();
    }

    /**
     * Cancel previous checkout operation (remove lock)
     *
     * @return bool TRUE on success, FALSE otherwise (the object has not been locked by current user)
     */
    public function undoCheckout()
    {
        return $this->unlock();
    }

    /**
     * Check-in object in database (save and then unlock)
     *
     * @param array $source An assoc array for binding to class vars (or null)
     *
     * @return true on success, false otherwise
     */
    public function checkin($source = null)
    {
        if (!$this->save($source))
            return false;

        //unlock if we are in pessimistic mode
        if(!$this->isLockOptimistic())
            $this->unlock();

        return true;
    }

    /**
     * Explicit serialization of object
     */
    public function serialize()
    {
        return parent::serialize();
    }

    /**
     * Explicit unserialization of object
     *
     * @param string $serialize The serialized content
     */
    public function unserialize($serialized)
    {
        // Rebuild masterClass, database, workflow, ...
        $this->getMasterClass();

        // Unserialize content
        parent::unserialize($serialized);
    }

    /**
     * Check if current object has been updated (or deleted) since last memory load
     * Note: if object has been deleted, $this->id will be set to zero
     *
     * @return bool TRUE if object is obsolete (updated/deleted), FALSE otherwise
     */
    public function isObsolete()
    {
        // Retrieve last revision number from database
        $revisionNumber = $this->database->executeScalar('SELECT revisionNumber FROM ' . $this->tableName . ' WHERE id=?',array($this->id));

        // If result is null object has been deleted!
        if ($revisionNumber === null && get_class($this)!= "wcmTemplate" && get_class($this)!= "wcmTemplateCategory") $this->id = 0;

        return ($this->revisionNumber != $revisionNumber);
    }

    /**
     * Archive a version of current sysobject in
     * the version manager
     *
     * @param string $comment Comment associated to version
     *
     * @return true on success, false otherwise
     */
    public function archive($comment = null)
    {
        // Clear last error message
        $this->lastErrorMsg = '';

        $this->versionNumber++;
        if (!wcmVersionManager::getInstance()->archive($this, $comment))
        {
            $this->versionNumber--;
            $this->lastErrorMsg = wcmVersionManager::getInstance()->getErrorMsg();
            return false;
        }
        else
        {
            // update version number in database
            $sql  = 'UPDATE ' . $this->tableName . ' SET versionNumber=' . $this->versionNumber;
            $sql .= ' WHERE id=' . $this->id;
            $this->database->executeStatement($sql);
        }

        // Free some memory
        $this->serialStorage = array();

        return true;
    }

    /**
     * Rollback to a previous stored version of current sysobject
     *
     * @param int $versionId Internal unique ID representing version to restore
     *
     * @return true on success, false otherwise
     */
    public function rollback($versionId = 0)
    {
        // Clear last error message
        $this->lastErrorMsg = '';

        return $this->restore($versionId, true);
    }

    /**
     * Restore a previous stored version of current sysobject
     *
     * @param int $versionId Internal unique ID representing version to restore
     * @param bool $rollBack True to rollback, i.e. remove in-between versions (false by default)
     *
     * @return true on success, false otherwise
     */
    public function restore($versionId = 0, $rollBack = false)
    {
        // Clear last error message
        $this->lastErrorMsg = '';

        // Check lock
        if (!$this->checkLock('restore'))
            return false;

        // When you RESTORE an object, the revisionNumber must increase
        $oldRevisionNumber = $this->revisionNumber;

        // Restore object
        $version = wcmVersionManager::getInstance()->restore($this->getClass(), $this->id, $versionId, $rollBack);
        if (!$version)
            return false;

        // When you RESTORE an object, the revisionNumber must increase!
        if (!$rollBack)
            $version->revisionNumber = $oldRevisionNumber;
        
        // The version has been retrieve, store it in the database and update current object
        // IMPORTANT: do not execute workflow on restore/rollback and adjust revision number
        $version->revisionNumber--;
        if (!$version->save(null, true))
            return false;

        // Free some memory
        $this->serialStorage = array();

        // Update current object
        $this->refresh($version->id);

        return true;
    }

    /**
     * Expose 'fulltext' to getAssocArray by computing a default full-text
     * string used for indexing
     *
     * Remark: this method is exposed by getAssocArray and may be
     * overloaded by inherited classes
     *
     * @param bool $toXML TRUE if method is called in the context of toXML()
     *
     * @return string A full-text representation of the object
     */
    public function getAssoc_fulltext($toXML = false)
    {
        // => Retrieve all string keys
        // => Remove all tags (html, xml)
        // => Parse result text and discard stop words
        $fulltext = null;
        foreach(getPublicProperties($this) as $value)
        {
            if (is_string($value))
            {
                $fulltext .= " " . $value;
            }
        }

        $fulltext = getRawText($fulltext);

        // Parse text (no stop word, retrieve a single occurence for each word)
        return trim(implode(" ", parseText($fulltext, null, true)));
    }
    
    /**
     * Exposes 'state' to the getAssocArray
     *
     * @param bool $toXML TRUE if method is called in the context of toXML()
     *
     * @return array An assoc array of the current wcmWorkflowState (or null)
     */
    public function getAssoc_state($toXML = false)
    {
        // Dont compute for toXML()
        if ($toXML) return null;

        $state = $this->getWorkflowState();
        return ($state == null) ? null : $state->getAssocArray($toXML);
    }
    
    /**
     * Exposes 'transitions' to the getAssocArray
     *
     * @param bool $toXML TRUE if method is called in the context of toXML()
     *
     * @return array An assoc array of available transitions (key is transition name)
     */
    public function getAssoc_transitions($toXML = false)
    {
        // Dont compute for toXML()
        if ($toXML) return null;

        $transitions = array();
        foreach($this->getAvailableTransitions() as $code => $transition)
        {
            $transitions[$code] = $transition->getAssocArray($toXML);
        }
        
        return $transitions;
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
        if ($propKey == 'properties')
        {
            $xml = '<properties>';
            foreach($this->properties as $name => $value)
            {
                $xml .= '<' . $name . '>';
                $xml .= is_object($value) ? serialize($value) : wcmXML::xmlEncode($value);
                $xml .= '</' . $name. '>';
            }
            $xml .= '</properties>';
        }
        else
        {
            if ($propKey == 'createdAt' || $propKey == 'modifiedAt')
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
        if ($property == 'properties')
        {
            // Assume <properties> <{propName}>{value}</propName> ... </properties>
            $this->properties = array();
            foreach($node->childNodes as $child)
            {
                if ($child->nodeName == '#text') continue;
                $this->properties[$child->nodeName] = $child->nodeValue;
            }
        }
        elseif ($property == 'createdAt' || $property == 'modifiedAt')
        {
            $this->$property = dateFromISO8601($node->nodeValue);
        }
        else
        {
            parent::initPropertyFromXMLNode($property, $node);
        }
    }

    /**
     * Check if code is unique, but only when it's a new code
     *
     * @return boolean True when code exist, false otherwise
     */
    protected function checkUniqueCode()
    {
        $sql = "SELECT COUNT(*) FROM " . $this->getTableName() . " WHERE code='".$this->code."' AND id !='" . $this->id . "'";
        if ($this->database->executeScalar($sql) > 0)
        {
            $this->lastErrorMsg = _ERROR_UNIQUE_CODE;
        }
        else
            return true;
    }
    
    /* OPTIMISATION NSTEIN 29/06/2009 */
    /**
     * Function used to serialize the current object
     *
     * @return array array of properties to serialize
     */
    public function __sleep()
    {
        $properties = array_keys(get_object_vars($this));
        $prop = array_diff($properties, array('database', 'masterClass', 'tableName', 'isLockOptimistic', 'workflow'));
        return $prop;
    }

    /* OPTIMISATION NSTEIN 29/06/2009 */
    /**
     * Function used to restore non serialized properties
     */
    public function __wakeup()
    {
        $this->getMasterClass();
    }
}
