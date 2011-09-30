<?php
/**
 * Project:     WCM
 * File:        biz.photo.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
 /**
 * A photo
 */

class photo extends bizobject {
    /**
     * (int) site id
     */
    public $siteId;

    /**
     * (int) channel id
     */
    public $channelId;

    /**
     * (string) Title
     */
    public $title;

    /**
     * (string) Keywords
     */
    public $keywords;

    /**
     * (string) Credits
     */
    public $credits;

    /**
     * (string) Location of original picture
     */
    public $original;

    /**
     * (int) Picture width
     */
    //public $width;

    /**
     * (int) Picture height
     */
    //public $height;

    /**
     * (string) Location of thumbnail picture
     */
    //public $thumbnail;

    /**
     * (int) Thumbnail width
     */
    //public $thumbWidth;

    /**
     * (int) Thumbnail height
     */
    //public $thumbHeight;

    /**
     * (date) Publication date
     */
    public $publicationDate;

    /**
     * (date) Expiration date
     */
    public $expirationDate;

    /**
     * (Array) Array of all saved ratios
     */
    public $ratios;

    public $listIds;
    public $channelIds;
    public $folderIds;

    public $formats;

    public $specialUses;

    public $iptc;

    /**
     * Set all initial values of an object
     * This method is invoked by the constructor
     */

    protected function setDefaultValues() {
        $timestamp = time();
        $date_time_array = getdate($timestamp);

        $hours = $date_time_array['hours'];
        $minutes = $date_time_array['minutes'];
        $seconds = $date_time_array['seconds'];
        $month = $date_time_array['mon'];
        $day = $date_time_array['mday'];
        $year = $date_time_array['year'];

        $timestamp = mktime($hours, $minutes, $seconds, $month + 6, $day, $year);

        parent::setDefaultValues();
        //$this->publicationDate = $this->embargoDate = date('Y-m-d');
        //$this->expirationDate = date('Y-m-d', $timestamp);
        $this->siteId = $this->channelId = 0;

        $this->ratingCount = $this->ratingTotal = $this->ratingValue = 0;
        $this->hitCount = 0;

        //$this->width = $this->height = $this->thumbWidth = $this->thumbHeight = 0;
        $this->original = null;

        $this->ratios = array();

    }

    /**
     * Unserialize some properties if needed
     * This method is called by nextEnum() and refresh()
     */

    public function unserializeProperties() {
        parent::unserializeProperties();
        $this->ratios = ($this->ratios) ? unserialize($this->ratios) : array();
    }

    /**
     * Returns an XML representation of a property
     *
     * @param string $propKey    Property key
     * @param mixed  $propValue  Property value
     */

    protected function propertyToXML($propKey, $propValue) {
        // Treat special properties
        if ($propKey == 'ratios') {
            $xml = '<ratios />';
            return $xml;
        } else if (($propKey == 'formats') && !is_array($propValue)) {
            $propValue = unserialize($propValue);
        }
        return parent::propertyToXML($propKey, $propValue);
    }

    /**
     * This function can be used to customize the initialisation of a specific property
     * from a XML node (invoked by initFromXML() method)
     *
     * @param string  $property  Property name to initialize
     * @param XMLNode $node      XML node used for initialization
     */

    protected function initPropertyFromXMLNode($property, $node) {
        if ($property == 'ratios') {
            $this->ratios = array();
        } else {
            parent::initPropertyFromXMLNode($property, $node);
        }
    }

    /**
     * Refresh photo by searching the same 'original' value in WCM
     * This method is used by the importation process
     *
     * @return photo A pointer to this photo or null if photo does not exists (also Id will be set to zero)
     */

    function refreshByOriginal($original) {
        $sql = 'SELECT * FROM '.$this->tableName.' WHERE original=?';
        $properties = $this->database->getFirstRow($sql, array($original));
        if ($properties) {
            bindArrayToObject($properties, $this);
            unset($properties);
            return $this;
        }

        $this->id = 0;
        return null;
    }

