<?php
/**
 * Project:     WCM
 * File:        moderationWorkflow.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

require_once(dirname(__FILE__) . '/base/wcmBaseWorkflowScript.php');

/**
 * This class is a basic moderation workflow script that
 * works in conjunction with the standard 'moderationWorkflow'
 * provided in WCM 4 demo web site.
 */
class moderationWorkflow extends wcmBaseWorkflowScript
{
    /**
     * This callback is invoked BEFORE the object is notified
     *
     * @return bool FALSE to cancel transition
     */
    public function beforeNotify()
    {
        wcmTrace('WF: Notifying ' . $this->wcmObject->getClass() . ' #' . $this->wcmObject->id);
        return true;
    }

    /**
     * This callback is invoked AFTER the object has been notified
     */
    public function onNotify()
    {
        wcmTrace('WF: ' . $this->wcmObject->getClass() . ' #' . $this->wcmObject->id . ' notified');
    }



    /**
     * This callback is invoked BEFORE the object is approved
     *
     * @return bool FALSE to cancel transition
     */
    public function beforeApprove()
    {
        wcmTrace('WF: Approving ' . $this->wcmObject->getClass() . ' #' . $this->wcmObject->id);
        return true;
    }

    /**
     * This callback is invoked AFTER the object has been approved
     */
    public function onApprove()
    {
        wcmTrace('WF: ' . $this->wcmObject->getClass() . ' #' . $this->wcmObject->id . ' approved');
    }



    /**
     * This callback is invoked BEFORE the object is rejected
     *
     * @return bool FALSE to cancel transition
     */
    public function beforeReject()
    {
        wcmTrace('WF: Rejecting ' . $this->wcmObject->getClass() . ' #' . $this->wcmObject->id);
        return true;
    }

    /**
     * This callback is invoked AFTER the object has been rejected
     */
    public function onReject()
    {
        wcmTrace('WF: ' . $this->wcmObject->getClass() . ' #' . $this->wcmObject->id . ' rejected');
    }
}