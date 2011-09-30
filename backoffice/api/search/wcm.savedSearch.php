<?php

/**
 * Project:     WCM
 * File:        api/search/wcm.savedSearch.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     3.2
 *
 */

class wcmSavedSearch extends wcmObject
{
    /**
     * Name of saved search
     *
     * @var string
     */
    public $name;

    /**
     * Description of saved search
     *
     * @var string
     */
    public $description;

    /**
     * userId of saved search owner
     *
     * @var int
     */
    public $userId;

    /**
     * Lucene-syntax query string
     *
     * @var string
     */
    public $queryString;

    /**
     * Search URL
     *
     * @var string
     */
    public $url;

    /**
     * Boolean to indicate whether the saved search is part of the dashboard or not
     */
    public $dashboard;

    /**
     * Boolean to indicate whether the saved search is accessible to all the users or just the creator
     */
    public $shared;
    
	/**
     * Boolean to indicate whether the saved search is accessible to all the users or just the creator
     */
    public $showui;
    
    /**
     * Constructor
     *
     * @param int $id Optional id to load existing saved search
     */
    function __construct($id=null)
    {
        $this->database = wcmProject::getInstance()->database;
        $this->tableName = '#__user_savedsearch';
        parent::__construct($id);
    }

     /** Computes the sql where clause matching foreign constraints
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
                case 'user':
                    if ($sql != null) $sql .= ' AND ';
                    $sql .= 'userId='.$value;
                    break;
            }
        }
        return $sql;
    }
}
?>