{assign var=xmlTags value=$bizobject.xmlTags}

    <h4>{'_BIZ_CATEGORIZATION_TAGS'|constant}</h4>

	    <dl>
            {if $xmlTags != ''}
		        {foreach from=$xmlTags item=tags key=tagType}
		        <dt>{if $tagType == 'ads'}
		              {'_BIZ_AD_SERVER'|constant}
		            {else}
		              {'_BIZ_TAGS'|constant}
		            {/if}
		        </dt>
	            <dd>   
                {foreach from=$tags item=tag name=loop}
                    {if $tag != ''}
                        {$tag}{if !$smarty.foreach.loop.last},{/if}
	                {else}
	                    {'_BIZ_NO_DETAIL'|constant}
	                {/if}
                {/foreach}
	            </dd>
		        </dt>
		        {/foreach}
		     {else}
                {'_BIZ_NO_DETAIL'|constant}
            {/if}
	    </dl>
