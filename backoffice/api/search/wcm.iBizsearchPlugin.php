<?php
/**
 * Project:     WCM
 * File:        api/search/wcm.iBizsearchPlugin.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

/**
 * The wcmIBizsearchPlugin interface defines the methods needed for a
 * given business search plugin implementation.
 */
interface wcmIBizsearchPlugin
{
    /*
     * GENERAL METHODS
     */

    /**
     * Gets the last error message.
     *
     * All business search plugin interface methods (except simple
     * setters and getters) must set the last error message on failure
     * before returning the special value that indicates that an error
     * has occurred (false or null depending on the method).
     *
     * @return string|null The last error message, or null if none
     */
    function getLastErrorMsg();

    /**
     * Gets the list of known search indexes.
     *
     * @return array The list of known search indexes
     */
    function getIndexList();

    /*
     * INDEXING METHODS
     */

    /**
     * Indexes a business object.
     *
     * @param wcmBizobject $bizobject The business object to index
     *
     * @return bool True on success, false on failure
     */
    function indexBizobject(wcmBizobject $bizobject);

    /**
     * Deindexes a business object.
     *
     * @param wcmBizobject $bizobject The business object to deindex
     *
     * @return bool True on success, false on failure
     */
    function deindexBizobject(wcmBizobject $bizobject);

    /**
     * Deindexes all business objects of a given class.
     *
     * @param string $className The class name of business objects to deindex
     *
     * @return bool True on success, false on failure
     */
    function deindexBizobjects($className);

    /**
     * Gets the count of indexed documents corresponding to business
     * objects of a given class.
     *
     * @param string|null $className The class name of business objects to search (null for any)
     *
     * @return int|null The count of documents, or null on failure
     */
    function getCount($className = null);

    /*
     * SEARCHING METHODS
     */

    /**
     * Initiates a new search using the native query specified by the
     * controlling business search instance.
     *
     * @param string      $searchId        A unique ID for the search
     * @param mixed       $query           The Lucene query or assoc. array of name-value pairs
     * @param string|null $sortingCriteria The sorting criteria (default is null)
     * @param string      $source          BO or FO
     *
     * @return int|null The number of search results, or null on error
     */
    function initSearch($searchId, $query, $sortingCriteria = null, $source = "BO");

    /**
     * Gets the current query.
     *
     * @return string The current query, or null if none
     */
    function getQuery();

    /**
     * Gets the native query initialized by the last call to the
     * initSearch method.
     *
     * @return string The native query
     */
    function getNativeQuery();

    /**
     * Gets a range of documents after a successful call to the
     * initSearch method.
     *
     * If the $xml parameter is true, returns XML of the following form:
     *
     * <resultSet>
     *     <result>
     *         <!-- business object XML representation -->
     *     </result>
     *     ...
     * </resultSet>
     *
     * @param int         $from     The zero-based index of the first document to get
     * @param int         $to       The zero-based index of the last document to get
     * @param string|null $searchId The search ID used in the call to the initSearch method
     * @param bool        $xml      Whether to return XML or an array of business objects
     *                               (default is true)
     *
     * @return mixed|null XML or an array business objects, or null on failure
     */
    function getDocumentRange($from, $to, $searchId = null, $xml = true);

    /*
     * FACETING METHODS
     */

    /**
     * Gets the raw facet values with occurrence counts for a given
     * facet index, possibly limited to a previous search result set.
     *
     * @param string      $indexName The facet index name
     * @param string|null $searchId  The reference search ID to limit values (default is null)
     *
     * @return array|null List of facet values, or null on failure
     */
    function getRawFacetValues($indexName, $searchId = null);

    /**
     * Gets the occurrence count for a given raw facet value, possibly
     * in the result set corresponding to a given reference search ID.
     *
     * @param string      $indexName     The facet index name
     * @param string      $rawFacetValue The raw facet value
     * @param string|null $searchId      The reference search ID (default is null)
     *
     * @return int|null The occurrence count, or null on failure
     */
    function getFacetValueCount($indexName, $rawFacetValue, $searchId = null);

    /*
     * QUERY PARSING METHODS
     */
    // NOT YET IMPLEMENTED (but very soon, so don't remove)

    /**
     * Converts a Lucene query term field name to its corresponding
     * search index name.
     *
     * @param string $field The query term field name to convert
     *
     * @return string The corresponding search index name, or null on failure
     */
    //function convertTermFieldName($field);

    /**
     * Parses a Lucene query to generate a native query.
     *
     * @param string $query Lucene query to parse
     *
     * @return string|null The native query, or null on failure
     */
    //function parseQuery($query);
}
?>