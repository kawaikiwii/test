<?php 
/**
 * Project:     WCM
 * File:        biz.admin1Codes.php
 *
 * @copyright   (c)2010 Relaxnews
 * @version     4.x
 * @author		LSJ
 *
 */
 /**
 * Definition of class admin1Codes : Codes administratifs for geoloc tables
 */
class admin1Codes extends bizobject
{
    /**
     * (string) Code admin1 (index)
     */
	public $admin1;
	
    /**
     * (int) Geoname ID (index unique)
     */
	public $geonameId;

    /**
     * (string) Name (english)
     */
	public $name;

    /**
     * (string) Name (ASCII for search)
     */
	public $asciiname;
}