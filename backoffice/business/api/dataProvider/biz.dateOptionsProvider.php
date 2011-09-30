<?php
/**
 * Project:     WCM
 * File:        business/api/dataProvider/biz.dateOptionsProvider.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * Date options data provider
 */
class dateOptionsProvider extends dataProvider
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
        $options["_"] = '('._BIZ_ALL_DATES.')';

        // Don't use array_merge or you'll mess up numeric keys
        $this->data['options'] = $options + getDateList();
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
	
	static function fieldDateToArray($date)
	{
		$dateFormat = array('hour' => substr($date, 11, 2),
							'minute' => substr($date, 14, 2),
							'second' => substr($date, 17, 2),
							'month' => substr($date, 5, 2),
							'day' => substr($date, 8, 2),
							'year' => substr($date, 0, 4));
											
		$dateFormat['mktime'] = mktime($dateFormat['hour'], $dateFormat['minute'], $dateFormat['second'], $dateFormat['month'], $dateFormat['day'], $dateFormat['year']);

		return $dateFormat;
	}
}
?>
