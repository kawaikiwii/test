<?php

/*
 * Project:     WCM
 * File:        tree_menu.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/*
 * The class is a helper to build menu tree
 *
 */
class tree_menu
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

        // Remove previous menus
        $tree->caption = getConst(_MENUS);        
        $tree->removeChildren();
        $tree->selectionId = null;

        // Load root menus
        foreach($project->layout->getRootMenus(true) as $menu)
        {
            $child = new wcmNode($tree, $menu->id);
            $child->class    = "wcmMenu";
            $child->link     = wcmMVC_Action::computeObjectURL($child->class, $child->id);
            $child->icon     = "menu.gif";
            $child->caption  = getConst($menu->name);
            $tree->addChild($child);

            // Recursive refresh
            if ($recursive && $maxDepth > $node->depth)
            {
                $child->expanded = true;
                self::refreshNode($child, $recursive, $maxDepth);
            }
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
            case "wcmMenu":
                // Add sub-menus
                $rootMenu = $project->layout->getMenuById($node->id);
                if (!$rootMenu) return;
                
                foreach($rootMenu->getSubMenus() as $menu)
                {
                    $child = new wcmNode($node->tree, $menu->id);
                    $child->class    = "wcmMenu";
                    $child->link     = wcmMVC_Action::computeObjectURL($child->class, $child->id);
                    $child->icon     = "menu.gif";
                    $child->caption  = getConst($menu->name);
                    $child->expanded = ($menu->subMenusCount() == 0);
                    $node->addChild($child);

                    // Recursive refresh
                    if ($recursive && $maxDepth > $node->depth)
                    {
                        $child->expanded = $autoExpand;
                        self::refreshNode($child, $recursive, $maxDepth);
                    }
                }
                break;
        }
    }
}
?>
