<?php
/**
 * Project:     WCM
 * File:        wcm.bizrelation.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

/**
 * This class represents a relation between two business objects
 * A bizrelation is ordered (rank) and typed (kind)
 * Also, a bizrelation as a validity periode (validityDate and expirationDate)
 *
 */
class wcmBizrelation extends wcmObject
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
    public $kind;

    /**
     * (int) Rank of relation in siblings (for same source/destination/kind)
     */
    public $rank;

    /**
     * (string) Optional header associated to relation
     */
    public $header;

    /**
     * (string) Optional title associated to relation
     */
    public $title;

    /**
     * (string; date) Optional validity date associated to relation
     */
    public $validityDate;

    /**
     * (string; date) Optional expiration date associated to relation
     * (set to NULL (by default) to preserve from expiration)
     */
    public $expirationDate;

    /**
     * (array) An assoc array of extended properties (name => value)
     */
    public $properties;    


    /**
     * Constructor
     *
     * @param int $id Optional id to restore an existing relation
     */
    public function __construct($id = null)
    {
        parent::__construct($id);
    }


    /**
     * Set all initial values of an object
     * This method is invoked by the constructor
     */
    protected function setDefaultValues()
    {
        parent::setDefaultValues();

        $this->kind = 1;
        $this->rank = 1;
        $this->header = $this->title = null;
        $this->validityDate = $this->expirationDate = null;
        $this->properties = array();
    }


    /**
     * Unserialize some properties if needed
     * This method is called by nextEnum() and refresh()
     */
    public function unserializeProperties()
    {
        $this->properties = ($this->properties) ? unserialize($this->properties) : array();
    }

    /**
     * Get the database used to store/fetch object
     */
    protected function getDatabase()
    {
        // Retrieve connector and then database
        if (!$this->database)
        {
            $connector = wcmProject::getInstance()->datalayer->getConnectorByReference('biz');
            if (!$connector)
                throw new Exception("bizrelation :: invalid connector reference 'biz'");

            $this->database = $connector->getBusinessDatabase();
            $this->tableName = "#___relation";
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
            return null;

        $sql = null;
        foreach($of as $className => $id)
        {
            // Assume we want to browse relation 'of' a specific source
            if ($sql != null) $sql .= ' AND ';
            $sql .= 'sourceClass="' . $className . '" AND sourceId='.$id;
        }
        return $sql;
    }

    /**
     * Removes any relation which has expired (using the 'expirationDate' property).
     *
     * @return int Number of affected rows
     */
    public static function purge()
    {
        // Build a relation to retrieve database and tableName
        $relation = new wcmBizrelation();
        
        // Remove any expired relation
        $sql = 'DELETE FROM '. $relation->getTableName() . ' WHERE expirationDate IS NOT NULL and expirationDate < ?';
        return $relation->getDatabase()->executeStatement($sql, array(date('Y-m-d H:i:s')));
    }

    /**
     * Retrieve relations related to a bizobject and return their corresponding associative arrays
     *
     * @param wcmBizobject $bizobject Bizobject used as source object (and potentially destination object)
     * @param int $kind Used to restrict to a specific kind of relation (or null for all)
     * @param string $destinationClass Used to restrict to a specific destinationClass (or null for all)
     * @param boolean $toXML TRUE when this method is called in the context of 'toXML()' method
     *
     * @return array An array of assoc arrays (wcmBizrelation->getAssocArray(false) calls)
     */
    public static function getBizobjectRelations($bizobject, $kind=null, $destinationClass=null, $toXML = false)
    {
        $where = "sourceClass='" . $bizobject->getClass() . "' AND sourceId=" . $bizobject->id;
        if ($destinationClass)
        {
            $where .= " AND destinationClass='" . $destinationClass . "'";
        }
        if ($kind)
        {
            $where .= " AND kind=" . $kind;
        }
        $relations = array();
        $relation = new wcmBizrelation();
        $relation->beginEnum($where, "validityDate, rank");
        while ($relation->nextEnum())
        {
            // clone before retrieve getAssocArray
            $rel = clone($relation);
            $relations[] =  $rel->getAssocArray($toXML);
        }
        $relation->endEnum();
        unset($relation);
        return $relations;
    }
    
    /**
     * Removes any reference to a specific bizobject.
     *
     * @param bizobject $bizobject The bizobject to remove
     *
     * @return int The number of affected rows
     */
    public static function removeBizobject($bizobject)
    {
        // Ignore empty bizobject
        if (!$bizobject || !$bizobject->id)
            return 0;

        // Remove any reference from/to bizobject
        $className = $bizobject->getClass();
        $id = $bizobject->id;

        // Build a relation to retrieve database and tableName
        $relation = new wcmBizrelation();

        $sql = 'DELETE FROM ' . $relation->getTableName() . ' WHERE (sourceClass=? AND sourceId=?) OR (destinationClass=? AND destinationId=?)';
        return $relation->getDatabase()->executeStatement($sql, array($className, $id, $className, $id));
    }

    /**
     * Serialize all relations of a bizobject.
     * This method is used to serialize the bizobject and its result will be
     * used by the restoreBizobject method.
     *
     * @param bizobject $bizobject The bizobject to serialize
     *
     * @return array An array of bizrelations's public properties
     */
    public static function archiveBizobject($bizobject)
    {
        $relations = array();
        $relation = new wcmBizrelation;

        $where = "sourceClass='" . $bizobject->getClass() . "' AND sourceId=" . $bizobject->id;
        $relation->beginEnum($where, "validityDate, rank");
        while ($relation->nextEnum())
        {
            $relations[] = getPublicProperties($relation);
        }
        $relation->endEnum();
        unset($relation);

        return $relations;
    }
    
    /**
     * Restore all relations of a bizobject.
     * This methods will delete all current relations and rebuild them.
     *
     * @param bizobject $bizobject The bizobject to restore
     * @param array $relations An array of array representing public properties of wcmBizrelation
     */
    public static function restoreBizobject($bizobject, $relations)
    {
        // Ignore empty bizobject
        if (!$bizobject || !$bizobject->id)
            return;

        // delete relations currently stored in DB
        $relation = new wcmBizrelation();
        $sql = 'DELETE FROM ' . $relation->getTableName() . ' WHERE (sourceClass=? AND sourceId=?) OR (destinationClass=? AND destinationId=?)';
        $relation->getDatabase()->executeStatement(
                        $sql,
                        array(  $bizobject->getClass(), $bizobject->id,
                                $bizobject->getClass(), $bizobject->id
                             )
                        );
        
        // restore relations (from $relations parameter)
        if (is_array($relations))
        {
            foreach($relations as $relationArray)
            {
                // restore relation
                bindArrayToObject($relationArray, $relation);
                $relation->id = 0;
                $relation->sourceClass = $bizobject->getClass();
                $relation->sourceId = $bizobject->id;
                $relation->save();
            }
        }
    }
    
    
    /**
     * Gets the closest date with a bizrelation that has the same
     * sourceId, sourceClass and kind as this relation.
     *
     * @param string $referenceDate Date of reference (Y-m-d) to search for (or null for today)
     *
     * @return string The nearest date in the format Y-m-d
     */
    public function getLastValidityDate($referenceDate=null)
    {
        if (!$referenceDate) $referenceDate = date(Y-m-d);
        
        $sql  = "SELECT MAX(validityDate) FROM ".$this->database->quoteIdentifier($this->tableName);
        $sql .= " WHERE sourceClass=? AND sourceId=? AND kind =? AND validityDate < ?";

        $params = array($this->sourceClass, $this->sourceId, $this->kind, $referenceDate);

        return $this->database->executeScalar($sql, $params);
    }

    /**
     * Gets all validity dates with same sourceId, sourceClass and kind
     *
     * @param int $limit Maximum number of dates returned (0, by default, for all dates)
     *
     * @return array All validity dates with same sourceId, sourceClass and kind by decreasing order
     */
    public function getValidityDates($limit = 0)
    {
        $dates = array();

        $sql = 'SELECT DISTINCT validityDate FROM '. $this->tableName . ' WHERE sourceClass=? AND sourceId=? AND kind=?';
        $params = array($this->sourceClass, $this->sourceId, $this->kind);

        $rs = $this->database->executeQuery($sql, $params, 0, $limit, ResultSet::FETCHMODE_NUM);
        if ($rs != null)
        {
            while ($rs->next())
            {
                $dates[] = $rs->get(1);
            }
        }
        
        return $dates;
    }

    /**
     * Gets the last used position (rank) for this source.
     *
     * @return int The highest rank of this kind of bizrelation linked to the sourceObject
     */
    function getLastRank()
    {
        $sql  = "SELECT MAX(rank) AS nextPos FROM ".$this->database->quoteIdentifier($this->tableName);
        $sql .= " WHERE sourceClass='".$this->sourceClass."' AND sourceId=".$this->sourceId." AND kind =".$this->kind;
        if($this->validityDate != null)
            $sql .= " AND validityDate ='".$this->validityDate."'";

        return $this->database->executeScalar($sql);
    }

    /**
     * Removes the bizrelation at a specified rank  (for same sourceClass and sourceId)
     *
     * @param int     $rank  The rank of the relation to remove (or null to use current)
     * @param boolean $shift To shift previous positions
     *
     * @return boolean True on success, false on failure
     */
    function removeByRank($rank = null, $shift=true)
    {
        // Get rank from current item ?
        if ($rank == null) $rank = $this->rank;

        // Delete reference
        $sql  = "DELETE FROM ".$this->database->quoteIdentifier($this->tableName)." WHERE sourceClass=? AND sourceId=? AND rank=?";
        if($this->validityDate != null)
        {
            $sql .= " AND validityDate=?";
        }
        $params = array($this->sourceClass, $this->sourceId, $rank, $this->validityDate);

        if(!$this->database->executeStatement($sql, $params))
            return false;

        if ($shift)
        {
            // Shift previous ranks
            $sql  = "UPDATE ".$this->database->quoteIdentifier($this->tableName)." SET rank=rank-1 WHERE rank>? AND sourceClass=? and sourceId=?";
            if($this->validityDate != null)
            {
                $sql .= " AND validityDate=?";
            }
            $params = array($rank, $this->sourceClass, $this->sourceId, $this->validityDate);

            return $this->database->executeStatement($sql, $params);
        }

        return true;
    }

    /**
     * Remove all relations with a specific kind (for same sourceClass and sourceId)
     *
     * @param int  $kind Kind of relations to remove (or null to use current)
     * @param bool $sameValidityDate TRUE to remove only relations with same validity date (TRUE by default)
     *
      *@return int Number of affected rows
     */
    function removeByKind($kind = null, $sameValidityDate = true)
    {
        $sql = 'DELETE FROM ' . $this->tableName . ' WHERE sourceClass=? AND sourceId=? AND kind=?';

        if ($sameValidityDate) $sql .= 'AND validityDate=?';
        if (!$kind) $kind = $this->kind;

        $params = array($this->sourceClass, $this->sourceId, $kind, $this->validityDate);
        
        return $this->database->executeStatement($sql, $params);
    }

    /**
     * Remove all relations with a specific validityDate (for same sourceClass and sourceId)
     *
     * @param date $validityDate Validity date of relations to remove (or null to use current)
     * @param int  $kind Optional kind of relations to remove (or null for any kind)
     *
      *@return int Number of affected rows
     */
    function removeByValidityDate($validityDate = null, $kind = null)
    {
        $sql = 'DELETE FROM ' . $this->tableName . ' WHERE sourceClass=? AND sourceId=? AND validityDate=?';
        if ($kind) $sql .= 'AND kind=?';

        $params = array($this->sourceClass, $this->sourceId, $validityDate, $kind);
        
        return $this->database->executeStatement($sql, $params);
    }
    
    /**
     * Moves the bizrelation from one rank to another
     *
     * @param int  $from The rank from which to move
     * @param int  $to   The rank to which to move
     *
     * @return boolean True on success, false on failure
     *
     */
    function move($from, $to)
    {
        // Switch ranks
        $sql = "UPDATE ".$this->database->quoteIdentifier($this->tableName)." SET rank=? WHERE rank=? AND sourceClass=? AND sourceId=? AND kind=?";
        $array = array('99999', $from, $this->sourceClass, $this->sourceId, $this->kind);
        if($this->validityDate != null)
        {
            $sql .= " AND validityDate=?";
            $array = array('99999', $from, $this->sourceClass, $this->sourceId, $this->kind, $this->validityDate);
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
        $array = array($to, "99999", $this->sourceClass, $this->sourceId, $this->kind);
        if($this->validityDate != null)
        {
            $sql .= " AND validityDate=?";
            $array = array($to, "99999", $this->sourceClass, $this->sourceId, $this->kind, $this->validityDate);
        }
        $this->database->executeStatement($sql, $array);

        return true;
    }

   /**
    * Copy all bizrelations from one validityDate to another
    *
    * @param date $fromDate The source validityDate
    * @param date $toDate   The destination validityDate
    *
    * @return boolean True if copy was successfull, otherwise false
    */
   public function copy($fromDate, $toDate)
   {
        $relation = new wcmBizrelation();
        $where = "sourceId = '".$this->sourceId."' AND sourceClass = '".$this->sourceClass."' AND  kind = '".$this->kind."' AND validityDate = '".$fromDate."'";

        $relation->beginEnum($where, "rank");
        while($relation->nextEnum())
        {
            $relation->id = 0;
            $relation->validityDate = $toDate;
            if (!$relation->save())
            {
                $relation->endEnum();
                return false;
            }
        }

        $relation->endEnum();
        return true;
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
        // This is a good opportunity to purge relations ;-)
	/* OPTIMISATION NSTEIN 29/06/2009 
        $sql = 'DELETE FROM '. $this->getTableName() . ' WHERE expirationDate IS NOT NULL and expirationDate < ?';
	*/
        $this->getDatabase()->executeStatement($sql, array(date('Y-m-d')));
        return parent::beginEnum($where, $orderby, $offset, $limit, $of);
    }

    /**
     * Returns the destination object of the bizrelation
     *
     * @return bizobject The destination bizobject or null if not found
     */
    public function getDestination()
    {
        $bo = new $this->destinationClass(null, $this->destinationId);
        return ($bo->id) ? $bo : null;
    }
    
    /**
     * Exposes 'destination' to the get assoc array
     *
     * @return array the destination bizobject's getAssocArray (or null if not found)
     */
    public function getAssoc_destination($toXML = false)
    {
        if ($toXML) return null;
        
        $bo = $this->getDestination();
        if ($bo === null) return null;
        
        return $bo->getAssocArray($toXML);
    }

    /**
     * Returns the source object of the bizrelation
     *
     * @return bizobject The source bizobject or null if not found
     */
    public function getSource()
    {
        $bo = new $this->sourceClass(null, $this->sourceId);
        return ($bo->id) ? $bo : null;
    }
    
    /**
     * Exposes 'source' to the get assoc array
     *
     * @return array the source bizobject's getAssocArray (or null if not found)
     */
    public function getAssoc_source($toXML = false)
    {
        if ($toXML) return null;
        
        $bo = $this->getSource();
        if ($bo === null) return null;
        
        return $bo->getAssocArray($toXML);
    }
    
    /**
     * Starts enumeration of bizrelation using existing characteristics
     * (sourceClass, sourceId, destinationClass, destinationId, kind)
     * and a specific validityDate
     *
     * @param string $referenceDate Date of reference (Y-m-d)
     * @param int $offset Optional offset to start enumeration
     * @param int $limit Optional limit to end enumeration
     *
     * @return bool TRUE on success
     */
    public function beginEnumOnDate($referenceDate, $offset, $limit)
    {
        $where = "validityDate='" . $referenceDate . "'";

        if ($this->sourceClass) $where .= " AND sourceClass='" . $this->sourceClass . "'";
        if ($this->sourceId) $where .= " AND sourceId='" . $this->sourceId . "'";
        if ($this->destinationClass) $where .= " AND destinationClass='" . $this->destinationClass . "'";
        if ($this->destinationId) $where .= " AND destinationId='" . $this->destinationId . "'";
        if ($this->kind) $where .= " AND kind='" . $this->kind . "'";
        
        return $this->beginEnum($where, 'rank', $offset, $limit);
    }
}
?>
