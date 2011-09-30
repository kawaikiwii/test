<?php 
/**
 * Project:     WCM
 * File:        biz.country.php
 *
 * @copyright   (c)2010 Relaxnews
 * @version     4.x
 * @author		LSJ
 *
 */
 /**
 * Definition of class country : Countries informations
 */
class country extends bizobject
{
    /**
     * (int) Geoname ID (index)
     */
	public $geonameId;

    /**
     * (string) ISO 2 code (ISO 3166-1 alpha-2)
     */
	public $ISO;

    /**
     * (string) ISO 3 code (ISO 3166-1 alpha-3)
     */
	public $ISO3;

    /**
     * (string) Country name (english)
     */
	public $country;

    /**
     * (string) Capital name (english)
     */
	public $capital;

    /**
     * (int) Area (km²)
     */
	public $area;

    /**
     * (int) Population
     */
	public $population;

    /**
     * (string) Continent code
     */
	public $continent;

    /**
     * (string) Internet suffix
     */
	public $tld;

    /**
     * (string) Currency code
     */
	public $currency_code;

    /**
     * (string) Currency name
     */
	public $currency_name;

    /**
     * (string) International prefix code
     */
	public $int_phone_code;

    /**
     * (string) Postal code format
     */
	public $postal_code_format;

    /**
     * (string) Regex validator postcode
     */
	public $postal_code_regex;

    /**
     * (string) Languages codes used in the country
     */
	public $languages;

    /**
     * (string) Neighbours codes
     */
	public $neighbours;

	public function getAlternateName($isolanguage = 'en') {
		$geonameId = $this->geonameId;
		$name = '';
		$enum = new alternate_names;
	    if (!$enum->beginEnum("geonameId = $geonameId AND isolanguage = '$isolanguage'", "isPreferredName DESC LIMIT 1"))
        return $this->country;
        
	    while ($enum->nextEnum()) {
			$name .= $enum->alternate_name;
		}
		
	    $enum->endEnum();
		
		unset ($enum);
		return $name;
	}
	
	public function getCountryNameByCode($iso)
	{
		$sql = "SELECT country FROM #__country WHERE iso='".$iso."'";
        return $this->database->executeScalar($sql);
	}
	
	public function getCountryCodeByGeonameId($geonameId)
	{
		$sql = "SELECT ISO FROM #__country WHERE geonameId='".$geonameId."'";
        return $this->database->executeScalar($sql);
	}
}
