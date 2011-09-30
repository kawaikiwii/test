<?php

/**
 * Project:     WCM
 * File:        wcmBusinessSearchWebService.class.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

/**
 * WCM Business Search Web Service
 *
 * Enables the client application to manage business search indices and
 * search business objects.
 *
 * NOTE: All service methods take a session token as the first
 *       parameter. If an invalid session token is passed
 *       to a method, the latter will throw an exception.
 */
class wcmBusinessSearchWebService
{
    /**
     * Logs a given user into the system, creating a new session.
     *
     * If the given user is already logged in, reuses the existing
     * session.
     *
     * If the given user identifier or password is invalid, throws
     * a WCM Web Service exception.
     *
     * NOTE: The returned session token must be passed as the first
     *       parameter to all other service calls.
     *
     * @param string $userId   The identifier of the user to log in
     * @param string $password The user's password
     * @param string $language The session language
     *
     * @return string The session token
     */
    public function login($userId, $password, $language)
    {
        return wcmWebServiceSession::login($userId, $password, $language);
    }

    /**
     * Logs the user associated with a given session token out of
     * the system.
     *
     * If the session token is invalid, throws a WCM Web Service
     * exception.
     *
     * NOTE: The session token must have been obtained from a previous
     *       login service call or a call to the wcmSession::getToken
     *       method.
     *
     * @param string $sessionToken The session token
     *
     * @return boolean True on success
     */
    public function logout($sessionToken)
    {
        wcmWebServiceSession::logout($sessionToken);
        return true;
    }

    /**
     * Indexes an object given its class name and identifier.
     *
     * If the class name or identifier refers to a non-existent class or
     * object, respectively, throws a WCM Web Service exception.
     *
     * @param string $sessionToken The session token
     * @param string $objectClass  The object's class name
     * @param string $objectId     The object's identifier
     *
     * @return boolean True on success
     */
    public function indexObject($sessionToken, $objectClass, $objectId)
    {
        wcmWebServiceSession::checkSession($sessionToken);

        $object = wcmWebServiceObject::getObjectInstance($objectClass, $objectId);

        $bizsearch = wcmBizsearch::getInstance();
        if (!$bizsearch->indexBizobject($object))
        {
            throw new wcmWebServiceException(WCM_WS_EXC_INDEX_OBJECT_FAILED, $objectClass, $objectId, $bizsearch->getLastErrorMsg());
        }

        return true;
    }

    /**
     * De-indexes objects given a class name.
     *
     * If the class name refers to a non-existent class, throws a WCM
     * Web Service exception.
     *
     * @param string $sessionToken The session token
     * @param string $objectClass  The class name
     *
     * @return boolean True on success
     */
    public function deindexObjects($sessionToken, $objectClass)
    {
        wcmWebServiceSession::checkSession($sessionToken);
        wcmWebServiceObject::checkObjectClass($objectClass);

        $bizsearch = wcmBizsearch::getInstance();
        if (!$bizsearch->deindexBizobjects($objectClass))
        {
            throw new wcmWebServiceException(WCM_WS_EXC_DEINDEX_OBJECTS_FAILED, $objectClass, $bizsearch->getLastErrorMsg());
        }

        return true;
    }

    /**
     * Re-indexes objects given a class name.
     *
     * If the class name refers to a non-existent class, throws a WCM
     * Web Service exception.
     *
     * @param string $sessionToken The session token
     * @param string $objectClass  The object's class name
     *
     * @return boolean True on success
     */
    public function reindexObjects($sessionToken, $objectClass)
    {
        wcmWebServiceSession::checkSession($sessionToken);
        wcmWebServiceObject::checkObjectClass($objectClass);

        $bizsearch = wcmBizsearch::getInstance();
        if (!$bizsearch->reindexBizobjects($objectClass))
        {
            throw new wcmWebServiceException(WCM_WS_EXC_REINDEX_OBJECTS_FAILED, $objectClass, $bizsearch->getLastErrorMsg());
        }

        return true;
    }

