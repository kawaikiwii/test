<?php
/**
 * Project:     WCM
 * File:        wcm.bizrelation.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * This class represents a relation between two business objects.
 * A bizrelation is ordered (rank) and typed (kind).
 * Also, a bizrelation as a validity periode (validityDate and expirationDate)
 *
 */
class bizrelation implements wcmEnumerable
{
    //
    // Kind of relations : an integer value from 1 to 5
    //
    
    /**
     * (1) IS_PART_OF     destination is part of source (e.g. chapitre of an article)
     */
    const IS_PART_OF = 1;

    /**
     * (2) IS_RELATED_TO  destination is related to source (e.g. link of an article)
     */
    const IS_RELATED_TO = 2;

    /**
     * (3) IS_COMPOSED_OF destination is composed of source (e.g. photo of a slideshow)
     */
    const IS_COMPOSED_OF = 3;
    
    /**
     * (4) IS_EMPTY specifically used to explain that there is no bizrelations
     */
    const IS_EMPTY = 4;
     
     /**
      * (5) IS_FORCED destination rank and value is forced by user (e.g. content of a channel)
      */
    const IS_FORCED = 5;

	const IS_DISTRIBUTED_BY = 6;
	
	const IS_EDITED_BY = 7;
	
	const IS_CONTACT_OF = 8;
    //
    // Public members
    //
     
    /**
     * (int) Unique identifier of bizrelation
     */
    public $id;

    /**
     * (string) Class name of source bizobject
     */
    public $sourceClass;

    /**
     * (int) Id of source bizobject
     */
    public $sourceId;

    /**
     * (string) Class name of destination bizobject
     */
    public $destinationClass;

    /**
     * (int) Id of destination bizobject
     */
    public $destinationId;

    /**
     * (int) Kind of relation existing between source and destination
     */
    public $kind = 1;

    /**
     * (int) Rank of relation in siblings (for same source/destination/kind)
     */
    public $rank = 0;

    /**
     * (string) Optional header associated to relation
     */
    public $header = null;

    /**
     * (string) Optional title associated to relation
     */
    public $title = null;

    /**
     * (string; date) Optional validity date associated to relation
     */
    public $validityDate = null;

    /**
     * (string; date) Optional expiration date associated to relation
     * (set to NULL (by default) to preserve from expiration)
     */
    public $expirationDate = null;


    //
    // Protected members
    //

    /**
     * (wcmDatabase) Datebase where biz__relation table is stored
     */
    protected $database = null;


    //
    // Private members
    //
    private $tableName      = "#___relation";
    private $enumTotalCount = null;
    private $enumQuery      = null;
    private $enumResult     = null;
    private $enumFrom       = null;
    private $enumLimit      = null;


    /**
     * Constructor
     *
     */
    function __construct()
    {
        //die('This class is obsolete!');
        $this->tableName = "#___relation";

        // Retrieve connector and then database
        $connector = wcmProject::getInstance()->datalayer->getConnectorByReference('biz');
        if (!$connector)
            throw new Exception("bizrelation :: invalid connector reference " . $connectorRef);
        $this->database = $connector->getBusinessDatabase();
    }

    /**
     * Returns the name of the table. This property is used by wcmDatabase::storeSysObject() to
     * store a generic object (assuming 'id' is a primary key with auto-increment)
     * 
     * @return string The table name where bizrelations are stored
     */
    public function getTableName() 
    {
        return $this->tableName;    
    }

    /**
     * Removes any relation which has expired (using the 'expirationDate' property).
     *
     * @return int Number of affected rows
     */
    public function purgeRelation()
    {
        // Remove any expired relation
        $sql = 'DELETE FROM '. $this->tableName . ' WHERE expirationDate IS NOT NULL and expirationDate < ?';
        return $this->database->executeStatement($sql, array(date('Y-m-d H:i:s')));
    }

