<?php

/**
 * Project:     WCM
 * File:        wcm.node.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * The wcmNode class represents a node belonging to a {@link wcmTree} object
 */
class wcmNode
{
    /**
     * Node id
     */
    public $id;

    /**
     * Node path
     */
    public $path;

    /**
     * wcmTree object associated to current node
     */
    public $tree;

    /**
     * Node class
     */
    public $class = null;

    /**
     * Node link (url)
     */
    public $link = null;

    /**
     * Node icon
     */
    public $icon = null;

    /**
     * Module associated to node (or null to use tree module)
     */
    public $module = null;

    /**
     * Generic xml parameters (root tag is "parameter")
     * => Warning, wcmNode use xml serialization, this propery must be well-formed !
     */
    public $parameters = "<parameters></parameters>";

    /**
     * Node depth (1 represents the root level)
     */
    public $depth = 1;

    /**
     * Parent node (or null)
     */
    public $parentNode = null;

    /**
     * An array of child nodes
     */
    public $childNodes = array();

    /**
     * Node caption
     */
    public $caption = null;

    /**
     * True if node is expanded
     */
    public $expanded = false;

    /**
     * Constructor
     *
     * @param wcmTree $tree Tree wcmNode (mandatory)
     * @param string  $id   Node id
     */
    public function __construct(&$tree, $id)
    {
        $this->tree =& $tree;
        $this->id   = $id;
        $this->path = $id;
    }

    /**
     * Destructor
     */
    function __destruct()
    {
        unset($this->tree);
        unset($this->parentNode);
        unset($this->childNodes);
    }

    /**
     * Clear the parent node
     */
    public function clearParentNode()
    {
        $this->parentNode = null;
        $this->path  = $this->id;
        $this->depth = 1;
    }

    /**
     * Defines the parent node
     *
     * @param wcmNode $parentNode ParentNode (or null)
     */
    public function setParentNode(&$parentNode)
    {
        if ($parentNode === null)
        {
            $this->clearParentNode();
        }
        else
        {
            $this->parentNode =& $parentNode;
            $this->path = $parentNode->path . $this->tree->pathSeparator . $this->id;
            $this->depth = $parentNode->depth + 1;
        }
    }

    /**
     * Expands wcmNode
     */
    public function expand()
    {
        $this->expanded = true;
    }

    /**
     * Collapse wcmNode
     */
    public function collapse()
    {
        $this->expanded = false;
    }

    /**
     * Select current node in tree
     */
    public function select()
    {
        $this->tree->selectNode($this);
    }

    /**
     * Check if current node is selected
     *
     * @return boolean True if current node is selected, false otherwise
     */
    public function isSelected()
    {
        return ($this->tree->selectedNode === $this);
    }

    /**
     * Number of child nodes
     */
    public function childCount()
    {
        return (is_array($this->childNodes) ? count($this->childNodes) : 0);
    }

    /**
     * Add a child to current child nodes
     *
     * @param wcmNode $child New node to add
     */
    public function addChild(&$child)
    {
        $child->tree =& $this->tree;
        $child->setParentNode($this);
        array_push($this->childNodes, $child);
    }

    /**
     * Remove all children nodes
     */
    public function removeChildren()
    {
        $this->childNodes = array();
    }

    /**
     * Refresh node
     * => This method will invoke the "refreshNode()" method of the node (or tree) module
     *
     * @param bool $recursive  true to refresh children (default is false)
     * @param int  $maxDepth   Max depth (when refresh is recursive)
     *
     * @return boolean True on success, false otherwise
     */
    public function refresh($recursive=false, $maxDepth = 0)
    {
        $this->removeChildren();
        if ($this->module != null)
        {
            // Load module
            $path  = $this->tree->modulePath . $this->module . ".php";
            if (file_exists($path))
            {
                require_once($path);

                // Execute refresh method with arguments
                $args = array(
                            "node" => &$this,
                            "recursive" => $recursive,
                            "maxDepth" => $maxDepth);
                return call_user_func_array(array($this->module, "refreshNode"), $args);
            }
            else
            {
                $this->caption = "Invalid wcmNode module : " . $this->module;
                return false;
            }
        }
        else if ($this->tree->module != null)
        {
            // Load module
           $path  = $this->tree->modulePath . $this->tree->module . ".php";
            if (file_exists($path))
            {
                require_once($path);

                // Execute refresh method with arguments
                $args = array(
                            "node" => &$this,
                            "recursive" => $recursive,
                            "maxDepth" => $maxDepth);
                return call_user_func_array(array($this->tree->module, "refreshNode"), $args);
            }
            else
            {
                $this->caption = "Invalid wcmTree module : " . $this->tree->module;
                return false;
            }
        }
    }

