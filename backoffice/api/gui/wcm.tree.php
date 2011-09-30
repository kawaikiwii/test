<?php

/**
 * Project:     WCM
 * File:        wcm.tree.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * The wcmTree class represents a generic tree object
 * => The tree use "module" to manage refreshment
 * => A module is a php class implementing refreshTree() and refreshNod() methods
 */
class wcmTree
{
    /**
     * Tree unique id (used for serialization and storage in $_SESSION)
     */
    public $id;

    /**
     * Path separator (default is ':')
     */
    public $pathSeparator;

    /**
     * Path to module (or null to use "/modules/tree" relative to project configuration path)
     */
    public $modulePath;

    /**
     * Module used for refreshing tree
     */
    public $module;

    /**
     * Xsl used to render tree
     */
    public $xsl = null;

    /**
     * Icon associated to tree
     */
    public $icon = null;

    /**
     * Caption associated to tree
     */
    public $caption = null;

    /**
     * Base URL for tree links
     */
    public $baseUrl = null;

    /**
     * Default URL for the root node
     */
    public $link = null;
    
    /**
     * Javascript callback to invoke when node is clicked (_wcmTreeRedirect is default)
     */
    public  $jsCallback;

    /**
     * Xml parameters (starting with a root tag "parameters")
     * => Warning, as tree use xml serialization for storage and render, this property must be well-formed
     */
    public $parameters = null;

    /**
     * The selected node (or null)
     */
    public $selectedNode = null;

    /**
     * An array of child nodes (at root-level)
     */
    public $childNodes  = array();

    /**
     * Constructor
     *
     * @param string $id            Tree id
     * @param string $baseUrl       Base URL for tree links (may be null or empty)
     * @param string $caption       Tree caption (when null, id will be used)
     * @param string $icon          Tree icon (when null, id will be used)
     * @param string $module        Module to refresh wcmTree (null)
     * @param string $modulePath    Path to module (or null to use "WCM_DIR . /modules/tree)
     * @param string $pathSeparator Separator used to create wcmNode path (":")
     * @param string $xsl           Path to xsl (or null to use "WCM_DIR . /xsl/tree/default.xsl")
     * @param string $jsCallback    Javascript callback to invoke when node is clicked (_wcmTreeRedirect is default)
     */
    function __construct($id, $baseUrl = null, $caption = null, $icon = null, $module = null, $modulePath = null, $pathSeparator = ':', $xsl = null, $jsCallback = null)
    {
        $config = wcmConfig::getInstance();
        // Fix baseUrl trailer slash
        if ($baseUrl && substr($baseUrl, -1) != '/')
            $baseUrl .= '/';
        $this->id = $id;
        $this->baseUrl = ($baseUrl == null) ? $config['wcm.backOffice.url'] : $baseUrl;
        $this->link = wcmMVC_Action::computeUrl();
        $this->caption = ($caption == null) ? $id : $caption;
        $this->icon = ($icon == null) ? 'refresh.gif' : $icon;
        $this->module = ($module == null) ? 'tree_'.$id : $module;
        $this->modulePath = ($modulePath == null) ? WCM_DIR . '/modules/tree/' : $modulePath;
        $this->pathSeparator = $pathSeparator;
        $this->xsl = ($xsl == null) ? WCM_DIR . '/xsl/tree/default.xsl' : $xsl;
        $this->jsCallback = ($jsCallback == null) ? '_wcmTreeRedirect' : $jsCallback;
    }

    /**
     * Refresh tree and select a specific node by its path
     *
     * @param string  $path   Path of wcmNode to select
     * @param boolean $select True to select node when found (default is false)
     *
     * @return boolean True if node was found, false otherwise
     */
    public function refreshToPath($path, $select = false)
    {
        $this->refresh();
        $ids = explode($this->pathSeparator, $path);
        $node =& $this;
        foreach($ids as $id)
        {
            // Search node with current id
            foreach($node->childNodes as $child)
            {
                if ($child->id == $id)
                {
                    // Last node found ?
                    if ($child->path == $path)
                    {
                        // Select node after refresh ?
                        if ($select)
                            $this->selectNode($child);
                        return true;
                    }

                    // Move forward in the node's hierarchy
                    $node =& $child;
                    $node->refresh();
                    break;
                }
            }
        }

        // Node was not found
        return false;
    }

