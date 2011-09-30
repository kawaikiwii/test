<?php

/**
 * Project:     WCM
 * File:        wcm.database.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * As Creole require the php.ini "include_dirs" to be correctly set up
 * We do not have to matter what is the absolute path to creole library\
 */
require_once('creole/Creole.php');

/**
 * Generic connector to a database
 * (this class use Creole)
 *
 */
class wcmDatabase
{
    // used to know if we have initialize the connection
    private static $initializedConnections = array();
    
    /**
     * Magic prefix used in your statement; this will be substituted by
     * the real table prefix on execute or by invoking substitutePrefix method
     *
     * @see substitutePrefix
     */
    const MAGIC_PREFIX = '#__';

    /**
     * Enable/disable debugging mode
     */
    public $debug = false;

    // A wcmLogger object to display debug information (or null)
    private $logger = null;

    // Table prefix substituted to any occurrence of the magic prefix
    private $tablePrefix;

    // Database connection string (using PEAR/DSN style)
    private $connectionString;

    // Connection to database
    private $connection;

    // The hash DSN (in PEAR/DSN style)
    // => Available keys are "phptype", "hostspec", "username", "password" and "database"
    private $DSN;

    // Last error message
    private $lastErrorMsg = null;

    /**
     * Database object constructor
     *
     * @param string $connectionString Database connection string (Creole PEAR/DSN syntax)
     * @param string $tablePrefix Common prefix for all tables in the database
     * @param wcmLogger $logger A logger object to debug database operations (or null)
     */
    public function __construct($connectionString, $tablePrefix = '', &$logger = null)
    {
        $config = wcmConfig::getInstance();

        $this->tablePrefix = $tablePrefix;
        $this->connectionString = $connectionString;
        $this->logger = ($logger !== null) ? $logger : new wcmLogger($config['wcm.logging.verbose'], $config['wcm.logging.debug']);
        $this->debug = $config['wcm.logging.debug'];

        $this->getConnection();
    }

    /**
     * Destructor (automatically close the connection)
     *
     */
    function __destruct()
    {
        /**
        * This needs to be changed. It seems the destructor is called at some moment and the connection is lost

        if ($this->connection != null && $this->connection->isConnected())
            $this->connection->close();
        $this->connection = null;
        */
        return;
    }

    /**
     * This function is automatically called on serialization of current object
     *
     * @return array An array of properties (their names) to store (serialize)
     */
    function __sleep()
    {
        return array('connectionString', 'tablePrefix', 'logger', 'debug');
    }

    /**
     * This function is called on unserialization
     */
    function __wakeup()
    {
        $this->getConnection();
    }

    /**
     * Reset (re-open) a (Creole) connection
     *
     * @param string $connectionString Database connection string (Creole/PEAR syntax)
     *               eg. "mysql://dbuser:dbpass@localhost/database"
     *
     * @return A creole connection
     */
    public function resetConnection($connectionString)
    {
        $this->connection = null;
        $this->connectionString = $connectionString;
        $this->tablePrefix = $this->tablePrefix;
        return $this->getConnection();
    }

    /**
     * Open a (Creole) connection
     *
     * @return A creole connection
     */
    public function getConnection()
    {
        if (($this->connection == null) && ($this->connectionString != ''))
        {
            try
            {
                $this->connection = Creole::getConnection($this->connectionString, Creole::PERSISTENT);
                $this->DSN = $this->connection->getDSN();
                
                // Initialize connection?
                if (!isset(self::$initializedConnections[$this->connectionString]))
                {
                    // ensure utf-8 is used as default encoding
                    // set traditional mode (i.e. non strict) for Creole compatibility
                    // also, don't trace those statements
                    $debug = $this->debug;
                    $this->debug = false;
                    $this->executeStatement("SET NAMES 'UTF8'", null, false);
                    $this->executeStatement("SET sql_mode=''", null, false);
                    self::$initializedConnections[$this->connectionString] = 1;
                    $this->debug = $debug;
                }
            }
            catch(SqlException $se)
            {
                $this->lastErrorMsg = 'getConnection (' . $this->connectionString . ') :: ' . $se->getMessage();
                $this->lastErrorMsg .= ' ' . $se->getNativeError();
                $this->logger->logError($this->lastErrorMsg);
                return null;
            }
            catch(Exception $e)
            {
                $this->lastErrorMsg = 'getConnection (' . $this->connectionString . ') :: ' . $e->getMessage();
                $this->logger->logError($this->lastErrorMsg);
                return null;
            }
        }


        return $this->connection;
    }

