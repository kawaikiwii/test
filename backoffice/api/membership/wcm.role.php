<?php

/**
 * Project:     WCM
 * File:        wcm.role.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
 
/*
 * The wcmRole class represents a generic PnW wcmRole
 *
 */
class wcmRole extends wcmSysobject
{
    /**
     * (string) Role name
     */
    public $name;
    
    /**
     * (string) Unique role code
     */
    public $code;
    
    /**
     * (int) Role category id
     */     
    public $categoryId;
   
     /**
     * Refresh role from code
     *
     * @param string $roleCode string Role code to be refresh
     */
    public function refreshFromCode($roleCode)
    {
        $query = "SELECT id FROM #__role WHERE code='".$roleCode."'";
        $this->_project->_database->setQuery($query);
        $id = $this->_project->_database->loadResult();
        $this->refresh($id);
    }

    /**
     * Returns the category associated to current role
     *
     * @return wcmRoleCategory The category associated to current role
     */
    public function getCategory()
    {
        $category = new wcmRoleCategory($this->getProject());
        return $category->refresh($this->categoryId);
    }
}
?>