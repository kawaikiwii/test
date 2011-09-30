<?php

/**
 * Project:     WCM
 * File:        wcm.XML.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * This class provides static static function to facilitate
 * XML, XSL and XPATH manipulation
 */
class wcmXML
{
    /**
     * Encode a string to be xml compliant ()replace <, >, ', " and &)
     *
     * @param $v mixed  The data to encode
     *
     * @return string   xml encoded string
     */
    static function xmlEncode($v)
    {
    	// Encode array
        if (is_array($v))
            return self::xmlEncodeArray($v);

        // Ignore empty string and object
        if ($v == null || is_object($v))
            return null;

        // Decode all entities
        $v = html_entity_decode($v, ENT_COMPAT, 'UTF-8');

        return htmlspecialchars($v, ENT_COMPAT, 'UTF-8');
    }

    /**
     * Decode a xml to be good string ()replace <, >, ', " and &)
     *
     * @param $v mixed  The data to decode
     *
     * @return string   string decoded xml
     */
    static function xmlDecode($v)
    {
        // Ignore empty string and object
        if ($v == null || is_object($v))
            return "";

        // Decode all entities
        $v = htmlentities($v, ENT_COMPAT, 'UTF-8');

        return htmlspecialchars_decode($v, ENT_COMPAT);
    }

    /**
     * Encode an array to be xml compliant
     *
     * @param $array array  An array to encode
     *
     * @return $xml string  array transformed in xml
     */
    static function xmlEncodeArray($array)
    {
        if (!is_array($array))
            return null;

        $xml = '';
        foreach($array as $key => $val)
        {
            if(preg_match('`^[a-zA-Z][a-zA-Z0-9]+$`', $key))
            {
            	// Alphanumeric key, let's create a tag!
                $xml .= '<'.$key.'>' . wcmXML::xmlEncode($val) . '</'.$key.'>';
            }
            else
            {
                // Numeric key, let's create a standard tag
                $xml .= '<item key="'.$key.'">' . wcmXML::xmlEncode($val) . '</item>';
            }
        }

        return $xml;
    }

    /**
     * Returns an xml string representing a given associative array
     * Each key is mapped to a tag
     *
     * @param $array array  the array to "xmlize"
     * @param $mainTag string (optional) the main surrounding tag
     *
     * @return string An XML representing the array
     */
    static function arrayToXml($array, $maintag='')
    {
        $xml = "";
        if ($maintag != '')
            $xml .= "<$maintag>";
        foreach($array as $key => $value)
            $xml .= "<$key>" . wcmXML::xmlEncode($value) . "</$key>";

        if ($maintag != '')
            $xml .= "</$maintag>";

        return $xml;
    }

    /**
     * Returns an associative array representing a given xml string
     * Each tag is mapped to a key
     *
     * @param $xml  xml string to pars
     *
     * @return array An assoc array representing the xml string
     */
    static function xmlToArray($xml)
    {
        $domXml = new DOMDocument();
        if (!$domXml->loadxml($xml))
        {
            throw new Exception('Invalid XML');
        }
        $rootNode = $domXml->documentElement;
        $array = array();
        foreach($rootNode->childNodes as $child)
        {
            $array[$child->nodeName] = self::xmlDecode($child->nodeValue);
        }
        return $array;
    }

    /**
     * Returns the innerXML from a DOMDocument node
     *
     * @param DOMNode A valid DOMNode
     *
     * @return string Textual value of the node (without children)
     */
    static function getNodeValue($node)
    {
        if ($node == null) return null;

        // Stop at the first text node
        foreach($node->childNodes as $child)
        {
            if ($child->nodeType == XML_TEXT_NODE)
                return trim($child->nodeValue);
        }
        return trim($node->nodeValue);
    }

    /**
     * Returns the outer XML content of a node (i.e. current tag include)
     *
     * @return string Outer XML content of a node
     */
    static function getOuterXml($node)
    {
        return ($node == null) ? null : $node->ownerDocument->saveXML($node);
    }

    /**
     * Returns the inner XML content of a node
     *
     * @return string Inner XML content of a node
     */
    static function getInnerXml($node)
    {
        if ($node == null)
            return null;

        $innerXml = '';
        $xmlDoc = $node->ownerDocument;
        foreach($node->childNodes as $child)
        {
            $innerXml .= $xmlDoc->saveXML($child);
        }
        return $innerXml;
    }

    /**
     * Retrieve the first node corresponding to a specific XPath query
     *
     * @param DomXPath  A valid DomXPath object
     * @param DomNode   Context node (or null)
     * @param string    XPath query
     *
     * @return string   The first node (or null)
     */
    static function getXPathFirstNode($xPath, $contextNode = null, $query)
    {
        // XPath query
        if ($contextNode)
            $nodeList = @$xPath->query($query, $contextNode);
        else
            $nodeList = @$xPath->query($query);

        // Retrieve first value or else return default value
        if ($nodeList && $nodeList->length > 0)
            return $nodeList->item(0);
        else
            return null;
    }

    /**
     * Retrieve the first node value corresponding to a specific XPath query
     *
     * @param DomXPath  A valid DomXPath object
     * @param DomNode   Context node (or null)
     * @param string    XPath query
     * @param mixed     Default value to return if query does not match any node (default is null)
     *
     * @return string   The node value (or the default value)
     */
    static function getXPathNodeValue($xPath, $contextNode = null, $query, $defaultValue = null)
    {
        // XPath query
        if ($contextNode)
            $nodeList = @$xPath->query($query, $contextNode);
        else
            $nodeList = @$xPath->query($query);

        // Retrieve first value or else return default value
        if ($nodeList && $nodeList->length > 0)
            return trim($nodeList->item(0)->nodeValue);
        else
            return $defaultValue;
    }

    /**
     * Process the XSL with the XML and returns the result
     *
     * @param string $xml    string  Contains filename of the XML
     * @param string $xsl    string  Contains filename of the XSL
     * @param array  $params array   Optional parameters for XSL-T (assoc array of null)
     *
     * @return string The result of the XSL transformation
     */
    static function processXSLT($xml, $xsl, $params = null)
    {
        // Create the domXSL
        $domXsl = new DOMDocument;
        if (!@$domXsl->load($xsl))
        {
            wcmProject::getInstance()->logger->logError('Invalid XSL: ' . $xsl);
            throw new Exception('Invalid XSL');
        }

        // Create the domXML
        $domXml = new DOMDocument();
        if (!@$domXml->loadXML($xml))
        {
            wcmProject::getInstance()->logger->logError('Invalid XML: ' . $xml);
            throw new Exception('Invalid XML');
        }

        // Process the XSL with optional parameters
        $proc = new XSLTProcessor;
        $proc->registerPHPFunctions();
        $proc->importStyleSheet($domXsl);
        if (is_array($params))
        {
            foreach($params as $param => $value)
            {
                $proc->setParameter("", $param, $value);
            }
        }

        return ($proc->transformToXML($domXml));
    }
}
