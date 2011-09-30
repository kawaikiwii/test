<?php
/**
 * Project:     WCM
 * File:        wcm.toolbox.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * Returns the root folder where to store trace logs
 * Note: The returned value will contains a trailing slash
 *
 * @return string Root folder where trace log are stored
 */
function wcmGetTraceFolder()
{
	$config = wcmConfig::getInstance();
    return $config['wcm.logging.path'] . 'traces/';
}

/**
 * Returns the name of the file use for trace (see wcmTrace function)
 *
 * @return string Name of trace file
 */
function wcmGetTraceFile()
{
    return wcmGetTraceFolder() . date('Y-m') . '/wcm.' . date('Y-m-d') . '.log';
}

/**
 * Traces each of its arguments by printing it to TRACE_FILE using
 * print_r.
 *
 * @param mixed ... the arguments to trace
 */
function wcmTrace($message)
{
    $text = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
    saveToFile(wcmGetTraceFile(), $text, true);
}

/**
 * Compute the name of the log file based on configuration
 * and optional format current date with any {...} tag
 * Example: "wcm-{Y-m-d}.log"
 *
 * @return string Name of the log file
 */
function getLogFilename()
{
    $config = wcmConfig::getInstance();

    return preg_replace("/{([^}]+)}/e",
                        "date('\\1')",
                        $config['wcm.logging.path'] . $config['wcm.logging.fileName']);
}

/**
 * Returns a 'raw' text from an XHTML source
 * (remove all tags, javascript, extra spaces..
 *
 * @param string $text Initial text (XML, HTML, javascript...)
 *
 * @return string Raw (cleaned-up) text
 */
function getRawText($text)
{
    // Remove all tags, entities and extra white spaces
    $search = array ('@<script[^>]*?>.*?</script>@si',
                    '@<[\/\!]*?[^<>]*?>@si',
                    '@([\r\n])[\s]+@',
                    '@&[^;]+;@i');

    $replace = array ('',
                    '',
                    '\1',
                    '');

    return preg_replace($search, $replace, $text);
}


/**
 * Encode HTML entities in UTF-8
 *
 * @param string $text Text to encode
 *
 * @return string Encoded text
 */
function textH8($text)
{
    return htmlentities($text, ENT_COMPAT, 'UTF-8');
}

/**
 * Echo and encode HTML entities in UTF-8
 *
 * @param string $text Text to echo
 */
function echoH8($text)
{
    echo textH8($text);
}

/**
 * Convert an object to an assoc array
 *
 * @param object $object           Object to convert
 * @param bool   $useGetAssocArray Whether to try to use getAssocArray() on objects (default is false)
 *
 * @return array Associative array representing the object
 */
function object2Array($object, $useGetAssocArray = false)
{
    $vars = null;
    if (is_array($object))
    {
        $vars = $object;
    }
    elseif (is_object($object))
    {
        if ($useGetAssocArray && method_exists($object, 'getAssocArray'))
            return $object->getAssocArray(false);

        $vars = get_object_vars($object);
    }

    if (!is_array($vars))
        return strval($object);

    $array = null;
    foreach ($vars as $key => $value)
        $array[$key] = object2Array($value, $useGetAssocArray);

    return $array;
}

/**
 * Returns a named parameter from an array or a default value if named parameter is unset
 *
 * @param   $arr     array      An assoc array
 * @param   $name    string     The key name into to retrieve value from the assoc array
 * @param   $default mixed      The default value to return if key does not exists in array
 *
 * @return mixed
 *
 */
function getArrayParameter($arr, $name, $default=null)
{
    if (!is_array($arr)) return $default;

    if (array_key_exists($name, $arr))
    {
        // Trim parameter when it is a string
        if (is_string($arr[$name]))
            $arr[$name] = trim($arr[$name]);

        return $arr[$name];
    }
    else
    {
        // Return default value
        return $default;
    }
}

/**
 * As with getArrayParameter(), returns a named parameter from an
 * array or a default value if named parameter is unset.
 *
 * Also updates the value of the named parameter in the array.
 *
 * @param   $arr     array      An assoc array
 * @param   $name    string     The key name into to retrieve value from the assoc array
 * @param   $default mixed      The default value to return if key does not exists in array
 *
 * @return mixed
 *
 */
function getUpdatedArrayParameter(&$arr, $name, $default=null)
{
    $value = getArrayParameter($arr, $name, $default);
    $arr[$name] = $value;
    return $value;
}

