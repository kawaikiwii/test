<?php
/**
 * Project:     WCM
 * File:        templateCategory.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
 

/**
 * This class implements the action controller for the templates
 */
class wcmTemplateCategoryAction extends wcmMVC_SysAction
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
        $this->id = getArrayParameter($_REQUEST, 'id', null);
        return parent::setContext($session, $project);
    }
}