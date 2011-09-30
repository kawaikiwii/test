<?php
/**
 * Project:     WCM
 * File:        wcm.importAFP.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 * 
 * 
 * This class imports articles (NewsML) and photos (with IPTC metadata). The imported data structure
 * should follow the same logic as AFP (Agence France Presse) provides to its customers.
 */
class wcmImportAFP extends wcmGenericFileImport
{
    /**
     * (string) default root folder used for AFP
     * Default value is "/data/wcm/WCM/3.1/business/import/in/AFP/"
     *
     * Important: if the value does NOT start with a dot '.' the path will be
     *             considered as relative to the WCM installation folder
     */
    const DEFAULT_ROOTFOLDER = "business/import/in/AFP/";   

    /**
     * (string) default relative physical path where XSL are stored (relative to WCM installation folder)
     * Default value is "business/import/xsl/AFP/"
     *
     * Important: if the value does NOT start with a dot '.' the path will be
     *             considered as relative to the WCM installation folder
     */
    const DEFAULT_XSLFOLDER = "business/import/xsl/AFP/";
    
    /**
     * (string) default relative physical path where to store photos (relative to WCM installation folder)
     * Default value is "img/photos/AFP/"
     *
     * Important: if the value does NOT start with a dot '.' the path will be
     *             considered as relative to the WCM installation folder
     */
    const DEFAULT_PHOTOFOLDER = "img/photos/biz/";

    /**
     * (int) Site Id use for import (default value is zero)
     */
    public $siteId = 0;
    
    /**
     * (string) Path of folder where XSL can be found (the folder should contains 'article.xsl' and 'photo.xsl' files)
     *
     * Important: if the value does NOT start with a dot '.' the path will be
     *             considered as relative to the WCM installation folder
     */
    public $xslFolder;
    
    /**
     * (string) Path of folder where to copy photos (original and thumbnail)
     *
     * Important: if the value does NOT start with a dot '.' the path will be
     *             considered as relative to the WCM installation folder
     */
    public $photoFolder;
    
    /**
     * (string) URL used to find (prefix) photos from the WCM back-office
     *
     * Important: if the value does NOT start with 'http:' or 'https:' the URL will be
     *             considered as relative to the WCM back-office URL
     */
    public $photoURL;
    
    /**
     * (int) current publication id
     */
    protected $currentPublicationId = null;
    
    /**
     * (int) current issue id
     */
    protected $currentIssueId = null;
    
    /**
     * (int) current channel id
     */
    protected $currentChannelId = null;
    
    /**
    * Constructor
    *
    * @param array      $parameters  An associative array used to assign properties (by their name) and extra parameters
    */
    public function __construct(array $parameters)
    {
        parent::__construct($parameters);
        
        $config = wcmConfig::getInstance();

        // Setup default values
        $this->fileRegExp = '^index.xml$';
        if (!$this->source) $this->source = 'AFP';
        
        $this->xslFolder   = getArrayParameter($parameters, 'wcmImportAFP_xslFolder', self::DEFAULT_XSLFOLDER);
        $this->photoFolder = WCM_DIR.'/'.getArrayParameter($this->parameters, 'wcmImportAFP_destinationFolder', $config['wcm.backOffice.photosPath']);
        
        if (!$this->rootFolder) $this->rootFolder = self::DEFAULT_ROOTFOLDER;
        if (!$this->photoURL) $this->photoURL = $config['wcm.backOffice.photosPath'];
        if (empty($this->siteId)) $this->siteId = wcmSession::getInstance()->getSiteId();
        if (array_key_exists('currentPublicationId', $parameters))
            $this->currentPublicationId = $parameters['currentPublicationId'];
        if (array_key_exists('currentIssueId', $parameters))
            $this->currentIssueId = $parameters['currentIssueId'];
        $this->getTotal();
    }
    
    public function getTotal()
    {
        $total = 0;
        if (is_dir($this->rootFolder))
        {
            $dir = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->rootFolder), RecursiveIteratorIterator::SELF_FIRST
            );
    