/**
 * Checks if constant is defined or not
 *
 * @param $const    string to check
 *
 * returns null if constant does not exist
 * returns constant value if constant exists
 *
 */
function getConst($const)
{
    // As of PHP 5.2.4, code like this causes a fatal error:
    //
    // defined('::foo')
    // defined(' ::bar')
    // ...
	
    if (preg_match('/^\s*::/', $const))
        return $const;
    elseif (defined($const))
        return constant($const);
    else
        return $const;
}

/**
 * Returns an array of all public (non static) properties of an object
 *
 * @param object $object The object to retrieve properties from
 * @param bool $ignoreOldPrivatePrefix TRUE to ignore properties starting with '_'
 *
 * @return array Array of all public properties
 */
function getPublicProperties($object, $ignoreOldPrivatePrefix = true)
{
    // adoy was there!
    $array = array();
    foreach($object as $key => $value)
    {
        
        if (!$ignoreOldPrivatePrefix || $key[0] != '_')
        {
            $array[$key] = $value;
        }
    }
    return $array;
}

/**
 *
 * Retrieves the default language from browser (web navigator)
 *
 * @param string $default Default language if none found
 *
 */
function getDefaultLanguage($default='en')
{
    // The navigator may returns more than one language
    $a = explode(",", getArrayParameter($_SERVER, 'HTTP_ACCEPT_LANGUAGE', $default), 1);

    // Returns the first two letters in lowercase
    return strtolower(substr($a[0], 0, 2));
}

/**
 * Parse a text and returns an array of words with their occurences
 *
 * @param string    The source string to parse
 * @param array     Stop words array
 * @param bool      Only return a single occurence of a same word (default true)
 *
 * @return an array of tokenized words (lowercase)
 *
 */
function parseText($text, $stopwords = null, $singleOccurence = true)
{
    // Empty stop words ?
    if (!$stopwords || !is_array($stopwords)) $stopwords = array();

    // Normalize punctuation
    $text   = preg_replace('/()*(["\'\.,;:\/\(\)\?!])()*/', ' \\2 ', $text);

    // Remove extra whitespaces
    $text   = preg_replace('/\s/', ' ', $text);

    // Parse words
    $words  = array();
    $forms  = explode(' ', $text);
    foreach($forms as $form)
    {
        // Ignore short words
        if (strlen($form) < 3) continue;

        // Keep words in uppercase as this (handles acronyms ; e.g. "FBI", "USA", "CEE" ...)
        if ($form == strtoupper($form))
        {
            $words[] = $form;
        }
        else
        {
            // Convert in lowercase
            $word = strtolower($form);

            // Ignore stopwords
            if (!@in_array($word, $stopwords))
            {
                // Add word (or a single occurence of the word if needed)
                if (!$singleOccurence || !@in_array($word, $words))
                    $words[] = $word;
            }
        }
    }

    return $words;
}

/**
* Ensure filename is a compliant OS string
* => All special chars will be replaced by an '-' char
*
* @param string Original filename
* @param int        Filename max length (or null by default)
*
* @return string    Safe filename
*
**/
function safeFileName($filename, $lenght = null)
{
    $chars = array();
    for ($i = 0; $i < mb_strlen($filename); $i++)
    {
        $chars[] = mb_substr($filename, $i, 1);
    }

    $newFileName = array();
    foreach ($chars as $c)
    {
        switch($c)
        {
            case 'à':
            case 'â':
            case 'ä':
                $newFileName[] = 'a';
                break;

            case 'é':
            case 'è':
            case 'ê':
            case 'ë':
                $newFileName[] = 'e';
                break;

            case 'i':
            case 'î':
            case 'ï':
                $newFileName[] = 'i';
                break;

            case 'ô':
            case 'ö':
                $newFileName[] = 'o';
                break;

            case 'ù':
            case 'û':
            case 'ü':
                $newFileName[] = 'u';
                break;

            case 'ç':
                $newFileName[] = 'c';
                break;

            case 'À':
            case 'Â':
            case 'Ä':
                $newFileName[] = 'A';
                break;

            case 'É':
            case 'È':
            case 'Ê':
            case 'Ë':
                $newFileName[] = 'E';
                break;

            case 'I':
            case 'Î':
            case 'Ï':
                $newFileName[] = 'I';
                break;

            case 'Ô':
            case 'Ö':
                $newFileName[] = 'O';
                break;

            case 'Ù':
            case 'Û':
            case 'Ü':
                $newFileName[] = 'U';
                break;

            case 'Ç':
                $newFileName[] = 'C';
                break;

            default:
                // Replace normal char with itself
                if (($c >= 'a' && $c <='z') || ($c >= 'A' && $c <='Z') || ($c >= '0' && $c <='9'))
                    $newFileName[] = $c;
                // Replace extra char with space
                else if (!(($c >= 'a' && $c <='z') || ($c >= 'A' && $c <='Z') || ($c >= '0' && $c <='9')))
                    $newFileName[] = ' ';
                break;
        }
    }
    $filename = implode('', $newFileName);
    // Remove extra whitespaces with '-'
    $filename = preg_replace('/\s\s+/', ' ', $filename);
    $filename = trim($filename);
    $filename = preg_replace('/\s/', '-', $filename);

    // Adjust length ?
    if (($lenght) && (strlen($filename) > $lenght))
        $filename = substr($filename, 0, $lenght);
    return $filename;
}

