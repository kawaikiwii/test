<?php
/**
 * Project:     WCM
 * File:        wcm.template.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * The template class represents a generic PnW template
 * => A template may belongs to a {@link wcmTemplateCategory}
 */
class wcmTemplateCategory extends wcmSysobject
{
    // Old template id
    private $oldId;
    
    /**
     * Category name (Dirname without extension)
     */
    public $name = null;


    /**
     * Parent category (relative file path)
     */
    public $categoryId = null;

    /**
     * Set all initial values of an object
     * This method is invoked by the constructor
     */
    protected function setDefaultValues()
    {
        parent::setDefaultValues();
        $this->oldId = $this->id = null;
    }
 
    /**
     * Computes the target for a permission
     * 
     * @return string the target for a specific permission
     */
    public function getPermissionTarget()
    {
        return $this->getMasterClass()->getPermissionTarget();
    }
 
    /**
     * Load or refresh object content
     *
     * @param int $id optional argument, if not specifed current id is used
     *
     * @return object freshen object or null on failure
     *
     */
    public function refresh($id = null)
    {
        if (!isset($id) && isset($this->id))
        {
            
            $id = $this->id;
        }
        
        if (!empty($id))
        {
            $this->oldId = $id;
            $this->id = $id;

            $parts = explode('/', $this->id);

            $this->name = array_pop($parts);

            $this->categoryId = implode('/', $parts);
            if ($this->categoryId)
                $this->categoryId .= '/';
        }

        return $this;
    }
    
    /**
     * Save and index object (bind, checkValidity, store and index)
     *
     * @param array $source An assoc array for binding to class vars (or null)
     *
     * @return true on success, false otherwise
     */
    public function save($source)
    {
        // Clear last error message
        $this->lastErrorMsg = '';

        // Bind and fix (new) id
        if (!$this->bind($source))   return false;
        $this->id = $this->categoryId . '/' . $this->name;
        $config = wcmConfig::getInstance();
        
        if (!$this->oldId)
        {
            mkdir($config['wcm.templates.path'] . $this->id);
            chmod($config['wcm.templates.path'] . $this->id, 0777); 
        }
        elseif($this->oldId != $this->id)
        {
            rename($this->getOldDirname(), $this->getDirname());
        }

        $this->oldId = $this->id;
        return true;
    }
    /**
     * Returns the template category
     *
     * @return category name
     */
    public function getCategory()
    {
        return $this->categoryId;
    }

    /**
     * Returns the location on disk of the template
     *
     * @return string Location on disk of current template
     */
    public function getOldDirname()
    {
        $config = wcmConfig::getInstance();
        return ($config['wcm.templates.path'] . $this->oldId);
    }
    
    /**
     * Returns the location on disk of the template
     *
     * @return string Location on disk of current template
     */
    public function getDirname()
    {
        $config = wcmConfig::getInstance();
        return ($config['wcm.templates.path'] . $this->id);
    }

    /**
     * Deletes the object from database.
     *
     * @return boolean True on success, false on failure
     *
     */
    public function delete()
    {
        if (!$this->oldId)
            return false;
        
        if (!@rmdir($this->getOldDirname()))
            $this->lastErrorMsg = _CANNOT_DELETE_TEMPLATE_CATEGORY;
        else
            return true;
    }
    
    /**
     * Returns all sub-categories of this category
     *  
     */
    
    public function getSubCategories()
    {
        $subdirs = array();
        $dir = new DirectoryIterator($this->getDirname());
        foreach($dir as $resource) {
            if ($resource->isDir() && $resource->getFilename() != '.' && $resource->getFilename() != '..' && $resource->getFilename() != '.svn')
            {
                $subdirs[] = $resource->getFilename();
            }
        }
        
        return $subdirs;

    }
}
?>
