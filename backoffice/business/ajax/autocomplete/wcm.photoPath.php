<?php
/**
 * Project:     WCM
 * File:        ajax/autocomplete/wcm.photoPath.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

// Initialize the system
require_once dirname(__FILE__).'/../../../initWebApp.php';
$config = wcmConfig::getInstance();
$photoPath = WCM_DIR.DIRECTORY_SEPARATOR.$config['wcm.backOffice.photoPath'];
$prefix = $_REQUEST['prefix'];

$folders = getFolders($prefix);
sort($folders);
echo '<ul>';
foreach($folders as $folder)
{
	echo '<li>';
    echo $folder;
    echo '</li>';
}
echo '</ul>';

function getFolders($prefix)
{
    $config = wcmConfig::getInstance();
    $photoPath = WCM_DIR.DIRECTORY_SEPARATOR.$config['wcm.backOffice.photoPath'];
    $folders = array();
    $fileName = '';
    
    $prefixes = explode('/',$prefix);
    $match = $prefixes[count($prefixes)-1];
    $path = implode('/',$prefixes);
    $path = str_replace($match, '',$path);
    
    $dr = new DirectoryIterator($photoPath.$path);
    
    foreach($dr as $drcontent)
    { 
   		if ((ereg("^$match", $drcontent->getFilename()) || $match == "") && $drcontent->getFilename() != '.' && $drcontent->getFilename() != '..' && $drcontent->getFilename() != '.svn')
   		{
   			if ($drcontent->isDir())
   				$folders[] = $path.$drcontent->getFilename();
   		}
    }
    
    return $folders;
 
}
