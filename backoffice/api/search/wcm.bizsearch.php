<?php 
/**
 * Project:     WCM
 * File:        api/search/wcm.bizsearch.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */
 
 /**
 * An implementation of the wcmIBizsearch interface that supports
 * search engine-specific plugins.
 */

class wcmBizsearch implements wcmIBizsearch {
    /**
     * The last class-wide error message (if any).
     * @var string
     */

    private static $lastErrorMsg = null;
    
    /**
     * The search plugin configuration.
     * @var SimpleXMLElement
     */

    private static $plugins = null;
    
    /**
     * The current plugin-specific business search instances.
     *
     * @var array
     */

    private static $instances = array();
    
    /**
     * Gets a business search instance using a given plugin. If the
     * $pluginName parameter is null, uses the plugin specified in the
     * WCM configuration.
     *
     * Creates a given plugin-specific business search instance only
     * once per session.
     *
     * @param string|null $pluginName The name of the plugin to use (default is null)
     *
     * @return Ibizsearch|null The business search instance, or null on error
     */

    public static function getInstance($pluginName = null) {
        // Select the plugin to use from the configuration if not given
        if (!$pluginName) {
            $config = wcmConfig::getInstance();
            $pluginName = $config['wcm.search.engine'];
        }
        if (!$pluginName) {
            self::$lastErrorMsg = sprintf(_INVALID_SEARCH_PLUGIN, $pluginName);
            wcmProject::getInstance()->logger->logError(self::$lastErrorMsg);
            return null;
        }
        
        // Create the plugin-specific business search instance if it
        // does not already exist
        if (!isset(self::$instances[$pluginName])) {
            // Load the search plugin configuration if not already loaded
            if (self::$plugins === null) {
                // The "@" in front of the simplexml_load_file call is
                // to prevent it from spitting out unwanted error
                // messages
                $pluginsFile = WCM_DIR.'/xml/searchPlugins.xml';
                $plugins = @simplexml_load_file($pluginsFile);
                if ($plugins === false) {
                    self::$lastErrorMsg = sprintf(_INVALID_SEARCH_PLUGIN_CONFIG, $pluginsFile);
                    wcmProject::getInstance()->logger->logError(self::$lastErrorMsg);
                    return null;
                }
                self::$plugins = $plugins;
            }
            
            // Get the plugin class name from the search plugin
            // configuration
            $nodes = $plugins->xpath('//plugin[@id="'.$pluginName.'"]');
            if (!$nodes || !($className = strval(array_shift($nodes)->className))) {
                self::$lastErrorMsg = sprintf(_INVALID_SEARCH_PLUGIN, $pluginName);
                wcmProject::getInstance()->logger->logError(self::$lastErrorMsg);
                return null;
            }
            
            // Ensure that the class exists
            if (!class_exists($className)) {
                self::$lastErrorMsg = sprintf(_INVALID_SEARCH_PLUGIN_CLASS, $className);
                wcmProject::getInstance()->logger->logError(self::$lastErrorMsg);
                return null;
            }
            
            // Create the plugin and the business search instance that
            // will use it
            self::$instances[$pluginName] = new self( new $className());
        }
        
        return self::$instances[$pluginName];
    }
    
    /**
     * Escapes Lucene special characters in a given query string.
     *
     * @param string $queryString The query string to escape
     *
     * @return string The escaped query string
     */

    public static function escapeQuery($queryString) {
        return preg_replace("/([\"\\[\\]\\\\&|!(){}^~*?:])/", '\\\\$1', $queryString);
    }
    
    /**
     * Unescapes Lucene special characters in a given query string.
     *
     * @param string $queryString The query string to unescape
     *
     * @return string The unescaped query string
     */

    public static function unescapeQuery($queryString) {
        return preg_replace("/\\\\([\"\\[\\]\\\\&|!(){}^~*?:+-])/", '$1', $queryString);
    }
    
    /**
     * Quotes a query string value if it contains whitespace
     * characters.
     *
     * @param string $value  The value to quoted
     * @param bool   $always Whether to quote the value in any case (default is false)
     *
     * @return string The quoted value
     */

