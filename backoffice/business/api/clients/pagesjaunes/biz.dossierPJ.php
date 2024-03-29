<?php

/**
 * Project:     WCM
 * File:        biz.dossierPJ.php
 *
 * @copyright   (c)2011 Relaxnews
 * @version     4.x
 *
 */
 /**
 * Definition of a place
 */

class dossierPJ extends bizobject
{ 
    /**
     * (int) site id
     */
    public $siteId;
   
    /**
     * (int) channel id
     */
    public $channelId;
	
     /**
     * (array int) channel ids
     */   
	 public $channelIds;
    /**
     * (array int) list ids
     */   
    public $listIds;
	
	 /**
     * (array int) folders ids
     */   
	public $folderIds;
   
   
    /**
     * (string) default title
     */
    public $title;
    
    public $startDate;
    public $header;
    public $description;
    public $photoUrl;
    public $photoCredit; 
    public $search;
	public $searchType;
	public $guid;
    
    /**
     * Set all initial values of an object
     * This method is invoked by the constructor
     */
    protected function setDefaultValues()
    {
    	$timestamp = time();
        $date_time_array = getdate($timestamp);
       
        $hours = $date_time_array['hours'];
        $minutes = $date_time_array['minutes'];
        $seconds = $date_time_array['seconds'];
        $month = $date_time_array['mon'];
        $day = $date_time_array['mday'];
        $year = $date_time_array['year'];
       
        $timestamp = mktime($hours, $minutes, $seconds, $month+6, $day, $year);
		
        parent::setDefaultValues();
        
    	$this->siteId = $this->channelId = 0;
    	//$this->versionNumber = 0;
    	// init workflowState
    	$this->workflowState = "published";
    }
   
	function getSearchType()
	{
	    return array( "pro"=>"Pro", "evt"=>"Evt");
	}
	
	public function checkValidity()
    {
     	if (empty($this->id) && !empty($this->guid))
		{
			$check = $this->testExistingGUID($this->guid);
			if (!empty($check))
			{
				$this->setErrorMsg("GUID existant!");
				return false;
			}
		}		
       
        return true;
    }
	
    public function resampleImage()
    {
    	$config = wcmConfig::getInstance();
    	
    	if (!empty($this->photoUrl))
    	{
    		// init chemin et nom des images
    		$path = $config['wcm.webSite.repository']."client/pagesjaunes/";
    		$file = basename($this->photoUrl); 		
    		$file2 = basename($this->photoUrl, ".jpg");
    		
    		if (file_exists($path.$file))
    		{ 		
		    	$source = imagecreatefromjpeg($path.$file);
				$destination = imagecreatetruecolor(430, 193);
				
				// Les fonctions imagesx et imagesy renvoient la largeur et la hauteur d'une image
				$largeur_source = imagesx($source);
				$hauteur_source = imagesy($source);
				$largeur_destination = imagesx($destination);
				$hauteur_destination = imagesy($destination);
				
				imagecopyresampled($destination, $source, 0, 0, 0, 0, $largeur_destination, $hauteur_destination, $largeur_source, $hauteur_source);
				imagejpeg($destination, $path.$file2."_p.jpg");
				
				return true;
    		}
    		else 
    			return false;
    	}
    	else
    		return false;
    }
    
	public function save($source = null, $skipGenerate = false)
	{	
		// on supprime la virgule à la fin de la chaine si elle est présente
		if (!empty($source["search"]) && (substr($source["search"], -1) == ","))
				$source["search"] = substr($source["search"], 0, -1);	
					
		if (parent::save($source))
		{
			// rééchantillonnage de l'image
			$this->resampleImage();
			// force indexation
			//$this->index();
        	return $this->generate(false);  
        	
		} 	
        else
			return false;
    }
	
   
    
	/**
	 * Gets the 'semantic' text that will be passed to the Text-Mining Engine
	 *
	 * @return string The semantic text to mine
	 */
	public function getSemanticText()
	{
	    $content = '';
	
		if ($this->title)
		    $content .= trim($this->title, " \t\n\r\0\x0B.").".\n";
		
		if ($this->xmlTags != NULL)
		{
			foreach($this->xmlTags['tags'] as $tag)
			{
				$content .= ','.$tag;
			}
		}
		
		$contents = $this->getContents();
		
		if (isset($contents) && is_array($contents))
		{
			foreach ($contents as $contentItem)
			{
				if ($contentItem->description != NULL)
				    $content .= trim($contentItem->description, " \t\n\r\0\x0B.").".\n";
				if ($contentItem->text != NULL)
				    $content .= trim($contentItem->text, " \t\n\r\0\x0B.").".\n";
			}
		}
		else { $content = ''; }
		
		return $content;
	}
		
 	/*
	 * supprimer les caractère spéciaux dans le XML
	 */
	public function putSafeText($text)
    {
    	$text = str_replace("&", "&amp;", $text); 
    	
    	// remplacement des caractères spéciaux
    	$text = str_replace('œ', 'oe', $text);
	    $text = str_replace('Œ', 'Oe', $text);
	    $text = str_replace('æ', 'ae', $text);
	    $text = str_replace('Æ', 'Ae', $text);
	    $text = str_replace('’', '\'', $text); 
	    $text = str_replace('…', '...', $text); 
	    	
    	return $text;
    }
    
