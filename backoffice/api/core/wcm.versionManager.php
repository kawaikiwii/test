<?php
/**
 * Project:     WCM
 * File:        wcm.versionManager.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

/**
 * The version manager is a singleton that helps managing
 * versions of wcmObject.
 */
class wcmVersionManager
{
    private static $singleton = null;

    private $enabled;
    private $exceptions;
    private $maxVersions;
    private $connector;

    private $lastErrorMsg = null;
    
    /**
     * Returns the unique instance of the version manager (singleton)
     *
     * @return wcmVersionManager Instance of version manager
     */
    public static function getInstance()
    {
        if (!isset(self::$singleton))
        {
            self::$singleton = new wcmVersionManager();
        }
        
        return self::$singleton;
    }

    /**
     * Default constructor
     */
    public function __construct()
    {
        $config = wcmConfig::getInstance();
        $this->enabled = $config['wcm.versioning.enabled'];
        if ($this->enabled)
        {
            $this->exceptions = explode(',', $config['wcm.versioning.exceptions']);
            $this->maxVersions = $config['wcm.versioning.maxVersions'];

            $this->connector = wcmProject::getInstance()->datalayer->getConnectorByReference($config['wcm.versioning.connector']);
            if (!$this->connector)
                die('Invalid connector "' . $config['wcm.versioning.connector'] . '" for versioning. Check your configuration');
        }
    }

    /**
     * Tells if the versioning is enabled
     *
     * @return bool TRUE when versioning is enabled
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Returns the maximum number of versions store per object
     *
     * @return int Maximum number of stored versions
     */
    public function getMaxVersions()
    {
        return $this->maxVersions;
    }
    
    /**
     * Returns the connector used by the version manager
     *
     * @return wcmConnector Connector used to store versions
     */
    public function getConnector()
    {
        return $this->connector;
    }
    
    /**
     * Returns the database used by the version manager
     *
     * @return wcmDatabase Database used to store versions
     */
    public function getDatabase()
    {
        return $this->connector->getBusinessDatabase();
    }

    /**
     * Create a new version of an object
     *
     * @param wcmObject $wcmObject Object to archive
     * @param string $comment Comment associated to archive
     *
     * @return true on success, false otherwise
     */
    public function archive($wcmObject, $comment)
    {
        $this->lastErrorMsg = null;
        
        if (!$this->enabled)
            return true;

        // Insert serialized object
        $version = new wcmVersion($wcmObject->getClass(), $wcmObject->id);
        $version->objectContent = $wcmObject->serialize();
        $version->revisionNumber = $wcmObject->revisionNumber;
        $version->comment = $comment;

        if (!$version->save())
        {
            $this->lastErrorMsg = $version->getErrorMsg();
            return false;
        }
        
        return true;
    }
    
    /**
     * Returns the last error message
     *
     */
    public function getErrorMsg()
    {
        return $this->lastErrorMsg;
    }
    
    /**
     * Rollback a previous version
     *
     * @param string $className Class of object to restore
     * @param int $id Id of object to restore
     * @param int $versionId Internal unique ID representing version to rollback
     *
     * @return wcmObject Previous version of the object (or null when not found)
     */
    public function rollback($className, $id, $versionId)
    {
        return $this->restore($className, $id, $versionId, true);
    }


    /**
     * Restore a previous version
     *
     * @param string $className Class of object to restore
     * @param int $id Id of object to restore
     * @param int $versionId Internal unique ID representing version to restore
     * @param bool $rollBack TRUE to remove in-between version (false by default)
     *
     * @return wcmObject Previous version of the object (or null when not found)
     */
    public function restore($className, $id, $versionId, $rollBack = false)
    {
        $this->lastErrorMsg = null;
        
        if (!$this->enabled)
            return null;

        $version = new wcmVersion($className, $id);
        $version->refresh($versionId);
        if (!$version->id) return null;

        // Restore original object
        $original = new $className;
        $original->unserialize($version->objectContent);

        // When rollback remove in-between version
        if ($rollBack) $version->rollback();
        
        return $original;
    }

    /**
     * Get history of versions for a specific wcmObject
     *
     * @param wcmObject $wcmObject Object to retrieve history from
     * @param int $limit Maximum number of versions to return
     *
     * @return array An array of wcmVersion objects
     */
    public function getObjectHistory(wcmObject $wcmObject, $limit = 0)
    {
        return $this->getHistory(get_class($wcmObject), $wcmObject->id, $limit);
    }
    
    /**
     * Get history of versions for a specific object
     *
     * @param string $className Class of object to restore
     * @param int $id Id of object to restore
     * @param int $limit Maximum number of versions to return
     *
     * @return array An array of wcmVersion objects
     */
    public function getHistory($className, $id, $limit = 0)
    {
        $history = array();
        
        if ($this->enabled)
        {
            $version = new wcmVersion($className, $id);
            $version->beginEnum('objectId='.$id, 'versionNumber DESC', 0, $limit);
            while ($version->nextEnum())
            {
                $history[] = clone($version);
            }
            $version->endEnum();
            unset($version);
        }

        return $history;
    }
    
}