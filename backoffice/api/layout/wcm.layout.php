<?php

/**
 * Project:     WCM
 * File:        wcm.layout.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * The wcmGenerator class is an helper class used to manage menus and design zones
 *
 * Remark: menus are stored in cache through static variables
 */
class wcmLayout
{
    /**
     * Returns hierarchical array of menus to populate dropdown list
     *
     * @param array $menus Array to update (should be initialized)
     * @param int $excludeId Optional ID of menu to exclud forom the list
     */
    function getMenuHierarchy(array &$menus, $excludeId=0, $prefix=null, $parentId=0)
    {
        foreach($this->getMenus() as $menu)
        {
            if ($menu->parentId == $parentId)
            {
                // Add menu (unless it is excluded)
                $path = ($prefix) ? $prefix . ' :: ' . getConst($menu->name) : getConst($menu->name);
                if ($menu->id != $excludeId)
                {
                    $menus[$menu->id] = $path;
                }

                // Recursively add sub-menus
                $this->getMenuHierarchy($menus, $excludeId, $path, $menu->id);
            }
        }
    }

    /**
     * Returns a menu corresponding to a specific id
     *
     * @param int $id The menu id
     *
     * @return wcmMenu The menu matching given id (or null if id is invalid)
     */
    public function getMenuById($id)
    {
        // Use cache
        return getArrayParameter($this->getMenus(), $id, null);
    }

    /**
     * Returns a menu object corresponding to a specific action code
     *
     * @param string $action The menu action to search for
     *
     * @return wcmMenu The menu matching given id (or null if id is invalid)
     */
    public function getMenuByAction($action)
    {
        // Use cache
        foreach($this->getMenus() as $menu)
        {
            if ($menu->action == $action)
                    return $menu;
        }

        return null;
    }

    /**
     * Returns an array of root menus
     *
     * @param boolean $resetCache Whether to reset the cache, ie. load from DB (default is false)
     *
     * @return An assoc array of {@link wcmMenu} objects (keys are ids)
     */
    public function getRootMenus($resetCache = false)
    {
        // Use cache
        $menus = array();
        foreach($this->getMenus($resetCache) as $menu)
        {
            // Return only root menus (i.e. no parent id)
            if (!$menu->parentId)
                    $menus[$menu->id] = $menu;
        }
        
        return $menus;
    }

    /**
     * Returns an array of all menus
     *
     * @param boolean $resetCache Whether to reset the cache, ie. load from DB (default is false)
     *
     * @return An assoc array of {@link wcmMenu} objects (keys are ids)
     */
    public function getMenus($resetCache = false)
    {
        $cached = wcmCache::fetch('wcmMenu');
        if ($resetCache || $cached === FALSE)
        {
            $project = wcmProject::getInstance();
            $enum = new wcmMenu($project);
            if (!$enum->beginEnum(null, 'parentId, rank'))
                    return null;

            $cached = array();
            while ($enum->nextEnum())
            {
                $cached[$enum->id] = clone($enum);
            }
            $enum->endEnum();

            // Cache objects
           wcmCache::store('wcmMenu', $cached);
        }

        return $cached;
    }

    /**
     * Returns an associative array of zones belonging to a bizobject.
     *
     * @param bizobject $bizobject Bizobject owner of the zones to retrieve
     *
     * @return array Zones belonging to bizobject
     */
    public function getZones($bizobject)
    {
        $enum = new wcmDesignZone();

        $zones = array();

        if($enum->beginEnum("sourceClass='".get_class($bizobject)."' AND sourceId=".$bizobject->id))
        {
            while($enum->nextEnum())
                    $zones[] = clone($enum);

            $enum->endEnum();
        }

        return $zones;
    }

