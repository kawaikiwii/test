<?php

/*
 * Project:     WCM
 * File:        tree_sysclass.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/*
 * The class is a helper to build sysclass tree
 *
 */
class tree_sysclass
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
        $tree->caption = getConst(_SYSCLASSES);
        $tree->removeChildren();
        $tree->selectionId = null;

        // Load sysclasses
        foreach($project->bizlogic->getSysclasses(true) as $sysclass)
        {
            $child           = new wcmNode($tree, $sysclass->id);
            $child->class    = "wcmSysclass";
            $child->link     = wcmMVC_Action::computeObjectURL($child->class, $child->id);
            $child->icon     = $sysclass->className . ".gif";
            $child->caption  = getConst($sysclass->name);
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
            case "wcmSysclass":
                // No child
                break;
        }
    }
}
?>