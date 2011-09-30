<?php 
/**
 * Project:     WCM
 * File:        wcm.carbeo.php
 * copie de:	backoffice/business/import/plugins/migration/wcm.migration.php
 *
 * @copyright   (c)2009 Nstein Technologies / 2010 Relaxnews
 * @version     4.x
 *
 */


class wcmImportCarbeo {
    protected $wcmBiz = null;
    protected $wcmSys = null;
    protected $siteId = 0;
    
    public $wkflow = array("Usable"=>'published', "ValidationPending"=>'submitted', "Valid"=>'approved');
	
    public function __construct(array $parameters) {
        $this->siteId = $parameters['siteId'];
    }
    
    /**
     * Launch importation process
     */
	
    public function process() {
        //$this->connectToBizDatabase("mysql.afprelax.local", "RELAX_BIZ", "relaxweb", "kzq!2007", 3306);
        //$this->connectToSysDatabase("mysql.afprelax.local", "RELAX_SYS", "relaxweb", "kzq!2007", 3306);
        $this->importNews();
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */
	
    public function connectToBizDatabase($hostname, $dbname, $username, $pw, $port) {
        try {
            $this->wcmBiz = new PDO("mysql:host=$hostname; dbname=$dbname", $username, $pw);
        }
        catch(PDOException $e) {
            echo("Connexion à la base de données $dbname impossible : ".$e->getMessage()."\r\n");
            $this->wcmBiz = null;
        }
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */

    public function connectToSysDatabase($hostname, $dbname, $username, $pw, $port) {
        try {
            $this->wcmSys = new PDO("mysql:host=$hostname; dbname=$dbname", $username, $pw);
        }
        catch(PDOException $e) {
            echo("Connexion à la base de données $dbname impossible : ".$e->getMessage()."\r\n");
            $this->wcmSys = null;
        }
    }

    public function remove_accents($str, $charset='utf-8')
	{
	    $str = htmlentities($str, ENT_NOQUOTES, $charset);
	    
	    $str = preg_replace('#\&([A-za-z])(?:acute|cedil|circ|grave|ring|tilde|uml)\;#', '\1', $str);
	    $str = preg_replace('#\&([A-za-z]{2})(?:lig)\;#', '\1', $str); // pour les ligatures e.g. '&oelig;'
	    $str = preg_replace('#\&[^;]+\;#', '', $str); // supprime les autres caractères
	    
	    return $str;
	}
	
    /**
     * Enter description here...
     *
     * @return unknown
     */

    public function importNews() 
    {
        $config = wcmConfig::getInstance();
		$urlRepository = $config['wcm.webSite.urlRepository'];	//pour URL des images du dataContent
		$repository = $config['wcm.webSite.repository'];			
	 	$xmlSaveFile = "carbeo-".date("Ymd-His").".xml";
		
		$xmlFile = "http://www.carbeo.com/rss/moy_extreme_nat.xml";
		$xslFile = dirname( __FILE__ )."/transform.xsl";
		$newXmlFile = $repository."import/carbeo/".$xmlSaveFile;
		$test = true;
		
		if($handle = fopen($xmlFile, 'rb')) {
			$strXml = stream_get_contents($handle);
			$domXml = new DOMDocument();
        	if(!@$domXml->loadXML($strXml)) {
        		die("Erreur en chargement de la chaine XML !\r\n");
				$test = false;
        	} else {
        		$ref_date = "";
        		$rootNode = $domXml->documentElement;
				foreach($rootNode->childNodes as $child) {
					if($child->nodeName == "date") {
						$ref_date = substr($child->nodeValue, 0, 10);
					}
				}
				if($ref_date == date("Y-m-d")) {
					echo "Date de référence du XML -> $ref_date : Ok !\r\n";
					$domXml->save($newXmlFile);
			   		$wcmXml = new wcmXML();
					if(!$text = @$wcmXml->processXSLT($strXml, $xslFile, array("urlRepository"=>$urlRepository))) {
						die("Erreur lors de la transformation XSL !\r\n");
						$test = false;
					}
				} else {
					echo "Le fichier XML ne référence pas la bonne date (balise date en fin de fichier) -> $ref_date !\r\n";
					$test = false;
				}
			}
		} else {
			echo "Erreur en chargement du XML (URL) !\r\n";
			$test = false;
		}

		if($test) {
	    	$news = new news();
		    $news->siteId = $this->siteId;
		    $news->title = "Baromètre national hebdomadaire du prix des carburants";
		    
		    $news->createdAt = date('Y-m-d H:i:s');
		    $news->createdBy = 22664;	//Robot Baromètre carburants
		    $news->modifiedAt  = date('Y-m-d H:i:s');
		    $news->modifiedBy = 22664;
		    $news->publicationDate = date('Y-m-d H:i:s');
	        //$news->embargoDate = date('Y-m-d H:i:s');
	        //$news->expirationDate = date('Y-m-d H:i:s');
		        
		    $news->channelId = 240;
		    $tabChan = array(240, 239, 238, 241);
		    $tabList = array(102, 232, 234);
		   
		    $news->workflowState = "approved";
		    		    
	        $news->channelIds = serialize($tabChan);
	       
	        $news->listIds = serialize($tabList);
	
	        $news->source = 12;
	        
	        $news->import_feed = "carbeo";
			$news->import_feed_id = $xmlSaveFile;
	            
	        $news->mustGenerate = true;
	        
	        $news->save();
	        
			$newsId = $news->id;
	        
	        if ($newsId != 0) {
				$description = "Chaque semaine, le Relaxfil, en partenariat avec le site <a target='_blank' href='http://www.carbeo.com'>Carbeo.com</a>, site communautaire de collecte des prix des carburants en France, publie le baromètre national du prix des carburants (SP98, SP95, SP95-E10, Gasoil, Gasoil Plus, GPL et E85).";
	
				$dataContent = new content();
				$dataContent->title = $news->title;
				$dataContent->referentId = $newsId;
				$dataContent->referentClass = "news";
				$dataContent->description = $description;
				$dataContent->text = $text;
				$dataContent->save();
				
				$photoRelation = new wcmBizrelation();
				$photoRelation->title="Carburants";
				$photoRelation->sourceClass = "news";
				$photoRelation->sourceId = $newsId;
				$photoRelation->destinationClass = "photo";
				$photoRelation->destinationId = 5576;
				$photoRelation->kind = 3;
				$photoRelation->rank = 1;
				$photoRelation->media_text = "";
				$photoRelation->media_description = "";
				$photoRelation->save();
				
	        	//$insert2 = 'INSERT INTO `biz_content` (`referentId`, `referentClass`, `format`, `lang`, `provider`, `authorId`, `title`, `titleContentType`, `titleSigns`, `titleWords`, `description`, `descriptionContentType`, `descriptionSigns`, `descriptionWords`, `text`, `textContentType`, `textSigns`, `textWords`) VALUES ( '.$newsId.', "news", "default", "fr", "icm", 1, \''.$news->title.'\', "text", NULL, NULL, \''.$description.'\', "xhtml", NULL, NULL, \''.$text.'\', "xhtml", NULL, NULL)';
	            //$this->wcmBiz->exec($insert2);
	        	echo "Creation de la news: ".$newsId."\r\n";
			} else {
				echo "Erreur en création de la news !\r\n";
			}
		} else {
			echo "Au moins une erreur a été trouvée : Abandon de la création !\r\n";
		}
	}
}
