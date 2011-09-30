<?php 
/**
 * Project:     WCM
 * File:        biz.site.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
 
 /**
 * Site object
 * This is the basic site object
 *
 */

class site extends bizobject {
    /**
     * Site title
     */
    public $title;
    
    /**
     * Site description
     */
    public $description;
    
    /**
     * Site language (ISO two-letters code)
     */
    public $language;
    
    /**
     * Site code
     */
    public $code;
    
    /**
     * Site url
     */
    public $url;
    
    /**
     * Liste des services autorisés pour un univers donné
     */
    public $services;
    
    /**
     * Site partnerFeeds
     */
    public $partnerFeeds;
    
    /**
     * Set all initial values of an object
     * This method is invoked by the constructor
     */

    protected function setDefaultValues() {
        parent::setDefaultValues();
        
        $config = wcmConfig::getInstance();
        $this->language = $config['wcm.default.language'];
    }
    
    /**
     * Deletes the object from database.
     *
     * @return boolean True on success, false on failure
     *
     */

    public function delete() {
        if ($this->id != $_SESSION['siteId']) {
            parent::delete();
            return true;
        } else {
            $this->lastErrorMsg = _BIZ_SITE_CANT_DELETE_CURRENT;
            return false;
        }
    }
    
    /**
     * Returns an array containing all the bizchannel of the site
     *
     * @return array
     */

    public function getChannels($project) {
        return bizobject::getBizobjects("channel", "siteId = '".$this->id."'", null);
    }
    
    /**
     * List (assoc array) of languages
     */

    public function getLanguageList() {
        return array("fr"=>_BIZ_FRENCH, "en"=>_BIZ_ENGLISH);
    }
    
    /**
     * List of existing site in array form
     */

    public function getArrayLangSites($where = '', $orderBy = 'id') {
        $className = $this->getClass();
        $enum = new $className();
        $siteArray = array();
        
        if (!$enum->beginEnum($where, $orderBy))
            return null;
            
        while ($enum->nextEnum())
            $siteArray[$enum->id] = $enum->language;
            
        $enum->endEnum();
        
        return $siteArray;
    }
    
    /**
     * array with site code and site id
     */

    public function getArrayCodesSites($where = '', $orderBy = 'id') {
        $className = $this->getClass();
        $enum = new $className();
        $siteArray = array();
        
        if (!$enum->beginEnum($where, $orderBy))
            return null;
            
        while ($enum->nextEnum())
            $siteArray[$enum->code] = $enum->id;
            
        $enum->endEnum();
        
        return $siteArray;
    }
    /**
     * array with site name and site id
     */

    public function getArrayNamesSites($where = '', $orderBy = 'id') {
        $className = $this->getClass();
        $enum = new $className();
        $siteArray = array();
        
        if (!$enum->beginEnum($where, $orderBy))
            return null;
            
        while ($enum->nextEnum())
            $siteArray[$enum->title] = $enum->id;
            
        $enum->endEnum();
        
        return $siteArray;
    }
    
    /**
     * Get code from id
     */

    public function getCodeFromId($id) {
        $this->refresh($id);
        return $this->code;
    }
    
    /**
     * Gets object ready to store by getting modified date, creation date etc
     * Will execute transition.
     */

    protected function store() {
        if (!parent::store())
            return false;
        if (isset($this->serialStorage['permissionTypes'])) {
            // Erase permissions for the sysclass and all its related objects
            $project = wcmProject::getInstance();
            $sql = 'DELETE FROM #__permission WHERE target=?';
            $params = array($this->getPermissionTarget());
            $project->database->executeStatement($sql, $params);
            if ($this->getClass()) {
                $sql = "DELETE FROM #__permission WHERE target LIKE '".$this->getClass()."_".$this->id."%'";
                $project->database->executeStatement($sql);
            }
            
            // insert the new permissions
            $newPermissions = getArrayParameter($this->serialStorage, 'permissions');
            if ($newPermissions) {
                $permsArray = array();
                foreach ($newPermissions as $permission) {
                    // get values
                    $perms = explode('*', $permission, 2);
                    $permsArray[$perms[0]][] = $perms[1];
                }
                
                foreach ($permsArray as $wcmObject=>$permissionMask) {
                    $securedObject = explode('_', $wcmObject);
                    $groupId = $securedObject[0];
                    
                    $finalMask = 0;
                    foreach ($permissionMask as $mask) {
                        // get group and values
                        $values = explode('#', $mask);
                        $finalMask += $values[0];
                        
                        // when a permission is set (WRITE, EXECUTE, DELETE)
                        // permission P_READ is always available
                        if ($finalMask)
                            $finalMask |= wcmPermission::P_READ;
                            
                        // when a permission is none keep only that one!
                        if ($finalMask & wcmPermission::P_NONE)
                            $finalMask = wcmPermission::P_NONE;
                            
                        // set permissions
                        $this->setGroupPermission($groupId, $finalMask);
                    }
                }
            }
        }
        
        return true;
    }
    
    /**
     * Check validity of object
     *
     * A generic method which can (should ?) be overloaded by the child class
     *
     * @return boolean true when object is valid
     *
     */

    public function checkValidity() {
        if (!parent::checkValidity())
            return false;
            
        if (trim(' '.$this->title) == '') {
            $this->lastErrorMsg = _BIZ_ERROR_TITLE_IS_MANDATORY;
            return false;
        }
        
        if (strlen($this->title) > 255) {
            $this->lastErrorMsg = _BIZ_ERROR_TITLE_TOO_LONG;
            return false;
        }
        
        return true;
    }
    
    /**
     * Refresh object using his code
     *
     * @param String $code optional argument, if not specifed current id is used
     *
     * @return object freshen object or null on failure
     *
     */

    public function refreshByCode($code) {
        $sql = 'SELECT id FROM '.$this->getTableName().' WHERE code=?';
        $id = $this->database->executeScalar($sql, array($code));
        return $this->refresh($id);
    }

    //$children permet de prendre en compte ou pas les sous-catégories
    public function isAllowed($children = "") {
    
        $permissions = $this->getAccountPermissions($children);
        
        if (!array_key_exists($this->id, $permissions)) {
            return (false);
        }
        return (true);
        
    }
    
}
