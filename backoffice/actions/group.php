<?php
/**
 * Project:     WCM
 * File:        membership.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
 

/**
 * This class implements the action controller for the membership page
 */
class wcmGroupAction extends wcmMVC_SysAction
{

    /**
     * Save permissions based on $_REQUEST
     */
    private function savePermissions($session, $project)
    {
        $permissionTypes = getArrayParameter($_REQUEST, 'permissionTypes');
        $permissions     = getArrayParameter($_REQUEST, 'permissions');
        $this->context->updatePermissions($permissionTypes, $permissions);
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

        // Save permissions
        $this->savePermissions($session, $project);

        // Remove obsolete users
        if ($this->context->id != wcmMembership::EVERYONE_GROUP_ID)
        {
            $users = array();
            if (isset($_REQUEST['_users']))
            {
                foreach ($_REQUEST['_users'] as $id => $active)
                {
                    if (!$active)
                        $this->context->removeMember($id);
                }
            }
        }
    }
}
