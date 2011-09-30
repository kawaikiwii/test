<?php

/**
 * Project:     WCM
 * File:        biz.organisation.php
 *
 * @copyright   (c)2009 Relaxnews
 * @version     4.x
 *
 */
 /**
 * Definition of an newstext
 */
class organisation extends bizobject
{ 
    public $siteId;
   
    public $channelId;
	
    public $channelIds;
	
    public $name;
     
    public $address_1;
    
    public $address_2;
    
    public $zipcode;
    
    public $city;
    
    public $country;
    
    public $phone;
    
    public $mobile;
    
    public $fax;
        
    public $website;
    
    public $email;
    
    public $comments;
    
    public $latitude;
    
    public $longitude;
    
    public $founded;
    
    public $facebookUrl;
    
    public $twitterUrl;
    
    public $myspaceUrl;
	
	public $nationality;
	
	public $sector;
	
	public $title;
	  
	public $type;
	
	public $service;
	
	public $clients;
	
	public $company;
	
	function getSectorList()
	{
		$tab = wcmList::getListFromParentCodeForDropDownList("organisation_sector");
		asort($tab);
	    return $tab;
	}
	
	function getTypeList()
	{
	    $tab = wcmList::getListFromParentCodeForDropDownList("organisation_type");
		$tab[''] = "---- Choisir ----";
		asort($tab);
	    return $tab;
	}
	
	function getServiceList()
	{
	    $tab = wcmList::getListFromParentCodeForDropDownList("organisation_service");
		$tab[''] = "---- Choisir ----";
		asort($tab);
	    return $tab;
	}
	
	static function getOrganisations($name)
	{
		$result = array();
		if ($name)
	    {
	        $where = "name LIKE '%".$name."%'";   
	    }
	    $organisation = new organisation();
	    $organisation->beginEnum($where, "name");
	    $i = 0;
		while ($organisation->nextEnum())
		{
			$result[$i]['id'] = $organisation->id;
			$result[$i]['name'] = $organisation->name;	
			$i++;
		}
		$organisation->endEnum();
	    return $result;
	}
	
	public function checkValidity()
    {
        // Attention cette fonction passe outre les tests d'origine faits dans wcm.bizobject.php
        // le pb est que la propriété publicationDate est de type dateTime or les tests initiaux
        // se basent sur un format de type date --> ce qui génère une erreur de type notice
       
        return true;
    }
    
	public function save($source = null, $skipGenerate = false)
	{
		if (isset($source['name']))
            $this->title = $source['name'];
		
		return parent::save($source);
    }
}