/**
 * Returns the extension corresponding to a specific type
 * (as returned by the getimagesize() method)
 *
 * @param int       $imagetype : type of image as the 3rd returned value of getimagesize()
 * @param string    $dot : Used to prefix returned extension (e.g. set this param to "." or "")
 *
 */
function getImageExtensionFromType($imagetype, $dot="")
{
    switch($imagetype)
    {
        case IMAGETYPE_GIF     : return $dot.'gif';
        case IMAGETYPE_JPEG    : return $dot.'jpg';
        case IMAGETYPE_PNG     : return $dot.'png';
        case IMAGETYPE_SWF     : return $dot.'swf';
        case IMAGETYPE_PSD     : return $dot.'psd';
        case IMAGETYPE_WBMP    : return $dot.'wbmp';
        case IMAGETYPE_XBM     : return $dot.'xbm';
        case IMAGETYPE_TIFF_II : return $dot.'tiff';
        case IMAGETYPE_TIFF_MM : return $dot.'tiff';
        case IMAGETYPE_IFF     : return $dot.'aiff';
        case IMAGETYPE_JB2     : return $dot.'jb2';
        case IMAGETYPE_JPC     : return $dot.'jpc';
        case IMAGETYPE_JP2     : return $dot.'jp2';
        case IMAGETYPE_JPX     : return $dot.'jpf';
        case IMAGETYPE_SWC     : return $dot.'swc';
        default                : return false;
    }
}

/**
 * Copy the assoc array content into the object as properties
 * only existing properties of object are filled. when undefined in hash, properties wont be deleted
 *
 * @param array  $array  the input array
 * @param object $object byref the object to fill (public properties matching array keys will be set)
 *
 * @return true on success, false on failure
 **/
function bindArrayToObject($properties = null, &$object)
{
    // ignore null parameter(s)
    if ($object === null || $properties === null)
        return true;

    foreach ($object as $property => $value)
    {
        $newValue = getArrayParameter($properties, $property, $object->$property);

        // Helps some property to keep their original type
        if(is_int($value))
            $object->$property = intval($newValue);
        else
            $object->$property = $newValue;
    }

    return true;
}

/**
 * Erase a directory content (recursively) and the directory itself (optional)
 *
 * @param string path       Full path of directory to erase
 *
 * @return boolean TRUE on success
 *
 **/
function eraseDirectory($path)
{
    if (!file_exists($path))
        return true;
        
    if (!is_writable($path))
    {
        if (!@chmod($path, 0777))
        {
            wcmTrace('Failed to erase folder ' . $path . ' not enough privileges!');
            return FALSE;
        }
    }

    $d = dir($path);
    while (FALSE !== ($entry = $d->read()))
    {
        if ($entry == '.' || $entry == '..')
        {
            continue;
        }
        $entry = $path . '/' . $entry;
        if (is_dir($entry))
        {
            if (!eraseDirectory($entry))
            {
                return FALSE;
            }
            continue;
        }
        if (!@unlink($entry))
        {
            wcmTrace('Failed to remove file ' . $entry);
            $d->close();
            return FALSE;
        }
    }

    $d->close();
    rmdir($path);

    return TRUE;
}

/**
 * Save a string into a file (will create file and directory if needed)
 *
 * @param string pn         Full path name
 * @param string s          File content
 * @param bool   append     True to append content to file (default false)
 * @param bool   utf8       True to convert and write file in UTF-8 (default is false)
 *
 * @return true on success
 *
 **/
