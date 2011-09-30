<?php
/**
 * Project:     WCM
 * File:        business/actions/search.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * Business search action controller
 */
class searchAction extends wcmMVC_BizAction
{
    /**
     * Initial 'filters' search option value in JSON format.
     */
    const INITIAL_FILTERS_JSON = '{
        "meta_concepts":     { "open": false, "checkedItems": {} },
        "meta_entities":     { "open": false, "checkedItems": {} },
        "meta_tags":         { "open": false, "checkedItems": {} },
        "meta_subjects":     { "open": false, "checkedItems": {} },
        "stats_sections":    { "open": false, "checkedItems": {} },
        "stats_asset_types": { "open": false, "checkedItems": {} },
		"stats_site_id": { "open": false, "checkedItems": {} },
        "stats_date_ranges": { "open": false, "checkedItems": {} },
        "stats_sources":     { "open": false, "checkedItems": {} }
    }';

    /**
     * Set the action's context.
     *
     * @param wcmSession $session Current session
     * @param wcmProject $project Current project
     */
    protected function setContext($session, $project)
    {
        // Search parameters and options
        $params = array();
        $options = array();

        // Retrieve the search parameters and options from the action
        // parameters if any
        $actionParams = $this->params;
        if ($actionParams)
        {
            // Search parameters begin with $actionParams['paramPrefix']
            $defaultPrefix = wcmBizsearchConfig::getInstance()->getDefaultParameterPrefix();
            $prefix = getArrayParameter($actionParams, 'paramPrefix', $defaultPrefix);
            $prefixLen = strlen($prefix);

            foreach ($actionParams as $name => $value)
            {
                // Search parameters are case-insensitive
                if (substr($name, 0, $prefixLen) == $prefix)
                    $params[strtolower(substr($name, $prefixLen))] = $value;
                else
                    $options[$name] = $value;
            }
        }

        // If the last todo is one of ours, try to re-use the saved context
        switch ($this->todo)
        {
        case 'fetchItems':
            $context = wcmMVC_Action::getContext();
            if ($context)
            {
                $context->params = array_merge($context->params, $params);
                $context->options = array_merge($context->options, $options);
                break;
            }
            // no context: fall through to default

        default:
            $context = new StdClass;
            $context->params = $params;
            $context->options = $options;
        }

        // Set and update the action's context based on the current
        // search parameters and options - this entails more than just
        // copying values as was done above.
        $this->context = $context;
        $this->updateContext();
    }

    /**
     * Updates the context based on the current search parameters and
     * options.
     */
    private function updateContext()
    {
        $context = $this->context;
        $params = &$context->params;
        $options = &$context->options;

        // Search parameters
        $context->query = getUpdatedArrayParameter($params, 'query', null);
        if ($context->query !== null)
        {
            $baseQuery = getArrayParameter($params, 'basequery', null);
            if ($baseQuery)
            {
                if ($context->query)
                    $context->query = '('.$context->query.') AND ('.$baseQuery.')';
                else
                    $context->query = $baseQuery;
            }
        }
        $context->sortedBy = getUpdatedArrayParameter($params, 'sortedby', null);

        // Search options
        $config = wcmBizsearchConfig::getInstance();
        $defaultConfigId = $config->getDefaultConfigId();
        $context->configId = getUpdatedArrayParameter($options, 'configId', $defaultConfigId);

        $todo = $this->todo == (!$this->todo || $this->todo == 'view') ? 'view' : 'initSearch';
        $context->pageType = $config->getPageType($context->configId, $todo);

        $context->ajaxRequest = $config->getAjaxRequest($context->configId, $context->pageType);
        $options['ajaxRequest'] = ($context->ajaxRequest ? 'true' : 'false');

        $defaultViewName = $config->getDefaultViewName($context->configId, $context->pageType);
        if ($defaultViewName)
        {
            $context->viewName = getUpdatedArrayParameter($options, 'view', $defaultViewName);
            $view = $config->getView($context->configId, $context->pageType, $context->viewName);
            if ($view)
            {
                $context->numPageLinksBeforeAndAfter = (int) $view->defaultPageLinksBeforeAndAfter;
                $defaultPageSize = (int) $view->defaultResultSetSize;
                $context->pageSize = getUpdatedArrayParameter($options, 'pageSize', $defaultPageSize);
            }
        }

        $context->engine = getUpdatedArrayParameter($options, 'engine', null);
        $context->name = getUpdatedArrayParameter($options, 'name', 'wcmBiz');
        $context->pageNum = getUpdatedArrayParameter($options, 'pageNum', 1);
        $context->sortOrder = getUpdatedArrayParameter($options, 'sortOrder', '');

        $context->id = getArrayParameter($options, 'searchId', null);
        $context->numItemsFound = getArrayParameter($options, 'numItemsFound', null);
        $context->elapsedTime = getArrayParameter($options, 'elapsedTime', null);

        $filtersJSON = getUpdatedArrayParameter($options, 'searchFilters', self::INITIAL_FILTERS_JSON);
        $context->filters = json_decode($filtersJSON);
    }

    /**
     * "View" action todo.
     *
     * @param wcmSession $session Current session
     * @param wcmProject $project Current project
     */
    protected function onView($session, $project)
    {
        // Update the action's context from the current search
        // parameters and options
        $this->updateContext();
    }

    /**
     * "Initiate search" action todo.
     *
     * @param wcmSession $session Current session
     * @param wcmProject $project Current project
     */
    protected function onInitSearch($session, $project)
    {
        // Update the action's context from the current search
        // parameters and options
        $this->updateContext();
        $context = $this->context;

        // Get an instance of the search engine specified by the
        // search options and save it in the context
        $bizsearch = wcmBizsearch::getInstance($context->engine);
        if ($bizsearch === null)
        {
            wcmMVC_Action::setError(wcmBizsearch::getLastErrorMsg());
            return;
        }
        $context->options['searchEngine'] = $context->engine;

        // Generate a search ID and save it in the context
        $context->id = 'wcmBizsearch_' . session_id();
        $context->options['searchId'] = $context->id;

        // If the 'query' parameter was not given, use the search
        // parameters if any
        
		

		if ($context->query === null)
        {
            $params = array();
            if ($context->params)
            {
                foreach ($context->params as $name => $value)
                {
                    // Skip the query and sortedby parmaeters
                    if ($name == 'query' || $name == 'sortedby') continue;

                    // An '_' value is equivalent to an empty value
                    // (useful as <SELECT> option values)
                    if ($value == '_') $value = '';
                    $params[$name] = $value;
                }
            }
            $context->query = $params;
        }
		
		/*
		 * Display only siteId-related Objects
		 */
		if (!strstr($context->query, 'classname:photo') && !strstr($context->query, 'classname:location') && !strstr($context->query, 'classname:exportRule'))
		{
			$context->query = $context->query . " AND siteId:(" . $_SESSION['wcmSession']->getSite()->id . ")";
		}
		
		
		

        // Initiate the search and save the elapsed time and number of
        // items found in the context
        $startTime = microtime(true);
        $numItemsFound = $bizsearch->initSearch($context->id, $context->query, $context->sortedBy);
        $context->elapsedTime = microtime(true) - $startTime;
        $context->options['elapsedTime'] = $context->elapsedTime;

        if ($numItemsFound === null)
        {
            wcmMVC_Action::setError($bizsearch->getLastErrorMsg());
            return;
        }
        $context->numItemsFound = $numItemsFound;
        $context->options['numItemsFound'] = $context->numItemsFound;

        // Get the query if set indirectly via search parameters and
        // save it in the search parameters
        if (is_array($context->query))
        {
            $context->query = $bizsearch->getQuery();
            $context->params['query'] = $context->query;
        }
        $context->nativeQuery = $bizsearch->getNativeQuery();

        // Save the query in the search history
        if (!isset($_SESSION['searchHistory']))
            $_SESSION['searchHistory'] = array();
        $_SESSION['searchHistory'][$context->query] = date('d/m/Y H:i:s');

        // Compute the search result
        $this->computeResult();
    }

    /**
     * "Fetch items" action todo.
     *
     * @param wcmSession $session Current session
     * @param wcmProject $project Current project
     */
    protected function onFetchItems($session, $project)
    {
        // Update the action's context from the current search
        // parameters and options
        $this->updateContext();
        $context = $this->context;

        // Compute the search result
        $this->computeResult();
    }

    /**
     * Computes the search result.
     */
    private function computeResult()
    {
    	
        $context = $this->context;

        // Compute the page count
        $numItemsFound = $context->numItemsFound;
        $context->pageCount = ceil($numItemsFound / $context->pageSize);

        // Adjust the current page number
        $context->pageNum = max(1, min($context->pageNum, $context->pageCount));
        $context->options['pageNum'] = $context->pageNum;

        // Compute the first and last items and save them in the context
        $context->firstItem  = 1 + ($context->pageNum - 1) * $context->pageSize;
        $context->lastItem = min($context->pageNum * $context->pageSize, $numItemsFound);

        // Fetch the found items
        $bizsearch = wcmBizsearch::getInstance($context->engine);
        if ($bizsearch === null)
        {
            wcmMVC_Action::setError(wcmBizsearch::getLastErrorMsg());
            return;
        }
        if ($context->viewName == 'highlight')
        {
        	$items = $bizsearch->getDocumentRange($context->firstItem - 1, 
		                                       $context->lastItem - 1,
                                                       $context->id, true);
        }
        else
        {
        	$items = $bizsearch->getDocumentRange($context->firstItem - 1, 
                                                       $context->lastItem - 1,
                                                       $context->id, false);
        }
        
        if ($items === null)
        {
            wcmMVC_Action::setError($bizsearch->getLastErrorMsg());
            return;
        }
        $context->items = $items;
        
        // Compute page data for pagination
        $this->computePageData();
        $this->computeViewData();
    }

    /**
     * Computes page data for pagination based on the current search
     * result.
     */
    private function computePageData()
    {
        $context = $this->context;

        // Compute page links
        $pageNum = $context->pageNum;
        $numPagesBefore = $pageNum - 1;
        $numPagesAfter = $context->pageCount - $pageNum;

        $disabledBefore = ($numPagesBefore == 0 ? ' disabled' : '');
        $disabledAfter = ($numPagesAfter == 0 ? ' disabled' : '');

        $numPageLinksBeforeAndAfter = $context->numPageLinksBeforeAndAfter;

        $minPageLink = $pageNum -
            min($numPagesBefore,
                $numPageLinksBeforeAndAfter + ($numPageLinksBeforeAndAfter -
                                               min($numPageLinksBeforeAndAfter, $numPagesAfter)));
        $maxPageLink = $pageNum +
            min($numPagesAfter,
                $numPageLinksBeforeAndAfter + ($numPageLinksBeforeAndAfter -
                                               min($numPageLinksBeforeAndAfter, $numPagesBefore)));
        $pageLinks = array();
        $pageLinks[] = $this->getPageLink(1, _BIZ_RESULTS_FIRST, 'first' . $disabledBefore);
        $pageLinks[] = $this->getPageLink($pageNum - 1,
                                          _BIZ_RESULTS_PREVIOUS, 'previous' . $disabledBefore);

        for ($tmpPageNum = $minPageLink; $tmpPageNum <= $maxPageLink; ++$tmpPageNum)
            $pageLinks[] =
                $this->getPageLink($tmpPageNum, (string) $tmpPageNum,
                                   $tmpPageNum == $pageNum ? 'selected' : '');

        $pageLinks[] = $this->getPageLink($pageNum + 1, _BIZ_RESULTS_NEXT, 'next' . $disabledAfter);
        $pageLinks[] = $this->getPageLink($context->pageCount,
                                          _BIZ_RESULTS_LAST, 'last' . $disabledAfter);

        $context->pageLinks = $pageLinks;
    }

    /**
     * Gets the search result page link for a given page number.
     *
     * @param int    $pageNum  The page number
     * @param string $label    The page link text
     * @param string $class    The page link class
     */
    private function getPageLink($pageNum, $text, $class)
    {
        $context = $this->context;
        $pageNum = max(1, min($pageNum, $context->pageCount));

        // Build the URL to fetch a page of items
        $formName = $context->name . 'SearchForm';
        $resultName = $context->name . 'SearchResult';
        $url = "javascript:fetchSearchResultItems(".$pageNum.")";

        // Creare the page link
        $pageLink = new StdClass;
        $pageLink->class = $class;
        $pageLink->disabled = in_array('disabled', explode(' ', $class));
        $pageLink->text = getConst($text);
        $pageLink->urlTitle = $pageLink->text;
        $pageLink->url = $url;

        return $pageLink;
    }

    /**
     * Computes view data for switching views.
     */
    private function computeViewData()
    {
        $config = wcmBizsearchConfig::getInstance();
        $context = $this->context;

        $viewLinks = array();
        foreach ($config->getViews($context->configId, $context->pageType) as $view)
        {
            $title = getConst(strval($view->title));
            $url = "javascript:fetchSearchResultItems(null, '" . $view['id'] . "')";

            $viewLink = new stdClass();
            $viewLink->class = strval($view['class']);
            $viewLink->text = $title;
            $viewLink->urlTitle = $title;
            $viewLink->url = $url;

            $viewLinks[] = $viewLink;
        }

        $context->viewLinks = $viewLinks;
    }
}
?>