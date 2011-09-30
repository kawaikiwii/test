{if $bizobject.semanticData.categories|@count gt 0}
    <h4> File under </h4>
    <ul class="topics">
      {foreach from=$bizobject.semanticData.categories key=tag item=data}
        <li><a href="{$site|@wcm:url}search/?search_category={$tag}">{$tag}</a></li>
      {/foreach}
    </ul>
{/if}   