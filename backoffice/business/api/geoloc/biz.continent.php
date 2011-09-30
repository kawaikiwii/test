<?php 
/**
 * Project:     WCM
 * File:        biz.continent.php
 *
 * @copyright   (c)2010 Relaxnews
 * @version     4.x
 * @author		LSJ
 *
 */
 /**
 * Definition of class continent
 */
class continent extends bizobject
{
    /**
     * (int) Geoname ID (index unique)
     */
	public $geonameId;

    /**
     * (string) Continent name
     */
	public $name;

    /**
     * (string) code ISO continent
     */
	public $code;

    /**
     * (string) Alternate names
     */
	public $alternateNames;

}