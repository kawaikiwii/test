<?php
/**
 * Project:     WCM
 * File:        api/search/wcm.bizsearchPluginDB.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

/**
 * The abstract wcmBizsearchPluginDB class helps implement a
 * database-oriented full-text search on bizobjects.
 *
 * This class serves as the base class for all DB-specific business
 * search plugin implementations.
 */
abstract class wcmBizsearchPluginDB extends wcmBizsearchPluginAbstract
{
    const SEARCH_CONNECTOR = 'biz';
    const SEARCH_TABLENAME = 'biz__search';

    // Protected properties (in memory only)
    protected $database     = null;
    protected $tableName    = null;

    /**
     * Normalizes a word-index value by replacing each
     * non-alphanumeric character with a space and removing any extra
     * whitespace.
     *
     * @param string $value      The value to normalize
     * @param bool   $wildcarded Whether the value contains wildcard characters (_ and %)
     *
     * @return string The normalized value
     */
    protected static function normalizeWordIndexValue($value, $wildcarded = false)
    {
        // Replace non-alphanumeric characters (except possibly
        // wildcards) with a space
        $validChars = 'A-Za-z0-9';
        if ($wildcarded)
            $validChars .= '_%';

        $value = preg_replace('/[^' . $validChars . ']+/', ' ', $value);

        // Remove extra whitespace
        $value = preg_replace('/\s+/', ' ', $value);

        return $value;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->database = wcmProject::getInstance()->datalayer->getConnectorByReference(self::SEARCH_CONNECTOR)->getBusinessDatabase();
        $this->tableName = self::SEARCH_TABLENAME;
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
        $db = $this->database;

        $sql = 'delete FROM ' . self::SEARCH_TABLENAME . ' WHERE classname=?';
        $params = array($className);

        return $db->executeStatement($sql, $params);
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

        $db = $this->database;

        $sql = 'DELETE FROM ' . self::SEARCH_TABLENAME . ' WHERE classname=? AND id=?';
        $params = array(get_class($bizobject), $bizobject->id);

        return $db->executeStatement($sql, $params);
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
        if (!$bizobject || !$bizobject->id)
        {
            return true;
        }

        // Get the XML represention of the bizobject and parse it
        $domDoc = new DOMDocument;
        if (!$domDoc->loadXML($bizobject->toXML()))
        {
            $this->lastErrorMsg = getConst(_BIZ_INVALID_XML)
                . ' (' . get_class($bizobject) . '-' . $bizobject->id . ')';
            $this->project->logger->logError(get_class() . ': ' . $this->lastErrorMsg);
            return false;
        }

        // For each index, fetch the corresponding value(s) from the
        // bizobject's XML representation and set it in the search item
        $domXPath = new DOMXPath($domDoc);
        $className = get_class($bizobject);

        $item = new StdClass;
        foreach (self::$indexes as $name => $index)
        {
            $values = array();

            $xpaths = $index['xpaths'];
            if ($xpaths)
            {
                foreach ($xpaths as $xpath)
                {
                    $nodeList = $domXPath->query('/' . $className . $xpath);
                    if ($nodeList)
                    {
                        foreach ($nodeList as $node)
                        {
                            if ($node->nodeValue)
                            {
                                switch ($index['kind'])
                                {
                                case 'word':
                                    $value = self::normalizeWordIndexValue($node->nodeValue);
                                    break;

                                default:
                                    $value = $node->nodeValue;
                                    break;
                                }
                                $values[] = $value;
                            }
                        }
                    }
                }
            }

            if ($values)
            {
                if ($index['multivalued'])
                {
                    $separator = $index['separator'];
                    $item->$name = $separator . implode($separator, $values) . $separator;
                }
                else
                {
                    $item->$name = $values[0];
                }
            }
        }

        // Deindex first
        $this->deindexBizobject($bizobject);

        // Reindex, ie., store the search item in the database
        return $this->database->insertObject(self::SEARCH_TABLENAME, $item);
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
        $sql = 'SELECT COUNT(*) FROM ' . $this->tableName;
        $params = array();

        if ($className)
        {
            $sql .= ' WHERE classname=?';
            $params[] = $className;
        }

        return $this->database->executeScalar($sql, $params);
    }

