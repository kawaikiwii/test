<?php
/**
 * Project:     WCM
 * File:        wcm.datalayer.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * The wcmDatalayer class is an helper class used to manage connectors and tables
 */
class wcmDatalayer
{
    /**
     * Returns an array of database tables used by the system
     *
     * @return array An array of all table names used by the system
     */
    public static function getSystemDBTables()
    {
        static $tables = null;

        if ($tables == null)
            $tables = array('biz__relation', 'biz__list', 'biz__search');

        return $tables;
    }

    /**
     * Returns a connector by its id
     *
     * @param int $id The connector id
     *
     * @return A wcmConnector object with given id (or null if id is invalid)
     */
    public function getConnectorById($id)
    {
        // Get list from cache
        return getArrayParameter($this->getConnectors(), $id, null);
    }

    /**
     * Returns a connector by its reference
     *
     * @param string $reference The connector reference
     *
     * @return A wcmConnector matching given reference (or null if none found)
     */
    public function getConnectorByReference($reference)
    {
        // Get list from cache
        foreach($this->getConnectors() as $connector)
        {
            if (0 == strcasecmp($connector->reference, $reference))
            {
                return $connector;
            }
        }
        return null;
    }

    /**
     * Returns an array of connectors matching specific conditions
     *
     * @param boolean $resetCache (optional) whether to reset the cache, ie. load from DB (default is false)
     *
     * @return array An associative array of connectors (keys are connectors id) or null on error
     */
    public function getConnectors($resetCache = false)
    {
        // Cache objects
        $cached = wcmCache::fetch('wcmConnector');
        if ($resetCache || $cached === FALSE)
        {
            $project = wcmProject::getInstance();
            $enum = new wcmConnector($project);
            if (!$enum->beginEnum(null, 'name'))
            {
                unset($enum);
                return null;
            }

            $cached = array();
            while ($enum->nextEnum())
            {
                $cached[$enum->id] = clone($enum);
            }
            $enum->endEnum();

            // Cache objects
            wcmCache::store('wcmConnector', $cached);
        }

        return $cached;
    }
}
?>