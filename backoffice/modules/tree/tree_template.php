<?php

/*
 * Project:     WCM
 * File:        tree_template.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/*
 * The class is a helper to build template tree
 *
 */
class tree_template
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
        
        $tree->pathSeparator = "-"; 
        
        $path_templates = $config['wcm.templates.path'];

        //Load folders
        foreach($project->generator->getCategories($path_templates) as $category)
        {
            $child = new wcmNode($tree, $category);
            $child->class    = "wcmTemplateCategory";
            $child->link     = wcmMVC_Action::computeObjectURL($child->class, $child->id);
            $child->icon     = "category.gif";
            $child->caption  = getConst($category);
            $child->expanded = $recursive;
            $tree->addChild($child);

            // Recursive refresh
            if ($recursive)
            {
                 tree_template::refreshNode($child, $recursive, $maxDepth);
            }
            
        }
        //Load template files
        foreach($project->generator->getTemplatesByPath() as $key => $template)
        {
            $child = new wcmNode($tree, $key.".tpl");
            $child->class   = "wcmTemplate";
            $child->link     = wcmMVC_Action::computeObjectURL($child->class, $child->id);
            $child->icon    = "template.gif";
            $child->caption = getConst($key.".tpl");
            $child->expanded = true;
            $tree->addChild($child);
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
        
        switch($node->class)
        {
            case "wcmTemplateCategory":
                
            $relative_path = $node->id."/";
            $path_templates = $config['wcm.templates.path'] . $relative_path;
        
            foreach($project->generator->getCategories($path_templates) as $category)
            {
                $child = new wcmNode($node->tree, $relative_path.$category);
                $child->class    = "wcmTemplateCategory";
                $child->link     = wcmMVC_Action::computeObjectURL($child->class, $child->id);
                $child->icon     = "category.gif";
                $child->caption  = getConst($category);//getConst($category->name);
                $node->addChild($child);

                // Recursive refresh
                if ($recursive && $maxDepth > $node->depth)
                {
                    $child->expanded = $autoExpand;
                    tree_template::refreshNode($child, $recursive, $maxDepth);
                }
            }

            // Add templates
            foreach($project->generator->getTemplatesByPath($relative_path) as $key => $template)
            {
                $child = new wcmNode($node->tree, $relative_path.$key.".tpl");
                $child->class   = "wcmTemplate";
                $child->link     = wcmMVC_Action::computeObjectURL($child->class, $child->id);
                $child->icon    = "template.gif";
                $child->caption = getConst($key.".tpl");
                $child->expanded = true;
                $node->addChild($child);
            }
                break;
            
            case "wcmTemplate":
                // No child
                break;
        }
    }
}
?>