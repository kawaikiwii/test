{if $bizobject.semanticData.concepts|@count gt 0}
    <h4> Topics </h4>
    <ul class="topics">
      {foreach from=$bizobject.semanticData.concepts key=tag item=data name=tme}
        {if $smarty.foreach.tme.index lt 10}
            <li><a href="{$site|@wcm:url}search/?search_concept={$tag}">{$tag}</a></li>
        {/if}
      {/foreach}
    </ul>
{/if}
