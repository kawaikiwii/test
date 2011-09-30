<div id="_mainContent">
    <h3>{$bizobject.title}</h3>
    
    {if $bizobject.description}
        <h4>{'_BIZ_DESCRIPTION'|constant}</h4>
        {$bizobject.description}
    {elseif $bizobject.caption}
        <h4>{'_BIZ_CAPTION'|constant}</h4>
        {$bizobject.caption}
    {/if}
    
    {if $bizobject.id}
    	<h3>{'_BIZ_ISSUES'|constant}</h3>
    
    	{foreach from=$bizobject.issues item=issue}
        	<a href="?_wcmAction=business/issue&id={$issue.id}"><h4>{$issue.title}</h4></a>
        	{$issue.description}
    	{/foreach}
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
    
    {if $bizobject.photos}
        <h4>{'_BIZ_SHARED_MEDIA'|constant}</h4>
        <ul class="media">
        {foreach from=$bizobject.photos item=photo key=key}
            <li>
                <a href="?_wcmAction=business/photo&id={$photo.id}"><img src="{$photo.thumbnail}" width="{$photo.thumbWidth}" height="{$photo.thumbHeight}" alt="{$photo.title}" title="{$photo.title}" /></a>
                <p>{$photo.caption|truncate:50:'...'}</p>
            </li>
        {/foreach}
        </ul>
    {/if}

    <h4>{'_BIZ_TME'|constant}</h4>
    <dl>
        <dt>{'_CATEGORIES'|constant}</dt>
        <dd>
            {foreach from=$bizobject.semanticData.categories key=data item=item name=cats}
                {$data}{if !$smarty.foreach.cats.last},{/if}
            {/foreach}
        </dd>
    </dl>
    <dl>
        <dt>{'_BIZ_TME_ENTITITES_PN'|constant}</dt>
        <dd>
            {foreach from=$bizobject.semanticData.PN key=data item=item name=pn}
                {$data}{if !$smarty.foreach.pn.last},{/if}
            {/foreach}
            <br/>
            <br/>
        </dd>
        <dt>{'_BIZ_TME_ENTITITES_GL'|constant}</dt>
        <dd>
            {foreach from=$bizobject.semanticData.GL key=data item=item name=gl}
                {$data}{if !$smarty.foreach.gl.last},{/if}
            {/foreach}
            <br/>
            <br/>
        </dd>
        <dt>{'_BIZ_TME_ENTITITES_ON'|constant}</dt>
        <dd>
            {foreach from=$bizobject.semanticData.on key=data item=item name=on}
                {$data}{if !$smarty.foreach.on.last},{/if}
            {/foreach}
        </dd>
    </dl>

    <h4>{'_BIZ_OUTBOUND_LINKS'|constant}</h4>
    <ul class="related">
    {foreach from=$bizobject.related item=object}
        <li><a href="?_wcmAction=business/{$object.className}&id={$object.id}">{$object.title}</a></li>
    {/foreach}
    </ul>
</div>
