{assign var="relations" value=$bizobject|@wcm:relations}
{if $relations|@count gt 0}
    <h2> Related content </h2>
    <ul>
      {foreach from=$relations item=related}
        <li><a href="{$related.destination|@wcm:url}/">{$related.title}</a></li>
      {/foreach}                                     
    </ul>
{/if}