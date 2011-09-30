<div id="_mainContent">
    <h3>
        {if $bizobject.title != ''}
            {$bizobject.title}({$bizobject.type|capitalize})
        {else}
            &lt;{'_BIZ_NEW'|constant}&gt;
        {/if}
    </h3>    
    <h4>{$bizobject.description}</h4>


    <div class="info">
        <h4>{'_BIZ_CONTENT'|constant}</h4>

	    {if $bizobject.location}
	    <dl>
	        <dt>{'_BIZ_LOCATION'|constant}</dt>
	        <dd>{$bizobject.location}</dd>
        </dl>
        {/if}

        {if 'item::ACCESS_PUBLIC'|constant == $bizobject.access}
            {assign var=access value='_BIZ_ACCESS_PUBLIC'|constant}
        {elseif 'item::ACCESS_PROTECTED'|constant == $bizobject.access}
            {assign var=access value='_BIZ_ACCESS_PROTECTED'|constant}
        {elseif 'item::ACCESS_PRIVATE'|constant == $bizobject.access}
            {assign var=access value='_BIZ_ACCESS_PRIVATE'|constant}
        {else}
            {assign var=access value='_BIZ_UNDETERMINED'|constant}
        {/if}

        {if $access != '_BIZ_UNDETERMINED'|constant}
        <dl>
            <dt>{'_BIZ_ACCESS_TITLE'|constant}</dt>
            <dd>
                {$access}
            </dd>
        </dl>
        {/if}

        {if $bizobject.text}
        <dl>
            <dt>{'_BIZ_TEXT'|constant}</dt>
            <dd>
                {$bizobject.text}
            </dd>
        </dl>
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
        
                
        <li><span class="label">{'_BIZ_SOURCE'|constant}:</span>
        {if $bizobject.source != ''}
            {$bizobject.source}
        {else}
            {'_BIZ_NO_DETAIL'|constant}
        {/if}
        </li>
        
        
    </ul>
    
    {wcm name="include_template" file="overview/blocks/related.tpl"}

</div>
