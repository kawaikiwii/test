<div id="resultset-info">
    {if $searchContext->numItemsFound eq 0}
        {'_BIZ_NO_RESULT'|constant}
    {else}
        <ul class="pagecount">
            {foreach from=$searchContext->pageLinks item=link}
                <li><a class="{$link->class}" href="{$link->url}" title="{$link->urlTitle}"><span>{$link->text}</span></a></li>
            {/foreach}
        </ul>

        {if not $displayedOnce}
        <ul class="display">
            {foreach from=$searchContext->viewLinks item=link}
                <li><a class="{$link->class}" href="{$link->url}" title="{$link->urlTitle}"><span style="visibility:hidden">{$link->text}</span></a></li>
            {/foreach}
        </ul>
        {/if}
        {assign var=displayedOnce value="TRUE"}
    {/if}
</div>
