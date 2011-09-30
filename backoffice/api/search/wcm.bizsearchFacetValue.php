<?php
/**
 * Project:     WCM
 * File:        api/search/wcm.bizsearchFacetValue.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * Represents a search facet value.
 *
 */
class wcmBizsearchFacetValue
{
    /**
     * Information about available facets.
     *
     * @see self::getFacetInfos()
     * @var array
     */
    protected static $facetInfos = null;

    /**
     * Gets information about available facets.
     *
     * Information about individual facets are returned in
     * wcmBizsearchFacetValue objects with empty value and text
     * properties.
     *
     * @return array Assoc. array of: facet name => wcmBizsearchFacetValue
     */
    public static function getFacetInfos()
    {
        if (self::$facetInfos === null)
        {
            self::$facetInfos = array();

            $config = wcmConfig::getInstance();
            for ($offset = 0; ; $offset++)
            {
                $key = 'wcm.search.facets.facet.' . $offset;
                if (!isset($config[$key . '.name']))
                    break;

                // Lowercase facet name et al. to allow case-insensitivity
                $name = strtolower($config[$key . '.name']);
                $index = strtolower($config[$key . '.index']);
                $searchIndex = strtolower($config[$key . '.searchIndex']);

                self::$facetInfos[$name] =
                    new wcmBizsearchFacetValue($name, null, null, $index, $searchIndex);
            }
        }

        return self::$facetInfos;
    }

    /**
     * Gets the raw facet values (ie., without any occurence count)
     * for a given facet.
     *
     * This function only supports the following facets:
     *
     * - classname
     * - channelid
     * - modifiedat
     *
     * @param string $facet The facet name
     *
     * @return array Assoc of raw facet value => wcmBizsearchFacetValue
     */
    public static function getFacetValues($facet)
    {
        // Facet names are case-insensitive
        $facet = strtolower($facet);

        $facetValues = array();

        $facetInfos = self::getFacetInfos();
        if (isset($facetInfos[$facet]))
        {
            $facetInfo = $facetInfos[$facet];
            $index = $facetInfo->index;
            $searchIndex = $facetInfo->searchIndex;

            switch ($facet)
            {
            case 'classname':
                $bizlogic = new wcmBizlogic();
                $bizclasses = $bizlogic->getBizclasses();
                if ($bizclasses)
                {
                    foreach ($bizclasses as $className => $bizclass)
                    {
                        $facetValues[$facet.':'.$className] =
                            new wcmBizsearchFacetValue($facet, $className,
                                                     getConst($bizclass->name), $index, $searchIndex);
                    }
                }
                break;

            case 'channelid':
                $bizobjects = bizobject::getBizobjects('channel');
                if ($bizobjects)
                {
                    foreach ($bizobjects as $bizobject)
                    {
                        $facetValues[$facet.':'.$bizobject->id] =
                            new wcmBizsearchFacetValue($facet, $bizobject->id,
                                                     $bizobject->title, $index, $searchIndex);
                    }
                }
                break;

            case 'modifiedat':
                $today = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
                $facetValues = array(
                    $facet.':'.$today => new wcmBizsearchFacetValue($facet, $today, getConst(_BIZ_TODAY), $index, $searchIndex),
                    $facet.':'.-1 => new wcmBizsearchFacetValue($facet, -1, getConst(_BIZ_YESTERDAY), $index, $searchIndex),
                    $facet.':'.-7 => new wcmBizsearchFacetValue($facet, -7, getConst(_BIZ_LAST_7_DAYS), $index, $searchIndex),
                    $facet.':'.-30 => new wcmBizsearchFacetValue($facet, -30, getConst(_BIZ_LAST_MONTH), $index, $searchIndex),
                    $facet.':'.-90 => new wcmBizsearchFacetValue($facet, -90, getConst(_BIZ_LAST_3MONTHS), $index, $searchIndex),
                    $facet.':'.-365 => new wcmBizsearchFacetValue($facet, -365, getConst(_BIZ_LAST_YEAR), $index, $searchIndex),
                    );
                break;
            }
        }

        return $facetValues;
    }

    /**
     * Compares two facet values in decreasing order of count.
     *
     * @param wcmBizsearchFacetValue $facetValue1 The first facet value
     * @param wcmBizsearchFacetValue $facetValue2 The second facet value
     *
     * @return int The difference $facetValue2->count - $facetValue1->count
     */
    public static function compareFacetValuesByCount($facetValue1, $facetValue2)
    {
        return $facetValue2->count - $facetValue1->count;
    }

    /**
     * The facet name.
     *
     * @var string
     */
    public $name;

    /**
     * The facet value per se.
     *
     * @var mixed
     */
    public $value;

    /**
     * The human-readable representation of the facet value.
     *
     * @var string
     */
    public $text;

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

    /**
     * Constructor
     *
     * @param string      $name        The facet name
     * @param mixed       $value       The facet value per se
     * @param string|null $text       The human-readable text (default is $value)
     * @param string|null $index       The facet value index (default is $name)
     * @param string|null $searchIndex The facet search index (default is $index or its default)
     * @param int|0       $count       The facet value occurence count (default is 0)
     */
    public function __construct($name, $value, $text = null,
                                $index = null, $searchIndex = null, $count = 0)
    {
        $this->name = $name;
        $this->value = $value;
        $this->text = $text ? $text : $this->value;
        $this->index = $index ? $index : $this->name;
        $this->searchIndex = $searchIndex ? $searchIndex : $this->index;
        $this->count = $count;

        // Small hack: the text of a "category" facet should not
        // include the IPTC code
        if ($this->name == 'category')
        {
            $parts = explode(' - ', $this->text, 2);
            if (count($parts) == 2)
            {
                $this->text = $parts[1];
            }
        }
    }
}
?>