    /**
     * Returns database schema for current connection
     *
     * @return Database schema (Creole::DatabaseInfo class)
     */
    public function getSchema()
    {
        if ($this->connection)
            return $this->connection->getDatabaseInfo();

        return null;
    }

    /**
     * Gets the DSN (hash PEAR/DSN style) associated to connection
     * => Available keys are "phptype", "hostspec", "username", "password" and "database"
     *
     * @return array The hash array representing the DSN (PEAR/DSN style)
     */
    public function getDSN()
    {
        return $this->DSN;
    }

    /**
     * Gets the PHP connection kind
     *
     * @return string The connection kind ("mysql", "oracle", "mssql", ...)
     */
    public function getConnectionKind()
    {
        return $this->DSN['phptype'];
    }

    /**
     * Returns the last error message
     *
     * @return string Last error message
     */
    public function getErrorMsg()
    {
        return $this->lastErrorMsg;
    }

    /**
     * Returns the default table prefix used for this connection database
     *
     * @return string Default table prefix
     */
    public function getPrefix()
    {
        return $this->tablePrefix;
    }

    /**
     * Substitute the magig prefix with the table prefix
     *
     * @param string $sql   An sql statement which may contains the magic prefix
     *
     * @return string An sql statement with magic prefix substituted with the table prefix
     */
    private function substitutePrefix($sql)
    {
        return str_replace(self::MAGIC_PREFIX, $this->tablePrefix, $sql);
    }

    /**
     * Quotes an identifier depending on the connection kind
     * => For mysql:  foo becomes `foo`
     * => For oracle: foo becomes "foo"
     * => For mssql:  foo becomes [foo] ...
     *
     * @param string $identifier Identifier to quote
     *
     * @return string Quoted identifier
     */
    public function quoteIdentifier($identifier)
    {
        switch($this->getConnectionKind())
        {
            case 'mysql':
            case 'mysqli':
                return '`'.$identifier.'`';
            case 'mssql':
            case 'pgsql':
            case 'sqlite':
                return '['.$identifier.']';
            case 'oracle':
            case 'odbc':
                return '"'.$identifier.'"';
            default:
                return $identifier;
        }
    }

    /**
     * Cleans the non init values of the parameters array to prevent clashes with ODBC creole driver
     *
     * @param array &$parameters Parameters used in conjunction with '?' occurrences in the sql statement
     *
     */
    private function cleanParameters(&$parameters)
    {
        if ($parameters)
        {
            foreach ($parameters as $idx => $val)
            {
                if ( !$parameters[$idx] && $parameters[$idx] !== 0 && $parameters[$idx] !== false)
                {
                    $parameters[$idx] = null;
                }
            }
        }
    }

    /**
     * Create a table in the database
     *
     * @param string $tableName Name of the table to create
     * @param array $fields Fields of the table ($name => array($type = autoinc|integer|float|bool|date|time|datetime|varchar|text),
     *                                                          $notnull, $default)
     * @param string $primaryKey Name of the primary key (or null)
     * @param array $indexList Array of field names used as index (or null)
     */
    public function createTable($tableName, $fields, $primaryKey = null, $indexList = null)
    {
        switch($this->getConnectionKind())
        {
            case 'mysql':
            case 'mysqli':
                $sql = 'CREATE TABLE ' . $this->quoteIdentifier($tableName) . '(' . PHP_EOL;
                $index = 0;
                foreach($fields as $name => $field)
                {
                    $sql .= $this->quoteIdentifier($name);
                    $type = getArrayParameter($field, 'type', 'varchar(255)');
                    switch($type)
                    {
                        case 'autoinc':
                            $sql .= ' integer not null auto_increment';
                            $primaryKey = $name;
                            break;

                        case 'integer':
                        case 'float':
                        case 'bool':
                        case 'date':
                        case 'time':
                        case 'datetime':
                        case 'text':
                            $sql .= ' ' . $type;
                            break;

                        default:
                            if ($type == 'varchar')
                                $sql .= ' varchar(255)';
                            elseif(substr($type, 0, 8) == 'varchar(')
                                $sql .= ' ' . $type;
                            else
                                die('Unexpected type "' . $type . ' for table creation!');
                            break;
                    }

                    if (getArrayParameter($field, 'notnull', false))
                        $sql .= ' not null';

                    if (getArrayParameter($field, 'default', null))
                        $sql .= ' default ' . $field['default'];

                    $sql .= ',' . PHP_EOL;
                }

                if ($primaryKey)
                    $sql .= ' PRIMARY KEY (' . $this->quoteIdentifier($primaryKey) . ')';

                if (is_array($indexList))
                {
                    foreach($indexList as $index)
                    {
                        $sql .= ',' . PHP_EOL . 'KEY ' . $this->quoteIdentifier($index) .
                                ' (' . $this->quoteIdentifier($index) . ')';
                    }
                }

                $sql .= ') ENGINE=MyISAM DEFAULT CHARSET=utf8;';
                break;

            case 'mssql':
            case 'pgsql':
            case 'oracle':
            case 'odbc':
                die('Create table is not implemented for this connector : ' . $this->getConnectionKind());
        }

        $this->executeStatement($sql);
    }

