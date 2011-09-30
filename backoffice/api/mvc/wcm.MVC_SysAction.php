<?php
/**
 * Project:     WCM
 * File:        wcm.MVC_SysAction.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */


/**
 * This class implements the default system action for the
 * MVC controller
 *
 * Basically it instanciate the context matching $_REQUEST['_wcmClass']
 * and execute the specific action according to $_REQUEST['_wcmTodo']
 */
class wcmMVC_SysAction extends wcmMVC_Action
{
    /**
     * Instanciate context (usually a wcmObject)
     *
     * @param wcmSession $session Current session
     * @param wcmProject $project Current project
     */
    protected function setContext($session, $project)
    {
        // Build tree automatically and recover selected object
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
        if (!$session->isAllowed($this->context, wcmPermission::P_READ))
            $this->redirect('?_wcmAction=403');
    }

    /**
     * Default action
     *
     * @param wcmSession $session Current session
     * @param wcmProject $project Current project
     */
    protected function on($session, $project)
    {
        // View is the default action
        $this->onView($session, $project);
    }

    /**
     * View action
     *
     * @param wcmSession $session Current session
     * @param wcmProject $project Current project
     */
    protected function onView($session, $project)
    {
        // Add statistics
        if ($this->context && $this->context->id)
        {
            $session->addStat(wcmSession::STAT_VIEW_OBJECT, $this->context, getObjectLabel($this->context));
        }
    }

    /**
     * onLock
     *
     * @param wcmSession $session Current session
     * @param wcmProject $project Current project
     */
    protected function onLock($session, $project)
    {
        if (!$this->context->lock($session->userId))
            wcmMVC_Action::setError(_BIZ_ERROR . $this->context->getErrorMsg());
    }

    /**
     * onUnlock
     *
     * @param wcmSession $session Current session
     * @param wcmProject $project Current project
     */
    protected function onUnlock($session, $project)
    {
        if (!$this->context->unlock($session->userId))
            wcmMVC_Action::setError(_BIZ_ERROR . $this->context->getErrorMsg());
    }

    /**
     * onCheckout
     *
     * @param wcmSession $session Current session
     * @param wcmProject $project Current project
     */
    protected function onCheckout($session, $project)
    {
        if (!$this->context->checkout())
            wcmMVC_Action::setError(_BIZ_ERROR . $this->context->getErrorMsg());
    }

    /**
     * onCheckin
     *
     * @param wcmSession $session Current session
     * @param wcmProject $project Current project
     */
    protected function onCheckin($session, $project)
    {
        $this->beforeSaving($session, $project);

        if (!$this->context->checkin($_REQUEST))
        {
            wcmMVC_Action::setError(_BIZ_ERROR . $this->context->getErrorMsg());
            return;
        }

        // Add statistics
        $session->addStat(wcmSession::STAT_SAVE_OBJECT, $this->context);

        // Create a new version?
        if (isset($_REQUEST['_comment']))
        {
            $this->onCreateVersion($session, $project);
        }

        // Redirect to 'view' URL
        $this->redirect(self::computeObjectURL($this->context->getClass(), $this->context->id));
    }

    /**
     * onUndocheckout
     *
     * @param wcmSession $session Current session
     * @param wcmProject $project Current project
     */
    protected function onUndocheckout($session, $project)
    {
        if (!$this->context->undoCheckout())
            wcmMVC_Action::setError(_BIZ_ERROR . $this->context->getErrorMsg());
    }

    /**
     * onSave
     *
     * @param wcmSession $session Current session
     * @param wcmProject $project Current project
     */
    protected function onSave($session, $project)
    {
        $this->beforeSaving($session, $project);
            
        // clone object ?
        if (isset($_REQUEST['clone']))
        {
            $this->onClone($session, $project, $_REQUEST);
        }
        else if (!$this->context->save($_REQUEST))
        {
            wcmMVC_Action::setError(_BIZ_ERROR . $this->context->getErrorMsg());
            return;
        }

        // Add statistics
        $session->addStat(wcmSession::STAT_SAVE_OBJECT, $this->context);
        
        // Create a new version?
        if (isset($_REQUEST['_comment']))
        {
            $this->onCreateVersion($session, $project);
        }
		
    	
        // Redirect to 'view' URL
        $this->redirect(self::computeObjectURL($this->context->getClass(), $this->context->id));
        
    }

