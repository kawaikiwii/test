<?php
/**
 * Project:     WCM
 * File:        biz.pollChoice.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * This class represents a pollChoice.
 * 
 * A poll_coice is a unique question that is always related to a {@link poll poll} via
 * the {@link pollChoice::pollId poll id}. As it's usually part of a collection of pollChoice instances,
 * it can be ranked (appear before or after another pollChoice belonging to the same poll).
 * 
 * Each pollChoice will also have a {@link pollChoice::voteCount $voteCount} property that keeps track 
 * of how many users have chosen this option
 * 
 * @see poll
 */
class pollChoice extends wcmObject
{
    /**
     * Id of belonging poll
     * 
     * @var int
     */
    public $pollId;

    /**
     * Choice text
     * 
     * @var string
     */
    public $text = '';

    /**
     * Choice rank
     * 
     * @var int
     */
    public $rank;

    /**
     * Total votes made on this choice
     * 
     * @var int
     */
    public $voteCount;
    
    
    /**
     * Returns the database used to store object
     *
     * @return wcmDatabase Database used for storage
     */
    protected function getDatabase()
    {
        if (!$this->database)
        {
            $this->database = wcmProject::getInstance()->bizlogic->getBizClassByClassName('poll')->getConnector()->getBusinessDatabase();
            $this->tableName = '#__poll_choice';
        }
    }
    
    /**
     * Set default values for the object.
     * 
     * @see wcmSysobject::setDefaultValues()
     */
    protected function setDefaultValues()
    {
        parent::setDefaultValues();
        
        $this->rank = 0;
        $this->voteCount = 0;
    }

    /**
     * Expose 'poll' in the getAssocArray
     *
     * @param bool $toXML TRUE if method is called in the context of toXML()
     *
     * @return array The beloging poll's getAssocArray
     */
    function getAssoc_poll($toXML = false)
    {
        if ($toXML) return null;
        
        $poll = $this->getPoll();
        return ($poll) ? $poll->getAssocArray($toXML) : null;
    }
    
    /**
    * Returns belonging poll
    *
    * @return poll Belonging poll (or null if orphan)
    */
    function getPoll()
    {
        $poll = new poll(null, $this->pollId);
        return ($poll->id) ? $poll : null;
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
                case "poll":
                    if ($sql != null) $sql .= " AND ";
                    $sql .= "pollId=".$value;
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
     * Save object in database and update search table
     *
     * @param array $source array for binding to class vars (or null)
     *
     * @return bool True on success, false otherwise
     */
    public function save($source = null)
    {
        // Save
        if (!parent::save($source))
            return false;

        $poll = new poll(wcmProject::getInstance(), $this->pollId);

        // Reindex the parent bizobject
        return wcmBizsearch::getInstance()->indexBizobject($poll);
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
        return wcmBizsearch::getInstance()->indexBizobject($this->getPoll());
    }
}