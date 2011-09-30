<?php 
/**
 * Project:     WCM
 * File:        biz.content.php
 *
 * @copyright   (c)2008 Relaxnews
 * @version     4.x
 *
 */
 /**
 * Definition of an content object
 */

class content extends wcmObject {
    /**
     * (int) Referent Id
     */
     
    public $referentId;
    
    /**
     * (string) Referent Class
     */
     
    public $referentClass;
    
    /**
     * (string) format
     */
    public $format;
    
    /**
     * (string) Provider Name Id
     */
     
    public $provider;
    
    /**
     * (int) Author Id
     */
    public $authorId;
    
    /**
     * (string) Title
     */
    public $title;
    
    /**
     * (string) Title's Content Type (xHTML, text, ...)
     */
    public $titleContentType;
    
    /**
     * (int) Title's signs count
     */
    public $titleSigns;
    
    /**
     * (int) Title's words count
     */
    public $titleWords;
    
    /**
     * (string) Description
     */
    public $description;
    
    /**
     * (string) Description's Content Type (xHTML, text, ...)
     */
    public $descriptionContentType;
    
    /**
     * (int) Description's signs count
     */
    public $descriptionSigns;
    
    /**
     * (int) Description's words count
     */
    public $descriptionWords;
    
    /**
     * (string) Title
     */
    public $text;
    
    /**
     * (string) Title's Content Type (xHTML, text, ...)
     */
    public $textContentType;
    
    /**
     * (int) Title's signs count
     */
    public $textSigns;
    
    /**
     * (int) Title's words count
     */
    public $textWords;
    
    /**
     * Set all initial values of an object
     * This method is invoked by the constructor
     */

    protected function setDefaultValues() {
        parent::setDefaultValues();
        $this->format = "default";
        $this->titleContentType = "text";
        $this->descriptionContentType = "xhtml";
        $this->textContentType = "xhtml";
    }
    
    /**
     * Returns the database used to store object
     *
     * @return wcmDatabase Database used for storage
     */

    protected function getDatabase() {
        if (!$this->database) {
            // use same DB as article
            $this->database = wcmProject::getInstance()->bizlogic->getBizClassByClassName('news')->getConnector()->getBusinessDatabase();
            $this->tableName = '#__content';
        }
    }
    /**
     * Returns the referent associated to this content
     *
     * @return content The content's referent (or null if orphan)
     */

    function getReferent() {
        $referent = new $this->referentClass(null, $this->referentId);
        return ($referent->id) ? $referent : null;
    }
    
    /**
     * use referentClass, referentId and format to find specific content
     *
     * @return content object
     */

    function getContentByFormat($referentClass, $referentId, $format) {
        $sql = 'SELECT id FROM '.$this->getTableName().' WHERE referentClass=? AND referentId=? AND format=?';
        $id = $this->database->executeScalar($sql, array($referentClass, $referentId, $format));
        if (! empty($id))
            return $this->refresh($id);
        else
            return false;
    }

  
    
    /**
     * Save object in database and update search table
     *
     * @param array $source array for binding to class vars (or null)
     *
     * @return true on success, false otherwise
     *
     */

    public function save($source = null) 
    {  
    	// remove html comment from description
        if (isset($source['description']))
        {
            $source['description'] = ereg_replace("<!-.*->", "", $source['description']);
//            $source['description'] = str_replace("<p>&nbsp;</p>", "", $source['description']);
        }
        // remove html comment from text
        if (isset($source['text']))
        {
            $source['text'] = ereg_replace("<!-.*->", "", $source['text']);
//            $source['text'] = str_replace("<p>&nbsp;</p>", "", $source['text']);
       }

        
        if (isset($source['title']))  		$source['title'] = bizobject::cleanStringFromSpecialChar($source['title']);  
        if (isset($source['description']))  $source['description'] = bizobject::cleanStringFromSpecialChar($source['description']);  
        if (isset($source['text'])) 		$source['text'] = bizobject::cleanStringFromSpecialChar($source['text']);  
            
        // Save
        if (!parent::save($source))
            return false;
            
        // Reindex the parent bizobject (article)
        return wcmBizsearch::getInstance()->indexBizobject($this->getReferent());
    }
    
    /**
     * Deletes object from database
     *
     * @param bool $indexParent TRUE to re-index parent (true by default)
     *
     * @return true on success or an error message (string)
     */

    public function delete($indexParent = true) {
        if (!$indexParent)
            return parent::delete();
            
        // Delete bizobject
        if (!parent::delete())
            return false;
            
        // Reindex the parent bizobject (article)
        return wcmBizsearch::getInstance()->indexBizobject($this->getReferent());
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
            
        if ($this->title && strlen($this->title) > 255) {
            $this->lastErrorMsg = _BIZ_ERROR_CHAPTER_TITLE_TOO_LONG;
            return false;
        }
        
        return true;
    }
    
}
