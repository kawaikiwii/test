<?php
/**
 * The wcmWorkflowTransition is used to describe and create workflows
 *
 * File:        wcm.WorkflowTransition.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
class wcmWorkflowTransition extends wcmSysobject
{
    /**
     * @var string  name of this workflow
     */
    public $name;
    /**
     * @var string  Code of this workflow
     */
    public $code;

    /**
     * @var string (unique) code of starting wcm_workflowState
     */
    public $fromState;

    /**
     * @var string (unique) code of ending wcm_workflowState
     */
    public $toState;

    /**
     * @var int $workflowId id of workflow this transition belongs too
     */
    public $workflowId;


    /**
     * The magic 'ANY' state (can be used for a fromState)
     */
    const ANY_STATE = '_ANY_STATE';

    /**
     * The magic 'SELF' state (can be used for a fromState or a toState)
     */
    const SELF_STATE = '_SELF_STATE';

    /**
     * Check validity of object
     *
     * @return boolean TRUE if object is valid
     */
    public function checkValidity()
    {
        if (!parent::checkValidity())
            return false;

        if (!$this->checkUniqueCode())
            return false;
        
        if ($this->workflowState  && strlen($this->workflowState) > 64)
        {
            $this->lastErrorMsg = _ERROR_WORKFLOWSTATE_TOO_LONG;
            return false;
        }
        
       if (trim($this->name . ' ') == '')
        {
            $this->lastErrorMsg = _ERROR_NAME_IS_MANDATORY;
            return false;
        }
            
        if (strlen($this->name) > 255)
        {
            $this->lastErrorMsg = _ERROR_NAME_TOO_LONG;
            return false;
        }
        
        if (trim($this->code . ' ') == '')
        {
            $this->lastErrorMsg = _ERROR_CODE_IS_MANDATORY;
            return false;
        }
        
        if (strlen($this->code) > 64)
        {
            $this->lastErrorMsg = _ERROR_CODE_TOO_LONG;
            return false;
        }
        
        if ($this->fromState  && strlen($this->fromState) > 64)
        {
            $this->lastErrorMsg = _ERROR_CODE_TOO_LONG;
            return false;
        }
        
        if ($this->toState  && strlen($this->toState) > 64)
        {
            $this->lastErrorMsg = _ERROR_CODE_TOO_LONG;
            return false;
        }    
        
        return true;
    }

    /**
     * Inserts or Updates object in database
     *
     * @param int   $userId Id of the wcmUser who is creating or updating the object
     *
     * @return boolean true on success, false on failure
     */
    protected function store($userId = null)
    {
        if (!parent::store($userId)) return false;

        // Update cache
        wcmCache::setElem($this->getClass(), $this->id, $this);

        return true;
    }

    /**
     * Deletes current object from database
     *
     * @return true on success or an error message (string)
     */
    public function delete()
    {
        if (!parent::delete())
            return false;

        // Update cache
        wcmCache::unsetElem($this->getClass(), $this->id);

        return true;
    }

    /**
     * Exposes 'fromState' to getAssocArray
     *
     * @param bool $toXML TRUE if method is called in the context of toXML()
     *
     * @return array The 'from' state getAssocArray (or null)
     */
    public function getAssoc_fromState($toXML = false)
    {
        $project = wcmProject::getInstance();
        $state = $project->workflowManager->getWorkflowStateByCode($this->fromState);
        return ($state == null) ? null : $state->getAssocArray($toXML);
    }

    /**
     * Exposes 'toState' to getAssocArray
     *
     * @param bool $toXML TRUE if method is called in the context of toXML()
     *
     * @return array The 'to' state getAssocArray (or null)
     */
    public function getAssoc_toState($toXML = false)
    {
        $project = wcmProject::getInstance();
        $state = $project->workflowManager->getWorkflowStateByCode($this->toState);
        return ($state == null) ? null : $state->getAssocArray($toXML);
    }
}
?>