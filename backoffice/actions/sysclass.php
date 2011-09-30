<?php
/**
 * Project:     WCM
 * File:        sysclass.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
 

/**
 * This class implements the action controller for the membership page
 */
class wcmSysclassAction extends wcmMVC_SysAction
{
    
    private function savePermissions($session, $project)
    {
        $permissions = (isset($_REQUEST['permissions'])) ? $_REQUEST['permissions'] : null;
        $this->context->updatePermissions(array('wcmSysclass'), $permissions);
    }
        
    /**
     * beforeSaving is called by onCheckin and onSave before storing the sysobject
     *
     * @param wcmSession $session Current session
     * @param wcmProject $project Current project
     */
    // @todo : check with Agostino if this should be in beforeSaving
    //         need to do a method similar to the updateChapters of articles
    protected function beforeSaving($session, $project)
    {
        parent::beforeSaving($session, $project);
        $this->savePermissions($session, $project);
    }
}
