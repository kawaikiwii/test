<?php

/**
 * Project:     WCM
 * File:        biz.toolbox_xml.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * Loads the taxonomy for a given business class from a given XML file
 * and returns it as a tree, thus:
 *
 * taxonomy = array(
 *   type_1 => array(
 *     tag_1,
 *     ...,
 *     tag_N
 *     sub_type_1 = array(...),
 *     ...,
 *     sub_type_N = array(...),
 *   ),
 *   ...
 *   type_N => array(...),
 * );
 *
 * If $fileNameSuffix is non-empty, first tries to load file
 * "{basename($fileName)}_{$fileNAmeSuffix}.xml". If the suffix is empty
 * or the corresponding file does not exist, loads file "$fileName".
 *
 * @param string $className      The name of the business class
 * @param string $fileName       The name of the taxonomy file
 * @param string $fileNameSuffix The file name suffix (default: '')
 *
 * @return array The taxonomy as a tree
 */
function loadTaxonomy($className, $fileName, $fileNameSuffix = '')
{
    // Use the suffixed file name in preference to the plain one
    if ($fileNameSuffix)
    {
        $fileNameDir  = dirname($fileName);
        $fileNameBase = basename($fileName, '.xml');

        $fileNameWithSuffix = '';
        if ($fileNameDir)
        {
            $fileNameWithSuffix .= $fileNameDir . '/';
        }
        $fileNameWithSuffix .= $fileNameBase . '_' . $fileNameSuffix . '.xml';

        if (file_exists($fileNameWithSuffix))
        {
            $fileName = $fileNameWithSuffix;
        }
    }

    // The taxonomy as a tree
    $tree = array();

    // Load and parse the taxonomy file
    $domDoc = new DOMDocument();
    if ($domDoc->load($fileName))
    {
        // The corresponding XPath object
        $domXPath = new DOMXPath($domDoc);

        // Get the first node corresponding to the given business class
        $nodeList = $domXPath->query('/tags/common|/tags/' . $className);
        $numNodes = $nodeList->length;

        for ($i = 0; $i < $numNodes; ++$i)
        {
            $node = $nodeList->item($i);
            $tree = array_merge_recursive($tree,
                        loadTaxonomyInternal($domXPath, $node));
        }
    }

    return $tree;
}

/**
 * Loads a taxonomy from a given DOM node and returns it as a tree; see
 * function 'loadTaxonomy'.
 *
 * @param DOMXPath $domXPath The XPath object to use for queries
 * @param DOMNode  $node     The node from which to load the taxonomy
 * @param int                    Maximum tree depth (default: 0 ==> unlimited)
 *
 * @return array The taxonomy as a tree
 */
function loadTaxonomyInternal($domXPath, $node)
{
    // The types as a tree
    $tree = array();

    // Parse each tag
    $nodeList = $domXPath->query('tag|type', $node);
    $numNodes = $nodeList->length;

    for ($i = 0; $i < $numNodes; ++$i)
    {
        if ($nodeList->item($i)->nodeName == 'tag')
        {
            $tagNode = $nodeList->item($i);
            $tagName = $tagNode->nodeValue;
    
            $tree[$tagName] = $tagName;
        }
        else
        {
            $typeNode = $nodeList->item($i);
            $typeName = $typeNode->getAttribute('name');
    
            $subTree = loadTaxonomyInternal($domXPath, $typeNode);
    
            if (isset($tree[$typeName]))
            {
                $tree[$typeName] = array_merge_recursive($tree[$typeName], $subTree);
            }
            else
            {
                $tree[$typeName] = $subTree;
            } 
        }
    }
    return $tree;
}

// Loads the configuration file for search UI
function loadSearchConfiguration()
{
    $configFile = WCM_DIR . '/business/xml/search/default.xml';
    if (!file_exists($configFile))
        throw new Exception(_BIZ_CANNOT_LOAD_CONFIGURARTION_FILE);

    $_SESSION['search_configuration'] = $configFile;
}
?>
