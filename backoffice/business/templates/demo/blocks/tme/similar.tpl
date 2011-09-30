{if $bizobject.semanticData.similars|@count gt 0}
    <h2> Similar stories </h2>
    <ul>
      {foreach from=$bizobject.semanticData.similars key=tag item=data}
        <li><a href="{$site|@wcm:url}{$data.className}/{$data.id}/">{$data.title}</a></li>
      {/foreach}
    </ul>
{/if}