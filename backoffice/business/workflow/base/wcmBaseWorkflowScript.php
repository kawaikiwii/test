<?php
/**
 * Project:     WCM
 * File:        wcmBaseWorkflowScript.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * This abstract class is a default base implementation
 * of a workflow script.
 *
 * It can be used as either a sample of a base class for
 * specific implementation.
 */
abstract class wcmBaseWorkflowScript implements wcmIWorkflowScript
{
    /**
     * Related wcmObject
     */
    protected $wcmObject;

    /**
     * Construct the workflow script
     *
     * @param wcmObject $wcmObject Related wcmObject
     */
    public function __construct($wcmObject)
    {
        $this->wcmObject = $wcmObject;    
    }

    /**
     * This callback is invoked BEFORE an object is created
     * If this function return false, the operation will be canceled!
     *
     * @return bool FALSE to cancel creation
     */
    public function beforeCreate()
    {
        wcmTrace('WF: Creating ' . $this->wcmObject->getClass());
        return true;
    }
    
    /**
     * This callback is invoked AFTER the object has been created
     */
    public function onCreate()
    {
        wcmTrace('WF: ' . $this->wcmObject->getClass() . ' #' . $this->wcmObject->id . ' created');
    }
    
    /**
     * This callback is invoked BEFORE the object is updated
     * If this function return false, the operation will be canceled!
     *
     * @return bool FALSE to cancel creation
     */
    public function beforeUpdate()
    {
        wcmTrace('WF: Updating ' . $this->wcmObject->getClass() . ' #' . $this->wcmObject->id);
        return true;
    }

    /**
     * This callback is invoked AFTER the object has been updated
     */
    public function onUpdate()
    {
        wcmTrace('WF: ' . $this->wcmObject->getClass() . ' #' . $this->wcmObject->id . ' updated');
    }
    

    /**
     * This callback is invoked BEFORE the object has been deleted
     */
    public function beforeDelete()
    {
        wcmTrace('WF: Deleting ' . $this->wcmObject->getClass() . ' #' . $this->wcmObject->id);
        return true;
    }

    /**
     * This callback is invoked AFTER the object has been deleted
     */
    public function onDelete()
    {
        wcmTrace('WF: ' . $this->wcmObject->getClass() . ' #' . $this->wcmObject->id . ' deleted');
    }

    /**
     * This callback is invoked BEFORE a generic transition is execute
     * and ONLY IF the dedicated function was not provided.
     *
     * For instance, when executing a transition with code 'Publish', WCM
     * will search for 'beforePublish()' method. If not found, beforeTransition()
     * will by called.
     *
     * @param wcmTransition $transition The transition to execute
     *
     * @return bool FALSE to cancel transition
     */
    public function beforeTransition(wcmWorkflowTransition $transition)
    {
        wcmTrace('WF: Executing transition ' . getConst($transition->name) . ' on ' .
                  $this->wcmObject->getClass() . ' #' . $this->wcmObject->id);

        return true;
    }

    /**
     * This callback is invoked AFTER a generic transition has been executed
     * (means that the state has been changed) but BEFORE the object has been saved.
     * Also, the callback will be execute ONLY IF the dedicated function was not provided.
     *
     * For instance, when executing a transition with code 'Publish', WCM
     * will search for 'onPublish()' method. If not found, onTransition()
     * will by called.
     *
     * IMPORTANT: if this method raise an exception the transition will be canceled,
     * and the object will returned to its previous state without beeing saved.
     *
     * @param wcmTransition $transition The transition to execute
     */
    public function onTransition(wcmWorkflowTransition $transition)
    {
        wcmTrace('WF: Transition ' . getConst($transition->name) . ' executed on ' .
                  $this->wcmObject->getClass() . ' #' . $this->wcmObject->id);
    }
}