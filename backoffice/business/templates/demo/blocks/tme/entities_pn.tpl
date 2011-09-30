{if $bizobject.semanticData.PN|@count gt 0}
    <h4> People </h4>
    <ul class="topics">
      {foreach from=$bizobject.semanticData.PN key=tag item=data}
        <li><a href="{$site|@wcm:url}search/?search_entity_PN={$tag}">{$tag}</a></li>
      {/foreach}
    </ul>
{/if}