function saveToFile($pn, $s, $append = false, $utf8 = true)
{
    // Normalize path name
    $pn = str_replace("\\", "/", $pn);
    $pos = strrpos($pn, "/");
    $mask = umask(0);
    if ($pos)
    {
        // Retrieve directory name
        $dn = substr($pn, 0, $pos);

        // Check (and even create) directory
        if (!is_dir($dn))
        {
            if (!makeDirectory($dn))
            {
                umask($mask);
                trigger_error("can't create directory $dn (saveToFile $pn)", E_USER_ERROR);
                return false;
            }
        }
    }

    // write in replace or append mode
    $flags = 0;
    if ($append) $flags |= FILE_APPEND;

    if(file_exists($pn) && !is_writable($pn))
        @chmod($pn, 0777); 
    
    if (file_put_contents($pn, $s, $flags) === FALSE)
    {
        umask($mask);
        return false;
    }
    
    // change file mode
    @chmod($pn, 0777);
    umask($mask);

    return true;
}

/**
 * Create a directory with a specific mask
 *
 * @param string $path Path of directory to create
 * @param int $mask Optional mask (default is 0777)
 *
 * @return boolean TRUE on success, FALSE on failure
 */
function makeDirectory($path, $mask = 0777)
{
    if (is_dir($path))
        return true;

    return @mkdir($path, $mask, true);
}

/**
 * Read a string from a file
 *
 * @param string pn Full path name
 * @param string &s String to update
 *
 * returns true on success
 *
 **/
function readFromFile($pn, &$s)
{
    $s = @file_get_contents($pn);
    return ($s === FALSE);
}

/**
 * Read the end of a file
 *
 * @param string pn Full path name
 * @param int $maxSize Maximum characters to read
 *
 * @return string End of file content
 */
function readEndOfFile($pn, $maxSize = 8192)
{
    if (!is_file($pn))
        return null;

    // Compute offset
    $offset = filesize($pn) - $maxSize;
    if ($offset < 0) $offset = 0;

    return file_get_contents($pn, 0, null, $offset);
}

/**
 * Tail a file
 *
 * @param string pn Full path name
 * @param int size //FRFR la position à partir de laquelle lire le fichier
 *
 * @return array // FRFR le contenu du fichier d'une position à une autre et la taille du fichier
 */
function tailFile($pn, $size)
{
    $newSize = filesize($pn);
    if ($size == $newSize)
        return false;
    else
    {
        //FRFR On retourne le fichier complet si size est à -1
        if ($size == -1)
            return array('fileSize' => $newSize, 'log' => file_get_contents($pn, 0, null));
        else
            return array('fileSize' => $newSize, 'log' => file_get_contents($pn, 0, null, $size, (int)$newSize));
    }
}


/**
 * Remove a file
 *
 * @param string pn Full path name
 *
 * returns true on success
 *
 **/
function removeFile($pn)
{
    if (is_file($pn))
        return unlink($pn);

    return false;
}

/**
 * Returns the URL to invoke in order to render
 * a module through an AJAX request
 *
 * @param string $module Module path (e.g. 'properties', 'business/article/photos', ...)
 * @param mixed  $..     This function use func_get_args() to retrieve all subsequent params
 *
 * @return string The URL to invoke
 */
function wcmModuleURL($module, array $params = array())
{
    // Retrieve module name
    $config = wcmConfig::getInstance();
    $url = $config['wcm.backOffice.url'].'ajax/wcm.module.php?module=' . $module . '&params=';

    $parameters = serialize($params);
    $url .= urlencode($parameters);
    return $url;
}

/**
 * Load a back-office UI module and render its result
 *
 * @param string $module Module path (e.g. 'properties', 'business/article/photos', ...)
 * @param mixed  $..     This function use func_get_args() to retrieve all subsequent params
 */
function wcmModule($module, array $params = array())
{
    
    $parts = explode('/', $module);

    if (count($parts) > 1)
    {
        $path = array_shift($parts);
        if ($path == 'business')
        {
            $root = WCM_DIR . '/business/modules/';
        }
        else if ($path == 'system')
        {
            $root = WCM_DIR . '/modules/';
        }
        else
        {
            $root = WCM_DIR . '/modules/' . $path . '/';
        }
    }
    else
    {
        $root = WCM_DIR . '/modules/';
    }

    $module = array_pop($parts);
    $path = $root . implode('/', $parts) . '/' . $module;

    if (file_exists($path . '.php'))
    {
        include($path . '.php');
        if (file_exists($path . '.js'))
        {
            include($path . '.js');
        }
    }
    else
    {
        echo '<div class="error"> INVALID_MODULE : ' . $path . '.php</div>';
        wcmProject::getInstance()->logger->logWarning("Invalid module called: " . $path . '.php');
    }
}

