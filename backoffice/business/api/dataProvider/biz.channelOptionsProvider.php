<?php
/**
 * Project:     WCM
 * File:        business/api/dataProvider/biz.channelOptionsProvider.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * Channel options data provider
 */
class channelOptionsProvider extends dataProvider
{
    /**
     * Gets and saves the channel options data.
     *
     * @param array $parameters Request parameters
     * @param array $options    Request options
     */
    function getData($parameters, $options)
    {
        $options = array();
        $options['_'] = '('._BIZ_ALL.')';

        $where = 'parentId is null';
        if (!getArrayParameter($options, 'multisite'))
            $where = 'siteId = '.$_SESSION['siteId'].' AND '.$where;

        $channels = bizobject::getBizobjects('channel', $where, "rank");
        if ($channels)
        {
            $excludeId  = getArrayParameter($options, 'excludeId');

            foreach ($channels as $channel)
            {
                if ($channel->id != $excludeId)
                    $options[textH8($channel->id)] = textH8($channel->title)." (id:".$channel->id.")";
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