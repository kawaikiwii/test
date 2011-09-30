<?php
/**
 * Project:     WCM
 * File:        importNITF.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * This class is used to import articles in NITF format
 *
 * Each folder/subfolders can contains as many XML files (all in NITF format) representing a unique article
 */
class wcmImportNITF extends wcmGenericFileImport
{
    /**
     * (string) default root folder used for NITF
     * Default value is "/data/wcm/WCM/3.1/business/import/in/NITF/"
     *
     * Important: if the value does NOT start with a dot '.' the path will be
     *             considered as relative to the WCM installation folder
     */
    const DEFAULT_ROOTFOLDER = "business/import/in/NITF/";   

    /**
     * (string) default relative physical path where XSL are stored (relative to WCM installation folder)
     * Default value is "business/import/xsl/NITF/"
     *
     * Important: if the value does NOT start with a dot '.' the path will be
     *             considered as relative to the WCM installation folder
     */
    const DEFAULT_XSLFOLDER = "business/import/xsl/NITF/";

    /**
     * (int) Default site id used for import (default value is zero)
     */
    public $siteId = 0;
    
    /**
     * (int) Default channel id used for import (default value is zero)
     */
    public $channelId  = 0;

    /**
     * (string) Path of folder where XSL can be found (the folder should contains 'article.xsl' and 'photo.xsl' files)
     *
     * Important: if the value does NOT start with a dot '.' the path will be
     *             considered as relative to the WCM installation folder
     */
    public $xslFolder;

    /**
    * Constructor
    *
    * @param string     $rootFolder  Root folder
    * @param array      $parameters  An associative array used to assign properties (by their name) and extra parameters
    */
    public function __construct($rootFolder, array $parameters)
    {
        parent::__construct($rootFolder, $parameters);  

        // Setup default values
        $this->fileRegExp = '^([A-Za-z]*)_([A-Za-z0-9]*)\.xml$'; 
        $this->source = 'NITF';
        if (!$this->rootFolder) $this->rootFolder = self::DEFAULT_ROOTFOLDER;
        if (!$this->xslFolder) $this->xslFolder = self::DEFAULT_XSLFOLDER;
        if (!$this->siteId) $this->siteId = $_SESSION['siteId'];
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
        $path = WCM_DIR . '/';
        if (substr($this->rootFolder, 0, 1) == '.') $this->rootFolder = $path . $this->rootFolder;
        if (substr($this->xslFolder, 0, 1) == '.') $this->xslFolder = $path . $this->xslFolder;

        // Import...        
        parent::process();
    }
    
    /**
    * This function is invoked each time a folder has to be processed
    * (the currentFolder property is updated before this function is invoked)
    *
    * @return bool Return FALSE to skip processing of inner files and sub-folders, true otherwise
    **/
    public function processFolder()
    {
        $this->logger->logMessage("processFolder : " . $this->currentFolder);
        return true;
    }
        
    /**
    * This function is invoked each time a file has to be processed
    * (the currentFile and currentFolder properties are updated before this function is invoked)
    **/
    public function processFile()
    {
        $this->logger->logMessage("processFile : ". $this->currentFile);
        
        // Load XML file
        $domXml = new DOMDocument();
        if (!$domXml->load($this->currentFile))
        {
            $this->logger->logError(sprintf(_BIZ_XML_INVALID, $this->currentFile));
            return;
        }

        // Build new article
        $article = new article();

        // Retrieve unique ID
        $xPath = new DOMXPath($domXml);
        $uniqueCode = getXPathNodeValue($xPath, null, "head/docdata/doc-id/@id-string");

        // Update properties from WCM
        $article->refreshFromSource($this->source, $uniqueCode);
        if ($article->id == 0)
            $this->logger->logMessage(sprintf(_BIZ_CREATE_ARTICLE, $uniqueCode));
        else
            $this->logger->logMessage(sprintf(_BIZ_UPDATE_ARTICLE, $article->id, $uniqueCode));
        $article->sourceId = $uniqueCode;
        $article->source = $this->source;
        $article->channelId = $this->parameters["currentChannelId"];
        $article->siteId = $this->siteId;

        // Retrieve publication date (ISO format)
        $isoDate = getXPathNodeValue($xPath, null, "head/docdata/date.issue/@norm");
        $article->publicationDate = date("Y-m-d H:i:s", mktime(substr($isoDate, 9, 2) + substr(date("O"),2,1),substr($isoDate, 11, 2),substr($isoDate, 13, 2),substr($isoDate, 4, 2), substr($isoDate, 6, 2), substr($isoDate, 0, 4)));
        
        if (!is_file($this->xslFolder.$article->getClass().".xsl"))
        {
            $this->logger->logError(_BIZ_XSL_NOT_FOUND);
        }
        else
        {
            $article->initFromXMLDocument($domXml,$this->xslFolder.$article->getClass().".xsl");
            $article->checkin();
        }

        // Remove processed file
        unlink($this->currentFile);
    }
}
?>
