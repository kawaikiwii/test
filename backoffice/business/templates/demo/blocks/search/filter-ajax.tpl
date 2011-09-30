<div id="search_filter_{$filterName}">
    <h5>{$filterTitle}</h5>
    <ul class="search_filter">
        {if isset($searchResult->facetValues->$filterName)}
            {foreach from=$searchResult->facetValues->$filterName item=facetValue}
                <li class="search_filter_item">
                    <a href="{$facetValue->url}" title="{$facetValue->urlTitle}">{$facetValue->label}</a>
                </li>
            {/foreach}
        {/if}
    </ul>
</div>
