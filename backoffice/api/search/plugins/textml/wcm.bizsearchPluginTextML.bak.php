<?php

/**
 * Project:     WCM
 * File:        api/search/plugins/textml/wcm.bizsearchPluginTextML.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

/**
 * The wcmBizsearchPluginTextML class implements a TextML-oriented
 * full-text search on bizobjects.
 */
class wcmBizsearchPluginTextML extends wcmBizsearchPluginAbstract
{
    // Protected properties (in memory only)
    protected $textmlWS     = null;
    protected $textmlServer = null;
    protected $textmlQuery  = null;

    /**
     * Constructor
     *
     * @param string|null $textmlWS URL of the Textml WebService (or null to retrieve from config file)
     */
    public function __construct($textmlWS = null)
    {
        parent::__construct();

        // Determine textml webservice URL
        if (!$textmlWS)
        {
            $config = wcmConfig::getInstance();
            $textmlWS = $config['textml.webServices'];
        }
        $this->textmlWS = $textmlWS;

        // Instantiate wcmTextmlServer class
        $this->textmlServer = new wcmTextmlServer($this->textmlWS, true, $this->project->logger);

        // Initialize default search parameters
        $this->parameters = array('sortedby' => 'hits');
    }

    /**
     * Gets the document name used to store the XML representation of a bizobject.
     *
     * @param bizobject $bizobject The bizobject
     *
     * @return string The document name used to store the XML representation of the bizobject
     */
    protected function getDocumentName($bizobject)
    {
        return ('/'.get_class($bizobject).'/'.get_class($bizobject).'_'.$bizobject->id.'.xml');
    }

    /**
     * Initiates a new search.
     *
     * The $query parameter may specify:
     *
     * - a string, in which case it must conform to the Lucene query
     *   syntax; or
     *
     * - an assoc. array of name-value pairs, in which case each name
     *   must refer to a search field.
     *
     * If $query specifies an assoc. array, a Lucene query is built by
     * forming a term with each name-value pair and then joining the
     * terms with the "AND" operator, thus:
     *
     *   name1:value1 AND name2:value2 AND ...
     *
     * @param string      $searchId        A unique ID for the search
     * @param mixed       $query           The Lucene query or assoc. array of name-value pairs
     * @param string|null $sortingCriteria The sorting criteria (default is null)
     *
     * @return string The number of search results
     */
    public function initSearch($searchId, $query, $sortingCriteria = null)
    {
        $this->searchId = $searchId;

        // Set query string if given
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

        // Set sort order if given
        if ($sortingCriteria !== null)
            $this->parameters['sortedby'] = strval($sortingCriteria);

        // Initialize query
        $result = $this->initQuery($this->queryString, $this->parameters);
        if ($result === null)
            return null;

        list($nativeQuery, $query) = $result;
        $this->project->logger->logVerbose(get_class() . ' searching for: ' . $nativeQuery);
        $this->queryString = $query;

        // Execute query
        $this->textmlQuery = new wcmTextmlQuery($this->textmlServer, $searchId);
        $total = $this->textmlQuery->initQuery($nativeQuery, null, $this->textmlServer);
        if ($total === null)
        {
            $this->lastErrorMsg = $this->textmlQuery->lastError;
            $this->project->logger->logError(get_class() . ' failed: ' . $this->lastErrorMsg);
            $this->textmlQuery = null;
        }

        return $total;
    }

