<?php

/*
 * Project:     WCM
 * File:        tree_list.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/*
 * The class is a helper to build template tree
 *
 */
class tree_list
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
        $config  = wcmConfig::getInstance();
        
        // Remove previous categories
        $tree->removeChildren();
        $tree->selectionId = null;

        
        //Load folders
        foreach(wcmList::getRootContent() as $lists)
        {

            $child = new wcmNode($tree, $lists['id']);
            $child->class    = "wcmList";
            $child->link     = wcmMVC_Action::computeObjectURL($child->class, $child->id);
            $child->icon     = "wcmList.gif";
            $child->caption  = $lists['label'];
            $child->expanded = $recursive;
            $tree->addChild($child);
            
            // Recursive refresh
            if ($recursive)
            {
                 tree_list::refreshNode($child, $recursive, $maxDepth);
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
        $config  = wcmConfig::getInstance();
        
        
            foreach(wcmList::getFinalContent($node->id,1,'id') as $key => $lists)
            {
              if($node->id != $key)
              {
                $child = new wcmNode($node->tree, $key);
                $child->class    = "wcmList";
                $child->link     = wcmMVC_Action::computeObjectURL($child->class, $key);
                $child->icon     = "wcmList.gif";
                $child->caption  = $lists;//getConst($category->name);
                $node->addChild($child);
              }
              
              // Recursive refresh
              if ($recursive && $maxDepth > $node->depth)
              {
                  $child->expanded = true;
                  tree_list::refreshNode($child, $recursive, $maxDepth);
              }
            }
    }
}
?>