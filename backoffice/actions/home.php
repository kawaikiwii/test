<?php
/**
 * Project:     WCM
 * File:        home.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
 

/**
 * This class implements the action controller for the home page
 */
class wcmHomeAction extends wcmMVC_Action
{
    /**
     * Instanciate context
     *
     * @param wcmSession $session Current session
     * @param wcmProject $project Current project
     *
     * @return wcmcontext Instanciated context
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
}