    /**
     * CheckIn object in database and update search table
     *
     * @param array $source An assoc array for binding to class vars (or null)
     * @param int   $userId Id of user who is creating or updating the object
     *
     * @return true on success, false otherwise
     */

    public function checkin($source = null, $userId = null) {
        if (!parent::checkin($source, $userId))
            return false;

        if (!$source)
            $source = $this->getAssocArray(false);

        return true;
    }

    /**
     * Get photo path using its format
     *
     * @param string format
     *
     * @return path
     */

    public function getPhotoRelativePathByFormat($format) {
        if (! empty($this->permalinks)) {
            $path = str_replace("%format%", $format, $this->permalinks, $count);
            if ($count > 0)
                return $path;
        }
        return false;
    }

    /**
     * Get photo url using its format
     *
     * @param string format
     *
     * @return url
     */

    /*public function getPhotoUrlByFormat($format = "original") {
        $config = wcmConfig::getInstance();
        if (! empty($this->permalinks)) {
            $path = str_replace("%format%", $format, $this->permalinks, $count);
            $originalPath = str_replace("%format%", "original", $this->permalinks, $count);

            if ($count > 0) {
                if (file_exists($config['wcm.webSite.repository'].$path))
                    return $config['wcm.webSite.urlRepository'].$path;
                else
                    return $config['wcm.webSite.urlRepository'].$originalPath;
            }
        }
        return false;
    }*/
	 public function getPhotoUrlByFormat($format, $alternate = "w250") {
        $config = wcmConfig::getInstance();
        if (! empty($this->permalinks)) {
            $path = str_replace("%format%", $format, $this->permalinks, $countPath);
            $alternatePath = str_replace("%format%", $alternate, $this->permalinks, $countAlternate);
            $originalPath = str_replace("%format%", "original", $this->permalinks, $countOriginal);

            if ($countPath > 0 || $countAlternate > 0 || $countOriginal > 0) {
                if (file_exists($config['wcm.webSite.repository'].$path))
                    return $config['wcm.webSite.urlRepository'].$path;
                elseif (file_exists($config['wcm.webSite.repository'].$alternatePath))
                    return $config['wcm.webSite.urlRepository'].$alternatePath;
                else
                    return $config['wcm.webSite.urlRepository'].$originalPath;
            }
        }
        return false;
    }

    /**
     * Test if a photo exists using its format
     *
     * @param string format
     *
     * @return boolean
     */

    public function boolPhotoFormatExists($format) {
        $config = wcmConfig::getInstance();
        if (! empty($this->permalinks)) {
	        $path = str_replace("%format%", $format, $this->permalinks, $countPath);
            if ($countPath > 0) {
                return file_exists($config['wcm.webSite.repository'].$path);
            } else
				return false;
		}
        return false;
    }

    public function getFormats() {
        $pictures = array();
        $height = array();
        $width = array();
		$squa = array();

        $config = wcmConfig::getInstance();
        $formats = unserialize($this->formats);
        foreach ($formats as $format=>$infos) {
            $item = array();

            $item["filename"] = $config['wcm.webSite.repository'].$this->getPhotoRelativePathByFormat($format);
            $item["fileurl"] = $this->getPhotoUrlByFormat($format);

            $item["format"] = $format;
            $item["width"] = $infos["width"];
            $item["height"] = $infos["height"];
            $item["weight"] = $infos["weight"];

            if ($format[0] == "h") {
                $height[] = $item;
            }
            if ($format[0] == "w") {
                $width[] = $item;
            }
			if ($format[0] == "s") {
                $squa[] = $item;
            }

            if ($format == "original") {
                $pictures["original"] = $item;
            }
        }

        $pictures["height"] = $height;
        $pictures["width"] = $width;
		$pictures["square"] = $squa;
        return $pictures;
    }

    /**
     * process images and decline to different formats available (configuration.xml)
     *
     * @param string fullPath
     * @param string relativePath
     * @param string image
     *
     * @return info array of different format
     */

