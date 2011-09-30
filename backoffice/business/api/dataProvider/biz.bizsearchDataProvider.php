<?php
/**
 * Project:     WCM
 * File:        business/api/dataProvider/biz.bizsearchDataProvider.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * Business search data provider
 */
class bizsearchDataProvider extends dataProvider
{
    /**
     * Gets and saves the data corresponding to the current business
     * search.
     *
     * @param array $parameters Search parameters
     * @param array $options    Search options
     *
     */
    public function getData($parameters, $options)
    {
        $defaultParamPrefix        = wcmBizsearchConfig::getInstance()->getDefaultParameterPrefix();
        $this->data['paramPrefix'] = getArrayParameter($options, 'paramPrefix', $defaultParamPrefix);
        $this->data['query']       = getArrayParameter($parameters, 'query', '');
        $this->data['baseQuery']   = getArrayParameter($parameters, 'basequery', '');
    }

    /**
     * Renders the saved search data by populating given parent and/or
     * children nodes.
     *
     * @param SimpleXMLElement &$parent   The parent node
     * @param SimpleXMLElement &$children The list of child nodes
     *
     */
    public function renderData(&$parent, &$children)
    {
        $queryId = $this->data['paramPrefix'].'query';
        $query = htmlspecialchars($this->data['query']);
        
        $globalTag = new SimpleXMLElement('<div/>');
    
        if ($this->data['baseQuery'])
        {
            $baseQueryId = $this->data['paramPrefix'].'basequery';
            $baseQuery = htmlspecialchars($this->data['baseQuery']);

            $child = $parent->addChild('input');
            $child->addAttribute('type', 'hidden');
            $child->addAttribute('id', $baseQueryId);
            $child->addAttribute('name', $baseQueryId);
            $child->addAttribute('value', $baseQuery);

            $ul = $globalTag->addChild('ul');
            $ul->addAttribute('class', 'tags');
            $js = '$(\'wcmLi' . $baseQueryId .'\').remove(); $(\'' . $baseQueryId . '\').setValue(\'\');';
            $li = $ul->addChild('li');
            $li->addAttribute('id', 'wcmLi' . $baseQueryId);
            $a  = $li->addChild('a', $this->data['baseQuery']);
            $a->addAttribute('onclick', $js);
            $a->addAttribute('href', '#');
        }
        
        $child = $globalTag->addChild('input');
        $child->addAttribute('type', 'text');
        $child->addAttribute('id', $queryId);
        $child->addAttribute('name', $queryId);
        if ($query)
        {
        	$child->addAttribute('value', $query);
        }
        
        //$globalTag->addChild()
        $children = $globalTag;

        $js = "if (event.keyCode == Event.KEY_RETURN && $('ajaxRequest').value == false) event.stop()";
        $child->addAttribute('onkeypress', $js);

    }
}
?>