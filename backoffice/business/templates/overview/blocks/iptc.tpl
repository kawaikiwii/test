    <h4>{'_BIZ_CATEGORIZATION_IPTC'|constant}</h4>

    {if $bizobject.semanticData.categories != ''}
	    <ul class="categories">
	    {foreach from=$bizobject.semanticData.categories key=data item=item name=cats}
            {if $data != ''}
	           <li>{$data}</li>
		    {else}
		        {'_BIZ_NO_DETAIL'|constant}    
		    {/if}
		{/foreach}
	    </ul>
    {else}
        {'_BIZ_NO_DETAIL'|constant}    
    {/if}
	    