    static function getPhotoFormats($original = false) {
        $config = wcmConfig::getInstance();
        $formats = explode(",", $config['afprelax.photoResizeFormats.formats']);
        if ($original == true)
            array_push($formats, "original");
        return $formats;
    }

    static function checkPhotoFormat($format) {
        $config = wcmConfig::getInstance();
        $formats = explode(",", $config['afprelax.photoResizeFormats.formats']);
        if (in_array($format, $formats))
            return true;
        else
            return false;
    }

    public function processImage($fullPath, $relativePath, $image)
    {
        $config = wcmConfig::getInstance();
        // délai supplémentaire pour le traitement des photos
        ini_set('max_execution_time', 180);
        $tabinfos = array();

        if (!file_exists($fullPath.$image) || empty($image))
        {
            //echo("introuvable : $fullPath$image \n");
            return null;
        }

        list($widthImgOriginal, $heightImgOriginal, $typeImgOriginal, $attrImgOriginal) = getimagesize($fullPath.$image, $iptc);

        // add IPTC infos
        //if (isset($iptc["APP13"]))
        //	$tabinfos['original']['iptc'] = $this->getIptcFromImage($iptc);

        $tabinfos['original']['width'] = $widthImgOriginal;
        $tabinfos['original']['height'] = $heightImgOriginal;

        @chmod($fullPath, 0775);
        @chmod($fullPath.$image, 0664);

        $result = array();
        //exec("identify -verbose ".$fullPath.$image, $results);
        if (! empty($result))
            $tabinfos['original']['infos'] = $results;

        $tabinfos['original']['weight'] = round((filesize($fullPath.$image) / 1024), 3).' ko';

        $formats = explode(",", $config['afprelax.photoResizeFormats.formats']);

        foreach ($formats as $format)
        {
            $formatType = $format[0];
            $formatValue = substr($format, 1, strlen($format));

            if ($formatType == "h")
                $formatCheck = "height";
            else if ($formatType == "w")
                $formatCheck = "width";

            if (($formatType != "s") && (intval($formatValue) <= $tabinfos['original'][$formatCheck]))
            {
                $fileName = str_replace('.original.', '.'.$format.'.', $image);
                $photoResizer = new wcmImageHelper($fullPath.$image);

                if ($formatCheck == "height")
                    $photoResizer->resize($fullPath.$fileName, 100000, $formatValue);
                else
                    $photoResizer->resize($fullPath.$fileName, $formatValue, 100000);

                //echo("création de $fullPath$fileName \n");
                //Change resolution
                if (file_exists($fullPath.$fileName))
                {
                    $quality = " -quality 100 ";
                    if (intval($formatValue) <= '100')
                        $quality = " -quality 70 ";

                    exec("convert ".$fullPath.$fileName." -resample 72x72 ".$quality.$fullPath.$fileName);
                    // echo("convert 72x72 de $fullPath$fileName \n");
                }

                list($widthImg, $heightImg, $typeImg, $attrImg) = getimagesize($fullPath.$fileName);

                $tabinfos[$formatType.$formatValue]['width'] = $widthImg;
                $tabinfos[$formatType.$formatValue]['height'] = $heightImg;

                $tabinfos[$formatType.$formatValue]['weight'] = round((filesize($fullPath.$fileName) / 1024), 3).' ko';
            }
            else if ($formatType == "s") // cas du cropping carré
            {
            	$fileName = str_replace('.original.', '.'.$format.'.', $image);
                $photoResizerOrigin = new wcmImageHelper($fullPath.$image);

                if ($tabinfos['original']['width'] > $tabinfos['original']['height'])
                {
                	// cas du paysage
                	$photoResizerOrigin->resize($fullPath.$fileName, 100000, $formatValue);
                	$photoResizer = new wcmImageHelper($fullPath.$fileName);
                	$photoResizer->squareCrop($fullPath.$fileName, $formatValue);
                }
                else
                {
                	// cas du portrait
                	$photoResizerOrigin->resize($fullPath.$fileName, $formatValue, 100000);
                	$photoResizer = new wcmImageHelper($fullPath.$fileName);
                	$photoResizer->squareCrop($fullPath.$fileName, $formatValue);
                }

                if (file_exists($fullPath.$fileName))
                {
                    $quality = " -quality 100 ";
                    if (intval($formatValue) <= '100')
                        $quality = " -quality 70 ";

                    exec("convert ".$fullPath.$fileName." -resample 72x72 ".$quality.$fullPath.$fileName);
                    // echo("convert 72x72 de $fullPath$fileName \n");
                }

                list($widthImg, $heightImg, $typeImg, $attrImg) = getimagesize($fullPath.$fileName);

                $tabinfos[$formatType.$formatValue]['width'] = $widthImg;
                $tabinfos[$formatType.$formatValue]['height'] = $heightImg;

                $tabinfos[$formatType.$formatValue]['weight'] = round((filesize($fullPath.$fileName) / 1024), 3).' ko';
            }
        }

        unset($im);

        return $tabinfos;
    }

