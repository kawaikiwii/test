<?php

/*
 * Project:     WCM
 * File:        tree_bizclass.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/*
 * The class is a helper to build bizclass tree
 *
 */
class tree_bizclass
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
        $tree->caption = getConst(_BIZCLASSES);
        $tree->removeChildren();
        $tree->selectionId = null;

        // Load bizclasses
        foreach($project->bizlogic->getBizclasses(true) as $bizclass)
        {
            $child           = new wcmNode($tree, $bizclass->id);
            $child->class    = "wcmBizclass";
            $child->link     = wcmMVC_Action::computeObjectURL($child->class, $child->id);
            $child->icon     = $bizclass->className . ".gif";
            $child->caption  = getConst($bizclass->name);
            $child->expanded = true;
            $tree->addChild($child);

            self::refreshNode($child, $recursive, $maxDepth);
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
            case "wcmBizclass":
                // No child
                break;
        }
    }
}
?>