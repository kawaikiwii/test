<?php
/**
 * Project:     WCM
 * File:        importBizObject.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     3.1
 *
 */

/**
 * This class is used to import generic bizobjects in XML format.
 * A folder can contains as many XML files (format "{className}_{sourceId}.xml")
 *
 * Each XML can be transformed using an XSL-T and the result is given to initFromXML() methods
 * to build a new bizobject.
 */
class wcmImportXML extends wcmGenericFileImport
{
    /**
     * (string) default root folder used for BizObject
     * Default value is "/data/wcm/WCM/3.1/business/import/in/BizObject/"
     *
     * Important: if the value does NOT start with a dot '.' the path will be
     *             considered as relative to the WCM installation folder
     */
    const DEFAULT_ROOTFOLDER = "business/import/in/BizObject/";   

    /**
     * (string) default relative physical path where XSL are stored (relative to WCM installation folder)
     * Default value is "business/import/xsl/BizObject/"
     *
     * Important: if the value does NOT start with a dot '.' the path will be
     *             considered as relative to the WCM installation folder
     */
    const DEFAULT_XSLFOLDER = "business/import/xsl/BizObject/";

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
    public $xslFolder = null;
    
    /**
    * Constructor
    *
    * @param string     $rootFolder  Root folder
    * @param array      $parameters  An associative array used to assign properties (by their name) and extra parameters
    */
    public function __construct(array $parameters)
    {
        parent::__construct($parameters);  

        // Setup default values
        $this->fileRegExp = '/([A-Za-z]*)_([A-Za-z0-9]*)\.xml/';
        $this->rootFolder = $parameters['wcmImportXML_sourceFolder'];
        if (!$this->source) $this->source = 'WCM';
        if (!$this->rootFolder) $this->rootFolder = self::DEFAULT_ROOTFOLDER;
        if (!$this->xslFolder) $this->xslFolder = self::DEFAULT_XSLFOLDER;
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
                
                if (preg_match($this->fileRegExp, $d->getFilename()))
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
        if (!$this->siteId) $this->siteId = $_SESSION['siteId'];

        // Start process (and process files)    
        parent::process(true);
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
    
    public function processOne($argItem)
    {
        return false;
        
    }
        
    /**
    * This function is invoked each time a file has to be processed
    * (the currentFile and currentFolder properties are updated before this function is invoked)
    **/
    public function processFile()
    {
        $this->logger->logMessage("processFile : ". $this->currentFile);
        
        // Deduce classname and id from file name ({className}_{objectSourceId}.xml)
        
        $fileName = basename($this->currentFile);
        
        $fileNameParts = explode('_',$fileName);
        
        $className = $fileNameParts[0];
        $uniqueCode = $fileNameParts[1];

        // Check if class is valid
        $classList = getClassList();
        if (!isset($classList[$className]))
        {
            $this->totalProcessed++;
            $this->writePollData(floor(($this->totalProcessed / $this->total) * 100));            
            $this->logger->logError(sprintf(_BIZ_INVALID_CLASSNAME, $className));
            return;        
        }
        
        
        
        $bo = new $className();
        
        $boXML = new DOMDocument();
        
        if (!$boXML->load($this->currentFile))
        {
            $this->totalProcessed++;
            $this->writePollData(floor(($this->totalProcessed / $this->total) * 100));            
            $this->logger->logError('INVALID XML');
            return false;
        }
        
        $xPath = new DOMXPath($boXML);
        
        
        $source = wcmXML::getXPathNodeValue($xPath,null,"//source", "BizObject");
        $sourceCode = wcmXML::getXPathNodeValue($xPath,null,"//sourceId",$uniqueCode);
        
        $this->logger->logMessage('Making new class: '.$className);
        
        $bo->refreshFromSource($source, $sourceCode);
        if ($bo->id == 0)
        {
            $this->logger->logMessage(sprintf(_BIZ_CREATE_BIZOBJECT,$className, $uniqueCode));
        }
        else
        {
            $originalId = $bo->id;
            $this->logger->logMessage(sprintf(_BIZ_UPDATE_BIZOBJECT, $className, $bo->id, $uniqueCode));
        }
        
        $bo->initFromXML(file_get_contents($this->currentFile));

        $this->logger->logMessage('Saving object. Class: '.get_class($bo));
        
        if (!$bo->versionNumber)
        {
            $bo->versionNumber = 1;
        }
        
        if (empty($bo->siteId))
        {
            $bo->siteId = wcmSession::getInstance()->getSiteId();
        }
        
        // Save bizobject
        if ($bo->save())
        {
            if ($bo->getClass() == 'article' && !empty($bo->text))
            {
                $chapter = new chapter();
                $chapter->articleId = $bo->id;
                $chapter->title = $bo->title;
                $chapter->text = $bo->text;
                if ($chapter->save())
                {
                    $this->logger->LogMessage('Chapter saved!');
                } else {
                    $this->logger->logError('Chapter for article: '.$article->title.':'.$article->id.' could not be saved.');
                }
            }
            $this->logger->logMessage('Saved!');
        } else {
            $this->logger->logError('Bizobject could not be saved: '.$bo->getErrorMsg());
        }
        $this->totalProcessed++;
        $this->writePollData(floor(($this->totalProcessed / $this->total) * 100));        
        return true;

    }
}