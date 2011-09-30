<?php
/**
 * Project:     WCM
 * File:        wcm.connector.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * The wcmConnector class represents an abstract connection
 * to a database (rdbms, xmldata, ...)
 */
class wcmConnector extends wcmSysobject
{
    /**
     * (string) Connector name
     */
    public $name = null;

    /**
    * (string) Unique reference representing this connector
    */
    public $reference = null;

    /**
    * (string) Connection string (Creole PEAR/DSN style)
    */
    public $connectionString = null;

    /**
    * (string) Table prefix (default is "biz_")
    */
    public $tablePrefix = "biz_";

    /**
     * wcmDatabase Pointer to related business database
     */
    protected $businessDatabase = null;

	/**
     * Check validity of object
     *
     * @return boolean TRUE if object is valid
     */
    public function checkValidity()
    {
       if (trim($this->name . ' ') == '')
        {
            $this->lastErrorMsg = _ERROR_NAME_IS_MANDATORY;
            return false;
        }
            
        if (strlen($this->name) > 255)
        {
            $this->lastErrorMsg = _ERROR_NAME_TOO_LONG;
            return false;
        }
        
        if (trim($this->reference . ' ') == '')
        {
            $this->lastErrorMsg = _ERROR_REFERENCE_IS_MANDATORY;
            return false;
        }
        
        if (strlen($this->reference) > 64)
        {
            $this->lastErrorMsg = _ERROR_REFERENCE_TOO_LONG;
            return false;
        }
        
      if (trim($this->connectionString . ' ') == '')
        {
            $this->lastErrorMsg = _ERROR_CONNECTIONSTRING_IS_MANDATORY;
            return false;
        }
        
      if (trim($this->tablePrefix . ' ') == '')
        {
            $this->lastErrorMsg = _ERROR_TABLEPREFIX_IS_MANDATORY;
            return false;
        }
        
        if (strlen($this->tablePrefix) > 32)
        {
            $this->lastErrorMsg = _ERROR_TABLEPREFIX_TOO_LONG;
            return false;
        }
        
        return true;
    }
    
    /**
     * Refresh connector using a reference
     *
     * @param string $reference An existing connector reference
     *
     * @return wcmConnector freshen connector or null on failure
     */
    public function refreshByReference($reference)
    {
        $id = $this->database->executeScalar('SELECT id FROM ' . $this->tableName . ' WHERE reference=?', array($reference));
        return $this->refresh($id);
    }

    /**
     * Returns an instance of a wcmDatabase object
     *
     * @return wcmDatabase A wcmDtabase object matching connector parameters
     */
    public function getBusinessDatabase()
    {
        return new wcmDatabase($this->connectionString, $this->tablePrefix);
    }

    /**
     * Returns database schema (metadata and information on the database, tables, ...)
     *
     * @return DatabaseInfo Creole DatabaseInfo metadata class
     */
    public function getSchema()
    {
        return $this->getBusinessDatabase()->getSchema();
    }

    /**
     * Inserts or Updates object in database
     *
     * @param int     $userId Id of the wcmUser who is creating or updating the object
     *
     * @return boolean true on success, false on failure
     */
    protected function store($userId = null)
    {
        if (!parent::store($userId))
            return false;

        // Update cache
        wcmCache::setElem($this->getClass(), $this->id, $this);

        return true;
    }

    /**
     * Deletes current connector from database (and associated tables and fields)
     *
     * @return true on success or an error message (string)
     */
    public function delete()
    {
        $oldId = $this->id;
        
        if (!parent::delete())
            return false;

        // Update cache
        wcmCache::unsetElem($this->getClass(), $oldId);

        return true;
    }
}