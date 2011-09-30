<?php
/**
 * Project:     WCM
 * File:        api/search/wcm.iBizsearch.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

/**
 * The wcmIBizsearch interface defines the methods needed for a given
 * business search implementation.
 */
interface wcmIBizsearch
{
    /*
     * GENERAL METHODS
     */

    /**
     * Gets the last error message.
     *
     * All business search interface methods (except simple setters
     * and getters) must set the last error message on failure before
     * returning the special value that indicates that an error has
     * occurred (false or null depending on the method).
     *
     * @return string|null The last error message, or null if none
     */
    function getLastErrorMsg();

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
     * Reindexes the business objects of a given class from the main
     * database.
     *
     * @param string      $className The class name of business objects to reindex
     * @param string|null $where     SQL-style where clause to limit scope (default is null)
     * @param string|null $orderby   SQL-style order-by clause to limit scope (default is null)
     * @param int|0       $offset    Zero-based offset of first business objects (default is 0)
     * @param int|0       $limit     Maximum number of business objects
     *                                (default is null to return all rows)
     *
     * @return bool True on success, false on failure
     */
    function reindexBizobjects($className, $where = null, $orderBy = null, $offset = 0, $limit = null);

    /**
     * Gets the count of indexed documents corresponding to business
     * objects of a given class.
     *
     * @param string|null $className The class name of business objects to search
     *                                (default is null for any class)
     *
     * @return int|null The count of documents, or null on failure
     */
    function getCount($className = null);

    /*
     * SEARCHING METHODS
     */

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
    function initSearch($searchId, $query, $sortingCriteria = null);

    /**
     * Gets the current search ID.
     *
     * @return string|null The current search ID, or null if none
     */
    function getSearchId();

    /**
     * Get the current Lucene query, whether it was given as is or
     * contructed from an sssoc. array of name-value pairs during the
     * last call to the initSearch method.
     *
     * @return string|null The current Lucene query, or null if none
     */
    function getQuery();

    /**
     * Gets the current sorting criteria.
     *
     * @return string The current sorting criteria, or null if none
     */
    function getSortingCriteria();

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
     * The caller must specify the search ID if the last call to the
     * initSearch method occurred during a different PHP request.
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
     *                               (default is null to use current search ID)
     * @param bool        $xml      Whether to return XML or an array of business objects
     *                               (default is true)
     *
     * @return string|array|null XML or an array business objects, or null on failure
     */
    function getDocumentRange($from, $to, $searchId = null, $xml = true);

    /*
     * FACETING METHODS
     */

    /**
     * Gets the facet values for a given list of facets.
     *
     * If the $searchId parameter is non-null, only returns those
     * facet values present in the corresponding result set.
     *
     * The $facets parameter is expected to be an assoc. array of:
     * facet name => maximum number of values for that facet
     *
     * If the $facets parameter specifies more than one facet, then:
     *
     * - first, merges the facet values for the given facet names,
     *   keeping only the top-N values for each facet, where N is the
     *   assoc. value for that facet (default is 512; null for no
     *   limit); and
     *
     * - second, keeps only the top-M values in the merged result,
     *   where M is the value of the $maxValues parameter (default is
     *   512; null for no limit).
     *
     * If the $sort parameter is true, sorts the list of facet values
     * in decreasing order by occurence count before returning it.
     *
     * @param array       $facets    The assoc. array of facet name => maximum number of values
     * @param string|null $searchId  The reference search ID (default is null)
     * @param int|null    $maxValues The total maximum number of values to return (default is 512)
     * @param bool        $sort      Whether to sort the list of facet values (default is true)
     *
     * @return array|null Assoc. array of: facet value => wcmBizsearchFacetValue, or null on failure
     */
    function getFacetValues(array $facets, $searchId = null, $maxValues = 512, $sort = true);
}
?>