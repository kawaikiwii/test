<?php
/**
 * Project:     WCM
 * File:        wcm.generation.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * The Generation class represents a generic PnW generation
 * A generation contains one or more {@link wcmGenerationContent} objects
 */
class wcmGeneration extends wcmSysobject
{
    /**
     * Object name
     */
    public $name = null;

    /**
     * Object code
     */
    public $code = "";

    /**
     * Path (relative or absolute) where to generation contents
     */
    public $location = "";

    /**
     * Generation context
     * => A string representing ids of contextual classes (or null)
     * => Example: "story.id=3, section.id=6"
     */
    public $context = "";

    /**
     * Id of generation set this generation belongs to
     *
     * @var integer
     */
    public $generationSetId;

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
     * Returns an array of contents belonging to current generation
     *
     * @return An associative array of {@link wcmGenerationContent} objects (keys are ids)
     */
    public function getContents()
    {
        // Use cache
        $contents = array();
        foreach($this->getProject()->generator->getGenerationContents() as $content)
        {
            if ($content->generationId == $this->id)
            {
                $contents[$content->id] = $content;
            }
        }

        return $contents;
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
        if (!$this->checkUniqueCode($this->code))
            return false;
        
        if (!parent::store($userId))
            return false;

        // Update cache
        wcmCache::setElem($this->getClass(), $this->id, $this);

        return true;
    }

    /**
     * Delete generation object from database (and associated generation contents)
     *
     * @return boolean True on success, false otherwise
     */
    public function delete()
    {
        if (!parent::delete())
            return false;

        // Update cache
        wcmCache::unsetElem($this->getClass(), $this->id);

        // Delete generation contents
        foreach($this->getContents() as $content)
        {
            $content->delete();
        }

        return true;
    }
}
?>
