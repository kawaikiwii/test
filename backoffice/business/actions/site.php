<?php
/**
 * Project:     WCM
 * File:        site.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
 
/**
 * This class implements the action controller for the site
 */
class siteAction extends wcmMVC_BizAction
{
    /**
     * Save permissions based on $_REQUEST
     */
    private function savePermissions()
    {
        $permissionTypes = getArrayParameter($_REQUEST, 'permissionTypes');
        $permissions     = getArrayParameter($_REQUEST, 'permissions');
        $this->context->updatePermissions($permissionTypes, $permissions);
    }
    
    /**
     * 
     */
    private function saveServices()
    {
    	$servicesList = getArrayParameter($_REQUEST, 'classList');
    	if ($this->context->services != '')
    		$this->context->services = '';
    	if (!empty($servicesList))
    	{	
    	foreach($servicesList as $service)
    		$this->context->services  .= $service.'|';
    	}
    }

    /**
     * is called on checkin and on save before the store
     *
     * @param wcmSession $session Current session
     * @param wcmProject $project Current project
     */
    protected function beforeSaving($session, $project)
    {
        parent::beforeSaving($session, $project);

        $this->savePermissions();
        $this->saveServices();
    }
}
