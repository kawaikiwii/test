<?php
/**
 * The wcmWorkflowState is used to describe and create workflows
 *
 * File:        wcm.WorkflowState.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
class wcmWorkflowState extends wcmSysobject
{
    /**
     * @var string  name of this workflow
     */
    public $name;

    /**
     * @var string
     */
    public $code;

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
     * CheckIn for workflow states
     * As code must be unique, validates that it doesn't exist before storing.
     *
     * @param array $source An assoc array for binding to class vars (or null)
     *
     * @return true on success, false otherwise
     */
    public function checkin($source = null)
    {
        $wkfManager = wcmSession::getInstance()->getProject()->workflowManager;
        if ($wkfManager->getWorkflowStateByCode($source['code']) != null)
        {
            $this->lastErrorMsg = 'The code "' . $source['code'] . '" is already used. Please choose another.';
            return false;
        }
        if (!parent::checkin($source))
            return false;
        return true;
    }

    /**
     * Deletes current object from database
     *
     * @return true on success or an error message (string)
     */
    public function delete()
    {
        $sql = 'SELECT count(*) FROM #__workflow_transition WHERE fromState=? OR toState=?';
        $count = $this->database->executeScalar($sql, array($this->code, $this->code));
        if ($count > 0)
        {
            $this->lastErrorMsg = 'This state (' . $this->name . ') is used in a transition and cannot be deleted';
            return false;
        }

        if (!parent::delete())
            return false;

        // Update cache
        wcmCache::unsetElem($this->getClass(), $this->id);

        return true;
    }
}