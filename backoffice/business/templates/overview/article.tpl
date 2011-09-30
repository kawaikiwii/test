<div id="_mainContent">
    <h3>{if $bizobject.title != ''}{$bizobject.title}{else}&lt;{'_BIZ_NEW'|constant}&gt;{/if}</h3>
    <h4>{$bizobject.subtitle}</h4>
    
    <div class="info">
    	<h4>{'_BIZ_ABSTRACT'|constant}</h4>
    	{$bizobject.abstract}
    </div>
    
    <div class="info">
    {if $bizobject.chapters|@count > 0 }
	    {foreach from=$bizobject.chapters item=chapter}
	        <h4>{$chapter.title}</h4>
	        {$chapter.text}
	    {/foreach}
    {else}
        <h4>{'_BIZ_CHAPTERS'|constant}</h4>
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
        
        <li><span  class="label">{'_BIZ_PUBLICATIONDATE'|constant}:</span>
        {if $bizobject.publicationDate != ''}
             {$bizobject.publicationDate}
        {else}
            {'_BIZ_NO_DETAIL'|constant}
        {/if}
        </li>
        
        
        <li><span  class="label">{'_BIZ_AUTHOR'|constant}:</span>         
        {if $bizobject.author != ''}    
            {$bizobject.author}
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
    
    {wcm name="include_template" file="overview/blocks/medias.tpl"}
    {wcm name="include_template" file="overview/blocks/iptc.tpl"}
    {wcm name="include_template" file="overview/blocks/tme.tpl"}
    {wcm name="include_template" file="overview/blocks/tags.tpl"}
    {wcm name="include_template" file="overview/blocks/related.tpl"}

</div>
