<?php

/**
 * Project:     WCM
 * File:        wcmWebServiceUtil.class.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

/**
 * WCM Web Service Utility Methods
 */
class wcmWebServiceUtil
{
    /**
     * Converts an array of wcmWebServiceNameValuePair objects into an
     * associative array.
     *
     * @param wcmWebServiceNameValuePair[] $pairs The array to convert
     * 
     * @return array The corresponding associative array
     */
    public static function pairs2assoc($pairs)
    {
        $assoc = array();

        if ($pairs)
        {
            foreach ($pairs as $pair)
            {
                $assoc[$pair->name] = $pair->value;
            }
        }

        return $assoc;
    }

    /**
     * Converts an associative array into an array
     * of wcmWebServiceNameValuePair objects.
     *
     * @param array $assoc The associative array to convert
     * 
     * @return wcmWebServiceNameValuePair[] The corresponding pairs array
     */
    public static function assoc2pairs($assoc)
    {
        $pairs = array();

        if ($assoc)
        {
            foreach ($assoc as $name => $value)
            {
                $pair = new wcmWebServiceNameValuePair;
                $pair->name = $name;
                $pair->value = $value;
                $pairs[] = $pair;
            }
        }

        return $pairs;
    }
}

?>