<?php

/**
 * Project:     WCM
 * File:        wcmWebServiceFacetValue.class.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

/**
 * A facet value that can be used in a service method parameter or
 * return value.
 */
class wcmWebServiceFacetValue
{
    /**
     * The facet name.
     *
     * @var string
     */
    public $name;

    /**
     * The facet value per se.
     *
     * @var string
     */
    public $value;

    /**
     * The human-readable representation of the facet value.
     *
     * @var string
     */
    public $title;

    /**
     * The facet value index, ie., the index from which to get the facet values.
     *
     * @var string
     */
    public $index;

    /**
     * The facet search index, ie., the index with which to perform a
     * search on a particular facet value.
     *
     * @var string
     */
    public $searchIndex;

    /**
     * The occurence count of the facet value.
     *
     * @var integer
     */
    public $count;
}

?>