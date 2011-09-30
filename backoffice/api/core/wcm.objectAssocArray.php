<?php
/**
 * Project:     WCM
 * File:        wcm.objectAssocArray.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * This class expose a wcmObject as an array used mainly
 * by the template engine to retrieve on-demande computed
 * properties in addition of existing public properties.
 */
class wcmObjectAssocArray implements ArrayAccess, Iterator
{
    /**
     * Prefix used to determine method name used to retrieve
     * a computed property.
     * e.g. To retrieve 'chapters' it will call 'getAssocChapters()'
     */
    const MAGIC_PREFIX = 'getAssoc_';

    private $toXML;
    private $wcmObject;
    private $cache;
    private $rewinded;

    /**
     * Builds a new wcmObjectAssocArray
     *
     * @param wcmObject $wcmObject A valid wcmObject
     * @param bool $toXML TRUE if this object is build in the context of toXML() method
     */
    function __construct($wcmObject, $toXML = false)
    {
        $this->wcmObject = $wcmObject;
        $this->toXML = $toXML;
        $this->cache = array();
    }

    /**
     * Implements interface ArrayAcccess
     *
     * @param string $key Configuration key
     *
     * @return bool True is key exists
     */
    public function offsetExists($key)
    {
        if ($key == 'this') return true;
        if (array_key_exists($key, $this->cache)) return true;
        if (property_exists($this->wcmObject, $key)) return true;
        if (method_exists($this->wcmObject, self::MAGIC_PREFIX . $key)) return true;

        return false;
    }

    /**
     * Implements interface ArrayAcccess
     *
     * @param string $key Configuration key
     *
     * @return mixed Configuration key value
     */
    public function offsetGet($key)
    {
        // Return the object?
        if ($key == 'this')
            return $this->wcmObject;

        // Use cached value
        if (!array_key_exists($key, $this->cache))
        {
            if (property_exists($this->wcmObject, $key))
            {
                $this->cache[$key] = $this->wcmObject->$key;
            }
            else
            {
                $name = self::MAGIC_PREFIX . $key;
                if (method_exists($this->wcmObject, $name))
                {
                    $this->cache[$key] = $this->wcmObject->$name($this->toXML);
                }
                else
                {
                    $this->cache[$key] = null;
                }
            }
        }
        return $this->cache[$key];
    }

    /**
     * Implements interface ArrayAcccess
     *
     * @param string $key Configuration key
     *
     * @return Exception wcmObjectAssocArray is read only!
     */
    public function offsetSet($key, $value)
    {
        throw new Exception('wcmObjectAssocArray is read-only');
    }

    /**
     * Implements interface ArrayAcccess
     *
     * @param string $key Configuration key
     *
     * @return Exception wcmObjectAssocArray is read only!
     */
    public function offsetUnset($key)
    {
        throw new Exception('wcmObjectAssocArray is read-only');
    }

    /**
     * Reset iterator
     */
    function rewind()
    {
        // Prepare to build cached assoc array
        if (!$this->rewinded)
        {
            // Retrieve all public properties
            $this->cache = getPublicProperties($this->wcmObject);

            // Retrieve all methods starting with magic prefix
            $reflection = new ReflectionClass(get_class($this->wcmObject));
            foreach($reflection->getMethods() as $method)
            {
                if ($method->isPublic())
                {
                    $name = $method->getName();
                    if (substr($name, 0, strlen(self::MAGIC_PREFIX)) == self::MAGIC_PREFIX)
                    {
                        $key = substr($method->getName(), strlen(self::MAGIC_PREFIX));
                        $val = $this->wcmObject->$name($this->toXML);

                        // Don't add null value
                        if ($val !== null) $this->cache[$key] = $val;
                    }
                }
            }
            $this->rewinded = true;
        }
        else
        {
            reset($this->cache);
        }
    }

    /**
     * Validate current iterator position
     */
    function valid()
    {
        return current($this->cache) !== false || key($this->cache);
    }

    /**
     * Returns the key associated to current iterator position
     */
    function key()
    {
        return key($this->cache);
    }

    /**
     * Returns the value associated to current iterator position
     */
    function current()
    {
        return current($this->cache);
    }

    /**
     * Returns TRUE if there is a next valid iterator position
     */
    function next()
    {
        return next($this->cache);
    }

    /**
     * @return array The PHP assoc. array equivalent
     */
    function toArray()
    {
        $array = array();

        foreach ($this as $name => $value)
            $array[$name] = $value;

        return $array;
    }
}
