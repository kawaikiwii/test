<?php
/**
 * Project:     WCM
 * File:        biz.croppingManager.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * The cropping manager helps management of images
 * and create/delete several thumnails cropped from an original pictures
 * Note: Cropping parameters are store in the '#__ratio' table
 */
class croppingManager
{
    const GET_PIC_FILENAME_FUNCTION = 'getPictureFileName';
    /**
     * (array) Available ratio settings
     */
    public $ratios = array();

    /**
     * (array) Available formats
     */
    public $formats = array();

    /**
     * (string) Original image source
     */
    public $src = null;

    /**
     * (wcmBizobject) Underlying bizobject
     */
    public $bizobject;

    /**
     * (bool) TRUE when data has been already loaded
     */
    public $loaded = false;

    /**
     * (string) XML representing ratio settings
     */
    public $xml = false;

    /**
     * Constructor
     *
     * @param wcmObject $bizobject  bizobject to crop
     * @param String    $xml        Path to the XML configuration file to load
     *
     */
    function __construct($bizobject, $xml)
    {
        $this->bizobject = $bizobject;
        $this->initFromXML($xml);
        if($this->bizobject)
        {
            $this->src = $this->bizobject->original;
        }
    }

    /**
     *  List of action to do on checkin
     *
     *  @param array $data array for binding cropping informations
     */
    public function onCheckin($data)
    {
        $this->saveRatios($data);
        // $this->generatePictures();
    }

    /**
     *  List of action to do on delete
     */
    public function onDelete()
    {
        $this->removePictures();
    }


    /**
     *  Init the Cropping Manager from a given XML file
     *
     *  @param String $file Path to the XML file
     */
    private function initFromXML($file)
    {
        $xml = new DOMDocument();
        $xml->load($file);

        $ratios = $xml->getElementsByTagName('ratio');

        foreach($ratios as $ratio)
        {

            $this->ratios[$ratio->getAttribute('name')] = array(
                            'rx'   => (int)$ratio->getAttribute('rx'),
                            'ry'   => (int)$ratio->getAttribute('ry'),
                            'x1'   => 0,
                            'x2'   => 0,
                            'y1'   => 0,
                            'y2'   => 0);

            $sizes = $ratio->getElementsByTagName('size');

            foreach($sizes as $size) {
                $this->formats[$size->getAttribute('code')] = array(
                                'ratio' => $ratio->getAttribute('name'),
                                'width' => (int)$size->getAttribute('width'),
                                'height' => (int)$size->getAttribute('height'));
            }

        }
        $this->xml = $xml;
    }

    /**
     *  Generate HiddenFileds witch store cropping informations based on different ratios in mod_cropping
     */
    public function generateHiddenFields()
    {
        if(!$this->loaded) $this->loadRatios();
        foreach($this->ratios as $value)
        {
            $name = 'ratio' . $value['rx'] . 'x' . $value['ry'];
            echo '<input name="' . $name . '-x1" id="' . $name . '-x1" type="hidden" value="' . $value['x1'] . '" />';
            echo '<input name="' . $name . '-x2" id="' . $name . '-x2" type="hidden" value="' . $value['x2'] . '" />';
            echo '<input name="' . $name . '-y1" id="' . $name . '-y1" type="hidden" value="' . $value['y1'] . '" />';
            echo '<input name="' . $name . '-y2" id="' . $name . '-y2" type="hidden" value="' . $value['y2'] . '" />';
        }
    }

