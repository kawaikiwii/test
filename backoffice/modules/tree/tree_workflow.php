<?php
/*
 * Project:     WCM
 * File:        tree_workflow.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     3.2
 *
 * The class is a helper to build workflow tree
 *
 */
class tree_workflow
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

        // Remove previous node
        $tree->caption = getConst(_WORKFLOWS);
        $tree->removeChildren();
        $tree->selectionId = null;

        // Reset cache
        $project->workflowManager->getWorkflows(true);
        $project->workflowManager->getWorkflowStates(true);
       
        $child           = new wcmNode($tree, 'States');
        $child->class    = "wcmWorkflowState";
        $child->icon     = "workflowState.gif";
        $child->link     = wcmMVC_Action::computeObjectURL($child->class);
        $child->caption  = getConst(_WORKFLOW_STATES);
        $tree->addChild($child);

        self::refreshNode($child, $recursive, $maxDepth);
        $child->expanded = true;

        $child           = new wcmNode($tree, 'Workflows');
        $child->class    = "wcmWorkflow";
        $child->link     = wcmMVC_Action::computeObjectURL($child->class);
        $child->icon     = "workflow.gif";
        $child->caption  = getConst(_WORKFLOWS);
        $tree->addChild($child);

        self::refreshNode($child, $recursive, $maxDepth);
        $child->expanded = true;
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

        if ($node->id == 'Workflows')
        {
            // Load workflows
            foreach($project->workflowManager->getWorkflows() as $workflow)
            {
                $child           = new wcmNode($node->tree, $workflow->id);
                $child->class    = "wcmWorkflow";
                $child->link     = wcmMVC_Action::computeObjectURL($child->class, $child->id);
                $child->icon     = "workflow.gif";
                $child->caption  = getConst($workflow->name);
                $node->addChild($child);

                // Recursive refresh
                if ($recursive && $maxDepth > $node->depth)
                {
                    $child->expanded = true;
                    self::refreshNode($child, $recursive, $maxDepth);
                }
            }
        }
        elseif ($node->id == 'States')
        {
            // Load states
            foreach($project->workflowManager->getWorkflowStates() as $workflowState)
            {
                $child           = new wcmNode($node, $workflowState->id);
                $child->class    = "wcmWorkflowState";
                $child->link     = wcmMVC_Action::computeObjectURL($child->class, $child->id);
                $child->icon     = "workflowState.gif";
                $child->caption  = getConst($workflowState->name);
                $child->expanded = true;
                $node->addChild($child);
            }
         }
         elseif($node->class == 'wcmWorkflow')
         {
            // Load workflow transitions
            foreach($project->workflowManager->getTransitions() as $workflowTransition)
            {
                if ($workflowTransition->workflowId == $node->id)
                {
                    $child           = new wcmNode($node->tree, $workflowTransition->id);
                    $child->class    = "wcmWorkflowTransition";
                    $child->icon     = "workflowTransition.gif";
                    $child->link     = wcmMVC_Action::computeObjectURL($child->class, $child->id);
                    $child->caption  = getConst($workflowTransition->name);
                    $child->expanded = true;
                    $node->addChild($child);
                }
            }
        }
    }
}