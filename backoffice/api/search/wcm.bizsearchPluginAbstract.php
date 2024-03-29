<?php 
/**
 * Project:     WCM
 * File:        api/search/wcm.bizsearchPluginAbstract.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */
 
 /**
 * The abstract wcmBizsearchPluginAbstract class implements methods
 * common to all business search plugin implementations.
 */

abstract class wcmBizsearchPluginAbstract implements wcmIBizsearchPlugin {
    /**
     * The list of known indexes.
     * @var array
     */

    protected static $indexes = array(
        /*
         * This indexes has been added by relaxnews to upgrade the search terms
         */
        'photoHeight'=>array('kind'=>'number', 'multivalued'=>false, 'xpaths'=>array("/*/photos/item/formats/original/height")),
        'photoWidth'=>array('kind'=>'number', 'multivalued'=>false, 'xpaths'=>array("/*/photos/item/formats/original/width")), 'referentclass'=>array('kind'=>'word', 'multivalued'=>false, 'xpaths'=>array("/*/referentClass")),
        'referentid'=>array('kind'=>'number', 'multivalued'=>false, 'xpaths'=>array("/*/referentId")),
        'listids'=>array('kind'=>'word', 'multivalued'=>false, 'xpaths'=>array("/*/listIds/item")), 'channelids'=>array('kind'=>'word', 'multivalued'=>false, 'xpaths'=>array("/*/channelIds/item")), 'event_startdate'=>array('kind'=>'date', 'multivalued'=>false, 'xpaths'=>array("/event/startDate")), 'event_enddate'=>array('kind'=>'date', 'multivalued'=>false, 'xpaths'=>array("/event/endDate")), 'forecast_startdate'=>array('kind'=>'date', 'multivalued'=>false, 'xpaths'=>array("/forecast/startDate")), 'forecast_enddate'=>array('kind'=>'date', 'multivalued'=>false, 'xpaths'=>array("/forecast/endDate")),
        /*
         *
         */
        'id'=>array('kind'=>'number', 'multivalued'=>false, 'xpaths'=>array("/id")), 'classname'=>array('kind'=>'string', 'multivalued'=>false, 'xpaths'=>array("/className")), 'createdat'=>array('kind'=>'date', 'multivalued'=>false, 'xpaths'=>array("/createdAt")), 'createdby'=>array('kind'=>'number', 'multivalued'=>false, 'xpaths'=>array("/createdBy")), 'modifiedat'=>array('kind'=>'date', 'multivalued'=>false, 'xpaths'=>array("/modifiedAt")), 'modifiedby'=>array('kind'=>'number', 'multivalued'=>false, 'xpaths'=>array("/modifiedBy")), 'mustgenerate'=>array('kind'=>'number', 'multivalued'=>false, 'xpaths'=>array("/mustGenerate")), 'workflowstate'=>array('kind'=>'string', 'multivalued'=>false, 'xpaths'=>array("/workflowState")), 'fulltext'=>array('kind'=>'word', 'multivalued'=>true, 'separator'=>' ', 'xpaths'=>array("//title", "//fulltext", "//semanticData", "//abstract", "//subtitle", "//suptitle", "//description", "//text")), 'fulltext_tokens'=>array('kind'=>'word', 'multivalued'=>true, 'separator'=>' ', 'xpaths'=>array("//title", "//fulltext", "//semanticData", "//abstract", "//subtitle", "//suptitle")), 'siteid'=>array('kind'=>'number', 'multivalued'=>false, 'xpaths'=>array("/siteId")), 'channelid'=>array('kind'=>'number', 'multivalued'=>false, 'xpaths'=>array("/channelId")), 'publicationdate'=>array('kind'=>'date', 'multivalued'=>false, 'xpaths'=>array("/publicationDate")), 'title'=>array('kind'=>'word', 'multivalued'=>false, 'xpaths'=>array("/title")), 'author'=>array('kind'=>'word', 'multivalued'=>false, 'xpaths'=>array("/author")), 'title_sort'=>array('kind'=>'string', 'multivalued'=>false, 'xpaths'=>array("/title")), 'hitcount'=>array('kind'=>'number', 'multivalued'=>false, 'xpaths'=>array("/hitCount")), 'ratingcount'=>array('kind'=>'number', 'multivalued'=>false, 'xpaths'=>array("/ratingCount")), 'ratingtotal'=>array('kind'=>'number', 'multivalued'=>false, 'xpaths'=>array("/ratingTotal")), 'ratingvalue'=>array('kind'=>'number', 'multivalued'=>false, 'xpaths'=>array("/ratingValue")), 'entity_on'=>array('kind'=>'word', 'multivalued'=>true, 'separator'=>' ', 'xpaths'=>array("/semanticData/entitiesON/ON")), 'entity_pn'=>array('kind'=>'word', 'multivalued'=>true, 'separator'=>' ', 'xpaths'=>array("/semanticData/entitiesPN/PN")), 'entity_gl'=>array('kind'=>'word', 'multivalued'=>true, 'separator'=>' ', 'xpaths'=>array("/semanticData/entitiesGL/GL")), 'list_pn'=>array('kind'=>'string', 'multivalued'=>true, 'separator'=>'|', 'xpaths'=>array("/semanticData/entitiesPN/PN")), 'list_on'=>array('kind'=>'string', 'multivalued'=>true, 'separator'=>'|', 'xpaths'=>array("/semanticData/entitiesON/ON")), 'list_gl'=>array('kind'=>'string', 'multivalued'=>true, 'separator'=>'|', 'xpaths'=>array("/semanticData/entitiesGL/GL")), 'category'=>array('kind'=>'word', 'multivalued'=>true, 'separator'=>' ', 'xpaths'=>array("/semanticData/categories/category")), 'list_category'=>array('kind'=>'string', 'multivalued'=>true, 'separator'=>'|', 'xpaths'=>array("/semanticData/categories/category")), 'concept'=>array('kind'=>'word', 'multivalued'=>true, 'separator'=>' ', 'xpaths'=>array("/semanticData/concepts/concept")), 'list_concept'=>array('kind'=>'string', 'multivalued'=>true, 'separator'=>'|', 'xpaths'=>array("/semanticData/concepts/concept")), 'tag'=>array('kind'=>'word', 'multivalued'=>true, 'separator'=>' ', 'xpaths'=>array("/tag-categories/tags/tag")), 'list_tag'=>array('kind'=>'string', 'multivalued'=>true, 'separator'=>'|', 'xpaths'=>array("/tag-categories/tags/tag")), 'source'=>array('kind'=>'word', 'multivalued'=>false, 'xpaths'=>array("/source")), 'list_source'=>array('kind'=>'string', 'multivalued'=>false, 'xpaths'=>array("/source")), 'username'=>array('kind'=>'word', 'multivalued'=>false, 'xpaths'=>array("/username")), 'country'=>array('kind'=>'word', 'multivalued'=>false, 'xpaths'=>array("/country")), 'email'=>array('kind'=>'word', 'multivalued'=>false, 'xpaths'=>array("/email")), 'publication'=>array('kind'=>'string', 'multivalued'=>false, 'xpaths'=>array("//article/publication")), 'publication_year'=>array('kind'=>'number', 'multivalued'=>false, 'xpaths'=>array("//article/publicationYear")));
        
