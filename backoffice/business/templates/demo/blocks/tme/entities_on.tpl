{if $bizobject.semanticData.ON|@count gt 0}
    <h4> Orgs </h4>
    <ul class="topics">
      {foreach from=$bizobject.semanticData.ON key=tag item=data}
        <li><a href="{$site|@wcm:url}search/?search_entity_ON={$tag}">{$tag}</a></li>
      {/foreach}
    </ul>
{/if}