    /**
     * Computes sql where clause corresponding to current values of this
     * object properties
     *
     * @param string $key Key to look for.
     * @param string $value Value to match for in the full text query.
     *
     * @return The value of the SQL match statement
     */
    abstract function getMatchCondition($key, $value);

    /**
     * generateDateTimeSQL
     * 
     * This function will create a MySQL subquery to search on a datetime index. 
     *
     * @param string $startDate First date
     * @param string $endDate End date
     *
     * @return string A MySQL subquery
     */
    public function generateDateTimeSQL($startDate, $endDate = null)
    {
        $isRange = false;
        $startTime = $endTime = 0;

        if(!is_numeric($startDate))
        {
            // Formatted date or 0 if *
            // If no time is given 00:00:00 will be there by default
            $startTime = ('' === $startDate) ? 0 : strtotime($startDate);
        }
        elseif(intval($startDate) > 365)
        {
            // Timestamp unix
            $startTime = intval($startDate);
        }
        else
        {
            // Delta in days, implies date range
            $isRange = true;
            $startTime = time();
            $endTime = strtotime($startDate . ' days');
        }


        if(null !== $endDate)
        {
            $isRange = true;
            if(!is_numeric($endDate))
            {
                // Formatted date or 0 if * 
                // If no time is given we add a day to make a search on all the day
                $endTime = ('' === $endDate) ? PHP_INT_MAX : strtotime($endDate) + (preg_match('/T\d+:\d+:\d+/', $endDate) ? 0 : 3600*24);
            }
            elseif(intval($endDate) > 365)
            {
                $endTime = intval($endDate);
            }
            else
            {
                $endTime = strtotime($startTime . ' days');
            }
        }
        elseif(!$endTime)
        {
            if(!preg_match('/T\d+:\d+:\d+/', $startDate))
            {
                $isRange = true;
                $endTime = $startTime + 3600*24;
            }
            else
            {
                $endTime = $startTime;
            }
        }

        if($isRange && $startTime > $endTime)
        {
            list($startTime, $endTime) = array($endTime, $startTime);
        }
        return " BETWEEN '".date('Y-m-d H:i:s', $startTime)."' AND '".date('Y-m-d H:i:s', $endTime)."'";
    }

    /**
     * TODO
     */
    public function generateDateSQL($targetDate, $sourceDate = null)
    {
        $isRange = false;

        // Target date => timestamp
        if (!is_numeric($targetDate))
        {
            // Formatted date
            $targetTime = (null !== $sourceDate && $targetDate === '') ? PHP_INT_MAX-(3600*24) : strtotime($targetDate);
        }
        elseif (intval($targetDate) > 365)
        {
            // Timestamp
            $targetTime = intval($targetDate);
        }
        else
        {
            // Delta in days, implies date range
            $isRange = true;
            $sourceTime = time();
            $targetTime = strtotime("$targetDate days");
        }

        if ($sourceDate !== null)
        {
            // Source date !== null implies date range
            $isRange = true;

            // Source date => timestamp
            if (!is_numeric($sourceDate))
            {
                // formatted date
                $sourceTime = strtotime($sourceDate);
            }
            elseif (intval($sourceDate) > 365)
            {
                // timestamp
                $sourceTime = intval($sourceDate);
            }
            else
            {
                // delta in days
                $sourceTime = strtotime("$sourceDate days");
            }
        }

        if ($isRange)
        {
            // Swap source and target times if necessary
            if ($targetTime < $sourceTime)
            {
                list($sourceTime, $targetTime) = array($targetTime, $sourceTime);
            }

            // Include whole day of target time
            $targetTime = strtotime('+1 day', $targetTime);
        }
        else
        {
            // Include whole day of target time
            $sourceTime = $targetTime;
            $targetTime = strtotime('+1 day', $targetTime);
        }

        // Round source time to beginning of day
        $sourceTime = mktime(0, 0, 0, date('m', $sourceTime),
                             date('d', $sourceTime), date('Y', $sourceTime));

        // Round target time (+1 day) to beginning of day
        $targetTime = mktime(0, 0, 0, date('m', $targetTime),
                             date('d', $targetTime), date('Y', $targetTime));
        return " BETWEEN '".date('Y-m-d', $sourceTime)."' AND '".date('Y-m-d', $targetTime)."'";
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
        $this->queryString = $query;

        $nbResult = $this->database->executeScalar($nativeQuery['queryCount']);
        if ($nbResult === null)
            return null;

        wcmCache::store('wcmBizsearch_' . $this->searchId, $nativeQuery);

        return $nbResult;
    }