    /**
     * Executes an sql query
     *
     * @param string $sqlQuery Sql query to execute
     * @param array $parameters Parameters used in conjunction with '?' occurrences in the sql statement
     * @param int $offset Offset of first returned row (0 by default)
     * @param int $limit Maximum number of returned row (0, by default, to retrieve all rows)
     * @param int $fetchmode Either ResultSet::FETCHMODE_NUM or ResultSet::FETCHMODE_ASSOC (by default)
     * @param bool $trace Trace query (if debug is on). This parameter is for internal usage (default is true)
     *
     * @return ResultSet Creole Resultset corresponding to query or null on failure
     */
    public function executeQuery($sqlQuery, $parameters = null, $offset = 0, $limit = 0, $fetchmode = ResultSet::FETCHMODE_ASSOC, $trace = true)
    {
        // Clear last error message
        $this->lastErrorMsg = null;

        // Trace for debug
        if ($trace && $this->debug)
        {
            $trace = 'executeQuery [' . $this->connectionString . ']' . $sqlQuery;
            if ($parameters)
            {
                $trace .= ' with';
                foreach($parameters as $val)
                    $trace .= ' : ' . strval($val);
            }
            wcmTrace($trace);
        }

        try
        {
        	// Prepare statement
            $sqlQuery = $this->substitutePrefix($sqlQuery);
            
            $stmt = $this->connection->prepareStatement($sqlQuery);

            // Add the limit clause?
            if ($offset >= 0 && $limit > 0)
            {
                $stmt->setOffset($offset);
                $stmt->setLimit($limit);
            }
            $this->cleanParameters($parameters);
            return $stmt->executeQuery($parameters, $fetchmode);
        }
        catch(SqlException $se)
        {
            $this->lastErrorMsg = 'executeQuery (' . $sqlQuery . ') :: ' . $se->getMessage();
            $this->lastErrorMsg .= ' ' . $se->getNativeError();
            $this->logger->logError($this->lastErrorMsg);
        }
        catch(Exception $e)
        {
            $this->lastErrorMsg = 'executeQuery (' . $sqlQuery . ') :: ' . $e->getMessage();
            $this->logger->logError($this->lastErrorMsg);
        }
        return null;
    }

    /**
     * Executes an sql statement (if you want to execute a SELECT use {@link executeQuery} instead)
     *
     * @param string $sql Sql statement to execute (an INSERT, UPDATE or DELETE statement, ...)
     * @param array $parameters Parameters used in conjunction with '?' occurrences in the sql statement
     *
     * @return int Number of affected rows or -1 on failure
     */
    public function executeStatement($sql, $parameters = null)
    {
        // Clear last error message
        $this->lastErrorMsg = null;

        // Substitute magic prefix in statement
        $sql = $this->substitutePrefix($sql);

        // Trace for debug
        if ($this->debug)
        {
            $trace = 'executeStatement [' . $this->connectionString . ']' . $sql;
            if ($parameters)
            {
                $trace .= ' with';
                foreach($parameters as $val)
                    $trace .= ' : ' . strval($val);
            }
            wcmTrace($trace);
        }

        try
        {
            // Prepare statement
            $stmt = $this->connection->prepareStatement($sql);

            $this->cleanParameters($parameters);
            $nbrow = $stmt->executeUpdate($parameters);
            return ($nbrow == 0) ? 1 : $nbrow;
        }
        catch(SqlException $se)
        {
            $this->lastErrorMsg = 'executeStatement (' . $sql . ') :: ' . $se->getMessage();
            $this->lastErrorMsg .= ' ' . $se->getNativeError();
            $this->logger->logError($this->lastErrorMsg);
        }
        catch(Exception $e)
        {
            $this->lastErrorMsg = 'executeStatement (' . $sql . ') :: ' . $e->getMessage();
            $this->logger->logError($this->lastErrorMsg);
        }
        return -1;
    }

