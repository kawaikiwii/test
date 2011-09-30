<?php
/**
 * Project:     WCM
 * File:        wcm.IWorkflowScript.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * The wcmIWorkflowScript defines the methods required for any user
 * defined workflow script.
 */
interface wcmIWorkflowScript
{
    /**
     * Construct the workflow script
     *
     * @param wcmObject $wcmObject Related wcmObject
     */
    function __construct($wcmObject);

    /**
     * This callback is invoked BEFORE an object is created
     * If this function return false, the operation will be canceled!
     *
     * @return bool FALSE to cancel creation
     */
    public function beforeCreate();
    
    /**
     * This callback is invoked AFTER the object has been created
     */
    public function onCreate();
    
    /**
     * This callback is invoked BEFORE the object is updated
     * If this function return false, the operation will be canceled!
     *
     * @return bool FALSE to cancel creation
     */
    public function beforeUpdate();

    /**
     * This callback is invoked AFTER the object has been updated
     */
    public function onUpdate();
    
    /**
     * This callback is invoked AFTER the object has been deleted
     */
    public function beforeDelete();

    /**
     * This callback is invoked AFTER the object has been deleted
     */
    public function onDelete();

    /**
     * This callback is invoked BEFORE a generic transition is execute
     * and ONLY IF the dedicated function was not provided.
     *
     * For instance, when executing a transition with code 'Publish', WCM
     * will search for 'beforePublish()' method. If not found, beforeTransition()
     * will by called.
     *
     * @param wcmWorkflowTransition $transition The transition to execute
     *
     * @return bool FALSE to cancel transition
     */
    public function beforeTransition(wcmWorkflowTransition $transition);

    /**
     * This callback is invoked AFGTER a generic transition has been executed
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
     * @param wcmWorkflowTransition $transition The transition to execute
     */
    public function onTransition(wcmWorkflowTransition $transition);
}