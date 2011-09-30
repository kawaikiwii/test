<?php
/**
 * Project:     WCM
 * File:        wcm.cache.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * This class supports different caching mechanism based on configuration file.
 * The best practice is to use the 'APC' extension, However, if APC is not set,
 * wcmCache will use Cache_Lite (as 'Lite' mode)
 */
class wcmCache
{
    /** OPTIMISATION NSTEIN 29/06/2009
     * Static cache for the request
     * @var array
     */
    private static $registryCache = array();

    /**
     * Returns the cache mode
     *
     * @return string Cache mode (either 'session' or 'apc')
     */
    static function getMode()
    {
        $config = wcmConfig::getInstance();
        return $config['wcm.cache.mode'];
    }

    /**
     * Returns the configured cache directory, ensuring that it exists.
     *
     * @return string The configured cache directory
     */
    static function getCacheDir()
    {
        $config = wcmConfig::getInstance();
        $cacheDir = $config['wcm.cache.path'];
        if (!is_dir($cacheDir)) @mkdir($cacheDir,0777,true);
        return $cacheDir;
    }

    /**
     * Store a variable in cache
     *
     * @param string $key Unique key used to retrieve (fetch) variable
     * @param mixed $var Variable to cache
     * @param int $ttl Optional time to live (default is configuration ttl)
     *
     * @return boolean FALSE on failure, TRUE on success
     */
    static function store($key, $var, $ttl = 0)
    {
    	$config = wcmConfig::getInstance();
    	$key = $config['wcm.project.guid'].$key;
    
        // use default ttl?
        if ($ttl == 0)
        {
            $ttl = intval($config['wcm.cache.lifeTime']);
        }
		/* OPTIMISATION NSTEIN 29/06/2009 */
		self::$registryCache[$key] = $var;

        switch(self::getMode())
        {
            case 'APC':
                return apc_store($key, serialize($var), $ttl);

            case 'Lite':
            default:
                require_once(WCM_DIR . '/includes/CacheLite/Lite.php');
                $cache = new Cache_Lite(array(
                                        'caching' => true,
                                        'cacheDir' => self::getCacheDir(),
                                        'automaticSerialization' => true,
                                        'lifeTime' => $ttl));
                return $cache->save($var, $key);
        }
    }

    /**
     * Fetch a cached variable
     *
     * @param string $key Unique key used to retrieve (fetch) variable
     *
     * @return mixed Variable value or else FALSE on failure
     */
    static function fetch($key)
    {
     	$config = wcmConfig::getInstance();
    	$key = $config['wcm.project.guid'].$key;

	/* OPTIMISATION NSTEIN 29/06/2009 */
	if(isset(self::$registryCache[$key]))
        {
            return self::$registryCache[$key];
        }
    
        switch(self::getMode())
        {
            case 'APC':
                $cached = apc_fetch($key);
                if ($cached === FALSE)
                    return FALSE;
		/* OPTIMISATION NSTEIN 29/06/2009 */
		self::$registryCache[$key] = unserialize($cached);
                /* return unserialize($cached); */
		break;
            case 'Lite':
            default:
                require_once(WCM_DIR . '/includes/CacheLite/Lite.php');
                $config = wcmConfig::getInstance();
                $cache = new Cache_Lite(array(
                                        'caching' => true,
                                        'cacheDir' => self::getCacheDir(),
                                        'automaticSerialization' => true,
                                        'lifeTime' => $config['wcm.cache.lifeTime']));
		/* OPTIMISATION NSTEIN 29/06/2009 */
		self::$registryCache[$key] = $cache->get($key);
                /* return $cache->get($key); */
		break;
        }
	/* OPTIMISATION NSTEIN 29/06/2009 */
	return self::$registryCache[$key];
    }

    /**
     * Delete a cached variable
     *
     * @param string $key Unique key representing the variable to delete
     *
     * @return boolean FALSE on failure, TRUE on success
     */
    static function delete($key)
    {
    	$config = wcmConfig::getInstance();
    	$key = $config['wcm.project.guid'].$key;

	/* OPTIMISATION NSTEIN 29/06/2009 */
	unset(self::$registryCache[$key]);
    	
        switch(self::getMode())
        {
            case 'APC':
                return apc_delete($key);

            case 'Lite':
            default:
                require_once(WCM_DIR . '/includes/CacheLite/Lite.php');
                $config = wcmConfig::getInstance();
                $cache = new Cache_Lite(array(
                                        'caching' => true,
                                        'cacheDir' => self::getCacheDir(),
                                        'automaticSerialization' => true,
                                        'lifeTime' => $config['wcm.cache.lifeTime']));
                return $cache->remove($key);
        }
    }

    /**
     * Clear the entire cache
     */
    static function clear()
    {
	/* OPTIMISATION NSTEIN 29/06/2009 */
	self::$registryCache = array();

        switch(self::getMode())
        {
            case 'APC':
                return apc_clear_cache();

            case 'Lite':
            default:
                require_once(WCM_DIR . '/includes/CacheLite/Lite.php');
                $config = wcmConfig::getInstance();
                $cache = new Cache_Lite(array(
                                        'caching' => true,
                                        'cacheDir' => self::getCacheDir(),
                                        'automaticSerialization' => true,
                                        'lifeTime' => $config['wcm.cache.lifeTime']));
                return $cache->clean();
        }
    }

    /**
     * Retrieve the value of an element of a cached assoc array
     *
     * @param string $key   Key of cached array
     * @param string $elem  Key of elem in the cached array
     *
     * @return mixed Cached element or NULL if not exists
     */
    static function getElem($key, $elem)
    {
        return getArrayParameter(self::fetch($key), $elem);
    }

    /**
     * Set an element of a cached assoc array
     *
     * @param string $key   Key of cached array
     * @param string $elem  Key of element in the cached array
     * @param mixed  $value The element value to cache
     */
    static function setElem($key, $elem, $value)
    {
        // use default ttl
        $config = wcmConfig::getInstance();
        $ttl = intval($config['wcm.cache.lifeTime']);

        $assocArray = self::fetch($key);
        if ($assocArray === FALSE || !is_array($assocArray))
        {
            $assocArray = array();
        }
        $assocArray[$elem] = $value;
        self::store($key, $assocArray);
    }

    /**
     * Unset an element of a cached assoc array
     *
     * @param string $key   Key of cached array
     * @param string $elem  Key of element in the cached array
     */
    static function unsetElem($key, $elem)
    {
        $assocArray = self::fetch($key);
        if ($assocArray === FALSE || !is_array($assocArray))
        {
            $assocArray = array();
        } else {
            if (isset($assocArray[$elem]))
                unset($assocArray[$elem]);
        }
        self::store($key, $assocArray);
    }
}
