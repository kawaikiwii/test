<?php

/**
 * Project:     WCM
 * File:        biz.schedule.php
 *
 * @copyright   (c)2008 Relaxnews
 * @version     4.x
 *
 */
 /**
 * Definition of an content object
 */
class schedule extends wcmObject
{
	public $referentId;
	public $referentClass;
	public $destinationId;
	public $format;
	public $provider;
	public $startsAt;
	public $endsAt;
	public $duration;
	public $state;
	
	
	
	
	/**
	 * Set all initial values of an object
	 * This method is invoked by the constructor
	 */
	protected function setDefaultValues()
	{
		parent::setDefaultValues();
		$this->format = "default";
	}

/**
     * Returns the database used to store object
     *
     * @return wcmDatabase Database used for storage
     */
    protected function getDatabase()
    {
        if (!$this->database)
        {
            // use same DB as article
            $this->database = wcmProject::getInstance()->bizlogic->getBizClassByClassName('event')->getConnector()->getBusinessDatabase();
            $this->tableName = '#__schedule';
        }
    }
	 /**
     * Returns the referent associated to this content
     *
     * @return content The content's referent (or null if orphan)
     */
    function getReferent()
    {
        $referent = new $this->referentClass(null, $this->referentId);
        return ($referent->id) ? $referent : null;
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
		if (isset($source['startsAt']))
		{
			$start = dateOptionsProvider::fieldDateToArray($source['startsAt']. ' 00:00:00');
			$end = dateOptionsProvider::fieldDateToArray($source['endsAt']. ' 00:00:00');
			$mktimeStart = $start['mktime'];
			$mktimeEnd = $end['mktime'];
			$count = 0;
	
			while ($mktimeStart <= $mktimeEnd)
			{
				$count++;
				$source['startsAt'] = date('Y-m-d', $mktimeStart);
				$source['endsAt'] = date('Y-m-d', $mktimeStart);
				$mktimeStart = mktime($start['hour'], $start['minute'], $start['second'], $start['month'], $start['day']+$count, $start['year']);
				parent::save($source);
			}
			
			if ($count == 0) { return parent::save($source); }
			
			return true;
		}

		else return parent::save($source);;
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
        return wcmBizsearch::getInstance()->indexBizobject($this->getReferent());
    }

    /**
     * Check validity of object
     *
     * A generic method which can (should ?) be overloaded by the child class
     *
     * @return boolean true when object is valid
     **/
     
    public function checkValidity()
    {
        return true;
    }

}
