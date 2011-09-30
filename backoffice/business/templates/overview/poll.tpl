<div id="_mainContent">
    <h3>{if $bizobject.title != ''}{$bizobject.title}{else}&lt;{'_BIZ_NEW'|constant}&gt;{/if}</h3>
    <div class="info">
	    {if $bizobject.text}
	        <h4>{'_BIZ_POLL_QUESTION'|constant}</h4>
	        {$bizobject.text}
	    {/if}

        <h4>{'_BIZ_POLL_CHOICES'|constant} / {'_BIZ_VOTES'|constant|lower}</h4>
        {if $bizobject.choices|@count > 0}

	        {assign var="choices" value=$bizobject.choices}
	        <ul class="graph">
	            {foreach item="choice" from="$choices"}
	            <li style="background-position: {if $bizobject.voteCount > 0} {math equation="((x / y) * 100) * 4" x=$choice.voteCount y=$bizobject.voteCount format="%d"}px 0{/if};">
	                <strong>{$choice.text}</strong> {$choice.voteCount} {if $bizobject.voteCount > 0}({math equation="(x / y) * 100" x=$choice.voteCount y=$bizobject.voteCount format="%d"}%){/if}
	            </li>
	            {/foreach}
	        </ul>
	
            <p>{$bizobject.voteCount} {'_BIZ_VOTES'|constant|lower}</p>

        {/if}
    </div>
    
</div>
<div id="_infoContent">
    <ul class="info">
        
        {assign var="pollKinds" value=$bizobject.kinds }
        {assign var="kind" value=$bizobject.kind }
        <li><span class="label">{'_BIZ_KIND_OF_POLL'|constant}:</span> {$pollKinds.$kind}</li>
        
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

</div>
