<?php
/**
 * The Generationset class represents a PnW generation set
 * A generation set will contain N generations that can be secured and launched together
 */
class wcmGenerationSet extends wcmSysobject
{
    /**
     * Object name
     */
    public $name = null;
    
    /**
     * GenerationSet code
     */
    public $code = "";
    
    /**
     * Path (relative or absolute) where to generation contents
     */
    public $location = "";

    /**
     * Generation set context
     * => A string representing ids of contextual classes (or null)
     * => Example: "story.id=3, section.id=6"
     */
    public $context = "";
   
    /**
     * Refresh object using his code
     *
     * @param String $code optional argument, if not specifed current id is used
     *
     * @return object freshen object or null on failure
     *
     */
    public function refreshByCode($code)
    {
        $sql = 'SELECT id FROM '.$this->getTableName().' WHERE code=?';
        $id = $this->database->executeScalar($sql, array($code));
        return $this->refresh($id); 
    }
    
    /**
     * Returns an array of generations belonging to current generation set
     *
     * @return An associative array of {@link wcmGeneration} objects (keys are ids)
     */
    public function getGenerations($resetCache = false)
    {
        // Use cache
        $generations = array();
        foreach($this->getProject()->generator->getGenerations($resetCache) as $generation)
        {
            if ($generation->generationSetId == $this->id)
            {
                $generations[$generation->id] = $generation;
            }
        }

        return $generations;
    }

    /**
     * Inserts or Updates object in database
     *
     * @return boolean true on success, false on failure
     */
    protected function store()
    {
        if (!$this->checkUniqueCode($this->code))
            return false;
        
        if (!parent::store())
            return false;

        // @todo : check if this is the correct place for the permissions update
            // delete permissions for this secure object
            $permissionType = $this->getClass();
            $sql = "DELETE FROM #__permission WHERE target like '%" . $permissionType . "%'";
            $params = array($permissionType);
    
             if (wcmProject::getInstance()->database->executeStatement($sql, $params) == -1)
             {
                 $this->lastErrorMsg = $permissionType . '::DELETE failed : ' . wcmProject::getInstance()->database->getErrorMsg();
                 return false;
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

                        // set permissions
                        $this->setGroupPermission($groupId, $finalMask);
                    }
                }
            }
        // @todo : remove to here if this is wrong 

        // Update cache
        wcmCache::setElem($this->getClass(), $this->id, $this);
        return true;
    }

    /**
     * Delete generationSet object from database (and associated generations and generation contents)
     *
     * @return boolean True on success, false otherwise
     */
    public function delete()
    {
        if (!parent::delete())
            return false;

        // Update cache
        wcmCache::unsetElem($this->getClass(), $this->id);

        // delete generations
        foreach ($this->getGenerations() as $generation)
        {
            $generation->delete();
        }

        return true;
    }

}