    /**
     * Initializes a native query from a given Lucene-syntax query
     * string and/or an assoc. array of search parameters.
     *
     * If a query string is given, uses the query parser returned by
     * $this->getQueryParser().
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

        // If the native query is empty, search all collections
        list($nativeQuery, $queryString) = $result;
        if (!$nativeQuery)
            $nativeQuery .= '<property NAME="Collection"><elem>/<anystr />/</elem></property>';

        // Include any previous searches
        if ($includes)
        {
            if (!is_array($includes))
                $includes = array($includes);

            foreach ($includes as $include)
                $nativeQuery .= '<include TYPE="ResultSpace">'.$include.'</include>';

            $nativeQuery = '<andkey>'.$nativeQuery.'</andkey>';
        }

        // Compute sort criteria
        // Assuming that 'sortedby' is built as a typical SQL sort condition
        // e.g. {index} [|ASC|DESC] (,{index} [|ASC|DESC])*
        $sortCriteria = null;
        $indexList  = $this->textmlServer->indexList;

        if (isset($parameters['sortedby']) && $parameters['sortedby'])
        {
            // The 'sortedby' parameter value is case-insensitive
            $sortList = explode(',', strtolower($parameters['sortedby']));
            foreach ($sortList as $sortParam)
            {
                $sortElem = explode(' ', trim($sortParam));
                $sortKey = $sortElem[0];
                $sortReverse = (count($sortElem) > 1 && ($sortElem[1] == 'desc')) ? 'True' : 'False';

                if ($sortKey == 'hits')
                {
                    $sortCriteria .= '<criteria TYPE="Hits" REVERSEORDER="' . $sortReverse . '"/>';
                }
                else
                {
                    $indexName = $this->convertFieldName($sortKey);
                    if (isset($indexList[$indexName]))
                    {
                        $index = $indexList[$indexName];
                        if ($index['kind'] == 'DateIndex')
                        {
                            $sortType = ($index['property']) ? 'Property' : 'Index';
                            $sortCriteria .= '<criteria TYPE="' . $sortType . '" NAME="' . $indexName . '" REVERSEORDER="' . $sortReverse . '"/>';
                            if (isset($indexList[$indexName."_time"]))
                            {
                                $sortType = ($indexList[$indexName."_time"]['property']) ? 'Property' : 'Index';
                                $sortCriteria .= '<criteria TYPE="' . $sortType . '" NAME="' . $indexName . '_time" REVERSEORDER="' . $sortReverse . '"/>';
                            }
                        }
                        else
                        {
                            $sortType = ($index['property']) ? 'Property' : 'Index';
                            $sortCriteria .= '<criteria TYPE="' . $sortType . '" NAME="' . $indexName . '" REVERSEORDER="' . $sortReverse . '"/>';
                        }
                    }
                }
            }
        }

        // Adjust query if sorting
        if ($sortCriteria)
        {
            $nativeQuery .= '<sort>'.$sortCriteria.'</sort>';
        }

        return array($nativeQuery, $queryString);
    }

    /**
     * Converts a boolean query object to its native equvalent.
     *
     * @param Zend_Search_Lucene_Search_Query $query   The boolean query object
     * @param mixed|null                      $context The current context
     *
     * @return string The native equvalent of the boolean query object
     */
    protected function convertQuery_Boolean($query, $context = null)
    {
        // Push the current query onto the query stack
        array_push($this->queryStack, $query);

        // Partition and convert sub-queries
        $subQueries = $this->convertQuery_BooleanHelper($query, $context);

        // Group optional sub-queries
        $optionalSubQueries = $subQueries['optional'];
        $optionalQuery = implode('', $optionalSubQueries);

        // Group required (+) sub-queries
        $requiredQuery = '';
        if ($requiredSubQueries = $subQueries['required'])
        {
            $requiredQuery .= '<andkey>';
            $requiredQuery .= implode('', $requiredSubQueries);
            $requiredQuery .= '</andkey>';
        }

        // Group prohibited (-) queries
        $prohibitedQuery = '';
        if ($prohibitedSubQueries = $subQueries['prohibited'])
        {
            $prohibitedQuery .= '<andnotkey>';
            $prohibitedQuery .= implode('', $prohibitedSubQueries);
            $prohibitedQuery .= '</andnotkey>';
        }

        $orKey = (count($optionalSubQueries) > 1 ||
                  $optionalQuery && $requiredQuery ||
                  $optionalQuery && $prohibitedQuery ||
                  $requiredQuery && $prohibitedQuery);

        $nativeQuery = '';

        if ($orKey) $nativeQuery .= '<orkey>';

        $nativeQuery .= $optionalQuery;
        $nativeQuery .= $requiredQuery;
        $nativeQuery .= $prohibitedQuery;

        if ($orKey) $nativeQuery .= '</orkey>';

        // Pop the query stack
        array_pop($this->queryStack);

        return $nativeQuery;
    }

