<?php
/**
 * Project:     WCM
 * File:        wcm.object.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * The object class is the parent abstract class
 * of all WCM database objects
 */
abstract class wcmObject implements wcmEnumerable
{
    // Remember editable state in memory
    private $editable = -1;

    /**
     * Unique identifier
     */
    public $id;

    /**
     * (array) Memory storage used for serialization purpose
     */
    protected $serialStorage = array();

    /**
     * (wcmDatabase) The database corresponding to the sysclass or bizclass
     */
    protected $database;

    /**
     * (string) The table name where to store the object (or null for disconnected object)
     */
    protected $tableName;

    /**
     * (string) The last error message that occurs during execution of a method
     */
    protected $lastErrorMsg;


    //
    // Protected members used for enumeration (wcmEnumerable interface)
    //

    /**
     * (string) The query that correspond to the current enumeration (see beginEnum() method)
     */
    protected $enumQuery;

    /**
     * (string) The query that correspond to the computation of current enumeration count  (see enumCount() method)
     */
    protected $enumCountQuery;

    /**
     * (Creole::ResultSet) The resulset that correspond to the current enumeration (see beginEnum() method)
     */
    protected $enumResultSet;

    /**
     * (int) The total number of results corresponding to the current enumeration (see beginEnum() method)
     */
    protected $enumTotalCount;

    /**
     * (bool) Tell if this object is lock in "optimistic" mode (vs. "pessimistic")
     */
    protected $isLockOptimistic;

    /**
     * (wcmLock) last lock info computed by isLocked() or isEditable() or getLockInfo()
     */
    protected $lockInfo;

    /**
     * Constructor
     *
     * Can be overloaded/supplemented by the child class
     *
     * @param int $id Optional id (used to refresh object)
     *
     */
    public function __construct($id = null)
    {
        // Set default values
        $this->setDefaultValues();

        // Get database - boubou was there!
        $this->getDatabase();

        // Refresh object?
        if ($id) $this->refresh($id);
    }

    /**
     * Set all initial values of an object
     * This method is invoked by the constructor
     */
    protected function setDefaultValues()
    {
        $this->id = 0;
        $this->lastErrorMsg = null;
        $this->enumQuery = null;
        $this->enumResultSet = null;
        $this->enumTotalCount = -1;
        $this->serialStorage = array();
    }

    /**
     * Explicit serialization of object
     */
    public function serialize()
    {
        // We assume that serializable stuff has been added to our
        // magic array 'serialStorage'
        $serial = array();
        foreach(getPublicProperties($this) as $key => $val)
        {
            $serial[$key] = serialize($val);
        }
        $serial['tableName'] = serialize($this->tableName);
        $serial['serialStorage'] = serialize($this->serialStorage);

        return serialize($serial);
    }

    /**
     * Explicit unserialization of object
     */
    public function unserialize($serialized)
    {
        // Rebuild database and tableName
        $this->getDatabase();
        $this->setDefaultValues();


        // Unserialize content
        $serial = unserialize($serialized);
        foreach($serial as $key => $val)
        {
            $this->$key = unserialize($val);
        }
        unset($serial);
        
        // Restore dependencies
        $this->restoreDependencies();
    }
    
    /**
     * Unserialize some properties if needed
     * This method is called by nextEnum() and refresh()
     * and may be invoked after a bindArrayToObject() call
     */
    public function unserializeProperties()
    {
    }

    /**
     * Restore dependencies
     * This method is called by restore/archive methods and is used to restore in
     * the database all dependencies (e.g. relations, sub-parts, etc...)
     *
     */
    protected function restoreDependencies()
    {
    }

    /**
     * Get the database used to store/fetch object
     *
     * IMPORTANT: This method is called by the constructor
     * Therefore, you may need to override this method to
     * define your database and tableName the first time.
     *
     * @return wcmDatabase Database used to store/fetch object
     */
    protected function getDatabase()
    {
        if (!$this->database)
            die($this->getClass() . ' fatal error: you must define your database and tableName when extending class wcmObject');

        return $this->database;
    }

    /**
     * Returns unique instance of project
     *
     * @return wcmProject Instance of wcmProject
     */
    public function getProject()
    {
        return wcmProject::getInstance();
    }
    
