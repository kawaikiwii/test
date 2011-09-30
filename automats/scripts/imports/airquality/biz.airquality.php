<?php 
/**
 * Project:     WCM
 * File:        wcm.airquality.php
 * copie de:	backoffice/business/import/plugins/migration/wcm.migration.php
 *
 * @copyright   (c)2009 Nstein Technologies / 2010 Relaxnews
 * @version     4.x
 *
 */


class wcmImportAirquality {
    protected $wcmBiz = null;
    protected $wcmSys = null;
    protected $siteId = 0;
    /*
    protected $step = 0;
    protected $currentNewsFolder = 990;
    */
    
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
     * Connection to BIZ database
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
     * Connection to SYS database
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
	
	private function traduct($town, $name_en) {
		switch($town) {
			case "london":
				$strName = "Londres [London]";
				break;
			case "brussels":
				$strName = "Bruxelles [Brussels]";
				break;
			case "padova":
				$strName = "Padoue [Padova]";
				break;
			case "basel":
				$strName = "Bâle [Basel]";
				break;
			case "cordoba":
				$strName = "Cordoue [Córdoba]";
				break;
			case "granada":
				$strName = "Grenade [Granada]";
				break;
			case "sevilla":
				$strName = "Séville [Sevilla]";
				break;
			case "cadiz":
				$strName = "Cadix [Cádiz]";
				break;
			default:
				$strName = $name_en;
				break;
		}
		return $strName;
	}
	
	private function country($town) {
		switch($town) {
			case "amsterdam":
				$strName = "(Pays-Bas)";
				break;
			case "berlin": case "munich": case "stuttgart": case "karlsruhe": case "freiburg": case "mannheim":
				$strName = "(Allemagne)";
				break;
			case "london": case "bristol": case "coventry": case "leicester": case "chichester": case "horsham": case "lewes": case "eastbourne": case "storrington":
				$strName = "(Royaume-Uni)";
				break;
			case "brussels":
				$strName = "(Belgique)";
				break;
			case "gdansk": case "gdynia": case "sopot": case "tczew":
				$strName = "(Pologne)";
				break;
			case "oslo":
				$strName = "(Norvège)";
				break;
			case "padova":
				$strName = "(Italie)";
				break;
			case "prague": case "brno":
				$strName = "(Rép. Tchèque)";
				break;
			case "rotterdam":
				$strName = "(Pays-Bas)";
				break;
			case "salzburg": case "linz": case "graz": case "innsbruck":
				$strName = "(Autriche)";
				break;
			case "zurich": case "basel":
				$strName = "(Suisse)";
				break;
			case "almeria": case "bahia_algeciras": case "cadiz": case "cordoba": case "granada": case "jaen": case "jerez": case "malaga": case "madrid": case "seville": case "huelva":
				$strName = "(Espagne)";
				break;
			case "maribor":
				$strName = "(Slovénie)";
				break;
			case "andorra":
				$strName = "(Andorre)";
				break;
				default:
				$strName = "";
				break;
		}
		return $strName;
	}
	
    /**
     * News creation
     *
     * @return unknown
     */

    public function importNews() 
    {
		$test = true;
 		$xmlSaveFile = "airquality-".date("Ymd-His").".xml";
        $config = wcmConfig::getInstance();
		$repository = $config['wcm.webSite.repository'];
		
        $xslFile = dirname( __FILE__ )."/transform.xsl";
        $xmlFile = "http://www.airqualitynow.eu/airquality.xml";
		
		if($handle = fopen($xmlFile, 'rb')) {
			$contents = stream_get_contents($handle);
			fclose($handle);
			$strXml = str_replace(' xmlns="http://www.airqualitynow.eu/ns/airquality"', '', $contents);
			$domXml = new DOMDocument();
			if(!@$domXml->loadXML($strXml)) {
        		die("Erreur en chargement de la chaine XML !\r\n");
				$test = false;
        	} else {
				$rootNode = $domXml->documentElement;
				$ref_date = substr($rootNode->getAttribute("update"), 0, 10);
				if($ref_date == date("Y-m-d")) {
					echo "Date de référence du XML -> $ref_date : Ok !\r\n";
					$cities = $rootNode->getElementsByTagName("city");
					foreach($cities as $city) {
						$cityName = $city->getAttribute("name");
						$cityId = $city->getAttribute("id");
						$newCityName = $this->traduct($cityId, $cityName)."<br/>".$this->country($cityId);
						$city->setAttribute("name", $newCityName);
					}
					
					$newXmlFile = $repository."import/airquality/".$xmlSaveFile;
					$domXml->save($newXmlFile);
					$handle = fopen($newXmlFile, 'rb');
					$strXml = stream_get_contents($handle);
					fclose($handle);
			
			   		$wcmXml = new wcmXML();
					if(!$text = @$wcmXml->processXSLT($strXml, $xslFile, null)) {
						die("Erreur lors de la transformation XSL !\r\n");
						$test = false;
					}
				} else {
					echo "Le fichier XML ne référence pas la bonne date (attribut 'update' de la balise 'airquality') -> $ref_date !\r\n";
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
		    $news->title = "Indices quotidiens de la qualité de l'air en France et en Europe";
		    
		    $news->createdAt = date('Y-m-d H:i:s');
		    $news->createdBy = 23143;	//Robot Imports Externes
		    $news->modifiedAt  = date('Y-m-d H:i:s');
		    $news->modifiedBy = 23143;
		    $news->publicationDate = date('Y-m-d H:i:s');
	        //$news->embargoDate = date('Y-m-d H:i:s');
	        //$news->expirationDate = date('Y-m-d H:i:s');
		        
		    $news->channelId = 227;
		    $tabChan = array(227, 211, 242);
		    $tabList = array(102, 232, 235);
		   
		    $news->workflowState = "approved";
	      
	        $news->channelIds = serialize($tabChan);
	       
	        $news->listIds = serialize($tabList);
	
	        $news->source = 12;
	        
	        $news->import_feed = "airquality";
			$news->import_feed_id = $xmlSaveFile;
	            
	        $news->mustGenerate = true;
	        
	        $news->save();
	        
			$newsId = $news->id;
	        
	        if ($newsId != 0) 
	        {
				$description = "<p>Chaque jour, le Relaxfil en partenariat avec CiteAIR diffuse les indices de la qualité de l'air constatés la veille et prévus pour la journée pour des villes européennes et françaises, dont Clermont-Ferrand, Le Havre, Paris, Reims, Rouen, Strasbourg et Toulouse.</p>";
				$dataContent = new content();
				$dataContent->title = $news->title;
				$dataContent->referentId = $newsId;
				$dataContent->referentClass = "news";
				$dataContent->description = $description;
				$dataContent->text = str_replace('<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd"> ','',$text);
				$dataContent->save();
				
				$photoRelation = new wcmBizrelation();
				$photoRelation->title="Qualité de l'air";
				$photoRelation->sourceClass = "news";
				$photoRelation->sourceId = $newsId;
				$photoRelation->destinationClass = "photo";
				$photoRelation->destinationId = 13245;
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
