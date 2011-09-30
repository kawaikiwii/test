<?php
/**
 * Project:     WCM
 * File:        wcm.versionManager.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * The version class represents an instance of
 * a wcmObject's version
 */
class wcmVersion extends wcmObject
{
    /**
     * (string) Class of versioned object
     */
    private $objectClass;
    
    /**
     * (int) Id of versioned object
     */
    public $objectId;

    /**
     * (string) Serialized content
     */
    public $objectContent;
    
    /**
     * (date)Date of creation
     */
    public $createdAt;
    
    /**
     * (int) ID of user who has created this version
     */
    public $createdBy;

    /**
     * (int) Version number
     */
    public $versionNumber;

    /**
     * (int) Revision number
     */
    public $revisionNumber;
    
    /**
     * (string) Version comment
     */
    public $comment;

    /**
     * Default constructor
     *
     * @param string $objectClass Class of versioned object
     * @param int $objectId ID of versioned object
     */
    public function __construct($objectClass, $objectId)
    {
        // Pre-set objectClass and objectId
        $this->objectClass = $objectClass;
        $this->objectId = $objectId;
        
        parent::__construct();
        
        // Check table existence
        $lastVersion = $this->getLastVersion();
        if ($lastVersion === null)
        {
            $this->createTable();
            $lastVersion = 0;
        }
        $this->versionNumber = $lastVersion + 1;
    }

    /**
     * Returns the database used to store/fetch object
     * 
     * @return wcmDatabase Database used to store/fetch object
     */
    protected function getDatabase()
    {
        if (!$this->database)
        {
            $this->database = wcmVersionManager::getInstance()->getDatabase();
            $this->tableName = '#__' . $this->objectClass . '_versions';
        }
        return $this->database;
    }

    /**
     * Set all initial values of an object
     * This method is invoked by the constructor
     */
    protected function setDefaultValues()
    {
        parent::setDefaultValues();
        
        $this->objectContent = null;
        $this->createdAt = date('Y-m-d H:i:s');
        $this->createdBy = wcmSession::getInstance()->userId;
        $this->comment = null;
        $this->versionNumber = 1;
        $this->revisionNumber = 1;
    }

    /**
     * Returns the last version stored in the database
     *
     * @return int Last version number (or null if table does not exist)
     */
    public function getLastVersion()
    {
        $sql = 'SELECT MAX(versionNumber) FROM ' . $this->tableName . ' WHERE objectId=' . $this->objectId;
        return $this->database->executeScalar($sql);
    }
    
    /**
     * Exposes 'creator' in the getAssocArray
     *
     * @param bool $toXML TRUE if method is called in the context of toXML()
     *
     * @return array The session's user getAssocArray() (or null)
     */
    public function getAssoc_creator($toXML = false)
    {
        if ($toXML) return null;
        
        $user = $this->getCreator();
        return ($user) ? $user->getAssocArray($toXML) : null;
    }

    /**
     * Returns the creator of the version
     *
     * @return wcmUser Creator of the current version
     */
    public function getCreator()
    {
        $user = new wcmUser(null, $this->createdBy);
        return ($user->id == 0) ? null : $user;
    }
    
    /**
     * Finalize rollback (delete all in-between versions)
     */
    public function rollback()
    {
        $sql = 'DELETE FROM ' . $this->tableName . ' WHERE id >= ' . $this->id;
        $this->database->executeStatement($sql);
    }
    
    /**
     * Checks if the database table used for storage exists
     * If not, create the table on the fly.
     */
    protected function createTable()
    {
        // Create table (tableName, fields, primary key, other index)
        !$this->database->createTable(   $this->tableName,
                                        array('id' => array('type' => 'autoinc'),
                                              'objectId' => array('type' => 'integer'),
                                              'objectContent' => array('type' => 'text'),
                                              'createdAt' => array('type' => 'datetime'),
                                              'createdBy' => array('type' => 'integer'),
                                              'versionNumber' => array('type' => 'integer'),
                                              'revisionNumber' => array('type' => 'integer'),
                                              'comment' => array('type' => 'text', 'default' => 'null')
                                              ),
                                          'id',
                                          array('objectId', 'versionNumber'));
    }

    /**
     * Computes the sql where clause matching foreign constraints
     * => This method must be overloaded by child class
     *
     * @param string $of Assoc Array with foreign constrains (key=className, value=id)
     *
     * @return string Sql where clause matching "of" constraints or null
     */
    protected function ofClause($of)
    {
        if (is_array($of))
        {
            foreach($of as $className => $id)
            {
                // Assume we want to browse versions 'of' a specific object
                if ($className == $this->objectClass)
                    return 'objectId='.$id;
            }
        }

        return null;
    }
}
?>