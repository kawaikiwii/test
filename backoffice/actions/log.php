<?php
/**
 * Project:     WCM
 * File:        log.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
 

/**
 * This class implements the action controller for the log page
 */
class wcmLogAction extends wcmMVC_Action
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
    protected function onClearTrace($session, $project)
    {
        $path = wcmGetTraceFolder();
        eraseDirectory($path);
        mkdir($path, 0777, true);

        wcmMVC_Action::setMessage(_TRACE_LOG_ERASED);
    }
}