    /**
     * Selects a specific node
     *
     * @param wcmNode $node Node to select (or null to clear selection)
     */
     public function selectNode(&$node)
     {
        if ($node == null)
            $this->clearSelection();
        else
            $this->selectedNode =& $node;
     }

    /**
     * Refresh tree
     * => This method invokes the "refreshTree()" method of the module
     *
     * @param bool $recursive  true to refresh children (default is false)
     * @param int  $maxDepth   Max depth (when refresh is recursive)
     *
     * @return boolean True on success, false on failure
     */
    public function refresh($recursive = false, $maxDepth = 0)
    {
        $this->clearSelection();
        $this->removeChildren();

        if ($this->module != null)
        {
            // Load module
            $path  = $this->modulePath . $this->module . ".php";
            if (file_exists($path))
            {
                require_once($path);

                // Execute refresh method with arguments
                $args = array(
                            "tree" => &$this,
                            "recursive" => $recursive,
                            "maxDepth" => $maxDepth);

                return call_user_func_array(array($this->module, "refreshTree"), $args);
            }
            else
            {
                $this->caption = "Invalid wcmTree module : " . $this->module;
                return false;
            }
        }

        return true;
    }

    /**
     * Clear tree selection
     */
    public function clearSelection()
    {
        $this->selectedNode = null;
    }

    /**
     * Number of root child nodes
     */
    public function childCount()
    {
        return (is_array($this->childNodes) ? count($this->childNodes) : 0);
    }

    /**
     * Add a node to current tree
     *
     * @param wcmNode $child Child node to add
     */
    public function addChild(&$child)
    {
        $child->wcmTree =& $this;
        $child->parentNode = null;
        $child->path = $child->id;
        $child->depth = 0;
        array_push($this->childNodes, $child);
    }

    /**
     * Remove all children nodes
     */
    function removeChildren()
    {
        $this->childNodes = array();
    }

    /**
     * Finds a wcmNode by its path
     *
     * @param string $path          Path of wcmNode to select
     * @param bool $autoRefresh     True to refresh wcmNode when no child found (defaut is false)
     * @param bool $autoExpand      True to expand wcmNode along the path (default is true)
     *
     * @return The specified wcmNode or null when not found
     */
    function getNodeByPath($path, $autoRefresh=false, $autoExpand=true)
    {
        if ($path == null || $path == "") return null;

        $ids = explode($this->pathSeparator, $path);
        $node =& $this;
        foreach($ids as $id)
        {
            // Auto-refresh children ?
            if ($autoRefresh && $node->childCount() == 0)
                $node->refresh();

            // Search child wcmNode by its id
            $found = false;
            foreach($node->childNodes as $child)
            {
                if ($child->id == $id)
                {
                    $found = true;
                    if ($autoExpand)
                        $child->expanded = true;
                    $node =& $child;
                    break;
                }
            }
            if ($found == false) return null;
        }

        // Found
        return $node;
    }

    /**
     * Select a wcmNode by its path
     *
     * @param string $path  Path of wcmNode to select
     * @param bool $autoRefresh     True to refresh wcmNode when no child found (defaut is false)
     * @param bool $autoExpand      True to expand wcmNode along the path (default is true)
     *
     * @return pwnNode The selected wcmNode or null when not found
     */
    public function selectNodeByPath($path, $autoRefresh=false, $autoExpand=true)
    {
        $node = $this->getNodeByPath($path, $autoRefresh, $autoExpand);
        if ($node) $this->selectNode($node);

        return $node;
    }

    /**
     * Returns an xml representation of current wcmTree
     *
     * @return string An xml representation of tres used for storage and render (xsl transformation)
     */
    public function toXml()
    {
        $xml  = "<tree id=\"" . $this->id . "\">\n";
        $xml .= "    <baseUrl>" .  wcmXML::xmlEncode($this->baseUrl) . "</baseUrl>\n";
        $xml .= "    <link>" .  wcmXML::xmlEncode($this->link) . "</link>\n";
        $xml .= "    <jsCallback>" .  wcmXML::xmlEncode($this->jsCallback) . "</jsCallback>\n";
        $xml .= "    <icon>" .  $this->icon . "</icon>\n";
        $xml .= "    <caption>" .  wcmXML::xmlEncode($this->caption) . "</caption>\n";
        $xml .= "    <pathSeparator>" .  wcmXML::xmlEncode($this->pathSeparator) . "</pathSeparator>\n";
        $xml .= "    <xsl>" . wcmXML::xmlEncode($this->xsl) . "</xsl>\n";
        $xml .= "    <modulePath>" . wcmXML::xmlEncode($this->modulePath) . "</modulePath>";
        $xml .= "    <module parameters=\"" . wcmXML::xmlEncode($this->parameters) . "\">";
        $xml .= wcmXML::xmlEncode($this->module) . "</module>\n";
        if ($this->selectedNode == null)
            $xml .= "    <selection/>\n";
        else
            $xml .= "    <selection id='" . $this->selectedNode->id . "' path='". $this->selectedNode->path . "'/>\n";
        foreach($this->childNodes as $root)
            $xml .= $root->toXml(true);
        $xml .= "</tree>";

        return $xml;
    }