    /**
     * Assoc. array of index alias => index name
     *
     * @var array
     */

    protected static $indexAliases = null;
    
    /**
     * The current project.
     *
     * @var wcmProject
     */
    protected $project = null;
    
    /**
     * The last error message, if any.
     *
     * @var string
     */
    protected $lastErrorMsg = null;
    
    /**
     * The current search ID.
     *
     * @var string
     */
    protected $searchId = null;
    
    /**
     * Search parameters (assoc. of name => value).
     *
     * @var array
     */
    protected $parameters = array();
    
    /**
     * Query string (Lucene query string syntax).
     *
     * @var string
     */
    protected $queryString = null;
    
    /**
     * The stack of sub-queries while converting a query.
     *
     * @var array
     */
    protected $queryStack;
    
    /**
     * Constructor
     */

    protected function __construct() {
        $this->project = wcmProject::getInstance();
    }
    
    /**
     * Returns the last error message
     *
     * @return string The last error message
     */

    public final function getLastErrorMsg() {
        return $this->lastErrorMsg;
    }
    
    /**
     * Gets the list of search indexes.
     *
     * @return array List of search indexes
     */

    public function getIndexList() {
        return self::$indexes;
    }
    
    /**
     * Gets the list of date indexes
     *
     * @return array List of date indexes
     */

    public function getDateIndexList() {

        static $dateIndexes = null;
        if (null === $dateIndexes) {
            $dateIndexes = array();
            foreach ($this->getIndexList() as $indexName=>$indexInfos) {
                if ($indexInfos['kind'] === 'date') {
                    $dateIndexes[] = $indexName;
                }
            }
        }
        return $dateIndexes;
    }
    
    /**
     * Gets the list of date time indexes
     *
     * @return array List of datetime indexes
     */