    /**
     * Converts a multi-term query object to its native equvalent.
     *
     * @param Zend_Search_Lucene_Search_Query $query   The multi-term query object
     * @param mixed|null                      $context The current context
     *
     * @return string The native equvalent
     */
    protected function convertQuery_MultiTerm($query, $context = null)
    {
        // Push the current query onto the query stack
        array_push($this->queryStack, $query);

        // Partition and convert terms
        $terms = $this->convertQuery_MultiTermHelper($query, $context);

        // Group optional terms
        $optionalTerms = $terms['optional'];
        $optionalTerm = implode('', $optionalTerms);

        // Group required (+) terms
        $requiredTerm = '';
        if ($requiredTerms = $terms['required'])
        {
            $requiredTerm .= '<andkey>';
            $requiredTerm .= implode('', $requiredTerms);
            $requiredTerm .= '</andkey>';
        }

        // Group prohibited (-) terms
        $prohibitedTerm = '';
        if ($prohibitedTerms = $terms['prohibited'])
        {
            $prohibitedTerm .= '<andnotkey>';
            $prohibitedTerm .= implode('', $prohibitedTerms);
            $prohibitedTerm .= '</andnotkey>';
        }

        $orKey = (count($optionalTerms) > 1 ||
                  $optionalTerm && $requiredTerm ||
                  $optionalTerm && $prohibitedTerm ||
                  $requiredTerm && $prohibitedTerm);

        $nativeTerm = '';

        if ($orKey) $nativeTerm .= '<orkey>';

        $nativeTerm .= $optionalTerm;
        $nativeTerm .= $requiredTerm;
        $nativeTerm .= $prohibitedTerm;

        if ($orKey) $nativeTerm .= '</orkey>';

        // Pop the query stack
        array_pop($this->queryStack);

        return $nativeTerm;
    }

    /**
     * Converts a phrase query object to its native equvalent.
     *
     * @param Zend_Search_Lucene_Search_Query $query   The phrase query object
     * @param mixed|null                      $context The current context
     *
     * @return string The native equvalent
     */
    protected function convertQuery_Phrase($query, $context = null)
    {
        // Push the current query onto the query stack
        array_push($this->queryStack, $query);

        // Parse the query into its field, term texts, and proximity
        // factor ("slop")
        $parts = $this->convertQuery_PhraseHelper($query, $context);
        $field = $parts['field'];
        $texts = $parts['texts'];
        $slop  = $parts['slop'];

        $nativeText = '';

        // If we have a proximity factor, we wrap the individual
        // <elem> elements in a <near> element, otherwise we just
        // convert the phrase as a whole
        if ($slop == 0)
        {
            $text = '"'.implode(' ', $texts).'"';
            $nativeText .= $this->convertTermText($field, $text, $context);
        }
        else
        {
            $nativeText .= '<near VALUE="'.$slop.'">';
            foreach ($texts as $text)
            {
                $nativeText .= $this->convertTermText($field, $text, $context);
            }
            $nativeText .= '</near>';
        }

        // Convert the term's field, including the native text
        $nativeQuery = $this->convertTermField($field, $nativeText, $context);

        // Pop the query stack
        array_pop($this->queryStack);

        return $nativeQuery;
    }

    /**
     * Converts a Lucene wildcard query object to its native
     * equvalent.
     *
     * @param Zend_Search_Lucene_Search_Query_Wildcard $query   The wildcard query object
     * @param mixed|null                               $context The current context
     *
     * @return string The native equvalent of the wildcard query object
     */
    protected function convertQuery_Wildcard(
        Zend_Search_Lucene_Search_Query_Wildcard $query, $context = null)
    {
        // Push the current query onto the query stack
        array_push($this->queryStack, $query);

        // A wildcard query has a pattern, which is really just a
        // query term
        $term = $query->getPattern();

        // Replace any Lucene wildcard characters with <anychr/>,
        // <anystr/>, etc. in the term's text
        $text = $term->text;
        $text = preg_replace('/\\?/', '<anychr/>', $text);
        $text = preg_replace('/\\*/', '<anystr/>', $text);
        $term->text = $text;

        // Now convert the term to its native equvalent
        $nativeQuery = $this->convertTerm($term, $context);

        // Pop the query stack
        array_pop($this->queryStack);

        return $nativeQuery;
    }