/**
 * Returns the url to invoke a dialog (a javascript openDialog)
 *
 * @param string $dialog        Dialog name
 * @param mixed  $parameters    Query string or associative array
 * @param int    $width         Dialog width (default 740)
 * @param int    $height        Dialog height (default 540)
 */
function wcmDialogUrl($dialog, $parameters, $width=740, $height=540)
{
    // Expand parameters?
    $queryString = (is_array($parameters)) ? http_build_query($parameters) : $parameters;

    return "javascript:openDialog('dialogs/".$dialog.".php', '" .
                                  urlencode($queryString) . "', " .
                                  $width . ", " . $height . ", " .
                                  "null, null, '" . urlencode($dialog) . "');";
}

/**
 * This function converts some difficult characters into numerical entities.
 *
 * @param string $utf2html_string
 * @return string Converted string.
 */
function utf2ent($argString)
{
    // Convert map works like this
    // Start decimal code, end decimal code, offset, mask
    // TODO Offset might have to be played around with a bit

    // mask
    $f = 0xffff;
    $convmap = array(
     160,  255, 0, $f,
     402,  402, 0, $f,  913,  929, 0, $f,  931,  937, 0, $f,
     945,  969, 0, $f,  977,  978, 0, $f,  982,  982, 0, $f,
    8226, 8226, 0, $f, 8230, 8230, 0, $f, 8242, 8243, 0, $f,
    8254, 8254, 0, $f, 8260, 8260, 0, $f, 8465, 8465, 0, $f,
    8472, 8472, 0, $f, 8476, 8476, 0, $f, 8482, 8482, 0, $f,
    8501, 8501, 0, $f, 8592, 8596, 0, $f, 8629, 8629, 0, $f,
    8656, 8660, 0, $f, 8704, 8704, 0, $f, 8706, 8707, 0, $f,
    8709, 8709, 0, $f, 8711, 8713, 0, $f, 8715, 8715, 0, $f,
    8719, 8719, 0, $f, 8721, 8722, 0, $f, 8727, 8727, 0, $f,
    8730, 8730, 0, $f, 8733, 8734, 0, $f, 8736, 8736, 0, $f,
    8743, 8747, 0, $f, 8756, 8756, 0, $f, 8764, 8764, 0, $f,
    8773, 8773, 0, $f, 8776, 8776, 0, $f, 8800, 8801, 0, $f,
    8804, 8805, 0, $f, 8834, 8836, 0, $f, 8838, 8839, 0, $f,
    8853, 8853, 0, $f, 8855, 8855, 0, $f, 8869, 8869, 0, $f,
    8901, 8901, 0, $f, 8968, 8971, 0, $f, 9001, 9002, 0, $f,
    9674, 9674, 0, $f, 9824, 9824, 0, $f, 9827, 9827, 0, $f,
    9829, 9830, 0, $f,
     338,  339, 0, $f,  352,  353, 0, $f,  376,  376, 0, $f,
     710,  710, 0, $f,  732,  732, 0, $f, 8194, 8195, 0, $f,
    8201, 8201, 0, $f, 8204, 8207, 0, $f, 8211, 8212, 0, $f,
    8216, 8218, 0, $f, 8218, 8218, 0, $f, 8220, 8222, 0, $f,
    8224, 8225, 0, $f, 8240, 8240, 0, $f, 8249, 8250, 0, $f,
    8364, 8364, 0, $f);

    return mb_encode_numericentity($argString, $convmap, "UTF-8");
}

//------------------------------------------------------
// Some functions to add for older PHP versions
//------------------------------------------------------

if(!function_exists('lcfirst'))
{
    /**
     * Make a string's first character lowercase
     *
     * @param string $str
     *
     * @return string the resulting string.
     */
    function lcfirst($str)
    {
        $str[0] = strtolower($str[0]);
        return (string)$str;
    }
}

