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
class wcmTemplate extends wcmSysobject 
{
    // Old template id
    private $oldId;
    
    /**
     * Template name (filename without extension)
     */
    public $name = null;

    /**
     * Template content
     */
    public $content = "";

    /**
     * Template category (relative file path)
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
    
    public function isObsolete()
    {
        return false;
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

    public function __construct($project = null, $id = null)
    {
        // Set default values
        $this->setDefaultValues();

        // Refresh object?
        if ($id) $this->refresh($id);
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
            $this->name = substr($this->name, 0, strlen($this->name)-4);
        
            $this->categoryId = implode('/', $parts);
            if ($this->categoryId)
                $this->categoryId .= '/';
                
            $this->content = $this->getFileContent();
            $this->modifiedAt = date("Y-m-d, H:i:s", $this->getFileModifiedAt());
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
        
        $category = (substr($this->categoryId, -1) != '/') ? $this->categoryId . '/' : $this->categoryId;
        $category = (substr($category, 0, 1) == '/') ? (substr($category, 1)) : $category;
        $name = (substr($this->name, -4, 4) != '.tpl') ? $this->name . ".tpl" : $this->name;
        $this->id = $category . $name;
        
        //Remove previous file?
        if($this->oldId != $this->id)
        {
            // Delete old file
            $this->delete();
        }

        $this->setFileContent();
        $this->index();

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
    public function getOldFilename()
    {
        $config = wcmConfig::getInstance();
        return ($config['wcm.templates.path'] . $this->oldId);
    }
    
    /**
     * Returns the location on disk of the template
     *
     * @return string Location on disk of current template
     */
    public function getFilename()
    {
		if(isAbsolutePath($this->id))
		{
				return $this->id;
		}
		else
		{
	        $config = wcmConfig::getInstance();
    	   return ($config['wcm.templates.path'] . $this->id);
		}
    }

    /**
     * Returns the content on disk of the template
     *
     * @return string Template content saved on disk or null
     */
    private function getFileContent()
    {
        $filename = $this->getFilename();
        
        $content = null;
        readFromFile($filename, $content);
        return $content;
    }

    /**
     * Save template on disk
     */
    private function setFileContent()
    {
        saveToFile($this->getFileName(), $this->content);
    }
    
    /**
     * Returns the last date of modification of the template file
     *
     * @return date The last date of modification of the template file or null if not found
     */
    private function getFileModifiedAt()
    {
        $filename = $this->getFilename();
        if (!is_file($filename))
            return null;

        return filemtime($this->getFileName());
    }

    /**
     * Deletes the object from database.
     *
     * @return boolean True on success, false on failure
     *
     */
    public function delete()
    {
        if (!$this->hasLock())
        {
            $this->lastErrorMsg = $this->getClass() . '::delete failed : object ' . $this->id . ' is locked';
            return false;
        }
        
        if (!$this->oldId)
            return false;
        
        @removeFile($this->getOldFilename());
        return true;
    }
}
?>
