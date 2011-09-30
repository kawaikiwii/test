<?php
/**
 * Project:     WCM
 * File:        smartyFunctions.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

/**
 * This class holds a set of Smarty functions
 * They can be called through a generic wcm
 * function. e.g. {wcm name='funcName' param1=... param2=...}
 * where 'funcName' is a valid method name of this class
 */
class smartyFunctions extends wcmSmartyFunctions
{
    private static $singleton = null;

    /**
     * Returns the singleton instance
     *
     * @return smartyModifier Unique singleton instance
     */
    public static function getInstance()
    {
        // Build singleton
        if (!isset(self::$singleton))
        {
            $className = __CLASS__;
            self::$singleton = new $className();
        }

        return self::$singleton;
    }
}
?>