    public function getDateTimeIndexList() {

        static $dateIndexes = null;
        if (null === $dateIndexes) {
            $dateIndexes = array();
            foreach ($this->getIndexList() as $indexName=>$indexInfos) {
                if ($indexInfos['kind'] === 'datetime') {
                    $dateIndexes[] = $indexName;
                }
            }
        }
        return $dateIndexes;
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

    protected function initQueryBO($queryString = null, $parameters = null) {
    	
        // If a query string was not given and $parameters is
        // non-empty, generate a Lucene-syntax query string from any
        // parameters referring to search indexes
        if ($queryString === null && $parameters) {
            // Get the list of indexes
            $indexList = $this->getIndexList();
            
            // Convert each index-specific parameter into a query term
            $terms = array();
            
            foreach ($parameters as $name=>$value) {
                if ($name && $value) {
                    if ($name == 'classname') {
                        $values = explode(',', $value);
                        if (count($values) > 1) {
                            $value = '('.implode(' OR ', $values).')';
                        }
                        $terms[] = $name.':'.$value;
                    }
                    else if (isset($indexList[$name])) {
                        $terms[] = $name.':'.$value;
                    }
                    
                }
            }
            
            // Build the query string by joining the query terms with
            // an 'AND' operator
            $queryString = implode(' AND ', $terms);
        }
        
        // Remove leading and trailing whitespace, as well as any
        // trailing backslash as it causes the Lucene parser to
        // trigger a "notice"
        $queryString = trim($queryString, " \t\\");
        
        // apply security filter
        $filter = $this->getSecurityFilter();
        if ($filter) {
            if ($queryString)
                $queryString = '('.$queryString.') AND '.$filter;
            else
                $queryString = $filter;
        }
         
        //echo ($queryString);
        //exit();
        
        // Parse the given query string and search parameters to
        // obtain a Zend_Search_Lucene_Search_Query object
        // representation of the query
        return $this->parseQuery($queryString, $parameters);
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

    protected function initQueryFO($queryString = null, $parameters = null) {
        $accountPermission = false;
        
        // If a query string was not given and $parameters is
        // non-empty, generate a Lucene-syntax query string from any
        // parameters referring to search indexes
        if ($queryString === null && $parameters) {
            // Get the list of indexes
            $indexList = $this->getIndexList();
            
            // Convert each index-specific parameter into a query term
            $terms = array();
            
            foreach ($parameters as $name=>$value) {
                if ($name && $value) {
                    if ($name == 'classname') {
                        $values = explode(',', $value);
                        if (count($values) > 1) {
                            $value = '('.implode(' OR ', $values).')';
                        }
                        $terms[] = $name.':'.$value;
                    }
                    else if (isset($indexList[$name])) {
                        $terms[] = $name.':'.$value;
                    }
                    
                }
                /* Added by Olivier */
                if ($name == "accountPermission")
                    $accountPermission = $value;
            }
            
            // Build the query string by joining the query terms with
            // an 'AND' operator
            $queryString = implode(' AND ', $terms);
        }
        
        // Remove leading and trailing whitespace, as well as any
        // trailing backslash as it causes the Lucene parser to
        // trigger a "notice"
        $queryString = trim($queryString, " \t\\");
        
        if ($accountPermission) {
            $permissions = $this->getSearchAccountPermissions();
            if ($permissions) {
            	if ($queryString) {
                	$queryString = '(('.$queryString.') AND ('.$permissions.') AND NOT(listids:(252)))';
                    
					$externalFoldersPermissions = $this->getSearchAccountExternalFoldersPermissions($queryString);
					//$queryString .= $externalFoldersPermissions;
					$queryString = $externalFoldersPermissions;
                }
                else
                    $queryString = $permissions;
            }
        }
         
        //echo ($queryString);
        //exit();
        
        // Parse the given query string and search parameters to
        // obtain a Zend_Search_Lucene_Search_Query object
        // representation of the query
        return $this->parseQuery($queryString, $parameters);
    }
    
    /**
     * Gets the current search ID.
     *
     * @return string|null The current search ID, or null if none
     */

    public function getSearchId() {
        return $this->searchId;
    }
    
    /**
     * Gets the current query.
     *
     * @return string The current query, or null if none
     */

    public final function getQuery() {
        return $this->queryString;
    }
    
    /**
     * Gets the current sorting criteria.
     *
     * @return string The current sorting criteria, or null if none
     */

    public function getSortingCriteria() {
        return isset($this->parameters['sortedby']) ? $this->parameters['sortedby'] : null;
    }
    
    /**
     * This function is a preg_replace_callback callback method for date format
     * it replace all chars not allowed by the Zend Lucene parser in other chars
     *
     * @params Array $matches
     * @return String String to replace
     */

    public static function replace_date_callback($matches) {
        $query = $matches[1].':';
        
        if (3 === count($matches)) {
            $query .= 'm'.$matches[2];
        } elseif (7 === count($matches)) {
            $query .= $matches[4].'x'.$matches[5].'x'.$matches[6];
        } else {
        
            $query .= $matches[7];
            if ('*' === $matches[8]) {
                $query .= '*';
            } else {
                $query .= $matches[10].'x'.$matches[11].'x'.$matches[12];
            }
            $query .= ' TO ';
            if ('*' === $matches[13]) {
                $query .= '*';
            } else {
                $query .= $matches[15].'x'.$matches[16].'x'.$matches[17];
            }
            $query .= $matches[18];
        }
        return $query;
    }
    
    /**
     * This function is a preg_replace_callback callback method for datetime format
     * it replace all chars not allowed by the Zend Lucene parser in other chars
     *
     * @params Array $matches
     * @return String String to replace
     */

    public static function replace_datetime_callback($matches) {
        $query = $matches[1].':';
        if (3 === count($matches)) {
            $query .= 'm'.$matches[2];
        } elseif (7 === count($matches)) {
            $query .= $matches[4].'x'.$matches[5].'x'.$matches[6];
        } elseif (10 === count($matches)) {
            $query .= $matches[4].'x'.$matches[5].'x'.$matches[6].'T'.$matches[7].'x'.$matches[8].'x'.$matches[9];
        } else {
            $query .= $matches[10];
            if ('*' === $matches[11]) {
                $query .= '*';
            } else {
                $query .= $matches[13].'x'.$matches[14].'x'.$matches[15];
                if ('' !== $matches[16])
                    $query .= 'T'.$matches[16].'x'.$matches[17].'x'.$matches[18];
            }
            $query .= ' TO ';
            if ('*' === $matches[19]) {
                $query .= '*';
            } else {
                $query .= $matches[21].'x'.$matches[22].'x'.$matches[23];
                if ('' !== $matches[24])
                    $query .= 'T'.$matches[24].'x'.$matches[25].'x'.$matches[26];
            }
            $query .= $matches[27];
        }
        return $query;
    }
    
    /**
     * Parses a Lucene-syntax query string or an assoc. array of
     * search parameters to generate a native query.
     *
     * If $queryString is null and $parameters is non-empty, generates
     * a Lucene-syntax query string from any parameters in $parameters
     * referring to search indexes.
     *
     * @param string|null $queryString Lucene-syntax query string
     * @param array|null  $parameters  Assoc. of search parameters
     *
     * @return array|null The native query and updated query string (in that order), or null on error
     */

    protected function parseQuery($queryString = null, $parameters = null) {
        // Attempt to parse the query string in two passes:
        //
        // Pass 1: Attempt to parse the query string as is.
        //
        // Pass 2: If an error occurred during the first pass, attempt
        // to parse the query string by double-quoting each word.
        //
        $query = null;
        $error = null;
        while ($query === null) {
            try {
                // We want exceptions
                Zend_Search_Lucene_Search_QueryParser::dontSuppressQueryParsingExceptions();
                
                // Our query strings can contain text or numbers in UTF-8
                Zend_Search_Lucene_Analysis_Analyzer::setDefault( new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8Num_CaseInsensitive);
                
                // HACK[
                // Work around a limitation in the Zend query parser
                $dateFields = $this->getDateIndexList();
                $hackRE = '/('.implode('|', $dateFields).'):(?:\\\\?-([0-9]+)|(")?(\d{4})-(\d{2})-(\d{2})(?(3)")|([[{])(\*|(")?(\d{4})-(\d{2})-(\d{2})(?(9)")) TO (\*|(")?(\d{4})-(\d{2})-(\d{2})(?(14)"))([}\]]))/i';
                $hackedQueryString = preg_replace_callback($hackRE, __CLASS__.'::replace_date_callback', $queryString);
                
                $dateTimeFields = $this->getDateTimeIndexList();
                $hackRE = '/('.implode('|', $dateTimeFields).'):(?:\\\\?-([0-9]+)|(")?(\d{4})-(\d{2})-(\d{2})(?:T(\d{2}):(\d{2}):(\d{2}))?(?(3)")|([[{])(\*|(")?(\d{4})-(\d{2})-(\d{2})(?:T(\d{2}):(\d{2}):(\d{2}))?(?(12)")) TO (\*|(")?(\d{4})-(\d{2})-(\d{2})(?:T(\d{2}):(\d{2}):(\d{2}))?(?(20)"))([}\]]))/i';
                $hackedQueryString = preg_replace_callback($hackRE, __CLASS__.'::replace_datetime_callback', $hackedQueryString);
                // HACK]
                
                // Parse the query string
                //$query = Zend_Search_Lucene_Search_QueryParser::parse($queryString, 'utf-8');
                $query = Zend_Search_Lucene_Search_QueryParser::parse($hackedQueryString, 'utf-8');
                $error = null;
            }
            catch(Zend_Search_Lucene_Search_QueryParserException $e) {
                // If this is pass 2, that's it
                if ($error !== null)
                    break;
                    
                // Pass 1 failed, so on to pass 2
                $error = $e;
                
                // The query string does not appear to be a valid
                // Lucene query, so we quote each word and try again
                $words = preg_split('/\\s+/', $queryString);
                if ($words) {
                    foreach ($words as & $word) {
                        $word = '"'.trim(trim(trim($word), '"')).'"';
                    }
                    
                    $queryString = implode(' ', $words);
                }
            }
        }
        
        // If an error occurred during the second pass, just log it
        if ($error) {
            $this->lastErrorMsg = $error->getMessage();
            $this->project->logger->logError('query syntax error: '.$this->lastErrorMsg);
            return null;
        }
        
        // Convert the query object
        $nativeQuery = $this->convertQuery($query);
        if ($nativeQuery === null)
            return null;
            
        return array($nativeQuery, $queryString);
    }
    
    /**
     * Converts a given Zend_Search_Lucene_Search_Query object into a
     * native query.
     *
     * This method dispatches to a method named
     * 'convertQuery_<queryType>' based on the query object class.
     *
     * The concrete subclass is expected to implement the query object
     * class-specific methods to build the native query based on the
     * nature of the query object.
     *
     * However, some of the conversion methods have a default
     * implementation in this abstract class and need not be
     * overridden unless something specific needs to be done, namely
     * (which see):
     *
     *     convertQuery_Fuzzy
     *     convertQuery_Insignificant
     *     convertQuery_Term
     *
     * The $context parameter can be used to pass in contextual
     * information.
     *
     * @param Zend_Search_Lucene_Search_Query $query   The query object
     * @param array|null                      $context The current context
     *
     * @return mixed The native query
     */

    protected function convertQuery(Zend_Search_Lucene_Search_Query $query, $context = null) {
        // Clear the query stack
        $this->queryStack = array();
        
        // Delegare to the query-specific conversion method
        $parts = explode('_', get_class($query));
        $functionName = "convertQuery_".array_pop($parts);
        return $this->$functionName($query, $context);
    }
    
    /**
     * Partitions and converts (by calling the convertQuery method)
     * the optional, required, and prohibited sub-queries of a given
     * boolean query object.
     *
     * @param Zend_Search_Lucene_Search_Query_Boolean $query   The boolean query object
     * @param array|null                              $context The current context
     *
     * @return array An assoc of 'optional', 'required', and 'prohibited' sub-queries
     */

    protected function convertQuery_BooleanHelper(Zend_Search_Lucene_Search_Query_Boolean $query, $context = null) {
        $optionalSubQueries = array();
        $requiredSubQueries = array();
        $prohibitedSubQueries = array();
        
        // A boolean query consists of one or more sub-queries, each
        // with a corresponding sign indicating whether it is optional
        // (null), required (true), or prohibited (false)
        $subQueries = $query->getSubqueries();
        if ($subQueries) {
            // Push the current query onto the query stack
            array_push($this->queryStack, $query);
            
            $signs = $query->getSigns();
            foreach ($subQueries as $subQuery) {
                // Convert the sub-query to its native equvalent
                $subQuery = $this->convertQuery($subQuery, $context);
                
                // Partition the subQueries according to their sign:
                //  foo => optional (null)
                // +foo => required (true)
                // -foo => prohibited (false)
                $sign = array_shift($signs);
                if ($sign === null)
                    $optionalSubQueries[] = $subQuery;
                elseif ($sign)
                    $requiredSubQueries[] = $subQuery;
                else
                    $prohibitedSubQueries[] = $subQuery;
            }
            
            // Pop the query stack
            array_pop($this->queryStack);
        }
        
        return array('optional'=>$optionalSubQueries, 'required'=>$requiredSubQueries, 'prohibited'=>$prohibitedSubQueries, );
    }
    
    /**
     * Partitions and converts (by calling the convertTerm method) the
     * optional, required, and prohibited terms of a given multi-term
     * query object.
     *
     * @param Zend_Search_Lucene_Search_Query_MultiTerm $query   The multi-term query object
     * @param array|null                                $context The current context
     *
     * @return array An assoc of 'optional', 'required', and 'prohibited' terms
     */

    protected function convertQuery_MultiTermHelper(Zend_Search_Lucene_Search_Query_MultiTerm $query, $context = null) {
        $optionalTerms = array();
        $requiredTerms = array();
        $prohibitedTerms = array();
        
        // A multi-term query consists of one or more terms, each with
        // a corresponding sign - very much like a boolean query which
        // consists of one or more sub-queries, each with a
        // corresponding sign - can you say *abstraction*? Or lack
        // thereof as far as Zend is concerned? :(
        $terms = $query->getTerms();
        if ($terms) {
            // Push the current query onto the query stack
            array_push($this->queryStack, $query);
            
            $signs = $query->getSigns();
            foreach ($terms as $term) {
                // Convert the term to its native equvalent
                $term = $this->convertTerm($term, $context);
                
                // Partition the terms according to their sign:
                //  foo => optional (null)
                // +foo => required (true)
                // -foo => prohibited (false)
                $sign = ($signs ? array_shift($signs) : null);
                if ($sign === null)
                    $optionalTerms[] = $term;
                elseif ($sign)
                    $requiredTerms[] = $term;
                else
                    $prohibitedTerms[] = $term;
            }
            
            // Pop the query stack
            array_pop($this->queryStack);
        }
        
        return array('optional'=>$optionalTerms, 'required'=>$requiredTerms, 'prohibited'=>$prohibitedTerms, );
    }
    
    /**
     * Converts a Lucene fuzzy query object to its native equvalent.
     *
     * The default implementation simply converts the associated term
     * without the fuzziness factor by calling the convertTerm method.
     *
     * @param Zend_Search_Lucene_Search_Query_Fuzzy $query   The fuzzy query object
     * @param array|null                            $context The current context
     *
     * @return string The native equvalent of the fuzzy query object
     */

    protected function convertQuery_Fuzzy(Zend_Search_Lucene_Search_Query_Fuzzy $query, $context = null) {
        // The fuzzy query object doesn't want to give up its term, so
        // we'll just create a term from its string representation
        list($field, $value) = explode(':', (string) $query);
        if (!$value) {
            $value = $field;
            $field = null;
        }
        list($text, $similarity) = explode('~', $value);
        if (!$similarity) {
            $similarity = '0.0';
        }
        $term = new Zend_Search_Lucene_Index_Term($text, $field);
        
        // We assume there's no built-in support for fuzzy searches by
        // default. Doing it manually would be too heavy, so we just
        // search for the term as-is
        array_push($this->queryStack, $query);
        $nativeQuery = $this->convertTerm($term, $context);
        array_pop($this->queryStack);
        
        return $nativeQuery;
    }
    
    /**
     * Converts a Lucene insignificant query object to its native
     * equvalent.
     *
     * The default implementation simply returns an empty string.
     *
     * @param Zend_Search_Lucene_Search_Query_Insignificant $query   The insignificant query object
     * @param array|null                                    $context The current context
     *
     * @return string The native equvalent of the insignificant query object
     */

    protected function convertQuery_Insignificant(Zend_Search_Lucene_Search_Query_Insignificant $query, $context = null) {
        // An insignificant query is one that is meaningless to Lucene
        // and would not yield any results, so we'll just behave as if
        // nothing had been given
        return '';
    }
    
    /**
     * Extracts the field, term texts, and proximity factor (slop) of
     * a Lucene phrase query.
     *
     * @param Zend_Search_Lucene_Search_Query_Phrase $query   The phrase query object
     * @param array|null                             $context The current context
     *
     * @return array An assoc. array of 'field' => string, 'texts' => array, and 'slop' => int
     */

    protected function convertQuery_PhraseHelper($query, $context = null) {
        $field = '';
        $texts = array();
        
        // A phrase consists of one or more terms, possibly with an
        // associated proximity factor
        $terms = $query->getTerms();
        if ($terms) {
            // All terms in a phrase have the same field
            $field = $terms[0]->field;
            
            // Convert and collect each term's text
            foreach ($terms as $term) {
                $texts[] = $term->text;
            }
        }
        
        // Extract the proximity factor
        $slop = intval($query->getSlop());
        
        return array('field'=>$field, 'texts'=>$texts, 'slop'=>$slop, );
    }
    
    /**
     * Converts a range query object to its native equvalent.
     *
     * This method need not be overridden unless something specific
     * needs to be done since it essentially creates a term of the
     * form <lower-term>..<upper-term> and then converts it.
     *
     * @param Zend_Search_Lucene_Search_Query $query   The range query object
     * @param array|null                      $context The current context
     *
     * @return string The native equvalent
     */

    protected function convertQuery_Range($query, $context = null) {
        // Push the current query onto the query stack
        array_push($this->queryStack, $query);
        
        // The convertTerm method handles ranges for us
        $text = ($query->getLowerTerm() ? $query->getLowerTerm()->text : '').'..'.($query->getUpperTerm() ? $query->getUpperTerm()->text : '');
        $term = new Zend_Search_Lucene_Index_Term($text, $query->getField());
        $nativeQuery = $this->convertTerm($term, $context);
        
        // Pop the query stack
        array_pop($this->queryStack);
        
        return $nativeQuery;
    }
    
    /**
     * Converts a Lucene term query object to its native equvalent.
     *
     * This method need not be overridden unless something specific
     * needs to be done. It is equivalent to:
     *
     *     return $this->convertTerm($query->getTerm(), $context);
     *
     * @param Zend_Search_Lucene_Search_Query_Term $query   The term query object
     * @param array|null                           $context The current context
     *
     * @return string The native equvalent
     */

    protected function convertQuery_Term(Zend_Search_Lucene_Search_Query_Term $query, $context = null) {
        // A term query just holds... er... a query term
        array_push($this->queryStack, $query);
        $nativeQuery = $this->convertTerm($query->getTerm(), $context);
        array_pop($this->queryStack);
        
        return $nativeQuery;
    }
    
    /**
     * Converts a Lucene index term object to its native equvalent.
     *
     * This method need not be overridden unless something specific
     * needs to be done. It is equivalent to:
     *
     *     $nativeText = $this->convertTermText($term->field, $term->text, $context);
     *     return $this->convertTermField($term->field, $nativeText, $context);
     *
     * @param Zend_Search_Lucene_Index_Term $term    The index term object
     * @param array|null                    $context The current context
     *
     * @return string The native equvalent of the index term object
     */

    protected function convertTerm(Zend_Search_Lucene_Index_Term $term, $context = null) {
        $field = $term->field;
        ;
        
        // Convert the term's text to its native equvalent
        $nativeText = $this->convertTermText($field, $term->text, $context);
        
        // Convert the term's field to its native equvalent, including
        // the native text in the result
        return $this->convertTermField($field, $nativeText, $context);
    }
    
    /**
     * Converts a Lucene field name or alias to its canonical form.
     *
     * @param string $field The Lucene field name
     *
     * @return string The canonical form of the Lucene field name or alias
     */

    protected function convertFieldNameOrAlias($field) {
        // Field names are case-insensitive
        $field = strtolower($field);
        
        $indexAliases = $this->getIndexAliasList();
        if (isset($indexAliases[$field]))
            $field = $indexAliases[$field];
            
        return $field;
    }
    
    /**
     * Gets the list of index aliases.
     *
     * @return array The list of index aliases
     */

    protected function getIndexAliasList() {
        if (self::$indexAliases === null) {
            self::$indexAliases = array();
            
            $config = wcmConfig::getAssocInstance();
            $indexes = $config['wcm']['search']['indexes']['index'];
            if ($indexes) {
                if (!is_array($indexes))
                    $indexes = array($indexes);
                    
                foreach ($indexes as $index) {
                    // Index names are case-insensitive
                    $name = strtolower($index['name']);
                    
                    $aliases = $index['aliases']['alias'];
                    if ($aliases) {
                        if (!is_array($aliases))
                            $aliases = array($aliases);
                            
                        foreach ($aliases as $alias) {
                            // Alias names are case-insensitive
                            self::$indexAliases[strtolower($alias)] = $name;
                        }
                    }
                }
            }
        }
        
        return self::$indexAliases;
    }

    private function getAccountPermissions() {
        $session = wcmSession::getInstance();
        
        $userId = $session->userId;
        $account = new account();
        $account->refreshByWcmUser($userId);
        
        return $account->getLucenePermissions();
    }
    
    public function getSearchAccountPermissions() {
    	$session = wcmSession::getInstance();
        
        $userId = $session->userId;
        $site = $session->getSite();
        $account = new account();
        $account->refreshByWcmUser($userId);
        
        return $account->getSearchLucenePermissions($site->id);
    }
    
    public function getSearchAccountExternalFoldersPermissions($queryString) {
    	$session = wcmSession::getInstance();
        $userId = $session->userId;
        $site = $session->getSite();
        $account = new account();
        $account->refreshByWcmUser($userId);
        $externalFoldersPermissions = "";
        
        $posDebutWorkflowstate = strpos($queryString," AND workflowstate");
        $queryString = "workflowstate:published AND (".substr($queryString,0,$posDebutWorkflowstate).substr($queryString,$posDebutWorkflowstate+28).")";
        
        $posDebutPublicationDate = strpos($queryString," AND publicationdate");
        $posFinPublicationDate = strpos($queryString,"]",$posDebutPublicationDate);
        $queryString = substr($queryString,$posDebutPublicationDate+5,$posFinPublicationDate-$posDebutPublicationDate-4)." AND ".substr($queryString,0,$posDebutPublicationDate).substr($queryString,$posFinPublicationDate+1);
        
        $posDebutFulltext = strpos($queryString," AND fulltext");
        if($posDebutFulltext !== false) {
	        $posFinFulltext = strpos($queryString,")",$posDebutFulltext);
	        $queryString = substr($queryString,$posDebutFulltext+5,$posFinFulltext-$posDebutFulltext-4)." AND ".substr($queryString,0,$posDebutFulltext).substr($queryString,$posFinFulltext+1);
        }
        
        $posDebutChannelids = strpos($queryString," AND channelids");
        if($posDebutChannelids !== false) {
	        $posFinChannelids = strpos($queryString,")",$posDebutChannelids);
	        $queryString = substr($queryString,$posDebutChannelids+5,$posFinChannelids-$posDebutChannelids-5)." AND ".substr($queryString,0,$posDebutChannelids).substr($queryString,$posFinChannelids);
        }
        
        if(strpos($queryString,"AND folderid") === false) {
	        $folder = new folder();
	        $folderUniverse = $folder->getFoldersMultiUniverse($site->id);
	        foreach($folderUniverse as $folderUnivers) {
				$folder->refresh($folderUnivers);
				$where = "id = '".$folderUnivers."' AND workflowstate = 'published'";
				if ($folder->beginEnum($where, "type DESC, rank ASC")) {
				    if ($folder->nextEnum()) {
				    	$site = new site();
		        		$site->refresh($folder->siteId);
		        		$permissions = $account->getSearchLucenePermissions($site->id);
		        		if($permissions != "siteId:-1") {
		        			$externalFoldersPermissions .= " OR (siteid:".$site->id." AND (".$permissions.") AND NOT(listids:(252)))";
		        		}
				    }
				}
	        }
        }
        
        $queryString = substr($queryString,0,strlen($queryString)-1).$externalFoldersPermissions.")";
        
        return $queryString;
    }
    
    /**
     * Computes and returns the security filter to apply to every
     * search to ensure that the user cannot find unauthorized sites,
     * bizclasses or workflow states.
     *
     * Note: this filter is computed once per session
     *
     * @return string The security filter
     */

    private function getSecurityFilter() {
        $session = wcmSession::getInstance();
        if (!isset($_SESSION['wcm_securityFilter'])) {
            $filter = null;
            
            if (!$session->isAdministrator()) {
                // Get allowed sites
                $allowedSites = null;
                $sites = bizobject::getBizobjects('site');
                foreach ($sites as $site) {
                    if ($session->isAllowed($site, wcmPermission::P_READ)) {
                        $allowedSites[] = $site->id;
                    }
                }
                
                if ($allowedSites && count($allowedSites) != count($sites)) {
                    $filter .= ' (siteid:'.implode(' OR siteid:', $allowedSites).') ';
                }
                
                // Get allowed bizclasses
                $allowedBizclasses = null;
                $bizlogic = new wcmBizlogic();
                $bizclasses = $bizlogic->getBizclasses();
                foreach ($bizclasses as $bizclass) {
                    if ($session->isAllowed($bizclass, wcmPermission::P_READ)) {
                        $allowedBizclasses[] = $bizclass->className;
                    }
                }
                
                if ($allowedBizclasses && count($allowedBizclasses) != count($bizclasses)) {
                    if ($filter)
                        $filter .= ' AND ';
                    $filter .= ' (classname:'.implode(' OR classname:', $allowedBizclasses).') ';
                }
                
                // Get allowed workflow states
                $allowedWorkflowStates = array();
                $workflowManager = $this->project->workflowManager;
                $workflowStates = $workflowManager->getWorkflowStates();
                foreach ($workflowStates as $workflowState) {
                    if ($session->isAllowed($workflowState, wcmPermission::P_READ))
                        $allowedWorkflowStates[] = $workflowState->code;
                }
                
                if ($allowedWorkflowStates && count($allowedWorkflowStates) != count($workflowStates)) {
                    if ($filter)
                        $filter .= ' AND ';
                    $filter .= ' (workflowstate:'.implode(' OR workflowstate:', $allowedWorkflowStates).') ';
                }
                
            }
            
            $_SESSION['wcm_securityFilter'] = $filter;
        }
        
        return $_SESSION['wcm_securityFilter'];
    }
}
?>
