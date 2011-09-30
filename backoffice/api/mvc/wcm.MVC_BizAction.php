<?php

/**
 * Project:     WCM
 * File:        wcm.MVC_SysAction.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */


/**
 * This class implements the default business action for the
 * MVC controller
 *
 * Basically it instanciate the bizobject matching $_REQUEST['_wcmClass']
 * and execute the specific action according to $_REQUEST['_wcmTodo']
 */
class wcmMVC_BizAction extends wcmMVC_SysAction
{
    /**
     * Instanciate context (usually a sysobject)
     *
     * @param wcmSession $session Current session
     * @param wcmProject $project Current project
     */
    protected function setContext($session, $project)
    {
        parent::setContext($session, $project);

        $context = $this->context;

        // Initialize site id
        if($context && property_exists($context, 'siteId'))
        {
            if ($context->siteId == null)
            {
                $context->siteId = $session->getSiteId();
            }

            // check site permission
            if (!$session->isAllowed('site_'.$context->siteId, wcmPermission::P_READ))
            {
                $this->redirect('?_wcmAction=403');
            }
        }

        // Initialize tags and semanticData
        unset($_SESSION['tags']);
        unset($_SESSION['semanticData']);
        if ($context)
        {
            $_SESSION['tags'] = $context->xmlTags;
            $_SESSION['semanticData'] = $context->semanticData;
        }
    }

    /**
     * onCheckin
     *
     * @param wcmSession $session Current session
     * @param wcmProject $project Current project
     */
    protected function onCheckin($session, $project)
    {
        parent::onCheckin($session, $project);
    }

    /**
     * onSave
     *
     * @param wcmSession $session Current session
     * @param wcmProject $project Current project
     */
    protected function onSave($session, $project)
    {
        parent::onSave($session, $project);
    }

    /**
     * onDelete
     *
     * @param wcmSession $session Current session
     * @param wcmProject $project Current project
     */
    protected function onDelete($session, $project)
    {
        unset($_SESSION['tags']);
        unset($_SESSION['semanticData']);

        parent::onDelete($session, $project);
    }

    /**
     * beforeSaving is called by onCheckin and onSave before storing the sysobject
     *
     * @param wcmSession $session Current session
     * @param wcmProject $project Current project
     */
    protected function beforeSaving($session, $project)
    {
        parent::beforeSaving($session, $project);

        $this->updateTags();
        $this->updateRelations();
        $this->updateSemanticData();
    }

    /**
     * onPublish
     *
     * @param wcmSession $session Current session
     * @param wcmProject $project Current project
     */
    protected function onPublish($session, $project)
    {
        if ($this->context && method_exists($this->context, 'generate'))
        {
            set_time_limit(3600);
            $logger = new wcmLogger(false, false, null, false);
            $this->context->generate(true, $logger);
            $html = "<div class='header'> " . _BIZ_GENERATION_RESULT ."</div>";
            $html .= '<ul>' . $logger->display(true) . '</ul>';
            unset($logger);

            // Add statistics
            $session->addStat(wcmSession::STAT_PUBLISH_OBJECT, $this->context, $html);

            // Send message
            wcmMVC_Action::setMessage('Object has been published');
        }
    }

    /**
     * Update tags
     */
    private function updateTags()
    {
        // Retrieve tags from session
        if (isset($_REQUEST['_xmlTags']))
        {
            foreach ($_REQUEST['_xmlTags'] as $type => $tags)
            {
                $this->context->xmlTags[$type] = explode('|', $tags);
            }
        }
    }

    /**
     * Update bizrelations
     */
    private function updateRelations()
    {
        // If we have a list save it in the bizobject
        if($lists = getArrayParameter($_REQUEST, '_list'))
        {
            foreach($lists as $pk)
                    $this->context->updateBizRelations($pk, getArrayParameter($_REQUEST, '_list'. $pk, array()));
        }
    }
    
    /**
     * Update semantic data
     */
     private function updateSemanticData()
     {
        if (isset($_REQUEST['_semanticData']))
        {
            if (!$this->context->semanticData)
                $this->context->semanticData = new wcmSemanticData();

            $this->context->semanticData->updateFromAssocArray($_REQUEST['_semanticData']);
        }
     }
}
