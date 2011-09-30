<?php
/**
 * Project:     WCM
 * File:        business/api/dataProvider/biz.workflowOptionsProvider.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * Workflow options data provider
 */
class workflowOptionsProvider extends dataProvider
{
    /**
     * Gets and saves the options data.
     *
     * @param array $parameters Request parameters
     * @param array $options    Request options
     */
    function getData($parameters, $options)
    {
        $options = array();
        $options['_'] = '('._BIZ_ALL.')';

        $workflowManager = wcmProject::getInstance()->workflowManager;
        $className = getArrayParameter($options, 'className', null);
        if ($className)
            $workflowStates = $workflowManager->getWorkflowStatesByClassName($className);
        else
            $workflowStates = $workflowManager->getWorkflowStates();

        if ($workflowStates)
        {
            foreach ($workflowStates as $workflowState)
                $options[$workflowState->code] = getConst($workflowState->name);
        }

        $this->data['options'] = $options;
    }

    /**
     * Renders the options data by populating the given parent and/or
     * children nodes.
     *
     * @param SimpleXMLElement &$parent   The parent node
     * @param SimpleXMLElement &$children The children nodes
     */
    function renderData(&$parent, &$children)
    {
        $options = $this->data['options'];
        if ($options)
        {
            foreach ($options as $value => $caption)
            {
                $child = $parent->addChild("option", $caption);
                $child->addattribute("value", $value);
                $child->addattribute("style", "cursor: pointer;");
            }
        }
    }
}
?>