<?php
/**
 * Project:     WCM
 * File:        business/api/dataProvider/biz.siteOptionsProvider.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * Site options data provider
 */
class siteOptionsProvider extends dataProvider
{
    /**
     * Gets and saves the options data.
     *
     * @param array $parameters Request parameters
     * @param array $options    Request options
     */
    function getData($searchParameters, $parameters)
    {
        $options = array();
        $options['_'] = '('._BIZ_ALL.')';

        $where   = getArrayParameter($parameters, 'where', null);
        $orderBy = getArrayParameter($parameters, 'orderBy', null);

        $sites = bizobject::getBizobjects('site', $where, $orderBy);
        if ($sites)
        {
            $session = wcmSession::getInstance();
            foreach ($sites as $site)
            {
                if ($session->isAllowed($site, wcmPermission::P_READ))
                    $options[$site->id] = textH8($site->title)." (id:".$site->id.")";
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
            foreach($options as $value => $caption)
            {
                $child = $parent->addChild("option", $caption);
                $child->addattribute("value", $value);
                $child->addattribute("style", "cursor: pointer;");
            }
        }
    }
}
?>