<?php

/**
 * Project:     WCM
 * File:        wcm.membership.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * The wcmMembership class is an helper class used to manage users, groups, roles and permissions
 */
class wcmMembership
{
    /**
     * (1) Unique identifier for everyone group
     */
    const EVERYONE_GROUP_ID = 1;

    /**
     * (1) Unique indentifier for root user
     */
    const ROOT_USER_ID = 1;

    /**
     * Returns the 'everyone' wcmGroup
     *
     * @return wcmGroup The {@link wcmGroup} corresponding to 'everyone'
     */
    public function getEveryoneGroup()
    {
        $project = wcmProject::getInstance();
        return new wcmGroup($project, wcmMembership::EVERYONE_GROUP_ID);
    }

    /**
     * Returns a group by its id
     *
     * @param int $id The wcmGroup id
     *
     * @return wcmGroup The groups matching given id or null if id is invalid
     */
    public function getGroupById($id)
    {
        $project = wcmProject::getInstance();
        $item = new wcmGroup($project, $id);
        if ($item->id == 0)
        {
            unset($item);
            return null;
        }
        
        return $item;
    }

    /**
     * Returns an array of groups matching a specific where clause
     *
     * @param string $where An optional where clause to apply on groups (default is null)
     * @param string $orderBy An optinal order clause (default is 'name')
     *
     * @return array An associative array of {@link wcmGroup} objects (keys are ids)
     */
    public function getGroups($where = null, $orderBy = 'name')
    {
        $project = wcmProject::getInstance();
        $enum = new wcmGroup($project);
        if (!$enum->beginEnum($where, $orderBy))
            return null;

        $result = array();
        while ($enum->nextEnum())
        {
            $result[$enum->id] = clone($enum);
        }
        $enum->endEnum();
        
        return $result;
    }

    /**
     * Returns a user by its id
     *
     * @param int $id The user id
     *
     * @return wcmUser The user matching given id (or null if id is invalid)
     */
    public function getUserById($id)
    {
        $project = wcmProject::getInstance();
        $item = new wcmUser($project, $id);
        if ($item->id == 0)
        {
            unset($item);
            return null;
        }
        return $item;
    }

    /**
     * Returns an array of users matching a specific where clause
     *
     * @param string $where An optional where clause to apply on users (default is null)
     * @param string $orderBy An optinal order clause (default is 'name')
     *
     * @return array An associative array of {@link wcmUser} objects (keys are ids)
     */
    public function getUsers($where = null, $orderBy = 'name')
    {
        $project = wcmProject::getInstance();
        $enum = new wcmUser($project);
        if (!$enum->beginEnum($where, $orderBy))
            return null;

        $result = array();
        while ($enum->nextEnum())
        {
            $result[$enum->id] = clone($enum);
        }
        $enum->endEnum();
        
        return $result;
    }

    /**
     * Returns an array of users beloging to a specific group
     *
     * @param mixed $groupId The group object of the group id to scan
     * @param string $orderBy An optinal order clause (default is 'name')
     *
     * @return array An associative array of {@link wcmUser} objects (keys are ids)
     */
    public function getUsersOfGroup($groupId, $orderBy = 'name')
    {
        if ($groupId instanceof wcmGroup)
            $groupId = $groupId->id;

        $where = "id in (select userId from #__member where groupId=".$groupId.")";
        return $this->getUsers($where, $orderBy);
    }
    
    /**
     * Returns an array of roles-categories
     *
     * @reutrn array An associative array of {@link wcmRoleCategory} objects (keys are ids)
     */
    function getRolesCategory($where = null, $orderBy = 'name')
    {
        $project = wcmProject::getInstance();
        $enum = new wcmRoleCategory($project);
        if (!$enum->beginEnum($where, $orderBy))
            return null;

        $result = array();
        while ($enum->nextEnum())
        {
            $result[$enum->id] = clone($enum);
        }
        $enum->endEnum();
        return $result;
    }

    /**
     * Returns a role-category by its id
     *
     * @param int $id The category id
     *
     * @return wcmRoleCategory The category matching given id (or null if id is invalid)
     */
    public function getRoleCategoryById($id)
    {
        $project = wcmProject::getInstance();
        $item = new wcmRoleCategory($project, $id);
        if ($item->id == 0)
        {
            unset($item);
            return null;
        }
        return $item;
    }
    
    /**
     * Returns an array of roles matching a specific where clause
     *
     * @param where string (optional) where clause to apply on roles
     * @param orderBy string (optional) order clause (default is 'name')
     */
    function getRoles($where = null, $orderBy = 'name')
    {
        $project = wcmProject::getInstance();
        $enum = new wcmRole($project);
        if (!$enum->beginEnum($where, $orderBy))
            return null;

        $result = array();
        while ($enum->nextEnum())
        {
            $result[$enum->id] = clone($enum);
        }
        $enum->endEnum();
        
        return $result;
    }

    /**
     * Returns a role by its id
     *
     * @param int $id The role id
     *
     * @return wcmRole The category matching given id (or null if id is invalid)
     */
    public function getRoleById($id)
    {
        $project = wcmProject::getInstance();
        $item = new wcmRole($project, $id);
        if ($item->id == 0)
        {
            unset($item);
            return null;
        }
        return $item;
    }
}
?>
