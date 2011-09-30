<?php
/**
 * Project:     WCM
 * File:        biz.echoSlideshow.php
 *
 * @copyright   (c)2010 Relaxnews
 * @version     4.x
 *
 */

class echoSlideshow extends wcmObject
{
    /**
     * (int) Article Id
     */
    public $cityId;

    /**
     * (int) Chapter rank (in siblings)
     */
    public $rank = 1;

    /**
     * Chapter title
     */
    public $kind;
  
    public $title;  	
	public $credits;
	public $file; 

    /**
     * Returns the database used to store object
     *
     * @return wcmDatabase Database used for storage
     */
    protected function getDatabase()
    {
        if (!$this->database)
        {
            // use same DB as echoCity
            $this->database = wcmProject::getInstance()->bizlogic->getBizClassByClassName('echoCity')->getConnector()->getBusinessDatabase();
            $this->tableName = '#__echoSlideshow';
        }
    }

    /**
     * Computes the sql where clause matching foreign constraints
     * => This method must be overloaded by child class
     *
     * @param string $of Assoc Array with foreign constrains (key=className, value=id)
     *
     * @return string Sql where clause matching "of" constraints or null
     */
    function ofClause($of)
    {
        if ($of == null || !is_array($of))
            return;

        $sql = null;

        foreach($of as $key => $value)
        {
            switch($key)
            {
                case "echoCity":
                    if ($sql != null) $sql .= " AND ";
                    $sql .= "cityId=".$value;
                    break;

                default:
                    // Invalid relation!!
                    if ($sql != null) $sql .= " AND ";
                    $sql .= "0=1";
                    break;
            }
        }
        return $sql;
    }

    /**
     * Exposes 'echoCity' to the getAssocArray
     *
     * @param bool $toXML TRUE if method is called in the context of toXML()
     *
     * @return array 
     */
    function getAssoc_echoCity($toXML = false)
    {
        if ($toXML) return null;

        $echoCity = $this->getEchoCity();
        return ($echoCity) ? $echoCity->getAssocArray($toXML) : null;
    }

    /**
     * Returns the echoCity associated 
     *
     * @return echoCity 
     */
    function getEchoCity()
    {
        $echoCity = new echoCity(null, $this->cityId);
        return ($echoCity->id) ? $echoCity : null;
    }

    /**
     * Save object in database and update search table
     *
     * @param array $source array for binding to class vars (or null)
     * @param int   $userId id of user who create ou update the document
     *
     * @return true on success, false otherwise
     *
     */
    public function save($source = null)
    {
        // Save
        if (!parent::save($source))
    	{
        	wcmTrace("LES ECHOS : erreur sauvegarde ".$this->getClass());
        	return false;
        }

        // Reindex the parent bizobject (article)
        return wcmBizsearch::getInstance()->indexBizobject($this->getEchoCity());
    }

 	/**
     * Deletes object from database
     *
     * @param bool $indexParent TRUE to re-index parent (true by default)
     *
     * @return true on success or an error message (string)
     */
    public function delete($indexParent = true)
    {
        if(!$indexParent)
            return parent::delete();

        // Delete bizobject
        if (!parent::delete())
            return false;

        // Reindex the parent bizobject (article)
        return wcmBizsearch::getInstance()->indexBizobject($this->getEchoCity());
    }

    /**
     * Check validity of object
     *
     * A generic method which can (should ?) be overloaded by the child class
     *
     * @return boolean true when object is valid
     *
     */
    public function checkValidity()
    {
        //if (!parent::checkValidity())
        //    return false;

        return true;
    }
    
    /**
	 * List kind available for an inserts
	 *
	 *
	 * @return array
	 *
     */
    public static function getKind()
    {
    	return array('' => '',
    				 '' => ''
    	);
    }
}