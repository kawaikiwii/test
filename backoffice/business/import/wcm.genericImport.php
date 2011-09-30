<?php

/**
 * Project:     WCM
 * File:        genericImport.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * Abstract class 
 */
abstract class genericImport
{
    /**
    * (wcmLogger) logger
    */
    protected $logger;
  
    /**
    * (string) Initial (root) folder
    */
    protected $rootFolder;

    /**
     * (string) A source to identified origin of import
     */
    protected $source;
    
    /**
    * (int) Maximum sub-folfders depth (0 = unlimited)
    */
    protected $maximumDepth = 0;
  
    /**
    * (int) current depth (start = 1)
    */
    protected $currentDepth = 1;
  
    /**
    * (string) current folder to process
    */
    protected $currentFolder;
  
    /**
    * (string) current file to process
    */
    protected $currentFile;
  
    /**
    * (string) regular expression that folders must follow to be processed
    * Default value is '[^\+?{}/:<>|\\]'
    */
    protected $folderRegExp = '[^\+?{}/:<>|\\]';
  
    /**
    * (string) regular expression that files must follow to be processed
    * Default value is '[^\+?{}/:<>|\\]'
    */
    protected $fileRegExp = '[^\+?{}/:<>|\\]';
  
    /**
    * (array) a generic associative array to transmit extra parameters to inherited classes
    */
    protected $parameters;
  
    /**
    * (bool) set this property to TRUE to cancel import process (checked within each process loop)
    */
    protected $cancelled = false;
    
    /**
     * FRFR logFile
     * 
     */
 	protected $logFile = null;
  
    /**
    * Constructor
    *
    * @param string     $rootFolder  Root folder
    * @param array      $parameters  An associative array used to assign properties (by their name) and extra parameters
    */
    public function __construct($rootFolder, array $parameters)
    {
        // Setup initial properties
        $this->rootFolder = $rootFolder;
        $this->parameters = (is_array($parameters)) ? $parameters : array();
        if (isset($parameters['logFile']))
        	$this->logFile = WCM_DIR . "/logs/traces/import_".$parameters['logFile'];
        else
        	$this->logFile = WCM_DIR . "/logs/traces/import_".date('YmdHis').".log";

        $this->logger = new wcmLogger(true, true, $this->logFile, false);
        
        // Update public and protected properties from parameters
        $reflection = new ReflectionClass(get_class($this));
        foreach($reflection->getProperties() as $property)
        {
            if (($property->isPublic() || $property->isProtected()) && !$property->isStatic())
            {
                $name = $property->getName();
                
                // Update property?
                if (array_key_exists($name, $this->parameters))
                {
                    $this->$name = $this->parameters[$name];
                }
            }
        }
    }

    /**
     * Returns current logger
     * @return wcmLogger Current logger
     */
    public function getLogger()
    {
        return $this->logger;
    }
    
    /**
     * Set current logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }
    
    /*
    * Launch import process
    *
    * @param $processFiles  bool  TRUE to automatically invoke processFile() methods (default is TRUE)
    */
    public function process($processFiles = true)
    {
        $this->logger->logMessage(sprintf(_BIZ_BEGIN_IMPORT, $this->rootFolder ));
        $this->browseFolders($this->rootFolder, 1, $processFiles);
        $this->logger->logMessage(sprintf(_BIZ_END_IMPORT, $this->rootFolder ));
    }
    

    /**
    * This method is used to notify import that processing should be cancel
    *
    * @param string $message An optional message to log before cancelling
    */
    protected function cancelImport($message = null)
    {
        if ($message) $this->logger->logInfo($message);
        $this->cancelled = true;
    }
    
    /**
    * Recurvise browse of folders
    * 
    * @param    $baseFolder     string  root folder
    * @param    $depth          int     current folder depth (starts at 1)
    * @param    $processFiles   bool    TRUE to automatically invoke processFile() methods  (default is TRUE)
    **/
    public function browseFolders($baseFolder, $depth = 1, $processFiles = true)
    {
        $subFolders = @opendir($baseFolder);
        $this->logger->logVerbose(_BIZ_READ_FOLDER.' '.$baseFolder);

        // browse folders
        while (($folder = @readdir($subFolders)) !== false)
        {
            // Is import cancelled?
            if ($this->cancelled) break;
            
            if ((is_dir($baseFolder.$folder))&&($folder[0]!='.'))
            {
                // check regexp
                if (ereg($this->folderRegExp, $folder))
                {
                    $this->logger->logVerbose(_BIZ_PROCESS_FOLDER.' '.$baseFolder.$folder);
                    $this->currentFolder = $baseFolder.$folder;                 

                    // process folder
                    if ($this->processFolder())
                    {
                        if (($this->maximumDepth == 0)||($this->maximumDepth > $depth))
                        {
                            // recursive call (increase folder depth)
                            $depth++;
                            $this->browseFolders($baseFolder . $folder . '/', $depth, $processFiles);
                        }
                    }
                }         
            }

        }
        
        // browse (and process) files?
        if ($processFiles)
        {
            $this->browseFiles($baseFolder);
        }
        @closedir($subFolders);
    }
    
    /**
    * Browse files of current folder and invoke processFile() method
    * 
    * @param    $baseFolder string  Folder to browse
    **/
    public function browseFiles($baseFolder)
    {
        $files = @opendir($baseFolder);
        $this->logger->logMessage(_BIZ_READ_FOLDER.' '.$baseFolder);

        // browse files
        while (($file = @readdir($files)) !== false)
        {
            // Is import cancelled?
            if ($this->cancelled) break;

            if ((is_file($baseFolder.$file))&&($file[0]!='.'))
            {
                // check regexp
                if (ereg($this->fileRegExp, $file))
                {
                    $this->currentFile = $file;
                    $this->logger->logVerbose(_BIZ_PROCESS_FILE.' '.$baseFolder.$file);
                    $this->currentFile = $baseFolder.$file;             
                    // process file
                    $this->processFile();
                }
                else
                {
                    $this->logger->logVerbose(sprintf(_BIZ_INVALID_FILE, $baseFolder.$file));
                }
            }
        }
        @closedir($files);
    }
    
    public function getRootFolder()
    {
    	return $this->rootFolder;
    }
    
    /**
    * This function is invoked each time a folder has to be processed
    * (the currentFolder property is updated before this function is invoked)
    *
    * @return bool Return FALSE to skip processing of inner files and sub-folders, true otherwise
    **/
    abstract public function processFolder();

    /**
    * This function is invoked each time a file has to be processed
    * (the currentFile and currentFolder properties are updated before this function is invoked)
    **/
    abstract public function processFile();
}
?>
