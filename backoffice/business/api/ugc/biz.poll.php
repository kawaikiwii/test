<?php
/**
 * Project:     WCM
 * File:        biz.poll.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * This class represents a poll.
 * 
 * A poll is generally a collection of questions, each question represented by instances of
 * {@link pollChoice pollChoice} objects. In order to use the poll, at least one pollChoice
 * must be available.
 * 
 * A poll may be enriched with TME
 * @see pollChoice
 */
class poll extends bizobject
{
   /**
    * Site id
    * 
    * @var int
    */
    public $siteId;

   /**
    * Channel id
    * 
    * @var int
    */
    public $channelId;

    /**
     * Title
     * 
     * @var string
     */
    public $title;
    
    /**
     * Kind of poll
     * 
     * @var string
     */
    public $kind;

    /**
     * Free HTML text
     * 
     * @var string
     */
    public $text;

    /**
     * Publication date
     * 
     * @var string
     */
    public $publicationDate = null;

    /**
     * Expiration date
     * 
     * @var string
     */
    public $expirationDate = null;

    /**
     * Set all initial values of an object
     * This method is invoked by the constructor
     */
    protected function setDefaultValues()
    {
        parent::setDefaultValues();

        $this->publicationDate = date('Y-m-d');
        $this->kind = 'sc';
    }

    /**
    * Returns an array of possible poll's kind  (code => literal description)
    *
    * @return array Array of possible kind
    */
    static function getKindList()
    {
        return array(
            "sc" => _BIZ_SINGLE_CHOICE,
            "mp" => _BIZ_MULTIPLE_CHOICE,
        );
    }

    public function getAssoc_kinds($toXML = false)
    {
        return $this->getKindList();
    }    
    
    
    /**
     * Exposes 'choices' in the getAssocArray()
     *
     * @param bool $toXML TRUE if method is called in the context of toXML()
     *
     * @return array An array of choices getAssocArray()
     */
    public function getAssoc_choices($toXML = false)
    {
        $choices = array();
        foreach($this->getPollChoices() as $choice)
        {
            $choices[] = $choice->getAssocArray($toXML);
        }
        
        return $choices;
    }

    /**
     * Exposes 'voteCount' in the getAssocArray()
     *
     * @param bool $toXML TRUE if method is called in the context of toXML()
     *
     * @return int Total number of votes
     */
    public function getAssoc_voteCount($toXML = false)
    {
        return $this->getVoteCount();
    }
 
    /**
    * Returns the choices of the poll
    *
    * @return  array An array containing all the pollChoice of the poll
    */
    public function getPollChoices()
    {
        return bizobject::getBizobjects('pollChoice', 'pollId=' . $this->id, 'rank');
    }

    /**
     * Update all the poll choices by the choices given in the array
     *
     * @param Array $array  Array of new choice
     */
    public function updateChoices($newChoices)
    {
        $this->serialStorage['pollChoices'] = $newChoices;
    }    
    
    /**
     * Computes the sql where clause matching foreign constraints
     * => This method must be overloaded by child class
     *
     * @param string $of Assoc Array with foreign constrains (key=className, value=id)
     *
     * @return string Sql where clause matching "of" constraints or null
     */
    public function ofClause($of)
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
            }
        }
        return $sql;
    }

    /**
     * Returns the number of votes made on this poll
     *
     * @return int
     */
    public function getVoteCount()
    {
        return $this->database->executeScalar('SELECT SUM(voteCount) FROM #__poll_choice WHERE pollId=' . $this->id);
    }
    
    /**
     * Deletes object from database
     *
     * @return mixed Boolean true on success or an error message (string)
     */
    public function delete()
    {
        if (!parent::delete())
            return false;

        // Delete all children sections of this poll
        $this->database->executeStatement('DELETE FROM #__poll_choice WHERE pollId=' . $this->id);
        return true;
    }

    /**
     * Delete all the chapters from the database
     */
    public function deletePollChoices()
    {
        // Delete all children chapters of this article
        $pollChoices = $this->getPollChoices();
        foreach($pollChoices as $pollChoice)
        {
            $pollChoice->delete(false);
        }
    }
        
    /**
     * Gets object ready to store by getting modified date, creation date etc
     * Will execute transition.
     *
     */
    protected function store()
    {
        if(!parent::store()) return false;

        $newPollChoices = getArrayParameter($this->serialStorage, 'pollChoices');

        if($newPollChoices)
        {
            $this->deletePollChoices();

            $pollChoice = new pollChoice();
            $pollChoice->pollId = $this->id;

            foreach($newPollChoices as $newPollChoice)
            {
                $pollChoice->id = 0;
                $pollChoice->rank++;
                $pollChoice->save($newPollChoice);
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
    public function checkValidity()
    {
        if (!parent::checkValidity())
            return false;

        if (trim(' ' . $this->title) == '')
        {
            $this->lastErrorMsg = _BIZ_ERROR_TITLE_IS_MANDATORY;
            return false;
        }
        
        if (strlen($this->title) > 255)
        {
            $this->lastErrorMsg = _BIZ_ERROR_TITLE_TOO_LONG;
            return false;
        }
        
        return true;
    }
}