    /**
     * Returns an xml representation of current wcmNode
     * This method can be overloaded
     * Notes : usually only the innerXml method is overloaded.
     *
     * @param bool $recursive   true to return children xml representation
     *                          even if they are not expanded
     *
     * @return string An xml string representing the current node
     */
    public function toXml($recursive=false)
    {
        $indent = str_repeat("    ", $this->depth);
        $xml  = $indent."<node";
        $xml .= " id=\"" .  wcmXML::xmlEncode($this->id) . "\"";
        $xml .= " class=\"" .  wcmXML::xmlEncode($this->class) . "\"";
        $xml .= " link=\"" .  wcmXML::xmlEncode($this->link) . "\"";
        $xml .= " depth=\"" .  wcmXML::xmlEncode($this->depth) . "\"";
        $xml .= " expanded=\"" .  ($this->expanded ? "1" : "0") . "\"";
        $xml .= " selected=\"" .  ($this->isSelected() ? "1" : "0") . "\"";
        $xml .= " path=\"" . wcmXML::xmlEncode($this->path) . "\"";
        $xml .= ">\n";
        $xml .= $indent."    <tree>" .  wcmXML::xmlEncode($this->tree->id) . "</tree>\n";
        $xml .= $indent."    <icon>" .  wcmXML::xmlEncode($this->icon) . "</icon>\n";
        $xml .= $indent."    <module parameters=\"" . wcmXML::xmlEncode($this->parameters) . "\">";
        $xml .= wcmXML::xmlEncode($this->module) . "</module>\n";
        $xml .= $indent."    <caption>" . wcmXML::xmlEncode($this->caption) . "</caption>\n";

        if ($this->expanded || $recursive)
        {
            foreach($this->childNodes as $child)
                $xml .= $child->toXml(true);
        }
        $xml .= $indent."</node>\n";

        return $xml;
    }

    /**
     * Initialize wcmNode from an xml element
     *
     * @param XmlElement An xml element from an XmlDocument representing the wcmNode
     */
    public function initFromXmlElement($element)
    {
        $this->id = $element->getAttribute("id");
        $this->path = $element->getAttribute("path");
        $this->class = $element->getAttribute("class");
        $this->link = $element->getAttribute("link");
        $this->expanded = ($element->getAttribute("expanded") == "1");
        $this->depth = intval($element->getAttribute("depth"));

        foreach($element->childNodes as $child)
        {
            switch($child->nodeName)
            {
                case "icon":
                    $this->icon = $child->nodeValue;
                    break;

                case "caption":
                    $this->caption = $child->nodeValue;
                    break;

                case "module":
                    $this->module = $child->nodeValue;
                    $this->parameters = $child->getAttribute("parameters");
                    break;

                case "node":
                    $childNode = new wcmNode($this->tree, $child->getAttribute("id"));
                    $this->addChild($childNode);
                    $childNode->initFromXmlElement($child);
                    break;
            }
        }
    }

    /**
     * Renders node after xsl transformation
     *
     * @param string $xsl Xsl to use (or null to use tree's xsl)
     * @param string $mode Xsl mode (a parameter named "mode" in the xsl)
     *
     * @return string The result of xsl-transformation on current node
     */
    public function renderHTML($xsl = null, $mode = "tree")
    {
        if ($xsl == null)
            $xsl = $this->tree->xsl;

        if ($xsl == null || !file_exists($xsl))
        {
            return "Invalid XSL $xsl !";
        }

        $domXml = new DOMDocument();
        if (!$domXml->loadXML($this->toXml(true)))
        {
            throw new Exception(_BIZ_INVALID_XML);
        }

        $domXsl = new DOMDocument();
        $domXsl->load($xsl);
        $proc = new XSLTProcessor;
        $proc->registerPhpFunctions();
        $proc->importStyleSheet($domXsl);
        $proc->setParameter("", "mode", $mode);
        $proc->setParameter("", "baseUrl", $this->tree->baseUrl);
        $proc->setParameter("", "jsCallback", $this->tree->jsCallback);
        $html = $proc->transformToXML($domXml);

        return $html;
    }
}
?>
