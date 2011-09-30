<?php
/**
 * Project:     WCM
 * File:        generation.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */
 

/**
 * This class implements the action controller for the generation page
 */
class wcmGenerationAction extends wcmMVC_SysAction
{
    /**
     * Instanciate context (usually a wcmObject)
     *
     * @param wcmSession $session Current session
     * @param wcmProject $project Current project
     */
    protected function setContext($session, $project)
    {
        parent::setContext($session, $project);

        // Set default generationSetId if given in $_REQUEST
        if ($this->context->id == 0)
        {
            $this->context->generationSetId = getArrayParameter($_REQUEST, 'generationSetId', null);
        }
    }
}