<?php
/**
 * Project:     WCM
 * File:        initApi.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

// preload constants and toolbox
require_once('constants.php');
_wcm_preload_toolbox();

// Register autoload
spl_autoload_register('_wcm_autoload');

// Load configuration and set various PHP settings
$config = wcmConfig::getInstance();

ini_set('magic_quotes_gpc','off');
$timezone = $config['wcm.default.timezone'];
if ($timezone)
{
    ini_set('date.timezone', $timezone);
}

/**
 * Process import plugins cache file
 */
if (!is_file(WCM_DIR.'/xml/importPlugins.xml'))
{
    $xml = new DOMDocument();
    $xml->preserveWhiteSpace = false;
    $xml->formatOutput = true;
    $rootNode = $xml->createElement('plugins');
    $xml->appendChild($rootNode);

    $dir = new RecursiveDirectoryIterator(WCM_DIR.'/business/import/plugins');
    foreach (new RecursiveIteratorIterator($dir, RecursiveIteratorIterator::SELF_FIRST) as $file)
    {
        if ($file->getFilename() !== 'plugin.xml') continue;
        $pluginDir = dirname($file->getPathname());
        $pluginDOM = DOMDocument::load($file->getPathname());
        $pluginXML = $xml->importNode($pluginDOM->documentElement, true);
        $pluginXML->appendChild($xml->createElement('path',$pluginDir));

        $xml->documentElement->appendChild($pluginXML);
    }

    $xml->save(WCM_DIR.'/xml/importPlugins.xml');
    unset($xml);
}

/**
 * Process search plugins cache file
 */
if (!is_file(WCM_DIR.'/xml/searchPlugins.xml'))
{
    $xml = new DOMDocument();
    $xml->preserveWhiteSpace = false;
    $xml->formatOutput = true;
    $rootNode = $xml->createElement('plugins');
    $xml->appendChild($rootNode);

    $dir = new RecursiveDirectoryIterator(WCM_DIR.'/api/search/plugins');
    foreach (new RecursiveIteratorIterator($dir, RecursiveIteratorIterator::SELF_FIRST) as $file)
    {
        if ($file->getFilename() !== 'info.xml') continue;
        $pluginDir = dirname($file->getPathname());
        $pluginDOM = DOMDocument::load($file->getPathname());

        $idVal = $pluginDOM->getElementsByTagName('id')->item(0)->textContent;

        $pluginXML = $xml->importNode($pluginDOM->documentElement, true);
        $pluginXML->appendChild($xml->createElement('path',$pluginDir));
        $pluginXML->setAttribute('id', $idVal);


        $xml->documentElement->appendChild($pluginXML);
    }

    $xml->save(WCM_DIR.'/xml/searchPlugins.xml');
    unset($xml);
}

/**
 * Remember classes paths in a global array to
 * facilitate dynamic autoload
 *
 * RULES:
 *    - A class wcmXxx should be in a file named wcm.xxx.php
 *    - A class xxx should be in a file named biz.xxx.php
 *    - A file containing 'toolbox' will be ignored
 *    - A directory containing 'svn' will be ignored
 *
 * @param $className string Name of the class to load
 */
function _wcm_autoload($classToLoad)
{
    $rootdir = dirname(__FILE__) . DIRECTORY_SEPARATOR;

    // Handle Zend class
    if (substr($classToLoad, 0, 5) == 'Zend_')
    {
        _wcm_zend_autoload($classToLoad);
        return;
    }

    // Handle Solr PHP Client class
    if (substr($classToLoad, 0, 12) == 'Apache_Solr_')
    {
        _wcm_apache_solr_autoload($classToLoad);
        return;
    }

    // Build autoload file?
    if (!file_exists($rootdir . 'autoload.php'))
    {
        _wcm_build_autoload();
    }

    // Include auto-load file
    include($rootdir . 'autoload.php');

    // Load class file
    if (array_key_exists($classToLoad, $paths))
    {
        require($paths[$classToLoad]);
    }
    else
    {
        // Retry once!
        _wcm_build_autoload();
        include($rootdir . 'autoload.php');

        if (array_key_exists($classToLoad, $paths))
        {
            require($paths[$classToLoad]);
        }
        else
        {
		die('WCM autoload failed => invalid className: ' . $classToLoad);
        }
    }
}

