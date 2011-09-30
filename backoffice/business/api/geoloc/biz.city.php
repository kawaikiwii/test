<?php 
/**
 * Project:     WCM
 * File:        biz.city.php
 *
 * @copyright   (c)2010 Relaxnews
 * @version     4.x
 * @author		LSJ
 *
 */
 /**
 * Definition of class city
 */
class city extends bizobject
{
    /**
     * (int) Geoname ID (index unique)
     */
	public $geonameId;

    /**
     * (string) City name (english)
     */
	public $name;

    /**
     * (string) City name (ASCII for search)
     */
	public $asciiname;

    /**
     * (string) Selection of alternate names
     */
	public $alternateNames;

    /**
     * (decimal) Latitude
     */
	public $latitude;

    /**
     * (decimal) Longitude
     */
	public $longitude;
	
	public $ftclass;

	public $ftcode;

    /**
     * (string) ISO 2 Country code (ISO 3166-1 alpha-2)
     */
	public $countryCode;

	public $cc2;

	public $admin1;

	public $admin2;

	public $admin3;

	public $admin4;

    /**
     * (int) Population
     */
	public $population;

    /**
     * (int) Elevation
     */
	public $elevation;

	public $gtopo30;

    /**
     * (string) Timezone
     */
	public $timezone;

	public function getAlternateName($isolanguage = 'en') {
		$geonameId = $this->geonameId;
		$name = '';
		$enum = new alternate_names;
	    if (!$enum->beginEnum("geonameId = $geonameId AND isolanguage = '$isolanguage'", "isPreferredName DESC LIMIT 1"))
        return $this->name;
        
	    while ($enum->nextEnum()) {
			$name .= $enum->alternate_name;
		}
		
	    $enum->endEnum();
		
		unset ($enum);
		return $name;
	}
	
	public function refreshByGeonameId($geonameId)
    {
		$sql = 'SELECT id FROM ' . $this->tableName . ' WHERE geonameId=?';
        $id = $this->database->executeScalar($sql, array($geonameId));
        return $this->refresh($id);
    }

}