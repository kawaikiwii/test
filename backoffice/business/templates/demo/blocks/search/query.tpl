<div id="search_query">
    <form id="search_form" method="get" action="?">
        <input id="search_query_string" name="searchQueryString" type="text" value="<?php echo $searcher->queryString; ?>" />
        <input id="search_page_num" name="searchPageNum" type="hidden" value="<?php echo $searchResult->pageNum; ?>" />
        <input id="search_id" name="searchId" type="hidden" value="<?php echo $searcher->id; ?>" />
        <input id="search_num_found" name="searchNumFound" type="hidden" value="<?php echo $searchResult->numFound; ?>" />
        <input id="search_submit" type="submit" value="Search" onclick="initSearch('search_form', 'search_result', 'search_filters'); return false" />
    </form>
</div>