            foreach ($dir as $d)
            {
                
                if ($d->getFilename() == 'index.xml')
                {
                    $total++;
                    
                    // This code will base the total on the number news components in each index.xml file
                    // $xml = simplexml_load_file($d->getPathname());
                    // $newsItems = $xml->xpath('//NewsItem/NewsComponent/NewsComponent');
                    // $total = $total + count($newsItems);
                }
            }
        }
        $this->total = $total;
        return $total;
    }
    
    /**
    * Launch import process
    */
    public function process()
    {
        
        // Adjust relative folders if needed
        $this->logger->logMessage('Starting imports');
        $path = WCM_DIR . '/';
        if (substr($this->rootFolder, 0, 1) == '.') $this->rootFolder = $path . $this->rootFolder;
        if (substr($this->xslFolder, 0, 1) == '.') $this->xslFolder = $path . $this->xslFolder;
        if (substr($this->photoFolder, 0, 1) == '.') $this->photoFolder = $path . $this->photoFolder;

        // Start processing (don't process file automatically)
        parent::process(false);
    }
        
    /**
    * This function is invoked each time a folder has to be processed
    * (the currentFolder property is updated before this function is invoked)
    *
    * @return bool Return FALSE to skip processing of inner files and sub-folders, true otherwise
    **/
    public function processFolder()
    {
$this->logger->logMessage('In ProcessFolder');
        $folderName = substr(strrchr($this->currentFolder, "/"), 1 );
        $this->logger->logMessage('Folder: '.$folderName);
        
        $channel = new channel(wcmProject::getInstance());
        
        // Seeking index.xml file
        if (!is_file($this->currentFolder."/index.xml"))
        {
            $this->logger->logVerbose(_BIZ_NO_INDEX_FILE_FOR_CHANNEL . $channel->title." [".$folderName."]");
            return true;
        }

        // Searching for a channel with same code as folder name and source equals to source
        
        $channel->refreshFromSource($this->source, $folderName);
        if ($channel->id)
        {
            $this->logger->logMessage(_BIZ_UPDATE_CHANNEL . $channel->title." [".$folderName."]");
        }
        else
        {
            $this->logger->logMessage(_BIZ_CREATE_CHANNEL . $folderName);
            $channel->title    = $folderName;
            $channel->sourceId = $folderName;
            $channel->source   = $this->source;
            $channel->siteId   = $this->siteId;
        }

        // Comparing last processing date with index date (processing date is stored in sourceVersion)
        $dateIndex  = date("Y-m-d H:i:s", filemtime($this->currentFolder."/index.xml"));
        $dernierImport = ($channel->sourceVersion) ? $channel->sourceVersion : "0000-00-00 00:00:00";
        if ($dateIndex <= $dernierImport)
        {
            // No change since last import
            $this->logger->logMessage(sprintf(_BIZ_INDEX_CHANNEL_UNCHANGED, $channel->title, $folderName, $dernierImport, $dateIndex ));
        }
        else
        {
            // Update last channel import date
            $channel->sourceVersion = $dateIndex;
            $channel->publicationId = $this->currentPublicationId;
            $channel->save();

            // XML parsing... 
            $domXml = @DomDocument::load($this->currentFolder."/index.xml");
            if (!$domXml)
            {
                $this->logger->logError(sprintf(_BIZ_INDEX_INVALID_XML, $this->currentFolder));
            }
            else
            {
                // Remember current channel Id
                $this->currentChannelId = $channel->id;
				$this->logger->logMessage("currentChannelId = ".$this->currentChannelId);
				 
                // Build XPath query to retrieve documents
                $xPath = new DOMXPath($domXml);
                $nodes = $xPath->query("NewsItem/NewsComponent/NewsComponent/NewsItemRef/@NewsItem");
$this->logger->logMessage("before for each ProcessFile");
                foreach($nodes as $node)
                {
                    // Process article
                    $this->currentFile = $this->currentFolder."/".$node->nodeValue;
                    $this->processFile();
                }
            }
        }
        
        $this->totalProcessed++;
        $this->writePollData(floor(($this->totalProcessed / $this->total) * 100));
        
        return true;
    }
    
    /**
    * This function is invoked each time a file has to be processed
    * (the currentFile and currentFolder properties are updated before this function is invoked)
    **/
    public function processFile()
    {
$this->logger->logMessage("In ProcessFile");
        // Check XML validity of file        
        $domXml = @DomDocument::load($this->currentFile);
        if (!$domXml)
        {
            $this->logger->logError(sprintf(_BIZ_XML_INVALID, $this->currentFile ));
            return false;
        }

        // Build a new article +yul
        $article = new news(wcmProject::getInstance());

        // Retrieve article unique ID
        $xPath = new DOMXPath($domXml);
        
        $uniqueCode = wcmXML::getXPathNodeValue($xPath, null, "NewsItem/Identification/NewsIdentifier/NewsItemId");

        // Search for existing article in WCM
        $article->refreshFromSource($this->source, $uniqueCode);
        
        $this->logger->logMessage("uniqueCode : ".$uniqueCode." - source : ".$this->source." - ChannelId : ".$this->currentChannelId);

        // Retrieve publication date (ISO format)
        $isoDate = wcmXML::getXPathNodeValue($xPath, null, "NewsItem/NewsManagement/FirstCreated");
        $article->publicationDate = date("Y-m-d H:i:s", mktime(substr($isoDate, 9, 2) + substr(date("O"),2,1),substr($isoDate, 11, 2),substr($isoDate, 13, 2),substr($isoDate, 4, 2), substr($isoDate, 6, 2), substr($isoDate, 0, 4)));

        // Init article from XML file (by applying an XSL transformation)
        $xsl = $this->xslFolder.$article->getClass().'.xsl';
        if (!file_exists($this->xslFolder.$article->getClass().".xsl"))
        {
            $this->logger->logMessage(_BIZ_XSL_NOT_FOUND.': '.$xsl);
        	return false;
        } else {
            try
            {
        	   $article->initFromXMLDocument($domXml,$this->xslFolder.$article->getClass().".xsl");
        	   $this->logger->logMessage(_BIZ_NEW_ARTICLE . ': '.$article->title);
            } catch (Exception $e) {
                $this->logger->logError(_BIZ_ARTICLE_XML_ERROR . $this->currentFile);
                return false;
            }
        }
        
        $article->sourceId = $uniqueCode;
        $article->source = $this->source;
        $article->channelId = $this->currentChannelId;
		$article->issueId = $this->currentIssueId;
		$article->publicationId = $this->currentPublicationId;
		
		if (empty($article->siteId)) $article->siteId = $this->siteId;
	
        // Save article
        if (!$article->save())
        {
            $this->logger->logError(_BIZ_ERROR_SAVE.': '.$article->getErrorMsg().' - site: '.$this->siteId);
            return false;
        }     
        
        $article->deleteChapters();
        
        // Add chapter
        $chapter = new chapter();
        $chapter->text = wcmXML::getXpathNodeValue($xPath, null, 'NewsItem/NewsComponent/NewsComponent/ContentItem/DataContent');
        $chapter->articleId = $article->id;
        $chapter->save();

        // Remove existing photos
        $bizRelation = new bizrelation(wcmProject::getInstance());
        $bizRelation->sourceClass = $article->getClass();
        $bizRelation->sourceId = $article->id;
        $bizRelation->kind = bizrelation::IS_COMPOSED_OF;
        $bizRelation->removeOne();
        
        // Browse and create photos
        $nodes  = $xPath->query("NewsItem/NewsComponent/NewsComponent[@Duid != '']");
        foreach($nodes as $node)
        {
            $currentPhoto = $this->processPhoto($node);
            if ($currentPhoto)
            {
                $bizRelation->destinationClass = $currentPhoto->getClass();
                $bizRelation->destinationId = $currentPhoto->id;
                $bizRelation->title = $currentPhoto->title;
                $bizRelation->header = ucfirst($currentPhoto->getClass());
                $bizRelation->validityDate = $currentPhoto->publicationDate;
                
                $bizRelation->rank = $bizRelation->getLastPosition() + 1;
                $bizRelation->addBizrelation();
                
                $this->logger->logMessage(sprintf(_BIZ_ASSOC_PHOTO_ARTICLE_DONE, $currentPhoto->title, $article->title));
            }
        }
        

    }
    

    /**
     * Process an article photo
     *
     * @param XMLNode $node A XMLNode (NewsML format) representing a photo
     *
     * @return photo The newly created photo or null on error
     */
    public function processPhoto($node)
    {
        // Retrieve photo unique ID
        $xPath = new DOMXPath($node->ownerDocument);
        $duid = wcmXML::getXpathNodeValue($xPath, $node, "@Duid");
        
        // Search for original file
        $nodeO = wcmXML::getXPathFirstNode($xPath, $node, "NewsComponent[Role/@FormalName='Quicklook']");
        if (!$nodeO)
        {
            $this->logger->logError(sprintf(_BIZ_ORIGINAL_PHOTO_NOT_FOUND, $duid));
            return null;
        }
        
        $fileO = utf8_decode(wcmXML::getXPathNodeValue($xPath, $nodeO, "ContentItem/@Href"));

        // Retrieve thumbnail
        $nodeT = wcmXML::getXPathFirstNode($xPath, $node, "NewsComponent[Role/@FormalName='Thumbnail']");
        if (!$nodeT)
        {
            $this->logger->logError(sprintf(_BIZ_THUMBNAIL_NOT_FOUND, $duid, $fileO ));
            return null;
        }
        $fileT = utf8_decode(wcmXML::getXPathNodeValue($xPath, $nodeT, "ContentItem/@Href"));
        
        
        $pathOfPhoto = ($this->parameters['wcmImportAFP_embeddedPhotosLocation'] == 'local')?
            $this->currentFolder : $this->parameters['wcmImportAFP_embeddedPhotos'];
        
        // Trailing slash fix
        if ($pathOfPhoto{strlen($pathOfPhoto)-1} != '/') $pathOfPhoto .= '/';
        
        if (!is_file($pathOfPhoto.$fileO))
        {
            $this->logger->logError('File not found: '.$pathOfPhoto.$fileO);
            return null;
        }
        
        // Where do we get info from?
        if ($this->parameters['wcmImportAFP_mediaData'] == 'iptc')
        {
            $importer = new wcmImportPhotos(array('siteId' => $this->siteId, 'noTotal' => true, 'logger' => $this->logger));
            $photo = $importer->processOne($pathOfPhoto.$fileO);
        } else {
            

            // Builds a new photo bizobject
            $photo = new photo(wcmProject::getInstance());
    
            // Naming rule convention: photo name is the left part of the string before ".thumbnail.default.nnnxnnn.jpg"
            $uniqueCode = substr($fileT, 0, strpos($fileT, '.thumbnail'));
            
            // Retrieve extension (including the dot)
            $extension = substr($fileO, strrpos($fileO, '.'));
            
            // Retrieve existing photo from WCM
            $photo->refreshFromSource($this->source, $uniqueCode);
            $photo->sourceId = $uniqueCode;
            $photo->source = $this->source;
            
            $publicationDate = filemtime($pathOfPhoto.$fileO);
            $photo->publicationDate = date("Y-m-d H:i:s", $publicationDate);
            $photo->issueId = $this->currentIssueId;
            $photo->publicationId = $this->currentPublicationId;
    
            // Compute pictures filenames (for original and thumbnail)
            umask(0);
            $folder  = "";
            if (!is_dir($this->photoFolder))
                @mkdir($this->photoFolder, 0777, true);

            $folder .= date("Y-m", $publicationDate);
            if (!is_dir($this->photoFolder.$folder))
                @mkdir($this->photoFolder.$folder, 0777, true);
                
            $folder .= "/" . date("Y-m-d", $publicationDate);
            if (!is_dir($this->photoFolder.$folder))
                @mkdir($this->photoFolder.$folder, 0777, true);
                
            $folder .= "/";
    
            $fileNameO = $uniqueCode . $extension;
            $fileNameT = $uniqueCode . '-thb' . $extension;
    
            $finalO = $this->photoFolder . $folder . $fileNameO;
            $finalT = $this->photoFolder . $folder . $fileNameT;
            
            
            $this->logger->logMessage('Copying photo: '.$pathOfPhoto.$fileO.' to '.$finalO);
            
            // Copy original and thumbnail to final directories
            if (!copy($pathOfPhoto . $fileO, $finalO))
                $this->logger->logWarning(sprintf(_BIZ_CANNOT_COPY_PHOTO_AFP, $pathOfPhoto, $fileO, $finalO));
                
            chmod($finalO, 0666);
            if (!copy($pathOfPhoto . "/" . $fileT, $finalT))
                $this->logger->logWarning(sprintf(_BIZ_CANNOT_COPY_THUMBNAIL_AFP, $$pathOfPhoto, $fileT, $finalT));
                
            chmod($finalT, 0666);
    

    
            $simpleXml = simplexml_import_dom($node);
            if (!file_exists($this->xslFolder.$photo->getClass().".xsl"))
            {
            	$this->logger->logError(_BIZ_XSL_NOT_FOUND);
            	return false;
            }
            
            try
            {
               $photo->initFromXML($simpleXml->asXML(), $this->xslFolder.$photo->getClass().".xsl");
            } catch (Exception $e) {
                $this->logger->logError(sprintf(_BIZ_NOINIT_PHOTO_XML, $this->currentFile));
                return false;
            }
           		
            // Determine web paths (URL) to original and thumbnail
            $photo->original  = $this->photoURL . $folder . $fileNameO;
            $photo->thumbnail = $this->photoURL . $folder . $fileNameT;
            
            $thumbInfo = getimagesize($finalT);
            $origInfo = getimagesize($finalO);
            
            $photo->width = $origInfo[0];
            $photo->height = $origInfo[1];
            
            $photo->thumbWidth = $thumbInfo[0];
            $photo->thumbHeight = $thumbInfo[1];
            
            $photo->siteId = $this->siteId;
            
            if (!$photo->save())
            {
                $this->logger->logError('Could not save photo: '.$photo->getErrorMsg());
            }
        }
        return $photo;
    }
}