    /**
     * Performs a search given a search identifier, a Lucene query,
     * and optional search parameters.
     *
     * The following search parameters are recognized:
     *
     *   sortedby - The search field(s) by which to sort the results (SQL "order by" syntaz)
     *
     * NOTE: The caller must use the same search identifier when fetching
     *       search results with the getSearchResults service call.
     *
     * @param string                       $sessionToken The session token
     * @param string                       $searchId     The search identifier
     * @param string                       $queryString  The Lucene query
     * @param wcmWebServiceNameValuePair[] $parameters   Array of search parameters
     *
     * @return int The number of search results
     */
    public function search($sessionToken, $searchId, $queryString, $parameters)
    {
        wcmWebServiceSession::checkSession($sessionToken);

        $sortingCriteria = null;
        if ($parameters)
        {
            foreach ($parameters as $parameter)
            {
                if (strcasecmp($parameter->name, 'sortedby') == 0)
                {
                    $sortingCriteria = $parameter->value;
                    break;
                }
            }
        }

        $bizsearch = wcmBizsearch::getInstance();
        $numFound = $bizsearch->initSearch($searchId, $queryString, $sortingCriteria);
        if ($numFound === null)
        {
            throw new wcmWebServiceException(
                WCM_WS_EXC_INIT_SEARCH_FAILED, $searchId, $bizsearch->getLastErrorMsg());
        }

        return $numFound;
    }

    /**
     * Gets a range of results from a previous search operation.
     *
     * @param string $sessionToken The session token
     * @param string $searchId     The search identifier as specified in the search service call
     * @param int    $fromIndex    The index of the first result to get
     * @param int    $toIndex      The index of the last result to get
     *
     * @return string An XML (<resultSet><result>.bizobject->toXML().</result>...</resultSet>)
     */
    public function getSearchResults($sessionToken, $searchId, $fromIndex, $toIndex)
    {
        wcmWebServiceSession::checkSession($sessionToken);

        $bizsearch = wcmBizsearch::getInstance();
        $results = $bizsearch->getDocumentRange($fromIndex, $toIndex, $searchId, true);
        if ($results === null)
        {
            throw new wcmWebServiceException(WCM_WS_EXC_GET_SEARCH_RESULTS_FAILED, $searchId, $bizsearch->getLastErrorMsg());
        }

        return $results;
    }

    /**
     * Gets the facet values for a given list of facets.
     *
     * If the $searchId parameter is non-empty, only those facet
     * values present in the corresponding result set are returned.
     *
     * The $facets parameter is expected to be an array of:
     * facet name => maximum number of values for that facet
     *
     * If more than one facet name is given in the $facets array
     * parameter, then:
     *
     * - first, the facet values for the given facet names are merged,
     *   keeping only the top-N values for each facet, where N is the
     *   pair value for that facet (zero for no limit); and
     *
     * - second, only the top-M values are kept in the merged result,
     *   where M is the value of the $maxValues parameter (zero for no
     *   limit).
     *
     * If the $sort parameter is true, the returned list of values is
     * sorted in decreasing order by occurence count.
     *
     * @param string                       $sessionToken The session token
     * @param string                       $searchId     The reference search ID (may be empty)
     * @param wcmWebServiceNameValuePair[] $facets       Array of facet name => maximum number of values
     * @param int                          $maxValues    The total maximum number of values to return
     * @param boolean                      $sort         Whether to sort
     *
     * @return wcmWebServiceFacetValue[] Array of facet values
     */
    public function getFacetValues($sessionToken, $searchId, $facets, $maxValues, $sort)
    {
        wcmWebServiceSession::checkSession($sessionToken);

        $facetsAssoc = array();
        if ($facets)
        {
            foreach ($facets as $facet)
            {
                $facetsAssoc[$facet->name] = $facet->value;
            }
        }

        $bizsearch = wcmBizsearch::getInstance();
        $facetValuesAssoc = $bizsearch->getFacetValues($facetsAssoc, $searchId, $maxValues, $sort);
        if ($facetValuesAssoc === null)
        {
            throw new wcmWebServiceException(WCM_WS_EXC_GET_FACET_VALUES_FAILED, $searchId, $bizsearch->getLastErrorMsg());
        }

        $facetValues = array();
        if ($facetValuesAssoc)
        {
            foreach ($facetValuesAssoc as $rawValue => $value)
            {
                $facetValue = new wcmWebServiceFacetValue;
                $facetValue->name = $value->name;
                $facetValue->value = $value->value;
                $facetValue->title = $value->title;
                $facetValue->index = $value->index;
                $facetValue->searchIndex = $value->searchIndex;
                $facetValue->count = $value->count;
                $facetValues[] = $facetValue;
            }
        }

        return $facetValues;
    }
}

?>