    /**
     * Returns name of table in database where object is stored (or null if object is disconnected)
     *
     * @return string Name of table where object is stored (or null if object is disconnected)
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * Set the last error message of an object
     * @access private (for internal usage)
     *
     * @param string $message Error message to remember
     */
    public function setErrorMsg($message)
    {
        $this->lastErrorMsg = $message;
    }
    
    /**
     * Returns the last error message
     *
     */
    public function getErrorMsg()
    {
        return $this->lastErrorMsg;
    }

    /**
     * Returns the class name of the current object
     *
     * @return string Classname of current object
     */
    public function getClass()
    {
        return get_class($this);
    }

    /**
     * Expose 'className' as a key in the assoc array
     *
     * @param bool $toXML TRUE if getAssocArray is called by toXML()
     *
     * @return string The object class name
     */
    public function getAssoc_className($toXML = false)
    {
        return $this->getClass();
    }

    /**
     * Returns an associative array containing public properties and their values
     * and additional 'dynamic' properties computed from public methods named 'getAssoc_XXX()'
     *
     * @param bool $toXML TRUE if getAssocArray should returns methods used in the context of toXML()
     *                    By default, this parameter is set to false.
     *
     * @return wcmObjectAssocArray An instance of dynamic ArrayAcces/Iterator class
     */
    public function getAssocArray($toXML = false)
    {
        return new wcmObjectAssocArray($this, $toXML);
    }

    /**
     * Binds an assoc array to this object
     *
     * @param array  $assocArray (or null to ignore bindings)
     *
     * @return true on success, false otherwise
     */
    public function bind($assocArray = null)
    {
        // Clear last error message
        $this->lastErrorMsg = '';
        if (!$assocArray)
            return true;

        return bindArrayToObject($assocArray, $this);
    }

    /**
     * Load or refresh object content
     *
     * @param int $id optional argument, if not specifed current id is used
     *
     * @return object freshen object or null on failure
     *
     */
    public function refresh($id = null)
    {
        // Clear last error message
        $this->lastErrorMsg = '';

        // Refresh object itself?
        if ($id == null) $id = $this->id;

        // Ignore new object
        if (!$id) return $this;

        // Clear all "public" properties before refresh
        foreach(getPublicProperties($this) as $key => $value)
        {
            $this->$key = (is_bool($value) ? false : null);
        }
        
        // Restore default
        $this->setDefaultValues();

        // Load values from database and bind them to current object
        if (!$this->database->bindSysObject($this, $id))
        {
            $this->lastErrorMsg = $this->getClass() . ':: refresh failed : ' . $this->database->getErrorMsg();
            $this->id = 0;
        }

        $this->unserializeProperties();

        return $this;
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
        // Clear last error message
        $this->lastErrorMsg = '';

        return true;
    }

    /**
     * Index object in search engine
     *
     * @return boolean true on success, false on failure
     */
    public function index()
    {
        return true;
    }

    /**
     * De-index object from search engine
     *
     * @return boolean true on success, false on failure
     */
    public function deindex()
    {
        return true;
    }

    /**
     * Store current object into database
     *
     * @return true on success or an error message (string)
     */
    protected function store()
    {
        // Update or insert object in database
        if (!$this->database->storeSysObject($this))
        {
            $this->lastErrorMsg = $this->getClass($this)." ::store failed : " . $this->database->getErrorMsg();
            return false;
        }

        return true;
    }

    /**
     * Deletes current object from database
     *
     * @return true on success or an error message (string)
     */
    public function delete()
    {
        if (!$this->checkLock('delete')) return false;
        
        // Remove sysobject from database
        if (!$this->database->deleteSysObject($this))
        {
            $this->lastErrorMsg = $this->getClass() . "::delete failed : " . $this->database->getErrorMsg();
            return false;
        }
        
        // Remove permission from database?
        if ($this instanceOf wcmSecureObject)
        {
            $params = array($this->getPermissionTarget());
            $this->getProject()->database->executeStatement('delete from #__permission where target=?', $params);
        }

        // De-index
        $this->deindex();

        // Unlock
        $this->unlock();

        // Clear all "public" properties
        foreach(getPublicProperties($this) as $key => $value)
        {
            if ($key[0] != '_')
                $this->$key = null;
        }

        return true;
    }