    /**
     * Converts a query term's text to its native equvalent.
     *
     * @param string     $field   The query term's field
     * @param string     $text    The query term's text
     * @param mixed|null $context The current context
     *
     * @return string The native equvalent of the query term's text
     */
    protected function convertTermText($field, $text, $context = null)
    {
        $indexList = $this->textmlServer->indexList;
        $indexName = $this->convertFieldName($field);
        $indexValue = $text;

        $nativeText = '';
        if ($indexName == 'Collection')
        {
            // The <elem> value for a Collection property needs to be
            // surrounded by slashes
            $nativeText .= '<elem>/'.$indexValue.'/</elem>';
        }
        else
        {
            // Convert the index value according to the kind of index
            $index = $indexList[$indexName];
            switch ($index['kind'])
            {
            case 'WordIndex':
                $nativeText .= wcmTextmlQueryHelper::generateWordQuery($indexValue);
                break;

            case 'StringIndex':
                $nativeText .= wcmTextmlQueryHelper::generateStringQuery($indexValue);
                break;

            case 'DateIndex':
                // Accept m10 for -10 and 2008x09x09 for 2008-09-09 to
                // work around a limitation in the Zend query parser
                $indexValue = preg_replace('/[mx]([0-9]+)/i', '-$1', $indexValue);

                // Some assumptions here:
                // - If the criteria contains '..' we want an interval (range)
                // - If the criteria is a small numeric value (<=365) we want a relative date
                if (strpos($indexValue, '..') !== false)
                {
                    $parts = explode('..', $indexValue);

                    // Trim leading and trailing whitespace
                    if (trim($parts[0]) == '') $parts[0] = null;
                    if (trim($parts[1]) == '') $parts[1] = null;

                    // Convert non-numeric dates (timestamps)
                    if (!is_numeric($parts[0])) $parts[0] = strtotime($parts[0]);
                    if (!is_numeric($parts[1])) $parts[1] = strtotime($parts[1]);

                    $nativeText .= wcmTextmlQueryHelper::generateIntervalDateQuery($parts[0], $parts[1]);
                }
                elseif (!is_numeric($indexValue))
                {
                    $nativeText .= wcmTextmlQueryHelper::generateDateQuery(strtotime($indexValue));
                }
                elseif (intval($indexValue) <= 365)
                {
                    $nativeText .= wcmTextmlQueryHelper::generateRelativeDateQuery(intval($indexValue));
                }
                else
                {
                    $nativeText .= wcmTextmlQueryHelper::generateDateQuery($indexValue);
                }
                break;

            case 'TimeIndex':
                $nativeText .= wcmTextmlQueryHelper::generateTimeQuery($indexValue);
                break;

            case 'NumericIndex':
                // Some assumptions here:
                // - If the criteria contains '..' we want an interval (range)
                if (strpos($indexValue, '..') !== false)
                {
                    $parts = explode('..', $indexValue);

                    // Trim leading and trailing whitespace
                    if (trim($parts[0]) == '') $parts[0] = null;
                    if (trim($parts[1]) == '') $parts[1] = null;

                    $nativeText .= wcmTextmlQueryHelper::generateIntervalNumberQuery($parts[0], $parts[1]);
                }
                else
                {
                    $nativeText .= wcmTextmlQueryHelper::generateNumberQuery($indexValue);
                }
                break;
            }
        }

        return $nativeText;
    }

    /**
     * Converts a query term's field to its native equvalent,
     * including the given native text in the result.
     *
     * @param string     $field      The query term's field
     * @param string     $nativeText The query term's text (already in native form)
     * @param mixed|null $context    The current context
     *
     * @return string The native equvalent of the query term's field
     */
    protected function convertTermField($field, $nativeText, $context = null)
    {
        // The TextML equvalent of a Lucene field are the <key> and
        // <property> elements
        $indexName = $this->convertFieldName($field);
        $tag = $this->textmlServer->indexList[$indexName]['property'] ? 'property' : 'key';
        return '<'.$tag.' NAME="'.$indexName.'">'.$nativeText.'</'.$tag.'>';
    }

