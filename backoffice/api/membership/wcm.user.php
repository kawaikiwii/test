<?php
/**
 * Project:     WCM
 * File:        wcm.user.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */


/**
 * The wcmUser class represents a generic Plug'N web user
 */
class wcmUser extends wcmSysobject
{
    // array of group IDs
    private $groups;
    
    /**
     * User name (firstname and lastname)
     */
    public $name;
    
    /**
     * User login (unique index)
     */
    public $login;
    
    /**
     * User password (encrypted)
     */
    public $password;
    
    /**
     * User password (encrypted to be readable)
     */
    public $token;
    
    /**
     * User e-mail
     */
    public $email;
    
    /**
     * True if user is the system administrator
     */
    public $isAdministrator;
    
    /**
     * Default language for this user
     */
    public $defaultLanguage;
    
    /**
     * Timezone for this user
     */
    public $timezone;

  
    /**
     * Set all initial values of an object
     * This method is invoked by the constructor
     */
    protected function setDefaultValues()
    {
        parent::setDefaultValues();

        $config  = wcmConfig::getInstance();
        $this->isAdministrator = 0;
        $this->timezone=0;
        $this->defaultLanguage = $config['wcm.default.language'];
        $this->groups = array(wcmMembership::EVERYONE_GROUP_ID => wcmMembership::EVERYONE_GROUP_ID);
    }
    
    /**
     * Returns an array of group IDs in which current wcmUser belongs
     *
     * @return array An associative array of group ids
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Defines groups in which current wcmUser belongs
     *
     * @param groups array  An array of group IDs
     *
     * @return True on success, false otherwise
     */
    public function setGroups(array $groupIds)
    {
        // ensure "everyone" group is in array
        $this->groups = array(wcmMembership::EVERYONE_GROUP_ID => wcmMembership::EVERYONE_GROUP_ID);
        foreach($groupIds as $id)
        {
            if (!array_key_exists($id, $this->groups))
                $this->groups[$id] = $id;
        }
    }

    /**
     * Adds the current user to a specific group
     *
     * @param int $userId  The user id to add in current group
     */
    public function addToGroup($groupId)
    {
        if (!array_key_exists($groupId, $this->groups))
            $this->groups[$groupId] = $groupId;
    }

    /**
     * Removes the current user from a specific group
     */
    public function removeFromGroup($groupId)
    {
        if ($groupId != wcmMembership::EVERYONE_GROUP_ID)
        {
            unset($this->groups[$groupId]);
        }
    }
    
    /**
     * Checks if current wcmUser belongs to a specific wcmGroup
     *
     * @param int $groupId The wcmGroup id to check
     *
     * @return boolean True if user belongs to specified group
     */
    public function isMemberOf($groupId)
    {
        return (array_key_exists($groupId, $this->groups));
    }

