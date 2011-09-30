<?php

/*
 * Project:     WCM
 * File:        tree_membership.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/*
 * The class is a helper to build membership tree
 *
 */
class tree_membership
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
        $tree->caption = getConst(_MEMBERSHIP);
        $tree->removeChildren();
        $tree->selectionId = null;

        // Load groups
        foreach($project->membership->getGroups() as $group)
        {
            $child = new wcmNode($tree, $group->id);
            $child->class    = "wcmGroup";
            $child->link     = wcmMVC_Action::computeObjectURL($child->class, $child->id);
            $child->icon     = "group.gif";
            $child->caption  = getConst($group->name);
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
            case "wcmGroup":
                // Add users
                foreach($project->membership->getUsersOfGroup($node->id) as $user)
                {
                    $child = new wcmNode($node->tree, $user->id);
                    $child->class    = "wcmUser";
                    $child->link     = wcmMVC_Action::computeObjectURL($child->class, $child->id);
                    $child->icon     = "user.gif";
                    $child->caption  = getConst($user->name);
                    $child->expanded = true;
                    $node->addChild($child);

                    // Recursive refresh
                    if ($recursive && $maxDepth > $node->depth)
                    {
                        $child->expanded = $autoExpand;
                        self::refreshNode($child, $recursive, $maxDepth);
                    }
                }
                break;

            case "wcmUser":
                // No child
                break;
        }
    }
}
?>