    /**
     * Converts a Lucene field name to its native equvalent.
     *
     * @param string $field The Lucene field name
     *
     * @return string The native equvalent of the Lucene field name
     */
    protected function convertFieldName($field)
    {
        // Convert a field alias to its canonical form
        $field = $this->convertFieldNameOrAlias($field);

        // The 'classname' field corresponds to the 'Collection' index
        $indexName = ($field == 'classname' ? 'Collection' : $field);

        // If an index name was not specified or it's invalid, use the
        // default one
        if (!$indexName || !isset($this->textmlServer->indexList[$indexName]))
        {
            // The 'fulltext' index seems like a good candidate for
            // the default index
            $indexName = "fulltext";
        }

        return $indexName;
    }

    /**
     * Formats a query term's text according to its field type for
     * inclusion in a Lucene-syntax query string, eg., a term text
     * with whitespace must be surrounded by double quotes, a numeric
     * field must be left unquoted, etc.
     *
     * @param string     $field   The query term's field
     * @param string     $text    The query term's text
     *
     * @return string The formatted query term's text
     */
    protected function formatTermText($field, $text)
    {
        $indexList = $this->textmlServer->indexList;
        $indexName = $this->convertFieldName($field);

        if (!isset($indexList[$indexName]))
        {
            return null;
        }
        $index = $indexList[$indexName];

        $formattedText = '';
        switch ($index['kind'])
        {
        case 'WordIndex':
        case 'StringIndex':
            $formattedText = wcmBizsearch::quoteQueryValue($text);
            break;

        case 'DateIndex':
        case 'TimeIndex':
        case 'NumericIndex':
            $formattedText = $text;
            break;
        }

        return $formattedText;
    }

    /**
     * Gets the native query initialized by the last call to the
     * initSearch method.
     *
     * @return string The native query
     */
    public function getNativeQuery()
    {
        if (!$this->textmlQuery)
        {
            return null;
        }
        return $this->textmlQuery->nativeQuery;
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
        if (!$this->textmlQuery)
        {
            if ($searchId)
                $this->textmlQuery = new wcmTextmlQuery($this->textmlServer, $searchId);

            if (!$searchId || !$this->textmlQuery)
            {
                $this->lastErrorMsg = sprintf(getConst(_BIZ_INVALID_ID), $searchId);
                return null;
            }
        }
        return $this->textmlQuery->getDocumentRange($from, $to, $xml);
    }

    /**
     * Gets the list of search indices.
     *
     * @return array List of search indices
     */
    public function getIndexList()
    {
        return $this->textmlServer->indexList;
    }

    /**
     * Gets the list of distinct values for a given index.
     *
     * @param string $indexName Name of the index to scan
     * @param int    $maxValues Maximum number of returned values (default is 512)
     * @param string $query     Optional native Textml query to reduce the scope of index values (default is NULL)
     *
     * @return array|null An assoc. array of index value => document count, or null on failure
     */
    public function getIndexValues($indexName, $maxValues = 512, $query = null)
    {
        return $this->textmlServer->getIndexValues($indexName, $maxValues, $query);
    }

    /**
     * Gets a range of documents data after a successful call to the
     * initSearch method.
     *
     * NOTE: The TextML indexes specified in the $indexList parameters
     * must be of type 'string' and their "keep extracted" attribute
     * must be set to "true'.
     *
     * @param string      $searchId   The search ID used in the call to the initSearch method
     * @param string      $indexList  The list of TextML indexes to search
     * @param int|null    $maxResults The maximum number of results per index (default is null)
     * @param bool|true   $xml        Whether to return raw XML or an array
     *
     * @return array|string|null Raw XML or an array containing the requested range of documents data,
     *                               or null on error
     */
    public function getDocumentsData($searchId, $indexList, $maxResults = null, $xml = true)
    {
        $textmlQuery = new wcmTextmlQuery($this->textmlServer, $searchId.$indexList.'_documentData');
        return $textmlQuery->getDocumentsData($searchId, $indexList, $maxResults, $xml);
    }

