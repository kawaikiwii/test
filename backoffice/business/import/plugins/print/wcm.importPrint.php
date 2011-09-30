<?php
/**
 * Project:     WCM
 * File:        importPrint.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
require_once('wcm.importAFP.php');

/**
 * This class is used to import a complete printed publication (i.e. publication,
 * issues, articles and photos)
 *
 * The article and photo importation rules is the same as AFP
 */
class wcmImportPrint extends wcmGenericFileImport
{
    /**
     * (string) default root folder used for PRINT
     * Default value is "./data/wcm/WCM/3.1/business/import/in/PRINT/"
     *
     * Important: if the value does NOT start with a dot '.' the path will be
     *             considered as relative to the WCM installation folder
     */
    const DEFAULT_ROOTFOLDER = "business/import/in/PRINT/";   

    /**
     * (string) default relative physical path where XSL are stored (relative to WCM installation folder)
     * Default value is "./business/import/xsl/AFP/"
     *
     * Important: if the value does NOT start with a dot '.' the path will be
     *             considered as relative to the WCM installation folder
     */
    const DEFAULT_XSLFOLDER = "business/import/xsl/AFP/";
    
    /**
     * (string) default relative physical path where to store photos (relative to WCM installation folder)
     * Default value is "./business/img/photos/PRINT/"
     *
     * Important: if the value does NOT start with a dot '.' the path will be
     *             considered as relative to the WCM installation folder
     */
    const DEFAULT_PHOTOFOLDER = "business/img/photos/PRINT/";

    /**
     * (string) default URL path used to reference photos in WCM back-office
     * Default value is "img/photos/PRINT/"
     *
     * Important: if the value does NOT start with 'http:' or 'https:' the URL will be
     *             considered as relative to the WCM back-office URL
     */
    const DEFAULT_PHOTOURL = "img/photos/PRINT/";

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
    * @param string     $rootFolder  Root folder
    * @param array      $parameters  An associative array used to assign properties (by their name) and extra parameters
    */
    public function __construct($rootFolder, array $parameters)
    {
        parent::__construct($rootFolder, $parameters);  

        // Setup default values
        $this->fileRegExp = '.xml$';
        if (!$this->source) $this->source = 'PRINT';
        if (!$this->rootFolder) $this->rootFolder = self::DEFAULT_ROOTFOLDER;
        if (!$this->xslFolder) $this->xslFolder = self::DEFAULT_XSLFOLDER;
        if (!$this->photoFolder) $this->photoFolder = self::DEFAULT_PHOTOFOLDER;
        if (!$this->photoURL) $this->photoURL = self::DEFAULT_PHOTOURL;
        if (!$this->siteId) $this->siteId = $_SESSION['siteId'];
    }
    
    /*
    * Launch import process
    */
    public function process()
    {
        // Adjust relative folders if needed
        $path = WCM_DIR . '/';
        if (substr($this->rootFolder, 0, 1) == '.') $this->rootFolder = $path . $this->rootFolder;
        if (substr($this->xslFolder, 0, 1) == '.') $this->xslFolder = $path . $this->xslFolder;
        if (substr($this->photoFolder, 0, 1) == '.') $this->photoFolder = $path . $this->photoFolder;

        // Start processing (no processing of files)
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
        /*
         * Depending on the currentDepth we have to process either the publication,
         * the issue, or the channel hierarchy
         */
    $ret = true;
        switch($this->currentDepth)
        {
            case 1:
                $this->processPublication();
                break;

            case 2:
                $this->processIssue();
                break;

            default:
                $this->processChannel();
                break;
        }
    }

    /**
     * Process the publication folder
     */
    private function processPublication()
    {
        $folderName = substr(strrchr($this->currentFolder, "/"), 1 );
        $publication = new publication();
        $publication->refreshFromSource($this->source, $folderName);
        if (!$publication->id)
            $this->logger->logMessage(sprintf(_BIZ_CREATE_PUBLICATION, $folderName));
        else
            $this->logger->logMessage(sprintf(_BIZ_UPDATE_PUBLICATION, $publication->title));

        $publication->title = $folderName;
        $publication->description = $folderName;
        $publication->siteId = $this->siteId;
        $publication->sourceId = $folderName;
        $publication->source   = $this->source;
        if (!$publication->checkin())
        return false;

        // Set current publication ID
        $this->currentPublicationId = $publication->id;
    $this->currentDepth = 2;
    $this->browseFolders($this->currentFolder."/", 1, false);
    $this->currentDepth = 1;
    }

    /**
     * Process the issue folder
     */
    private function processIssue()
    {
        $folderName = substr(strrchr($this->currentFolder, "/"), 1 );
        $issue = new issue();
        $issue->refreshFromSource($this->source, $folderName);
        if (!$issue->id)
            $this->logger->logMessage(sprintf(_BIZ_CREATE_ISSUE, $folderName));
        else
            $this->logger->logMessage(sprintf(_BIZ_UPDATE_ISSUE, $issue->title));

        $issue->title = $folderName;
        $issue->description = $folderName;
        $issue->siteId = $this->siteId;
        $issue->sourceId = $folderName;
        $issue->source = $this->source;
        $issue->publicationId = $this->currentPublicationId;
        if (!$issue->checkin())
        return false;

        // Set current issue ID
        $this->currentIssueId = $issue->id;
    $this->currentDepth = 3;
    $this->processChannel();
    $this->currentDepth = 2;
    }

    /**
     * Process a channel folder
     */
    private function processChannel()
    {
        // Processing a channel basically consists in processing a standard AFP import
        // with the corresponding parameters
        $parameters = array(
                'currentPublicationId' => $this->currentPublicationId,
                'currentIssueId' => $this->currentIssueId,
                            'source' => $this->source,
                            'xslFolder' => $this->xslFolder,
                            'photoFolder' => $this->photoFolder,
                            'photoURL' => $this->photoURL );
 
        $importAFP = new importAFP($this->currentFolder.'/', $parameters);
        $importAFP->setLogger($this->logger);

        // Process channel (and belonging articles and photos)
    $importAFP->process();
    }

    /**
    * This function is invoked each time a file has to be processed
    * (the currentFile and currentFolder properties are updated before this function is invoked)
    **/
    public function processFile()
    {
    $this->logger->logMessage("processFile : ". $this->currentFile);
    }
}
?>