    /**
     * Initializes a native query from a given Lucene-syntax query
     * string and/or an assoc. array of search parameters.
     *
     * If a query string is given, uses the query parser returned by
     * $this->getQueryParser().
     *
     * The returned native query is actually an assoc. array as follows:
     *
     *     'querySelect' => the SQL statement to select the rows of the result set
     *     'queryCount'  => the SQL statement to select the counr of rows in the result set
     *     'queryWhere'  => the WHERE clause of the above statements (without the "WHERE")
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

        list($queryWhere, $queryString) = $result;

        // Append any previous queries
        if ($includes)
        {
            if (!is_array($includes))
                $includes = array($includes);

            $includeQueryWheres = array();
            foreach ($includes as $include)
            {
                $includedNativeQuery = wcmCache::fetch('wcmBizsearch_' . $include);
                if ($includedNativeQuery !== false)
                {
                    $includeQueryWhere = $includedNativeQuery['queryWhere'];
                    if ($includeQueryWhere)
                        $includeQueryWheres[] = '(' . $includeQueryWhere . ')';
                }
            }

            if ($includeQueryWheres)
            {
                if ($queryWhere)
                    $queryWhere = '(' . $queryWhere . ') AND ';

                $queryWhere .= implode(' AND ', $includeQueryWheres);
            }
        }

        // Handle specific search parameters
        $orderBy = null;
        if (isset($parameters['sortedby']) && $parameters['sortedby'])
            $orderBy = strtolower($parameters['sortedby']);

        // Select count query
        $queryCount = 'SELECT COUNT(*) FROM ' . $this->tableName;
        if ($queryWhere)
            $queryCount .= ' WHERE ' . $queryWhere;
        if ($orderBy)
            $queryCount .= ' ORDER BY ' . $orderBy;

        $querySelect = 'SELECT classname,id FROM ' . $this->tableName;
        if ($queryWhere)
            $querySelect .= ' WHERE ' . $queryWhere;
        if ($orderBy)
            $querySelect .= ' ORDER BY ' . $orderBy;

        $query = array();
        $query['querySelect'] = $querySelect;
        $query['queryCount']  = $queryCount;
        $query['queryWhere']  = $queryWhere;

        return array($query, $queryString);
    }

    /**
     * Converts a boolean query object to its native equvalent.
     *
     * @param Zend_Search_Lucene_Search_Query $query   The boolean query object
     * @param array|null                      $context The current context
     *
     * @return string The native equvalent of the boolean query object
     */
    protected function convertQuery_Boolean($query, $context = null)
    {
        // Push the current query onto the query stack
        array_push($this->queryStack, $query);

        // Get optional, required, and prohibited sub-queries
        $subQueries = $this->convertQuery_BooleanHelper($query, $context);

        // Build the native query starting with optional sub-queries
        $nativeQuery = '';
        if ($optionalSubQueries = $subQueries['optional'])
        {
            $nativeQuery .= '(' . implode(') OR (', $optionalSubQueries) . ')';
        }

        // If there are required sub-queries, append them to the
        // native query, making sure to parenthesie any optional
        // sub-queries
        if ($requiredSubQueries = $subQueries['required'])
        {
            if ($optionalSubQueries)
                $nativeQuery .= '(' . $nativeQuery . ') AND ';

            $nativeQuery .= '(' . implode(') AND (', $requiredSubQueries) . ')';
        }

        // If there are prohibited sub-queries, append them to the
        // native query, making sure to parenthesie any optional
        // sub-queries if there were no required sub-queries
        // (otherwise the parenthesizing has already been done)
        if ($prohibitedSubQueries = $subQueries['prohibited'])
        {
            if ($requiredSubQueries)
                $nativeQuery .= ' AND ';
            elseif ($optionalSubQueries)
                $nativeQuery .= '(' . $nativeQuery . ') AND ';

            $nativeQuery .= 'NOT (' . implode(') AND NOT (', $prohibitedSubQueries) . ')';
        }

        // Pop the query stack
        array_pop($this->queryStack);

        return $nativeQuery;
    }