    public static function quoteQueryValue($value, $always = false) {
        $value = trim(trim(trim($value), '"'));
        
        $quotedValue = $value;
        if ($always || strpos($value, ' ') !== false)
            $quotedValue = '"'.$value.'"';
            
        return $quotedValue;
    }
    
    /**
     * The business search plugin used.
     *
     * @var wcmIBizsearchPlugin
     */
    private $plugin;
    
    /**
     * Constructor
     *
     * @param wcmIBizsearchPlugin $plugin The plugin to use
     */

    private function __construct(wcmIBizsearchPlugin $plugin) {
        $this->plugin = $plugin;
    }
    
    /**
     * Deindex all bizobjects of a given class.
     *
     * @param string $className The name of the class for which to deindex
     *
     * @return bool False on failure, True on success
     */

    public function deindexBizobjects($className) {
        return $this->plugin->deindexBizobjects($className);
    }
    
    /**
     * Deindex a bizobject
     *
     * @param bizobject $bizobject  Bizobject to remove
     *
     * @return bool False on failure, True on success
     */

    public function deindexBizobject(wcmBizobject $bizobject) {
        return $this->plugin->deindexBizobject($bizobject);
    }
    
    /**
     * Index a bizobject in the search table
     *
     * @param bizobject $bizobject  Bizobject to index
     *
     * @return bool False on failure, True on success
     */

    public function indexBizobject(wcmBizobject $bizobject) {
        return $this->plugin->indexBizobject($bizobject);
    }
    
    /**
     * Returns the last error message
     *
     * @return string The last error message
     */

    public function getLastErrorMsg() {
        if (!($this instanceof wcmIBizsearch))
            return self::$lastErrorMsg;
            
        return $this->plugin->getLastErrorMsg();
    }
    
    /**
     * Count the number of elements indexed for the sourceClass
     *
     * @param string $className class to search for (or null for all)
     *
     * @return int Number of indexed elements of a class
     */