    /**
     * onDelete
     *
     * @param wcmSession $session Current session
     * @param wcmProject $project Current project
     */
    protected function onDelete($session, $project)
    {
        $context = $this->context;
        $contextId = $this->context->id;
        
        if (!$context->delete())
        {
            wcmMVC_Action::setError(_BIZ_ERROR . $context->getErrorMsg());
            return;
        }

        // Add statistics
        $session->addStat(wcmSession::STAT_DELETE_OBJECT, $this->context);
        
        // Make sure item isn't linkable in statistics
        $session->disableStatItem(get_class($context), $contextId);

        // Redirect to default action page
        $this->redirect($this->computeURL());
    }

    /**
     * onTransition
     * Launched when a transition is executed from any sysobject
     *
     * @param wcmSession $session Current session
     * @param wcmProject $project Current project
     */
    protected function onTransition($session, $project)
    {
        $transition = $project->workflowManager->getWorkflowTransitionById(getArrayParameter($_REQUEST, '_wcmTransitionId', null));
        if (!$this->context->executeTransition($transition))
        {
            $message = $this->context->getErrorMsg();
            wcmMVC_Action::setError(_TRANSITION_FAILED . ': ' . $message);
        }
    }

    /**
     * onCreateVersion
     * Launched when a version must be created
     *
     * @param wcmSession $session Current session
     * @param wcmProject $project Current project
     */
    protected function onCreateVersion($session, $project)
    {
        // retrieve version comment
        $comment = strip_tags(getArrayParameter($_REQUEST, '_comment', null));
        
        // create version
        if ($this->context->archive($comment))
        {
            wcmMVC_Action::setMessage(_BIZ_VERSION_ADDED);
            // Redirect to default action page
            $this->redirect(self::computeObjectURL($this->context->getClass(), $this->context->id));
        }
        else
        {
            // display error message
            wcmMVC_Action::setError(_BIZ_VERSION_ADDED_FAILED);
        }
    }

    /* relaxnews update for object duplication */
    protected function onClone($session, $project, $request)
    {
    	$this->context->id = null;
    	$this->context->workflowState = 'draft';
    	$this->context->revisionNumber = null;
    	$request['id'] = null;
    	if ($this->context->save($request))
    		$this->redirect(self::computeObjectURL($this->context->getClass(), $this->context->id));
		else
			wcmMVC_Action::setError(_BIZ_ERROR . $this->context->getErrorMsg());            
    }
    
    /**
     * onRestoreVersion
     * Launched when a version must be restored
     *
     * @param wcmSession $session Current session
     * @param wcmProject $project Current project
     */
    protected function onRestoreVersion($session, $project)
    {
        // retrieve version id to restore
        $versionId = getArrayParameter($_REQUEST, '_versionId', 0);
        if ($versionId)
        {
            // restore version
            if ($this->context->restore($versionId))
            {
                // redirect to default action page
                wcmMVC_Action::setMessage(_BIZ_VERSION_RESTORED);
                $this->redirect(self::computeObjectURL($this->context->getClass(), $this->context->id));
            }
            else
            {
                // display error message
                wcmMVC_Action::setError(_BIZ_VERSION_RESTORE_FAILED . ': ' . $this->context->getErrorMsg());
            }
        }
    }

    /**
     * onRollackVersion
     * Launched when a version must be rolled-back
     *
     * @param wcmSession $session Current session
     * @param wcmProject $project Current project
     */
    protected function onRollbackVersion($session, $project)
    {
        // retrieve version id to rollback
        $versionId = getArrayParameter($_REQUEST, '_versionId', 0);
        if ($versionId)
        {
            // restore version
            if ($this->context->rollback($versionId))
            {
                // redirect to default action page
                wcmMVC_Action::setMessage(_BIZ_VERSION_ROLLEDBACK);
                $this->redirect(self::computeObjectURL($this->context->getClass(), $this->context->id));
            }
            else
            {
                // display error message
                wcmMVC_Action::setError(_BIZ_VERSION_ROLLBACK_FAILED . ': ' . $this->context->getErrorMsg());
            }
        }
    }
    
    /**
     * beforeSaving is called by onCheckin and onSave before storing the sysobject
     *
     * @param wcmSession $session Current session
     * @param wcmProject $project Current project
     */
    protected function beforeSaving($session, $project)
    {
    }
}
