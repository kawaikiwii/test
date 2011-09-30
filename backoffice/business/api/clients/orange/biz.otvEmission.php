<?php

/**
 * Project:     WCM
 * File:        biz.otvEmission.php
 *
 * @copyright   (c)2010 Relaxnews
 * @version     4.x
 *
 */
 /**
 * Definition of an newstext
 */
class otvEmission extends bizobject
{ 
    public $siteId;
   
    public $channelId;
	
    public $title;
	
	public $date;
	
	public $catcher;
	
	public $text;
		
	public $photo;
	
	public $video;
	
	public function updateText($text)
	{
		$this->text = serialize($text);
	}
	
	public function updateVideos($videos)
	{
		$this->video = serialize($videos);
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
		
		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<emission xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" >\n<date>".$this->date."</date>\n<titre><![CDATA[".$this->title."]]></titre>\n<accroche><![CDATA[".$this->catcher."]]></accroche>\n";	
		
		if (!empty($this->text))
		{
			$text = unserialize($this->text);	
			if (sizeof($text)>0)
			{
				foreach($text as $data)
				{
					if (!empty($data["subtitle"]) && !empty($data["paragraph"]))
					{
						$xml .= "<texte>\n";
						$xml .= "<sousTitre>".relaxGUI::removeSpecialChar($data["subtitle"])."</sousTitre>\n";
						$xml .= "<paragraphe><![CDATA[".relaxGUI::removeSpecialChar($data["paragraph"])."]]></paragraphe>\n";		
						$xml .= "</texte>\n";
					}
				}
			}
		}
		
		if (!empty($this->photo)) 
			$xml .= "<photo>".basename($this->photo)."</photo>\n";
		
		if (!empty($this->video))
		{
			$xml .= "<video>\n";
			$video = unserialize($this->video);
			if (sizeof($video["video"])>0)
			{
				foreach($video["video"] as $vid)
				{
					$getVideo = explode(".",$vid);
					if (sizeof($getVideo)>1)
					{
						if(substr($vid,-3) == "wmv")
							$xml .= "<src>".$vid."</src>\n";
					}
					else
					{ 
						if (!empty($vid)) $xml .= "<src>".$vid.".wmv</src>\n";
					}	
				}
			}
			$xml .= "</video>\n";
		}
		
		$xml .= "</emission>";
		  
		return $xml;
	}
	
	public function save($source = null, $skipGenerate = false)
	{
		return parent::save($source);
    }
	
}

