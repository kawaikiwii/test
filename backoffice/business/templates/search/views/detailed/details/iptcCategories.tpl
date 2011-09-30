{assign var=iptcCategories value=$object->semanticData->categories} 
    <li class="detail"><span class="label">{'_BIZ_CATEGORIZATION_IPTC'|constant}
        <ul>
        {if $iptcCategories}
		    {foreach from=$iptcCategories item=category key=categoryName}
		       <li>{$categoryName}</li>
		    {/foreach}
	    {else}
	        {'_BIZ_NO_DETAIL'|constant}
	    {/if}
        </ul>
    </li>
