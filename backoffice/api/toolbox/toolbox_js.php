<?php
/**
 * Project:     WCM
 * File:        wcm.toolbox_js.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */


/**
 * Generates a SCRIPT element to include a given JavaScript file
 *
 * @param string     $baseUrl The base URL for generated links
 *
 * @param string $filePath The path of the file for which to generate
 *                         the SCRIPT element, relative to the Web
 *                         application URL
 *
 * @return string The generated 'document.write' function call
 *
 */
function jsIncludeFile($baseUrl, $filePath)
{
    return "<script language='JavaScript' type='text/javascript' src='" . $baseUrl . $filePath . "'></script>\n";
}

/**
 * Recursively invokes the jsIncludeFile function for each JavaScript
 * file under a given directory.
 *
 * @param string     $baseUrl The base URL for generated links
 * @param string     $dirPath The path of the directory
 * @param array      $ignore  List of entries to ignore (default: null)
 *
 * @return string The generated 'document.write' function call
 *
 */
function jsIncludeDir($baseUrl, $dirPath, $ignore = null)
{
    $js = '';

    if (is_dir($dirPath))
    {
        $jsExts = array('js', 'js.php');

        $entries = scandir($dirPath);
        foreach ($entries as $entry)
        {
            if ($entry != '.' && $entry != '..' && (!$ignore || !in_array($entry, $ignore)))
            {
                $entryPath = $dirPath.$entry;
                if (is_dir($entryPath))
                {
                    $js .= jsIncludeDir($baseUrl, $entryPath, $ignore);
                }
                else
                {
                    foreach ($jsExts as $jsExt)
                    {
                        if (mb_substr($entry, mb_strlen($entry) - mb_strlen($jsExt)) == $jsExt)
                        {
                            $js .= jsIncludeFile($baseUrl, $entryPath);
                            break;
                        }
                    }
                }
            }
        }
    }

    return $js;
}
?>
