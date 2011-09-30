<?php

/**
 * Project:     WCM
 * File:        wcm.session.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

require_once("wcm.enumerable.php");

/**
 * This class represents lock information of an object
 * A lock belongs to a user (and a session) and concern a specific object
 * Also, a lock has an expiration date.
 */
class wcmLock implements wcmEnumerable
{
    /*
     * (int) ID of the session corresponding to the lock
     */
    public $sessionId = null;

    /**
     * (int) ID of the user who owns the lock
     */
    public $userId = null;

    /**
     * (string) Locked-object class name
     */
    public $objectClass = null;

    /**
     * (string) Locked-object id
     */
    public $objectId = null;

    /**
     * (string) Object title
     */
    public $title = null;

    /**
     * (date) Date (and time) of lock
     */
    public $lockDate = '0000-00-00 00:00:00';

    /**
     * (date) Expiration date
    */
    public $expirationDate = '0000-00-00 00:00:00';

    /** Protected properties used for enumeration */
    protected $enumQuery = '';
    protected $enumResultSet  = null;
    protected $enumTotalCount = null;
    protected $database = null;
    protected $tableName = '#__lock';

    /**
     * Constructor
     *
     * @param wcmSysObject $sysObject    The sysobject (or bizobject) to lock (or null)
     * @param int          $lockDuration Duration of lock in minutes (default value comes from configuration)
     */
    public function __construct($sysObject = null, $lockDuration = null)
    {
        $session = wcmSession::getInstance();
        $project = wcmProject::getInstance();
        $config = wcmConfig::getInstance();

        // Use default lock duration?
        if (!$lockDuration)
            $lockDuration = intval($config['wcm.default.lockDuration']);

        $this->sessionId = $session->id;
        $this->userId = $session->userId;

        if ($sysObject)
        {
            $this->objectClass = $sysObject->getClass();
            $this->objectId = $sysObject->id;
            // not all sysobjects have title
            if ($this->title) $this->title = $sysObject->title;
            $this->lockDate = date('Y-m-d H:i:s');
            $this->expirationDate = date('Y-m-d H:i:s', time() + $lockDuration*60);
        }
    }

    /**
     * Returns the class name of this object
     *
     * @return string Class name of this object
     */
    public function getClass()
    {
        return get_class($this);
    }

    /**
     * Lock a sysObject
     *
     * @param wcmSysObject SysObject to lock
     * @param int          $lockDuration Duration of lock in minutes (default value comes from configuration)
     *
     * @return wcmLock The lock information or null on failure
     */
    public static function lock($sysObject, $lockDuration = null)
    {
        $lock = new wcmLock($sysObject, $lockDuration);
        if ($lock->save())
            return $lock;

        return null;
    }

    /**
     * Unlock a sysObject
     *
     * @param wcmSysObject SysObject to unlock
     *
     * @return bool TRUE on success, FALSE on failure
     */
    public static function unlock($sysObject)
    {
        $sql = 'DELETE FROM #__lock WHERE objectClass=? AND objectId=?';
        return wcmProject::getInstance()->database->executeStatement($sql, array($sysObject->getClass(), $sysObject->id));
    }

    /**
     * Extend expiration date of a locked sysobject
     *
     * @param wcmSysObject $sysObject     Sysobject to extend lock
     * @param int          $lockExtension Extension in minutes of the lock (default value comes from configuration)
     *
     * @return bool TRUE on success, FALSE on failure
     */
    public static function extend($sysObject, $lockExtension = 30)
    {
        $expirationDate = date('Y-m-d H:i:s', time() + $lockExtension*60);
        $sql = 'UPDATE #__lock SET expirationDate=? WHERE objectClass=? AND objectId=?';
        return wcmProject::getInstance()->database->executeStatement($sql, array($expirationDate, $sysObject->getClass(), $sysObject->id));
    }

    /**
     * Get lock information on a sysObject.
     * Important: if the sysObject is not locked the userId and sessionId properties will be set to zero
     *
     * @param wcmSysObject SysObject to search for lock information
     *
     * @return wcmLock The lock information
     */
    public static function get($sysObject)
    {
        $lock = new wcmLock($sysObject);
        $lock->refresh();

        return $lock;
    }