    /**
     *
     * Executes a "SELECT" statement and returns the first value of the first row
     *
     * @param string $query SQL query with possible occurrences of '?' for parameters
     * @param array  $parameters parameters matching the '?' occurences or null
     *
     * @return mixed The value of the first column of the first row (or null on empty/error)
     */
    public function executeScalar($sql, $parameters = null)
    {
        // Clear last error message
        $this->lastErrorMsg = null;

        // Trace for debug
        if ($this->debug)
        {
            $trace = 'executeScalar [' . $this->connectionString . ']' . $sql;
            if ($parameters)
            {
                $trace .= ' with';
                foreach($parameters as $val)
                    $trace .= ' : ' . strval($val);
            }
            wcmTrace($trace);
        }

        try
        {
            // Retrieve resultset (first row only, no assoc mode) and do not trace again!
            $rs = $this->executeQuery($sql, $parameters, 0, 1, ResultSet::FETCHMODE_NUM, false);
            if ($rs == null || !$rs->next())
            {
                // No value returned
                return null;
            }

            // Retrieve first value (Creole row is 1-based index !!!!)
            $retval = $rs->get(1);
            unset($rs);
            return $retval;
        }
        catch(SqlException $se)
        {
            $this->lastErrorMsg = 'executeScalar (' . $sql . ') ::' . $se->getMessage();
            $this->lastErrorMsg .= ' ' . $se->getNativeError();
            $this->logger->logError($this->lastErrorMsg);
        }
        catch(Exception $e)
        {
            $this->lastErrorMsg = 'executeScalar (' . $sql . ') ::' . $e->getMessage();
            $this->logger->logError($this->lastErrorMsg);
        }
        return null;
    }

    /**
     *
     * Executes a "SELECT" statement and returns the first row values
     *
     * @param string $query SQL query with possible occurrences of '?' for parameters
     * @param array  $parameters parameters matching the '?' occurences (or null by default)
     * @param int $fetchmode Either ResultSet::FETCHMODE_NUM or ResultSet::FETCHMODE_ASSOC (by default)
     *
     * @return array An assoc array containings the first row values (keys are column names) or null
     */
    public function getFirstRow($query, $parameters = null, $fetchmode = ResultSet::FETCHMODE_ASSOC)
    {
        // Clear last error message
        $this->lastErrorMsg = null;

        try
        {
            // Retrieve resultset (first row only)
            $rs = $this->executeQuery($query, $parameters, 0, 1, $fetchmode);
            if ($rs == null || !$rs->next()) return null;

            return $rs->getRow();
        }
        catch(SqlException $se)
        {
            $this->lastErrorMsg = 'getFirstRow (' . $query . ') :: ' . $se->getMessage();
            $this->lastErrorMsg .= ' ' . $se->getNativeError();
            $this->logger->logError($this->lastErrorMsg);
        }
        catch(Exception $e)
        {
            $this->lastErrorMsg = 'getFirstRow (' . $query . ') :: ' . $e->getMessage();
            $this->logger->logError($this->lastErrorMsg);
        }
        return null;
    }

    /**
     * Inserts any object into the database
     *
     * @param string $tableName
     * @param object $object Object (by ref) to insert (public properties must match column names)
     * @param array  $ignore An optional array of fields/properties to ignore on insert (default is null)
     *
     * @return boolean True on success, false on failure
     */
    public function insertObject($tableName, &$object, $ignore = null)
    {
        // Clear last error message
        $this->lastErrorMsg = null;

        // Ensure $ignore is an array
        if (!is_array($ignore)) $ignore = array();

        $fields = array();
        $values = array();
        $params = array();

        // Retrieve all public properties of object
        foreach ($object as $k => $v)
        {
            // Ignore this property ?
            if (in_array($k, $ignore))
                continue;

            // Serialize public array or object
            if  (is_array($v) or is_object($v))
            {
                $v = serialize($v);
            }
            
            $fields[] = $this->quoteIdentifier($k);
            $values[] = '?';
            $params[] = $v;
        }

        // Nothing to update ?
        if (count($fields) == 0) return false;

        // Prepare statement
        $sql = sprintf('INSERT INTO '.$tableName.'(%s) VALUES(%s)', implode(',', $fields), implode(',', $values));
        return (1 == $this->executeStatement($sql, $params));
    }