    /**
     * Save object in database
     *
     * @param array $source An optional assoc array for binding to class vars (default: null)
     *
     * @return boolean True on success, false otherwise
     */

    public function save($source = null) {
        $session = wcmSession::getInstance();
        $config = wcmConfig::getInstance();

        if (isset($source['original']))
            $this->original = $source['original'];

        $date = (isset($this->createdAt)) ? $this->createdAt : date('Y-m-d h:i:s');
        $creationDate = dateOptionsProvider::fieldDateToArray($date);

        $publicationPath = 'illustration/photo/'.$creationDate['year'].'/'.$creationDate['month'].'/'.$creationDate['day'].'/';
        $filename = str_replace(".original.", ".%format%.", $this->original);

        $source['permalinks'] = $publicationPath.$filename;
        $dir = $config['wcm.webSite.repository'].$publicationPath;
        $infos = $this->processImage($dir, $publicationPath, $this->original);
        //$this->updateMustGenerate(0);
        //print_r($infos);

        $source['formats'] = serialize($infos);
        $source['mustGenerate'] = 0;

        if (isset($source['content_photo_title'])) 			$this->title 	= bizobject::cleanStringFromSpecialChar($source['content_photo_title']);
        if (isset($source['content_photo_specialUses'])) 	$this->specialUses = bizobject::cleanStringFromSpecialChar($source['content_photo_specialUses']);
        if (isset($source['content_photo_credits'])) 		$this->credits 	= bizobject::cleanStringFromSpecialChar($source['content_photo_credits']);

        return parent::save($source);
    }

    public function getIptcFromImage($info) {
        if (isset($info["APP13"])) {
            $iptc = iptcparse($info["APP13"]);
            if (is_array($iptc)) {
                $iptcInfos = array();
                $iptcInfos['caption'] = (isset($iptc["2#120"][0])) ? $iptc["2#120"][0] : '';
                $iptcInfos['graphic_name'] = (isset($iptc["2#005"][0])) ? $iptc["2#005"][0] : '';
                $iptcInfos['urgency'] = (isset($iptc["2#010"][0])) ? $iptc["2#010"][0] : '';
                $iptcInfos['category'] = (isset($iptc["2#015"][0])) ? $iptc["2#015"][0] : '';
                // note that sometimes supp_categories contans multiple entries
                $iptcInfos['supp_categories'] = (isset($iptc["2#020"][0])) ? $iptc["2#020"][0] : '';
                $iptcInfos['spec_instr'] = (isset($iptc["2#040"][0])) ? $iptc["2#040"][0] : '';
                $iptcInfos['creation_date'] = (isset($iptc["2#055"][0])) ? $iptc["2#055"][0] : '';
                $iptcInfos['photog'] = (isset($iptc["2#080"][0])) ? $iptc["2#080"][0] : '';
                $iptcInfos['credit_byline_title'] = (isset($iptc["2#085"][0])) ? $iptc["2#085"][0] : '';
                $iptcInfos['city'] = (isset($iptc["2#090"][0])) ? $iptc["2#090"][0] : '';
                $iptcInfos['state'] = (isset($iptc["2#095"][0])) ? $iptc["2#095"][0] : '';
                $iptcInfos['country'] = (isset($iptc["2#101"][0])) ? $iptc["2#101"][0] : '';
                $iptcInfos['otr'] = (isset($iptc["2#103"][0])) ? $iptc["2#103"][0] : '';
                $iptcInfos['headline'] = (isset($iptc["2#105"][0])) ? $iptc["2#105"][0] : '';
                $iptcInfos['source'] = (isset($iptc["2#110"][0])) ? $iptc["2#110"][0] : '';
                $iptcInfos['photo_source'] = (isset($iptc["2#115"][0])) ? $iptc["2#115"][0] : '';
            }

            return $iptcInfos;
        } else
            return false;
    }

