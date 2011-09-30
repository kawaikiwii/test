<?php
/**
 * Project:     WCM
 * File:        wcm.dashboard.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     3.2
 *
 */

/**
 * The wcmDashboard manages dashboards for users
 * A dashboard is composed of 1 to n zones that, in turn, 
 * are composed of 1 to n modules.
 */
class wcmDashboard
{
    /**
     * Additional context for this dashboard
     */
    private $context;
    
    /**
     * Array that may contain more information (eg userId etc...)
     */
    private $dashboard;
    
    /**
     * Path to xml file containing configurations
     */
    private $xmlPath;
    
    /**
     * Simple XML object containing configurations
     */
    private $xml;
    
    /**
     * Dashboard's constructor.
     * Will set default values for $this->dashboard and get xml config file
     *
     * @param string $xmlFile XML file to use (or null to use default)
     */
    public function __construct($xmlFile = null)
    {
        $session = wcmSession::getInstance();
        
        $this->dashboard['userId'] = $session->userId;

        // Determine which XML file to use
        if (!$xmlFile)
        {
            $xmlFile = WCM_DIR . '/business/xml/dashboard/user-' . $session->userId . '.xml';
            if (!file_exists($xmlFile))
            {
                $xmlFile = WCM_DIR . '/business/xml/dashboard/default_'.$session->getSite()->language.'.xml';
            }
        }

        // Load xml
        $this->xml = simplexml_load_file($xmlFile);
        if (!$this->xml)
            die(_BIZ_INVALID_XML . ': ' . $xmlFile);
    }
    
    /**
     * Basic rendring method
     *
     * @return  string  $html   containing final html of dashboard
     */
    public function render()
    {
        $html  = '';
        $html .= '<div id="dashboard">';
        $zid = 0;
        foreach($this->xml->children() as $child)
        {
            switch($child->getName())
            {
               case 'zone':
                   $zid++;
                   $html .= $this->renderZone($child, 'dbzone_'.$zid);
                   break;
               case 'literal':
                   $html .= self::parseText(strval($child));
                   break;
            }
        }
        $html .= '</div>';
        return $html;
    }    

    /**
     * Render zones
     *
     * @param   simple xml object   $zone
     * @param   int a default zone id
     *
     * @return  string  $html   containing final html of zone  
     */
    private function renderZone($zone, $id)
    {
        // Add explicit id?
        if (!$zone->id) $zone->id = $id;
        
        $html  = '';
        $html .= '<style type="text/css">.collapsed {border: 3px red solid;}</style>';
        $html .= '<div class="' . trim('zone' . ' ' . $zone['class']) . '">';
        $html .= '<h3 onclick="$(\'' . $id . '\').toggle();'
                 . ' if ($(this).className == \'collapsed\') $(this).removeClassName(\'collapsed\');'
                 . ' else $(this).addClassName(\'collapsed\');">' . getConst($zone->title) . '</h3>';
        $html .= '<div id="' . $zone->id . '">';
        $mid = 0;
        foreach ($zone->module as $module)
        {
            $mid++;
            $html .= $this->renderModule($module, $zone->id, $mid);
        }
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Render module (use cache if @ttl is provided)
     * Set values that will be interpreted in the smarty template
     *
     * @param   simple xml object   $module
     * @param   int the zone id
     * @param   int a default module id
     *
     * @return  string  $html   containing final html of module  
     */
    private function renderModule($module, $zoneId, $id)
    {
        // Add explicit id?
        if (!$module->id) $module->id = $zoneId.$id;

        // Header
        $html  = '<div class="' . trim('module' . ' ' . $module['class']) . '">';
        $html .= '<h4>' . getConst($module->title) . '</h4>';
        $html .= '<div id="' . $module->id .'" class="scroll">';

        // Cache module?
        $ttl = intval($module['ttl']);
        if ($ttl)
        {
            $key  = $module->id . wcmSession::getInstance()->userId;
            $content = wcmCache::fetch($key);
            if ($content === FALSE)
            {
                $content = $this->renderModuleContent($module);
                wcmCache::store($key, $content, $ttl);
            }
            $html .= $content;
        }
        else
        {
            $html .= $this->renderModuleContent($module);
        }
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Render module content (no cache)
     *
     * @param   simple xml object   $module
     * @return  string  $html   containing final html of module  
     */
    private function renderModuleContent($module)
    {
        $parameters = array();
        $parameters['dashboard'] = $this;
        $parameters['module'] = array('id' => $module->id, 'title' => $module->title);
        $params = array();
        foreach ($module->parameters->param as $param)
        {
            if (isset($param->param))
            {
                $subparams = array();
                foreach($param->param as $subparam)
                {
                    $subparams[strval($subparam['name'])] = self::parseText(strval($subparam));
                }
                $params[strval($param['name'])] = $subparams;
            }
            else
            {
                $params[strval($param['name'])] = self::parseText(strval($param));
            }
        }
        $parameters['params'] = $params;

        $generator = new wcmTemplateGenerator();
        return $generator->executeTemplate($module->template, $parameters);
    }

    /*
     * Replace predefined variable references with their values.
     * Available variables are:
     * @SessionID   The current session ID
     * @UserID      the current connected user ID
     * @@SiteID     The current context (working site ID)
     * @Date        The current date in sql format 'Y-m-d'
     * @Time        The current time in sql format 'H-i-s'
     * @DateTime    The current date and time in sql format 'Y-m-d H:i:s'
     *
     * @param string $text Text containing references to variables
     *
     * @return sting Text with variable references replaced by their values
     */
    private static function parseText($text)
    {
        $session = wcmSession::getInstance();

        $text = str_replace('@SessionID', $session->id, $text);
        $text = str_replace('@SiteID', $session->getSiteId(), $text);
        $text = str_replace('@UserID', $session->userId, $text);
        $text = str_replace('@Date', "'".date('Y-m-d')."'", $text);
        $text = str_replace('@Time', ".".date('H:i:s')."'", $text);
        $text = str_replace('@DateTime', ".".date('Y-m-d H:i:s')."'", $text);

        return $text;
    }

}
