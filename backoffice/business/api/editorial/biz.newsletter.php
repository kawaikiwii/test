<?php

/**
 * Project:     WCM
 * File:        biz.newsletter.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/*
 * The newsletter object is a basic business object used to
 * manage newsletter. Each newsletter has a list of subscribers
 * (some webuser bizobjects) and have associated a text and an HTML
 * template
 */
class newsletter extends bizobject
{
   /**
    * (int) site id
    */
    public $siteId;

    /**
     * (string) unique code
     */
    public $code;

    /**
     * (string) newsletter title
     */
    public $title;
    
    /**
     * (string) newsletter description
     */
    public $description;
    
    /**
     * (string) human readable sender's name
     */
    public $sender;

    /**
     * (string) newsletter sender's email address
     */
    public $from;
    
    /**
     * (string) newsletter replyTo email address
     */
    public $replyTo;

    /**
     * (string) Id of template used for the HTML newsletter
     */
    public $htmlTemplate;

    /**
     * (string) Id of template used for the TEXT newsletter
     */
    public $textTemplate;

    /**
     * Refresh object from unique code
     *
     * @param string $code Code used to refresh newsletter
     *
     * @return newsletter The newsletter object
     */
    public function refreshByCode($code)
    {
        $sql = 'SELECT id FROM ' . $this->tableName . ' WHERE code=?';
        $id = $this->database->executeScalar($sql, array($code));
        return $this->refresh($id);
    }
    

    /**
     * Gets the subscriptions to the newsletter.
     *
     * @param boolean $toXML TRUE if called by toXML() method
     *
     * @return array An array of the newsletter's subscriptions (as subscription objects)
     */
    public function getAssoc_subscriptions($toXML = false)
    {
        if ($toXML) return null;
        
        $return = array();
        $subscriptions = $this->getSubscriptions();
        if ($subscriptions)
        {
            foreach($subscriptions as $subscription)
            {
                $return[] = $subscription->getAssocArray($toXML);
            }
        }
        return $return;
    }
    

    /**
     * Gets the subscriptions to the newsletter.
     *
     * @return array An array of the newsletter's subscriptions (as subscription objects)
     */
    public function getSubscriptions()
    {
        $where  = "subscribedId='".$this->id."'";
        $where .= " AND subscribedClass='".$this->getClass()."'";

        return bizobject::getBizobjects("subscription", $where);
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

        if (trim($this->code . ' ') == '')
        {
            $this->lastErrorMsg = _BIZ_ERROR_CODE_IS_MANDATORY;
            return false;
        }

        if (strlen($this->code) > 255)
        {
            $this->lastErrorMsg = _BIZ_ERROR_CODE_TOO_LONG;
            return false;
        }

        if (trim($this->title . ' ') == '')
        {
            $this->lastErrorMsg = _BIZ_ERROR_TITLE_IS_MANDATORY;
            return false;
        }

        if (strlen($this->title) > 255)
        {
            $this->lastErrorMsg = _BIZ_ERROR_TITLE_TOO_LONG;
            return false;
        }
        
        if ($this->sender && strlen($this->sender) > 128)
        {
            $this->lastErrorMsg = _BIZ_ERROR_SENDER_TOO_LONG;
            return false;
        }
        
        if ($this->from && strlen($this->from) > 128)
        {
            $this->lastErrorMsg = _BIZ_ERROR_FROM_TOO_LONG;
            return false;
        }

        if ($this->replyTo && strlen($this->replyTo) > 128)
        {
            $this->lastErrorMsg = _BIZ_ERROR_REPLY_TO_TOO_LONG;
            return false;
        }
        return true;
    }
}