    /**
     * Expose 'urls' of cropped images to the getAssocArray()
     *
     * @return array An assoc array ('croppConfig' => 'fileName')
     */

    public function getAssoc_urlByFormat() {
        $ret = array();
        //$original =  $this->getPhotoUrlByFormat();

        $formats = $this->getPhotoFormats(true);
        foreach ($formats as $format) {
            $ret[$format] = $this->getPhotoUrlByFormat($format);
        }

        return $ret;
    }


    public function getAssoc_urls($toXML = false) {
        $config = wcmConfig::getInstance();
        $cmanager = new croppingManager($this, WCM_DIR.'/business/xml/photosratios/default.xml');

        return $cmanager->getFilesNames($config['wcm.backOffice.photosPath']);
    }

    public function getAssoc_thumbnail() {
        if (! empty($this->permalinks))
            return $this->getPhotoUrlByFormat('w100');
        else
            return false;
    }

    public function getAssoc_minithumbnail() {
        if (! empty($this->permalinks))
            return $this->getPhotoUrlByFormat('w50');
        else
            return false;
    }

    public function getAssoc_thumbnailPic($nameSize = null, $getFullPath = false) {
        $config = wcmConfig::getInstance();
        $creationDate = dateOptionsProvider::fieldDateToArray($this->createdAt);
        $filename = $this->original;
        $dir = $config['wcm.webSite.repository'].'illustration/photo/'.$creationDate['year'].'/'.$creationDate['month'].'/'.$creationDate['day'].'/';

        if ($nameSize != null && $this->formats != NULL) {
            $filenameDesired = str_replace('.original.', '.'.$nameSize.'.', $filename);

            if (file_exists($dir.$filenameDesired)) {
                if ($getFullPath) {
                    return array('path'=>$dir, 'pic'=>$filenameDesired);
                } else {
                    return $filenameDesired;
                }
            } else if (file_exists($dir.str_replace('original', $nameSize, $filenameDesired))) {
                if ($getFullPath) {
                    return array('path'=>$dir, 'pic'=>str_replace('original', $nameSize, $filenameDesired));
                } else {
                    return str_replace('original', $nameSize, $filenameDesired);
                }
            } else {
                if ($getFullPath) {
                    return array('path'=>$dir, 'pic'=>$filename);
                } else {
                    return $filename;
                }
            }
        } else {
            if ($this->formats != NULL) {
                $formats = unserialize($this->formats);
                foreach ($formats as $label=>$format) {
                    if ($label == 'w400' || $label == 'w250' || $label == 'w100' || $label == 'w50') {
                        $fileNameDesired = str_replace('.original.', '.'.$label.'.', $filename);

                        if (file_exists($dir.$fileNameDesired)) {
                            if ($getFullPath) {
                                return array('path'=>$dir, 'pic'=>$fileNameDesired);
                            } else {
                                return $fileNameDesired;
                            }
                        } else if (file_exists($dir.str_replace('original', $label, $fileNameDesired))) {
                            if ($getFullPath) {
                                return array('path'=>$dir, 'pic'=>str_replace('original', $label, $fileNameDesired));
                            } else {
                                return str_replace('original', $label, $fileNameDesired);
                            }
                        }
                    }
                }
            }

            if ($getFullPath) {
                return array('path'=>$dir, 'pic'=>$filename);
            } else {
                return $filename;
            }
        }
    }


