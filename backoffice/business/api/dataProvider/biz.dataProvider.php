<?php
/**
 * Project:     WCM
 * File:        business/api/dataProvider/biz.dataProvider.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * Data provider for the different fieldsets of a search form.
 */
abstract class dataProvider
{
    /**
     * The associated project.
     *
     * #var wcmProject
     */
    public $project;

    /**
     * The associated data.
     *
     * @var array
     */
    public $data;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->project = wcmProject::getInstance();
        $this->data = array();      
    }

    /**
     * Gets and saves the associated data.
     *
     * @param array $parameters Request parameters
     * @param array $options    Request options
     */
    abstract public function getData($parameters, $options);

    /**
     * Renders the associated data by populating given parent and/or
     * children nodes.
     *
     * @param SimpleXMLElement $parent   The parent node
     * @param SimpleXMLElement $children The child nodes
     */
    abstract public function renderData(&$parent, &$children);
}
?>