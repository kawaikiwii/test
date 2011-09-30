<?php
/**
 * Project:     WCM
 * File:        wcm.subscription.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * Represents a {@link webuser webuser} subscription.
 * 
 * The subscription class is used to subscribe a webuser to another bizobject of any kind
 * (typically a newsletter bizobject)
 * 
 * The subscription merely represents the begin and ending dates of the subscription as well
 * as the bizobject class and ID that the webuser is subscribed to.
 * 
 * Another use for the subscription bizobject is to allow a user access to an entire class, product,
 * or section of a site (or any protected content) simply by leaving the $subscribedId public property
 * blank and fill the $subscribedClass with the corresponding value.
 * 
 * @see {@link webuser webuser}
 * @see {@link newsletter newsletter}
 */
class subscription extends wcmObject
{
    /**
     * The webuser id
     * 
     * @var int
     */
    public $webuserId;
    
    /**
     * Start date
     * 
     * @var string
     */
    public $subscriptionStart;

    /**
     * End date
     * 
     * @var string
     */
    public $subscriptionEnd;

    /**
     * Kind of product/service subscribed (e.g. 'newsletter')
     * => the class name of the bizobject
     * 
     * @var string
     */
    public $subscribedClass;
    
    /**
     * Identifier of the product/service subscribed
     * => the ID of the bizobject
     * 
     * @var int
     */
    public $subscribedId;

    /**
     * Returns the database used to store object
     *
     * @return wcmDatabase Database used for storage
     */
    protected function getDatabase()
    {
        if (!$this->database)
        {
            // use same DB as webuser
            $bc = wcmProject::getInstance()->bizlogic->getBizClassByClassName('webuser');
            $this->database = $bc->getConnector()->getBusinessDatabase();
            $this->tableName = '#__subscription';
        }
        
        return $this->database;
    }

    /**
     * Computes the sql where clause matching foreign constraints
     * => This method must be overloaded by child class
     *
     * @param string $of Assoc Array with foreign constrains (key=className, value=id)
     *
     * @return string Sql where clause matching "of" constraints or null
     */
    protected function ofClause($of)
    {
        if ($of == null || !is_array($of))
            return;

        $sql = null;
        foreach($of as $key => $value)
        {
            switch($key)
            {
                case "webuser":
                    if ($sql != null) $sql .= " AND ";
                    $sql .= "webuserId=".$value;
                    break;

                default:
                    // Assume it's the source!
                    if ($sql != null) $sql .= " AND ";
                    $sql .= "subscribedClass='".$key."' AND subscribedId=".$value;
                    break;
            }
        }
        return $sql;
    }

    /**
     * Exposes 'source' to the getAssocArray
     *
     * @param boolean $toXML TRUE if called by toXML() method
     *
     * @return array The source's assocarray or null
     */
    public function getAssoc_Source($toXML = false)
    {
        if ($toXML) return null;
        
        $bo = $this->getSource();
        return ($bo->id) ? $bo->getAssocArray($toXML) : null;
    }
    
    /**
     * Exposes 'webuser' to the getAssocArray
     *
     * @param boolean $toXML TRUE if called by toXML() method
     *
     * @return array The webuser's assocarray or null
     */
    public function getAssoc_Webuser($toXML = false)
    {
        if ($toXML) return null;
        
        $bo = $this->getWebuser();
        return ($bo->id) ? $bo->getAssocArray($toXML) : null;
    }

    /**
     * Returns the source of subscription (like a newsletter for instance)
     *
     * @return bizobject The subscription's source
     */
    public function getSource()
    {
        $className = $this->subscribedClass;
        return new $className(null, $this->subscribedId);
    }
    
    /**
     * Returns the webuser who has subscribed to this subscription
     *
     * @return webuser The subscriber (a webuser)
     */
    public function getWebuser()
    {
        return new webuser(null, $this->webuserId);
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

        if ($this->subscriptionStart >  $this->subscriptionEnd)
        {
            $this->lastErrorMsg = _BIZ_ERROR_BEG_END_DATE_FORMAT;
            return false;
        }
        return true;
    }
}