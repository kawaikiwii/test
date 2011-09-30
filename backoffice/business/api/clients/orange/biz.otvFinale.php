<?php

/**
 * Project:     WCM
 * File:        biz.otvFinale.php
 *
 * @copyright   (c)2010 Relaxnews
 * @version     4.x
 *
 */
 /**
 * Definition of an newstext
 */
class otvFinale extends bizobject
{ 
    public $siteId;
   
    public $channelId;
	
    public $title;
	
    public $subtitle;
    
	public $type;
	
	public $number;
	
	public $lastname;
	
	public $firstname;
	
	public $date;
	
	public $description;
	
	public $status;
	
	public $video;
	
	public $type2;
	
	public $number2;
	
	public $lastname2;
	
	public $firstname2;
	
	public $date2;
	
	public $description2;
	
	public $status2;
	
	public $video2;
	
	public function updateText($description)
	{
		$this->description = serialize($description);
	}
	
	public function updateText2($description)
	{
		$this->description2 = serialize($description);
	}
	
	public function getFinalNumber()
    {
    	$arrayType = array();
    	$otvPortrait = new otvPortrait();
    	$otvPortrait->beginEnum("status=1");
    	
    	$arrayType[0] = "Select Number";
	    while ($otvPortrait->nextEnum())
			$arrayType[$otvPortrait->number] = $otvPortrait->number;

		$otvPortrait->endEnum();
	    return $arrayType;
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
		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<finale>\n<chapeau><![CDATA[".$this->subtitle."]]></chapeau>\n";
		
		//debut finaliste 1
		$xml .= "<portrait>\n<genre>".$this->type."</genre>\n<id>".$this->number."</id>\n<nom>".$this->lastname."</nom>\n<prenom>".$this->firstname."</prenom>\n<date>".$this->date."</date>\n<description>\n";
		$description = unserialize($this->description);
		
		if (sizeof($description)>0)
		{
			foreach($description as $data)
			{
				if (!empty($data["subtitle"]) && !empty($data["paragraph"]))
				{
					$xml .= "<sousTitre>".$data["subtitle"]."</sousTitre>\n";
					$xml .= "<paragraphe><![CDATA[".$data["paragraph"]."]]></paragraphe>\n";
				}			
			}
		}
		    
		$xml .= "</description>\n<statut>".$this->status."</statut>\n";
		
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
		
		$xml .= "</portrait>\n";
		
		//debut finaliste 2
		$xml .= "<portrait>\n<genre>".$this->type2."</genre>\n<id>".$this->number2."</id>\n<nom>".$this->lastname2."</nom>\n<prenom>".$this->firstname2."</prenom>\n<date>".$this->date2."</date>\n<description>\n";
		$description = unserialize($this->description2);
		
		if (sizeof($description)>0)
		{
			foreach($description as $data)
			{
				if (!empty($data["subtitle"]) && !empty($data["paragraph"]))
				{
					$xml .= "<sousTitre>".relaxGUI::removeSpecialChar($data["subtitle"])."</sousTitre>\n";
					$xml .= "<paragraphe><![CDATA[".relaxGUI::removeSpecialChar($data["paragraph"])."]]></paragraphe>\n";
				}			
			}
		}
		    
		$xml .= "</description>\n<statut>".$this->status2."</statut>\n";
		
		$getVideo = explode(".", $this->video2);
		if (sizeof($getVideo)>1)
		{
			if(substr($this->video,-3) == "wmv")
				$xml .= "<video>".$this->video2."</video>\n";
		}
		else
		{ 
			if (!empty($this->video)) $xml .= "<video>".$this->video2.".wmv</video>\n";
		}
		
		$xml .= "</portrait>\n";	
		$xml .= "</finale>";
		
		return $xml;
	}
	
	public function save($source = null, $skipGenerate = false)
	{
		if (isset($source['lastname']) && isset($source['lastname2']))
            $this->title = $source['lastname']." vs ".$source['lastname2'];
		return parent::save($source);
    }
	
}

