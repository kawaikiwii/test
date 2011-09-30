<div id="_mainContent">
    <h3>{if $bizobject.title != ''}{$bizobject.title}{else}&lt;{'_BIZ_NEW'|constant}&gt;{/if}</h3>
    
   	<div class="info">
   	    <h4>{'_BIZ_IMAGE'|constant}</h4>
        {if $bizobject.original}
            {if $bizobject.width > 450}
                {assign var="width" value=450}
            {else}
                {assign var="width" value=$bizobject.width}
            {/if}
            <img src="{$bizobject.original}" width="{$width}"></img><br />
            {$bizobject.caption}
        {/if}
    </div>
</div>

<div id="_infoContent">
    <ul class="info">
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

        <li><span class="label">{'_BIZ_CREDITS'|constant}:</span>
        {if $bizobject.credits != ''}
             {$bizobject.credits}
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
