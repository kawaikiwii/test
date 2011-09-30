<?php
// Initialize the system
require_once dirname(__FILE__).'/../../init.php';
require_once dirname(__FILE__).'/../../ajax/search/wcm.siteSearcher.php';

// Include the common header
$channelId = 0;
$pageTitle = 'Site Search';
$lastUpdated = '';
include($config['wcm.webSite.path'].'site{$site.id}/cache/header.php');

// Get the search parameters
$queryString = getArrayParameter($_REQUEST, 'searchQueryString', '');

// Perform an initial search
$searcher = new wcmSiteSearcher;
$searcher->initSearch($queryString);
$searcher->fetchItems();
$searcher->fetchFacets();
$searchResult = $searcher->result;
?>