    /**
     * Save and index object (bind, checkValidity, store and index)
     *
     * @param array $source An assoc array for binding to class vars (or null)
     *
     * @return true on success, false otherwise
     */
    public function save($source = null)
    {
        // Clear last error message
        $this->lastErrorMsg = '';

        // Check lock
        if (!$this->checkLock('save')) return false;

        // Bind, check and store
        if (!$this->bind($source))   return false;
        if (!$this->checkValidity()) return false;
        if (!$this->store())         return false;

        // As we cannot ensure transactional mode between store and index,
        // even if the indexation may fail the method will return true...
        $this->index();

        return true;
    }

    /**
    * Fill-in public properties of the sysobject from an XML fragment
    * as returned by toXML() method.
    *
    * @param string      $xml       XML fragment representing the object
    * @param string|null $xslFile   XSL xsl file (if defined, apply the XSL transformation)
    * @param string|null $className Object class name (if root tag is different than actual class name)
    *
    * @return boolean True on success, false otherwise
    */
    public function initFromXML($xml, $xslFile = null, $className = null)
    {
        // Create XML document
        $domXml = new DOMDocument();
        if (!$domXml->loadXML($xml))
        {
            throw new Exception(_BIZ_INVALID_XML);
        }

        return $this->initFromXMLDocument($domXml, $xslFile, $className);
    }

    /**
     * Fill-in public properties of the sysobject from a valid XML DOMDocument
     * If a XSL is defined, apply the XSL transformation
     *
     * @param DOMDocument $domXml    XML DOMDocument representing the object
     * @param string|null $xslFile   XSL xsl file (if defined, apply the XSL transformation)
     * @param string|null $className Object class name (if root tag is different than actual class name)
     *
     * @return boolean True on success, false otherwise
     */
    public function initFromXMLDocument($domXml, $xslFile = null, $className = null)
    {
        // If XSL, Transform XML document
        if ($xslFile)
        {
            // Create XSL document
            $xslDoc = new DOMDocument();
            $xslDoc->load($xslFile);

            // Create XSLT processor
            $xsltProc = new XSLTProcessor;
            $xsltProc->importStyleSheet($xslDoc);

            // Transform XML document
            $xml = $xsltProc->transformToXML($domXml);

            // Create XML document
            $domXml = new DOMDocument();
            if (!$domXml->loadXML($xml))
            {
                throw new Exception(_BIZ_INVALID_XML);
            }
        }

        if ($className === null)
            $className = $this->getClass();

        $domXPath = new DOMXPath($domXml);

        // Set object default values
        $this->setDefaultValues();

        // Expected format is <$className> <$propertyName> $propertyValue </$propertyName> ... </$className>
        foreach(getPublicProperties($this) as $property => $value)
        {
            $xpath = '/' .$className . '/' . $property;
            $node = wcmXML::getXPathFirstNode($domXPath, null, $xpath);
            if ($node)
            {
                // Use custom initialization for this property
                $this->initPropertyFromXMLNode($property, $node);
            }
        }

        return true;
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
        $value = wcmXML::getNodeValue($node);
        if (is_int($this->$property)) $value = intval($value);
        $this->$property = $value;
    }

    /**
     * Returns an XML representation of current object
     *
     * => the root tag name is equal to the class name
     * => the xml contains a tag for each properties found in getAssocArray(true) method
     * => when a property value is an array, a subtag <key id=""> is generated for each array entry
     *
     * @return string An XML fragment representing current object
     */
    public function toXML()
    {
        $className = $this->getClass();

        $xml = '<'. $className . '>'.PHP_EOL;
        foreach ($this->getAssocArray(true) as $k => $v)
        {
            $xml .= $this->propertyToXML($k, $v);
        }
        $xml .= '</'. $className . '>'.PHP_EOL;

        return $xml;
    }


