<?php
/**
 * Project:     WCM
 * File:        collection.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
 
/**
 * This class implements the action controller for the collection
 */
class collectionAction extends wcmMVC_BizAction
{
    /**
     * Save last edited design zones
     */
    private function saveZones()
    {
        // Retrieve new design zones?
        $zones = getArrayParameter($_REQUEST, '_zones', null);
        $this->context->updateZones($zones);
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

        $this->saveZones();
    }
}