    public function getCount($className = null) {
        return $this->plugin->getCount($className);
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
     * @param string      $source          BO or FO
     *
     * @return int|null The number of search results, or null on error
     */

    public function initSearch($searchId, $query, $sortingCriteria = null, $source = "BO") {
    	
		$search =  $this->plugin->initSearch($searchId, $query, $sortingCriteria, $source);
		//echo "Lucene : <p>". $this->getQuery() ."</p>";
		//echo "TextML : <xmp>". $this->getNativeQuery() ."</xmp>";
		
        return $search;
    }
    
    /**
     * Gets the current search ID.
     *
     * @return string The current search ID, or null if none
     */

    public function getSearchId() {
        return $this->plugin->getSearchId();
    }
    
    /**
     * Gets the current query.
     *
     * @return string The current query, or null if none
     */

    public function getQuery() {
        return $this->plugin->getQuery();
    }
    
    /**
     * Gets the current sorting criteria.
     *
     * @return string The current sorting criteria, or null if none
     */

    public function getSortingCriteria() {
        return $this->plugin->getSortingCriteria();
    }
    
    /**
     * Gets the native query initialized by the last call to the
     * initSearch method.
     *
     * @return string The native query
     */

    public function getNativeQuery() {
        return $this->plugin->getNativeQuery();
    }
    
    /**
     * Get a range of documents after a successful call to the
     * initSearch method.
     *
     * @param int         $from     The index of the first document to get
     * @param int         $to       The index of the last document to get
     * @param string|null $searchId The search ID used in the call to the initSearch method
     * @param bool        $xml      Whether to return an XML or an array of bizobjects
     *
     * @return mixed|null Raw XML or an array containing requested bizobjects
     *                    or null if the initSearch method failed or was not called
     */

    public function getDocumentRange($from, $to, $searchId = null, $xml = true) {
        return $this->plugin->getDocumentRange($from, $to, $searchId, $xml);
        
    }
    
    /**
     * Gets the facet values for a given list of facets.
     *
     * If the $searchId parameter is non-null, only those facet values
     * present in the corresponding result set are returned.
     *
     * The $facets parameter is expected to be an assoc. array of:
     * facet name => maximum number of values for that facet
     *
     * If more than one facet name is given in the $facets parameter,
     * then:
     *
     * - first, the facet values for the given facet names are merged,
     *   keeping only the top-N values for each facet, where N is the
     *   assoc. value for that facet (default is 512; null for no
     *   limit)
     {
     } and
     *
     * - second, only the top-M values are kept in the merged result,
     *   where M is the value of the $maxValues parameter (default is
     *   512; null for no limit).
     *
     * If the $sort parameter is true, the returned list of values is
     * sorted in decreasing order by occurence count.
     *
     * @param array       $facets    The list of facet name => maximum number of values
     * @param string|null $searchId  The reference search ID (default is null)
     * @param int|null    $maxValues The total maximum number of value to return (default is 512)
     *
     * @return array|null Assoc. array of: facet value => wcmBizsearchFacetValue, or null on error
     */

    public function getFacetValues(array $facets, $searchId = null, $maxValues = 512, $sort = true) {
        $facetValues = array();
        
        if ($facets) {
            // Get information about available facets
            $facetInfos = wcmBizsearchFacetValue::getFacetInfos();
            
            foreach ($facets as $facet=>$partialMaxValues) {
                // Facet names are case-insensitive
                $facet = strtolower($facet);
                
                // Attempt to get "small set size" facet values first
                $partialFacetValues = wcmBizsearchFacetValue::getFacetValues($facet);
                if ($partialFacetValues) {
                    foreach ($partialFacetValues as $value=>$facetValue) {
                        $facetValue->count = $this->plugin->getFacetValueCount($facetValue->index, $facetValue->value, $searchId);
                    }
                } elseif (isset($facetInfos[$facet])) {
                    $facetInfo = $facetInfos[$facet];
                    $index = $facetInfo->index;
                    
                    $rawFacetValues = $this->plugin->getRawFacetValues($index, $searchId);
                    if ($rawFacetValues === null)
                        return null;
                        
                    if ($rawFacetValues) {
                        foreach ($rawFacetValues as $value=>$count) {
                            $partialFacetValues[$facet.':'.$value] = new wcmBizsearchFacetValue($facet, $value, null, $index, $facetInfo->searchIndex, $count);
                        }
                    }
                }
                
                if ($partialFacetValues) {
                    // Possibly sort the facets in decreasing order by count
                    if ($sort) {
                        $callback = array('wcmBizsearchFacetValue', 'compareFacetValuesByCount');
                        uasort($partialFacetValues, $callback);
                    }
                    
                    // Keep only the top-$partialMaxValues values
                    if ($partialMaxValues)
                        $partialFacetValues = array_slice($partialFacetValues, 0, $partialMaxValues, true);
                        
                    // Merge facet values
                    $facetValues = array_merge($facetValues, $partialFacetValues);
                }
            }
            
            // Keep only the top-$maxValues values
            if ($maxValues) {
                $facetValues = array_slice($facetValues, 0, $maxValues, true);
            }
        }
        
        return $facetValues;
    }
    
    /**
     * Reindex the content from the business database
     *
     * @param string      $className The class of bizobjects to reindex
     * @param string|null $where     Optional where clause (default is null)
     * @param string|null $orderby   Optional order clause (default is null)
     * @param int|0       $offset    Optional offset of first row (default is 0)
     * @param int|0       $limit     Optional maximum number of returned rows
     *                                   (default is 0 to return all rows)
     *
     * @return boolean True on success, false otherwise
     */

    public function reindexBizobjects($className, $where = null, $orderBy = null, $offset = 0, $limit = null) {
        $bizobject = new $className($this->project);
        if ($bizobject->beginEnum($where, $orderBy, $offset, $limit)) {
        
            while ($bizobject->nextEnum()) {
                echo "$bizobject->id : $bizobject->title \n";
                $this->indexBizobject($bizobject);
                $bizobject->updateMustGenerate(0);
            }
            
            $bizobject->endEnum();
        }
        
        return true;
    }
}
?>