    /**
     * Save all the ratios in the bizobject ratios propertie
     *
     * @param   array|null  $data   REQUEST field
     */
    public function saveRatios($data)
    {
        $sql = '';
        $params = array();

        $this->bizobject->ratios = array();

        foreach($this->ratios as $name => &$value)
        {
            $key = 'ratio' . $value['rx'] . 'x' . $value['ry'];
            $value['x1'] = getArrayParameter($data, $key.'-x1', 0);
            $value['x2'] = getArrayParameter($data, $key.'-x2', 0);
            $value['y1'] = getArrayParameter($data, $key.'-y1', 0);
            $value['y2'] = getArrayParameter($data, $key.'-y2', 0);

            // FRFR - Si les formats ratios ne sont pas définis
            if (($value['x1'] == 0) && ($value['x2'] == 0) && ($value['y1'] == 0 ) && ($value['y2'] == 0))
            {

                $data['width'] = getArrayParameter($data, 'width', 0);
                $data['height'] = getArrayParameter($data, 'height', 0);

                $croppingType = "";
                // if landscape
                if ($value['rx'] > $value['ry'])
                {
                    //if bigger Ratio
                    if ($data['width']*$value['ry']/$value['rx']>$data['height'])
                            $croppingType = "portrait";
                    else
                            $croppingType = "landscape";
                }
                // else portrait
                else
                {
                    //if bigger Ratio
                    if ($data['height']*$value['rx']/$value['ry']>$data['width'])
                            $croppingType = "landscape";
                    else
                            $croppingType = "portrait";
                }

                switch ($croppingType)
                {
                    case "portrait":
                            $value['x1'] = ($data['width']/2) - ($data['height']*$value['rx']/$value['ry'])/2;
                            $value['x2'] = ($data['width']/2) + ($data['height']*$value['rx']/$value['ry'])/2;
                            $value['y1'] = 0;
                            $value['y2'] = $data['height'];
                    break;
                    case "landscape":
                            $value['x1'] = 0;
                            $value['x2'] = $data['width'];
                            $value['y1'] = ($data['height']/2) - ($data['width']*$value['ry']/$value['rx'])/2;
                            $value['y2'] = ($data['height']/2) + ($data['width']*$value['ry']/$value['rx'])/2;
                    break;
                }
            }

            $this->bizobject->ratios[$name] = $value;
        }

        $this->loaded = true;
    }

    /**
     *  Get an array of all positions use to generate the photo cropping
     *
     *  @param array $data array for binding cropping informations
     *
     *  @return array The array of all positions
     */
    public function getRatios($data)
    {
        $array = array();
        foreach($this->ratios as $name => &$value)
        {
            $key = 'ratio' . $value['rx'] . 'x' . $value['ry'];
            $array[$key . '-x1'] = getArrayParameter($data, $key.'-x1', 0);
            $array[$key . '-x2'] = getArrayParameter($data, $key.'-x2', 0);
            $array[$key . '-y1'] = getArrayParameter($data, $key.'-y1', 0);
            $array[$key . '-y2'] = getArrayParameter($data, $key.'-y2', 0);
        }
        return $array;
    }

    /**
     *  Load all the ratios from the ratios field in the bizObject
     */
    public function loadRatios()
    {
        foreach($this->bizobject->ratios as $name => $row)
        {
            if(array_key_exists($name, $this->ratios))
            {
                $this->ratios[$name]['x1'] = $row['x1'];
                $this->ratios[$name]['x2'] = $row['x2'];
                $this->ratios[$name]['y1'] = $row['y1'];
                $this->ratios[$name]['y2'] = $row['y2'];
            }
        }
        $this->loaded = true;
    }

    /**
     *  Generate a picture with a specific given format
     *
     *  @param string   $format Name of the format witch is define in the XML Configuration
     */
    private function generatePicture($format)
    {		 
		$config = wcmConfig::getInstance();

		$creationDate = dateOptionsProvider::fieldDateToArray($this->bizobject->createdAt);
		$dir = $config['wcm.webSite.path'].'/illustration/photo/'.$creationDate['year'].'/'.$creationDate['month'].'/'.$creationDate['day'].'/';
        $image = new wcmImageHelper($dir . $this->bizobject->original);
        $size = $this->formats[$format];

        if(!$this->loaded) $this->loadRatios();

        if(array_key_exists($size['ratio'], $this->ratios))
        {
            $ratio = $this->ratios[$size['ratio']];
            $fileName = $this->getPictureFileName($format);

            if($ratio['x1'] == 0 && $ratio['x2'] == 0 && $ratio['y1'] == 0 && $ratio['y2'] == 0)
                $image->thumb($dir.$fileName, $size['width'], $size['height']);
            else
                $image->crop($dir.$fileName, $size['width'], $size['height'], $ratio['x1'], $ratio['y1'], $ratio['x2'], $ratio['y2']);
        }
    }

