<div id="_mainContent">
    <h3>{$bizobject.title}</h3>
    
    {if $bizobject.description}
        <h4>{'_BIZ_DESCRIPTION'|constant}</h4>
        {$bizobject.description}
    {elseif $bizobject.caption}
        <h4>{'_BIZ_CAPTION'|constant}</h4>
        {$bizobject.caption}
    {/if}
    
</div>
<div id="_infoContent">
    <ul class="info">
        {if $bizobject.title != ''}
            <li><span class="label">{'_BIZ_SECTION'|constant}:</span> {$bizobject.channel.title}</li>
        {/if}
        {if $bizobject.publicationDate != ''}
            <li><span class="label">{'_BIZ_PUBLICATIONDATE'|constant}:</span> {$bizobject.publicationDate}</li>
        {/if}
        {if $bizobject.author != ''}
            <li><span class="label">{'_BIZ_AUTHOR'|constant}:</span> {$bizobject.author}</li>
        {/if}
        {if $bizobject.source != ''}
            <li><span class="label">{'_BIZ_SOURCE'|constant}:</span> {$bizobject.source}</li>
        {/if}
    </ul>
    
    {wcm name="include_template" file="overview/blocks/medias.tpl"}
    {wcm name="include_template" file="overview/blocks/iptc.tpl"}
    {wcm name="include_template" file="overview/blocks/tme.tpl"}
    {wcm name="include_template" file="overview/blocks/tags.tpl"}
    {wcm name="include_template" file="overview/blocks/related.tpl"}

</div>
