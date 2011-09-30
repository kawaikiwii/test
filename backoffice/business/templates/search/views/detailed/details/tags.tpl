{assign var=xmlTags value=$object->xmlTags}
{if $xmlTags}
	<li class="detail">
		<span class="label">{'_BIZ_CATEGORIZATION_TAGS'|constant}</span>
		<ul>
		{foreach from=$xmlTags item=tags key=tagType}
			<li>
				<span class="label">{if $tagType == 'ads'}{'_BIZ_AD_SERVER'|constant}{else}{'_BIZ_TAGS'|constant}{/if}</span>
				   <ul>
				    {if $tags|@count}
						{foreach from=$tags item=tag }
							<li>{$tag}</li>
						{/foreach}
	                {else}
	                    {'_BIZ_NO_DETAIL'|constant}
	                {/if}
				   </ul>
			</li>
		{/foreach}
		</ul>
	</li>
{/if}