    /**
     * Resets the zone contents (plural!) belonging to a bizobject in
     * the current session, ie. unsets the session values.
     *
     * If $inDb is true, also clears the zone contents in DB.
     *
     * @param bizobject $bizobject Bizobject owner of the zone
     * @param boolean   $inDb      Whether to clear zone content in DB
     */
    public function resetZoneContents($bizobject, $inDb)
    {
        // Get current zones
        $zones = $this->getZones($bizobject);

        // Reset zones
        if ($zones)
        {
            foreach ($zones as $zone) $zones->delete();
        }
    }

    public function setZoneContents($bizobject, $array, $datas, $widgetsettings = null)
    {
        foreach($array as $zoneName => $widgets)
        {
            $zone = new wcmDesignZone(get_class($bizobject), $bizobject->id, $zoneName);
            if(is_array($widgets))
            {
                foreach($widgets as $widget)
                {
                    if(empty($widget)) continue;
                    list($widgetName, $widgetGuid) = explode('-', $widget);
					
                    $settings = array();
                    if($serialisation = getArrayParameter($widgetsettings, $widget.'-settings',false))
                    	parse_str($serialisation, $settings);
                    
                    $zone->addWidget($widgetName, $widgetGuid, $settings);
                }
            }
            $zone->save();
        }
    }

    /**
     *  Return a widget list 
     *
     *  @param wcmObject|null   $object If the object is define the array will just return the widgets for this object
     *
     *  @return array widgetClassname => Human readable name
     */
    public function getWidgets($object = null)
    {
        $widgetList = array();
        foreach($this->loadWidgetConfig() as $widget)
        {
            if($object===null) $widgetList[(string)($widget->class)] = (string)($widget->name);
            else 
            {
                foreach(explode(',',$widget->context) as $context)
                    if($object instanceof $context)
                    {
                        $widgetList[(string)($widget->class)] = (string)($widget->name);
                        break;
                    }
            }
        }
        return $widgetList;
    }

    /**
     *  Create the widgets.xml configuration file
     */
    public function createWidgetConfig($fileName = null)
    {
        if($fileName === null) $filename = WCM_DIR . '/xml/widgets.xml';
        
        $config = wcmConfig::getInstance();

        $xml = new DOMDocument();
        $rootNode = $xml->createElement('widgets');
        $xml->appendChild($rootNode);

        $wd = new RecursiveDirectoryIterator($config['wcm.widgets.path']);
        foreach (new RecursiveIteratorIterator($wd, RecursiveIteratorIterator::SELF_FIRST) as $file)
        {
            if($file->getFilename() !== 'infos.xml') continue;

            $widgetdir = dirname($file->getPathname());
            $widgetDOM = DOMDocument::load($file->getPathname());
            $widgetXML = $xml->importNode($widgetDOM->documentElement, true);

            $widgetXML->appendChild($xml->createElement('path', $widgetdir));
            
            if($widgetDOM->getElementsByTagName('class')->length == 0) 
            {
                $className = 'wcmWidget';
                foreach(explode(DIRECTORY_SEPARATOR, substr($widgetdir, strlen($config['wcm.widgets.path']))) as $ns)
                        $className .= ucfirst($ns);
                $widgetXML->appendChild($xml->createElement('class', $className));
            }

            $xml->documentElement->appendChild($widgetXML);

        }   
        $xml->save($fileName);
    }

    /**
     * Create an autoload array for the wigdets
     */
    public function getWidgetAutoloadArray()
    {
        $widgetAutloadArray = array();
        foreach($this->loadWidgetConfig() as $widget)
        {
            $widgetAutloadArray[(string)($widget->class)] = $widget->path . '/widget.php';
        }
        return $widgetAutloadArray;
    }
            
    /**
     * Load the widgets configuration file
     */
    public function loadWidgetConfig()
    {
        static $xmlconfig = null;
        if($xmlconfig === null) 
        {
            $config = wcmConfig::getInstance();
            $filename = WCM_DIR . '/xml/widgets.xml';
            if(!file_exists($filename))
                    $this->createWidgetConfig($filename);
            $xmlconfig = simplexml_load_file($filename);
        }
        return $xmlconfig;
    }
        
}
