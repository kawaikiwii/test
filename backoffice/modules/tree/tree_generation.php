<?php
/*
 * Project:     WCM
 * File:        tree_generation.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/*
 * The class is a helper to build generation tree
 *
 */
class tree_generation
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
        $session = wcmSession::getInstance();

        // Remove previous generation sets
        $tree->caption = getConst(_GENERATION);
        $tree->removeChildren();
        $tree->selectionId = null;

        // Load generation sets
        $generationSets = $project->generator->getGenerationSets(true);

        foreach($generationSets as $generationSet)
        {
            if ($session->isAllowed($generationSet, wcmPermission::P_READ))
            {
                $child = new wcmNode($tree, $generationSet->id);
                $child->class    = "wcmGenerationSet";
                $child->link     = wcmMVC_Action::computeObjectURL($child->class, $child->id);
                $child->icon     = "generationSet.gif";
                $child->caption  = getConst($generationSet->name);
                $tree->addChild($child);
    
                // Recursive refresh
                if ($recursive && $maxDepth > $node->depth)
                {
                    $child->expanded = true;
                    self::refreshNode($child, $recursive, $maxDepth);
                }
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
        $project = wcmProject::getInstance();

        switch($node->class)
        {
            case "wcmGenerationSet":
                // Add generation content (use cache)
                $generations = $project->generator->getGenerations();
                foreach($generations as $generation)
                {
                    if ($generation->generationSetId == $node->id)
                    {
                        $child = new wcmNode($node->tree, $generation->id);
                        $child->class    = "wcmGeneration";
                        $child->link     = wcmMVC_Action::computeObjectURL($child->class, $child->id);
                        $child->icon     = "generation.gif";
                        $child->caption  = getConst($generation->name);
                        $node->addChild($child);
                    }
                }
                break;
                
            case "wcmGeneration":
                // Add generation content (use cache)
                foreach($project->generator->getGenerationContents() as $content)
                {
                    if ($content->generationId == $node->id)
                    {
                        $child = new wcmNode($node->tree, $content->id);
                        $child->class    = "wcmGenerationContent";
                        $child->link     = wcmMVC_Action::computeObjectURL($child->class, $child->id);
                        $child->icon     = ($content->loop == null || $content->loop == "") ? "field.gif" : "table.gif";
                        $child->caption  = getConst($content->name);
                        $child->expanded = true;
                        $node->addChild($child);
                    }
                }
                break;

            case "wcmGenerationContent":
                // No refresh needed
                break;
        }
    }
}
?>