    /**
     * Returns an XML representation of a property (taken from getAssocArray() method)
     *
     * @param string $propKey    Property key
     * @param mixed  $propValue  Property value
     */
    protected function propertyToXML($propKey, $propValue)
    {
        // create a tag for each property
        $xml = '<' . $propKey . '>';

        if(is_bool($propValue))
        {
        	$xml .= ($propValue) ? '1' : '0';
        }
        elseif(is_object($propValue))
        {
            $xml .= serialize($propValue);
        }
        else
        {
        	// This clause has been added by yul - relaxnews
		    if ($propKey == 'listIds' || $propKey == 'folderIds' || $propKey == 'channelIds')
		    {
		        if (!is_array($propValue))
		        {
	      		    $propValue = unserialize($propValue);
	   			}
		    }
			if($propKey == "schedules") {
		    	if(isset($propValue[0]["destinationId"])) {
			    	$objLocation = new location(null, $propValue[0]["destinationId"]);
			    	$city = $objLocation->city;
			    	$propValue[0]["city"] = $city;
		    	}
		    }
            $xml .= wcmXML::xmlEncode($propValue);
        }

        $xml .= '</' . $propKey . '>'.PHP_EOL;

        return $xml;
    }

    /**
     * Returns the number of object of current class matching a query
     *
     * @param string $where Optional where clause to match (default is  null)
     * @param array $params Optional parameters for the where clause (default is null)
     *
     * @return int Total number of object matching the clause or null on error
     */
    public function getCount($where = null, $params = null)
    {
        $sql = 'SELECT COUNT(*) FROM ' . $this->tableName;
        if ($where) $sql .= ' WHERE ' . $where;
        return $this->database->executeScalar($sql, $params);
    }
    
    /**
     * Computes the sql where clause matching foreign constraints
     * => This method must be overloaded by child class
     *
     * @param array $of Assoc Array with foreign constrains (key=className, value=id)
     *
     * @return string Sql where clause matching "of" constraints or null
     */
    protected function ofClause($of)
    {
        return null;
    }

    /**
     * Initialize and starts a new enumeration on current wcmSysobject
     *
     * @param string $where     Optional where clause
     * @param string $orderby   Optional order clause
     * @param int    $offset    Optional offset of first row (default is zero)
     * @param int    $limit     Optional maximum number of returned rows (default is zero, means return all rows)
     * @param string $of        Optional assoc Array with foreign constraint (key=className, value=id)
     *
     * @return boolean  True on success, false on failure
     */
    public function beginEnum($where = null, $orderby = null, $offset = 0, $limit = 0, $of = null)
    {
        // Ensure to free previous enumeration
        $this->endEnum();

        // Extends where clause with "of" clause
        $ofClause = $this->ofClause($of);
        if ($ofClause != null)
        {
            if ($where != null && $where != "")
                $where = "(".$where.") and ".$ofClause;
            else
                $where = $ofClause;
        }

        // First of all, compute total number of items
        if ($where == null || (strcmp($where, $this->enumQuery) != 0))
        {
            // Remember actual query
            $this->enumQuery = $where;

            // Compute number of wcmSysObject matching the query
            $sql = 'SELECT COUNT(*) FROM ' . $this->tableName;
            if ($where != null && $where != '')
                $sql .= " WHERE ".$where;
            $this->enumCountQuery = $sql;
        }

        // Prepare sql query
        $sql = 'SELECT * FROM ' . $this->tableName;
        if ($where !== null && $where != '')
            $sql .= ' WHERE ' . $where;
        if ($orderby != null)
            $sql .= ' ORDER BY '.$orderby;

        // Load resultset
        $this->enumResultSet = $this->database->executeQuery($sql, null, $offset, $limit);
        if ($this->enumResultSet == null)
        {
            $this->lastErrorMsg = $this->database->getErrorMsg();
            return false;
        }

        return true;
    }

    /**
     * Moves to the next item of current enumeration
     *
     * @return boolean True if enumeration has succeed, false otherwise
     */
    public function nextEnum()
    {
        if ($this->enumResultSet == null)
            return false;

        // Erase existing property
        $properties = getPublicProperties($this);
        foreach ($properties as $property => $value)
        {
            $this->$property = null;
        }

        if (!$this->enumResultSet->next())
        {
            // We have reach the end of the resultset
            $this->endEnum();
            return false;
        }

        // Bind current object to current resultset row
        bindArrayToObject($this->enumResultSet->getRow(), $this);
        $this->unserializeProperties();

        return true;
    }