    /**
     * Updates any object into the database
     *
     * @param string $tableName
     * @param object $object Object (by ref) to insert (public properties must match column names)
     * @param string $primaryKey Name of column representing the primary key (default is "id")
     * @param array  $ignore An optional array of fields/properties to ignore on insert (default is null)
     *
     * @return boolean True on success, false on failure
     */
    public function updateObject($tableName, &$object, $primaryKey = 'id', $ignore = null)
    {
        // Clear last error message
        $this->lastErrorMsg = null;

        try
        {
            $fields = array();
            $params = array();

            // Retrieve all public properties of object (which does not start with '_')
            foreach (getPublicProperties($object) as $k => $v)
            {
                // Ignore this property ?
                if (is_array($ignore) && in_array($k, $ignore))
                    continue;

                // Ignore array, object and private properties
                if  (is_array($v) or is_object($v))
                    $v = serialize($v);

                $fields[] = $this->quoteIdentifier($k) . '=?';
                $params[] = $v;
            }

            // Nothing to update ?
            if (count($fields) == 0) return false;

            // Prepare statement
            $sql = 'UPDATE ' . $this->quoteIdentifier($tableName) . ' SET ' . implode(',', $fields);
            $sql .= ' WHERE ' . $this->quoteIdentifier($primaryKey) . '=?';
            $params[] = $object->$primaryKey;

            $sql = $this->substitutePrefix($sql);
            $stmt = $this->connection->prepareStatement($sql);

            // Execute statement
            $this->cleanParameters($params);
            $stmt->executeUpdate($params);
            return true;
        }
        catch(SqlException $se)
        {
            $this->lastErrorMsg = 'updateObject (' . $tableName . ') :: ' . $se->getMessage();
            $this->lastErrorMsg .= ' ' . $se->getNativeError();
            $this->logger->logError($this->lastErrorMsg);
        }
        catch(Exception $e)
        {
            $this->lastErrorMsg = 'updateObject (' . $tableName . ') :: ' . $e->getMessage();
            $this->logger->logError($this->lastErrorMsg);
        }
        return false;
    }

    /**
     *
     * Load objects properties from the database using a specific id
     *
     * @param wcmObject $object Object to bind
     * @param int $id Id used to select row (or null to use object->id)
     *
     * @return boolean True on success, false on failure
     */
    public function bindSysObject($wcmObject, $id = null)
    {
        // We assume that the primary key of a sysobject is "id"
        if ($id == null)
            $id = $wcmObject->id;

        $query = 'SELECT * FROM ' . $wcmObject->getTableName() . ' WHERE id='.$id;
        return bindArrayToObject($this->getFirstRow($query), $wcmObject);
    }

    /**
     *
     * Update or insert a wcmSysobject into the database
     *
     * @param wcmSysObject $object SysObject (by ref) to fill in
     * @param int $id Id used to select row in the database (or null to use wcmSysObject id)
     * @param array  $ignore An optional array of fields/properties to ignore on insert (default is null)
     *
     * @return boolean True on success, false on failure
     */
    public function storeSysObject($object, $ignore = null)
    {
        if ($object->id)
        {
            // Update existing row (primary key is 'id', so don't update this value)
            return $this->updateObject($object->getTableName(), $object, 'id', array('id'));
        }
        else
        {
            // Insert new row (check if rdbms uses sequence)
            $idgen = $this->getConnection()->getIdGenerator();

            if ($idgen->isBeforeInsert())
            {
                // Retrieve a new id from seqence
                $object->id = $idgen->getId(strtolower(get_class($object)).'_id', $ignore);

                // Insert object with the new id
                return $this->insertObject($object->getTableName(), $object);
            }
            else
            {
                try
                {
                    // Inserting object will generate a new id (so don't set any value for 'id')
                    if (!$this->insertObject($object->getTableName(), $object, array('id')))
                        return false;

                    // Retrieve auto-increment id
                    $object->id = $idgen->getId($this->substitutePrefix($object->getTableName()));
                    return ($object->id && $object->id > 0);
                }
                catch(SQLException $e)
                {
                    $this->lastErrorMsg = 'storeSysObject :: ' . $e->getMessage();
                    $this->logger->logError($this->lastErrorMsg);
                }
            }
        }
        return false;
    }

    /**
     * Delete a wcmSysobject
     *
     * @param wcmSysObject $object SysObject (by ref) to delete
     *
     * @return boolean True on success, false on failure
     */
    public function deleteSysObject(&$object)
    {
        $sql = 'DELETE FROM ' . $this->quoteIdentifier($object->getTableName()) . ' WHERE id=?';
        return $this->executeStatement($sql, array($object->id));
    }
}
?>
