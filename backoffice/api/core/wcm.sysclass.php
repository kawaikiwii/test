<?php
/**
 * Project:     WCM
 * File:        wcm.sysclass.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * The wcmSysclass is used to describe system objects
 */
class wcmSysclass extends wcmSecureObject
{
    /**
     * Class name (unique index)
     */
    public $className;

    /**
     * Human readable name of this class
     * Use constant for I18N
     */
    public $name;
    
    /**
     * Connector for this bizclass
     */
    public $connectorId;

    /**
     * Table name for this bizclass
     */
    public $connectorTable;

    /**
     * Workflow for this bizclass
     */
    public $workflowId;

    /**
     * TRUE to allow optimistic lock mode
     */
    public $allowOptimisticLock;

    
    /**
     * Constructor
     */
    public function __construct($id = null)
    {
        $this->isLockOptimistic = true;
        parent::__construct($id);
    }

    /**
     * Set initial (default) values
     */
    protected function setDefaultValues()
    {
        $config = wcmConfig::getInstance();

         parent::setDefaultValues();
         $this->allowOptimisticLock = intval($config['wcm.default.optimisticLock']);
    }

    /**
     * Get the database used to store/fetch object
     */
    protected function getDatabase()
    {
        if (!$this->database)
        {
            $this->database = wcmProject::getInstance()->database;
            $this->tableName = '#__sysclass';
        }
        
        return $this->database;
    }
    
    /**
     * Retrieve the wcmConnector corresponding to current bizClass
     *
     * @return wcmConnector Connector corresponding to current bizClass
     */
    public function getConnector()
    {
        // Use cache
        return wcmProject::getInstance()->datalayer->getConnectorById($this->connectorId);
    }
    
    /**
     * Check validity of object
     *
     * @return boolean true when object is valid
     */
    public function checkValidity()
    {
        if (!parent::checkValidity())
            return false;
 
        // Check that class name is unique
        $sql = "SELECT COUNT(*) FROM " . $this->getTableName() . " WHERE className='".$this->className."' AND id!='" . $this->id . "'";
        if ($this->database->executeScalar($sql) > 0)
        {
            $this->lastErrorMsg = _CLASS_NAME_EXIST;
            return false;
        }
        
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
        
        if ($this->className  && strlen($this->className) > 255)
        {
            $this->lastErrorMsg = _ERROR_CLASSNAME_TOO_LONG;
            return false;
        }
        
        if ($this->connectorTable  && strlen($this->connectorTable) > 255)
        {
            $this->lastErrorMsg = _ERROR_CONNECTORTABLE_TOO_LONG;
            return false;
        }
        
        return true;
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

        if (isset($this->serialStorage['permissionTypes']))
        {
            // Erase permissions for the sysclass and all its related objects
            $project = wcmProject::getInstance();
            $sql = 'DELETE FROM #__permission WHERE target=?';
            $params = array($this->getPermissionTarget());
            $project->database->executeStatement($sql, $params);
            if ($this->className)
            {
                $sql = "DELETE FROM #__permission WHERE target LIKE '". $this->className . "%'";
                $project->database->executeStatement($sql);
            }

            // insert the new permissions
            $newPermissions = getArrayParameter($this->serialStorage, 'permissions');
            if($newPermissions)
            {
                $permsArray = array();
                foreach ($newPermissions as $permission)
                {
                    // get values
                    $perms = explode('*', $permission, 2);
                    $permsArray[$perms[0]][] = $perms[1];
                }

                foreach ($permsArray as $wcmObject => $permissionMask)
                {
                    $securedObject = explode('_', $wcmObject);
                    $groupId   = $securedObject[0];

                    $finalMask = 0;
                    foreach($permissionMask as $mask)
                    {
                        // get group and values
                        $values = explode('#', $mask);
                        $finalMask += $values[0];

                        // when a permission is set (WRITE, EXECUTE, DELETE)
                        // permission P_READ is always available
                        if ($finalMask) $finalMask |= wcmPermission::P_READ;
                        
                        // when a permission is none keep only that one!
                        if ($finalMask & wcmPermission::P_NONE) $finalMask = wcmPermission::P_NONE;

                        // set permissions
                        $this->setGroupPermission($groupId, $finalMask);
                    }
                }
            }
        }
        
        // Update cache
        wcmCache::setElem($this->getClass(), $this->className, $this);

        return true;
    }

    /**
     * Deletes current object from database
     *
     * @return boolean true on success, false on failure
     */
    public function delete()
    {
        if (!parent::delete())
            return false;
            
        // Erase permissions for the sysclass and all its related objects
        $project = wcmProject::getInstance();
        $sql = 'DELETE FROM #__permission WHERE target=?';
        $params = array($this->getPermissionTarget());
        $project->database->executeStatement($sql, $params);
        if ($this->className)
        {
            $sql = "DELETE FROM #__permission WHERE target LIKE '". $this->className . "%'";
            $project->database->executeStatement($sql);
        }

        // Update cache
        wcmCache::unsetElem($this->getClass(), $this->className, $this);
        
        return true;

    }
}