<?php

/**
 * Project:     WCM
 * File:        biz.person.php
 *
 * @copyright   (c)2009 Relaxnews
 * @version     4.x
 *
 */
 /**
 * Definition of an newstext
 */
class person extends bizobject
{ 
    public $siteId;
   
    public $channelId;
	
    public $channelIds;
	
    public $nickname;
    
    public $firstname;
    
    public $lastname;
    
    public $born;
    
    public $jobtitle;
    
   	public $company;
   	
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
    
    public $civility;
    
    public $deceaseDate;
    
    public $facebookUrl;
    
    public $twitterUrl;
    
    public $myspaceUrl;
	
    public $nationality;
    
    public $title;
	/**
	* civility 
	*/
	function getCivility()
	{
	    return array( "Mr", "Mme", "Melle");
	}
	
	function getNationalityList()
	{
	    return wcmList::getListFromParentCodeForDropDownList("nationality");
	}
	
	function getJobtitleList()
	{
	    return wcmList::getListFromParentCodeForDropDownList("person_jobtitle");
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
		if (isset($source['lastname']) && isset($source['firstname']))
            $this->title = $source['lastname']." ".$source['firstname'];
		return parent::save($source);
    }
	
	
}

