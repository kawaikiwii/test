<?php 
/**
 * Project:     WCM
 * File:        biz.alloCineTopBA.php
 * copie de:	backoffice/business/import/plugins/migration/wcm.migration.php
 *
 * @copyright   (c)2009 Nstein Technologies / 2010 Relaxnews
 * @version     4.x
 *
 */

class wcmImportAlloCineTopBA {
    protected $wcmBiz = null;
    protected $wcmSys = null;
    protected $siteId = 0;
    protected $top = 5;
    
    public $wkflow = array("Usable"=>'published', "ValidationPending"=>'submitted', "Valid"=>'approved');
	
    public function __construct(array $parameters) {
        $this->siteId = $parameters['siteId'];
        $this->top = $parameters['top'];
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

    public function remove_accents($str, $charset='utf-8') {
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

    public function importNews() {
    	echo "connexion FTP en cours\n";
    	// définition des paramètres de connexion FTP et des répertoire distants
		$ftp_server 	= "ftp.relaxfil.com";
		$ftp_user 		= "admin";
		$ftp_pass 		= "ie1frn";
		$path			= "/relaxfil/incoming/allocine/";
		$test			= "true";

		// Mise en place de la connexion ftp
		$conn_id = ftp_connect($ftp_server) or die("erreur connexion FTP $ftp_server");
		// Tentative d'identification
		if (ftp_login($conn_id, $ftp_user, $ftp_pass)) {
		    echo "Connexion FTP réussie : $ftp_server \r\n";
		} else {
		    echo "Connexion FTP impossible en tant que $ftp_user !\r\n";
			$test = "false";
		}

		if (ftp_chdir($conn_id, $path)) {
			if($this->top == 5) {
				$remote_file = "AlloCine_Top5_BA_Lundi.xml";	//nom du fichier XML du jour
			} else if($this->top == 10) {
				$remote_file = "AlloCine_Top10_BA_Jeudi.xml";			
			}
			$local_file = "/opt/nfs/production/automats/scripts/imports/allocine/ftp_temp.txt";
							
			// création d'un fichier temporaire pour l'upload sur le FTP
			if(!file_exists($local_file)) {
				die("Fichier local ftp_temp.txt non trouvé !\r\n");
				$test = "false";
			} else {
				$tempHandle = fopen($local_file, "w+");
				if (@ftp_fget($conn_id, $tempHandle, $remote_file, FTP_ASCII, 0)) {
					echo "Ecriture dans le fichier ftp_temp.txt avec succès\r\n";
					if(@ftp_rename($conn_id, $remote_file, $remote_file."_".date('YmdHis').".bak")) {
						echo "Fichier source $remote_file renommé !\r\n";
					} else {
						echo "Erreur lors du renommage du fichier source $remote_file !\r\n";
					}
					rewind($tempHandle); 
					$strXml = stream_get_contents($tempHandle);
					// Fermeture de la connexion et du pointeur de fichier
					fclose($tempHandle);
			 		$xmlSaveFile = "20780-".date("YmdHis")."-top".$this->top."BA.xml";
					
			        $config = wcmConfig::getInstance();
					$urlRepository = $config['wcm.webSite.urlRepository'];	//pour URL des images du dataContent
					$repository = $config['wcm.webSite.repository'];
					$newXmlFile = $repository."import/allocine/".$xmlSaveFile;
					
					$domXml = new DOMDocument();
		        	if(!@$domXml->loadXML($strXml)) {
		        		die("Erreur en chargement de la chaine XML issue du fichier ftp_temp.txt\r\n");
						$test = "false";
		        	} else {
						if($domXml->save($newXmlFile)) {
							echo "Sauvegarde copie du xml dans ".$newXmlFile."\r\n";
						}			        	
			       		$wcmXml = new wcmXML();
						$xslFile = dirname( __FILE__ )."/AlloCine_Top".$this->top."_BA.xsl";
						if(!$text = @$wcmXml->processXSLT($strXml, $xslFile, array("urlRepository"=>$urlRepository))) {
							echo "Erreur lors de la transformation XSLT !\r\n";
							$test = "false";
						} else {
							echo "Transformation XSLT : Ok !\r\n";
						}
					}
				} else {
					echo "Problème lors du téléchargement du fichier $remote_file dans ftp_temp.txt\r\n";
					$test = "false";
				}
		 	}
			ftp_close($conn_id);
		} else {
			echo "Problème lors du changement de répertoire !\r\n";
			$test = "false";
		}
		if($test=="true") {
	    	$news = new news();
		    $news->siteId = $this->siteId;
		    $news->title = "Top $this->top des bandes-annonces ciné les plus consultées sur Internet";
		    echo "Title : $news->title\r\n";
		    
		    $news->createdAt = date('Y-m-d H:i:s');
		    $news->createdBy = 20780;	//Robot Audience Allociné	1531
		    $news->modifiedAt  = date('Y-m-d H:i:s');
		    $news->modifiedBy = 20780;
		    $news->publicationDate = date('Y-m-d H:i:s');
	        //$news->embargoDate = date('Y-m-d H:i:s');
	        //$news->expirationDate = date('Y-m-d H:i:s');
		        
		    $news->channelId = 197;
		    $tabChan = array(197, 195, 255);
		    $tabList = array(102, 232);
			$tabFolder = array(272);
		   
		    $news->workflowState = "approved";
		    
	        $news->channelIds = serialize($tabChan);
	       
	        $news->listIds = serialize($tabList);
	       
	        $news->folderIds = serialize($tabFolder);
	
	        $news->source = 12;
	        
	        $news->import_feed = "allocine";
			$news->import_feed_id = $xmlSaveFile;
	            
	        $news->mustGenerate = true;
	        echo "Sauvegarde en cours\r\n";
	        $news->save();
	        echo "Sauvegarde effectuée : $news->id !\r\n";
	        
			$newsId = $news->id;
	
	        if ($newsId != 0) {				
				$description = "<p><em>Relaxnews</em> réalise en partenariat avec le site AlloCiné (<a href=\"http://www.allocine.fr/\" target=\"_blank\">www.allocine.fr</a>) le top $this->top des bandes-annonces les plus consultées";
				if($this->top == 5) $description .= " pour les films à sortir le mercredi suivant";
				$description .= ".</p>";
	
				$dataContent = new content();
				$dataContent->title = $news->title;
				$dataContent->referentId = $newsId;
				$dataContent->referentClass = "news";
				$dataContent->description = $description;
				$dataContent->text = $text;
				$dataContent->save();
				
				$photoRelation = new wcmBizrelation();
				$photoRelation->title = $news->title;
				$photoRelation->sourceClass = "news";
				$photoRelation->sourceId = $newsId;
				$photoRelation->destinationClass = "photo";
				if($this->top == 5) {
					$photoRelation->destinationId = 53984;
				} else {
					$photoRelation->destinationId = 24225;
				}
				$photoRelation->kind = 3;
				$photoRelation->rank = 1;
				$photoRelation->media_text = "";
				$photoRelation->media_description = "";
				$photoRelation->save();
				
	        	echo "Creation de la news: ".$newsId."\r\n";
			} else {
				echo "Erreur en création de la news !\r\n";
			}	
		} else {
			echo "Au moins une erreur a été trouvée : Abandon de la création !\r\n";
		}	
	}
}
