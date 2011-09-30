{if $bizobject.semanticData.GL|@count gt 0}
    <h4> Places </h4>
    <ul class="topics">
      {foreach from=$bizobject.semanticData.GL key=tag item=data}
        <li><a href="{$site|@wcm:url}search/?search_entity_GL={$tag}">{$tag}</a></li>
      {/foreach}
    </ul>
{/if}
