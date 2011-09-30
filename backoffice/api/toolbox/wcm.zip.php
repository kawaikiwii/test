<?php

/**
 * Project:     WCM
 * File:        wcm.zip.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * This class is used to zip folders content
 */
class wcmZip
{

    protected $zip;
    protected $root;
    protected $ignored_names;

    /**
     * Constructor
     *
     * @param string $file      File to create
     * @param string $folder    Path of folder to zip
     * @param mixed $ignored    Ignored files (a string or an array of string) or null
     * @param mixed $ignoredext Ignored extensions (a string or an array of string) or null
     */
    function __construct($file, $folder, $ignored=null, $ignoredext=null) 
    {
    
        $this->zip = new ZipArchive();
        $this->ignored_names = is_array($ignored) ? $ignored : $ignored ? array($ignored) : array();
        $this->ignored_extensions = is_array($ignoredext) ? $ignoredext : $ignoredext ? array($ignoredext) : array();
        
        if ($this->zip->open($file, ZIPARCHIVE::CREATE)!==TRUE) {
            throw new Exception("cannot open <$file>\n");
        }
        $folder = substr($folder, -1) == '/' ? substr($folder, 0, strlen($folder)-1) : $folder;
        
        if(strstr($folder, '/')) 
        {
            $this->root = substr($folder, 0, strrpos($folder, '/')+1);
            $folder = substr($folder, strrpos($folder, '/')+1);
        }
        $this->zip($folder);
        $this->zip->close();
    }

    /**
     * Recursive zip
     *
     * @param string $folder Path of folder to zip
     * @param string $parent Parent path
     */
    private function zip($folder, $parent=null) 
    {
        $full_path = $this->root.$parent.$folder;
        $zip_path = $parent.$folder;
        $this->zip->addEmptyDir($zip_path);
        $dir = new DirectoryIterator($full_path);
        
        foreach($dir as $file) 
        {
            if(!$file->isDot()) 
            {
                $filename = $file->getFilename();
                $extension = explode(".", $file->getFilename());
                $extension = $extension[count($extension)-1];
                
                if(!in_array($filename, $this->ignored_names) && !in_array($extension, $this->ignored_extensions)) 
                {
                    if($file->isDir()) 
                    {
                        $this->zip($filename, $zip_path.'/');
                    }
                    else 
                    {   
                        $this->zip->addFile($full_path.'/'.$filename, $zip_path.'/'.$filename);
                    }
                }
            }
        }
    }
}