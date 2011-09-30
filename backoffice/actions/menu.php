<?php
/**
 * Project:     WCM
 * File:        menu.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
 

/**
 * This class implements the action controller for the menu page
 */
class wcmMenuAction extends wcmMVC_SysAction
{
    /**
     * Instanciate context (usually a wcmObject)
     *
     * @param wcmSession $session Current session
     * @param wcmProject $project Current project
     */
    protected function setContext($session, $project)
    {
        parent::setContext($session, $project);

        // Set default parentId if given in $_REQUEST
        if ($this->context->id == 0)
        {
            $this->context->parentId = getArrayParameter($_REQUEST, 'parentId', null);
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
        // Believe it or not, 'action' cannot be assigned to an input in a form
        // or else you won't be able to update the form's action in javascript!!!!
        if (isset($_REQUEST['_action'])) $this->context->action = $_REQUEST['_action'];
    }
}