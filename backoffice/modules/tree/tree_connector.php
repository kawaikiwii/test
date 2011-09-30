<?php

/*
 * Project:     WCM
 * File:        tree_connector.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/*
 * The class is a helper to build template tree
 *
 */
class tree_connector
{
    /************************************************************************
     * Refresh current tree
     *
     * @param tree  $tree       tree to refresh
     * @param bool  $recursive  True to perform recursive refresh
     * @param int   $maxDepth   Max depth (when refresh is recursive)
     *
     ************************************************************************/
    static function refreshTree(&$tree, $recursive = false, $maxDepth = 0)
    {
        $session = wcmSession::getInstance();
        $project = wcmProject::getInstance();

        // Remove previous connectors
        $tree->caption = getConst(_CONNECTORS);
        $tree->removeChildren();
        $tree->selectionId = null;

        // Load connectors
        $connectors = $project->datalayer->getConnectors(true);
        foreach($connectors as $connector)
        {
            $child = new wcmNode($tree, $connector->id);
            $child->class    = "wcmConnector";
            $child->link     = wcmMVC_Action::computeObjectURL($child->class, $child->id);
            $child->icon     = "connector.gif";
            $child->caption  = getConst($connector->name);
            $child->expanded = true;
            $tree->addChild($child);

            // Recursive refresh
            tree_connector::refreshNode($child, $recursive, $maxDepth);
        }
    }

    /************************************************************************
     * Refresh current node
     *
     * @param node  $node       Node to refresh
     * @param bool  $recursive  True to perform recursive refresh
     * @param int   $maxDepth   Max depth (when refresh is recursive)
     *
     ************************************************************************/
    static function refreshNode(&$node, $recursive, $maxDepth = 0, $autoExpand = false)
    {
        $session = wcmSession::getInstance();
        $project = wcmProject::getInstance();

        switch($node->class)
        {
            case "wcmConnector":
                break;
        }
    }
}
?>