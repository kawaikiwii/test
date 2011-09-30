<?php

/**
 * Project:     WCM
 * File:        wcm.group.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */


/**
 * The wcmGroup class represents a generic Plug'n web group
 * => Groups have a set of {@link wcmUser} objects and permissions affected to {@link wcmRole} objects
 */
class wcmGroup extends wcmSysobject
{
    /**
     * Object name
     */
    public $name;
    
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
 
        return true;
    }
    
    /**
     * Returns an array of users belonging to current group
     *
     * @param string $where   An optional where clause (default is null)
     * @param string $orderBy An optional order clause (default is 'name')
     *
     * @return array An associative array of {@link wcmUser} objects (keys are ids)
     */
    public function getMembers($where = null, $orderBy = 'name')
    {
        if ($this->id == wcmMembership::EVERYONE_GROUP_ID)
        {
            $clause = null;
        }
        else
        {
            $clause = "id IN (SELECT userId FROM #__member WHERE groupId=".$this->id.")";
        }
        
        if ($where != null)
            $clause = ($clause == null ) ? $where : ($clause . " AND (" . $where . ")");

        return $this->getProject()->membership->getUsers($clause, $orderBy);
    }

    /**
     * Checks if a specific {@link wcmUser} belongs to current group
     *
     * @param int $userId The wcmUser id to check
     *
     * @return boolean True if specified user belongs to current group
     */
    public function hasMember($userId)
    {
        $sql = "select count(*) from #__member where groupId=? AND userId=?";
        $params = array($this->id, $userId);
        return ($this->getProject()->database->executeScalar($sql, $params) > 0);
    }   
    
    /**
     * Adds a specific user to current group
     *
     * @param int $userId  The user id to add in current group
     *
     * @return boolean True if user has been added, false otherwise
     */
    public function addMember($userId)
    {
        $this->lastErrorMsg =  null;
        
        $sql = "insert into #__member(groupId,userId) values(?,?)";
        $params = array($this->id, $userId);

        if ($this->getProject()->database->executeStatement($sql, $params) == -1)
        {
            $this->lastErrorMsg = $this->getProject()->database->getErrorMsg();
            return false;
        }
        
        return true;
    }
    
    /**
     * Removes a specific wcmUser from current wcmGroup
     *
     * @param int $userId   The wcmUser id to remove
     *
     * @return boolean True on success, false on failure
     */
    public function removeMember($userId)
    {
        $this->lastErrorMsg =  null;
        
        $sql = "delete from #__member where groupId=? AND userId=?";
        $params = array($this->id, $userId);
        if ($this->getProject()->database->executeStatement($sql, $params) == -1)
        {
            $this->lastErrorMsg = $this->getProject()->database->getErrorMsg();
            return false;
        }
        
        return true;
    }

    /**
     * Delete group from database (and associated members and permissions)
     *
     * @return boolean True on success, false otherwise
     */
    public function delete()
    {
        $this->lastErrorMsg =  null;
        
        // isEverybody cannot be deleted
        if ($this->id == wcmMembership::EVERYONE_GROUP_ID)
        {
            $this->lastErrorMsg = "The 'everyone' group cannot be removed !";
            return false;
        }

        // Delete members
        $sql = "delete from #__member where groupId=?";
        $params = array($this->id);
        if ($this->getProject()->database->executeStatement($sql, $params) == -1)
        {
            $this->lastErrorMsg = $this->getProject()->database->getErrorMsg();
            return false;
        }

        // Delete permissions
        $sql = "delete from #__permission where groupId=?";
        $params = array($this->id);
        if ($this->getProject()->database->executeStatement($sql, $params) == -1)
        {
            $this->lastErrorMsg = $this->getProject()->database->getErrorMsg();
            return false;
        }

        // Delete object
        return parent::delete();
    }

    /**
     * Computes the sql where clause matching foreign constraints
     *
     * @param string $of Assoc Array with foreign constrains (key=className, value=id)
     *
     * @return string Sql where clause matching "of" constraints or null
     */
    protected function ofClause($of)
    {
        if ($of == null || !is_array($of)) return null;

        $sql = null;
        foreach($of as $key => $value)
        {
            switch($key)
            {
                case "wcmUser": return ("id in (select groupId from #__member where userId=".$value.")");
            }
        }

        return $sql;
    }

    /**
     * Gets object ready to store by getting modified date, creation date etc
     * The wcmGroup store will delete old permissions and update new ones.
     *
     */
    protected function store()
    {
        if(!parent::store()) return false;
        
        $project = wcmProject::getInstance();

        // before storing, we need to remove old permissions
        $permissionTypes = getArrayParameter($this->serialStorage, 'permissionTypes');
        if ($permissionTypes)
        {
            foreach ($permissionTypes as $permissionType)
            {
                $sql = "DELETE FROM #__permission WHERE groupId=? AND target like '%" . $permissionType . "%'";
                $params = array($this->id, $permissionType);
    
                if ($project->database->executeStatement($sql, $params) == -1)
                {
                    $this->lastErrorMsg = $permissionType . '::DELETE failed : ' . $project->database->getErrorMsg();
                    return false;
                }
            }
        }

        // insert the new permissions
        $newPermissions = getArrayParameter($this->serialStorage, 'permissions');
        if($newPermissions)
        {
            foreach ($newPermissions as $permission)
            {
                // get values
                $perms = explode('*', $permission, 2);
                $permsArray[$perms[0]][] = $perms[1];
            }

            foreach ($permsArray as $wcmObject => $permissionMask)
            {
                $finalMask = 0;
                foreach($permissionMask as $mask)
                {
                    // get group and values
                    $values = explode('#', $mask);
                    $group = ($values[1] == 0) ? $this->id : $values[1];
                    $finalMask += $values[0];

                    // when a permission is set (WRITE, EXECUTE, DELETE)
                    // permission P_READ is always available
                    if ($finalMask) $finalMask |= wcmPermission::P_READ;
    
                    // when a permission is none keep only that one!
                    if ($finalMask & wcmPermission::P_NONE) $finalMask = wcmPermission::P_NONE;

                    // get object to securize
                    $securedObject = explode('_', $wcmObject);
                    $obj = new $securedObject[0]();
                    $obj->refresh($securedObject[1]);

                    // set permissions
                    $obj->setGroupPermission($group, $finalMask);
                }
            }
        }
        return true;
    }
}