<?php
/**
 * Project:     WCM
 * File:        api/search/wcm.bizsearchConfig.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * Business search configuration
 */
class wcmBizsearchConfig
{
    /**
     * The singleton instance.
     *
     * @var wcmBizsearchConfig
     */
    private static $instance = null;

    /**
     * Gets the singleton instance.
     *
     * @return wcmBizsearchConfig The singleton instance
     */
    public static function getInstance()
    {
        if (self::$instance === null)
            self::$instance = new wcmBizsearchConfig;

        return self::$instance;
    }

    /**
     * Simple XML object corresponding to the XML configuration file.
     *
     * @var SimpleXML
     */
    private $simpleXML = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->load();
    }

    /**
     * Loads the XML configuration file.
     */
    public function load()
    {
        // Check that the XML configuration file exists
        $filePath = WCM_DIR . '/business/xml/search/default.xml';
        if (!file_exists($filePath))
            throw new Exception(_BIZ_CANNOT_LOAD_CONFIGURARTION_FILE);

        // Load the XML configuration file into a SimpleXML object
        $simpleXML = simplexml_load_file($filePath);
        if ($simpleXML === false)
            throw new Exception(_BIZ_INVALID_XML);

        $this->simpleXML = $simpleXML;
    }

    /**
     * Gets the default search configuration ID.
     *
     * @return string The default search configuration ID
     */
    public function getDefaultConfigId()
    {
        return (string) $this->simpleXML->attributes()->defaultId;
    }

    /**
     * Gets the default search parameter prefix.
     *
     * @return string The default search parameter prefix
     */
    public function getDefaultParameterPrefix()
    {
        // TODO this should probably be stored per <search> element
        return 'search_';
    }

    /**
     * Gets a given search configuration.
     *
     * @param string $configId The search configuration ID
     *
     * @return SimpleXMLElement|null The requested search configuration, or null if none
     */
    public function getSearch($configId)
    {
        $searches = $this->simpleXML->xpath('search[@id="'.$configId.'"]');
        if (!$searches)
        {
            // TODO log warning
            return null;
        }

        return $searches[0];
    }

    /**
     * Gets the list of search configurations.
     *
     * @return array The list of search configurations
     */
    public function getSearches()
    {
        $searches = array();
        foreach ($this->simpleXML as $search)
            $searches[] = $search->attributes();

        return $searches;
    }

    /**
     * Gets the 'ajaxRequest' attribute for a given search page type.
     *
     * @param string $configId The search configuration ID
     * @param string $pageType The search page type
     *
     * @return bool|null The attribute value, or null if none
     */
    public function getAjaxRequest($configId, $pageType)
    {
        $search = $this->getSearch($configId);
        if (!$search || !($searchForm = $search->searchForm))
            return null;

        return $searchForm->attributes()->ajaxRequest == 'true';
    }

    /**
     * Gets a given page from a given search configuration.
     *
     * @param string $configId The search configuration ID
     * @param string $pageType The search page type
     *
     * @return SimpleXMLElement|null The requested page, or null if none
     */
    public function getPage($configId, $pageType)
    {
        $search = $this->getSearch($configId);
        if (!$search)
            return null;

        return $search->$pageType;
    }

    /**
     * Gets the page type corresponding to a given search action
     * "todo" for a given search configuration.
     *
     * @param string $configId The search configuration ID
     * @param string $todo     The search action "todo"
     *
     * @return string The page type
     */
    public function getPageType($configId, $todo)
    {
        $search = $this->getSearch($configId);
        if (!$search)
            return null;

        $pages = $search->xpath('*[@todo="'.$todo.'"]');
        if (!$pages)
            return null;

        return $pages[0]->getName();
    }

    /**
     * Gets the name of the default view for a given search
     * configuration and page type.
     *
     * @param string $configId The search configuration ID
     * @param string $pageType The search page type
     *
     * @return string The default view name
     */
    public function getDefaultViewName($configId, $pageType)
    {
        $search = $this->getSearch($configId);
        if (!$search || !($resultSet = $search->$pageType->resultSet))
            return null;

        return (string) $resultSet->attributes()->defaultView;
    }

    /**
     * Gets a given view from a given search configuration.
     *
     * @param string $configId The search configuration ID
     * @param string $pageType The search page type
     * @param string $viewName The view name
     *
     * @return SimpleXMLElement|null The requested view, or null if none
     */
    public function getView($configId, $pageType, $viewName)
    {
        $search = $this->getSearch($configId);
        if (!$search || !($resultSet = $search->$pageType->resultSet))
            return null;

        $views = $resultSet->xpath('//view[@name="'.$viewName.'"]');
        if (!$views)
            return null;

        return $views[0];
    }

    /**
     * Gets the list of views for a given search configurations.
     *
     * @param string $configId The search configurations ID
     * @param string $pageType The search page type
     *
     * @return array The list of views
     */
    public function getViews($configId, $pageType)
    {
        $search = $this->getSearch($configId);
        if (!$search)
            return null;

        $views = array();
        foreach ($search->$pageType->resultSet->children() as $view)
            $views[] = $view->attributes();

        return $views;
    }

    /**
     * Designs a search page of a given type based on a given search
     * configuration and a context (as initialized by executing the
     * business search action).
     *
     * @param string   $configId The search configuration ID
     * @param string   $pageType The search page type
     * @param StdClass $context  The search context
     *
     * @return string|null The page HTML, or null on error
     */
    public function designSearchPage($configId, $pageType, $context)
    {
        // Get the corresponding search configuration

        $search = $this->getSearch($configId);

        if (!$search || !($templateName = $search->$pageType->template))
            return null;

        // Determine the template path
        $config = wcmConfig::getInstance();

        $templatePath = $config['wcm.templates.path'] . 'search/' . $templateName;

        if (!file_exists($templatePath))
            return null;

        // Process the template accorfing to its type
        $pathinfo = pathinfo($templatePath);
        switch ($pathinfo['extension'])
        {
        case 'html':
        case 'php':
            ob_start();
            include $templatePath;
			$html = ob_get_clean();
            break;
        case 'tpl':
            $generator = new wcmTemplateGenerator(wcmProject::getInstance());
            $templateParams = array('searchConfig' => $this, 'searchContext' => $context);
            $html = $generator->executeTemplate($templatePath, $templateParams);
            break;
        default:
            $html = null;
            break;
        }

        if ($html)
        {
            // Include the corresponding JavaScript file if any
            $jsPath = $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '.js';
            if (file_exists($jsPath))
            {
                ob_start();
                include $jsPath;
                $html .= ob_get_clean();
            }
        }

        return $html;
    }

    /**
     * Designs a search form based on a given search configuration, a
     * page type, some search parameters, and some search options.
     *
     * @param string $configId  The search configuration ID
     * @param string $pageType  The search page type
     * @param array $parameters Search parameters from which to populate input fields
     * @param array $options    Search options, eg., searchId, searchPage, etc.
     *
     * @return string|null The form HTML, or null on error
     */
    public function designSearchForm($configId, $pageType, $parameters, $options)
    {


        // Get the corresponding search configuration
        $search = $this->getSearch($configId);
        if (!$search || !($searchFormElements = $search->xpath("//$pageType/searchForm/*")))
            return null;

        $html = '';
        foreach ($searchFormElements as $element)
        {
            $elementName = $element->getName();
            $html .= '<'.$elementName.'>';
            foreach ($element->children() as $child)
            {
                $this->localizeNode($child, $element, $parameters, $options);
                $html .= $child->asXML();
            }

            $html .= '</'.$elementName.'>';
        }

        return $html;
    }

    /**
     * Replaces all localization constants in a given node with their
     * localized values.
     *
     * @param DOMNode &$node     The node to process
     * @param DOMNode &$parent    The node's parent
     * @param array   $parameters The request parameters
     * @param array   $options    The provider options
     */
    private function localizeNode(&$node, &$parent, $parameters = null, $options = null)
    {
        // Process the node according to its name
        $nodeName = $node->getName();
        if ($nodeName == 'label' || $nodeName == 'legend')
        {
            $parent->$nodeName = getConst(strip_tags($node->asXML()));
        }
        elseif ($nodeName == 'input')
        {
            $nodeType = $node['type'];
            if ($nodeType == 'button' || $nodeType == 'reset' || $nodeType == 'submit')
            {
                $node['value'] = htmlspecialchars_decode(getConst($node['value']));
            } else {
                $node['value'] = htmlspecialchars_decode($node['value']);
            }
        }
        elseif ($nodeName == 'dataProvider')
        {
            $class = strip_tags($node->asXML());
            $field = new $class();
            $field->getData($parameters, $options);
            $field->renderData($parent, $node);
        }

        // Recursively process any children
        foreach ($node->children() as $child)
            $this->localizeNode($child, $node);
    }
}
?>