    public function getPictureDownloads($toXML = false) {
        $config = wcmConfig::getInstance();
        $creationDate = dateOptionsProvider::fieldDateToArray($this->createdAt);
        $filename = $this->original;
        $dir = $config['wcm.webSite.repository'].'illustration/photo/'.$creationDate['year'].'/'.$creationDate['month'].'/'.$creationDate['day'].'/';
        $dirPublication = $config['wcm.webSite.urlRepository'].'illustration/photo/'.$creationDate['year'].'/'.$creationDate['month'].'/'.$creationDate['day'].'/';
        $pictures = array();

        if (isset($this->formats) && $this->formats != NULL) {
            $formats = unserialize($this->formats);

            foreach ($formats as $format=>$sizes) {
                $pictures[] = array('label'=>$sizes['width'].'x'.$sizes['height'], 'weight'=>$sizes['weight'], 'fileName'=>str_replace('original', $format, $filename), 'filePath'=>$dir);
            }
        }

        return $pictures;
    }

    /**
     * Computes the sql where clause matching foreign constraints
     * => This method must be overloaded by child class
     *
     * @param string $of Assoc Array with foreign constrains (key=className, value=id)
     *
     * @return string Sql where clause matching "of" constraints or null
     */

    function ofClause($of) {
        if ($of == null || !is_array($of))
            return;

        $sql = null;

        foreach ($of as $key=>$value) {
            switch ($key) {
                case "site":
                    if ($sql != null)
                        $sql .= " AND ";
                    $sql .= "siteId=".$value;
                    break;

                case "channel":
                    if ($sql != null)
                        $sql .= " AND ";
                    $sql .= "channelId=".$value;
                    break;

                case "article":
                case "slideshow":
                    // TODO: override beginEnum to retrieve bizRelation
                    break;
            }
        }
        return $sql;
    }

    /**
     * Gets the 'semantic' text that will be passed to the Text-Mining Engine
     *
     * @return string The semantic text to mine
     */

    public function getSemanticText() {
        $content = '';

        if ($this->title)
            $content .= trim($this->title, " \t\n\r\0\x0B.").".\n";
        if ($this->keywords)
            $content .= trim($this->keywords, " \t\n\r\0\x0B.").".\n";

        if ($this->xmlTags != NULL) {
            foreach ($this->xmlTags['tags'] as $tag) {
                $content .= ','.$tag;
            }
        }

        $contents = $this->getContents();
        if ($contents != NULL) {
            foreach ($contents as $contentItem) {
                if ($contentItem->description != NULL)
                    $content .= trim($contentItem->description, " \t\n\r\0\x0B.").".\n";
                if ($contentItem->text != NULL)
                    $content .= trim($contentItem->text, " \t\n\r\0\x0B.").".\n";
            }
        }

        return $content;
    }

    /**
     * Check validity of object
     *
     * A generic method which can (should ?) be overloaded by the child class
     *
     * @return boolean true when object is valid
     *
     */

