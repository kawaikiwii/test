<?php
/**
 * Project:     WCM
 * File:        business/api/dataProvider/biz.bizobjectOptionsProvider.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * Business object options data provider
 */
class bizobjectOptionsProvider extends dataProvider
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

        $bizClasses = wcmProject::getInstance()->bizlogic->getBizclasses();
        if ($bizClasses)
        {
            $session = wcmSession::getInstance();
            $excludedBizClassNames = array('chapter', 'pollChoice');

            foreach ($bizClasses as $bizClass)
            {
                $className = $bizClass->className;
                if (!in_array($className, $excludedBizClassNames)
                    && $session->isAllowed($bizClass, wcmPermission::P_READ))
                {
                    $options[textH8(getConst($className))] =
                        textH8(getConst($bizClass->name))." (id:".$bizClass->id.")";
                }
            }
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