    /**
     * Converts a multi-term query object to its native equvalent.
     *
     * @param Zend_Search_Lucene_Search_Query $query   The multi-term query object
     * @param array|null                      $context The current context
     *
     * @return string The native equvalent
     */
    protected function convertQuery_MultiTerm($query, $context = null)
    {
        // Push the current query onto the query stack
        array_push($this->queryStack, $query);

        // Get optional, required, and prohibited terms
        $terms = $this->convertQuery_MultiTermHelper($query, $context);

        // Build the native query starting with optional terms
        $nativeQuery = '';
        if ($optionalTerms = $terms['optional'])
        {
            $nativeQuery .= implode(' OR ', $optionalTerms);
        }

        // If there are required terms, append them to the native
        // query, making sure to parenthesie any optional terms
        if ($requiredTerms = $terms['required'])
        {
            if ($optionalTerms)
                $nativeQuery .= '(' . $nativeQuery . ') AND ';

            $nativeQuery .= implode(' AND ', $requiredTerms);
        }

        // If there are prohibited terms, append them to the native
        // query, making sure to parenthesie any optional terms if
        // there were no required terms (otherwise the parenthesizing
        // has already been done)
        if ($prohibitedTerms = $terms['prohibited'])
        {
            if ($requiredTerms)
                $nativeQuery .= ' AND ';
            elseif ($optionalTerms)
                $nativeQuery .= '(' . $nativeQuery . ') AND ';

            $nativeQuery .= 'NOT ' . implode(' AND NOT ', $prohibitedTerms);
        }

        // Pop the query stack
        array_pop($this->queryStack);

        return $nativeQuery;
    }

