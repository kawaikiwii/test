<?php 
/**
 * Project:     WCM
 * File:        biz.alternate_names.php
 *
 * @copyright   (c)2010 Relaxnews
 * @version     4.x
 * @author		LSJ
 *
 */
 /**
 * Definition of class alternate_names : alternate names (translate) for geoloc tables
 */
class alternate_names extends bizobject
{
    /**
     * (int) Alternate name ID (index unique)
     */
	public $alternateNameId;

    /**
     * (int) Geoname ID (index)
     */
	public $geonameId;

    /**
     * (string) code ISO language
     */
	public $isolanguage;

    /**
     * (string) Alternate name
     */
	public $alternate_name;

    /**
     * (bool) is Preferred Name (Y/N)
     */
	public $isPreferredName;

    /**
     * (bool) is Short Name (Y/N)
     */
	public $isShortName;

	public function getNameByGeonameId($geonameId, $isolanguage)
    {
		$sql = 'SELECT alternate_name FROM ' . $this->tableName . ' WHERE geonameId=? AND isolanguage=?';
        $name = $this->database->executeScalar($sql, array($geonameId,$isolanguage));
        if (!empty($name))	return $name;
        else return false;       
    }
}