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
 * This class is used to import pictures (JPG) and build photo objects
 *
 * Each folder/subfolders can contains as many (*.jpg | *.jpeg) files
 * The IIM (IPTC/APP13) fields will be used to populate the photo properties
 */
class wcmImportPhotos extends wcmGenericImport
{
    /**
     * (string) default root folder used for PHOTOS
     * Default value is "/data/wcm/WCM/3.1/business/import/in/PHOTOS/"
     *
     * Important: if the value does NOT start with a dot '.' the path will be
     *             considered as relative to the WCM installation folder
     */
    const DEFAULT_ROOTFOLDER = "business/import/in/PHOTOS/";

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
     * Default value is "img/photos/import/"
     *
     * Important: if the value does NOT start with a dot '.' the path will be
     *             considered as relative to the WCM installation folder
     */
    const DEFAULT_PHOTOFOLDER = "img/photos/import/";

    /**
     * (string) default URL path used to reference photos in WCM back-office
     * Default value is "img/photos/import/"
     *
     * Important: if the value does NOT start with 'http:' or 'https:' the URL will be
     *             considered as relative to the WCM back-office URL
     */
    const DEFAULT_PHOTOURL = "img/photos/import/";

    /**
     * (int) Site Id use for import (default value is zero)
     */
    public $siteId = 0;
    
    /**
     * Path to source folder
     */
    public $in;

    /**
     * Path to destination folder
     */
    public $out;
    
    /**
     * (string) URL used to find (prefix) photos from the WCM back-office
     *
     * Important: if the value does NOT start with 'http:' or 'https:' the URL will be
     *             considered as relative to the WCM back-office URL
     */
    public $photoURL;
    
    /**
    * Constructor
    *
    * @param string     $rootFolder  Root folder
    * @param array      $parameters  An associative array used to assign properties (by their name) and extra parameters
    */
    public function __construct(array $parameters)
    {
        parent::__construct($parameters);
        $config = wcmConfig::getInstance();
        $session = wcmSession::getInstance();
        
        $this->siteId = $session->getSiteId();
        $this->in = getArrayParameter($parameters, 'wcmImportPhotos_sourceFolder', self::DEFAULT_ROOTFOLDER);
        $this->out = WCM_DIR .'/' . $config['wcm.backOffice.photosPath'];
        $this->photoURL = $config['wcm.backOffice.photosPath'];

        // Setup default values
        $this->fileRegExp = '(\.jpeg|\.JPEG|\.JPG|\.jpg)$';
        if (!isset($parameters['noTotal']) || !$parameters['noTotal'])
        {
            $this->getTotal();
        }
    }

    /**
     * Launch importation process
     */
    public function process()
    {
        // Adjust relative folders if needed
        $path = WCM_DIR . '/';
        if (substr($this->out, 0, 1) == '.') $this->out = $path . $this->out;
        if (substr($this->in, 0, 1) == '.') $this->in = $path . $this->in;
        
        // Start processing (process files)
        $dir = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($this->in), RecursiveIteratorIterator::SELF_FIRST
                ); 

