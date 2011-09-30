<?php

/**
 * Project:     WCM
 * File:        biz.otvPortrait.php
 *
 * @copyright   (c)2010 Relaxnews
 * @version     4.x
 *
 */
 /**
 * Definition of an newstext
 */
class otvPortrait extends bizobject
{ 
    public $siteId;
   
    public $channelId;
	
    public $title;
	
	public $number;
	
	public $type;
	
	public $lastname;
	
	public $firstname;
	
	public $date;
	
	public $description;
	
	public $status;
	
	public $photo;
	
	public $photoLandscape;
	
	public $video;
	
	/*
	 * Sérialise les paragraphes de la  variable description
	 */
	public function updateDescription($description)
	{
		$this->description = serialize($description);
	}
	
	/*
	 * retourne un array avec les numbers utilisés pour les portraits
	 */
	public function getSavedNumbers()
	{
		$savedNumbers = array();
        $sql  = "SELECT DISTINCT number FROM ".$this->tableName;

		$rs = $this->database->executeQuery($sql);
		if ($rs != null)
		{
		    while ($rs->next())
		    {
			$savedNumbers[] = $rs->get('number');
		    }
		}

		return $savedNumbers;
	}
	
	/*
	 * retourne un array avec les numbers disponibles pour les portraits
	 */
	public function getAvailableNumbers($all = false)
	{
		$initNumbers = array( "1"=>"1","2"=>"2","3"=>"3","4"=>"4","5"=>"5","6"=>"6","7"=>"7","8"=>"8","9"=>"9","10"=>"10","11"=>"11","12"=>"12","13"=>"13","14"=>"14","15"=>"15","16"=>"16","17"=>"17","18"=>"18","19"=>"19","20"=>"20");
		
		$savedNumbers = $this->getSavedNumbers();
		
		if (!empty($savedNumbers) && $all != true)
		{
			foreach($savedNumbers as $number)
			{
				if (isset($initNumbers[$number])) unset($initNumbers[$number]);
			}
		}
		
		return $initNumbers;
	}
	
	public function checkValidity()
    {
        // Attention cette fonction passe outre les tests d'origine faits dans wcm.bizobject.php
        // le pb est que la propriété publicationDate est de type dateTime or les tests initiaux
        // se basent sur un format de type date --> ce qui génère une erreur de type notice
       
        return true;
    }
	
    /*
	 * préparation de la structure XML pour l'export
	 */
    public function exportXmlStructure()
	{
		$config = wcmConfig::getInstance();
		$photoPortrait = "";
		$photoLandscape = "";
		
		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<portrait xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" >\n<genre>".$this->type."</genre>\n<id>".$this->id."</id>\n<nom>".$this->lastname."</nom>\n<prenom>".$this->firstname."</prenom>\n<date>".$this->date."</date>\n<description>\n";
		
		$desc = unserialize($this->description);
		if (sizeof($desc["paragraph"])>0)
		{
			foreach($desc["paragraph"] as $paragraphe)
			{
				if (!empty($paragraphe)) $xml .= "<paragraphe><![CDATA[".relaxGUI::removeSpecialChar($paragraphe)."]]></paragraphe>\n";
			}
		}
		
		$xml .= "</description>\n<statut>".$this->status."</statut>\n";

		$xml .= "<photo>\n";	
		if (!empty($this->photo)) $xml .= "<portrait>".basename($this->photo)."</portrait>\n";
		if (!empty($this->photoLandscape)) $xml .= "<paysage>".basename($this->photoLandscape)."</paysage>\n";
		$xml .= "</photo>\n";	
				
		$getVideo = explode(".", $this->video);
		if (sizeof($getVideo)>1)
		{
			if(substr($this->video,-3) == "wmv")
				$xml .= "<video>".$this->video."</video>\n";
		}
		else
		{ 
			if (!empty($this->video)) $xml .= "<video>".$this->video.".wmv</video>\n";
		}		
		
		$xml .= "</portrait>";	
		return $xml;
	}
    
	public function save($source = null, $skipGenerate = false)
	{
		if (isset($source['lastname']) && isset($source['firstname']))
            $this->title = $source['lastname']." ".$source['firstname'];
		return parent::save($source);
    }
	
}

