<?php
/**
 * Project:     WCM
 * File:        lock.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
 

/**
 * This class implements the action controller for the lock page
 */
class wcmLockAction extends wcmMVC_Action
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
        return new wcmLock();
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
     * onUnlock
     *
     * @param wcmSession $session Current session
     * @param wcmProject $project Current project
     */
    protected function onUnlock($session, $project)
    {
        $lock = $this->context;
        
        if ($lock)
        {
            $byClass = getArrayParameter($_REQUEST, 'byClass', null);
            $byUser = getArrayParameter($_REQUEST, 'byUser', null);
            $byId = getArrayParameter($_REQUEST, 'byId', null);

            if ($byId)
            {
                $lock->objectClass = $byClass;
                $lock->objectId = $byId;
                $lock->delete();
            }
            elseif ($byClass)
            {
                $lock->deleteByClass($byClass);
            }
            elseif ($byUser)
            {
                $lock->deleteByClass($byUser);
            }
        }
    }
}