    /**
     * Stops current enumeration
     */
    public function endEnum()
    {
        // Free resultset
        $this->enumResultSet = null;
        $this->enumQuery = null;
        $this->enumTotalCount = -1;
    }

    /**
     * Returns the total number of objects which can be enumerated (regardless of the
     * offset and limit parameters passed in the {@link beginEnum()} method)
     *
     * @return int Total number of enumerable objects
     */
    public function enumCount()
    {
        // compute enum count on demand and only once!
        if ($this->enumTotalCount == -1)
        {
            $this->enumTotalCount = $this->database->executeScalar($this->enumCountQuery);
        }

        return $this->enumTotalCount;
    }

    /**
     * Check is lock mode is optimistic (and not pessimistic)
     *
     * @return bool TRUE is lock mode is optimistic
     */
    public function isLockOptimistic()
    {
        return $this->isLockOptimistic;
    }

    /**
     * Lock current object
     *
     * @return bool TRUE if lock has been set, FALSE is object was locked by another user
     */
    public function lock()
    {
        // Check lock
        if (!$this->checkLock('lock')) return false;

        $this->editable = (wcmLock::lock($this) !== null);
        return $this->editable;
    }

    /**
     * Get current information on lock
     *
     * @param bool $refresh TRUE to refresh lock info (by default) or retrieve last info
     *
     * @return wcmLock Lock information for current sysobject
     */
    public function getLockInfo($refresh = true)
    {
        if ($refresh)
            $this->lockInfo = wcmLock::get($this);

        return $this->lockInfo;
    }

    /**
     * Unlock current object
     *
     * @return boolean TRUE if lock has been removed, FALSE if object was locked by another user
     */
    public function unlock()
    {
        // Check lock
        if (!$this->checkLock('unlock')) return false;
        $this->editable = -1;
        return wcmLock::unlock($this);
    }

    /**
     * Check if current object is editable from the point of view of the currently connected user.
     * An object is editable if one of the 3 conditions below are matched:
     * - Object allow optimistic lock
     * - Object is a new object
     * - Object is unlocked or locked by current user
     *
     * @return bool TRUE when object is editable by current user
     */
    public function isEditable()
    {
        if (!$this->id)
        {
            $this->lockInfo = new wcmLock($this);
            return true;
        }

        // First computation?
        if ($this->editable == -1)
        {
            if ($this->isLockOptimistic)
            {
                $this->lockInfo = wcmLock::get($this);
                $this->editable = ($this->lockInfo->userId == 0 || $this->lockInfo->userId == wcmSession::getInstance()->userId);
            }
            else
            {
                $this->lockInfo = wcmLock::get($this);
                $this->editable = ($this->lockInfo->userId == wcmSession::getInstance()->userId);
            }
        }

       return $this->editable;
    }

    /**
     * Check if object is locked from the point of view of the currently connected user.
     * For instance: if current user (from session) has locked this object, isLocked() will return FALSE
     * if another user has locked this object, isLocked() will return TRUE
     * if nobody has locked this object, isLocked() will return FALSE
     *
     * @return bool TRUE if current connected user cannot modify/delete the object
     */
    public function isLocked()
    {
        $this->lockInfo = wcmLock::get($this);
        return ($this->lockInfo->userId != 0 && $this->lockInfo->userId != wcmSession::getInstance()->userId);
    }
    
    /**
     * Check if object has a lock on it. This is different from isLocked() as hasLock() will return
     * true regardless of which session user is calling this method.
     *
     * @return bool True if object has a lock, false if object doesn't.
     */
    public function hasLock()
    {
        $lockInfo = wcmLock::get($this);
        return !($lockInfo->userId != 0);
    }

    /**
     * This function test is the connected user (from session) can modify/delete the current object
     * and update the lastErrorMessage property if object is locked.
     *
     * @param string $operation Name of operation to attempt (e.g. 'delete', 'save', ...)
     *
     * @return bool TRUE if connected user can alter the current object
     */
    protected function checkLock($operation)
    {
        if ($this->isLocked())
        {
            $this->lastErrorMsg = $this->getClass() . '::' . $operation . ' failed : object ' . $this->id . ' is locked';
            return false;
        }

        return true;
    }
}
?>
