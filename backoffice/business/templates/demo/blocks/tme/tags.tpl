{load class="site" where="id=`$bizobject.siteId`"}
{if $bizobject.tags|@count gt 0}
    <h4> File under </h4>
    <ul class="editorial">
      {foreach from=$bizobject.tags item=tag}
        <li><a href="{$site|@wcm:url}search/?search_tag={$tag}">{$tag}</a></li>
      {/foreach}
    </ul>
{/if}