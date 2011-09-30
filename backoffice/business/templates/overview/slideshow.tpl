<div id="_mainContent">
    <h3>{if $bizobject.title != ''}{$bizobject.title}{else}&lt;{'_BIZ_NEW'|constant}&gt;{/if}</h3>
    <h4>{$bizobject.description}</h4>
    
    <div class="info">
        <h4>{'_BIZ_PHOTOS'|constant}</h4>
	    <ul>
	    {foreach from=$bizobject.photos item=photo }
	        <li>
	           <a href="?_wcmAction=business/{$photo.classname}&id={$photo.id}"><img src="{$photo.thumbnail}" width="{$photo.thumbWidth}" height="{$photo.thumbHeight}" lt="{$photo.title}" title="{$photo.title}"></img></a>
	        </li>
	    {/foreach}
	    </ul>
    </div>
</div>

    <div id="_infoContent">
        <ul class="info ">
        <li>
        <span class="label">{'_BIZ_SECTION'|constant}:</span>
        {if $bizobject.title != ''}
            {$bizobject.channel.title}
        {else}
            {'_BIZ_NO_DETAIL'|constant}
        {/if}
        </li>
        
        <li><span class="label">{'_BIZ_PUBLICATIONDATE'|constant}:</span>
        {if $bizobject.publicationDate != ''}
             {$bizobject.publicationDate}
        {else}
            {'_BIZ_NO_DETAIL'|constant}
        {/if}
        </li>
        
        <li><span class="label">{'_BIZ_SOURCE'|constant}:</span>
        {if $bizobject.source != ''}
            {$bizobject.source}
        {else}
            {'_BIZ_NO_DETAIL'|constant}
        {/if}
        </li>
        </ul>
        
        {wcm name="include_template" file="overview/blocks/iptc.tpl"}
        {wcm name="include_template" file="overview/blocks/tme.tpl"}
        {wcm name="include_template" file="overview/blocks/tags.tpl"}
        {wcm name="include_template" file="overview/blocks/related.tpl"}
    
    </div>