function _wcm_build_autoload()
{
    $rootdir = dirname(__FILE__) . DIRECTORY_SEPARATOR;

    $dirs = array();
    $paths = array();

    $recursive_dirs = array(
            $rootdir . 'api' . DIRECTORY_SEPARATOR,
            $rootdir . 'business' . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR,
            $rootdir . 'business' . DIRECTORY_SEPARATOR . 'import',
            $rootdir . 'webservices' . DIRECTORY_SEPARATOR . 'lib',
            $rootdir . 'includes' . DIRECTORY_SEPARATOR . 'Smarty',
        );

    foreach($recursive_dirs as $recursive_dir)
    {
        $dirs[] = $recursive_dir;
        $rdi = new RecursiveDirectoryIterator($recursive_dir);

        foreach (new RecursiveIteratorIterator($rdi, RecursiveIteratorIterator::SELF_FIRST) as $dir)
        {
            if (!$dir->isDir())
                continue;

            // Skipping SVN directories (temporary)
            if (FALSE !== strstr($dir->getPathname(), '.svn'))
                continue;

            $dirs[] = $dir->getPathname() . DIRECTORY_SEPARATOR;
        }

    }

    // Map class and path
    foreach ($dirs as $directory)
    {
        foreach (new DirectoryIterator($directory) as $file)
        {
            if (!$file->isFile() || substr($file->getFilename(), -4)!='.php')
              continue;

            // Skipping toolbox files
            if (false !== strstr($file->getFilename(), 'toolbox'))
              continue;

            // Strip path and [.class].php extension from filename to get class name
            //
            // TODO The .class.php extension is currently used by the
            // Web services classes; the corresponding files should
            // perhaps be renamed eventually to follow our convention
            $className = basename($file->getFilename(), '.php');
            $className = basename($className, '.class');

            // Rewriting business class names
            if ('biz.' === substr($className, 0, 4))
                $className = substr($className, 4);

            // Rewriting system class names
            if ('wcm.' === substr($className, 0, 4))
                $className = 'wcm'.ucfirst(substr($className, 4));

            // Adding class to map
            $paths[$className] = $file->getPathname();
        }
    }

    if(class_exists('wcmProject') && class_exists('wcmConfig')) {
        $project = wcmProject::getInstance();
        $paths = array_merge($paths, $project->layout->getWidgetAutoloadArray());
    }

    // Write autoload file
    $content = "<?php\n/* WCM auto-load file\n * This is is automatically generated\n * Just erase this file to rebuild it\n*/\n\n"
             . "\$paths = array(\n";
    foreach($paths as $aClass => $aPath)
    {
        $content .= "'$aClass' => '$aPath',\n";
    }
    $content .= ");\n?>";

    saveToFile($rootdir . 'autoload.php', $content);
}

function _wcm_zend_autoload($classToLoad)
{
    // Zend classes follow a pattern such as Zend_[a-z]+_[a-z]+_[a-z]+
    $parts = explode('_', $classToLoad);

    // Last part is the filename, other parts are directory names
    $filename = array_pop($parts);
    $path = (count($parts))? join('/', $parts).'/' : '';

    // Use include_once instead of require_once in order to track/display the real error
    // (e.g. 'Fatal Error: Class 'XXX' not found)
    $filename = dirname(__FILE__) . '/includes/' . $path . $filename . '.php';
    if (!file_exists($filename))
    {
        die('Unable to load Zend class: ' . $filename);
    }
    require_once $filename;
}

function _wcm_apache_solr_autoload($classToLoad)
{
    // Zend classes follow a pattern such as Zend_[a-z]+_[a-z]+_[a-z]+
    $parts = explode('_', $classToLoad);

    // Last part is the filename, other parts are directory names
    $filename = array_pop($parts);
    $path = (count($parts))? join('/', $parts).'/' : '';

    // Use include_once instead of require_once in order to track/display the real error
    // (e.g. 'Fatal Error: Class 'XXX' not found)
    $filename = dirname(__FILE__) . '/includes/SolrPhpClient/' . $path . $filename . '.php';
    if (!file_exists($filename))
    {
        die('Unable to load Solr PHP Client class: ' . $filename);
    }
    require_once $filename;
}

/**
 * Preload toolbox files as they cannot be auto-loaded (no class mapping)
 * RULES:
 *    - system toolbox files must start with 'toolbox'
 *    - business toolbox files must start with 'biz.toolbox'
 */
function _wcm_preload_toolbox()
{
    require_once dirname(__FILE__). '/api/toolbox/toolbox.php';
    require_once dirname(__FILE__). '/business/api/toolbox/biz.toolbox.php';
    return;

    $rootdir = dirname(__FILE__) . DIRECTORY_SEPARATOR;

    // system toolbox
    foreach (new DirectoryIterator($rootdir . 'api' . DIRECTORY_SEPARATOR . 'toolbox') as $file)
    {
        if (!$file->isFile())
          continue;

       $path = $file->getFilename();
       if ('toolbox' === substr($path, 0, 7))
           require_once($file->getPathname());
    }

    // business toolbox
    foreach (new DirectoryIterator($rootdir . 'business' . DIRECTORY_SEPARATOR .'api' . DIRECTORY_SEPARATOR . 'toolbox') as $file)
    {
        if (!$file->isFile())
          continue;

       $path = $file->getFilename();
       if ('biz.toolbox' === substr($path, 0, 11))
           require_once($file->getPathname());
    }
}