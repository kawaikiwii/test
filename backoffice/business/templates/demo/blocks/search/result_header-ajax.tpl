<div id="search_result_header">
    <p class="search_result_page_info">
        {if $searchResult->pageMax > 0}
            Showing {$searchResult->first} to {$searchResult->last} of {$searchResult->numFound} results
        {else}
            No results
        {/if}
    </p>
    <ul class="search_result_page_links">
        {foreach from=$searchResult->pages item=page}
            <li class="search_result_page_link">
                {$searcher->renderPageLink($page)}
            </li>
        {/foreach}
    </ul>
</div>