        // Fetch every single file.
        foreach ($dir as $d) 
        {
            $ext = strtolower(pathinfo($d->getFilename(),PATHINFO_EXTENSION));
            if ($ext == 'jpeg' || $ext == 'jpg')
            {
                $this->processOne($d->getPathname());
                $this->totalProcessed++;
                if ($this->total)
                    $this->writePollData(floor(($this->totalProcessed / $this->total) * 100));
            }
        }
    }
    
    /**
     * Get total of files to process
     *
     * @return  int     total files to process
     */
    public function getTotal()
    {
        $dir =  new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($this->in), RecursiveIteratorIterator::SELF_FIRST
                ); 

        // Fetch every single file.
        foreach ($dir as $d) 
        {
            $ext = strtolower(pathinfo($d->getFilename(),PATHINFO_EXTENSION));
            if ($ext == 'jpeg' || $ext == 'jpg')
            {
                $this->total++;
            }
        }
        
        return $this->total;
    }

    /**
     * Process one photo
     *
     * @param   string  path of photo to import
     * @return  photo   wcm Photo on success, false on failure
     */
    public function processOne($argImportItem)
    {
        $argFile = $argImportItem;
        $this->logger->logMessage(_BIZ_IMPORT_PROCESS . ' ' .$argImportItem);
        $filename = basename($argFile);
        
        // Build a photo object (try to refresh by the original value)
        $photo = new photo();
        if ($photo->refreshByOriginal($this->photoURL . $filename))
            $this->logger->logMessage(_BIZ_IMPORT_UPDATE_PHOTO.' '.$photo->title.' ['.$photo->id.']');
        else
            $this->logger->logMessage(_BIZ_IMPORT_INSERT_PHOTO.' '.$photo->title);

        /**
         * Parse IPTC fields
         *
         * Rule => Title comes from 2#105 (title) or is equals to short file name (without extension)
         *         Caption comes from 2#120 (caption) or is null
         *         Credits comes from 2#110 (credit) or 2#116 (copyright notice) or is null
         *         Keywords come from both 2#025 (keywords) and 2#015 (categories) or is null
         */
        $size = GetImageSize($argFile, $info);
        if (isset($info['APP13']))
        {
            $iptc = iptcparse($info['APP13']);
            // Update caption, title, credits and keywords
            $photo->title    = (isset($iptc['2#105'])) ? utf8_encode($iptc['2#105'][0]) : mb_substr($filename, 0, mb_strrpos($filename,'.'));
            $photo->caption  = (isset($iptc['2#120'])) ? utf8_encode($iptc['2#120'][0]) : '';
            $photo->credits  = (isset($iptc['2#110'])) ? utf8_encode($iptc['2#110'][0]) : ((isset($iptc['2#116'])) ? utf8_encode($iptc['2#116'][0]) : '');

            $photo->keywords = '';
            if (isset($iptc['2#025'])) $photo->keywords .= utf8_encode(implode(', ', $iptc['2#025']));
            if (isset($iptc['2#015'])) $photo->keywords .= ', ' . utf8_encode(implode(', ', $iptc['2#015']));
        }
        else
        {
            // if no title is set and there's no APP13, set file name as title
            $photo->title = mb_substr($filename, 0, mb_strrpos($filename,'.'));
            $this->logger->logWarning(_BIZ_IMPORT_PHOTO_FILE . ' ' . $filename . _BIZ_IMPORT_PHOTO_NO_TITLE );
        }

        $photo->publicationDate = date('Y-m-d H:i:s');
        $photo->siteId = $this->siteId;

        $photo->width = $size[0];
        $photo->height = $size[1];

        // Copy original picture
        if (!@is_dir($this->out))
        {
            if (!@mkdir($this->out))
            {
                $this->logger->logError(sprintf(_BIZ_IMPORT_NO_DESTINATION_DIRECTORY, $this->out));
                return false;
            } else {
                $this->logger->logMessage(sprintf(_BIZ_IMPORT_NO_DESTINATION_DIRECTORY_CREATED));
            }
        }
        
        if (!@copy($argFile, $this->out.$filename))
        {
            $this->logger->logError(sprintf(_BIZ_CANNOT_COPY_PHOTO_AFP, $argFile, $filename, $this->out));
            return false;
        }
        @chmod($this->out.$filename, 0666);

        $photo->original = $this->photoURL.$filename;

        // Create thumbnail
        $image = new wcmImageHelper($this->out.$filename);
        $image->thumb($this->out.'thb-'.$filename, 100, 75);
        $photo->thumbnail = $this->photoURL.'thb-'.$filename;

        // Retrieve thumbnail size
        $sizeThumbnail = GetImageSize($this->out.'thb-'.$filename);
        $photo->thumbWidth = $sizeThumbnail[0];
        $photo->thumbHeight = $sizeThumbnail[1];
    
        // Save new photo object
        if ($photo->save())
        {
            $this->logger->logMessage(_BIZ_IMPORT_PHOTO_OK . ' '.$filename);
            return $photo;
        } else {
            $this->logger->logError(sprintf(_BIZ_FILE_IMPORT_INCORRECT, $filename) . $photo->getErrorMsg());
            return false;
        }
    }
}