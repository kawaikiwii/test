<?php
/**
 * The wcmWorkflow is used to describe and create workflows
 *
 * File:        wcm.workflow.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     3.2
 *
 */
class wcmWorkflow extends wcmSysobject
{
    /**
     * @var string  name of this workflow
     */
    public $name;

    /**
     * @var string (unique) code of initial wcm_workflowState
     */
    public $initialState;

    /**
     * @var string  name of file containing script
     */
    public $script;
    
 	/**
     * Check validity of object
     *
     * @return boolean TRUE if object is valid
     */
    public function checkValidity()
    {
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
        
        if ($this->initialState  && strlen($this->initialState) > 64)
        {
            $this->lastErrorMsg = _ERROR_INITIALSTATE_TOO_LONG;
            return false;
        }
        
        if ($this->script  && strlen($this->script) > 255)
        {
            $this->lastErrorMsg = _ERROR_SCRIPT_TOO_LONG;
            return false;
        }    
        
        return true;
    }

    /**
     * Inserts or Updates object in database
     *
     * @return boolean true on success, false on failure
     */
    protected function store()
    {
        if (!parent::store()) return false;

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

        // Delete related transitions
        foreach(wcmProject::getInstance()->workflowManager->getTransitions() as $transition)
        {
            if ($transition->workflowId == $this->id)
                $transition->delete();
        }
        return true;
    }
}