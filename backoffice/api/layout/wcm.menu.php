<?php

/**
 * Project:     WCM
 * File:        wcm.menu.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */


/**
 * This class represents a menu entry
 */
class wcmMenu extends wcmSysobject
{
    /**
     * (string) Menu name
     */
    public $name = null;

    /**
     * (int) Parent id (or zero for root level menus)
     */
    public $parentId = 0;

    /**
     * (int) Menu rank (in siblings)
     */
    public $rank = 0;

    /**
     * (string) Url to load when menu is selected (or null)
     */
    public $url = null;

    /**
     * (string) Action associated to menu (used in conjunction with url)
     */
    public $action = null;

    /**
     * (string) Is menu dynamically rendered?
     */
    public $isDynamic = false;

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
        
        if ($this->url  && strlen($this->url) > 255)
        {
            $this->lastErrorMsg = _ERROR_URL_TOO_LONG;
            return false;
        }
        
        if ($this->action  && strlen($this->action) > 64)
        {
            $this->lastErrorMsg = _ERROR_ACTION_TOO_LONG;
            return false;
        }    
        
        return true;
    }
    
    /**
     * Set all initial values of an object
     * This method is invoked by the constructor
     */
    protected function setDefaultValues()
    {
        parent::setDefaultValues();

        $this->isDynamic = 0;
    }    
    
    /**
     * Returns the parent menu or null if current menu is at root level
     *
     * @return wcmMenu The parent menu (or null if current menu is at root level)
     */
    public function getParentMenu()
    {
        // Use cache
        return wcmProject::getInstance()->layout->getMenuById($this->parentId);
    }

    /**
     * Returns the number of sub-menus in current menu
     *
     * @return int Number of sub-menus in current menu
     */
    public function subMenusCount()
    {
        return count($this->getSubMenus());
    }

    /**
     * Returns an array containing the sub menus of current menu
     *
     * @param boolean $resetCache whether to reset the cache, ie. load from DB (default is false)
     *
     * @return array An assoc array of {@link wcmMenu} objects (keys is the 'rank')
     */
    public function getSubMenus($resetCache = false)
    {
        // Use cache
        $menus = array();
        foreach(wcmProject::getInstance()->layout->getMenus($resetCache) as $menu)
        {
            if  ($menu->parentId == $this->id)
            {
                // Workaround!
                // If two menus has same rank we should increase the rank!
                while(isset($menus[$menu->rank])) $menu->rank++;

                $menus[$menu->rank] = $menu;
            }
        }
        ksort($menus);

        return $menus;
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
        // Force parentId to zero if empty or null or false
        if (!$this->parentId)
            $this->parentId = 0;

        if (!parent::store($userId))
            return false;

        // Update cache
        wcmCache::setElem($this->getClass(), $this->id, $this);

        return true;
    }

    /**
     * Delete menu from database (and associated submenus and permissions)
     *
     * @return True on success, false otherwise
     */
    public function delete()
    {
        // Delete menu
        if (!parent::delete())
            return false;

        // Update cache
        wcmCache::unsetElem($this->getClass(), $this->id);

        // Delete sub-menus
        foreach($this->getSubMenus() as $submenu)
            $submenu->delete();

        return true;
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
                case "wcmMenu":
                    return ($value !== null) ? ("parentId=".$value) : ("parentId is null");
            }
        }

        return $sql;
    }

    /**
     * Re-assign ranks to a list of menu (usually to re-sort sub-menus)
     *
     * @param array $source Assoc array (menuId => menuRank)
     */
    public function sort($source)
    {
        foreach($source as $key => $rank)
        {
            if (substr($key, 0, 5) == "rank_")
            {
                // Retrieve submenu id
                $id   = intval(substr($key, 5));
                $rank = intval($rank);

                $sql  = 'UPDATE ' . $this->tableName . ' SET rank=' . $rank . ' WHERE id='.$id.';';

                if (!$this->database->executeStatement($sql))
                    return false;

                return true;
            }
        }
    }
}