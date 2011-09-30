<?php
/**
 * Project:     WCM
 * File:        wcm.bizclass.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * The wcmBizclass is used to describe and create business objects
 */
class wcmBizclass extends wcmSysclass
{
    /**
     * Get the database used to store/fetch object
     */
    protected function getDatabase()
    {
        if (!$this->database)
        {
            $this->database = wcmProject::getInstance()->database;
            $this->tableName = '#__bizclass';
        }
        
        return $this->database;
    }
}