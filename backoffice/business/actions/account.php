<?php
/**
 * Project:     WCM
 * File:        account.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
 
/**
 * This class implements the action controller for the channel
 */
class accountAction extends wcmMVC_BizAction
{
/**
     * Instanciate context (usually a sysobject)
     *
     * @param wcmSession $session Current session
     * @param wcmProject $project Current project
     */
    protected function setContext($session, $project)
    {
	$tree = new wcmTree($this->treeId);
	$tree->initFromSession();

	// If no id is defined, retrieve selected node (if exists)
	if ($this->id === null && $tree->selectedNode && $this->class == $tree->selectedNode->class && is_int($tree->selectedNode->id))
	{
	    $this->id = $tree->selectedNode->id;
	}
	else
	{
	    // If current context does not match selected node
	    // clear selection from the tree
	    if ($tree->selectedNode)
	    {
		if ($this->class != $tree->selectedNode->class || $this->id != $tree->selectedNode->id)
		{
		    $tree->selectedNode = null;
		    $tree->saveIntoSession();
		}
	    }
	}

	// Instanciate classname
	$className = $this->class;
	$this->context = new $className();
	$this->context->refresh($this->id);
        $session = wcmSession::getInstance();
    }
}
