<?php

/**
 * Project:     WCM
 * File:        wcm.roleCategory.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
 
/*
 * The wcmRoleCategory class represents a role category.
 * Each category can contains one or many wcmRole objects
 */
class wcmRoleCategory extends wcmSysobject
{
    /**
     * (string) Category name
     */
    public $name;
 
   
    /*
     * Check if there is role in this category
     *
     * @return bool TRUE if there is no roles in this category
     */
    public function isEmpty()
    {
        return (1 >= $this->getProject()->database->executeScalar('SELECT COUNT(*) FROM #__role WHERE id=' . $this->id, $params));
    }

    /**
     * Returns an array of roles belonging to current category
     *
     * @return array An associative array of wcmRoles (keys are roles ids) or nll on error
     */
    function getRoles()
    {
        $where = 'categoryId='. $this->id;
        $enum = new wcmRole($this->getProject());
        if (!$enum->beginEnum($where, 'name'))
            return null;

        $result = array();
        while ($enum->nextEnum())
        {
            $result[$enum->id] = clone($enum);
        }
        $enum->endEnum();
        
        return $result;
    }
}
?>