    /**
     * Removes any reference to a specific bizobject.
     *
     * @param bizobject $bizobject    The bizobject to remove
     *
     * @return int The number of affected rows
     */
    static function removeBizobject($bizobject)
    {
        // Ignore empty bizobject
        if (!$bizobject || !$bizobject->id) return true;

        // Retrieve connector and then database
        $connector = wcmProject::getInstance()->datalayer->getConnectorByReference('biz');
        if (!$connector)
            throw new Exception("bizrelation :: invalid connector reference " . $connectorRef);
        $db = $connector->getBusinessDatabase();

        // Remove any reference from/to bizobject
        $className = get_class($bizobject);
        $id = $bizobject->id;

        $sql = "DELETE FROM #___relation WHERE (sourceClass=? AND sourceId=?) OR (destinationClass=? AND destinationId=?)";
        return $db->executeStatement($sql, array($className, $id, $className, $id));
    }

    /**
     * Adds the relation to the DB.
     */
    public function addBizrelation()
    {
        $this->database->storeSysObject($this);
    }

    /**
     * Initialize and starts a new enumeration on current wcmSysobject
     *
     * @param string $where     Optional where clause
     * @param string $orderby   Optional order clause
     * @param int    $offset    Optional offset of first row (default is zero)
     * @param int    $limit     Optional maximum number of returned rows (default is zero, means return all rows)
     * @param string $of        Optional assoc Array with foreign constraint (key=className, value=id)
     *
     * @return boolean  True on success, false on failure
     */
    public function beginEnum($where = null, $orderby = null, $offset = 0, $limit = 0, $of = null)
    {
        // Ensure to free previous enumeration
        $this->endEnum();

        // Extends where clause with "of" clause
        $ofClause = $this->ofClause($of);
        if ($ofClause != null)
        {
            if ($where != null && $where != '')
                $where = '('.$where.') AND '.$ofClause;
            else
                $where = $ofClause;
        }

        // First of all, compute total number of items
        if ($where == null || (strcmp($where, $this->enumQuery) != 0))
        {
            // Remember actual query
            $this->enumQuery = $where;

            // Compute number of wcmSysObject matching the query
            $sql = 'SELECT COUNT(*) FROM ' . $this->tableName;
            if ($where != null && $where != '')
                $sql .= ' WHERE '.$where;

            $this->enumTotalCount = $this->database->executeScalar($sql);
        }

        // Prepare sql query
        $sql = 'SELECT * FROM ' . $this->tableName;
        if ($where !== null && $where != '')
            $sql .= ' WHERE '.$where;
        if ($orderby != null)
            $sql .= ' ORDER BY '.$orderby;

        // Load resultset
        $this->enumResult = $this->database->executeQuery($sql, null, $offset, $limit);

        if ($this->enumResult == null)
        {
            $this->lastErrorMsg = $this->database->getErrorMsg();
            return false;
        }

        return true;
    }

    /**
     * Gets the next item in the current enumeration.
     *
     * @return mixed The next item on success, false otherwise
     */
    public function nextEnum()
    {
        if ($this->enumResult == null)
            return false;
            
        if (!$this->enumResult->next())
        {
            // We have reach the end of the resultset
            $this->endEnum();
            return false;
        }

        // Bind current object to current resultset row
        return bindArrayToObject($this->enumResult->getRow(), $this);
    }

    /**
     * Ends the current enumeration.
     *
     */
    function endEnum()
    {
        // Free resultset
        $this->enumResult = null;
        $this->enumTotalCount = 0;
    }

