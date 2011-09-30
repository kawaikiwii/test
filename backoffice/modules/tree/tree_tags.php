<?php

/*
 * Project:     WCM
 * File:        tree_tags.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/*
 * The class is a helper to build tags tree
 *
 */
class tree_tags
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
        $project = wcmProject::getInstance();

        // Remove previous types
        $tree->caption = getConst(_TAGS);
        $tree->removeChildren();
        $tree->selectionId = null;

        // Determine context and target class name
        list($context, $className, $id) = explode('_', $tree->id);

        // Load taxonomy
        $taxonomy = loadTaxonomy(
            $className,                                 // target class name
            WCM_DIR . '/xml/taxonomies/default.xml',    // default taxonomy
            $_SESSION['siteId']                         // site-specific suffix
        );

        // For each top-level type ...
        if ($taxonomy)
        {
            foreach (array_keys($taxonomy) as $type)
            {
                // Create child node
                $child = tree_tags::createNode('type', $tree, $type, $type);
                $child->expanded = $recursive;

                // Add child node to tree
                $tree->addChild($child);
    
                if ($recursive)
                {
                    // Refresh child node recursively
                    tree_tags::refreshNode($child, $recursive, $maxDepth, true);
                }
            }
        }

        // Save taxonomy in session to avoid reloading it for each node
        $_SESSION[$tree->id] = $taxonomy;
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
        $project = wcmProject::getInstance();

        switch ($node->class)
        {
            case "type":
                // Get taxonomy from session
                $taxonomy = $_SESSION[$node->tree->id];

                // Get type corresponding to tree node
                $typeSep = ' / ';
                $types = explode($typeSep, $node->id);
                if ($types)
                {
                    foreach ($types as $type)
                    {
                        $taxonomy = $taxonomy[$type];
                    }
                }

                // For each sub-type ...
                if ($taxonomy)
                {
                    foreach ($taxonomy as $name => $value)
                    {
                        if (is_array($value))
                        {
                            // Create child node
                            $child = tree_tags::createNode('type', $node->tree, $node->id . $typeSep . $name, $name);
                        }
                        else
                        {
                            // Create child node
                            $child = tree_tags::createNode('tag', $node->tree, $node->id . '|' . $name, $name);
                        }
    
                        // Add child node to tree
                        $node->addChild($child);
    
                        if ($recursive && $maxDepth > $node->depth)
                        {
                            // Refresh recursively
                            $child->expanded = $autoExpand;
                            tree_tags::refreshNode($child, $recursive, $maxDepth);
                        }
                    }
                }
                break;

            case "tag":
                // No children
                break;
        }
    }

    private static function createNode($nodeType, $tree, $id, $caption)
    {
        $child          = new wcmNode($tree, $id);
        $child->class   = $nodeType;
        $child->caption = $caption;

        switch ($nodeType)
        {
            case 'type':
                $child->icon = "tagType.gif";
                break;

            case 'tag':
                $child->icon     = "tag.gif";
                $child->expanded = true;
                break;
        }

        return $child;
    }
}

?>
