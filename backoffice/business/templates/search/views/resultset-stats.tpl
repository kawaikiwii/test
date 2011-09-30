<div id="query-builder">
    {if $searchContext->numItemsFound gt 0}
        {if $searchContext->elapsedTime gt 0}
        <ul class="stats">
            <li><strong>{$searchContext->numItemsFound}</strong> {'_BIZ_ITEMS_FOUND_IN'|constant} <strong>{$searchContext->elapsedTime|string_format:"%.3f"} {'_BIZ_SECONDS'|constant}</strong>.</li>
            <li>{'_BIZ_REFINE'|constant}</li>
        </ul>
        {/if}
    {/if}

</div>