    /**
     * Gets the total number of objects which can be enumerated.
     *
     * @return int The total number of objects which can be enumerated
     */
    function enumCount()
    {
        return $this->enumTotalCount;
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
                case 'site':
                    if ($sql != null) $sql .= ' AND ';
                    $sql .= 'siteId='.$value;
                    break;

                case 'channel':
                    if ($sql != null) $sql .= ' AND ';
                    $sql .= 'channelId='.$value;
                    break;
            }
        }
        return $sql;
    }
    
    /**
     * Gets the closest date with a bizrelation that has the same
     * sourceId, sourceClass and kind as this relation.
     *
     * @return string The nearest date in the format Y-m-d
     */
    function getNearestDate()
    {
        $sql  = "SELECT MAX(validityDate) FROM ".$this->database->quoteIdentifier($this->tableName);
        $sql .= " WHERE sourceClass=? AND sourceId=? AND kind =? AND validityDate < ?";

        $params = array($this->sourceClass, $this->sourceId, $this->kind, $this->validityDate);

        return $this->database->executeScalar($sql, $params);
    }

    /**
     * Gets all dates with a bizrelation that has the same sourceId, sourceClass and kind as $this
     *
     * @return array All dates available for this bizobject
     */
    function getAllDates()
    {
        $sql2  = "SELECT DISTINCT validityDate FROM ".$this->database->quoteIdentifier($this->tableName);
        $sql2 .= " WHERE sourceClass='".$this->sourceClass."' AND sourceId='".$this->sourceId."' AND kind ='".$this->kind . "'";

        return $this->database->executeQuery($sql2);
    }

    /**
     * Gets the last used position (rank) for this source.
     *
     * @return int The highest rank of this kind of bizrelation linked to the sourceObject
     *
     */
    function getLastPosition()
    {
        $sql  = "SELECT MAX(rank) AS nextPos FROM ".$this->database->quoteIdentifier($this->tableName);
        $sql .= " WHERE sourceClass='".$this->sourceClass."' AND sourceId=".$this->sourceId." AND kind =".$this->kind;
        if($this->validityDate != null)
            $sql .= " AND validityDate ='".$this->validityDate."'";

        return $this->database->executeScalar($sql);
    }

    /**
     * Moves the bizrelation.
     *
     * @param int  $from The rank from which to move
     * @param int  $to   The rank to which to move
     *
     * @return boolean True on success, false on failure
     *
     */
    function move($from, $to)
    {
        $displayError = true;

        // Switch ranks
        $sql = "UPDATE ".$this->database->quoteIdentifier($this->tableName)." SET rank=? WHERE rank=? AND sourceClass=? AND sourceId=? AND kind=?";
        $array = array('9999', $from, $this->sourceClass, $this->sourceId, $this->kind);
        if($this->validityDate != null)
        {
            $sql .= " AND validityDate=?";
            $array = array('9999', $from, $this->sourceClass, $this->sourceId, $this->kind, $this->validityDate);
        }
        $this->database->executeStatement($sql, $array);

        $sql = "UPDATE ".$this->database->quoteIdentifier($this->tableName)." SET rank=? WHERE rank=? AND sourceClass=? AND sourceId=? AND kind=?";
        $array = array($from, $to, $this->sourceClass, $this->sourceId, $this->kind);
        if($this->validityDate != null)
        {
            $sql .= " AND validityDate=?";
            $array = array($from, $to, $this->sourceClass, $this->sourceId, $this->kind, $this->validityDate);
        }
        $this->database->executeStatement($sql, $array);

        $sql = "UPDATE ".$this->database->quoteIdentifier($this->tableName)." SET rank=? WHERE rank=? AND sourceClass=? AND sourceId=? AND kind=?";
        $array = array($to, "9999", $this->sourceClass, $this->sourceId, $this->kind);
        if($this->validityDate != null)
        {
            $sql .= " AND validityDate=?";
            $array = array($to, "9999", $this->sourceClass, $this->sourceId, $this->kind, $this->validityDate);
        }
        $this->database->executeStatement($sql, $array);


        $displayError = false;

        return true;
    }

    /**
     * Update the bizrelation.
     *
     * @param string $destinationClass Class name of the destination object
     * @param int    $destinationId    ID of the destination object
     * @param int    $kind             Kind of the relation
     * @param string $header           Header of the relation
     * @param title  $title            Title of the relation
     * @param int    $rank             Rank of the relation
     *
     * @return boolean True on success, false on failure
     *
     */
    function update($destinationClass, $destinationId, $kind, $header, $title, $rank)
    {
         $sql  = "UPDATE ".$this->tableName." SET destinationClass=?, destinationId=?, header=?, title=? WHERE sourceId=? AND sourceClass=? AND rank=? AND kind=?";
        $array = array($destinationClass, $destinationId, $header, $title, $this->sourceId, $this->sourceClass, $rank, $kind);
        if($this->validityDate != null)
        {
            $sql .= " AND validityDate=?";
            $array = array($destinationClass, $destinationId, $header, $title, $this->sourceId, $this->sourceClass, $rank, $kind, $this->validityDate);
        }
        return $this->database->executeStatement($sql, $array);
    }

    /**
     * Insert the bizrelation in the DB.
     *
     * @param string $destinationClass Class name of the destination object
     * @param int    $destinationId    ID of the destination object
     * @param int    $kind             Kind of the relation
     * @param string $header           Header of the relation
     * @param title  $title            Title of the relation
     * @param int    $rank             Rank of the relation
     *
     * @return boolean True on success, false on failure
     *
     */
    function insert($destinationClass, $destinationId, $kind, $header, $title, $rank = null)
    {
        // Fix rank in current range
        if($rank == null)
            $max = $this->getLastPosition() + 1;
        else
            $max = $rank;
        $newrelation = new bizrelation();
        $newrelation->rank = $max;
        $newrelation->sourceClass = $this->sourceClass ;
        $newrelation->sourceId = $this->sourceId;
        $newrelation->kind = $kind;
        $newrelation->destinationClass = $destinationClass;
        $newrelation->destinationId = $destinationId;
        $newrelation->title = urldecode($title);
        $newrelation->header = urldecode($header);
        $newrelation->validityDate = $this->validityDate;
        $newrelation->expirationDate = $this->expirationDate;
        return $this->database->storeSysObject($newrelation);
    }

	function insertSpecificObject($destinationClass, $destinationId, $kind, $title = null, $rank = null)
    {
        // Fix rank in current range
        if($rank == null)
            $max = $this->getLastPosition() + 1;
        else
            $max = $rank;
        $newrelation = new bizrelation();
        $newrelation->rank = $max;
        $newrelation->sourceClass = $this->sourceClass ;
        $newrelation->sourceId = $this->sourceId;
        $newrelation->kind = $kind;
        $newrelation->destinationClass = $destinationClass;
        $newrelation->destinationId = $destinationId;
        $newrelation->title = urldecode($title);
        $newrelation->validityDate = $this->validityDate;
        $newrelation->expirationDate = $this->expirationDate;
        return $this->database->storeSysObject($newrelation);
    }
    /**
     * Removes the bizrelation with a specified rank.
     *
     * @param int     $rank  The rank of the relation to remove
     * @param boolean $shift To shift previous positions
     *      *
     * @return boolean True on success, false on failure
     *
     */
    function remove($rank = null, $shift=true)
    {
        // Get rank from current item ?
        if ($rank == null) $rank = $this->rank;

        // Delete reference
        $sql  = "DELETE FROM ".$this->database->quoteIdentifier($this->tableName)." WHERE sourceClass=? AND sourceId=? AND rank=?";
        $array = array($this->sourceClass, $this->sourceId, $rank);

        if($this->validityDate != null)
        {
            $sql .= " AND validityDate=?";
            $array = array($this->sourceClass, $this->sourceId, $rank, $this->validityDate);
        }

        if($this->database->executeStatement($sql, $array))
        {
            if ($shift)
            {
                // Shift previous ranks
                $sql  = "UPDATE ".$this->database->quoteIdentifier($this->tableName)." SET rank=rank-1 WHERE rank>? AND sourceClass=? and sourceId=?";
                $array = array($rank, $this->sourceClass, $this->sourceId);
                if($this->validityDate != null)
                {
                    $sql .= " AND validityDate=?";
                    $array = array($rank, $this->sourceClass, $this->sourceId, $this->validityDate);
                }
    
                return $this->database->executeStatement($sql, $array);
            }
            else
                return true;
        }
        else
            return false;
    }

    /**
     * Removes one kind of bizrelations.
     *
     * @param int $Kind The kind of bizrelations to remove
     *
     * @return boolean True on success, false on failure
     *
     */
    function removeOneKind($kind)
    {
        // Delete reference
        $sql  = "DELETE FROM ".$this->database->quoteIdentifier($this->tableName)." WHERE sourceClass=? AND sourceId=? AND kind=? AND validityDate=?";
        $result =  $this->database->executeStatement($sql, array($this->sourceClass, $this->sourceId, $kind, $this->validityDate));
        return $result;
    }

    /**
     * Removes one kind of bizrelations where the destinationClass.
     *
     * @param int $kind The kind of bizrelations to remove
     *
     * @return boolean True on success, false on failure
     *
     */
    function removeOne($kind = null)
    {
        if (!$kind)
            $kind = $this->kind;
        // Delete reference
        $sql  = "DELETE FROM ".$this->database->quoteIdentifier($this->tableName)." WHERE sourceClass=? AND sourceId=? AND destinationClass=? AND kind=?";
        return $this->database->executeStatement($sql, array($this->sourceClass, $this->sourceId, $this->destinationClass, $kind));
    }
    
    /**
     * Returns executeStatement
     *
     * @return executeStatement
     */
	function removeSpecificObjectByKind($objectName, $objectId, $destinationClass, $kind)
    {
        $sql  = "DELETE FROM ".$this->database->quoteIdentifier($this->tableName)." WHERE sourceClass=? AND sourceId=? AND destinationClass=? AND kind=?";
        return $this->database->executeStatement($sql, array($objectName, $objectId, $destinationClass, $kind));
    }
    
   /**
    * Tests whether there are bizrelations for the specified date and kind.
    *
    * @param string $date A date
    * @param int    $kind A kind
    *
    * @return boolean True if there is at least 1 bizrelation for the date and kind
    */
   public function exist($date, $kind)
   {
        $where = "rank > 0 AND sourceId = '".$this->sourceId."' AND sourceClass = '".$this->sourceClass."' AND  kind = '".$kind."' AND validityDate = '".$date."'";

        $this->beginEnum($where, "rank");
        if($this->enumTotalCount > 0)
            return true;

        return false;
   }

   /**
    * Copy all bizrelations from one date to another.
    *
    * @param date $fromDate The source date
    * @param date $toDate   The destination date
    *
    * @return boolean True if copy was successfull, otherwise false
    */
   public function copy($fromDate, $toDate)
   {
        $where = "rank > 0 AND sourceId = '".$this->sourceId."' AND sourceClass = '".$this->sourceClass."' AND  kind = '".$this->kind."' AND validityDate = '".$fromDate."'";
        $this->beginEnum($where, "rank");

        // If no result returned
        if($this->enumTotalCount == 0)
            return false;

        // Copy all bizrelation
        while($this->nextEnum())
        {
            $this->validityDate = $toDate;
            // Check if insert is successful
            if(!$this->insert($this->destinationClass, $this->destinationId, $this->kind, $this->header, $this->title, $this->rank))
                return false;
        }
        return true;
   }

    /*
    * @return array Assoc array representing the $key => values of the class's properties
    */
    public function getAssocArray()
    {
        $assoc = array();
        foreach($this as $key => $value)
        {
            $assoc[$key] = $value;
        }
        return $assoc;
    }

}
?>