<?php
/**
 * Project:     WCM
 * File:        api/search/plugins/solr/wcm.bizsearchPluginSolr.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * The wcmBizsearchPluginSolr class implements a Solr-oriented
 * full-text search on bizobjects.
 */
class wcmBizsearchPluginSolr extends wcmBizsearchPluginAbstract
{
    /**
     * The Solr date format.
     */
    const SOLR_DATE_FORMAT = 'Y-m-d';

    /**
     * The Solr time format.
     */
    const SOLR_TIME_FORMAT = 'H:i:s';

    /**
     * The Solr date-time format.
     */
    const SOLR_DATETIME_FORMAT = 'Y-m-d\TH:i:s\Z';

    /**
     * Regexp matching a Lucene query term field.
     */
    private static $queryTermFieldRegExp = null;

    /**
     * Regexp matching a Lucene query term date.
     */
    private static $queryTermDateRegExp = null;

    /**
     * The Solr service URL.
     * @var string
     */
    protected $solrServiceURL = null;

    /**
     * The Solr service instance.
     * @var Apache_Solr_Service
     */
    protected $solrService = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        try
        {
            $config = wcmConfig::getInstance();
            $solrServiceURL = $config['solr.service'];
            if (!$solrServiceURL)
                throw new Exception(sprintf(_INVALID_SEARCH_SERVICE, $solrServiceURL));

            $solrServiceURLParts = @parse_url($solrServiceURL);
            if ($solrServiceURL === false)
                throw new Exception(sprintf(_INVALID_SEARCH_SERVICE, $solrServiceURL));

            $this->solrServiceURL = $solrServiceURL;
            $this->solrService = new Apache_Solr_Service($solrServiceURLParts['host'],
                                                         $solrServiceURLParts['port'],
                                                         $solrServiceURLParts['path']);
        }
        catch (Exception $error)
        {
            $this->project->logger->logError(get_class() . ': ' . $error->getMessage());
            throw $error;
        }
    }

    // INDEXING

    /**
     * Indexes a business object.
     *
     * @param wcmBizobject $bizobject The business object to index
     *
     * @return bool True on success, false on failure
     */
    public function indexBizobject(wcmBizobject $bizobject)
    {
        // Ignore empty bizobject
        if (!is_object($bizobject) || !$bizobject->id)
            return true;

        $className = get_class($bizobject);
        $id = $bizobject->id;

        try
        {
            // Get the XML represention of the bizobject and parse it
            $objectXml = $bizobject->toXML();
            $simpleXml = simplexml_load_string($objectXml);
            if ($simpleXml === false)
                throw new Exception(sprintf(_BIZ_INVALID_OBJECT_XML, $className, $id));

            // Create a Solr document and initialize its properties
            $document = new Apache_Solr_Document;
            $document->solrDocumentId = $className . '_' . $id;
            $document->objectXml = $objectXml;

            foreach (self::$indexes as $name => $index)
            {
                $values = array();

                $xpaths = $index['xpaths'];
                if ($xpaths)
                {
                    foreach ($xpaths as $xpath)
                    {
                        $nodes = $simpleXml->xpath('/' . $className . $xpath);
                        if ($nodes)
                        {
                            foreach ($nodes as $node)
                            {
                                $value = strval($node);
                                if ($value)
                                {
                                    if ($index['kind'] == 'date')
                                    {
                                        $dateTime = new DateTime($value);
                                        $value = $dateTime->format(self::SOLR_DATETIME_FORMAT);
                                    }

                                    if ($index['multivalued'])
                                        $document->setMultiValue($name, $value);
                                    else
                                        $values[] = $value;
                                }
                            }
                        }
                    }
                }

                if ($values)
                    $document->$name = $values[0];
            }

            // Index the Solr document
            $solrService = $this->getSolrService();
            $solrService->addDocument($document);
            $solrService->commit();
        }
        catch (Exception $error)
        {
            $this->lastErrorMsg = $error->getMessage();
            $this->project->logger->logError(get_class() . ': ' . $this->lastErrorMsg);
            return false;
        }

        return true;
    }

    /**
     * Deindexes a business object.
     *
     * @param wcmBizobject $bizobject The business object to deindex
     *
     * @return bool True on success, false on failure
     */
    public function deindexBizobject(wcmBizobject $bizobject)
    {
        // Ignore empty bizobject
        if (!is_object($bizobject) || !$bizobject->id)
            return true;

        try
        {
            $this->getSolrService()->deleteById(get_class($bizobject) . '_' . $bizobject->id);
        }
        catch (Exception $error)
        {
            $this->lastErrorMsg = $error->getMessage();
            $this->project->logger->logError(get_class() . ': ' . $this->lastErrorMsg);
            return false;
        }

        return true;
    }

    /**
     * Deindexes all business objects of a given class.
     *
     * @param string $className The class name of business objects to deindex
     *
     * @return bool True on success, false on failure
     */
    public function deindexBizobjects($className)
    {
        try
        {
            $this->getSolrService()->deleteByQuery('classname:' . $className);
        }
        catch (Exception $error)
        {
            $this->lastErrorMsg = $error->getMessage();
            $this->project->logger->logError(get_class() . ': ' . $this->lastErrorMsg);
            return false;
        }

        return true;
    }

    /**
     * Gets the count of indexed documents corresponding to business
     * objects of a given class.
     *
     * @param string|null $className The class name of business objects to search
     *                                (default is null for any class)
     *
     * @return int|null The count of documents, or null on failure
     */
    public function getCount($className = null)
    {
        if (!$className)
            $className = '[* TO *]';

        return $this->initSearch(uniqid(), 'classname:' . wcmBizsearch::escapeQuery($className));
    }

    // SEARCHING

    /**
     * Initiates a new search given a unique ID, a query, and sorting
     * criteria.
     *
     * The $query parameter may specify:
     *
     * - a string, in which case its value is expected to conform to
     *   the Lucene query syntax; or
     *
     * - an assoc. array of name-value pairs, in which case each name
     *   is epxected to refer to the name of a search field.
     *
     * The assoc. array form of the query is equivalent to a Lucene
     * query in which each name-value pair forms a term and the terms
     * are joined by the "AND" operator, thus:
     *
     *   name1:value1 AND name2:value2 AND ...
     *
     * If non-null, the $sortingCriteria parameter is expected to
     * follow the SQL "order by" syntax with the default sort order
     * being "ASC", like so:
     *
     * name1 [ASC|DESC], name2 [ASC|DESC], ...
     *
     * @param string      $searchId        The unique ID to identify the search
     * @param mixed       $query           The Lucene query or assoc. array of name-value pairs
     * @param string|null $sortingCriteria The sorting criteria (default is null)
     *
     * @return int|null The number of search results, or null on failure
     */
    public function initSearch($searchId, $query, $sortingCriteria = null)
    {
        $this->searchId = $searchId;

        if (is_array($query))
        {
            $this->parameters = $query;
            $this->queryString = null;
        }
        else
        {
            $this->parameters = array();
            $this->queryString = $query;
        }

        if ($sortingCriteria !== null)
            $this->parameters['sortedby'] = strval($sortingCriteria);

        $result = $this->initQuery($this->queryString, $this->parameters);
        if ($result === null)
            return null;

        list($nativeQuery, $query) = $result;
        wcmCache::store('wcmBizsearch_' . $this->searchId, $nativeQuery);
        $this->queryString = $query;

        try
        {
            $result = $this->getSolrService()->search($nativeQuery['query'], 0, 0);
        }
        catch (Exception $error)
        {
            $this->lastErrorMsg = $error->getMessage();
            $this->project->logger->logError(get_class() . ': ' . $this->lastErrorMsg);
            return null;
        }

        return $result->response->numFound;
    }

    /**
     * Initializes a native query from a given Lucene-syntax query
     * string and/or an assoc. array of search parameters.
     *
     * The returned native query is actually an assoc. array as follows:
     *
     *   'query'       => the Lucene query
     *   'queryParams' => the query parameters (eg. for sorting)
     *
     * @param string|null       $queryString Lucene-syntax query string
     * @param array|null        $parameters  Assoc. of search parameters
     * @param string|array|null $includes    Previous search ID(s) to include
     *
     * @return array|null The native query and updated query string (in that order), or null on error
     */
    protected function initQuery($queryString = null, $parameters = null, $includes = null)
    {
        // Convert the query string or parameters into a native query
        $result = parent::initQuery($queryString, $parameters);
        if ($result === null)
            return null;

        list($solrQuery, $query) = $result;

        // Append any previous queries
        if ($includes)
        {
            if (!is_array($includes))
                $includes = array($includes);

            $includeQueries = array();
            foreach ($includes as $include)
            {
                $includedNativeQuery = wcmCache::fetch('wcmBizsearch_' . $include);
                if ($includedNativeQuery !== false)
                {
                    $includeQuery = $includedNativeQuery['query'];
                    if ($includeQueryW)
                        $includeQueries[] = '(' . $includeQuery . ')';
                }
            }

            if ($includeQueryies)
            {
                if ($solrQuery)
                    $solrQuery = '(' . $solrQuery . ') AND ';

                $solrQuery .= implode(' AND ', $includeQueries);
            }
        }

        // Solr does not accept an empty query to mean "everything"
        if (!$solrQuery)
            $solrQuery = '[* TO *]';

        // Handle specific search parameters
        $sort = null;
        if (isset($parameters['sortedby']) && $parameters['sortedby'])
            $sort = strtolower($parameters['sortedby']);

        $nativeQuery = array();
        $nativeQuery['query'] = $solrQuery;
        $nativeQuery['queryParams']  = array('sort' => $sort);

        return array($nativeQuery, $query);
    }

    /**
     * Parses a Lucene query or an assoc. array of search parameters
     * to generate a native query.
     *
     * If the $query parameter is null and $parameters is non-empty,
     * generates a Lucene query from any parameters in the $parameters
     * parameter referring to search indexes.
     *
     * @param string|null $query      The Lucene query
     * @param array|null  $parameters The assoc. of search parameters
     *
     * @return array|null The native query and updated query (in that order), or null on error
     */
    protected function parseQuery($query = null, $parameters = null)
    {
        // No conversion necessary since a Solr query *is* a Lucene query
        $nativeQuery = $query;

        // Noramlize query term fields
        $regExp = $this->getQueryTermFieldRegExp();
        $callback = array($this, 'normalizeQueryTermField_callback');
        $nativeQuery = preg_replace_callback($regExp, $callback, $nativeQuery);

        // Noramlize query term dates
        $regExp = $this->getQueryTermDateRegExp();
        $callback = array($this, 'normalizeQueryTermDate_callback');
        $nativeQuery = preg_replace_callback($regExp, $callback, $nativeQuery);

        return array($nativeQuery, $query);
    }

    /**
     * Gets the native query initialized by the last call to the
     * initSearch method.
     *
     * @return string The native query
     */
    public function getNativeQuery()
    {
        $nativeQuery = null;
        if ($this->searchId)
        {
            $nativeQuery = wcmCache::fetch('wcmBizsearch_' . $this->searchId);
            if ($nativeQuery === false)
                $nativeQuery = null;
        }

        return $nativeQuery;
    }

    /**
     * Gets a range of documents after a successful call to the
     * initSearch method.
     *
     * @param int         $from     The index of the first document to get
     * @param int         $to       The index of the last document to get
     * @param string|null $searchId The search ID used in the call to the initSearch method
     * @param bool|true   $xml      Whether to return raw XML or an array of bizobjects
     *
     * @return array|null Raw XML or an array containing the requested range of documents (bizobjects),
     *                    or null if the initSearch method failed or was not called
     */
    public function getDocumentRange($from, $to, $searchId = null, $xml = true)
    {
        if ($searchId === null)
            $searchId = $this->searchId;

        $nativeQuery = wcmCache::fetch('wcmBizsearch_' . $searchId);
        if ($nativeQuery === false)
        {
            $this->lastErrorMsg = sprintf(_INVALID_SEARCH_ID, $searchId);
            $this->project->logger->logError(get_class() . ': ' . $this->lastErrorMsg);
            return null;
        }

        try
        {
            $solrResponse = $this->getSolrService()->search(
                $nativeQuery['query'], $from, $to - $from + 1, $nativeQuery['queryParams']);
        }
        catch (Exception $error)
        {
            $this->lastErrorMsg = $error->getMessage();
            $this->project->logger->logError(get_class() . ': ' . $this->lastErrorMsg);
            return null;
        }

        if ($xml)
            $result = '<resultSet>';
        else
            $result = array();

        foreach ($solrResponse->response->docs as $document)
        {
            $objectXml = $document->objectXml;
            if ($xml)
            {
                $result .= '<result>' . $objectXml . '</result>';
            }
            else
            {
                list($className, $id) = explode('_', $document->solrDocumentId);
                try
                {
                    $bizobject = new $className;
                    if (!$bizobject->initFromXML($objectXml))
                        throw new Exception(_BIZ_INVALID_OBJECT_XML, $className, $id);
                }
                catch (Exception $error)
                {
                    $this->lastErrorMsg = $error->getMessage();
                    $this->project->logger->logError(get_class() . ': ' . $this->lastErrorMsg);
                    return null;
                }

                $result[] = $bizobject;
            }
        }

        if ($xml)
            $result .= '</resultSet>';

        return $result;
    }

    // FACETING

    /**
     * Gets the raw facet values and their occurence count for a given
     * facet index, possibly limited to a previous search result set.
     *
     * @param string      $indexName The facet index name
     * @param string|null $searchId  The reference search ID to limit values
     *
     * @return array|null Assoc. of raw facet value => occurence count, or null on error
     */
    public function getRawFacetValues($indexName, $searchId = null)
    {
        $facetValues = array();
        try
        {
            if ($searchId)
            {
                $nativeQuery = wcmCache::fetch('wcmBizsearch_' . $searchId);
                if ($nativeQuery === false)
                    throw new Exception(sprintf(_INVALID_SEARCH_ID, $searchId));

                $query = $nativeQuery['query'];
            }
            else
            {
                $query = '[* TO *]';
            }

            $queryParams = array();
            $queryParams['facet'] = 'true';
            $queryParams['facet.enum.cache.minDf'] = 1;
            $queryParams['facet.field'] = array($indexName);
            $queryParams['facet.limit'] = 512/*TODO*/;
            $queryParams['facet.mincount'] = 1;
            $queryParams['facet.sort'] = 'true';

            $solrResponse = $this->getSolrService()->search($query, 0, 0, $queryParams);
            $facetValues = get_object_vars($solrResponse->facet_counts->facet_fields->$indexName);
        }
        catch (Exception $error)
        {
            $this->lastErrorMsg = $error->getMessage();
            $this->project->logger->logError(get_class() . ': ' . $this->lastErrorMsg);
            return null;
        }

        return $facetValues;
    }

    /**
     * Get the count of occurences for a given raw facet value,
     * possibly in the result set corresponding to a given reference
     * search ID.
     *
     * @param string      $indexName     The facet index name
     * @param string      $rawFacetValue The raw facet value
     * @param string|null $searchId      The reference search ID (may be null)
     *
     * @return int The count of occurences
     */
    public function getFacetValueCount($indexName, $rawFacetValue, $searchId = null)
    {
        $facetValues = $this->getRawFacetValues($indexName, $searchId);
        if ($facetValues === null || !isset($facetValues[$rawFacetValue]))
            return 0;

        return $facetValues[$rawFacetValue];
    }

    // IMPLEMENTATION

    /**
     * Gets the Solr service instance.
     *
     * Throws an exception if the service times out.
     *
     * @return Apache_Solr_Service The Solr service instance
     */
    private function getSolrService()
    {
        if (@$this->solrService->ping() === false)
            throw new Exception(sprintf(_SEARCH_SERVICE_NOT_RESPONDING, $this->solrServiceURL));

        return $this->solrService;
    }

    /**
     * Gets the regexp matching a Lucene query term field.
     *
     * @return string The regexp
     */
    private function getQueryTermFieldRegExp()
    {
        if (self::$queryTermFieldRegExp === null)
        {
            $fields = array_merge(
                array_keys($this->getIndexList()),
                array_keys($this->getIndexAliasList()));

            self::$queryTermFieldRegExp = '/\\b('.implode('|', $fields).')\\s*:/i';
        }

        return self::$queryTermFieldRegExp;
    }

    /**
     * Gets the regexp matching a Lucene query term date.
     *
     * @return string The regexp
     */
    private function getQueryTermDateRegExp()
    {
        if (self::$queryTermDateRegExp === null)
        {
            $fields = array();
            foreach (self::$indexes as $name => $index)
            {
                if ($index['kind'] == 'date')
                    $fields[] = $name;
            }

            $fieldRegExp = '\\b('.implode('|', $fields).')\\s*:\\s*';
            $dateRegExp = '(\\S+)(?:\\s+TO\\s+(\\S+))?';

            self::$queryTermDateRegExp = '/'.$fieldRegExp.$dateRegExp.'/i';
        }

        return self::$queryTermDateRegExp;
    }

    /**
     * Callback for the preg_replace_callback function that normalizes
     * a Lucene query term field as defined by the regexp returned by
     * the getQueryTermFieldRegExp method.
     *
     * @param array $matches The current matches
     *
     * @return string The replacement string
     */
    private function normalizeQueryTermField_callback($matches)
    {
        return $this->convertFieldNameOrAlias($matches[1]) . ':';
    }

    /**
     * Callback for the preg_replace_callback function that normalizes
     * a Lucene query term date as defined by the regexp returned by
     * the getQueryTermDateRegExp method.
     *
     * @param array $matches The current matches
     *
     * @return string The replacement string
     */
    private function normalizeQueryTermDate_callback($matches)
    {
        $field    = $matches[1];
        $fromDate = $matches[2];
        $toDate   = isset($matches[3]) ? $matches[3] : null;

        // Generate a "to" date if none
        if (!$toDate)
        {
            // If "from" date is relative, "to" date is "now",
            // otherwise "to" date is equal to "from" date
            if (is_numeric($fromDate) && intval($fromDate) <= 365)
                $toDate = time();
            else
                $toDate = $fromDate;
        }

        // Convert dates to timestamps
        $fromTime = $this->date2time($fromDate);
        $toTime   = $this->date2time($toDate);

        // Swap "from" and "to" dates if reversed
        if ($fromTime > $toTime)
            list($toTime, $fromTime) = array($fromTime, $toTime);

        // Timestamp => date only => timestamp (midnight)
        $fromTime = strtotime(date(self::SOLR_DATE_FORMAT, $fromTime));

        // Timestamp => date only => timestamp (midnight) + 1 day
        $toTime = strtotime(date(self::SOLR_DATE_FORMAT, $toTime)) + 24 * 60 * 60;

        // Timestamp => date-time
        $fromDate = wcmBizsearch::escapeQuery(date(self::SOLR_DATETIME_FORMAT, $fromTime));
        $toDate   = wcmBizsearch::escapeQuery(date(self::SOLR_DATETIME_FORMAT, $toTime));

        return $field.':['.$fromDate.' TO '.$toDate.']';
    }

    /**
     * Converts a given date to a Unix timestamp.
     *
     * @param mixed $date The date to convert
     *
     * @return int The corresponding timestamp
     */
    private function date2time($date)
    {
        $time = null;
        if (!is_numeric($date))
        {
            // Accept m10 for -10 and 2008x09x09 for 2008-09-09 to
            // work around a limitation in the Zend query parser
            $date = preg_replace('/[mx]([0-9]+)/i', '-$1', $date);

            // Alpha-numeric date format
            $time = strtotime($date);
        }
        elseif (intval($date) <= 365)
        {
            // Delta in days
            $time = strtotime("$date days");
        }
        else
        {
            // Unix timestamp
            $time = intval($date);
        }

        return $time;
    }
}
?>