    /**
     * Check validity of user
     *
     * @return boolean TRUE when user is valid
     */
    public function checkValidity()
    {
        $this->lastErrorMsg = null;
           
        // Login is mandatory
        if ($this->login == null || $this->login == "")
        {
            $this->lastErrorMsg = _ERROR_USER_LOGIN_IS_MANDATORY;
            return false;
        }

        // Login must be unique
        $sql = "SELECT COUNT(*) FROM " . $this->getTableName() . " WHERE login='".$this->login."' AND id !='" . $this->id . "'";
        if ($this->database->executeScalar($sql) > 0)
        {
            $this->lastErrorMsg = _LOGIN_EXIST;
            return false;
        }
        
        // If empty, set name equals to login
        if ($this->name == null || $this->name == "")
            $this->name = $this->login;

        // Enforce isAdministrator to true if user is root
        if ($this->id == wcmMembership::ROOT_USER_ID)
            $this->isAdministrator = true;
        
        if (strlen($this->login) > 64)
        {
            $this->lastErrorMsg = _ERROR_USER_LOGIN_TOO_LONG;
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
        
        if ($this->email  && strlen($this->email) > 255)
        {
            $this->lastErrorMsg = _ERROR_EMAIL_TOO_LONG;
            return false;
        }
               
        if ($this->defaultLanguage  && strlen($this->defaultLanguage) > 8)
        {
            $this->lastErrorMsg = _ERROR_DEFAULT_LANGUAGE_TOO_LONG;
            return false;
        }
        
        return true;
    }

    /**
     * Load or refresh object content
     *
     * @param int $id optional argument, if not specifed current id is used
     *
     * @return object freshen object or null on failure
     */
    public function refresh($id = null)
    {
        if (!parent::refresh($id))
            return null;

        if ($this->id)
        {
            // load group IDs in memory
            $this->groups = array(wcmMembership::EVERYONE_GROUP_ID);
            $rs = $this->database->executeQuery('SELECT groupId FROM #__member WHERE userId=' . $this->id);
            if ($rs)
            {
                while ($rs->next())
                {
                    $row = $rs->getRow();
                    if (!array_key_exists($row['groupId'], $this->groups))
                    {
                        $this->groups[$row['groupId']] = $row['groupId'];
                    }
                }
                unset($rs);
            }
        }

        return $this;
    }
    
    /**
     * Binds an assoc array to this object
     *
     * @param array  $assocArray (or null to ignore bindings)
     *
     * @return true on success, false otherwise
     */
     public function bind(array $assocArray = null)
     {
        if (is_array($assocArray))
        {
            // encode password when provided
            if (getArrayParameter($assocArray, 'password', null))
            {
                // same password?
                if ($assocArray['password'] != $this->password)
                {
                    $assocArray['token'] = base64_encode($assocArray['password']);
                    $assocArray['password'] = md5($assocArray['password']);
                }
            }
            else
            {
                unset($assocArray['password']);
            }
        }

        return parent::bind($assocArray);
     }

    /**
     * Store current object into database
     *
     * @return true on success or an error message (string)
     */
    protected function store()
    {
        if (!parent::store())
            return false;
        
        // remove previous groups
        $sql = "DELETE FROM #__member WHERE userId=".$this->id;
        if ($this->getProject()->database->executeStatement($sql) == - 1)
        {
            $this->lastErrorMsg = $this->getProject()->database->getErrorMsg();
            return false;
        }

        // add new groups
        foreach($this->groups as $id => $group)
        {
            $sql = "INSERT INTO #__member(userId, groupId) VALUES(?, ?)";
            $params = array($this->id, $id);

            if ($this->getProject()->database->executeStatement($sql, $params) == - 1)
            {
                $this->lastErrorMsg = $this->getProject()->database->getErrorMsg();
                return false;
            }
        }
                
        return true;
    }


    /**
     * Deletes user from database (and associated members and permission)
     *
     * @return boolean True on success, false otherwise
     */
    public function delete($oid=null)
    {
        $this->lastErrorMsg =  null;
        
        // Delete members
        $sql = "delete from #__member where userId=?";
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
     * Checks if a given action is allowed for current user
     *
     * @param   sysobject   $sysobject  sysobject on which to check permssion
     * @param   constant    $permission permission value
     *
     * @return boolean True if role is granted, false if role is denied
     */
    public function isAllowed($sysobject, $permission)
    {
        // bypass security if user is an administrator
        return ($this->isAdministrator || (($this->getPermissions($sysobject)) & $permission));
    }
    
    /**
     * Get permission value
     * 
     * @param   sysobject   $sysobject  sysobject on which to check permssion
     * 
     * @return permission value
     */
    public function getPermissions($sysobject)
    {
        return $sysobject->getUserPermissions($this->id);
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
                case "wcmGroup":
                    if ($sql != null) $sql .= ' AND ';
                    $sql .= ("id in (select userId from #__member where groupId=".$value.")");
                    break;
            }
        }

        return $sql;
    }
}