    /*
	 * préparation de la structure XML pour l'export
	 */
    public function exportXmlStructure()
	{
		$exportDate = date("D, d M Y H:i:s O");
		
		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		$xml .= "<rss version=\"2.0\">\n";  		
		$xml .= "<channel>\n";
		$xml .= "	<title>Relaxnews</title>\n";
		$xml .= "	<link>http://www.relaxnews.com</link>\n";
		$xml .= "	<description>Relaxnews is the world's first leisure newswire service</description>\n";
		$xml .= "	<pubDate>".$exportDate."</pubDate>\n";
		$xml .= "	<generator>Relaxnews</generator>\n";
		$xml .= "	<language>fr</language>\n";
		$xml .= "	<xhtml:meta xmlns:xhtml=\"http://www.w3.org/1999/xhtml\" name=\"robots\" content=\"noindex\"/>\n";
	
		$where = "workflowstate='published'";
		$this->beginEnum($where);
        
        while ($this->nextEnum())       
		{
			// conversion du format de la date pour être conforme avec celui attendu dans le XML
			$tempDate = strtotime($this->startDate);
			$sDate = date("Y/m/d", $tempDate);		
			
			$xml .= "<item>\n";
			// utilisation d'une structure de variable dynamique pour récpérer les différentes données des sites/langues
			$xml .= "<startDate>".$sDate."</startDate>\n";
			$xml .= "<source>Relaxnews</source>\n";
			$xml .= "<guid>".$this->guid."</guid>\n";
			$xml .= "<title>".$this->putSafeText($this->title)."</title>\n";
			$xml .= "<header>".$this->putSafeText($this->header)."</header>\n";
			$xml .= "<description>".$this->putSafeText($this->description)."</description>\n";
			//$xml .= "<description><![CDATA[".$this->description."]]></description>\n";
			$xml .= "<enclosure rendition=\"w737\" url=\"".$this->photoUrl."\" type=\"image/jpeg\">\n";
			$xml .= "<credits>".$this->photoCredit."</credits>\n";
			$xml .= "</enclosure>\n";
			$urlBis = str_replace(".jpg","",$this->photoUrl);
			$urlBis = $urlBis."_p.jpg";
			$xml .= "<enclosure rendition=\"w430\" url=\"".$urlBis."\" type=\"image/jpeg\">\n";
			$xml .= "<credits>".$this->photoCredit."</credits>\n";
			$xml .= "</enclosure>\n";
			
			if ($this->searchType == "pro")
				$xml .= "<search type=\"".$this->searchType."\">".$this->search."</search>\n";
			else 
			{
				$searchEvt = "";
				$item = explode(",", $this->search);
				$i = 0;
				foreach ($item as $searchItem)
				{
					if ($i != 0) $searchEvt .= ",";
					$searchEvt .= "cat:".$searchItem;
					$i++;
				}
				$xml .= "<search type=\"".$this->searchType."\">".$searchEvt."</search>\n";
			}	
			$xml .= "</item>\n";			
		}
		
		$this->endEnum();
		
		$xml .= "</channel>\n";
		$xml .= "</rss>\n";
		return $xml;
	}
	
	public function exportHtmlStructure()
	{
		$exportDate = date("D, d M Y H:i:s O");
		
		$html = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
		$html .= "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n";
		$html .= "<head>\n";
    	$html .= "<title>Dossiers Pagesjaunes</title>\n";
    	$html .= "<meta http-equiv=\"Content-Type\" content=\"text/html;charset=UTF-8\" />\n";
		$html .= "</head>\n";
    	$html .= "<body>\n";
		
		$where = "workflowstate='published'";
		$this->beginEnum($where, "startDate ASC");
        
        while ($this->nextEnum())       
		{
			// conversion du format de la date pour être conforme avec celui attendu dans le XML
			$tempDate = strtotime($this->startDate);
			$sDate = date("Y/m/d", $tempDate);		
			
			// utilisation d'une structure de variable dynamique pour récpérer les différentes données des sites/langues
			$html .= "<div style=\"font-family:verdana\">Date planning : ".$sDate." (id du dossier : ".$this->guid.")<br />\n";
			
			$html .= "<h1>".$this->title."</h1>\n";
			$html .= "<h2>".$this->header."</h2>\n";
			$html .= "<p style='width: 737px;text-align:justify'>".$this->description."</p>\n";
			
			$html .= "<img src='$this->photoUrl' border='0'><br />\n";
			$html .= "<p>Crédits : ".$this->photoCredit."</p>\n";
			
			$html .= "</div><br /><hr align='left' width='737'><br />\n";			
		}
		
		$this->endEnum();
		
		$html .= "</body>\n";
		$html .= "</html>\n";
		return $html;
	}
	
	public function refreshByGUID($guid) 
	{
        $sql = 'SELECT id FROM '.$this->getTableName().' WHERE guid=?';
        $id = $this->database->executeScalar($sql, array($guid));
        return $this->refresh($id);
    }
	
	public function testExistingGUID($guid) 
	{
        $sql = 'SELECT id FROM '.$this->getTableName().' WHERE guid=?';
        $id = $this->database->executeScalar($sql, array($guid));
        return $id;
    }
}