    /**
     * Converts a phrase query object to its native equvalent.
     *
     * @param Zend_Search_Lucene_Search_Query $query   The phrase query object
     * @param array|null                      $context The current context
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

        // If we have a proximity factor, we wrap the individual
        // <elem> elements in a <near> element, otherwise we just
        // convert the phrase as a whole
        if ($slop == 0)
        {
            $text = implode(' ', $texts);
            $nativeText = $this->convertTermText($field, $text, $context);
        }
        else
        {
            // TODO can we do anything with this? Let's just ignore
            // the slop for now
            $text = implode(' ', $texts);
            $nativeText = $this->convertTermText($field, $text, $context);
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
     * @param array|null                               $context The current context
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

        // Replace any Lucene wildcard characters with '_', '%',
        // etc. in the term's text
        $text = $term->text;
        $text = preg_replace('/\\?/', '_', $text);
        $text = preg_replace('/\\*/', '%', $text);
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
     * @param array|null $context The current context
     *
     * @return string The native equvalent of the query term's text,
     *                    including any leading operator, eg., '=', 'LIKE', etc.
     */
    protected function convertTermText($field, $text, $context = null)
    {
        $nativeField = $this->convertFieldName($field);
        $nativeText = '';

        // The 'fulltext_tokens' native field is special - it uses an
        // SQL MATCH condition in the WHERE clause and does not need a
        // field/operator prefix
        if ($nativeField == 'fulltext_tokens')
        {
            $nativeText .= $this->getMatchCondition($nativeField, $text);
        }
        else
        {
            $query = $this->queryStack[count($this->queryStack) - 1];
            $wildcarded = ($query instanceof Zend_Search_Lucene_Search_Query_Wildcard);

            $index = self::$indexes[$nativeField];
            switch ($index['kind'])
            {
            case 'date':
                // Accept m10 for -10 and 2008x09x09 for 2008-09-09 to
                // work around a limitation in the Zend query parser
                $text = preg_replace('/[mx]([0-9]+)/i', '-$1', $text);

                $range = explode('..', $text);

                if (count($range) == 1)
                {
                    $nativeText .= $this->generateDateSQL($range[0]);
                }
                else
                {
                    $nativeText .= $this->generateDateSQL($range[1], $range[0]);
                }
                break;
            case 'datetime':
                $text = preg_replace(array('/T(\d+)x(\d+)x(\d+)/i','/[mx]([0-9]+)/i'), array('T$1:$2:$3','-$1'), $text);
                $range = explode('..', $text);
                
                if(1 === count($range))
                {
                    $nativeText .= $this->generateDateTimeSQL($range[0]);
                }
                else
                {
                    $nativeText .= $this->generateDateTimeSQL($range[0], $range[1]);
                }
                break;
            case 'number':
                $range = explode('..', $text);
                if (count($range) == 1)
                {
                    $nativeText .= '=' . $text;
                }
                else
                {
                    $nativeText .= ' BETWEEN ' . $range[0] . ' AND ' . $range[1];
                }
                break;

            case 'word':
                $text = self::normalizeWordIndexValue($text, $wildcarded);
                // Fall through ...

            case 'string':
                $quoted_text = str_replace("'", "''", $text);
                if ($index['multivalued'])
                {
                    $nativeText .= " LIKE '%" . $quoted_text . "%'";
                }
                else
                {
                    if ($wildcarded)
                        $nativeText .= " LIKE '" . $quoted_text . "'";
                    else
                        $nativeText .= " RLIKE '[[:<:]]" . $quoted_text . "[[:>:]]'";
                }
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
     * @param array|null $context    The current context
     *
     * @return string The native equvalent of the query term's field
     */
    protected function convertTermField($field, $nativeText, $context = null)
    {
        $nativeField = $this->convertFieldName($field);

        // The 'fulltext_tokens' native field is special - it uses an
        // SQL MATCH condition in the WHERE clause and does not need a
        // field/operator prefix
        if ($nativeField == 'fulltext_tokens')
        {
            return $nativeText;
        }

        return '`'.$nativeField.'`'.$nativeText;
    }

    /**
     * Converts a Lucene field name to its native equvalent.
     *
     * @param string $field The Lucene field name
     *
     * @return string The native equivalent of the Lucene field name
     */
    protected function convertFieldName($field)
    {
        // Convert a field alias to its canonical form
        $field = $this->convertFieldNameOrAlias($field);

        // If a field name was not specified or it's invalid, use the
        // default one
        if (!$field || !isset(self::$indexes[$field]))
        {
            // The 'fulltext' field seems like a good candidate for
            // the default field
            $field = "fulltext";
        }

        // The 'fulltext' field is special if the term text has no
        // wildcards - in this case, the native field is
        // 'fulltext_tokens'
        $query = $this->queryStack[count($this->queryStack) - 1];
        if ($field == 'fulltext' &&
            !($query instanceof Zend_Search_Lucene_Search_Query_Phrase) &&
            !($query instanceof Zend_Search_Lucene_Search_Query_Wildcard))
        {
            $field = 'fulltext_tokens';
        }

        return $field;
    }

    /**
     * Formats a query term's text according to its field kind for
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
        $indexes = self::$indexes;
        $nativeField = $this->convertFieldName($field);

        if (!isset($indexes[$nativeField]))
        {
            return null;
        }
        $index = $indexes[$nativeField];

        $formattedText = '';
        switch ($index['kind'])
        {
        case 'word':
        case 'string':
            $formattedText = wcmBizsearch::quoteQueryValue($text);
            break;

        case 'date':
        case 'time':
        case 'number':
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
        $nativeQuerySelect = null;
        if ($this->searchId)
        {
            $nativeQuery = wcmCache::fetch('wcmBizsearch_' . $this->searchId);
            if ($nativeQuery !== false)
                $nativeQuerySelect = $nativeQuery['querySelect'];
        }

        return $nativeQuerySelect;
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
            $this->lastErrorMsg = sprintf(getConst(_BIZ_INVALID_ID), $searchId);
            return null;
        }
        $sql =  $nativeQuery['querySelect'];

        $rs = $this->database->executeQuery($sql, array(), $from, $to - $from + 1);
        if ($rs === null)
        {
            $this->lastErrorMsg = $this->database->lastErrorMsg;
            return null;
        }

        if ($xml)
            $result = '<resultSet>';
        else
            $result = array();

        while ($rs->next())
        {
            $row = $rs->getRow();
            $bizobject = @new $row['classname']($this->project, $row['id']);
            if ($bizobject && $bizobject->id)
            {
                if ($xml)
                    $result .= '<result>' . $bizobject->toXML() . '</result>';
                else
                    $result[] = $bizobject;
            }
        }

        if ($xml)
            $result .= '</resultSet>';

        return $result;
    }

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
        $queryWhere = ' WHERE ' . $indexName . ' IS NOT NULL';
        $groupBy = ' GROUP BY ' . $indexName;

        if ($searchId !== null)
        {
            $nativeQuery = wcmCache::fetch('wcmBizsearch_' . $searchId);
            if ($nativeQuery === false)
            {
                $this->lastErrorMsg = sprintf(getConst(_BIZ_INVALID_ID), $searchId);
                return null;
            }

            if ($nativeQuery['queryWhere'])
                $queryWhere .= ' AND (' . $nativeQuery['queryWhere'] . ')';
        }

        $sql = 'SELECT ' . $indexName . ' FROM ' . $this->tableName . $queryWhere . $groupBy;

        $rawFacetValues = array();
        $index = self::$indexes[$indexName];

        $rs = $this->database->executeQuery($sql, array(), 0, 512/*TODO*/);
        if ($rs === null)
        {
            $this->lastErrorMsg = $this->database->lastErrorMsg;
            return null;
        }

        if ($rs)
        {
            while ($rs->next())
            {
                $row = $rs->getRow();
                if ($row)
                {
                    $rowColumn = $row[$indexName];
                    if ($index['multivalued'])
                    {
                        $separator = $index['separator'];
                        $values = explode($separator, trim($rowColumn, $separator));
                    }
                    else
                    {
                        $values = array($rowColumn);
                    }

                    foreach ($values as $value)
                    {
                        if ($value)
                        {
                            $count = $this->getFacetValueCount($indexName, $value, $searchId);
                            $rawFacetValues[$value] = $count;
                        }
                    }
                }
            }
        }

        return $rawFacetValues;
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
        $queryString = $indexName . ':' . wcmBizsearch::quoteQueryValue($value, true);

        $result = $this->initQuery($queryString, null, $searchId);
        if ($result === null)
            return 0;

        list($nativeQuery, $queryString) = $result;
        $count = $this->database->executeScalar($nativeQuery['queryCount']);
        if ($count === null)
        {
            $this->lastErrorMsg = $this->database->getErrorMsg();
            $this->project->logger->logError($this->lastErrorMsg);
            return 0;
        }

        return $count;
    }
}

?>