    /**
     * Count the number of elements indexed for the sourceClass
     *
     * @param string $className class to search for (or null for all)
     *
     * @return int Number of indexed elements of a class
     */
    public function getCount($className = null)
    {
        $count = 0;

        if ($className)
        {
            $query = '<property NAME="Collection"><elem>/'.$className.'/</elem></property>';

            $tq = new wcmTextmlQuery($this->textmlServer, 'totalCount_'.$className);
            $count = $tq->initQuery($query);
            if ($count === null)
            {
                $this->lastErrorMsg = $tq->lastError;
                $this->project->logger->logError($this->lastErrorMsg);
            }

            unset($tq);
        }
        else
        {
            $collections = $this->getIndexValues('Collection');
            if ($collections)
            {
                foreach ($collections as $name => $partialCount)
                {
                    $count += $partialCount;
                }
            }
        }

        return $count;
    }

    /**
     * Index a bizobject in the search table
     *
     * @param bizobject $bizobject  Bizobject to index
     *
     * @return bool False on failure, True on success
     */
    public function indexBizobject(wcmBizobject $bizobject)
    {
        // Ignore empty bizobject
        if (!$bizobject || !$bizobject->id) return true;

        $documentName = $this->getDocumentName($bizobject);
        $xmlContent = '<?xml version="1.0" encoding="utf-8" ?>' . $bizobject->toXML();

        // Index in the textML database
        $textmlServer = new wcmTextmlServer($this->textmlWS, false, $this->project->logger);
        if (!$textmlServer->setDocument($documentName, $xmlContent))
        {
            $this->lastErrorMsg = $textmlServer->lastError;
            $this->project->logger->logError($this->lastErrorMsg);
            return false;
        }
        return true;
    }

    /**
     * Deindex a bizobject
     *
     * @param bizobject $bizobject  Bizobject to remove
     *
     * @return bool False on failure, True on success
     */
    public function deindexBizobject(wcmBizobject $bizobject)
    {
        // Ignore empty bizobject
        if (!$bizobject || !$bizobject->id) return true;

        // Deindex through textML
        $textmlServer = new wcmTextmlServer($this->textmlWS, false, $this->project->logger);
        if (!$textmlServer->removeDocument($this->getDocumentName($bizobject)))
        {
            $this->lastErrorMsg = $textmlServer->lastError;
            $this->project->logger->logError($this->lastErrorMsg);
            return false;
        }
        return true;
    }

    /**
     * Deindex all bizobjects of a given class.
     *
     * @param string $className The name of the class for which to deindex
     *
     * @return bool False on failure, True on success
     */
    public function deindexBizobjects($className)
    {
        $textmlServer = new wcmTextmlServer($this->textmlWS, false, $this->project->logger);
        if (!$textmlServer->removeCollection('/' . $className . '/'))
        {
            $this->lastErrorMsg = $textmlServer->lastError;
            $this->project->logger->logError($this->lastErrorMsg);
            return false;
        }

        return true;
    }

    /**
     * Gets the raw facet values and their occurence count for a given
     * facet.
     *
     * @param string $facet The facet
     *
     * @return array Assoc. of raw facet value => occurence count
     */
    public function getRawFacetValues($indexName, $searchId = null)
    {
        $fieldList = '<fieldlist><field NAME="'.$indexName.'" TYPE="Index" /></fieldlist>';

        $documentsData = $this->getDocumentsData($searchId, $fieldList, 512/*TODO*/, false);
        if (!$documentsData || !isset($documentsData[$indexName]))
            return null;

        return $documentsData[$indexName];
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
        // Search for the raw value in the reference search result set
        // (the third parameter to "initQuery" below) to get an
        // occurence count.

        $value = wcmBizsearch::escapeQuery($rawFacetValue);
        $queryString = $indexName . ':' . wcmBizsearch::quoteQueryValue($value);

        $result = $this->initQuery($queryString, null, $searchId);
        if ($result === null)
            return 0;

        list($nativeQuery, $queryString) = $result;
        $textmlQuery = new wcmTextmlQuery($this->textmlServer, $searchId.$value.'_facetValue');

        $count = $textmlQuery->initQuery($nativeQuery, null, $this->textmlServer);
        if ($count === null)
        {
            $this->lastErrorMsg = $textmlQuery->lastError;
            $this->project->logger->logError($this->lastErrorMsg);
            return 0;
        }

        return $count;
    }
}
?>
