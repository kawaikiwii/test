<?php
/**
 * Project:     WCM
 * File:        business/ajax/taxonomy/biz.autocomplete.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

// Initialize the system
require_once dirname(__FILE__) . '/../../../initWebApp.php';

    // Retrieve parameters
    $prefix = getArrayParameter($_REQUEST, 'prefix');
    $type = getArrayParameter($_REQUEST, 'type', 'general');
    $maxValues = intval(getArrayParameter($_REQUEST, 'maxValues', 8));
    

    // Load available taxonomies
    $domXml = new DOMDocument;
    $domXml->load(WCM_DIR.'/business/xml/taxonomies/default.xml');
    $domXPath = new DOMXPath($domXml);
    
    /*
     * Determine starting node
     *   - If prefix is "a/b/c" search for tags starting with 'c' in tag 'b' of tag 'a'
     *   - If prefix is "abc" search for tags starting with 'abc' in the entire document
     */
    $startingNode = wcmXML::getXPathFirstNode($domXPath, $domXml->documentElement, $type);
    $parts = explode('/', $prefix);
    $prefix = array_pop($parts);
    $tags = array();
    
    // Browse descendants
    while (count($parts) > 0)
    {
        // Search node
        $value = array_shift($parts);
        foreach($startingNode->childNodes as $child)
        {
            if ($child->nodeName == 'tag' && wcmXML::getNodeValue($child) == $value)
            {
                $startingNode = $child;
                break;
            }
        }
    }
    
    // Search for matching tags in the taxonomy
    $nodes = $domXPath->query('.//tag', $startingNode);
    foreach ($nodes as $node)
    {
        $key = wcmXML::getNodeValue($node);
        $start = substr($key, 0, strlen($prefix));
        if (strtolower($start) == strtolower($prefix))
        {
            // Compute label (node hierarchy)
            $label = $key;
            while ($node->parentNode)
            {
                if ($node->parentNode->nodeName != 'tag')
                    break;
                
                $node = $node->parentNode;
                $label = wcmXML::getNodeValue($node) . '/' . $label;
            }
            
            // Add tag and check if we have reached the maximum number of values
            $tags[$key] = $label;
            if (count($tags) == $maxValues) break;
        }
    }

    // Render available tags
    echo '<ul>';
    foreach ($tags as $key => $label)
    {
        echo '<li>' . $label . '</li>';
    }
    echo '</ul>';