    public function checkValidity() {
        if ($this->source && strlen($this->source) > 255) {
            $this->lastErrorMsg = _BIZ_ERROR_SOURCE_TOO_LONG;
            return false;
        }

        if ($this->sourceId && strlen($this->sourceId) > 255) {
            $this->lastErrorMsg = _BIZ_ERROR_SOURCE_ID_TOO_LONG;
            return false;
        }

        if ($this->sourceVersion && strlen($this->sourceVersion) > 255) {
            $this->lastErrorMsg = _BIZ_ERROR_SOURCE_VERSION_TOO_LONG;
            return false;
        }

        if ($this->permalinks && strlen($this->permalinks) > 255) {
            $this->lastErrorMsg = _BIZ_ERROR_PERMALINKS_TOO_LONG;
            return false;
        }

        if (property_exists($this, 'siteId') && $this->siteId == '') {
            $this->lastErrorMsg = _BIZ_ERROR_SITE_IS_MANDATORY;
            return false;
        }

        if (strlen($this->title) > 255) {
            $this->lastErrorMsg = _BIZ_ERROR_TITLE_TOO_LONG;
            return false;
        }

        if ($this->credits && strlen($this->credits) > 255) {
            $this->lastErrorMsg = _BIZ_ERROR_CREDITS_TOO_LONG;
            return false;
        }

        if ($this->keywords && strlen($this->keywords) > 255) {
            $this->lastErrorMsg = _BIZ_ERROR_KEYWORDS_TOO_LONG;
            return false;
        }

    	if (empty($this->channelId))
        {
            $this->lastErrorMsg = _BIZ_ERROR_CHANNELID_IS_MANDATORY;
            //return false;
        }

        return true;
    }

    /**
     * Return a cleaned file name
     *
     * @param string 	$originalFileName 	Original file name
     *
     * @return string 	$fileName 			Cleaned file name
     *
     */

    static function cleanFileName($originalFileName, $maxlength = 15) {
        $fileName = trim(strtr($originalFileName, 'À??ÂÃÄÅÇÈÉÊËÌ??Î??ÒÓÔÕÖÙÚÛÜ??àáâãäåçèéêëìíîïðòóôõöùúûüýÿ', 'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy'));
        $fileName = preg_replace('/([^.a-z0-9]+)/i', '_', $fileName);
        $fileName = strtolower($fileName);

        $fileNameWithoutExt = substr($fileName, 0, strrpos($fileName, '.') - 1);
        $ext = substr($fileName, strrpos($fileName, '.'));
        if (strlen($fileNameWithoutExt) > $maxlength) {
            $fileName = substr($fileNameWithoutExt, 0, $maxlength).$ext;
        }

        return $fileName;
    }

    /**
     * Return the final name for the uploaded Pic
     *
     * @param string 	$originalFileName 	Original file name
     *
     * @return string 	$dir 				Dir where the pic is uploaded
     *
     */

    static function getFinalPicName($originalFileName, $dir) {
        $ext = substr($originalFileName, strrpos($originalFileName, '.'));
        $fileNameWithoutExt = substr($originalFileName, 0, strrpos($originalFileName, '.'));
        $fileNameWithoutExt = str_replace(' ', '', $fileNameWithoutExt);
        $fileNameWithoutExt = str_replace('.', '', $fileNameWithoutExt);

        $hash = sha1($fileNameWithoutExt);
        $hashCut = substr($hash, rand(0, strlen($hash) - 6), 5);
        $timeCut = date('His');

        $newFileNameOriginal = $fileNameWithoutExt.'.'.$hashCut.$timeCut.'.original'.$ext;
        if (substr($newFileNameOriginal, 0, 1) == '_') {
            $newFileNameOriginal = substr($newFileNameOriginal, 5);
        }
        //$newFileNameThumb = str_replace('original', 'thumb', $newFileNameOriginal);

        $fileName = $newFileNameOriginal;
        //'thumb' => $newFileNameThumb);

        return $fileName;
    }

    public function getWidthAndHeight($formatNameSeek) {
        foreach (unserialize($this->formats) as $formatName=>$sizes) {
            if ($formatNameSeek == $formatName) {
                return $sizes;
            }
        }

        return array('width'=>'*', 'height'=>'*');
    }

    public function getInfosByFormat($format) {
        $formats = unserialize($this->formats);

        if (isset($formats[$format])) {
            return $formats[$format];
        } else {
            return $formats["original"];
        }
    }

}

