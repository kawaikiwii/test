<?php

/**
 * Project:     WCM
 * File:        api/search/wcm.bin.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     3.2
 *
 */

class wcmBin implements wcmEnumerable
{
    
    /**
     * Name of saved bin
     */
    public $name;
    
    /**
     * Description of saved bin
     */
    public $description;
    
    /**
     * userId of bin owner
     */
    public $userId;
    
    /**
     * XML representation of objects present in the bin 
     */
    public $content;
    
    /**
     * Bin id
     */
    public $id;
    
    /**
     * Boolean to indicate whether the bin is part of the dashboard or not
     */
    public $dashboard;
     
    /**
     * Private members
     */ 
    private $tableName      = "wcm_user_bin";
    private $enumTotalCount = null;
    private $enumQuery      = null;
    private $enumResult     = null;
    private $enumFrom       = null;
    private $enumLimit      = null;
    /**
     * Protected members
     */

    /**
     * (wcmDatabase) Datebase where biz__bin table is stored
     */
    protected $database = null;

    /**
     * (wcmProject) Current project
     */
    protected $project = null;
    
    
    /**
     * Constructor
     *
     * @param wcmProject $project      The current project
     * @param string     $connectorRef Optional reference of connector used to access database where biz__relation table is stored (default: biz)
     * @param string     $tableName   Optional table name (default: biz__relation)
     *
     */
    function __construct($project = null, $tableName = "wcm_user_bin")
    {
        $this->tableName = $tableName;
        $this->project = wcmProject::getInstance();
        $this->database  = $this->project->database;
    }
    
    public function save()
    {
        $this->database->storeSysObject($this);
    }
    
    public function removeOne()
    {
        // Delete reference
        $sql  = "DELETE FROM ".$this->database->quoteIdentifier($this->tableName)." WHERE id=? ";
        return $this->database->executeStatement($sql, array($this->id));
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
            if ($where != null && $where != '')
                $where = '('.$where.') AND '.$ofClause;
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
                $sql .= ' WHERE '.$where;

            $this->enumTotalCount = $this->database->executeScalar($sql);
            if ($this->enumTotalCount == 0)
                return false;
        }

        // Prepare sql query
        $sql = 'SELECT * FROM ' . $this->tableName;
        if ($where !== null && $where != '')
            $sql .= ' WHERE '.$where;
        if ($orderby != null)
            $sql .= ' ORDER BY '.$orderby;

        // Load resultset
        $this->enumResult = $this->database->executeQuery($sql, null, $offset, $limit);

        if ($this->enumResult == null)
        {
            $this->_lastErrorMsg = $this->database->getErrorMsg();
            return false;
        }

        return true;
    }

    /**
     * Gets the next item in the current enumeration.
     *
     * @return mixed The next item on success, false otherwise
     */
    public function nextEnum()
    {
        if ($this->enumResult == null)
            return false;
            
        if (!$this->enumResult->next())
        {
            // We have reach the end of the resultset
            $this->endEnum();
            return false;
        }

        // Bind current object to current resultset row
        return bindArrayToObject($this->enumResult->getRow(), $this);
    }

    /**
     * Ends the current enumeration.
     *
     */
    function endEnum()
    {
        // Free resultset
        $this->enumResult = null;
        $this->enumTotalCount = 0;
    }

    /**
     * Gets the total number of objects which can be enumerated.
     *
     * @return int The total number of objects which can be enumerated
     */
    function enumCount()
    {
        return $this->enumTotalCount;
    }
    
     /** Computes the sql where clause matching foreign constraints
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
                case 'site':
                    if ($sql != null) $sql .= ' AND ';
                    $sql .= 'siteId='.$value;
                    break;

                case 'channel':
                    if ($sql != null) $sql .= ' AND ';
                    $sql .= 'channelId='.$value;
                    break;
            }
        }
        return $sql;
    }
    
     /**
     * Returns the name of the table. This property is used by wcmDatabase::storeSysObject() to
     * store a generic object (assuming 'id' is a primary key with auto-increment)
     * 
     * @return string The table name where bizrelations are stored
    */
    public function getTableName() 
    {
        return $this->tableName;    
    }
    
     /**
     * Returns an associative array containing public properties
     * of the object with corresponding values
     *
     * @return assoc array
     */
    public function getAssocArray()
    {
        $assoc = array();

        foreach(getPublicProperties($this) as $key => $val)
        {
            $assoc[$key] = $val;
        }
        
        return $assoc;
    }

     /**
     * Returns the class name of this object
     *
     * @return string Class name of this object
     */
    public function getClass()
    {
        return get_class($this);
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
        if (!$this->database) return null;

        // Clear last error message
        $this->_lastErrorMsg = '';

        // Refresh object itself ?
        if ($id == null)
            $id = $this->id;

        // Ignore empty or disconnected object
        if ($id === null || $id == 0 || $this->tableName === null || $this->tableName == '')
            return $this;

        // Clear all "public" properties before refresh
        foreach(getPublicProperties($this) as $key => $value)
        {
            if ($key[0] != '_')
                $this->$key = (is_bool($this->$key) ? false : null);
        }

        // Load values from database and bind them to current object
        if (!$this->database->bindSysObject($this, $id))
        {
            $this->_lastErrorMsg = $this->getClass() . ':: refresh failed : ' . $this->database->getErrorMsg();
            $this->id = 0;
        }

        return $this;
    }
}
?>