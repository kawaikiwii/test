<?php
/**
 * Project:     WCM
 * File:        business/actions/maintenance.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
 
/**
 * This class implements the action controller for the maintenance page
 */
class maintenanceAction extends wcmMVC_Action
{
    /**
     * Instanciate context
     *
     * @param wcmSession $session Current session
     * @param wcmProject $project Current project
     *
     * @return wcmLock Instanciated context
     */
    protected function setContext($session, $project)
    {
        return null;
    }

    /**
     * Default action
     *
     * @param wcmSession $session Current session
     * @param wcmProject $project Current project
     */
    protected function on($session, $project)
    {
    }

    /**
     * Clear trace files
     *
     * @param wcmSession $session Current session
     * @param wcmProject $project Current project
     */
    protected function onPurge($session, $project)
    {
        if (isset($_REQUEST['_purgeClasses']))
        {
            $lockedwarning = false;
            $purgeLocked = getArrayParameter($_REQUEST, '_purgeLocked', 0);
            
            // purge by class name
            foreach($_REQUEST['_purgeClasses'] as $className)
            {
                $bizobject = new $className;
                if ($bizobject->beginEnum("expirationDate < '".date('Y-m-d H:i:s')."'"))
                {
                    while($bizobject->nextEnum())
                    {
                    	//suppose object is unlocked
                        $locked = false;
                        //if the purge even locked object is not selected,
                        //make sure the object is not locked 
                    	if (!$purgeLocked)
                        {
                            // check if object is locked
                            $lock = $bizobject->getLockInfo();
                            if ($lock->userId != 0)
                            {
                                // remember that, at least, an expired object is locked
								$lockedwarning = true;
                                $locked = true;
                            }
                        }
                        //if the object is not locked or the purge even all is selected delete it
                        if(!$locked)
                        	$bizobject->delete();
                    }
                    $bizobject->endEnum();
                }
                unset($bizobject);
            }

            // display message            
            if ($lockedwarning)
            {
                wcmMVC_Action::setWarning(_BIZ_PURGE_DONE_BUT_WITH_LOCKED);
            }
            else
            {
                wcmMVC_Action::setMessage(_BIZ_PURGE_DONE);
            }
        }
        else
        {
            wcmMVC_Action::setMessage(_BIZ_PURGE_NOTHING_TO_PURGE);
        }
    }
}