    /**
     * Initialize tree from session object (or create a new session object if needed)
     *
     * @param int $id An optional tree identifier (or null to use current id)
     *
     * @return boolean True on success, false otherwise
     */
    public function initFromSession($id = null)
    {
        if ($id != null)
            $this->id = $id;

        if (!isset($_SESSION["tree_".$this->id]))
        {
            $this->refresh();
            $this->saveIntoSession();
        }
        return $this->initFromXml($_SESSION["tree_".$this->id]);
    }

    /**
     * Saves tree into session object
     */
    public function saveIntoSession()
    {
        $_SESSION["tree_".$this->id] = $this->toXml();
    }

    /**
     * Initialize tree from an xml string
     *
     * @param string $xml An xml string representing the tree
     *
     * @return boolean True on success, false otherwise
     */
   public function initFromXml($xml)
   {
        if ($xml == null)
            return false;

        $dom = new DOMDocument();
        if (!$dom->loadXML($xml))
        {
            throw new Exception(_BIZ_INVALID_XML);
        }

        return $this->initFromXmlElement($dom->documentElement);
   }

    /**
     * Initialize wcmTree from an xml element
     *
     * @param XmlElement $root An XmlElement of a valid DomDocument
     *
     * @return boolean True on success, false otherwise
     */
    public function initFromXmlElement($root)
    {
        $this->clearSelection();
        $this->removeChildren();
        $selectedPath = null;

        $this->id = $root->getAttribute("id");

        // Id is mandatory
        if ($this->id == null)
            return false;

        foreach($root->childNodes as $child)
        {
            switch($child->nodeName)
            {
                case "baseUrl":
                case "link":
                case "jsCallback":
                case "icon":
                case "caption":
                case "pathSeparator":
                case "xsl":
                case "modulePath":
                    $name = $child->nodeName;
                    $this->$name = $child->nodeValue;
                    break;

                case "selection":
                    $selectedPath = $child->getAttribute("path");
                    break;

                case "module":
                    $this->module = $child->nodeValue;
                    $this->parameters = $child->getAttribute("parameters");
                    break;

                case "node":
                    $childNode = new wcmNode($this, $child->getAttribute("id"));
                    $this->addChild($childNode);
                    $childNode->initFromXmlElement($child);
                   break;
            }
        }

        // Select wcmNode by its path
        $this->selectNodeByPath($selectedPath, true);

        return true;
    }

    /**
     * Render wcmTree thanks to an xsl transformation
     *
     * @param string $xsl Path to xsl (or null to use 'WCM_DIR . /xsl/tree/default.xsl')
     * @param string $mode Xsl mode (an xsl parameter named "mode")
     *
     * @return string Result of the xsl transformation
     */
    public function renderHTML($xsl = null, $mode = "tree")
    {
        $domXml = new DOMDocument();
        $domXml->loadXML($this->toXml());
        $domXsl = new DOMDocument();
        $domXsl->load($this->xsl);

        if (!$domXml) return "*Invalid XML";
        if (!$domXsl) return "*Invalid XSL : " . $xsl;

        $proc = new XSLTProcessor;
        $proc->registerPhpFunctions();
        $proc->importStyleSheet($domXsl);
        $proc->setParameter("", "mode", $mode);
        // @todo : this fix is hackish. For some reason, IE expects a character.
        // this also works if we create a $html = 'A'; variable first.
        $proc->setParameter("", "baseUrl", utf8_encode($this->baseUrl));
        $proc->setParameter("", "jsCallback", $this->jsCallback);
        $proc->setParameter("", "type", $this->id);

        $html = $proc->transformToXML($domXml);

        return $html;
    }
}

?>
