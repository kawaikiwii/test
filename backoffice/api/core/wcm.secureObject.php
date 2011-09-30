<?php
/**
 * Project:     WCM
 * File:        wcm.SecureObject.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * The secure object class is the parent abstract class
 * of all WCM objects that need to be securized
 */
abstract class wcmSecureObject extends wcmObject 
{
    /**
     * Defines permission for a specific group
     *
     * @param int $groupId Identifier of group
     * @param int $permission permission mask (e.g. P_READ | P_DELETE)
     *
     * @return boolean True on success, False on failure
     */
    public function setGroupPermission($groupId, $permission)
    {
        $this->lastErrorMsg = '';
        $project = wcmProject::getInstance();

        // Erase previous permission
        $sql = 'DELETE FROM #__permission WHERE groupId=? AND target=?';
        $params = array($groupId, $this->getPermissionTarget());

        if ($project->database->executeStatement($sql, $params) == -1)
        {
            $this->lastErrorMsg = $this->getClass() . '::setGroupPermission failed : ' . $project->database->getErrorMsg();
            return false;
        }
        
        // Don't write permission when it's null or empty
        if ($permission)
        {
            $sql = 'INSERT INTO #__permission(groupId, permissions, target) VALUES(?, ?, ?)';
            $params = array($groupId, $permission, $this->getPermissionTarget());
            if ($project->database->executeStatement($sql, $params) == -1)
            {
                $this->lastErrorMsg = $this->getClass() . '::setGroupPermission failed : ' . $project->database->getErrorMsg();
                return false;
            }
        }

        return true;
    }
    
    /**
     * Gets permissions for a group
     *
     * @param   int     $groupId    Identifier of group
     * 
     * @return  int     $permission value (bit field) of this permission or null if sys/biz class is not defined
     */
    public function getGroupPermissions($groupId)
    {
        $project = wcmProject::getInstance();
        $sql     = "SELECT permissions FROM #__permission WHERE groupId=? AND target=?";
        $permissions  = $project->database->executeScalar($sql, array($groupId, $this->getPermissionTarget()));
        if ($permissions === null)
        {
            if ($this instanceof wcmBizobject)
            {
                $bc = $project->bizlogic->getBizclassByClassName($this->getClass());
            }
            elseif ($this instanceof wcmSysobject)
            {
                $bc = $project->bizlogic->getSysclassByClassName($this->getClass());
            }
            else
            {
                // wcmObject has no masterclass
                return null;
            }
            
            if (!$bc)
            {
                throw new Exception('No bizclass or sysclass found for ' . $this->getClass());
            }
            $permissions = $project->database->executeScalar($sql, array($groupId, $bc->getPermissionTarget()));
            
        }

        return $permissions;
    }
    
    /**
     * Gets and computes permissions for a user
     *
     * @param   int $userId     Identifier of user
     * 
     * @return  int $permission value of this user on a sysobject
     */
    public function getUserPermissions($userId)
    {
        $project = wcmProject::getInstance();
        $user = $project->membership->getUserById($userId);

        if ($user->isAdministrator)
           return wcmPermission::P_ALL;

        $permissions = 0;
        foreach ($user->getGroups() as $id => $group)
        {
            $permissions |= $this->getGroupPermissions($id);
        }
        
        return $permissions;
    }
    
    /**
     * Computes the target for a permission
     * 
     * @return string the target for a specific permission
     */
    public function getPermissionTarget()
    {
        return $this->getClass() . '_' . $this->id;
    }

    /**
     * Update the permissions
     *
     * @param Array $array  Array of new permissions
     */
    public function updatePermissions($permissionTypes = null, $newPermissions = null)
    {
        if ($permissionTypes) $this->serialStorage['permissionTypes'] = $permissionTypes;
        if ($newPermissions)  $this->serialStorage['permissions']     = $newPermissions;
    }

    /**
     * Lock current object
     *
     * @return bool TRUE if lock has been set, FALSE is object was locked by another user
     */
    public function lock()
    {
        // Check P_WRITE
        if (!wcmSession::getInstance()->isAllowed($this, wcmPermission::P_WRITE))
        {
            $this->lastErrorMsg = _INSUFFICIENT_PRIVILEGES;
            return false;
        }
        
        // Check lock
        if (!$this->checkLock('lock')) return false;

        $this->editable = (wcmLock::lock($this) !== null);
        return $this->editable;
    }

    /**
     * Unlock current object
     *
     * @return boolean TRUE if lock has been removed, FALSE if object was locked by another user
     */
    public function unlock()
    {
        // Check P_WRITE
        if (!wcmSession::getInstance()->isAllowed($this, wcmPermission::P_WRITE))
        {
            $this->lastErrorMsg = _INSUFFICIENT_PRIVILEGES;
            return false;
        }

        // Check lock
        if (!$this->checkLock('unlock')) return false;
        $this->editable = -1;
        return wcmLock::unlock($this);
    }

    /**
     * Deletes current object from database
     *
     * @return boolean true on success
     */
    public function delete()
    {
        // Check P_DELETE
        if (!wcmSession::getInstance()->isAllowed($this, wcmPermission::P_DELETE))
        {
            $this->lastErrorMsg = _INSUFFICIENT_PRIVILEGES;
            return false;
        }

        if (!parent::delete())
            return false;
            
        // Erase permissions
        $project = wcmProject::getInstance();
        $sql = 'DELETE FROM #__permission WHERE target=?';
        $params = array($this->getPermissionTarget());
        $project->database->executeStatement($sql, $params);
        
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
        // Check P_WRITE
        if (!wcmSession::getInstance()->isAllowed($this, wcmPermission::P_WRITE))
        {
            $this->lastErrorMsg = _INSUFFICIENT_PRIVILEGES;
            return false;
        }

        if (!parent::save($source))
            return false;

        return true;
    }
}