// Initialize template directories and caching
if (!function_exists('sys_get_temp_dir'))
{
    // Based on http://www.phpit.net/
    // article/creating-zip-tar-archives-dynamically-php/2/
    function sys_get_temp_dir()
    {
        // Try to get from environment variable
        if (!empty($_ENV['TMP']))
        {
            return realpath($_ENV['TMP']);
        }
        else if (!empty($_ENV['TMPDIR']))
        {
            return realpath($_ENV['TMPDIR']);
        }
        else if (!empty($_ENV['TEMP']))
        {
            return realpath($_ENV['TEMP']);
        }

        // Detect by creating a temporary file
        else
        {
            // Try to use system's temporary directory
            // as random name shouldn't exist
            $temp_file = tempnam(md5(uniqid(rand(), TRUE)), '');
            if ($temp_file)
            {
                $temp_dir = realpath(dirname($temp_file));
                unlink($temp_file);
                return $temp_dir;
            }
            else
            {
                return FALSE;
            }
        }
    }
}

/**
 *  Function which check if the given path is absolute or not
 *
 *  @param String   $path   Path to check
 *
 *  @return Boolean Return TRUE if the path is absolute and FALSE if it's not
 */
function isAbsolutePath($path)
{
    $pattern = '@^([a-zA-Z]:|(\\\){2,2}|\/)@';
    return preg_match($pattern, $path);
}

/**
 *  This method execute a function and capture the output result and return it into a string
 *
 *  @return string  The XML
 */
function captureOutput()
{
    ob_start();
    ob_implicit_flush(false);
    $args = func_get_args();
    $function = array_shift($args);
    call_user_func_array($function, $args);
    $string = ob_get_contents();
    ob_end_clean();
    return $string;
}

function parsePath($path, $nodeName)
{
    $result = '';
    $paths = explode('/', $path);
    foreach ($paths as $pth)
    {
        $subPath = explode('-', $pth);
        if (($subPath[0] != $nodeName) && (count($subPath) == 2))
        {
            $result .= $subPath[0] . '/';
        }
    }
    return addslashes($result);
}

/**
 *  This method receives a file path name and validates if it exists
 *  It will return filename-1, filename-2, filename-3 depending on the available one
 *
 *  @param: desFile, the path to the file (including file name) excluding WCM_DIR
 */
function checkFileName($destFile)
{
    $config = wcmConfig::getInstance();
    
    //find the last occurence of / or \
    $posS = strripos($destFile, "/");
    $posB = strripos($destFile, "\\");
    $pos = ($posS>$posB) ? $posS : $posB;
    
    //find the file name in destFile
    $newFileName = substr($destFile, $pos+1, strlen($destFile));
    //find the Repertory name in destFile
    $destRep = substr($destFile, 0, $pos+1);
    
    $fileNumber = 0;
    //check if a photo is already exists before upload
    while (file_exists(WCM_DIR.'/'.$destFile))
    {
        //get the image name without change
        $fileName = $newFileName;
        $fileNumber++;
        //get the image extension
        $extension = strrchr($fileName, '.');
        //change the file name with no extension ex: name to name-1
        if (!$extension) $fileName = $fileName."-".$fileNumber;
        //change the image name ex: name.jpg to name-1.jpg
        else $fileName = substr($fileName, 0, -strlen($extension))."-".$fileNumber.$extension;
        //built the file destination path
        $destFile = $destRep.$fileName;
    }   
    return $destFile;
}

/**
 * Convert a given date from ISO-8601 format to native format.
 *
 * Currently, the only difference between the two formats is a 'T' in
 * the ISO-8601 format where the native format has a space:
 *
 * ISO-8601 format: 2008-09-16T12:35:46
 * Native format:   2008-09-16 12:35:46
 *
 * @param string $date The date in ISO-8601 format
 *
 * @return string The date in native format
 */
function dateFromISO8601($date)
{
    if (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}$/', $date))
        $date = str_replace('T', ' ', $date);

    return $date;
}

/**
 * Convert a given date from native format to ISO-8601 format.
 *
 * Currently, the only difference between the two formats is a 'T' in
 * the ISO-8601 format where the native format has a space:
 *
 * Native format:   2008-09-16 12:35:46
 * ISO-8601 format: 2008-09-16T12:35:46
 *
 * @param string $date The date in native format
 *
 * @return string The date in ISO-8601 format
 */
function dateToISO8601($date)
{
    if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $date))
        $date = str_replace(' ', 'T', $date);

    return $date;
}