    /**
     *  Generate all pictures
     */
    public function generatePictures()
    {
        foreach($this->formats as $format => $size)
        {
            $this->generatePicture($format);
        }
    }

    /**
     *  Remove all generated pictures
     */
    public function removePictures()
    {
        if($this->bizobject->id == 0) return false;

        $config = wcmConfig::getInstance();
        //$dir = $config['wcm.webSite.path'].$config['wcm.backOffice.photosPath'];
		$creationDate = dateOptionsProvider::fieldDateToArray($this->bizobject->createdAt);
		$dir = $config['wcm.webSite.path'].'/illustration/photo/'.$creationDate['year'].'/'.$creationDate['month'].'/'.$creationDate['day'].'/';

        foreach($this->formats as $format => $size)
        {
            $fileName = $this->getPictureFileName($format);

            if(file_exists($dir.$fileName))
                    unlink($dir.$fileName);
        }
    }

    /**
     *
     */
    public function getFilesNames($prefix="")
    {
        $returnedArray = array();
        foreach($this->formats as $format => $size)
        {
            $fileName = $this->getPictureFileName($format);
            $returnedArray[$format] = $prefix.$fileName;
        }
        return $returnedArray;
    }

    /**
     *  Get the filename of a specific format to generate
     *
     *  @param string $format Name of the format witch is define in the XML Configuration
     */
    private function getPictureFileName($format)
    {
        $method = self::GET_PIC_FILENAME_FUNCTION;
        if(method_exists($this->bizobject, $method))
                return $this->bizobject->$method($format);
        else
                //return get_class($this->bizobject) . '.' . $this->bizobject->id . '.' . $format . '.jpg';
				$fileNameWithoutExt = substr($this->bizobject->original, 0, strrpos($this->bizobject->original, '.') - 1);
				$ext = substr($this->bizobject->original, strrpos($this->bizobject->original, '.'));
				return str_replace('original', 'gen', $fileNameWithoutExt) . '.' . $format . $ext;
    }

    /**
     *
     */
    public function displayPictures()
    {
        $config = wcmConfig::getInstance();
        //$dir = $config['wcm.webSite.path'].$config['wcm.backOffice.photosPath'];
		$creationDate = dateOptionsProvider::fieldDateToArray($this->bizobject->createdAt);
		$dir = $config['wcm.webSite.path'].'/illustration/photo/'.$creationDate['year'].'/'.$creationDate['month'].'/'.$creationDate['day'].'/';

        foreach($this->ratios as $name => $ratio)
        {
            if(array_key_exists($name, $this->sizes))
            {

                $size = $this->sizes[$name][0];
                //foreach($this->sizes[$name] as $size)
                //{
                $fileName = $this->getPictureFileName($name);

                echo $name." (".$size[0]."x".$size[1].")<br/><img src='".$dir.$fileName."'><br/><br/>";
                //}
            }
        }
    }

    /**
     *  Get a photo URL and generate it if needed
     *
     *  @param  string  $format Name of the format witch is define in the XML Configuration
     *  @param  boolean $force  set to true if you want to force the file generation
     *
     *  @return string  URL of the generated URL
     */
    public function getPhotoUrl($format, $force = false)
    {
        if(array_key_exists($format, $this->formats))
        {
            $config = wcmConfig::getInstance();

            //$dir = WCM_DIR . DIRECTORY_SEPARATOR;
			$creationDate = dateOptionsProvider::fieldDateToArray($this->bizobject->createdAt);
			$dir = $config['wcm.webSite.path'].'/illustration/photo/'.$creationDate['year'].'/'.$creationDate['month'].'/'.$creationDate['day'].'/';
            
			//$fileName = $config['wcm.backOffice.photosPath'] . $this->getPictureFileName($format);
			$fileName = $dir . $this->getPictureFileName($format);

            if(!file_exists($dir.$fileName) || $force)
                    $this->generatePicture($format);

            return $fileName;
        }
        else
        {
            return false;
        }
    }
}