    /**
     * Purges all expired locks
     *
     * @return int Number of affected rows
     */
    public static function purge()
    {
        $sql = 'DELETE FROM #__lock WHERE expirationDate <= ?';
        return wcmProject::getInstance()->database->executeStatement($sql, array(date('Y-m-d H:i:s')));
    }

    /**
     * Delete all locks wich belong to a specific user
     *
     * @param int $userId Id of the user
     *
     * @return int Number of affected rows
     */
    public static function deleteByUser($userId)
    {
        $sql = 'DELETE FROM #__lock WHERE userId = ?';
        return wcmProject::getInstance()->database->executeStatement($sql, array($userId));
    }

    /**
     * Delete all locks wich belong to a specific session
     *
     * @param int $sessionId Id of the session
     *
     * @return int Number of affected rows
     */
    public static function deleteBySession($sessionId)
    {
        $sql = 'DELETE FROM #__lock WHERE sessionId = ?';
        return wcmProject::getInstance()->database->executeStatement($sql, array($sessionId));
    }

    /**
     * Delete all locks wich belong to a specific object class
     * (or just a specific lock)
     *
     * @param string $objectClass Classname of the objects
     * @param int $objectId Optional specific object id (or null for all)
     *
     * @return int Number of affected rows
     */
    public static function deleteByClass($objectClass, $objectId = null)
    {
        $sql = 'DELETE FROM #__lock WHERE objectClass=?';
        if ($objectId) $sql .= ' AND objectId=?';
        return wcmProject::getInstance()->database->executeStatement($sql, array($objectClass, $objectId));
    }

    /**
     * Returns the number of existing locks by object class
     *
     * @return array An associative array ( objectClass => numberOfLocks)
     */
    public static function getCountByClass()
    {
        $sql = 'SELECT objectClass, COUNT(objectId) FROM #__lock GROUP BY objectClass';

        // Retrieve resultset
        $result = array();
        $rs = wcmProject::getInstance()->database->executeQuery($sql, null, 0, 0, ResultSet::FETCHMODE_NUM);
        if ($rs != null)
        {
            while($rs->next())
            {
                $result[] = $rs->getRow();
            }
        }
        $rs = null;

        return $result;
    }

    /**
     * Returns the number of existing locks by user
     *
     * @return array An associative array ( objectClass => numberOfLocks)
     */
    public static function getCountByUser()
    {
        $sql = 'SELECT userId, COUNT(objectId) FROM #__lock GROUP BY userId';

        // Retrieve resultset
        $result = array();
        $rs = wcmProject::getInstance()->database->executeQuery($sql, null, 0, 0, ResultSet::FETCHMODE_NUM);
        if ($rs != null)
        {
            while($rs->next())
            {
                $result[] = $rs->getRow();
            }
        }
        $rs = null;

        return $result;
    }

    /**
     * Refresh current lock information
     * If there is no lock (or if lock has expired) userId and sessionId properties will be set to zero
     *
     * @param wcmSysObject An optional specific sysobject (or null to take current)
     */
    public function refresh($sysObject = null)
    {
        if ($sysObject)
        {
            $this->objectClass = $sysObject->getClass();
            $this->objectId = $sysobject->id;
        }
        $this->sessionId = 0;
        $this->userId = 0;

        $sql = 'SELECT * FROM #__lock WHERE objectClass=? AND objectId=?';
        $row = wcmProject::getInstance()->database->getFirstRow($sql, array($this->objectClass, $this->objectId));
        if ($row)
        {
            // Check if lock has not already expired
            if (strtotime($row['expirationDate']) > time())
            {
                bindArrayToObject($row, $this);
            }
            else
            {
                // Purge expired lock
                $this->delete();
            }
        }
    }

    /**
     * Returns user owner of current lock
     *
     * @return wcmUser Lock's owner (or null if lock is obsolete)
     */
    public function getUser()
    {
        $user = new wcmUser(null, $this->userId);
        if (!$user->id)
        {
            $this->delete();
            return null;
        }

        return $user;
    }


    /**
     * Returns locked object
     *
     * @return wcmSysobject Locked object (or null if lock is obsolete)
     */
    public function getObject()
    {
        $object = new $this->objectClass(null, $this->objectId);
        if (!$object->id)
        {
            $this->delete();
            return null;
        }

        return $object;
    }

