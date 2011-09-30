<?php
/**
 * Project:     WCM
 * File:        wcm.businesslogic.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * The wcmBizlogic class is an helper class used to manage system classes and business classes
 *
 * Remark: sysClasses and bizClasses are store in cache through static variables
 */
class wcmBizlogic
{
    /**
     * Returns the bizclass corresponding to a specific id
     *
     * @param int $id Id of bizclass to retrieve
     *
     * @return wcmBizclass Corresponding bizclass or NULL
     */
    public function getBizclassById($id)
    {
        
        // Use cache
        foreach($this->getBizclasses() as $bizClass)
        {
            if ($bizClass->id == $id)
                return $bizClass;
        }
        
        return null;
    }

    /**
     * Returns the bizclass corresponding to a specific className
     *
     * @param string $className Classname of bizclass to retrieve
     *
     * @return wcmBizclass Corresponding bizclass or NULL
     */
    public function getBizclassByClassName($className)
    {
        // Use cache
        return getArrayParameter($this->getBizclasses(), $className, null);
    }
    
    /**
     * Returns an array of bizclasses matching a specific where clause
     *
     * @param boolean $resetCache Whether to reset the cache, ie. reload from DB (default is false)
     * 
     * @return An assoc array of {@link wcmBizclass} objects (keys are ids)
     */ 
    public function getBizclasses($resetCache = false)
    {
        $cached = wcmCache::fetch('wcmBizclass');
        if ($resetCache || $cached === FALSE)
        {
            $project = wcmProject::getInstance();

            $enum = new wcmBizclass();
            if (!$enum->beginEnum())
            {
                $project->logger->logError('Bizclass enumeration failed: ' . $enum->getErrorMsg());
                die('FATAL ERROR: Bizclass enumeration failed' . $enum->getErrorMsg());
            }
    
            $cached = array();
            while ($enum->nextEnum())
            {
                $cached[$enum->className] = clone($enum);
            }
            $enum->endEnum();

            // Update cache
            wcmCache::store('wcmBizclass', $cached);
        }
        
        return $cached;
    }

    /**
     * Returns the sysclass corresponding to a specific id
     *
     * @param int $id Id of sysclass to retrieve
     *
     * @return wcmSysclass Corresponding sysclass or NULL
     */
    public function getSysclassById($id)
    {
        // Use cache
        foreach($this->getSysclasses() as $sysClass)
        {
            if ($sysClass->id == $id)
                return $sysClass;
        }

        return null;
    }

    /**
     * Returns the sysclass corresponding to a specific className
     *
     * @param string $className Classname of sysclass to retrieve
     *
     * @return wcmSysclass Corresponding sysclass or NULL
     */
    public function getSysclassByClassName($className)
    {
        // Use cache
        return getArrayParameter($this->getSysclasses(), $className, null);
    }
    
    /**
     * Returns an array of sysclasses matching a specific where clause
     *
     * @return An assoc array of {@link wcmSysclass} objects (keys are ids)
     */ 
    public function getSysclasses($resetCache = false)
    {
        $cached = wcmCache::fetch('wcmSysclass');
        if ($resetCache || $cached === FALSE)
        {
            $project = wcmProject::getInstance();
            $enum = new wcmSysclass();
            if (!$enum->beginEnum())
            {
                $project->logger->logError('Sysclass enumeration failed: ' . $enum->getErrorMsg());
                die('FATAL ERROR: Sysclass enumeration failed' . $enum->getErrorMsg());
            }
            
            $cached = array();
            while ($enum->nextEnum())
            {
                $cached[$enum->className] = clone($enum);
            }
            $enum->endEnum();

            // Update cache
           wcmCache::store('wcmSysclass', $cached);
        }
        
        return $cached;
    }
}