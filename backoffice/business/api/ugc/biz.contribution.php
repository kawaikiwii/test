<?php
/**
 * Project:     WCM
 * File:        biz.contribution.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * This class represents a contribution
 * 
 * A contribution is user generated content related to another object 
 * such as an article or a forum. Every contribution will have a referent 
 * class and a referent id to keep track of the contributed bizobject.
 * 
 * Contributions may also have a parent id. This is used when 
 * the contribution is an answer to another contribution. In other words, 
 * a contribution will never have another contribution as a referent, but 
 * may have a parent that will always be another contribution.
 * 
 * By default, contributions can be added to articles, photos, newsitems, 
 * forums and other contributions. This behavior can be extended.
 * 
 * Contributions also have a workflow state (valid, approved or suspicious). 
 * Depending on specific implementations, contributions may need to be 
 * approved before showing up on a web site. Again, depending on implementations,
 * a contribution may be suspicious if, for example, it contains words in a black list.
 * 
 * A contribution may be enriched with TME.
 */
class contribution extends bizobject
{
    /**
     * Site ID
     * 
     * @var int
     */
    public $siteId;

    /**
     * Channel ID
     * 
     * @var int
     */
    public $channelId;

    /**
     * The class of the contributed bizobject
     * 
     * @var string
     */
    public $referentClass;

    /**
     * The id of the contributed bizobject
     * 
     * @var int
     */
    public $referentId;
    
    /**
     * The id of the parent contribution (or null for root contributions)
     * 
     * @var int
     */
    public $parentId;
    
    /**
     * Title
     * 
     * @var string
     */
    public $title;
    
    /**
     * Text
     * 
     * @var string
     */
    public $text;
    
    /**
     * Nickname of the contribution
     * 
     * @var string
     */
    public $nickname;
    
    /**
     * Email address of contributer
     *
     * @var string
     */
    public $email;

    /**
     * WebuserId of the contributor (if known, or null)
     * 
     * @var int
     */
    public $webuserId = null;

    /**
     * Set all initial values of an object
     * 
     * This method is invoked by the constructor
     */
    protected function setDefaultValues()
    {
        parent::setDefaultValues();

        $this->siteId = $this->channelId = 0;
        $this->title = '';
    }

    /**
     * Exposes the 'referent' object of the contribution
     *
     * @param boolean $toXML True when this method is invoked from toXML()
     * 
     * @return array The reference get assoc array
     */
    public function getAssoc_referent($toXML = false)
    {
        if ($toXML) return null;
        
        $referent = $this->getReferent();
        if (!$referent) return null;
        
        return $referent->getAssocArray();
    }
    
    /**
    * Object to which the contribution refers
    *
    * @return object The object to which the contribution refers
    */
    public function getReferent()
    {
    	if($this->referentClass)
    	{
    		$referent = new $this->referentClass;
        	$referent->refresh($this->referentId);
        	return ($referent->id) ? $referent : null;
    	}
     	return null;   
    }

    /**
    * Parent of the contribution
    *
    * @return contribution The parent contribution
    *
    */
    function getParent()
    {
        return new contribution(null, $this->parentId);
    }

    /**
     * Computes the sql where clause matching foreign constraints
     * 
     * This method must be overloaded by child class
     *
     * @param  string $of Assoc Array with foreign constrains (key=className, value=id)
     *
     * @return string Sql Where clause matching "of" constraints or null
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
                case "site":
                    if ($sql != null) $sql .= " AND ";
                    $sql .= "siteId=".$value;
                    break;

                case "channel":
                    if ($sql != null) $sql .= " AND ";
                    $sql .= "channelId=".$value;
                    break;

                case "contribution":
                    if ($sql != null) $sql .= " AND ";
                    $sql .= "parentId=".$value;
                    break;

                // Other classes are considered as possible referents
                default:
                    if ($sql != null) $sql .= " AND ";
                    $sql .= "referentClass='" . $key . "' AND referentId=".$value;
                    break;

            }
        }
        return $sql;
    }

    /**
     * Gets the 'semantic' text that will be passed to the Text-Mining Engine
     *
     * @return string The semantic text to mine
     */
    public function getSemanticText()
    {
        $content = '';

        if ($this->text)
            $content .= trim($this->text, " \t\n\r\0\x0B.") . ".\n";
        if ($this->title)
            $content .= trim($this->title, " \t\n\r\0\x0B.") . ".\n";

        return $content;
    }

    /**
     * Returns the comments of the bizobject
     *
     * @return array An array containing all comments at the root level
     */
    public function getComments()
    {
        $where = "parentId=" . $this->id;
        return bizobject::getBizobjects('contribution', $where);
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
        if (!parent::checkValidity())
            return false;

        if ($this->title && strlen($this->title) > 255)
        {
            $this->lastErrorMsg = _BIZ_ERROR_TITLE_TOO_LONG;
            return false;
        }
        
        if ($this->nickname && strlen($this->nickname) > 255)
        {
            $this->lastErrorMsg = _BIZ_ERROR_NICKNAME_TOO_LONG;
            return false;
        }

        return true;
    }
}