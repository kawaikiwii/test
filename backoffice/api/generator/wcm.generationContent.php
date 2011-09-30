<?php
/**
 * Project:     WCM
 * File:        wcm.generationContent.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * This class represents a content belonging to a {@link wcmGeneration}
 */
class wcmGenerationContent extends wcmSysobject
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
     * Id of generation on which current content belongs
     */
    public $generationId = null;

    /**
     * Id of template to generate
     */
    public $templateId = null;

    /**
     * Loop parameters (or null)
     * => Example: "name=myItem class=story where='workflowState=valid' from=0 to=6"
     */
    public $loop = "";

    /**
     * Generation context
     * => A string representing ids of contextual classes (or null)
     * => Example: "story.id=3, section.id=6"
     */
    public $context = "";

    /**
     * A pattern (template) representing how to name the created file(s)
     * => Example: "Page_{story.title}.html"
     */
    public $namingRule = "";

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
     * Returns the {@link wcmGeneration} associated to this content
     *
     * @return The {@wcmGeneration} object associated to current content
     */
    public function getGeneration()
    {
        return $this->getProject()->generator->getGenerationById($this->generationId);
    }

    /**
     * Returns the {@link wcmTemplate} associated to this content
     *
     * @return The {@link wcmTemplate} associated to this content
     */
    public function getTemplate()
    {
        return new wcmTemplate(null, $this->templateId);
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
                case "wcmGeneration":
                    return ('generationId='.$value);
            }
        }
        return $sql;
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
     * Delete generationContent object from database
     *
     * @return boolean True on success, false otherwise
     */
    public function delete()
    {
        if (!parent::delete())
            return false;

        // Update cache
        wcmCache::unsetElem($this->getClass(), $this->id);

        return true;
    }
}
