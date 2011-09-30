<?php
/**
 * Project:     WCM
 * File:        smartyModifiers.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

/**
 * This class holds a set of Smarty modifiers
 * They call be called through a generic |wcm
 * modifier. e.g. {$bizobject|@wcm:name:param1:param2}
 * where 'name' is the function name
 */
class smartyModifiers extends wcmSmartyModifiers
{
    private static $singleton = null;

    // @todo :  move to base class
    public static function sentiment($argBizobject)
    {
        
        $bizobject = $argBizobject['this'];
        
        $sd = $bizobject->semanticData;
        $t = $sd->tone;
        $s = $sd->subjectivity;
        
        // @todo : add ressources
        $sentiment = 'Neutral';
        
        if ($t < -30 && $s >= 75) $sentiment = 'Troll';
        if (($t < 25 && $t > -25) && ($s < 74 && $s > 50)) $sentiment = 'Insightful';
        if (($t < 25 && $t > -25) && ($s < 49 && $s > 25)) $sentiment = 'Informative';
        if (($t < 10 && $t > -10) && ($s < 25)) $sentiment = 'Fact';
        if (($t > 25) && ($s > 75)) $sentiment = 'Positive';
        if (($t <= 0 && $t > -25) && ($s > 75)) $sentiment = 'Negative';     

        return $sentiment;
        break;
        
    }
    
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
