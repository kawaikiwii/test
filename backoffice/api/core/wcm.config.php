<?php
/**
 * Project:     WCM
 * File:        wcm.config.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * Holds the configuration of a project
 */
class wcmConfig implements ArrayAccess
{
    protected static $singleton = null;
    protected static $assocArray = null;

    private $config = null;

    /**
     * Implements interface ArrayAcccess
     *
     * @param string $key Configuration key
     *
     * @return bool True is key exists
     */
    public function offsetExists($key)
    {
        return array_key_exists($key, $this->config);
    }

    /**
     * Implements interface ArrayAcccess
     *
     * @param string $key Configuration key
     * @param mixed  $defaultValue Default value if key was not found
     *
     * @return mixed Configuration key value
     */
    public function offsetGet($key, $defaultValue = null)
    {
        return getArrayParameter($this->config, $key, $defaultValue);
    }

    /**
     * Implements interface ArrayAcccess
     *
     * @param string $key Configuration key
     *
     * @return Exception Configuration is read only!
     */
    public function offsetSet($key, $value)
    {
        trigger_error('Invalid access: configuration is read only!');
    }

    /**
     * Implements interface ArrayAcccess
     *
     * @param string $key Configuration key
     *
     * @return Exception Configuration is read only!
     */
    public function offsetUnset($key)
    {
        trigger_error('Invalid access: configuration is read only!');
    }

    /**
     * Constructor
     * Builds configuration from XML config file
     *
     * @param string Optional path to configuration file
     */
    public function __construct($configFile = null)
    {
        // Determine which config file to use
        if (defined('WCM_CONFIG_FILE'))
        {
            $configFile = WCM_CONFIG_FILE;
        }
        else
        {
            $configFile = WCM_DIR . '/xml/configuration.xml';
        }

        $array = array();
        self::xml2Array($array, null, simplexml_load_file($configFile));
        $this->config = $array;
    }

    /**
     * Implements Singleton
     *
     * @return wcmConfig Unique configuration instance
     */
    public static function getInstance()
    {
        // Build singleton
        if (!isset(self::$singleton))
        {
            self::$singleton = new wcmConfig();
        }

        return self::$singleton;
    }

    /**
     * Returns an assoc array of (recursive) array for templates
     *
     * @return array Config as array of array
     */
    public static function getAssocInstance()
    {
        // Determine which config file to use
        if (defined('WCM_CONFIG_FILE'))
        {
            $configFile = WCM_CONFIG_FILE;
        }
        else
        {
            $configFile = WCM_DIR . '/xml/configuration.xml';
        }

        // Build an associative array for smarty templates
        if (!isset(self::$assocArray))
        {
            self::$assocArray = object2Array(simplexml_load_file($configFile));
        }
        
        return self::$assocArray;
    }
    
    /**
     * Convert an object to an assoc array
     *
     * @param object $object Object to convert
     *
     * @return array Associative array representing the object
     */
    private static function xml2Array(&$array, $prefix = null, $object)
    {
        if(is_array($object))
        {
            foreach($object as $key => $value)
            {
                $key = ($prefix) ? $prefix . '.' . $key : $key;
                self::xml2Array($array, $key, $value);
            }
       }
       else
       {
           $var = get_object_vars($object);
           if($var)
           {
               foreach($var as $key => $value)
               {
                    $key = ($prefix) ? $prefix . '.' . $key : $key;
                    self::xml2Array($array, $key, $value);
               }
           }
           else
           {
                $array[$prefix] = strval($object);
           }
       }
    }
}
?>