    /**
     * Postpone the expiration of the lock (extends lock duration)
     *
     * @param int $lockExtension Extension in minutes of the lock (default value comes from configuration)
     */
    public function postpone($lockExtension = 30)
    {
        $expirationDate = date('Y-m-d H:i:s', time() + $lockExtension*60);
        $sql = 'UPDATE #__lock SET expirationDate=? WHERE objectClass=? AND objectId=?';
        return wcmProject::getInstance()->database->executeStatement($sql, array($expirationDate, $this->objectClass, $this->objectId));
    }

    /**
     * Save lock in database
     *
     * @return bool TRUE on success, FALSE on failure
     */
    public function save()
    {
        $this->delete();
        return wcmProject::getInstance()->database->insertObject('#__lock', $this);
    }

    /**
     * Remove lock from database
     *
     * @return bool TRUE on success, FALSE on failure
     */
    public function delete()
    {
        $sql = 'DELETE FROM #__lock WHERE objectClass=? AND objectId=?';
        return wcmProject::getInstance()->database->executeStatement($sql, array($this->objectClass, $this->objectId));
    }

    /**
     * Returns an associative array containing public properties
     * of the object with corresponding values
     *
     * @return assoc array
     */
    public function getAssocArray($toXML = false)
    {
        // First, retrieve user and object (check if obsolete)
        $user = $this->getUser();
        if (!$user) return null;
        $object = $this->getObject();
        if (!$object) return null;

        $assoc = array();

        foreach(getPublicProperties($this) as $key => $val)
        {
            $assoc[$key] = $val;
        }

        // Add user
        $assoc['user'] = $user->getAssocArray($toXML);

        // Add object
        $assoc['object'] = $object->getAssocArray($toXML);

        return $assoc;
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
        // This is a good opportunity to purge obsolete locks!
        $sql = 'DELETE FROM #__lock WHERE expirationDate < ?';
        wcmProject::getInstance()->database->executeStatement($sql, array(date('Y-m-d H:i:s')));

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

            $this->enumTotalCount = wcmProject::getInstance()->database->executeScalar($sql);
        }

        // Prepare sql query
        $sql = 'SELECT * FROM ' . $this->tableName;
        if ($where !== null && $where != '')
            $sql .= ' WHERE '.$where;
        if ($orderby != null)
            $sql .= ' ORDER BY '.$orderby;

        // Load resultset
        $this->enumResultSet = wcmProject::getInstance()->database->executeQuery($sql, null, $offset, $limit);
        if ($this->enumResultSet == null)
        {
            $this->lastErrorMsg = wcmProject::getInstance()->database->getErrorMsg();
            return false;
        }

        return true;
    }

    /**
     * Moves to the next item of current enumeration
     *
     * @return boolean True if enumeration has succeed, false otherwise
     */
    public function nextEnum()
    {
        if ($this->enumResultSet == null)
            return false;

        // Erase existing property
        $properties = getPublicProperties($this);
        foreach ($properties as $property => $value)
        {
            $this->$property = null;
        }

        if (!$this->enumResultSet->next())
        {
            // We have reach the end of the resultset
            $this->endEnum();
            return false;
        }

        // Bind current object to current resultset row
        return bindArrayToObject($this->enumResultSet->getRow(), $this);
    }

    /**
     * Stops current enumeration
     */
    public function endEnum()
    {
        // Free resultset
        $this->enumResultSet = null;
        $this->enumQuery = null;
        $this->enumTotalCount = 0;
    }

    /**
     * Returns the total number of objects which can be enumerated (regardless of the
     * offset and limit parameters passed in the {@link beginEnum()} method)
     *
     * @return int Total number of enumerable objects
     */
    public function enumCount()
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
    protected function ofClause($of)
    {
        if ($of == null || !is_array($of)) return null;

        $sql = null;
        foreach($of as $key => $value)
        {
            switch($key)
            {
                case "wcmSession":
                    if ($sql) $sql .= ' AND ';
                    $sql .= ("sessionId=" . $value);
                    break;

                case "wcmUser":
                    if ($sql) $sql .= ' AND ';
                    $sql .= ("userId=" . $value);
                    break;

                case "wcmSysclass":
                case "wcmBizclass":
                    if ($sql) $sql .= ' AND ';
                    $sql .= ("objectClass=" . $value);
                    break;
            }
        }

        return $sql;
    }
}
?>
