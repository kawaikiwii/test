<?php
/**
 * Project:     WCM
 * File:        wcm.workflowManager.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     3.2
 *
 */

/**
 * The wcmWorkflowManager class is an helper class used to manage workflows
 */
class wcmWorkflowManager
{
    /**
     * Returns the workflow corresponding to a specific id
     *
     * @param int $id Id of workflow to retrieve
     *
     * @return wcmWorkflow Corresponding workflow or NULL
     */
    public function getWorkflowById($id)
    {
        // Use cache
        return getArrayParameter($this->getWorkflows(), $id, null);
    }

    /**
     * Returns an array of workflows matching a specific where clause
     *
     * @param boolean $resetCache Whether to reset the cache, ie. reload from DB (default is false)
     *
     * @return An assoc array of {@link wcmWorkflows} objects (keys are ids)
     */
    public function getWorkflows($resetCache = false)
    {
        $cached = wcmCache::fetch('wcmWorkflow');
        if ($resetCache || $cached === FALSE)
        {
            $project = wcmProject::getInstance();
            $enum = new wcmWorkflow();

            if (!$enum->beginEnum())
            {
                $project->logger->logError('Workflow enumeration failed: ' . $enum->lastErroMsg);
                return null;
            }

            $cached = array();
            while ($enum->nextEnum())
            {
                $cached[$enum->id] = clone($enum);
            }
            $enum->endEnum();

            // Update cache
            wcmCache::store('wcmWorkflow', $cached);
            wcmCache::delete('wcmWorkflowTransition');
        }
        return $cached;
    }


    /**
     * Returns an array of wcmWorkflowStates matching a specific where clause
     *
     * @param boolean $resetCache Whether to reset the cache, ie. reload from DB (default is false)
     *
     * @return An assoc array of {@link wcmWorkflowStates} objects (keys are ids)
     */
    public function getWorkflowStates($resetCache = false)
    {
        $cached = wcmCache::fetch('wcmWorkflowState');
        if ($resetCache || $cached === FALSE)
        {
            $project = wcmProject::getInstance();
            $enum = new wcmWorkflowState($project);

            if (!$enum->beginEnum())
            {
                $project->logger->logError('Workflow state enumeration failed: ' . $enum->lastErroMsg);
                return null;
            }

            $cached = array();
            while ($enum->nextEnum())
            {
                $cached[$enum->code] = clone($enum);
            }
            $enum->endEnum();

            // Update cache
            wcmCache::store('wcmWorkflowState', $cached);
        }
        
        return $cached;
    }

    /**
     * Returns the workflow  state corresponding to a specific code
     *
     * @param string $code Code of workflow state to retrieve
     *
     * @return wcmWorkflowState Corresponding workflwo state or NULL
     */
    public function getWorkflowStateByCode($code)
    {
        // Use cache
        return getArrayParameter($this->getWorkflowStates(true), $code, null);
    }

    /**
     * Returns an array of wcmWorkflowTransitions matching a specific where clause
     *
     * @param boolean $resetCache Whether to reset the cache, ie. reload from DB (default is false)
     *
     * @return An assoc array of {@link wcmWorkflowTransitions} objects (keys are ids)
     */
    public function getTransitions($resetCache = false)
    {
        $cached = wcmCache::fetch('wcmWorkflowTransition');
        if ($resetCache || $cached === FALSE)
        {
            $project = wcmProject::getInstance();

            $enum = new wcmWorkflowTransition;
            if (!$enum->beginEnum())
            {
                $project->logger->logError('Workflow transition enumeration failed: ' . $enum->lastErroMsg);
                return null;
            }

            $cached = array();
            while ($enum->nextEnum())
            {
                $cached[$enum->code] = clone($enum);
            }
            $enum->endEnum();

            // Update cache
            wcmCache::store('wcmWorkflowTransition', $cached);
        }

        return $cached;
    }

    /**
     * Returns the workflow transition corresponding to a specific id
     *
     * @param int $id Id of workflow transition to retrieve
     *
     * @return wcmWorkflowTransition Corresponding workflow transition or NULL
     */
    public function getWorkflowTransitionById($id)
    {
        // Use cache (but cache is stored by code)
        foreach($this->getTransitions() as $code => $transition)
        {
            if ($transition->id === $id)
                return $transition;
        }

        return null;
    }

    /**
     * Returns the workflow transition corresponding to a specific code
     *
     * @param int $code Code of workflow transition to retrieve
     *
     * @return wcmWorkflowTransition Corresponding workflow transition or NULL
     */
    public function getWorkflowTransitionByCode($code)
    {
        // Use cache
        return getArrayParameter($this->getTransitions(), $code);
    }


    /**
     * Recursive method to get all possible states for a specific bizclass
     *
     * @param   string  $className
     * @param   string  $state
     *
     * @return  array   $statesArray    all workflow states (wcmWorkflowStates) for this bizClass
     */
    public function getWorkflowStatesByClassName($className, $state = null)
    {
        $project    = wcmProject::getInstance();
        $wkfManager = $project->workflowManager;

        if (!$state)
        {
            $bizClass = $project->bizlogic->getBizclassByClassName($className);
            $workflow = $wkfManager->getWorkflowById($bizClass->workflowId);
            $state = $workflow->initialState;
        }

        $statesArray = array($wkfManager->getWorkflowStateByCode($state));

        $enum = new wcmWorkflowTransition($project);
        if (!$enum->beginEnum('fromState="' . $state . '"'))
        {
            $project->logger->logError('Workflow transitions enumeration failed: ' . $enum->lastErroMsg);
            return null;
        }
        while ($enum->nextEnum())
        {
            $toState = $wkfManager->getWorkflowStateByCode($enum->toState);
            $statesArray[] = $toState;
            $statesArray = array_merge($statesArray, $wkfManager->getWorkflowStatesByClassName($className, $enum->toState));
        }
        $enum->endEnum();

        return $statesArray;
    }

    /**
     * Returns an array of wcmWorkflowScripts matching a specific where clause
     *
     * @param boolean $resetCache Whether to reset the cache, ie. reload from DB (default is false)
     *
     * @return An array of workflow scripts (file)
     */
    public function getWorkflowScripts($resetCache = false)
    {
        $workflowScripts = array();
        if ($resetCache || !isset($this->workflowScripts))
        {
            // Script location
            $path = WCM_DIR . '/business/workflow/';
            $scripts = array();
            if (is_dir($path))
            {
                if ($dh = opendir($path))
                {
                    $col  = 0;
                    while (($file = readdir($dh)) !== false)
                    {
                        if (substr($file, -4) == '.php')
                        {
                            $workflowScripts[] = $file;
                        }
                    }
                    closedir($dh);
                }
            }
        }

        return $workflowScripts;
    }
}