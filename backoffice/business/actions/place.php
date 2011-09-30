<?php
/**
 * Project:     WCM
 * File:        place.php
 *
 * @copyright   (c)2011 Relaxnews
 * @version     4.x
 *
 */

 /**
 * This class implements the action controller for the photo
 */
class placeAction extends wcmMVC_BizAction
{
    /**
     * onSave
     *
     * @param wcmSession $session Current session
     * @param wcmProject $project Current project
     */
    protected function onSave($session, $project)
    {
        $this->beforeSaving($session, $project);
    	if (!$this->context->save($_REQUEST))
        {
            wcmMVC_Action::setError(_BIZ_ERROR.$this->context->getErrorMsg());
            return;
        }
        
        // Redirect to 'view' URL
        if ( isset ($_REQUEST['_redirect']))
        {
            $this->redirect(wcmModuleURL('business/subForms/createPlace',
            array ('uid'=>$_REQUEST['_redirect'], 'placeId' => $this->context->id)
            ));
        }
    }	
}
